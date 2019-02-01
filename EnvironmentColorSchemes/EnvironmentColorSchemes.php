<?php
/**
 * Environment Color Schemes
 *
 * NOTE: SITE_ENVIRONMENT should be defined in wp-config
 * CSS_DIR should be corrected for your environment
 *
 */

namespace EnvironmentColorSchemes;


class EnvironmentColorSchemes {
    const CSS_DIR = "EnvironmentColorSchemes/css/"; // 

    // Set environments array - NOTE scheme and icons are only used for user color picker
    const ENVIRONMENTS = [
        'dev'     => [
            'colors' => array('#123018', '#2f533b', '#356e49', '#c46c96'),
            'icons'  => array('base' => '#ece6f6', 'focus' => '#fff', 'current' => '#fff')
        ],
        'sandbox' => [
            'colors' => array('#123018', '#2f533b', '#356e49', '#c46c96'),
            'icons'  => array('base' => '#ece6f6', 'focus' => '#fff', 'current' => '#fff')
        ],
        'test'    => [
            'colors' => array('#47111f', '#843a4c', '#a2405c', '#b3647d'),
            'icons'  => array('base' => '#e5f8ff', 'focus' => '#fff', 'current' => '#fff')
        ],
        'stage'   => [
            'colors' => array('#052e3d', '#204863', '#1c5988', '#337d9c'),
            'icons'  => array('base' => '#e5f8ff', 'focus' => '#fff', 'current' => '#fff')
        ],
        'prod'    => [
            'colors' => array('#25282b', '#363b3f', '#69a8bb', '#e14d43'),
            'icons'  => array('base' => '#f1f2f3', 'focus' => '#fff', 'current' => '#fff')
        ],
    ];


    public function __construct() {
        \add_filter('get_user_option_admin_color', array($this, 'getEnvironment'), 5);
        \add_action('admin_init', array($this, 'registerEnvironmentColorScheme'));
        \add_action('wp_enqueue_scripts', array($this, 'enqueueEnvironmentColorScheme'));

        //Disable user color scheme picker
        \remove_action("admin_color_scheme_picker", "admin_color_scheme_picker");
    }


    function getEnvironment() {
        // check if environment is defined - default is dev
        if (defined('SITE_ENVIRONMENT')) {

            //Set color scheme to environment const value
            $_normalizedEnv = strtolower(SITE_ENVIRONMENT);
            /**
             * Check if in const ENVIRONMENTS to prevent invalid/unaccepted environments
             * NOTE: key_exists() is used because php 5.6 will throw a fatal error when isset() is used on the result of an expression
             *  checking !empty() will throw a undefined index notice when the value is not in the array
             */
            if (key_exists($_normalizedEnv, self::ENVIRONMENTS)) {
                //
                return $_normalizedEnv;
            }
        }

        return 'dev';

    }

    /**
     * Get the environment object
     */
    function getEnvironmentColorSchemeObj() {
        return self::ENVIRONMENTS[self::getEnvironment()];
    }

    /**e
     * register the color scheme
     * works on the backend only!!!
     */
    function registerEnvironmentColorScheme() {
        //get the environment and obj
        $_env    = self::getEnvironment();
        $_envObj = self::getEnvironmentColorSchemeObj();

        // Register the scheme
        wp_admin_css_color($_env, _x(ucwords($_env) . ' Environment', 'admin color scheme'),
            self::CSS_DIR . $_env . ".css",
            $_envObj['colors'],
            $_envObj['icons']
        );
    }


    /**
     * Enqueue the registered color schemes on the front end
     */
    function enqueueEnvironmentColorScheme() {
        if (!is_admin_bar_showing()) {
            return;
        }

        // get the admin color user option
        $_adminColorScheme = get_user_option('admin_color');

        //get the environment obj
        $_env    = self::getEnvironment();
        $_envObj = self::getEnvironmentColorSchemeObj();

        if (isset($_adminColorScheme)) {
            wp_enqueue_style($_adminColorScheme . 'color-scheme', self::CSS_DIR . $_env . ".css");
        }
    }
}



