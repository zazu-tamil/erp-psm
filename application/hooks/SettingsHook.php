<?php
class SettingsHook {

    public function load_settings()
    {
        $CI =& get_instance();
        $CI->load->database();
        $CI->load->model('Settings_model');

        $settings = $CI->Settings_model->get_all_settings();

        foreach ($settings as $row) {
            if (!defined($row->setting_key)) {
                define($row->setting_key, $row->setting_value);
            }
        }
    }
}

