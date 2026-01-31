<?php

namespace DiviChild\Modules\Security;

use DiviChild\Core\Abstracts\ModuleService;

class Service extends ModuleService
{
  /**
   * Initializes all module services
   * @return void
   * @since 1.0.0
   */
  public function init_service()
  {
    // Last Login tracking (always active to capture logins)
    if ($this->is_option_enabled('track_last_login')) {
      add_action('wp_login', [$this, 'log_last_login'], 10, 2);

      if (is_admin()) {
        add_filter('manage_users_columns', [$this, 'add_last_login_column']);
        add_filter('manage_users_custom_column', [$this, 'show_last_login_value'], 10, 3);
      }
    }

    // Disable Author Archives
    if ($this->is_option_enabled('disable_author_archives')) {
      add_action('template_redirect', [$this, 'redirect_author_archives'], 1);
      add_filter('author_link', [$this, 'disable_author_link'], PHP_INT_MAX);
      add_filter('author_rewrite_rules', '__return_empty_array');

      if (class_exists('WP_Sitemaps')) {
        add_filter('wp_sitemaps_add_provider', [$this, 'remove_authors_from_sitemap'], 10, 2);
      }

      if (is_admin()) {
        add_filter('user_row_actions', [$this, 'remove_user_view_action'], PHP_INT_MAX);
      }
    }

    // Obfuscate Author Slugs (only if author archives are NOT disabled)
    if ($this->is_option_enabled('obfuscate_author_slugs') && !$this->is_option_enabled('disable_author_archives')) {
      add_filter('author_link', [$this, 'encrypt_author_link'], 10, 3);
      add_action('pre_get_posts', [$this, 'decrypt_author_query'], 10);
      add_filter('rest_prepare_user', [$this, 'obfuscate_rest_user_slug'], 10, 3);
    }
  }


  // =====================================================================
  // Last Login
  // =====================================================================

  /**
   * Stores the login timestamp in user meta
   * @param string $user_login
   * @param \WP_User $user
   * @return void
   */
  public function log_last_login($user_login, $user)
  {
    if (is_object($user) && property_exists($user, 'ID')) {
      update_user_meta($user->ID, 'divi_child_last_login', time());
    }
  }

  /**
   * Adds the "Last Login" column to the users table
   * @param array $columns
   * @return array
   */
  public function add_last_login_column($columns)
  {
    $columns['dvc_last_login'] = __('Last Login', 'divi-child');
    return $columns;
  }

  /**
   * Renders the last login value for each user row
   * @param string $output
   * @param string $column_name
   * @param int $user_id
   * @return string
   */
  public function show_last_login_value($output, $column_name, $user_id)
  {
    if ($column_name !== 'dvc_last_login') {
      return $output;
    }

    $last_login = get_user_meta($user_id, 'divi_child_last_login', true);

    if (empty($last_login)) {
      return __('Never', 'divi-child');
    }

    $date_format = get_option('date_format');
    $time_format = get_option('time_format');

    return wp_date("{$date_format} {$time_format}", (int) $last_login);
  }


  // =====================================================================
  // Disable Author Archives
  // =====================================================================

  /**
   * Redirects author archive pages to 404
   * @return void
   */
  public function redirect_author_archives()
  {
    if (isset($_GET['author']) || is_author()) {
      global $wp_query;
      $wp_query->set_404();
      status_header(404);
      nocache_headers();
    }
  }

  /**
   * Replaces author links with '#'
   * @param string $link
   * @return string
   */
  public function disable_author_link($link)
  {
    return '#';
  }

  /**
   * Removes users from the XML sitemap
   * @param mixed $provider
   * @param string $name
   * @return mixed
   */
  public function remove_authors_from_sitemap($provider, $name)
  {
    if ($name === 'users') {
      return false;
    }
    return $provider;
  }

  /**
   * Removes the "View" action from user row actions
   * @param array $actions
   * @return array
   */
  public function remove_user_view_action($actions)
  {
    unset($actions['view']);
    return $actions;
  }


  // =====================================================================
  // Obfuscate Author Slugs
  // =====================================================================

  /**
   * Encrypts a user ID into an obfuscated author slug
   * @param int $user_id
   * @return string
   */
  private function encrypt_user_id($user_id)
  {
    $key = substr(AUTH_KEY, 0, 24);
    $encrypted = openssl_encrypt(
      base_convert($user_id, 10, 36),
      'DES-EDE3',
      $key,
      OPENSSL_RAW_DATA
    );
    return bin2hex($encrypted);
  }

  /**
   * Decrypts an obfuscated author slug back to a user ID
   * @param string $encrypted_slug
   * @return int|false
   */
  private function decrypt_author_slug($encrypted_slug)
  {
    if (!ctype_xdigit($encrypted_slug)) {
      return false;
    }

    $key = substr(AUTH_KEY, 0, 24);
    $decrypted = openssl_decrypt(
      pack('H*', $encrypted_slug),
      'DES-EDE3',
      $key,
      OPENSSL_RAW_DATA
    );

    if ($decrypted === false) {
      return false;
    }

    return (int) base_convert($decrypted, 36, 10);
  }

  /**
   * Replaces the author slug in author links with an encrypted version
   * @param string $link
   * @param int $author_id
   * @param string $author_nicename
   * @return string
   */
  public function encrypt_author_link($link, $author_id, $author_nicename)
  {
    $encrypted = $this->encrypt_user_id($author_id);
    return str_replace("/{$author_nicename}", "/{$encrypted}", $link);
  }

  /**
   * Decrypts encrypted author slugs in queries
   * @param \WP_Query $query
   * @return void
   */
  public function decrypt_author_query($query)
  {
    if (!$query->is_author() || empty($query->query_vars['author_name'])) {
      return;
    }

    $author_name = $query->query_vars['author_name'];

    // Check if it's an encrypted slug (hex string)
    if (ctype_xdigit($author_name)) {
      $user_id = $this->decrypt_author_slug($author_name);
      $user = $user_id ? get_user_by('id', $user_id) : false;

      if ($user) {
        $query->set('author_name', $user->user_nicename);
      } else {
        $query->is_404 = true;
        $query->is_author = false;
        $query->is_archive = false;
      }
    } else {
      // Original author slug used — block it
      $query->is_404 = true;
      $query->is_author = false;
      $query->is_archive = false;
    }
  }

  /**
   * Obfuscates the user slug in REST API responses
   * @param \WP_REST_Response $response
   * @param \WP_User $user
   * @param \WP_REST_Request $request
   * @return \WP_REST_Response
   */
  public function obfuscate_rest_user_slug($response, $user, $request)
  {
    $data = $response->get_data();
    $data['slug'] = $this->encrypt_user_id($data['id']);
    $response->set_data($data);
    return $response;
  }
}
