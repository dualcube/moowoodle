<?php
class MooWoodle_Settings {

  private $tabs = array();
  public $settings_library = array();
  private $options;
  public $report;
  public $pro_sticker = '';

  /*
  * Start up
  */
  public function __construct() {
    //Admin menu
    global $MooWoodle;
    $this->settings_library = $MooWoodle->library->moowoodle_get_options();
    add_action('admin_menu', array($this, 'add_settings_page'));
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
      MOOWOODLE_TEXT_DOMAIN,
      array($this, 'option_page'),
      esc_url($MooWoodle->plugin_url) . 'assets/images/moowoodle.png',
      50
    );
    foreach ($this->settings_library["menu"] as $menu_slug => $menu) {
        add_submenu_page(
            MOOWOODLE_TEXT_DOMAIN,
            $menu['name'],
            $menu['name'],
            'manage_options',
            $menu_slug,
            array($this, 'option_page')
        );
    }
    if ($MooWoodle->moowoodle_pro_adv) {
      add_submenu_page(
        MOOWOODLE_TEXT_DOMAIN,
        __("Upgrade to Pro", MOOWOODLE_TEXT_DOMAIN),
        '<div class="upgrade-to-pro"><i class="dashicons dashicons-awards"></i>' .  esc_html__("Upgrade to Pro", MOOWOODLE_TEXT_DOMAIN) . '</div> ',
        'manage_options',
        '',
        array($this, 'handle_external_redirects')
      );
    }
    
  }

  // Upgrade to pro link
  public function handle_external_redirects() {
    wp_redirect(esc_url(MOOWOODLE_PRO_SHOP_URL));
    die;
  }

  public function option_page() {
    global $MooWoodle;
    $menu_slug = null;
    $page   = $_REQUEST['page'];
    $layout = $this->moowoodle_get_page_layout(); ?>
    <div class="mw-admin-dashbord <?php echo $page; ?>">
      <div class="mw-general-wrapper">
        <div class="mw-header-wapper"><?php echo __('MooWoodle', MOOWOODLE_TEXT_DOMAIN); ?></div>
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


                    foreach ($this->settings_library['menu'] as $menuItem) {

                      foreach ($menuItem['tabs'] as $tab_id => $tab) {
                            if (empty($default_tab)) {
                              foreach ($this->settings_library['menu'][$page]['tabs'] as $tabKey => $tabValue) {
                                $default_tab = $tabKey;
                                break;
                              }
                            }
                        $current_tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
                          if ($current_tab == $tab_id) {
                            settings_fields($tab['setting']);
                            $show_submit = true;
                            $submit_btn_value = isset($tab['submit_btn_value']) ? $tab['submit_btn_value'] : '' ;
                            $submit_btn_name = isset($tab['submit_btn_name']) ? $tab['submit_btn_name'] : 'submit' ;
                          }

                        foreach($tab['section'] as $section_id => $section){
                          $current_tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
                            if ($current_tab == $tab_id or $current_tab === false) {
                              if ($layout == '2-col') {
                                echo '<div id="' . esc_attr($section_id) . '" class="mw-section-wraper">';
                                $this->moowoodle_do_settings_sections($section_id, $show_submit);
                                echo '</div>';
                              } else {
                                $this->moowoodle_do_settings_sections($section_id);
                              }
                            }
                        }
                      }
                    }
                    if ($show_submit && $submit_btn_value != null) : ?>
                      <p class="mw-save-changes">
                        <input name="<?php esc_html_e($submit_btn_name); ?>" type="submit" value="<?php esc_html_e($submit_btn_value); ?>" class="button-primary" />
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
                if($MooWoodle->moowoodle_pro_adv) {
                ?>
                  <div class="mw-banner-right">
                    <a class="mw-image-adv">
                      <img src="<?php echo esc_url(plugins_url()) ?>/moowoodle/framework/coming-soon-pro-sidebar.jpg" />
                    </a>
                  </div>
                  <div class="support-widget">
                    <p class="supt-link">
                      <a href="<?php echo esc_url(MOOWOODLE_SUPPORT_URL)?>" target="_blank">
                        <?php esc_html_e('Got a Support Question', MOOWOODLE_TEXT_DOMAIN) ?>
                      </a>
                      <i class="fas fa-question-circle"></i>
                    </p>
                  </div>
                <?php
                }

                // Additional banner for pro version
                do_action('moowoodle_pro_right_side_bar');
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
      foreach ($this->settings_library["menu"] as $k => $v) {
        $page = $_REQUEST['page'];
        if ($page == $k) {
          if (isset($v['layout'])) {
            $layout = $v['layout'];
          }
        }
      }
      return $layout;
    }

    //tab 
    public function moowoodle_plugin_options_tabs() {
      global $MooWoodle;
      $menu_slug   = null;
      $page        = $_REQUEST['page'];
      $uses_tabs   = false;
      $current_tab = isset($_GET['tab']) ? $_GET['tab'] : false;
      $tab_count   = 1; 
      $pro_sticker = $MooWoodle->moowoodle_pro_adv ? '<span class="mw-pro-tag">Pro</span>' : '';
      //Check if this config uses tabs
      foreach ($this->settings_library['menu'] as $menuItem) {
        if (isset($menuItem['tabs'])) {
          $uses_tabs = true;
          break;
        }
      }


      // If uses tabs then generate the tabs
      if ($uses_tabs) {
        if(isset($this->settings_library['menu'][$page])){
          echo '<div class="mw-current-tab-lists">';
          if(isset($this->settings_library['menu'][$page]['tabs']))
          foreach($this->settings_library['menu'][$page]['tabs'] as $tab_id => $tab){
            $active = '';
            if ($current_tab) {
              $active = $current_tab == $tab_id ? 'nav-tab-active' : '';
            } elseif ($tab_count == 1) {
              $active = 'nav-tab-active';
            }
            if ($tab_id == 'moowoodle-from') {
              echo '<a id="' . esc_attr($tab_id) . '" class="nav-tab ' . $active . '" href="admin.php?moowoodle&tab=moowoodle-from">';
            } else {
              echo '<a id="' . esc_attr($tab_id) . '" class="nav-tab ' . $active . '" href="?page=' . $page . '&tab=' . $tab_id . '">';
            }
            if (isset($tab['font_class'])) {
              echo '<i class="dashicons ' . esc_attr($tab['font_class']) . '"></i> ';
            }

            // Add extra tab for pro version
            do_action('moowoodle_pro_tabs_adv', $tab);
            echo esc_html($tab['label']);
            if (isset($tab['is_pro'])) {
              echo $pro_sticker;
            }
            echo '</a>';
            $tab_count++;
          }
          // For free version only
          if($MooWoodle->moowoodle_pro_adv){
            echo '<a class="nav-tab moowoodle-upgrade" href="' . MOOWOODLE_PRO_SHOP_URL . '" target="_blank" rel="noopener noreferrer"><i class="dashicons dashicons-awards"></i> ' . esc_html__('Upgrade to Pro for More Features', MOOWOODLE_TEXT_DOMAIN) . '</a>';
          }
          echo '</div>';
        }
      }


    }

    public function moowoodle_do_settings_sections($page) {
      global $wp_settings_sections, $wp_settings_fields,$MooWoodle;
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
        ?>
            </div>
            </div>
        <?php
        if($MooWoodle->moowoodle_pro_adv){
          echo '<div class="mw-image-overlay">
          <div class="mw-overlay-content">
          <h3>' . esc_html__('This is not accessable.', MOOWOODLE_TEXT_DOMAIN) . '</h3>
          <p>' . esc_html__('I want to know more.', MOOWOODLE_TEXT_DOMAIN) . '</p>
          <div class="mw-img-overlay-arrow">
            <span class="dashicons dashicons-arrow-down-alt"></span>
          </div>
          <a class="mw-go-pro-btn" target="_blank" href="' . MOOWOODLE_PRO_SHOP_URL . '">' . esc_html__('Available in MooWoodle Pro', MOOWOODLE_TEXT_DOMAIN) . '</a>
          </div>
          </div>';
        }
      }
    }

    function moowoodle_do_settings_fields($page, $section) {
      global $wp_settings_fields,$MooWoodle;

      if (!isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section])) {
        return;
      }
      foreach ((array) $wp_settings_fields[$page][$section] as $field_id => $field) {

        if (str_contains($field_id, 'posttype')){
          if (isset($field_id) && $field_id == 'test_connect_posttype') {
            echo '<div class="mw-form-group';
            if (isset($field['args']['is_pro']) && $field['args']['is_pro'] == 'pro' && $MooWoodle->moowoodle_pro_adv) {
              echo ' mw-pro-popup-overlay ';
            }
            echo '">';
            echo '<label class="mw-form-label " for=""><p>' .  __('Mooowoodle Test Connection', MOOWOODLE_TEXT_DOMAIN) . '</p></label>';
          }
          call_user_func($field['callback'], $field['args']);
        }else{
          echo '<div class="mw-form-group';
          if (isset($field['args']['is_pro']) && $field['args']['is_pro'] == 'pro' && $MooWoodle->moowoodle_pro_adv) {
            echo ' mw-pro-popup-overlay ';
          }
          echo '">';
          if (!empty($field['args']['label_for'])) {
            echo '<label class="mw-form-label " for="' . esc_attr($field['args']['label_for']) . '"><p>' . $field['title'] . '</p></label>';
          } elseif (str_contains($field_id, 'posttype')) {
            //no white space for posttype
          } else {
            echo '<label scope="row" class="mw-form-label ' . esc_attr($field_id) . '"><p>' . $field['title'] . '</p></label>';
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
      $settingValues = array();
      foreach ($this->settings_library['menu'] as $menuItem) {
        foreach ($menuItem['tabs'] as $tab) {
          if (empty($tab['validate_function'])) {
              $validate_function = array(
              &$this,
              'validate_machine'
            );
            $setting_id = $tab['setting'];
            register_setting($tab['setting'], $tab['setting'], $validate_function);
          }
          foreach($tab['section'] as $section_id => $section){
            if (empty($section['desc_callback'])) {
              $section['desc_callback'] = array(
                &$this,
                'return_empty_string'
              );
            }
            add_settings_section($section_id, $section['label'], $section['desc_callback'], $section_id);
            if (is_array($section['field_types']))
            foreach($section['field_types'] as $field_id => $field){
              if (is_array($field)) {
                if (empty($field['callback'])) {
                  $field['callback'] = array($this, 'field_machine');
                }

                add_settings_field(
                  $field_id,
                  (isset($field['label']) ? $field['label'] : ''),
                  $field['callback'],
                  $section_id,
                  $section_id,
                  apply_filters(
                    'moowoodle_add_settings_field',
                    array(
                      'id'            => $field_id,
                      'name'          => (isset($field['name']) ? $field['name'] : ''),
                      'desc'          => (isset($field['desc']) ? $field['desc'] : ''),
                      'setting_id'    => $setting_id,
                      'class'         => (isset($field['class']) ? $field['class'] : ''),
                      'type'          => (isset($field['type']) ? $field['type'] : ''),
                      'default_value' => (isset($field['default_value']) ? $field['default_value'] : ''),
                      'option_values' => (isset($field['option_values']) ? $field['option_values'] : ''),
                      'extra_input'   => (isset($field['extra_input']) ? $field['extra_input'] : ''),
                      'font_class'    => (isset($field['font_class']) ? $field['font_class'] : ''),
                      'disabled'      => (isset($field['disabled']) ? $field['disabled'] : ''),
                      'is_pro'        => (isset($field['is_pro']) ? $field['is_pro'] : ''),
                      'copy_text'     => (isset($field['copy_text']) ? $field['copy_text'] : '')
                    ),
                    $field
                  )
                );
              }
            }
          }
        }
      }
  }

    public function field_machine($args) {
      global $MooWoodle;
      extract($args); //$id, $desc, $setting_id, $class, $type, $default_value, $option_values
      // Load defaults

      $defaults = array();
      foreach ($this->settings_library['menu'] as $menu => $menuData) {
        foreach ($menuData['tabs'] as $tab => $tabData) {
          foreach ($tabData['section'] as $section => $sectionData) {
            foreach ($sectionData['field_types'] as $fieldType) {
              // Check if 'id' exists in the field type and add it to the result array
              if (isset($fieldType['default_value'])) {
                  $defaults[$fieldType['id']] = $fieldType['default_value'];
              }
            }
          }
        }
      }
      $options = get_option($setting_id);
      $options = wp_parse_args($options, $defaults);
      $path = apply_filters('mooewoodle_field_types_posttype_path', $MooWoodle->plugin_path . 'framework/field-types/' . $type . '.php', $type);
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
