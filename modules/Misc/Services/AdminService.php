<?php

namespace DiviChild\Modules\Misc;

use DiviChild\Core\Abstracts\ModuleService;
use DiviChild\Core\Interfaces\AdminServiceInterface;

class AdminService extends ModuleService implements AdminServiceInterface
{
  /**
   * Initializes the common service
   * @return void
   * @package Misc
   * @since 1.1.0
   */
  public function init_admin()
  {
    parent::init_admin();

    // 1. Admin Dark Mode
    if ($this->is_option_enabled('admin_dark_mode')) {
      add_action('admin_head', [$this, 'get_theme_colors']);
    }

    // 2. Duplicate Posts
    if ($this->is_option_enabled('duplicate_posts')) {
      add_action("admin_action_duplicate_post_as_draft", [$this, "duplicate_post_as_draft"]);
      add_filter("post_row_actions", [$this, "duplicate_post_link"], 10, 2);
      add_filter("page_row_actions", [$this, "duplicate_post_link"], 10, 2);
    }

  }

  public function enqueue_admin_assets()
  {

    // 1. Admin Dark Mode Styles
    if ($this->is_option_enabled('admin_dark_mode')) {
      wp_enqueue_style('admin-dark-mode', $this->module->get_asset_url("css/misc-admin-dark-mode.min.css"), [], $this->module->get_version());
    }
  }

  /**
   * Adds admin theme colors of a user to CSS variables
   * @return void
   * @package Misc
   * @since 1.1.0
   */
  public function get_theme_colors()
  {
    $user_id = get_current_user_id();
    $color_scheme = get_user_option('admin_color', $user_id);
    global $_wp_admin_css_colors;
    if (isset($_wp_admin_css_colors[$color_scheme])) {
      $colors = $_wp_admin_css_colors[$color_scheme]->colors;
      // Wenn Modern und nur 3 Farben, zweite Farbe aus der ersten ableiten
      if ($color_scheme === 'modern' && count($colors) === 3) {
        $lighter = $this->lighten_color($colors[0], 0.1);
        array_splice($colors, 1, 0, $lighter);
      }
      echo '<style>:root {';
      foreach ($colors as $i => $color) {
        echo "--wp-admin-theme-color-$i: $color;";
      }
      echo '}</style>';
    }
  }


  public function lighten_color($hex, $percent)
  {
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
      $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $r = min(255, intval($r + (255 - $r) * $percent));
    $g = min(255, intval($g + (255 - $g) * $percent));
    $b = min(255, intval($b + (255 - $b) * $percent));
    return sprintf("#%02x%02x%02x", $r, $g, $b);
  }


  /**
   * Handles the duplication of posts as drafts
   * @return void
   * @package Misc
   * @since 1.1.0
   */
  public function duplicate_post_as_draft()
  {
    if (!current_user_can("edit_posts")) {
      return;
    }
    if (!isset($_GET["duplicate_nonce"]) || !wp_verify_nonce(sanitize_text_field($_GET["duplicate_nonce"]), 'duplicate_nonce')) {
      return;
    }
    global $wpdb;
    if (!(isset($_GET["post"]) || isset($_POST["post"]) || (isset($_REQUEST["action"]) && "duplicate_post_as_draft" == $_REQUEST["action"]))) {
      wp_die("No post to duplicate has been supplied!");
    }

    $post_id = isset($_GET["post"]) ? absint($_GET["post"]) : absint($_POST["post"]);
    $post = get_post($post_id);
    $current_user = wp_get_current_user();
    $new_post_author = $current_user->ID;
    if (isset($post) && $post != null) {
      $args = [
        "comment_status" => $post->comment_status,
        "ping_status" => $post->ping_status,
        "post_author" => $new_post_author,
        "post_content" => $post->post_content,
        "post_excerpt" => $post->post_excerpt,
        "post_name" => $post->post_name,
        "post_parent" => $post->post_parent,
        "post_password" => $post->post_password,
        "post_status" => "draft",
        "post_title" => $post->post_title . ' -- Copy',
        "post_type" => $post->post_type,
        "to_ping" => $post->to_ping,
        "menu_order" => $post->menu_order,
      ];

      $new_post_id = wp_insert_post($args);
      $taxonomies = get_object_taxonomies($post->post_type);
      foreach ($taxonomies as $taxonomy) {
        $post_terms = wp_get_object_terms($post_id, $taxonomy, [
          "fields" => "slugs",
        ]);
        wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
      }
      $post_meta_infos = $wpdb->get_results($wpdb->prepare("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d", $post_id)); // phpcs:ignore
      if (count($post_meta_infos) != 0) {
        $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) VALUES ";
        $sql_query_params = array();
        foreach ($post_meta_infos as $meta_info) {
          $meta_key = $meta_info->meta_key;
          if ($meta_key == "_wp_old_slug") {
            continue;
          }
          $meta_value = $meta_info->meta_value;
          $sql_query .= "(%d, %s, %s),";
          $sql_query_params[] = $new_post_id;
          $sql_query_params[] = $meta_key;
          $sql_query_params[] = $meta_value;
        }
        $sql_query = rtrim($sql_query, ',');
        $wpdb->query($wpdb->prepare($sql_query, $sql_query_params)); // phpcs:ignore
      }
      wp_safe_redirect(admin_url("post.php?action=edit&post={$new_post_id}"));
      exit();
    } else {
      wp_die("Post creation failed, could not find original post: " . absint($post_id));
    }
  }


  /**
   * Adds a "Duplicate" link to the post and page row actions
   * @param array $actions
   * @param object $post
   * @return array
   * @package Misc
   * @since 1.1.0
   */
  public function duplicate_post_link($actions, $post)
  {
    if (current_user_can('edit_posts')) {
      $actions['duplicate'] = '<a href="' . esc_url(admin_url('admin.php?action=duplicate_post_as_draft&post=' . $post->ID . '&duplicate_nonce=' . wp_create_nonce('duplicate_nonce'))) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
    }
    return $actions;
  }

}