<?php
class DC_Woodle_Settings_Sync {
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;
  
  private $tab;

  /**
   * Start up
   */
  public function __construct($tab) {
    $this->tab = $tab;
    $this->options = get_option( "dc_{$this->tab}_settings_name" );
    $this->settings_page_init();
  }
  
  /**
   * Register and add settings
   */
  public function settings_page_init() {
    global $DC_Woodle;
    
    $settings_tab_options = array("tab" => "{$this->tab}",
                                  "ref" => &$this,
                                  "sections" => array("sync_section" => array("title" => "Synchronise Courses And Course Categories From Moodle", // Another section
                                  "fields" => array("sync_now" => array('title' => __('Synchronise now ?', $DC_Woodle->text_domain), 'type' => 'radio', 'id' => 'sync_now', 'label_for' => 'sync_now', 'name' => 'sync_now', 'options' => array('yes' => 'Yes', 'no' => 'No'), 'dfvalue' => 'no', 'desc' => __('Select yes to sync courses and course categories from Moodle.', $DC_Woodle->text_domain)),// Radio
                                  "sync_course" => array('title' => __('Synchronise by Courses ?', $DC_Woodle->text_domain), 'type' => 'radio', 'id' => 'sync_course', 'label_for' => 'sync_course', 'name' => 'sync_course', 'options' => array('yes' => 'Yes', 'no' => 'No'), 'dfvalue' => 'yes'),
                                    "sync_category" => array('title' => __('Synchronise by Categories ?', $DC_Woodle->text_domain), 'type' => 'radio', 'id' => 'sync_category', 'label_for' => 'sync_category', 'name' => 'sync_category', 'options' => array('yes' => 'Yes', 'no' => 'No'), 'dfvalue' => 'yes'), 
                                    "action" => array('title' => '', 'type' => 'hidden', 'id' => 'action', 'name' => 'action', 'value' => 'sync_courses_and_categories'), // Hidden
                                                                                                          )
                                                                                         )
                                                      )
                                  );
    
    $DC_Woodle->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
  }

  /** 
   * Print the Section text
   */
  public function sync_section_info() {}
}