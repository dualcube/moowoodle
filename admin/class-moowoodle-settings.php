<?php
class MooWoodle_Settings {

  private $tabs = array();
  private $options;
  public $report;
  // public $current_tab;

  /*
	* Start up
	*/
  public function __construct() {
    //Admin menu
    add_action('admin_menu', array($this, 'add_settings_page'), 100);
    add_action('admin_init', array($this, 'settings_page_init'));
  }

  /**
   * Add Option page
   */
  public function add_settings_page() {
    global $MooWoodle;
    add_menu_page(
      "MooWoodle",
      "MooWoodle",
      'manage_options',
      'moowoodle',
      array($this, 'option_page'),
      esc_url($MooWoodle->plugin_url) . 'assets/images/moowoodle.png',
      50
    );

    add_submenu_page(
      'moowoodle',
      __("All Courses", 'moowoodle'),
      __("All Courses", 'moowoodle'),
      'manage_options',
      'moowoodle',
      array($this, 'option_page')
    );

    add_submenu_page(
      'moowoodle',
      __("Manage Enrolment", 'moowoodle') . apply_filters('moowoodle_pro_sticker', '<span class="mw-pro-tag">Pro</span>'),
      __("Manage Enrolment", 'moowoodle') . apply_filters('moowoodle_pro_sticker', '<span class="mw-pro-tag">Pro</span>'),
      'manage_options',
      'moowoodle-manage-enrolment',
      array($this, 'option_page')
    );

    add_submenu_page(
      'moowoodle',
      __("Synchronization", 'moowoodle'),
      __("Synchronization", 'moowoodle'),
      'manage_options',
      'moowoodle-synchronization',
      array($this, 'option_page')
    );

    add_submenu_page(
      'moowoodle',
      __("Settings", 'moowoodle'),
      __("Settings", 'moowoodle'),
      'manage_options',
      'moowoodle-settings',
      array($this, 'option_page')
    );

    if (apply_filters('moowoodle_menu_hide', true)) {
      add_submenu_page(
        'moowoodle',
        __("Upgrade to Pro", 'moowoodle'),
        '<div class="upgrade-to-pro"><i class="dashicons dashicons-awards"></i>' .  esc_html__("Upgrade to Pro", 'moowoodle') . '</div> ',
        'manage_options',
        '',
        array($this, 'handle_external_redirects')
      );
    }
  }

  // Upgrade to pro link
  public function handle_external_redirects() {
    wp_redirect(esc_url('https://dualcube.com/shop/'));
    die;
  }

  public function option_page() {
    global $MooWoodle;
    $menu_slug = null;
    $page   = $_REQUEST['page'];
    $layout = $this->moowoodle_get_page_layout(); ?>
    <div class="mw-admin-dashbord <?php echo $page; ?>">
      <div class="mw-general-wrapper">
        <div class="mw-header-wapper"><?php echo __('MooWoodle', 'moowoodle'); ?></div>
        <div class="mw-container">
          <div class="mw-middle-container-wrapper mw-horizontal-tabs">
            <div class="mw-middle-child-container">
              <?php $this->moowoodle_plugin_options_tabs(); ?>
              <div class="mw-tab-content">
                <?php if ($layout == '2-col') : ?>
                  <div class="mw-dynamic-fields-wrapper">
                  <?php endif; ?>
                  <form class="mw-dynamic-form" action="options.php" method="post">
                    <?php
                    $show_submit = false;
                    // $page = '';
                    foreach ($MooWoodle->library->moowoodle_get_options() as $v) {

                      if (isset($v['menu_slug'])) {
                        $menu_slug = $v['menu_slug'];
                      }
                      if ($menu_slug == $page) {
                        switch ($v['type']) {
                          case 'menu':
                            break;
                          case 'tab':
                            $tab = $v;
                            if (empty($default_tab)) {
                              $default_tab = $v['id'];
                            }
                            break;
                          case 'setting':
                            $current_tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
                            if ($current_tab == $tab['id']) {
                              settings_fields($v['id']);
                              $show_submit = true;
                            }
                            break;
                          case 'section':
                            $current_tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
                            if ($current_tab == $tab['id'] or $current_tab === false) {
                              if ($layout == '2-col') {
                                echo '<div id="' . esc_attr($v['id']) . '" class="mw-section-wraper">';
                                $this->moowoodle_do_settings_sections($v['id'], $show_submit);
                                echo '</div>';
                              } else {
                                $this->moowoodle_do_settings_sections($v['id']);
                              }
                            }
                            break;
                        }
                      }
                    }
                    if ($show_submit) : ?>
                      <p class="mw-save-changes">
                        <?php
                        if ($current_tab == "moowoodle-courses-sync") {
                        ?>
                          <input name="syncnow" type="submit" value="<?php esc_html_e('Sync Now', 'moowoodle'); ?>" class="button-primary" />
                        <?php
                        } elseif ($current_tab == "moowoodle-connection") {
                        ?>
                          <input name="submit" type="submit" value="<?php esc_html_e('Save All Changes', 'moowoodle'); ?>" class="button-primary" />
                        <?php
                        } elseif ($current_tab == "moowoodle-linked-courses" || $current_tab == "moowoodle-manage-enrolment" || $current_tab == "moowoodle-log") {
                        ?>
                        <?php
                        } else {
                          // echo $page;
                        ?>
                          <input name="submit" type="submit" value="<?php esc_html_e('Save All Changes', 'moowoodle'); ?>" class="button-primary" />
                        <?php
                        }
                        ?>
                      </p>
                    <?php endif; ?>
                  </form>
                  </div> <!-- #poststuff -->

              </div> <!-- .wrap -->
            </div>
            <!-- moowoodle right banner start -->
            <?php if ($layout == '2-col') : ?>
              <div class="mw-sidebar">
                <?php
                if (apply_filters('moowoodle_free_active_side_adv', true)) {
                ?>
                  <div class="mw-banner-right">
                    <a class="mw-image-adv">
                      <img src="<?php echo esc_url(plugins_url()) ?>/moowoodle/framework/coming-soon-pro-sidebar.jpg" />
                    </a>
                  </div>
                  <div class="support-widget">
                    <p class="supt-link">
                      <a href="https://wordpress.org/support/plugin/moowoodle/" target="_blank">
                        <?php esc_html_e('Got a Support Question', 'moowoodle') ?>
                      </a>
                      <i class="fas fa-question-circle"></i>
                    </p>
                  </div>
                <?php
                }

                // Additional banner for pro version
                do_action('moowoodle_additional_banner');
                ?>
              </div>
            <?php endif; ?>
            <!-- moowoodle right banner end -->
          </div>
        </div>
      </div>

      <?php
      // MooWoodle admin footer
      do_action('moowoodle_admin_footer');
    }

    public function moowoodle_get_page_layout()
    {
      global $MooWoodle;
      $layout = 'classic';
      foreach ($MooWoodle->library->moowoodle_get_options() as $v) {
        switch ($v['type']) {
          case 'menu':
            $page = $_REQUEST['page'];
            if ($page == $v['menu_slug']) {
              if (isset($v['layout'])) {
                $layout = $v['layout'];
              }
            }
            break;
        }
      }
      return $layout;
    }

    //tab 
    public function moowoodle_plugin_options_tabs()
    {
      global $MooWoodle;
      $menu_slug   = null;
      $page        = $_REQUEST['page'];
      $uses_tabs   = false;
      $current_tab = isset($_GET['tab']) ? $_GET['tab'] : false;

      //Check if this config uses tabs
      foreach ($MooWoodle->library->moowoodle_get_options() as $v) {
        if ($v['type'] == 'tab') {
          $uses_tabs = true;
          break;
        }
      }
      // If uses tabs then generate the tabs
      if ($uses_tabs) {
        echo '<div class="mw-current-tab-lists">';
        $c = 1;
        foreach ($MooWoodle->library->moowoodle_get_options() as $v) {
          if (isset($v['menu_slug'])) {
            $menu_slug = $v['menu_slug'];
          }
          if ($menu_slug == $page && $v['type'] == 'tab') {
            $active = '';
            if ($current_tab) {
              $active = $current_tab == $v['id'] ? 'nav-tab-active' : '';
            } elseif ($c == 1) {
              $active = 'nav-tab-active';
            }
            if ($v['id'] == 'moowoodle-from') {
              echo '<a id="' . esc_attr($v['id']) . '" class="nav-tab ' . $active . '" href="admin.php?moowoodle&tab=moowoodle-from">';
            } else {
              echo '<a id="' . esc_attr($v['id']) . '" class="nav-tab ' . $active . '" href="?page=' . $menu_slug . '&tab=' . $v['id'] . '">';
            }
            if (isset($v['font_class'])) {
              echo '<i class="dashicons ' . esc_attr($v['font_class']) . '"></i> ';
            }

            // Add extra tab for pro version
            do_action('moowoodle_add_additional_tabs', $v);
            echo esc_html($v['label']);
            if (isset($v['is_pro'])) {
              echo apply_filters('moowoodle_pro_sticker', '<span class="mw-pro-tag">Pro</span>');
            }
            echo '</a>';
            $c++;
          }
        }

        // For free version only
        if (apply_filters('moowoodle_free_active', true)) {
          echo '<a class="nav-tab moowoodle-upgrade" href="https://dualcube.com/shop/" target="_blank" rel="noopener noreferrer"><i class="dashicons dashicons-awards"></i> ' . esc_html__('Upgrade to Pro for More Features', 'moowoodle') . '</a>';
        }

        // Add extra tab for pro version
        do_action('moowoodle_added_extra_tab_after', $v);
        echo '</div>';
      }
    }

    public function moowoodle_do_settings_sections($page, $show_submit)
    {
      global $wp_settings_sections, $wp_settings_fields;
      if (!isset($wp_settings_sections) || !isset($wp_settings_sections[$page])) {
        return;
      }
      foreach ((array) $wp_settings_sections[$page] as $section) {
        echo '<div class="mw-section-child-wraper"><div class="mw-header-search-wrap"><div class="mw-section-header">';
        echo "<h3>{$section['title']}</h3>\n";
        echo '</div></div>';
        echo '<div class="mw-section-containt">';
        if (!isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']])) {
          continue;
        }

        $this->moowoodle_do_settings_fields($page, $section['id']);

        if ($show_submit) : ?>
          <p>
            <?php
            if ($page == "moowoodle-connection") {
            ?>

            <?php
            }
            ?>
          </p>
        <?php endif; ?>
    </div>
    </div>
<?php
        echo apply_filters('moowoodle_pro_sticker', '<div class="mw-image-overlay">
        <div class="mw-overlay-content">
        <h3>This is not accessable.</h3>
        <p>I want to know more.</p>
        <div class="mw-img-overlay-arrow">
          <span class="dashicons dashicons-arrow-down-alt"></span>
        </div>
        <a class="mw-go-pro-btn" target="_blank" href="https://dualstg.wpengine.com/product/wordpress-moowoodle-pro/">Available in MooWoodle Pro</a>
      </div>
       </div>');
      }
    }

    function moowoodle_do_settings_fields($page, $section) {
      global $wp_settings_fields;

      if (!isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section])) {
        return;
      }
      foreach ((array) $wp_settings_fields[$page][$section] as $field) {
        // echo json_encode($field);

        if (str_contains($field['id'], 'posttype')){
          if (isset($field['id']) && $field['id'] == 'test_connect_posttype') {
            echo '<div class="mw-form-group';
            if (isset($field['args']['is_pro']) && $field['args']['is_pro'] == 'pro') {
              echo apply_filters('moowoodle_pro_sticker', ' mw-pro-popup-overlay ');
            }
            echo '">';
            echo '<label class="mw-form-label " for=""><p>' .  __('Mooowoodle Test Connection', 'moowoodle') . '</p></label>';
          }
          call_user_func($field['callback'], $field['args']);
        }else{
          echo '<div class="mw-form-group';
          if (isset($field['args']['is_pro']) && $field['args']['is_pro'] == 'pro') {
            echo apply_filters('moowoodle_pro_sticker', ' mw-pro-popup-overlay ');
          }
          echo '">';
          if (!empty($field['args']['label_for'])) {
            echo '<label class="mw-form-label " for="' . esc_attr($field['args']['label_for']) . '"><p>' . $field['title'] . '</p></label>';
          } elseif (str_contains($field['id'], 'posttype')) {
            //no white space for posttype
          } else {
            echo '<label scope="row" class="mw-form-label ' . esc_attr($field['id']) . '"><p>' . $field['title'] . '</p></label>';
          }
          echo '<div class="mw-input-content">';
          call_user_func($field['callback'], $field['args']);
          echo '</div>';
        }
        echo '</div>';//middle-child-container  end
      }
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
      global $MooWoodle;
      foreach ($MooWoodle->library->moowoodle_get_options() as $k => $v) {
        switch ($v['type']) {
          case 'menu':
            $menu_slug = $v['menu_slug'];
            break;
          case 'setting':
            if (empty($v['validate_function'])) {
              $v['validate_function'] = array(
                &$this,
                'validate_machine'
              );
            }
            register_setting($v['id'], $v['id'], $v['validate_function']);
            $setting_id = $v['id'];
            break;
          case 'section':
            if (empty($v['desc_callback'])) {
              $v['desc_callback'] = array(
                &$this,
                'return_empty_string'
              );
            } else {
              $v['desc_callback'] = $v['desc_callback'];
            }
            add_settings_section($v['id'], $v['label'], $v['desc_callback'], $v['id']);
            $section_id = $v['id'];
            break;
          case 'tab':
            break;
          default:
            if (empty($v['callback'])) {
              $v['callback'] = array($this, 'field_machine');
            }

            add_settings_field(
              $v['id'],
              $v['label'],
              $v['callback'],
              $section_id,
              $section_id,
              apply_filters(
                'moowoodle_add_settings_field',
                array(
                  'id'            => $v['id'],
                  'name'          => (isset($v['name']) ? $v['name'] : ''),
                  'desc'          => (isset($v['desc']) ? $v['desc'] : ''),
                  'setting_id'    => $setting_id,
                  'class'         => (isset($v['class']) ? $v['class'] : ''),
                  'type'          => $v['type'],
                  'default_value' => (isset($v['default_value']) ? $v['default_value'] : ''),
                  'option_values' => (isset($v['option_values']) ? $v['option_values'] : ''),
                  'extra_input'   => (isset($v['extra_input']) ? $v['extra_input'] : ''),
                  'font_class'    => (isset($v['font_class']) ? $v['font_class'] : ''),
                  'disabled'      => (isset($v['disabled']) ? $v['disabled'] : ''),
                  'is_pro'        => (isset($v['is_pro']) ? $v['is_pro'] : '')

                ),
                $v
              )
            );
        }
      }
    }

    public function field_machine($args) {
      global $MooWoodle;
      extract($args); //$id, $desc, $setting_id, $class, $type, $default_value, $option_values
      // Load defaults
      $defaults = array();
      foreach ($MooWoodle->library->moowoodle_get_options() as $k) {
        switch ($k['type']) {
          case 'setting':
          case 'section':
          case 'tab':
            break;
          default:
            if (isset($k['default_value'])) {
              $defaults[$k['id']] = $k['default_value'];
            }
        }
      }

      $options = get_option($setting_id);
      $options = wp_parse_args($options, $defaults);
      $path = apply_filters('mooewoodle_field_types_posttype_path', $MooWoodle->plugin_path . 'framework/field-types/' . $type . '.php', $type);
      // echo $path;
      if (file_exists($path)) {
        // Show Field
        include($path);
        // Show description
        if (!empty($desc)) {
          echo "<p class='mw-form-description'>{$desc}</p>";
        }
      }
    }
  }
