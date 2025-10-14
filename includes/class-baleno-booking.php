<?php
/**
 * The core plugin class
 */
class Baleno_Booking {

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->version = BALENO_BOOKING_VERSION;
        $this->plugin_name = 'baleno-booking';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        require_once BALENO_BOOKING_PLUGIN_DIR . 'includes/class-baleno-loader.php';
        require_once BALENO_BOOKING_PLUGIN_DIR . 'includes/class-baleno-booking-db.php';
        require_once BALENO_BOOKING_PLUGIN_DIR . 'includes/class-baleno-booking-email.php';
        require_once BALENO_BOOKING_PLUGIN_DIR . 'admin/class-baleno-admin.php';
        require_once BALENO_BOOKING_PLUGIN_DIR . 'public/class-baleno-public.php';

        $this->loader = new Baleno_Loader();
    }

    private function define_admin_hooks() {
        $plugin_admin = new Baleno_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');

        // AJAX actions
        $this->loader->add_action('wp_ajax_baleno_approve_booking', $plugin_admin, 'approve_booking');
        $this->loader->add_action('wp_ajax_baleno_reject_booking', $plugin_admin, 'reject_booking');
        $this->loader->add_action('wp_ajax_baleno_delete_booking', $plugin_admin, 'delete_booking');
        $this->loader->add_action('wp_ajax_baleno_get_bookings', $plugin_admin, 'get_bookings_ajax');
        $this->loader->add_action('wp_ajax_baleno_create_manual_booking', $plugin_admin, 'create_manual_booking');
        $this->loader->add_action('wp_ajax_baleno_update_booking', $plugin_admin, 'update_booking');
    }

    private function define_public_hooks() {
        $plugin_public = new Baleno_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        // Register shortcodes
        $this->loader->add_action('init', $plugin_public, 'register_shortcodes');

        // AJAX actions for public
        $this->loader->add_action('wp_ajax_baleno_submit_booking', $plugin_public, 'submit_booking');
        $this->loader->add_action('wp_ajax_nopriv_baleno_submit_booking', $plugin_public, 'submit_booking');
        $this->loader->add_action('wp_ajax_baleno_check_availability', $plugin_public, 'check_availability');
        $this->loader->add_action('wp_ajax_nopriv_baleno_check_availability', $plugin_public, 'check_availability');
        $this->loader->add_action('wp_ajax_baleno_get_space_price', $plugin_public, 'get_space_price');
        $this->loader->add_action('wp_ajax_nopriv_baleno_get_space_price', $plugin_public, 'get_space_price');
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_loader() {
        return $this->loader;
    }

    public function get_version() {
        return $this->version;
    }
}
