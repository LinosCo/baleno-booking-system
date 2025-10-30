<?php
/**
 * Fired during plugin activation
 */
class Baleno_Activator {

    public static function activate() {
        self::create_tables();
        self::create_custom_role();
        self::insert_default_data();

        // Set default options
        add_option('baleno_booking_email_admin', get_option('admin_email'));
        add_option('baleno_booking_caution_amount', 50);
        add_option('baleno_booking_auto_approve', 0);
        add_option('baleno_booking_plugin_details', array(
            'name' => 'Baleno Booking System',
            'description' => 'Sistema completo di gestione prenotazioni per la Casa di Quartiere Baleno - San Zeno, Verona',
            'version' => BALENO_BOOKING_VERSION,
            'author' => 'Nicola Zago',
            'author_uri' => 'https://balenosanzeno.it',
            'plugin_uri' => 'https://balenosanzeno.it'
        ));

        // Flush rewrite rules
        flush_rewrite_rules();
    }

    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Table for bookings
        $table_bookings = $wpdb->prefix . 'baleno_bookings';
        $sql_bookings = "CREATE TABLE IF NOT EXISTS $table_bookings (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20),
            booking_code varchar(50) NOT NULL,
            full_name varchar(255) NOT NULL,
            codice_fiscale varchar(16) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(50) NOT NULL,
            address varchar(255),
            city varchar(100),
            cap varchar(10),
            organization_name varchar(255),
            organization_address varchar(255),
            organization_piva varchar(20),
            space_id bigint(20) NOT NULL,
            external_space tinyint(1) DEFAULT 0,
            booking_date date NOT NULL,
            start_time time NOT NULL,
            end_time time NOT NULL,
            time_slot varchar(50),
            num_people int(11) NOT NULL,
            event_type varchar(255),
            event_description text,
            special_notes text,
            equipment_ids text,
            total_price decimal(10,2) DEFAULT 0.00,
            caution_paid decimal(10,2) DEFAULT 0.00,
            payment_status varchar(50) DEFAULT 'pending',
            payment_receipt varchar(255),
            payment_received tinyint(1) DEFAULT 0,
            receipt_issued tinyint(1) DEFAULT 0,
            booking_status varchar(50) DEFAULT 'pending',
            approved_by bigint(20),
            approved_date datetime,
            rejection_reason text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY booking_code (booking_code),
            KEY space_id (space_id),
            KEY booking_date (booking_date),
            KEY booking_status (booking_status)
        ) $charset_collate;";

        // Table for spaces/rooms
        $table_spaces = $wpdb->prefix . 'baleno_spaces';
        $sql_spaces = "CREATE TABLE IF NOT EXISTS $table_spaces (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            space_code varchar(50) NOT NULL,
            space_name varchar(255) NOT NULL,
            space_category varchar(100),
            description text,
            capacity int(11),
            size_mq decimal(10,2),
            price_1h decimal(10,2),
            price_2h decimal(10,2),
            price_half_day decimal(10,2),
            price_full_day decimal(10,2),
            is_active tinyint(1) DEFAULT 1,
            display_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY space_code (space_code)
        ) $charset_collate;";

        // Table for equipment
        $table_equipment = $wpdb->prefix . 'baleno_equipment';
        $sql_equipment = "CREATE TABLE IF NOT EXISTS $table_equipment (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            equipment_name varchar(255) NOT NULL,
            description text,
            price decimal(10,2) DEFAULT 0.00,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_bookings);
        dbDelta($sql_spaces);
        dbDelta($sql_equipment);
    }

    private static function create_custom_role() {
        // Add custom capability to administrator
        $admin_role = get_role('administrator');
        if ($admin_role) {
            $admin_role->add_cap('manage_baleno_bookings');
            $admin_role->add_cap('approve_baleno_bookings');
        }

        // Create Baleno Manager role
        add_role(
            'baleno_manager',
            'Baleno Manager',
            array(
                'read' => true,
                'manage_baleno_bookings' => true,
                'approve_baleno_bookings' => true,
            )
        );
    }

    private static function insert_default_data() {
        global $wpdb;
        $table_spaces = $wpdb->prefix . 'baleno_spaces';
        $table_equipment = $wpdb->prefix . 'baleno_equipment';

        // Check if data already exists
        $existing = $wpdb->get_var("SELECT COUNT(*) FROM $table_spaces");
        if ($existing > 0) {
            return;
        }

        // Insert spaces from regulations
        $spaces = array(
            array(
                'space_code' => 'A1',
                'space_name' => 'Navata Completa (Pipino)',
                'space_category' => 'A. NAVATA PIPINO',
                'description' => 'Sala polifunzionale attrezzata con sala riunioni (Orata), area morbida, calcetto, ping pong, giochi. Ideale per laboratori, formazioni, riunioni.',
                'capacity' => 50,
                'size_mq' => 74,
                'price_1h' => 50.00,
                'price_2h' => 90.00,
                'price_half_day' => 120.00,
                'price_full_day' => 180.00,
                'is_active' => 1,
                'display_order' => 1
            ),
            array(
                'space_code' => 'A2',
                'space_name' => 'Sala Riunioni Orata (Pipino)',
                'space_category' => 'A. NAVATA PIPINO',
                'description' => 'Sala riunioni dotata di tavolo rettangolare e 8 sedie ed eventuali sedute supplementari. Ideale per incontri di piccoli gruppi, riunioni, corsi e laboratori.',
                'capacity' => 12,
                'size_mq' => 18.5,
                'price_1h' => 20.00,
                'price_2h' => 35.00,
                'price_half_day' => 60.00,
                'price_full_day' => 100.00,
                'is_active' => 1,
                'display_order' => 2
            ),
            array(
                'space_code' => 'A3',
                'space_name' => 'Spazio Libero (Pipino)',
                'space_category' => 'A. NAVATA PIPINO',
                'description' => 'Sala attrezzata con area morbida, calcetto, ping pong, giochi. Ideale per attività destinate a bambini e ragazzi (spazio compiti, ludoteca, spazio lettura).',
                'capacity' => 30,
                'size_mq' => 37,
                'price_1h' => 50.00,
                'price_2h' => 90.00,
                'price_half_day' => 120.00,
                'price_full_day' => 180.00,
                'is_active' => 1,
                'display_order' => 3
            ),
            array(
                'space_code' => 'B1',
                'space_name' => 'Sala Riunioni (Spagna)',
                'space_category' => 'B. NAVATA SPAGNA',
                'description' => 'Sala riunioni dotata di tavolo rotondo, 8 sedie ed eventuali sedute supplementari. Ideale per incontri di piccoli gruppi, riunioni, corsi e laboratori.',
                'capacity' => 12,
                'size_mq' => 18.5,
                'price_1h' => 20.00,
                'price_2h' => 35.00,
                'price_half_day' => 60.00,
                'price_full_day' => 100.00,
                'is_active' => 1,
                'display_order' => 4
            ),
            array(
                'space_code' => 'C',
                'space_name' => 'Navata Centrale',
                'space_category' => 'C. NAVATA CENTRALE',
                'description' => 'Salone polifunzionale attrezzato con 6 tavoli pieghevoli, 4 set tavolino e sedia, 48 sedie pieghevoli, Service audio/video.',
                'capacity' => 100,
                'size_mq' => 148,
                'price_1h' => 0,
                'price_2h' => 120.00,
                'price_half_day' => 200.00,
                'price_full_day' => 400.00,
                'is_active' => 1,
                'display_order' => 5
            ),
            array(
                'space_code' => 'D',
                'space_name' => 'Baleno Completo',
                'space_category' => 'D. BALENO COMPLETO',
                'description' => 'Tutte le dotazioni complete (eventuale utilizzo di ripostiglio e frigorifero da concordare).',
                'capacity' => 150,
                'size_mq' => 314.5,
                'price_1h' => 0,
                'price_2h' => 0,
                'price_half_day' => 400.00,
                'price_full_day' => 800.00,
                'is_active' => 1,
                'display_order' => 6
            )
        );

        foreach ($spaces as $space) {
            $wpdb->insert($table_spaces, $space);
        }

        // Insert equipment
        $equipment = array(
            array('equipment_name' => 'Videoproiettore', 'description' => 'Videoproiettore professionale', 'price' => 20.00, 'is_active' => 1),
            array('equipment_name' => 'Impianto Audio', 'description' => 'Impianto stereo e voce con 1 microfono con asta, mixer 12 ingressi e 2 casse da 800W', 'price' => 30.00, 'is_active' => 1),
            array('equipment_name' => 'Lavagna a Fogli Mobili', 'description' => 'Lavagna flipchart con fogli', 'price' => 5.00, 'is_active' => 1),
            array('equipment_name' => 'Tavoli Pieghevoli Extra', 'description' => 'Tavoli pieghevoli aggiuntivi', 'price' => 10.00, 'is_active' => 1),
            array('equipment_name' => 'Sedie Extra', 'description' => 'Sedie aggiuntive (per 10 sedie)', 'price' => 5.00, 'is_active' => 1),
            array('equipment_name' => 'Frigorifero', 'description' => 'Uso frigorifero (da concordare)', 'price' => 10.00, 'is_active' => 1)
        );

        foreach ($equipment as $item) {
            $wpdb->insert($table_equipment, $item);
        }
    }
}
