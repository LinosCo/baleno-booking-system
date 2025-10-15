<?php
/**
 * Plugin Name: Baleno Booking System
 * Plugin URI: https://balenosanzeno.it
 * Description: Sistema completo di gestione prenotazioni per la Casa di Quartiere Baleno - San Zeno, Verona
 * Version: 1.0.0
 * Author: Nicola Zago
 * Author URI: https://balenosanzeno.it
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: baleno-booking
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Plugin version
define('BALENO_BOOKING_VERSION', '1.0.0');
define('BALENO_BOOKING_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BALENO_BOOKING_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BALENO_BOOKING_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_baleno_booking() {
    require_once BALENO_BOOKING_PLUGIN_DIR . 'includes/class-baleno-activator.php';
    Baleno_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_baleno_booking() {
    require_once BALENO_BOOKING_PLUGIN_DIR . 'includes/class-baleno-deactivator.php';
    Baleno_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_baleno_booking');
register_deactivation_hook(__FILE__, 'deactivate_baleno_booking');

/**
 * The core plugin class
 */
require BALENO_BOOKING_PLUGIN_DIR . 'includes/class-baleno-booking.php';

/**
 * Begins execution of the plugin.
 */
function run_baleno_booking() {
    $plugin = new Baleno_Booking();
    $plugin->run();
}
run_baleno_booking();
