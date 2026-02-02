<?php

namespace DiviChild\Core\Traits;

trait DependencyChecker
{
  /**
   * Checks if all dependencies for a setting are met
   * @param array $dependencies
   * @return array
   */
  public function check_dependencies(array $dependencies): array
  {
    $result = ['supported' => true];

    // WordPress Version Check
    if (isset($dependencies['wordpress'])) {
      $wp_version = get_bloginfo('version');
      $wp_check = $this->check_version_constraint($wp_version, $dependencies['wordpress']);
      if (!$wp_check['valid']) {
        $result['supported'] = false;
      }
    }

    // Divi Version Check
    if (isset($dependencies['divi'])) {
      $divi_version = function_exists('et_get_theme_version') ? et_get_theme_version() : '0.0.0';
      $divi_check = $this->check_version_constraint($divi_version, $dependencies['divi']);
      if (!$divi_check['valid']) {
        $result['supported'] = false;
      }
    }

    // Plugin Dependencies
    if (isset($dependencies['plugins'])) {
      foreach ($dependencies['plugins'] as $plugin => $constraint) {
        if (!$this->check_plugin_active($plugin, $constraint)) {
          $result['supported'] = false;
        }
      }
    }

    return $result;
  }

  /**
   * Checks version constraint with flexible operators
   * @param string $current Current version
   * @param string $constraint Version constraint (e.g., ">= 4.7", "< 5.8", "= 4.9.1", "4.0-4.5")
   * @return array
   */
  private function check_version_constraint(string $current, string $constraint): array
  {
    // Check for range constraint (e.g., "4.0-4.5")
    if (\preg_match('/^(.+?)\s*-\s*(.+)$/', \trim($constraint), $matches)) {
      $min_version = $matches[1];
      $max_version = $matches[2];
      
      $valid = version_compare($current, $min_version, '>=') && version_compare($current, $max_version, '<=');
      
      return ['valid' => $valid];
    }
    
    // Parse single constraint
    if (\preg_match('/^(>=|<=|>|<|=)\s*(.+)$/', \trim($constraint), $matches)) {
      $operator = $matches[1];
      $version = $matches[2];
      
      $valid = false;
      
      switch ($operator) {
        case '>=':
          $valid = version_compare($current, $version, '>=');
          break;
        case '<=':
          $valid = version_compare($current, $version, '<=');
          break;
        case '>':
          $valid = version_compare($current, $version, '>');
          break;
        case '<':
          $valid = version_compare($current, $version, '<');
          break;
        case '=':
          $valid = version_compare($current, $version, '=');
          break;
      }
      
      return ['valid' => $valid];
    }
    
    return ['valid' => true];
  }

  /**
   * Checks if a plugin is active and meets version constraints
   * @param string $plugin Plugin slug or file
   * @param string $constraint Version constraint
   * @return bool
   */
  private function check_plugin_active(string $plugin, string $constraint = ''): bool
  {
    if (!function_exists('is_plugin_active')) {
      include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    
    // Simple active check if no constraint
    if (empty($constraint)) {
      return is_plugin_active($plugin);
    }
    
    // TODO: Implement plugin version checking if needed
    return is_plugin_active($plugin);
  }
}