<?php
/**
 * Database migration for Baleno Booking System
 * Run this to update existing installations
 */
class Baleno_Migration {

    /**
     * Run all pending migrations
     */
    public static function run() {
        self::add_payment_tracking_columns();
    }

    /**
     * Add payment_received and receipt_issued columns
     */
    private static function add_payment_tracking_columns() {
        global $wpdb;
        $table = $wpdb->prefix . 'baleno_bookings';

        // Check if columns already exist
        $columns = $wpdb->get_results("SHOW COLUMNS FROM $table LIKE 'payment_received'");
        if (empty($columns)) {
            $wpdb->query("ALTER TABLE $table ADD COLUMN payment_received tinyint(1) DEFAULT 0 AFTER payment_receipt");
        }

        $columns = $wpdb->get_results("SHOW COLUMNS FROM $table LIKE 'receipt_issued'");
        if (empty($columns)) {
            $wpdb->query("ALTER TABLE $table ADD COLUMN receipt_issued tinyint(1) DEFAULT 0 AFTER payment_received");
        }
    }
}
