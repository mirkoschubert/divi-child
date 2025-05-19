<?php

namespace DiviChild\Admin;

use DiviChild\Core\Config;

final class UI {

  protected $config;
  protected $options;

  public function __construct()
  {
    $this->config = new Config();
    $this->options = $this->config->get_options();
  }

  public function header($headline, $version) {
    ?>
    <div class="dvc-header">
      <h1><?php echo esc_html($headline); ?> <small class="version">v<?php echo esc_html($version); ?></small></h1>
    </div>
    <?php
  }

  public function checkbox($module, $id, $description, $version = NULL)
    {
      ?>
			<label for="<?php echo $id; ?>">
				<?php $checked = $this->config->get_option($module, $id); ?>
				<input type="checkbox" name="dvc_options[<?php echo $module; ?>][<?php echo $id; ?>]" id="<?php echo $id; ?>" <?php checked($checked, 'on'); ?>> <?php echo $description;
                   if ($version) { ?> <span class="versions"><?php echo $version; ?></span><?php } ?>
			</label>
			<br>
			<?php
    }

  /**
   * Save the options via AJAX
   * @return void
   * @since 3.0.0
   */
  public function list_modules($modules)
  {
    ?>
    <div class="dvc-modules">
      <div class="modules-grid">
      <?php foreach ($modules as $slug => $module) : ?>
        <div class="module <?php echo $module['enabled'] ? 'enabled' : 'disabled'; ?>">
          <div class="module-content">
            <div class="module-info">
              <h2><?php echo esc_html($module['name']); ?> <small class="version">v<?php echo esc_html($module['version']); ?></small></h2>
              <p><?php echo esc_html($module['description']); ?></p>
            </div>
            <div class="toggle-field">
              <label class="toggle-switch">
                <input type="checkbox" class="toggle-input module-toggle" data-slug="<?php echo esc_attr($slug); ?>" <?php checked($module['enabled'] === true || $module['enabled'] === 'on', true); ?>>
                <span class="toggle-slider"></span>
              </label>
            </div>
          </div>
          <?php if (!empty($module['options'])) : ?>
          <div class="module-footer">
            <button class="btn settings-btn" data-slug="<?php echo esc_attr($module['slug']); ?>" <?php echo $module['enabled'] ? '' : 'disabled'; ?>>
              <?php esc_html_e('Einstellungen', 'divi-child'); ?>
            </button>
          </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
      </div>
    </div>
    <?php
  }

    public function modal() {
  ?>
  <div id="module-settings-modal" class="dvc-modal" style="display:none;">
    <div class="dvc-modal-content">
      <div class="dvc-modal-header">
        <h3 id="module-settings-title"><!-- Module title is loaded dynamically --></h3>
        <button type="button" class="dvc-modal-close">&times;</button>
      </div>
      <div class="dvc-modal-body" id="module-settings-container">
        <!-- Module settings are loaded dynamically -->
      </div>
      <div class="dvc-modal-footer">
        <div id="save-status" class="save-status"></div>
        <div class="modal-actions">
          <button type="button" class="button button-secondary close-modal-btn"><?php esc_html_e('Abbrechen', 'divi-child'); ?></button>
          <button type="button" class="button button-primary save-module-settings"><?php esc_html_e('Speichern', 'divi-child'); ?></button>
        </div>
      </div>
    </div>
  </div>
  <?php
}
  
  /**
   * Rendes the settings form for a module
   * @param string $module_slug
   * @param array $settings_definition
   * @param array $current_values
   * @return void
   * @since 3.0.0
   */
  public function render_module_settings_form($module_slug, $settings_definition, $current_values = []) {
    if (empty($settings_definition)) {
      echo '<p>' . __('For this module are no settings available.', 'divi-child') . '</p>';
      return;
    }

    echo '<form id="module-settings-form" data-module="' . esc_attr($module_slug) . '">';
    
    foreach ($settings_definition as $field_id => $field_config) {
      // "enabled" überspringen, da wir das über den Hauptschalter steuern
      if ($field_id === 'enabled') continue;
      
      $current_value = $current_values[$field_id] ?? $field_config['default'];
      
      switch ($field_config['type']) {
        case 'toggle':
          $this->toggle_field($field_id, $field_config, $current_value);
          break;
        case 'text':
          $this->text_field($field_id, $field_config, $current_value);
          break;
        case 'textarea':
          $this->textarea_field($field_id, $field_config, $current_value);
          break;
        case 'select':
          $this->select_field($field_id, $field_config, $current_value);
          break;
        case 'number':
          $this->number_field($field_id, $field_config, $current_value);
          break;
        case 'list':
          $this->list_field($field_id, $field_config, $current_value);
          break;
        // Weitere Feldtypen nach Bedarf
      }
      
    }
    
    echo '</form>';
  }

  /**
   * Renders a toggle field
   * @since 3.0.0
   */
  private function toggle_field($field_id, $field_config, $current_value) {
    ?>
    <div class="dvc-field toggle-field">
      <div class="field-info">
        <span class="field-label" id="label-<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_config['label']); ?></span>
        <?php if (!empty($field_config['description'])): ?>
          <p class="field-description"><?php echo esc_html($field_config['description']); ?></p>
        <?php endif; ?>
      </div>
      <label for="<?php echo esc_attr($field_id); ?>" class="toggle-switch">
        <input type="checkbox" 
               id="<?php echo esc_attr($field_id); ?>" 
               name="<?php echo esc_attr($field_id); ?>" 
               <?php checked($current_value); ?>
               class="toggle-input"
               aria-labelledby="label-<?php echo esc_attr($field_id); ?>">
        <span class="toggle-slider"></span>
        <span class="screen-reader-text"><?php echo esc_html($field_config['label']); ?></span>
      </label>
    </div>
    <?php
  }

  /**
   * Renders a text field
   * @since 3.0.0
   */
  private function text_field($field_id, $field_config, $current_value) {
    ?>
    <div class="dvc-field text-field">
      <label for="<?php echo esc_attr($field_id); ?>" class="field-label">
        <?php echo esc_html($field_config['label']); ?>
      </label>
      <?php if (!empty($field_config['description'])): ?>
        <p class="field-description"><?php echo esc_html($field_config['description']); ?></p>
      <?php endif; ?>
      <input type="text" 
             id="<?php echo esc_attr($field_id); ?>" 
             name="<?php echo esc_attr($field_id); ?>" 
             value="<?php echo esc_attr($current_value); ?>"
             class="regular-text">
    </div>
    <?php
  }

  /**
   * Renders a textarea field
   * @since 3.0.0
   */
  private function textarea_field($field_id, $field_config, $current_value) {
    ?>
    <div class="dvc-field textarea-field">
      <label for="<?php echo esc_attr($field_id); ?>" class="field-label">
        <?php echo esc_html($field_config['label']); ?>
      </label>
      <?php if (!empty($field_config['description'])): ?>
        <p class="field-description"><?php echo esc_html($field_config['description']); ?></p>
      <?php endif; ?>
      <textarea id="<?php echo esc_attr($field_id); ?>" 
                name="<?php echo esc_attr($field_id); ?>"
                class="large-text" 
                rows="5"><?php echo esc_textarea($current_value); ?></textarea>
    </div>
    <?php
  }

  /**
   * Renders a select field
   * @since 3.0.0
   */
  private function select_field($field_id, $field_config, $current_value) {
    ?>
    <div class="dvc-field select-field">
      <label for="<?php echo esc_attr($field_id); ?>" class="field-label">
        <?php echo esc_html($field_config['label']); ?>
      </label>
      <?php if (!empty($field_config['description'])): ?>
        <p class="field-description"><?php echo esc_html($field_config['description']); ?></p>
      <?php endif; ?>
      <select id="<?php echo esc_attr($field_id); ?>" 
              name="<?php echo esc_attr($field_id); ?>">
        <?php foreach ($field_config['options'] as $option_value => $option_label): ?>
          <option value="<?php echo esc_attr($option_value); ?>" 
                  <?php selected($current_value, $option_value); ?>>
            <?php echo esc_html($option_label); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php
  }

  /**
   * Renders a number field
   * @since 3.0.0
   */
  private function number_field($field_id, $field_config, $current_value) {
    $min = $field_config['min'] ?? '';
    $max = $field_config['max'] ?? '';
    $step = $field_config['step'] ?? '1';
    ?>
    <div class="dvc-field number-field"<?php echo $this->get_dependency_attributes($field_config); ?>>
      <div class="field-info">
        <label for="<?php echo esc_attr($field_id); ?>" class="field-label">
          <?php echo esc_html($field_config['label']); ?>
        </label>
        <?php if (!empty($field_config['description'])): ?>
          <p class="field-description"><?php echo esc_html($field_config['description']); ?></p>
        <?php endif; ?>
      </div>
      <input type="number" 
             id="<?php echo esc_attr($field_id); ?>" 
             name="<?php echo esc_attr($field_id); ?>" 
             value="<?php echo esc_attr($current_value); ?>"
             min="<?php echo esc_attr($min); ?>" 
             max="<?php echo esc_attr($max); ?>" 
             step="<?php echo esc_attr($step); ?>"
             class="small-text">
    </div>
    <?php
  }

  /**
   * Rendert ein Liste-Eingabefeld
   * @param string $field_id Feld-ID
   * @param array $field_config Feld-Konfiguration
   * @param mixed $current_value Aktueller Wert
   * @since 3.0.0
   */
  private function list_field($field_id, $field_config, $current_value) {
    // Sicherstellen, dass der Wert ein Array ist
    if (!is_array($current_value)) {
      $current_value = ($current_value && !empty($current_value)) ? [$current_value] : [];
    }
    
    // Default-Wert verwenden, wenn aktueller Wert leer ist
    if (empty($current_value) && isset($field_config['default']) && !empty($field_config['default'])) {
      $current_value = is_array($field_config['default']) ? $field_config['default'] : [$field_config['default']];
    }
    
    ?>
    <div class="dvc-field list-field">
      <div class="field-info">
        <span class="field-label" id="label-<?php echo esc_attr($field_id); ?>"><?php echo esc_html($field_config['label']); ?></span>
        <?php if (!empty($field_config['description'])): ?>
          <p class="field-description"><?php echo esc_html($field_config['description']); ?></p>
        <?php endif; ?>
        <div class="validation-message"></div>
      </div>
      
      <div class="list-input-wrapper">
        <input type="text" 
               id="<?php echo esc_attr($field_id); ?>_new" 
               class="list-entry-input regular-text"
               placeholder="<?php esc_attr_e('Type new entry and press Enter', 'divi-child'); ?>"
               aria-labelledby="label-<?php echo esc_attr($field_id); ?>"
               <?php if (isset($field_config['validate']) && isset($field_config['validate']['pattern'])): ?>
               pattern="<?php echo esc_attr(trim($field_config['validate']['pattern'], '/')); ?>"
               <?php endif; ?>>
        <button type="button" class="button add-list-item" data-target="<?php echo esc_attr($field_id); ?>">
          <?php esc_html_e('Hinzufügen', 'divi-child'); ?>
        </button>
      </div>
      
      <ul class="list-items-container" id="<?php echo esc_attr($field_id); ?>_items">
        <?php foreach ($current_value as $index => $item): ?>
          <li class="list-item">
            <span class="list-item-text"><?php echo esc_html($item); ?></span>
            <input type="hidden" name="<?php echo esc_attr($field_id); ?>[]" value="<?php echo esc_attr($item); ?>">
            <button type="button" class="button button-small remove-list-item" aria-label="<?php esc_attr_e('Remove entry', 'divi-child'); ?>">
              <span class="dashicons dashicons-no-alt"></span>
            </button>
          </li>
        <?php endforeach; ?>
      </ul>
      
      <?php if (empty($current_value)): ?>
        <p class="list-empty-message"><?php esc_html_e('No entries available yet. Add entries using the field above.', 'divi-child'); ?></p>
      <?php endif; ?>
    </div>
    <?php
  }

  /**
   * Helper function to get dependency attributes
   * @return array Attribute und CSS-Klassen für Abhängigkeiten
   */
  private function get_dependency_attributes($field_config): string {
    if (!isset($field_config['depends_on'])) {
      return '';
    }
    
    $dependencies = [];
    
    foreach ($field_config['depends_on'] as $dep_field => $dep_value) {
      $dependencies[] = 'data-depends-on="' . esc_attr($dep_field) . '" data-depends-value="' . esc_attr($dep_value) . '"';      
    }

    return ' ' . implode(' ', $dependencies);
  }
}