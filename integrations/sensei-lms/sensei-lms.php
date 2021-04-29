<?php
/**
 * Plugin Name:           AutomatorWP - Sensei LMS integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-sensei-lms-integration/
 * Description:           Connect AutomatorWP with Sensei LMS.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-sensei-lms-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Sensei_LMS
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Sensei_LMS {

    /**
     * @var         AutomatorWP_Integration_Sensei_LMS $instance The one true AutomatorWP_Integration_Sensei_LMS
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Sensei_LMS self::$instance The one true AutomatorWP_Integration_Sensei_LMS
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Sensei_LMS();

            if( ! self::$instance->pro_installed() ) {

                self::$instance->constants();
                self::$instance->includes();
                
            }

            self::$instance->hooks();
        }

        return self::$instance;
    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {
        // Plugin version
        define( 'AUTOMATORWP_SENSEI_LMS_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_SENSEI_LMS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_SENSEI_LMS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_SENSEI_LMS_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() ) {

            // Triggers
            require_once AUTOMATORWP_SENSEI_LMS_DIR . 'includes/triggers/complete-quiz.php';
            require_once AUTOMATORWP_SENSEI_LMS_DIR . 'includes/triggers/complete-lesson.php';
            require_once AUTOMATORWP_SENSEI_LMS_DIR . 'includes/triggers/complete-course.php';

        }
    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {

        add_action( 'automatorwp_init', array( $this, 'register_integration' ) );
        
    }

    /**
     * Registers this integration
     *
     * @since 1.0.0
     */
    function register_integration() {

        automatorwp_register_integration( 'sensei_lms', array(
            'label' => 'Sensei LMS',
            'icon'  => AUTOMATORWP_SENSEI_LMS_URL . 'assets/sensei-lms.svg',
        ) );

    }

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */
    private function meets_requirements() {

        if ( ! class_exists( 'AutomatorWP' ) ) {
            return false;
        }

        if ( ! class_exists( 'Sensei_Main' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Check if the pro version of this integration is installed
     *
     * @since  1.0.0
     *
     * @return bool True if pro version installed
     */
    private function pro_installed() {

        if ( ! class_exists( 'AutomatorWP_Sensei_LMS' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Sensei_LMS instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Sensei_LMS The one true AutomatorWP_Integration_Sensei_LMS
 */
function AutomatorWP_Integration_Sensei_LMS() {
    return AutomatorWP_Integration_Sensei_LMS::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Sensei_LMS' );
