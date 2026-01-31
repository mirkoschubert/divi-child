<?php

namespace DiviChild\Modules\Administration;

use DiviChild\Core\Abstracts\ModuleService;

class Service extends ModuleService
{
  /**
   * Initializes all module services
   * @return void
   * @since 1.3.0
   */
  public function init_service()
  {
    // === Common (Admin + Frontend) ===

    // Unregister Projects
    if ($this->is_option_enabled('disable_projects')) {
      add_action('init', [$this, 'unregister_projects'], 15);
    }

    // Stop Update Mails
    if ($this->is_option_enabled('stop_mail_updates')) {
      add_filter('auto_core_update_send_email', [$this, 'stop_update_mails'], 10, 4);
      add_filter('auto_plugin_update_send_email', '__return_false');
      add_filter('auto_theme_update_send_email', '__return_false');
    }

    // Enable infinite scroll for media library
    if ($this->is_option_enabled('media_infinite_scroll')) {
      add_filter('media_library_infinite_scrolling', '__return_true');
      add_filter('upload_per_page', function () {
        return 999999999999999999;
      });
    }

    // Add SVG, WebP and AVIF support
    if ($this->is_option_enabled('svg_support') || $this->is_option_enabled('webp_support') || $this->is_option_enabled('avif_support')) {
      if (version_compare(get_bloginfo('version'), '5.8', '<')) {
        add_filter('mime_types', [$this, 'supported_mimes']);
      } else {
        add_filter('upload_mimes', [$this, 'supported_mimes']);
      }
      add_filter('wp_check_filetype_and_ext', [$this, 'handle_modern_image_upload'], 10, 5);
    }

    // Disable Divi Upsells
    if ($this->is_option_enabled('disable_divi_upsells')) {
      add_action('init', [$this, 'hide_divi_dashboard']);
    }

    // Enqueue common assets (upsells/AI CSS on both admin + frontend)
    add_action('wp_enqueue_scripts', [$this, 'enqueue_common_assets']);
    add_action('admin_enqueue_scripts', [$this, 'enqueue_common_assets']);

    // === Admin Only ===

    if (is_admin()) {
      // Duplicate Posts
      if ($this->is_option_enabled('duplicate_posts')) {
        add_action('admin_action_duplicate_post_as_draft', [$this, 'duplicate_post_as_draft']);
        add_filter('post_row_actions', [$this, 'duplicate_post_link'], 10, 2);
        add_filter('page_row_actions', [$this, 'duplicate_post_link'], 10, 2);
      }

      // Duplicate Divi Library Items
      if ($this->is_option_enabled('duplicate_library')) {
        add_action('admin_action_duplicate_library_as_draft', [$this, 'duplicate_library_as_draft']);
        add_filter('post_row_actions', [$this, 'duplicate_library_link'], 10, 2);
      }

      // Enable Divi Builder By Default
      if ($this->is_option_enabled('builder_default')) {
        add_action('load-post-new.php', [$this, 'builder_default_on_new_post']);
      }
    }

    // === Frontend Only ===

    if (!is_admin()) {
      // Frontend CSS
      add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);

      // External Links (via JS to cover all page areas including Theme Builder)
      if ($this->is_option_enabled('external_links_new_tab')) {
        add_action('wp_footer', [$this, 'render_external_links_script'], 99);
      }
    }
  }


  // =====================================================================
  // Common
  // =====================================================================

  /**
   * Enqueues common assets (admin + frontend)
   * @return void
   * @since 1.0.0
   */
  public function enqueue_common_assets()
  {
    if ($this->is_option_enabled('disable_divi_upsells')) {
      wp_enqueue_style('divi-child-upsells', $this->module->get_asset_url('css/misc-disable-divi-upsells.min.css'));
    }
    if ($this->is_option_enabled('disable_divi_ai')) {
      wp_enqueue_style('divi-child-ai', $this->module->get_asset_url('css/misc-disable-divi-ai.min.css'));
    }
  }

  /**
   * Unregisters default Project type and taxonomies
   * @return void
   * @since 1.0.0
   */
  public function unregister_projects()
  {
    if (taxonomy_exists('project_category')) {
      unregister_taxonomy('project_category');
    }
    if (taxonomy_exists('project_tag')) {
      unregister_taxonomy('project_tag');
    }
    if (post_type_exists('project')) {
      unregister_post_type('project');
    }
  }

  /**
   * Stops email notifications for automatic updates
   * @param bool $send
   * @param string $type
   * @param object $core_update
   * @param mixed $result
   * @return bool
   * @since 1.0.0
   */
  public function stop_update_mails($send, $type, $core_update, $result): bool
  {
    return empty($type) || $type !== 'success';
  }

  /**
   * Adds SVG, WebP and AVIF support for file uploads
   * @param array $mimes
   * @return array
   * @since 1.0.0
   */
  public function supported_mimes(array $mimes = []): array
  {
    if ($this->is_option_enabled('svg_support')) {
      $mimes['svg'] = 'image/svg+xml';
    }
    if ($this->is_option_enabled('webp_support') && version_compare(get_bloginfo('version'), '5.8', '<')) {
      $mimes['webp'] = 'image/webp';
    }
    if ($this->is_option_enabled('avif_support') && version_compare(get_bloginfo('version'), '6.5', '<')) {
      $mimes['avif'] = 'image/avif';
    }
    return $mimes;
  }

  /**
   * Handles modern image format upload checks
   * @param array $data
   * @param string $file
   * @param string $filename
   * @param array $mimes
   * @param string|false $real_mime
   * @return array
   * @since 1.0.0
   */
  public function handle_modern_image_upload($data, $file, $filename, $mimes, $real_mime)
  {
    if (!empty($data['type'])) {
      return $data;
    }

    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if ($ext === 'svg' && $this->is_option_enabled('svg_support')) {
      $data['type'] = 'image/svg+xml';
      $data['ext'] = 'svg';
    } elseif ($ext === 'webp' && $this->is_option_enabled('webp_support') && version_compare(get_bloginfo('version'), '5.8', '<')) {
      $data['type'] = 'image/webp';
      $data['ext'] = 'webp';
    } elseif ($ext === 'avif' && $this->is_option_enabled('avif_support') && version_compare(get_bloginfo('version'), '6.5', '<')) {
      $data['type'] = 'image/avif';
      $data['ext'] = 'avif';
    }

    return $data;
  }

  /**
   * Blocks the Divi onboarding/upsell dashboard page
   * @return void
   * @since 1.0.0
   */
  public function hide_divi_dashboard()
  {
    global $pagenow;
    $page = !empty($_GET['page']) ? $_GET['page'] : ''; //phpcs:ignore
    if (!empty($page) && $page === 'et_onboarding' && !empty($pagenow) && $pagenow === 'admin.php') {
      wp_die(esc_attr__("You don't have permission to access this page", 'divi-child'));
    }
  }


  // =====================================================================
  // Admin
  // =====================================================================

  /**
   * Handles duplication of posts as drafts
   * @return void
   * @since 1.1.0
   */
  public function duplicate_post_as_draft()
  {
    if (!current_user_can('edit_posts')) {
      return;
    }
    if (!isset($_GET['duplicate_nonce']) || !wp_verify_nonce(sanitize_text_field($_GET['duplicate_nonce']), 'duplicate_nonce')) {
      return;
    }
    global $wpdb;
    if (!(isset($_GET['post']) || isset($_POST['post']) || (isset($_REQUEST['action']) && $_REQUEST['action'] === 'duplicate_post_as_draft'))) {
      wp_die('No post to duplicate has been supplied!');
    }

    $post_id = isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']);
    $post = get_post($post_id);

    if (!$post) {
      wp_die('Post creation failed, could not find original post: ' . absint($post_id));
    }

    $new_post_id = wp_insert_post([
      'comment_status' => $post->comment_status,
      'ping_status'    => $post->ping_status,
      'post_author'    => get_current_user_id(),
      'post_content'   => $post->post_content,
      'post_excerpt'   => $post->post_excerpt,
      'post_name'      => $post->post_name,
      'post_parent'    => $post->post_parent,
      'post_password'  => $post->post_password,
      'post_status'    => 'draft',
      'post_title'     => $post->post_title . ' -- Copy',
      'post_type'      => $post->post_type,
      'to_ping'        => $post->to_ping,
      'menu_order'     => $post->menu_order,
    ]);

    $taxonomies = get_object_taxonomies($post->post_type);
    foreach ($taxonomies as $taxonomy) {
      $terms = wp_get_object_terms($post_id, $taxonomy, ['fields' => 'slugs']);
      wp_set_object_terms($new_post_id, $terms, $taxonomy, false);
    }

    $post_meta_infos = $wpdb->get_results($wpdb->prepare("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", $post_id)); // phpcs:ignore
    if (count($post_meta_infos) > 0) {
      $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) VALUES ";
      $sql_query_params = [];
      foreach ($post_meta_infos as $meta_info) {
        if ($meta_info->meta_key === '_wp_old_slug') {
          continue;
        }
        $sql_query .= '(%d, %s, %s),';
        $sql_query_params[] = $new_post_id;
        $sql_query_params[] = $meta_info->meta_key;
        $sql_query_params[] = $meta_info->meta_value;
      }
      $sql_query = rtrim($sql_query, ',');
      if (!empty($sql_query_params)) {
        $wpdb->query($wpdb->prepare($sql_query, $sql_query_params)); // phpcs:ignore
      }
    }

    wp_safe_redirect(admin_url("post.php?action=edit&post={$new_post_id}"));
    exit();
  }

  /**
   * Adds a "Duplicate" link to post/page row actions (excludes Divi Library)
   * @param array $actions
   * @param \WP_Post $post
   * @return array
   * @since 1.1.0
   */
  public function duplicate_post_link($actions, $post)
  {
    if ($post->post_type === 'et_pb_layout') {
      return $actions;
    }
    if (current_user_can('edit_posts')) {
      $actions['duplicate'] = '<a href="' . esc_url(admin_url('admin.php?action=duplicate_post_as_draft&post=' . $post->ID . '&duplicate_nonce=' . wp_create_nonce('duplicate_nonce'))) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
    }
    return $actions;
  }

  /**
   * Adds a "Duplicate" link to Divi Library item row actions
   * @param array $actions
   * @param \WP_Post $post
   * @return array
   * @since 1.3.0
   */
  public function duplicate_library_link($actions, $post)
  {
    if ($post->post_type !== 'et_pb_layout' || !current_user_can('edit_posts')) {
      return $actions;
    }
    $actions['duplicate_library'] = '<a href="' . esc_url(admin_url('admin.php?action=duplicate_library_as_draft&post=' . $post->ID . '&duplicate_nonce=' . wp_create_nonce('duplicate_nonce'))) . '" title="Duplicate this library item" rel="permalink">Duplicate</a>';
    return $actions;
  }

  /**
   * Handles duplication of Divi Library items as drafts
   * @return void
   * @since 1.3.0
   */
  public function duplicate_library_as_draft()
  {
    if (!current_user_can('edit_posts')) {
      return;
    }
    if (!isset($_GET['duplicate_nonce']) || !wp_verify_nonce(sanitize_text_field($_GET['duplicate_nonce']), 'duplicate_nonce')) {
      return;
    }

    $post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;
    $post = get_post($post_id);

    if (!$post || $post->post_type !== 'et_pb_layout') {
      wp_die('Invalid library item.');
    }

    $new_post_id = wp_insert_post([
      'post_title'     => $post->post_title . ' -- Copy',
      'post_type'      => $post->post_type,
      'post_status'    => 'draft',
      'post_content'   => $post->post_content,
      'post_excerpt'   => $post->post_excerpt,
      'post_name'      => $post->post_name,
      'comment_status' => $post->comment_status,
      'ping_status'    => $post->ping_status,
      'post_author'    => get_current_user_id(),
    ]);

    if (is_wp_error($new_post_id)) {
      wp_die('Library item duplication failed.');
    }

    // Copy taxonomies
    $taxonomies = get_object_taxonomies($post->post_type);
    foreach ($taxonomies as $taxonomy) {
      $terms = wp_get_object_terms($post_id, $taxonomy, ['fields' => 'slugs']);
      wp_set_object_terms($new_post_id, $terms, $taxonomy);
    }

    // Copy post meta
    global $wpdb;
    $post_meta = $wpdb->get_results($wpdb->prepare(
      "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d",
      $post_id
    ));
    foreach ($post_meta as $meta) {
      if ($meta->meta_key === '_wp_old_slug') {
        continue;
      }
      update_post_meta($new_post_id, $meta->meta_key, maybe_unserialize($meta->meta_value));
    }

    wp_safe_redirect(admin_url('edit.php?post_type=et_pb_layout'));
    exit();
  }

  /**
   * Hooks into new post creation to enable Divi Builder by default
   * @return void
   * @since 1.3.0
   */
  public function builder_default_on_new_post()
  {
    add_action('wp_insert_post', [$this, 'enable_builder_on_insert'], 10, 2);
  }

  /**
   * Enables the Divi Builder on newly created posts
   * @param int $post_id
   * @param \WP_Post $post
   * @return void
   * @since 1.3.0
   */
  public function enable_builder_on_insert($post_id, $post)
  {
    remove_action('wp_insert_post', [$this, 'enable_builder_on_insert']);

    if (!isset($post->post_type)) {
      return;
    }

    $allowed_post_types = $this->get_module_option('builder_post_types');
    if (!is_array($allowed_post_types) || empty($allowed_post_types)) {
      return;
    }

    if (!function_exists('et_builder_get_enabled_builder_post_types')) {
      return;
    }

    $builder_post_types = et_builder_get_enabled_builder_post_types();
    if (!in_array($post->post_type, $builder_post_types) || !in_array($post->post_type, $allowed_post_types)) {
      return;
    }

    wp_update_post([
      'ID'           => $post_id,
      'post_status'  => 'draft',
      'post_title'   => '',
      'post_content' => '[et_pb_section][/et_pb_section]',
    ]);
    update_post_meta($post_id, '_et_pb_use_builder', 'on');

    wp_safe_redirect(admin_url("post.php?post={$post_id}&action=edit"));
    exit();
  }


  // =====================================================================
  // Frontend
  // =====================================================================

  /**
   * Enqueues frontend CSS assets
   * @return void
   * @since 1.0.0
   */
  public function enqueue_frontend_assets()
  {
    if ($this->is_option_enabled('hyphens')) {
      wp_enqueue_style('divi-child-hyphens', $this->module->get_asset_url('css/misc-hyphens.min.css'));
    }
    if ($this->is_option_enabled('mobile_menu_breakpoint')) {
      wp_enqueue_style('divi-child-mobile-menu-breakpoint', $this->module->get_asset_url('css/misc-mobile-menu-breakpoint.min.css'));
    }
    if ($this->is_option_enabled('mobile_menu_fullscreen')) {
      wp_enqueue_style('divi-child-mobile-menu-fullscreen', $this->module->get_asset_url('css/misc-mobile-menu-fullscreen.min.css'));
    }
  }

  /**
   * Renders inline JS to add target/rel attributes to all external links on the page
   * Uses JS instead of PHP content filter to cover Theme Builder, widgets, footer etc.
   * @return void
   * @since 1.3.0
   */
  public function render_external_links_script()
  {
    $rel_value = $this->get_module_option('external_links_rel') ?: 'noopener noreferrer nofollow';
    $site_host = wp_parse_url(home_url(), PHP_URL_HOST);
    ?>
    <script>
    (function() {
      var siteHost = <?php echo wp_json_encode($site_host); ?>;
      var relValue = <?php echo wp_json_encode($rel_value); ?>;
      document.querySelectorAll('a[href]').forEach(function(link) {
        try {
          var url = new URL(link.href, window.location.origin);
          if (url.protocol !== 'http:' && url.protocol !== 'https:') return;
          if (url.hostname === siteHost || url.hostname.endsWith('.' + siteHost)) return;
          if (!link.hasAttribute('target')) link.setAttribute('target', '_blank');
          link.setAttribute('rel', relValue);
        } catch(e) {}
      });
    })();
    </script>
    <?php
  }
}
