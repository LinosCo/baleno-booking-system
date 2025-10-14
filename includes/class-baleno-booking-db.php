<?php
/**
 * Database operations for Baleno Booking System
 */
class Baleno_Booking_DB {

    /**
     * Get all spaces
     */
    public static function get_spaces($active_only = true) {
        global $wpdb;
        $table = $wpdb->prefix . 'baleno_spaces';

        $where = $active_only ? "WHERE is_active = 1" : "";
        $sql = "SELECT * FROM $table $where ORDER BY display_order ASC";

        return $wpdb->get_results($sql);
    }

    /**
     * Get space by ID
     */
    public static function get_space($space_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'baleno_spaces';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $space_id
        ));
    }

    /**
     * Get all equipment
     */
    public static function get_equipment($active_only = true) {
        global $wpdb;
        $table = $wpdb->prefix . 'baleno_equipment';

        $where = $active_only ? "WHERE is_active = 1" : "";
        $sql = "SELECT * FROM $table $where ORDER BY equipment_name ASC";

        return $wpdb->get_results($sql);
    }

    /**
     * Create a new booking
     */
    public static function create_booking($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'baleno_bookings';

        // Generate unique booking code
        $booking_code = self::generate_booking_code();

        $booking_data = array(
            'booking_code' => $booking_code,
            'user_id' => get_current_user_id() ?: null,
            'full_name' => sanitize_text_field($data['full_name']),
            'codice_fiscale' => strtoupper(sanitize_text_field($data['codice_fiscale'])),
            'email' => sanitize_email($data['email']),
            'phone' => sanitize_text_field($data['phone']),
            'address' => sanitize_text_field($data['address']),
            'city' => sanitize_text_field($data['city']),
            'cap' => sanitize_text_field($data['cap']),
            'organization_name' => sanitize_text_field($data['organization_name']),
            'organization_address' => sanitize_text_field($data['organization_address']),
            'organization_piva' => sanitize_text_field($data['organization_piva']),
            'space_id' => intval($data['space_id']),
            'external_space' => isset($data['external_space']) ? 1 : 0,
            'booking_date' => sanitize_text_field($data['booking_date']),
            'start_time' => sanitize_text_field($data['start_time']),
            'end_time' => sanitize_text_field($data['end_time']),
            'time_slot' => sanitize_text_field($data['time_slot']),
            'num_people' => intval($data['num_people']),
            'event_type' => sanitize_text_field($data['event_type']),
            'event_description' => sanitize_textarea_field($data['event_description']),
            'special_notes' => sanitize_textarea_field($data['special_notes']),
            'equipment_ids' => isset($data['equipment_ids']) ? json_encode($data['equipment_ids']) : null,
            'total_price' => floatval($data['total_price']),
            'caution_paid' => 0,
            'payment_status' => 'pending',
            'booking_status' => 'pending'
        );

        $result = $wpdb->insert($table, $booking_data);

        if ($result) {
            return array(
                'success' => true,
                'booking_id' => $wpdb->insert_id,
                'booking_code' => $booking_code
            );
        }

        return array('success' => false, 'error' => $wpdb->last_error);
    }

    /**
     * Update an existing booking
     */
    public static function update_booking($booking_id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'baleno_bookings';

        $booking_data = array(
            'full_name' => sanitize_text_field($data['full_name']),
            'codice_fiscale' => strtoupper(sanitize_text_field($data['codice_fiscale'])),
            'email' => sanitize_email($data['email']),
            'phone' => sanitize_text_field($data['phone']),
            'address' => sanitize_text_field($data['address']),
            'city' => sanitize_text_field($data['city']),
            'cap' => sanitize_text_field($data['cap']),
            'organization_name' => sanitize_text_field($data['organization_name']),
            'organization_address' => sanitize_text_field($data['organization_address']),
            'organization_piva' => sanitize_text_field($data['organization_piva']),
            'space_id' => intval($data['space_id']),
            'external_space' => isset($data['external_space']) ? 1 : 0,
            'booking_date' => sanitize_text_field($data['booking_date']),
            'start_time' => sanitize_text_field($data['start_time']),
            'end_time' => sanitize_text_field($data['end_time']),
            'time_slot' => sanitize_text_field($data['time_slot']),
            'num_people' => intval($data['num_people']),
            'event_type' => sanitize_text_field($data['event_type']),
            'event_description' => sanitize_textarea_field($data['event_description']),
            'special_notes' => sanitize_textarea_field($data['special_notes']),
            'equipment_ids' => isset($data['equipment_ids']) ? json_encode($data['equipment_ids']) : null,
            'total_price' => floatval($data['total_price'])
        );

        $result = $wpdb->update(
            $table,
            $booking_data,
            array('id' => $booking_id),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%f'),
            array('%d')
        );

        if ($result !== false) {
            return array(
                'success' => true,
                'booking_id' => $booking_id
            );
        }

        return array('success' => false, 'error' => $wpdb->last_error);
    }

    /**
     * Update booking status
     */
    public static function update_booking_status($booking_id, $status, $user_id = null, $reason = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'baleno_bookings';

        $data = array('booking_status' => $status);

        if ($status === 'approved') {
            $data['approved_by'] = $user_id ?: get_current_user_id();
            $data['approved_date'] = current_time('mysql');
        }

        if ($status === 'rejected' && $reason) {
            $data['rejection_reason'] = $reason;
        }

        return $wpdb->update(
            $table,
            $data,
            array('id' => $booking_id),
            array('%s', '%d', '%s', '%s'),
            array('%d')
        );
    }

    /**
     * Get booking by ID
     */
    public static function get_booking($booking_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'baleno_bookings';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $booking_id
        ));
    }

    /**
     * Get booking by code
     */
    public static function get_booking_by_code($booking_code) {
        global $wpdb;
        $table = $wpdb->prefix . 'baleno_bookings';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE booking_code = %s",
            $booking_code
        ));
    }

    /**
     * Get all bookings with filters
     */
    public static function get_bookings($filters = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'baleno_bookings';
        $spaces_table = $wpdb->prefix . 'baleno_spaces';

        $where = array();
        $params = array();

        if (!empty($filters['status'])) {
            $where[] = "b.booking_status = %s";
            $params[] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $where[] = "b.booking_date >= %s";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where[] = "b.booking_date <= %s";
            $params[] = $filters['date_to'];
        }

        if (!empty($filters['space_id'])) {
            $where[] = "b.space_id = %d";
            $params[] = $filters['space_id'];
        }

        $where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT b.*, s.space_name, s.space_code
                FROM $table b
                LEFT JOIN $spaces_table s ON b.space_id = s.id
                $where_sql
                ORDER BY b.booking_date DESC, b.start_time DESC";

        if (!empty($params)) {
            $sql = $wpdb->prepare($sql, $params);
        }

        return $wpdb->get_results($sql);
    }

    /**
     * Check availability for a space on a specific date/time
     */
    public static function check_availability($space_id, $date, $start_time, $end_time, $exclude_booking_id = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'baleno_bookings';

        $sql = "SELECT COUNT(*) FROM $table
                WHERE space_id = %d
                AND booking_date = %s
                AND booking_status IN ('pending', 'approved')
                AND (
                    (start_time < %s AND end_time > %s) OR
                    (start_time < %s AND end_time > %s) OR
                    (start_time >= %s AND end_time <= %s)
                )";

        $params = array($space_id, $date, $end_time, $start_time, $end_time, $end_time, $start_time, $end_time);

        if ($exclude_booking_id) {
            $sql .= " AND id != %d";
            $params[] = $exclude_booking_id;
        }

        $count = $wpdb->get_var($wpdb->prepare($sql, $params));

        return $count == 0;
    }

    /**
     * Delete booking
     */
    public static function delete_booking($booking_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'baleno_bookings';

        return $wpdb->delete($table, array('id' => $booking_id), array('%d'));
    }

    /**
     * Get bookings statistics
     */
    public static function get_statistics() {
        global $wpdb;
        $table = $wpdb->prefix . 'baleno_bookings';

        $stats = array();

        // Total bookings
        $stats['total'] = $wpdb->get_var("SELECT COUNT(*) FROM $table");

        // Pending bookings
        $stats['pending'] = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE booking_status = 'pending'");

        // Approved bookings
        $stats['approved'] = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE booking_status = 'approved'");

        // Rejected bookings
        $stats['rejected'] = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE booking_status = 'rejected'");

        // This month bookings
        $stats['this_month'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE MONTH(booking_date) = %d AND YEAR(booking_date) = %d",
            date('m'),
            date('Y')
        ));

        // Total revenue
        $stats['total_revenue'] = $wpdb->get_var(
            "SELECT SUM(total_price) FROM $table WHERE booking_status = 'approved' AND payment_status = 'paid'"
        ) ?: 0;

        return $stats;
    }

    /**
     * Generate unique booking code
     */
    private static function generate_booking_code() {
        global $wpdb;
        $table = $wpdb->prefix . 'baleno_bookings';

        do {
            $code = 'BAL-' . date('Y') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE booking_code = %s",
                $code
            ));
        } while ($exists > 0);

        return $code;
    }

    /**
     * Calculate price based on space and duration
     */
    public static function calculate_price($space_id, $time_slot, $equipment_ids = array()) {
        $space = self::get_space($space_id);
        if (!$space) {
            return 0;
        }

        $price = 0;

        switch ($time_slot) {
            case '1h':
                $price = floatval($space->price_1h);
                break;
            case '2h':
                $price = floatval($space->price_2h);
                break;
            case 'half_day':
                $price = floatval($space->price_half_day);
                break;
            case 'full_day':
                $price = floatval($space->price_full_day);
                break;
        }

        // Add equipment prices
        if (!empty($equipment_ids)) {
            global $wpdb;
            $equipment_table = $wpdb->prefix . 'baleno_equipment';
            $ids = implode(',', array_map('intval', $equipment_ids));

            $equipment_price = $wpdb->get_var(
                "SELECT SUM(price) FROM $equipment_table WHERE id IN ($ids)"
            );

            $price += floatval($equipment_price);
        }

        return $price;
    }
}
