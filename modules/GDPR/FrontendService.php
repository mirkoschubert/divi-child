<?php

namespace DiviChild\Modules\GDPR;

use DiviChild\Core\Abstracts\ModuleService;
use DiviChild\Core\Interfaces\FrontendServiceInterface;

class FrontendService extends ModuleService implements FrontendServiceInterface {
    
    /**
     * Initialisiert Frontend-spezifische Funktionalität
     * @return void
     */
    public function init_frontend() {
        parent::init_frontend();

        // 1. Comments External
        if ($this->is_option_enabled('comments_external')) {
            add_filter("comment_text", [$this, 'external_comment_links']);
            add_filter("get_comment_author_link", [$this, 'external_comment_links']);
        }

        // 2. Comments IP
        if ($this->is_option_enabled('comments_ip')) {
            add_filter('pre_comment_user_ip', [$this, 'remove_comments_ip']);
        }

        // 3. Disable Emojis
        if ($this->is_option_enabled('disable_emojis')) {
            $this->disable_emojis();
        }

        // 4. Disable oEmbeds
        if ($this->is_option_enabled('disable_oembeds')) {
            $this->disable_embeds();
        }

        // 5. Remove DNS Prefetching
        if ($this->is_option_enabled('dns_prefetching')) {
            $this->remove_dns_prefetch();
        }

        // 6. Remove REST API & XML-RPC Headers
        if ($this->is_option_enabled('rest_api')) {
            $this->remove_api_headers();
        }
    }

    /**
     * Lädt Frontend-spezifische Assets
     * @return void
     */
    public function enqueue_frontend_assets() {
        // Keine Assets für dieses Modul im Frontend
    }

    /**
     * Macht jeden Kommentar- und Autor-Link zu einem externen Link
     * @param string $content
     * @return string
     */
    public function external_comment_links($content) {
        return str_replace("<a ", "<a target='_blank' rel='noopener noreferrer' ", $content);
    }

    /**
     * Entfernt IP-Adressen aus Kommentaren
     * @param string $comment_author_ip
     * @return string
     */
    public function remove_comments_ip($comment_author_ip) {
        return '';
    }

    /**
     * Deaktiviert Emojis
     * @return void
     */
    private function disable_emojis() {
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
     * Entfernt Emoji-Plugin aus TinyMCE
     * @param array $plugins
     * @return array
     */
    public function disable_emojis_tinymce($plugins) {
        if (is_array($plugins)) {
            return array_diff($plugins, ['wpemoji']);
        }
        return [];
    }

    /**
     * Entfernt DNS-Prefetch für Emojis
     * @param array $urls
     * @param string $relation_type
     * @return array
     */
    public function disable_emojis_remove_dns_prefetch($urls, $relation_type) {
        if ('dns-prefetch' == $relation_type) {
            $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');
            $urls = array_diff($urls, [$emoji_svg_url]);
        }
        return $urls;
    }

    /**
     * Deaktiviert oEmbeds
     * @return void
     */
    private function disable_embeds() {
        remove_action('rest_api_init', 'wp_oembed_register_route'); // JSON API
        add_filter('embed_oembed_discover', '__return_false'); // Auto Discover
        remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10); // Results
        remove_action('wp_head', 'wp_oembed_add_discovery_links'); // Discovery Links
        remove_action('wp_head', 'wp_oembed_add_host_js'); // Frontend JS
        add_filter('tiny_mce_plugins', [$this, 'disable_embeds_tinymce_plugin']); // TinyMCE
        add_filter('rewrite_rules_array', [$this, 'disable_embeds_rewrites']); // Rewrite Rules
        remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10); // oEmbeds Preloader
    }

    /**
     * Entfernt oEmbed-Plugin aus TinyMCE
     * @param array $plugins
     * @return array
     */
    public function disable_embeds_tinymce_plugin($plugins) {
        return array_diff($plugins, ['wpembed']);
    }

    /**
     * Entfernt oEmbed-Rewrite-Rules
     * @param array $rules
     * @return array
     */
    public function disable_embeds_rewrites($rules) {
        foreach ($rules as $rule => $rewrite) {
            if (false !== strpos($rewrite, 'embed=true')) {
                unset($rules[$rule]);
            }
        }
        return $rules;
    }

    /**
     * Entfernt DNS-Prefetching für WordPress
     * @return void
     */
    private function remove_dns_prefetch() {
        remove_action('wp_head', 'wp_resource_hints', 2);
    }

    /**
     * Entfernt REST API & XML-RPC-Infos aus head und headers
     * @return void
     */
    private function remove_api_headers() {
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
}