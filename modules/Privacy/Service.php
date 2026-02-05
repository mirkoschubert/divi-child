<?php

namespace DiviChild\Modules\Privacy;

use DiviChild\Core\Abstracts\ModuleService;

class Service extends ModuleService
{
  /**
   * Initializes all module services
   * @return void
   * @since 3.0.0
   */
  public function init_service()
  {
    // === Common (Admin + Frontend) ===

    // Last Login tracking (always active to capture logins)
    if ($this->is_option_enabled('track_last_login')) {
      add_action('wp_login', [$this, 'log_last_login'], 10, 2);
    }

    // Disable Author Archives (common hooks)
    if ($this->is_option_enabled('disable_author_archives')) {
      add_filter('author_rewrite_rules', '__return_empty_array');

      if (\class_exists('WP_Sitemaps')) {
        add_filter('wp_sitemaps_add_provider', [$this, 'remove_authors_from_sitemap'], 10, 2);
      }
    }

    // === Admin Only ===

    if (is_admin()) {
      // Last Login column in users table
      if ($this->is_option_enabled('track_last_login')) {
        add_filter('manage_users_columns', [$this, 'add_last_login_column']);
        add_filter('manage_users_custom_column', [$this, 'show_last_login_value'], 10, 3);
      }

      // Remove "View" action from user row actions
      if ($this->is_option_enabled('disable_author_archives')) {
        add_filter('user_row_actions', [$this, 'remove_user_view_action'], PHP_INT_MAX);
      }
    }

    // === Frontend Only ===

    if (!is_admin()) {
      // Comments External
      if ($this->is_option_enabled('comments_external')) {
        add_filter("comment_text", [$this, 'external_comment_links']);
        add_filter("get_comment_author_link", [$this, 'external_comment_links']);
      }

      // Comments IP
      if ($this->is_option_enabled('comments_ip')) {
        add_filter('pre_comment_user_ip', [$this, 'remove_comments_ip']);
      }

      // Disable Emojis
      if ($this->is_option_enabled('disable_emojis')) {
        $this->disable_emojis();
      }

      // Disable oEmbeds
      if ($this->is_option_enabled('disable_oembeds')) {
        $this->disable_embeds();
      }

      // Remove DNS Prefetching
      if ($this->is_option_enabled('dns_prefetching')) {
        $this->remove_dns_prefetch();
      }

      // Remove REST API & XML-RPC Headers
      if ($this->is_option_enabled('rest_api')) {
        $this->remove_api_headers();
      }

      // Disable Author Archives (frontend redirect)
      if ($this->is_option_enabled('disable_author_archives')) {
        add_action('template_redirect', [$this, 'redirect_author_archives'], 1);
        add_filter('author_link', [$this, 'disable_author_link'], PHP_INT_MAX);
      }

      // Obfuscate Author Slugs (only if author archives are NOT disabled)
      if ($this->is_option_enabled('obfuscate_author_slugs') && !$this->is_option_enabled('disable_author_archives')) {
        add_filter('author_link', [$this, 'encrypt_author_link'], 10, 3);
        add_action('pre_get_posts', [$this, 'decrypt_author_query'], 10);
        add_filter('rest_prepare_user', [$this, 'obfuscate_rest_user_slug'], 10, 3);
      }
    }
  }


  // =====================================================================
  // Privacy: Comments
  // =====================================================================

  /**
   * Makes every comment and author link an external link
   * @param string $content
   * @return string
   */
  public function external_comment_links($content)
  {
    return \str_replace("<a ", "<a target='_blank' rel='noopener noreferrer' ", $content);
  }

  /**
   * Removes IP addresses from comments
   * @param string $comment_author_ip
   * @return string
   */
  public function remove_comments_ip($comment_author_ip)
  {
    return '';
  }


  // =====================================================================
  // Privacy: Emojis
  // =====================================================================

  /**
   * Disables emojis
   * @return void
   */
  private function disable_emojis()
  {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('tiny_mce_plugins', [$this, 'disable_emojis_tinymce']);
    add_filter('wp_resource_hints', [$this, 'disable_emojis_remove_dns_prefetch'], 10, 2);
  }

  /**
   * Removes the emoji plugin from TinyMCE
   * @param array $plugins
   * @return array
   */
  public function disable_emojis_tinymce($plugins)
  {
    if (\is_array($plugins)) {
      return \array_diff($plugins, ['wpemoji']);
    }
    return [];
  }

  /**
   * Removes DNS prefetch for emojis
   * @param array $urls
   * @param string $relation_type
   * @return array
   */
  public function disable_emojis_remove_dns_prefetch($urls, $relation_type)
  {
    if ('dns-prefetch' == $relation_type) {
      $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');
      $urls = \array_diff($urls, [$emoji_svg_url]);
    }
    return $urls;
  }


  // =====================================================================
  // Privacy: oEmbeds
  // =====================================================================

  /**
   * Disables oEmbeds
   * @return void
   */
  private function disable_embeds()
  {
    remove_action('rest_api_init', 'wp_oembed_register_route');
    add_filter('embed_oembed_discover', '__return_false');
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    add_filter('tiny_mce_plugins', [$this, 'disable_embeds_tinymce_plugin']);
    add_filter('rewrite_rules_array', [$this, 'disable_embeds_rewrites']);
    remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);
  }

  /**
   * Removes the oEmbed plugin from TinyMCE
   * @param array $plugins
   * @return array
   */
  public function disable_embeds_tinymce_plugin($plugins)
  {
    return \array_diff($plugins, ['wpembed']);
  }

  /**
   * Removes oEmbed rewrite rules
   * @param array $rules
   * @return array
   */
  public function disable_embeds_rewrites($rules)
  {
    foreach ($rules as $rule => $rewrite) {
      if (false !== \strpos($rewrite, 'embed=true')) {
        unset($rules[$rule]);
      }
    }
    return $rules;
  }


  // =====================================================================
  // Privacy: DNS Prefetch & API Headers
  // =====================================================================

  /**
   * Removes DNS prefetching
   * @return void
   */
  private function remove_dns_prefetch()
  {
    remove_action('wp_head', 'wp_resource_hints', 2);
  }

  /**
   * Removes REST API & XML-RPC info from head and headers
   * @return void
   */
  private function remove_api_headers()
  {
    // Deactivate XML-RPC
    remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
    add_filter('xmlrpc_enabled', '__return_false');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');

    // Remove REST API links
    if (!is_admin()) {
      remove_action('wp_head', 'rest_output_link_wp_head', 10);
      remove_action('template_redirect', 'rest_output_link_header', 11);
    }

    // Remove Generator Tag
    remove_action('wp_head', 'wp_generator');
  }


  // =====================================================================
  // Security: Last Login
  // =====================================================================

  /**
   * Stores the login timestamp in user meta
   * @param string $user_login
   * @param \WP_User $user
   * @return void
   */
  public function log_last_login($user_login, $user)
  {
    if (\is_object($user) && \property_exists($user, 'ID')) {
      update_user_meta($user->ID, 'divi_child_last_login', \time());
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

    return wp_date("{$date_format} {$time_format}", \intval($last_login));
  }


  // =====================================================================
  // Security: Disable Author Archives
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
  // Security: Obfuscate Author Slugs
  // =====================================================================

  /**
   * Generates an obfuscated author slug from a user ID using HMAC
   * @param int $user_id
   * @return string
   */
  private function hash_user_id($user_id)
  {
    return \substr(hash_hmac('sha256', (string) $user_id, constant('AUTH_KEY')), 0, 16);
  }

  /**
   * Finds a user by their obfuscated author slug
   * @param string $hashed_slug
   * @return int|false
   */
  private function find_user_by_hash($hashed_slug)
  {
    if (!\ctype_xdigit($hashed_slug) || \strlen($hashed_slug) !== 16) {
      return false;
    }

    $users = get_users(['fields' => 'ID']);
    foreach ($users as $user_id) {
      if ($this->hash_user_id($user_id) === $hashed_slug) {
        return (int) $user_id;
      }
    }

    return false;
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
    $hashed = $this->hash_user_id($author_id);
    return \str_replace("/{$author_nicename}", "/{$hashed}", $link);
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

    if (\ctype_xdigit($author_name) && \strlen($author_name) === 16) {
      $user_id = $this->find_user_by_hash($author_name);
      $user = $user_id ? get_user_by('id', $user_id) : false;

      if ($user) {
        $query->set('author_name', $user->user_nicename);
      } else {
        $query->is_404 = true;
        $query->is_author = false;
        $query->is_archive = false;
      }
    } else {
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
    $data['slug'] = $this->hash_user_id($data['id']);
    $response->set_data($data);
    return $response;
  }
}
