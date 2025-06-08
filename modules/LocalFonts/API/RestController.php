<?php

namespace DiviChild\Modules\LocalFonts;

use DiviChild\API\Abstracts\ModuleController;
use WP_REST_Server;
use Exception;

class RestController extends ModuleController
{
    /**
     * Registriert die LocalFonts-spezifischen Routes
     */
    public function register_routes()
    {
        // GET /divi-child/v1/modules/localfonts/fonts/list
        register_rest_route($this->namespace, "/{$this->rest_base}/fonts/list", [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'get_fonts_list'],
            'permission_callback' => [$this, 'check_permissions']
        ]);

        // POST /divi-child/v1/modules/localfonts/fonts/download
        register_rest_route($this->namespace, "/{$this->rest_base}/fonts/download", [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'download_fonts'],
            'permission_callback' => [$this, 'check_permissions'],
            'args' => [
                'selected_fonts' => [
                    'required' => true,
                    'type' => 'array'
                ]
            ]
        ]);

        // GET /divi-child/v1/modules/localfonts/fonts/check-updates
        register_rest_route($this->namespace, "/{$this->rest_base}/fonts/check-updates", [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'check_font_updates'],
            'permission_callback' => [$this, 'check_permissions']
        ]);

        // POST /divi-child/v1/modules/localfonts/fonts/update
        register_rest_route($this->namespace, "/{$this->rest_base}/fonts/update", [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'update_fonts'],
            'permission_callback' => [$this, 'check_permissions'],
            'args' => [
                'fonts_to_update' => [
                    'required' => true,
                    'type' => 'array'
                ]
            ]
        ]);
    }

    /**
     * Gibt verfügbare Google Fonts zurück
     */
    public function get_fonts_list($request)
    {
        try {
            $api_key = $this->get_google_fonts_api_key();

            if (empty($api_key)) {
                return $this->error_response(
                    'no_api_key',
                    __('Google Fonts API Key not defined. Please add GOOGLE_FONTS_API_KEY constant to functions.php.', 'divi-child'),
                    400
                );
            }

            $fonts_list = $this->fetch_google_fonts_api($api_key);
            
            if (empty($fonts_list)) {
                // Fallback zu web-safe Fonts
                $fonts_list = $this->get_websafe_fonts();
            }

            return $this->success_response($fonts_list);
        } catch (Exception $e) {
            return $this->error_response(
                'fonts_load_failed',
                __('Failed to load fonts list.', 'divi-child'),
                500
            );
        }
    }

    /**
     * Lädt Google Fonts über die offizielle API mit VOLLSTÄNDIGEN Daten
     */
    private function fetch_google_fonts_api($api_key)
    {
        $cache_key = 'divi_child_google_fonts_api_' . md5($api_key);
        $cached_fonts = get_transient($cache_key);
        
        if ($cached_fonts !== false) {
            return $cached_fonts;
        }

        $api_url = "https://www.googleapis.com/webfonts/v1/webfonts?key={$api_key}&sort=popularity";
        
        $response = wp_remote_get($api_url, [
            'timeout' => 15,
            'user-agent' => 'DiviLocalFonts/1.0'
        ]);

        if (is_wp_error($response)) {
            error_log('❌ Google Fonts API Error: ' . $response->get_error_message());
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!isset($data['items']) || !is_array($data['items'])) {
            error_log('❌ Google Fonts API returned invalid data');
            return [];
        }

        $fonts_list = [];
        $max_fonts = apply_filters('divi_child_max_google_fonts', 1000);
        $fonts_to_process = array_slice($data['items'], 0, $max_fonts);
        
        foreach ($fonts_to_process as $font) {
            $family = $font['family'];
            $variants = $font['variants'] ?? ['regular'];
            $subsets = $font['subsets'] ?? ['latin'];
            $category = $font['category'] ?? 'sans-serif';
            $version = $font['version'] ?? '1.0';
            $lastModified = $font['lastModified'] ?? null;
            $files = $font['files'] ?? [];
            
            // VOLLSTÄNDIGE Variants verarbeiten (alle weights + styles)
            $weights = [];
            $styles = [];
            
            foreach ($variants as $variant) {
                if ($variant === 'regular') {
                    $weights[] = '400';
                    $styles[] = 'normal';
                } elseif ($variant === 'italic') {
                    $weights[] = '400';
                    $styles[] = 'italic';
                } elseif (is_numeric($variant)) {
                    $weights[] = $variant;
                    $styles[] = 'normal';
                } elseif (preg_match('/^(\d+)italic?$/', $variant, $matches)) {
                    $weights[] = $matches[1];
                    $styles[] = 'italic';
                }
            }
            
            $weights = array_unique($weights);
            $styles = array_unique($styles);
            sort($weights, SORT_NUMERIC);
            
            if (!empty($weights)) {
                $fonts_list[] = [
                    'family' => $family,
                    'variants' => $variants, // Original variants für Download
                    'weights' => $weights,   // Extrahierte weights
                    'styles' => $styles,     // normal/italic
                    'subsets' => $subsets,
                    'category' => $category,
                    'version' => $version,
                    'lastModified' => $lastModified,
                    'files' => $files,
                    'type' => 'google'
                ];
            }
        }

        // Cache für 24 Stunden
        set_transient($cache_key, $fonts_list, DAY_IN_SECONDS);
        
        return $fonts_list;
    }

    /**
     * Web-safe Fonts als Fallback
     */
    private function get_websafe_fonts()
    {
        return [
            ['family' => 'Arial', 'weights' => ['400', '700'], 'styles' => ['normal'], 'category' => 'sans-serif', 'type' => 'websafe'],
            ['family' => 'Helvetica', 'weights' => ['400', '700'], 'styles' => ['normal'], 'category' => 'sans-serif', 'type' => 'websafe'],
            ['family' => 'Times New Roman', 'weights' => ['400', '700'], 'styles' => ['normal', 'italic'], 'category' => 'serif', 'type' => 'websafe'],
            ['family' => 'Georgia', 'weights' => ['400', '700'], 'styles' => ['normal', 'italic'], 'category' => 'serif', 'type' => 'websafe'],
            ['family' => 'Verdana', 'weights' => ['400', '700'], 'styles' => ['normal'], 'category' => 'sans-serif', 'type' => 'websafe'],
            ['family' => 'Courier New', 'weights' => ['400', '700'], 'styles' => ['normal'], 'category' => 'monospace', 'type' => 'websafe'],
        ];
    }

    /**
     * Prüft auf Font-Updates
     */
    public function check_font_updates($request)
    {
        try {
            $installed_fonts = $this->get_installed_fonts();
            $api_fonts = $this->fetch_google_fonts_api($this->get_google_fonts_api_key());
            
            $updates_available = [];
            
            foreach ($installed_fonts as $font_family => $installed_data) {
                if (!isset($installed_data['version'])) continue;
                
                $api_font = array_filter($api_fonts, function($font) use ($font_family) {
                    return $font['family'] === $font_family;
                });
                
                if (!empty($api_font)) {
                    $api_font = reset($api_font);
                    $current_version = $installed_data['version'];
                    $latest_version = $api_font['version'];
                    
                    if (version_compare($current_version, $latest_version, '<')) {
                        $updates_available[] = [
                            'family' => $font_family,
                            'current_version' => $current_version,
                            'latest_version' => $latest_version,
                            'last_modified' => $api_font['lastModified'] ?? null
                        ];
                    }
                }
            }
            
            return $this->success_response([
                'updates_available' => $updates_available,
                'total_fonts' => count($installed_fonts),
                'fonts_with_updates' => count($updates_available)
            ]);
            
        } catch (Exception $e) {
            return $this->error_response(
                'update_check_failed',
                __('Failed to check for font updates.', 'divi-child'),
                500
            );
        }
    }

    /**
     * Aktualisiert spezifische Fonts
     */
    public function update_fonts($request)
    {
        try {
            $fonts_to_update = $request->get_param('fonts_to_update');
            $result = $this->process_font_download($fonts_to_update);
            
            return $this->success_response(null, sprintf(
                __('%d fonts updated successfully.', 'divi-child'),
                count($fonts_to_update)
            ));
            
        } catch (Exception $e) {
            return $this->error_response(
                'update_failed',
                __('Font update failed.', 'divi-child'),
                500
            );
        }
    }

    /**
     * Lädt Google Fonts herunter
     */
    public function download_fonts($request)
    {
        try {
            $selected_fonts = $request->get_param('selected_fonts');
            
            if (empty($selected_fonts)) {
                return $this->error_response(
                    'no_fonts_selected',
                    __('No fonts selected.', 'divi-child')
                );
            }

            $result = $this->process_font_download($selected_fonts);
            
            if ($result['success']) {
                $this->update_installed_fonts($selected_fonts);
                return $this->success_response(null, $result['message']);
            } else {
                return $this->error_response(
                    'download_failed',
                    $result['message'],
                    500
                );
            }

        } catch (Exception $e) {
            return $this->error_response(
                'download_error',
                __('Font download failed.', 'divi-child'),
                500
            );
        }
    }

    /**
     * Verarbeitet Font-Download mit ALLEN weights/styles
     */
    private function process_font_download($fonts)
    {
        $upload_dir = wp_upload_dir();
        $fonts_dir = $upload_dir['basedir'] . '/local-fonts';
        
        if (!file_exists($fonts_dir)) {
            wp_mkdir_p($fonts_dir);
        }

        $downloaded_count = 0;
        $total_count = count($fonts);

        foreach ($fonts as $font_family) {
            if ($this->download_complete_google_font($font_family, $fonts_dir)) {
                $downloaded_count++;
            }
        }

        return [
            'success' => $downloaded_count > 0,
            'message' => sprintf(
                __('%d of %d fonts downloaded successfully.', 'divi-child'),
                $downloaded_count,
                $total_count
            ),
            'downloaded_count' => $downloaded_count,
            'total_count' => $total_count
        ];
    }

    /**
     * Lädt eine Google Font KOMPLETT herunter (alle weights/styles in latin-ext)
     */
    private function download_complete_google_font($font_family, $fonts_dir)
    {
        // ALLE weights und styles für latin-ext
        $css_url = "https://fonts.googleapis.com/css2?family=" . urlencode($font_family) . ":ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&subset=latin-ext&display=swap";
        
        $response = wp_remote_get($css_url, [
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]);
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $css_content = wp_remote_retrieve_body($response);
        
        // Font-URLs aus CSS extrahieren und Dateien herunterladen
        preg_match_all('/url\((https:\/\/fonts\.gstatic\.com\/[^)]+)\)/', $css_content, $matches);
        
        $local_css = $css_content;
        
        foreach ($matches[1] as $font_url) {
            $font_filename = basename(parse_url($font_url, PHP_URL_PATH));
            $local_font_path = $fonts_dir . '/' . $font_filename;
            
            // Font-Datei herunterladen
            $font_response = wp_remote_get($font_url);
            if (!is_wp_error($font_response)) {
                file_put_contents($local_font_path, wp_remote_retrieve_body($font_response));
                
                // URL in CSS ersetzen
                $upload_dir = wp_upload_dir();
                $font_url_local = $upload_dir['baseurl'] . '/local-fonts/' . $font_filename;
                $local_css = str_replace($font_url, $font_url_local, $local_css);
            }
        }
        
        // CSS-Datei speichern
        $css_filename = sanitize_title($font_family) . '.css';
        $css_path = $fonts_dir . '/' . $css_filename;
        
        return file_put_contents($css_path, $local_css) !== false;
    }

    /**
     * Holt installierte Fonts
     */
    private function get_installed_fonts()
    {
        return get_option('divi_child_installed_fonts', []);
    }

    /**
     * Aktualisiert installierte Fonts
     */
    private function update_installed_fonts($fonts)
    {
        $installed = $this->get_installed_fonts();
        $api_fonts = $this->fetch_google_fonts_api($this->get_google_fonts_api_key());
        
        foreach ($fonts as $font_family) {
            $api_font = array_filter($api_fonts, function($font) use ($font_family) {
                return $font['family'] === $font_family;
            });
            
            if (!empty($api_font)) {
                $api_font = reset($api_font);
                $installed[$font_family] = [
                    'version' => $api_font['version'],
                    'installed_date' => current_time('mysql'),
                    'weights' => $api_font['weights'],
                    'styles' => $api_font['styles']
                ];
            }
        }
        
        update_option('divi_child_installed_fonts', $installed);
    }

    /**
     * Google Fonts API Key aus Konstante holen
     */
    private function get_google_fonts_api_key()
    {
        return defined('GOOGLE_FONTS_API_KEY') ? GOOGLE_FONTS_API_KEY : '';
    }
}