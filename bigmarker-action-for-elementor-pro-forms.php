<?php

/**
 * Plugin Name: BigMarker Action for Elementor Pro Forms
 * Description: Plugin to extend Elementor forms with BigMarker.
 * Plugin URI: https://wordpress.org/plugins/bigmarker-action-for-elementor-pro-forms
 * Version:     1.2.1
 * Requires at least: 5.5
 * Requires PHP: 7.3
 * Author:      Krisztian Czako
 * Author URI:  https://devopsakademia.com/
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: bigmarker-action-for-elementor-pro-forms
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Main BigMarker Elements Extension Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class BigMarker_Action_for_Elementor_Pro_Forms {

    /**
     * Plugin Version
     *
     * @since 1.0.0
     *
     * @var string The plugin version.
     */
    const VERSION = '1.0.0';

    /**
     * Minimum Elementor Version
     *
     * @since 1.0.0
     *
     * @var string Minimum Elementor version required to run the plugin.
     */
    const MINIMUM_ELEMENTOR_VERSION = '3.0.0';

    /**
     * Minimum PHP Version
     *
     * @since 1.0.0
     *
     * @var string Minimum PHP version required to run the plugin.
     */
    const MINIMUM_PHP_VERSION = '7.3';

    /**
     * Instance
     *
     * @since 1.0.0
     *
     * @access private
     * @static
     *
     * @var BigMarker_Action_for_Elementor_Pro_Forms The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @since 1.0.0
     *
     * @access public
     * @static
     *
     * @return BigMarker_Action_for_Elementor_Pro_Forms An instance of the class.
     */
    public static function instance() {

        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;

    }

    /**
     * Constructor
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function __construct() {

        add_action( 'init', [ $this, 'i18n' ] );
        add_action( 'plugins_loaded', [ $this, 'init' ] );

    }

    /**
     * Load Textdomain
     *
     * Load plugin localization files.
     *
     * Fired by `init` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function i18n() {

        load_plugin_textdomain( 'bigmarker-action-for-elementor-pro-forms' );

    }

    /**
     * Initialize the plugin
     *
     * Load the plugin only after Elementor (and other plugins) are loaded.
     * Checks for basic plugin requirements, if one check fail don't continue,
     * if all check have passed load the files required to run the plugin.
     *
     * Fired by `plugins_loaded` action hook.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function init() {

        // Check if Elementor installed and activated
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
            return;
        }

        // Check for required Elementor version
        if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
            return;
        }

        // Check for required PHP version
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
            return;
        }

        // Check if Elementor Pro Exists
        if(!function_exists( 'elementor_pro_load_plugin' )){
            add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
            return;
        }

        // Add Plugin actions
        add_action( 'elementor_pro/init', [ $this, 'init_actions' ] );
    }
    /**
     * Admin notice
     *
     * Warning when the site doesn't have Elementor installed or activated.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_missing_main_plugin() {

        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

        $message = sprintf(
        /* translators: 1: Plugin name 2: Elementor Pro*/
            esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'bigmarker-action-for-elementor-pro-forms' ),
            '<strong>' . esc_html__( 'BigMarker Action for Elementor Pro Forms', 'bigmarker-action-for-elementor-pro-forms' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor Pro', 'bigmarker-action-for-elementor-pro-forms' ) . '</strong>'
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required Elementor version.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_minimum_elementor_version() {

        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

        $message = sprintf(
        /* translators: 1: Plugin name 2: Elementor Pro 3: Required Elementor Pro version */
            esc_html__( '"%1$s" requires "%2$s" version "%3$s" or greater.', 'bigmarker-action-for-elementor-pro-forms' ),
            '<strong>' . esc_html__( 'BigMarker Action for Elementor Pro Forms', 'bigmarker-action-for-elementor-pro-forms' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor Pro', 'bigmarker-action-for-elementor-pro-forms' ) . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have a minimum required PHP version.
     *
     * @since 1.0.0
     *
     * @access public
     */
    public function admin_notice_minimum_php_version() {

        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

        $message = sprintf(
        /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'bigmarker-action-for-elementor-pro-forms' ),
            '<strong>' . esc_html__( 'BigMarker Action for Elementor Pro Forms', 'bigmarker-action-for-elementor-pro-forms' ) . '</strong>',
            '<strong>' . esc_html__( 'PHP', 'bigmarker-action-for-elementor-pro-forms' ) . '</strong>',
            self::MINIMUM_PHP_VERSION
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

    }

  /**
   * Init form actions
   *
   * Include the after form submit class and register it
   *
   * @since 1.1.2
   *
   * @access public
   */
  public function init_actions() {
    // Here its safe to include our action class file
    include_once( 'includes/bigmarker-action-after-submit.php' );

    // Instantiate the action class
    $bigmarker_action = new BigMarker_Action_After_Submit();

    // Register the action with form widget
    \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $bigmarker_action->get_name(), $bigmarker_action );
  }

}

BigMarker_Action_for_Elementor_Pro_Forms::instance();
