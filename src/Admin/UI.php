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

  public function modules()
  {
    ?>
    <div class="dvc-modules-container">
      <div class="dvc-modules-grid">

      </div>
      <h2><?php esc_html_e('Modules', 'divi-child'); ?></h2>
      <ul class="modules-list">
        <li><a href="#"><?php esc_html_e('Module 1', 'divi-child'); ?></a></li>
        <li><a href="#"><?php esc_html_e('Module 2', 'divi-child'); ?></a></li>
        <li><a href="#"><?php esc_html_e('Module 3', 'divi-child'); ?></a></li>
      </ul>
    </div>
    <?php
  }

  private function module($name, $description, $version, $enabled)
  {
    ?>
    <li class="divi-child-module">
      <h3><?php echo esc_html($name); ?></h3>
      <p><?php echo esc_html($description); ?></p>
      <label>
        <input type="checkbox" <?php checked($enabled); ?> />
        <?php esc_html_e('Enable', 'divi-child'); ?>
      </label>
    </li>
    <?php
  }

}