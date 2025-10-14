<?php
/**
 * The admin-specific functionality of the plugin
 */
class Baleno_Admin {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            BALENO_BOOKING_PLUGIN_URL . 'assets/css/baleno-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            BALENO_BOOKING_PLUGIN_URL . 'assets/js/baleno-admin.js',
            array('jquery'),
            $this->version,
            false
        );

        wp_localize_script($this->plugin_name, 'balenoAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('baleno_admin_nonce')
        ));
    }

    public function add_plugin_admin_menu() {
        // Check if user has permission
        if (!current_user_can('manage_baleno_bookings')) {
            return;
        }

        // Main menu
        add_menu_page(
            'Baleno Booking',
            'Baleno Booking',
            'manage_baleno_bookings',
            'baleno-bookings',
            array($this, 'display_bookings_page'),
            'dashicons-calendar-alt',
            30
        );

        // Submenu: All Bookings
        add_submenu_page(
            'baleno-bookings',
            'Tutte le Prenotazioni',
            'Prenotazioni',
            'manage_baleno_bookings',
            'baleno-bookings',
            array($this, 'display_bookings_page')
        );

        // Submenu: Calendar
        add_submenu_page(
            'baleno-bookings',
            'Calendario',
            'Calendario',
            'manage_baleno_bookings',
            'baleno-calendar',
            array($this, 'display_calendar_page')
        );

        // Submenu: Spaces
        add_submenu_page(
            'baleno-bookings',
            'Gestione Spazi',
            'Spazi',
            'manage_baleno_bookings',
            'baleno-spaces',
            array($this, 'display_spaces_page')
        );

        // Submenu: Equipment
        add_submenu_page(
            'baleno-bookings',
            'Gestione Attrezzature',
            'Attrezzature',
            'manage_baleno_bookings',
            'baleno-equipment',
            array($this, 'display_equipment_page')
        );

        // Submenu: Settings
        add_submenu_page(
            'baleno-bookings',
            'Impostazioni',
            'Impostazioni',
            'manage_options',
            'baleno-settings',
            array($this, 'display_settings_page')
        );
    }

    /**
     * Display bookings page
     */
    public function display_bookings_page() {
        $stats = Baleno_Booking_DB::get_statistics();
        $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';

        $filters = array();
        if ($status_filter) {
            $filters['status'] = $status_filter;
        }

        $bookings = Baleno_Booking_DB::get_bookings($filters);
        ?>
        <div class="wrap baleno-admin-page">
            <h1>Gestione Prenotazioni Baleno</h1>

            <!-- Statistics Dashboard -->
            <div class="baleno-stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-content">
                        <h3><?php echo esc_html($stats['total']); ?></h3>
                        <p>Prenotazioni Totali</p>
                    </div>
                </div>
                <div class="stat-card pending">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-content">
                        <h3><?php echo esc_html($stats['pending']); ?></h3>
                        <p>In Attesa</p>
                    </div>
                </div>
                <div class="stat-card approved">
                    <div class="stat-icon">✅</div>
                    <div class="stat-content">
                        <h3><?php echo esc_html($stats['approved']); ?></h3>
                        <p>Approvate</p>
                    </div>
                </div>
                <div class="stat-card rejected">
                    <div class="stat-icon">❌</div>
                    <div class="stat-content">
                        <h3><?php echo esc_html($stats['rejected']); ?></h3>
                        <p>Rifiutate</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    <div class="stat-content">
                        <h3><?php echo esc_html($stats['this_month']); ?></h3>
                        <p>Questo Mese</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-content">
                        <h3>€ <?php echo number_format($stats['total_revenue'], 2, ',', '.'); ?></h3>
                        <p>Entrate Totali</p>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="baleno-filters">
                <a href="<?php echo admin_url('admin.php?page=baleno-bookings'); ?>"
                   class="filter-btn <?php echo $status_filter === '' ? 'active' : ''; ?>">
                    Tutte
                </a>
                <a href="<?php echo admin_url('admin.php?page=baleno-bookings&status=pending'); ?>"
                   class="filter-btn <?php echo $status_filter === 'pending' ? 'active' : ''; ?>">
                    In Attesa (<?php echo $stats['pending']; ?>)
                </a>
                <a href="<?php echo admin_url('admin.php?page=baleno-bookings&status=approved'); ?>"
                   class="filter-btn <?php echo $status_filter === 'approved' ? 'active' : ''; ?>">
                    Approvate
                </a>
                <a href="<?php echo admin_url('admin.php?page=baleno-bookings&status=rejected'); ?>"
                   class="filter-btn <?php echo $status_filter === 'rejected' ? 'active' : ''; ?>">
                    Rifiutate
                </a>
            </div>

            <!-- Bookings Table -->
            <div class="baleno-table-container">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Codice</th>
                            <th>Cliente</th>
                            <th>Contatti</th>
                            <th>Sala</th>
                            <th>Data/Ora</th>
                            <th>Persone</th>
                            <th>Importo</th>
                            <th>Stato</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center;">Nessuna prenotazione trovata.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr class="booking-row status-<?php echo esc_attr($booking->booking_status); ?>">
                                    <td>
                                        <strong><?php echo esc_html($booking->booking_code); ?></strong>
                                    </td>
                                    <td>
                                        <strong><?php echo esc_html($booking->full_name); ?></strong><br>
                                        <small>CF: <?php echo esc_html($booking->codice_fiscale); ?></small>
                                        <?php if ($booking->organization_name): ?>
                                            <br><small>Org: <?php echo esc_html($booking->organization_name); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small>
                                            📧 <?php echo esc_html($booking->email); ?><br>
                                            📱 <?php echo esc_html($booking->phone); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <strong><?php echo esc_html($booking->space_name); ?></strong><br>
                                        <small><?php echo esc_html($booking->space_code); ?></small>
                                    </td>
                                    <td>
                                        <?php echo date('d/m/Y', strtotime($booking->booking_date)); ?><br>
                                        <small><?php echo esc_html($booking->start_time); ?> - <?php echo esc_html($booking->end_time); ?></small>
                                    </td>
                                    <td><?php echo esc_html($booking->num_people); ?></td>
                                    <td>
                                        <strong>€ <?php echo number_format($booking->total_price, 2, ',', '.'); ?></strong>
                                    </td>
                                    <td>
                                        <?php echo $this->get_status_badge($booking->booking_status); ?>
                                    </td>
                                    <td class="actions">
                                        <button class="button button-small btn-view-details"
                                                data-booking-id="<?php echo esc_attr($booking->id); ?>">
                                            👁 Dettagli
                                        </button>
                                        <?php if ($booking->booking_status === 'pending'): ?>
                                            <button class="button button-primary button-small btn-approve"
                                                    data-booking-id="<?php echo esc_attr($booking->id); ?>">
                                                ✓ Approva
                                            </button>
                                            <button class="button button-small btn-reject"
                                                    data-booking-id="<?php echo esc_attr($booking->id); ?>">
                                                ✗ Rifiuta
                                            </button>
                                        <?php endif; ?>
                                        <button class="button button-small button-link-delete btn-delete"
                                                data-booking-id="<?php echo esc_attr($booking->id); ?>">
                                            🗑 Elimina
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Booking Details Modal -->
        <div id="booking-details-modal" class="baleno-modal" style="display: none;">
            <div class="baleno-modal-content">
                <span class="baleno-modal-close">&times;</span>
                <div id="booking-details-content"></div>
            </div>
        </div>

        <!-- Rejection Reason Modal -->
        <div id="rejection-modal" class="baleno-modal" style="display: none;">
            <div class="baleno-modal-content">
                <span class="baleno-modal-close">&times;</span>
                <h2>Motivo del Rifiuto</h2>
                <form id="rejection-form">
                    <input type="hidden" id="reject-booking-id" name="booking_id">
                    <div class="form-group">
                        <label for="rejection-reason">Inserisci il motivo del rifiuto:</label>
                        <textarea id="rejection-reason" name="reason" rows="5" required></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="button button-primary">Conferma Rifiuto</button>
                        <button type="button" class="button baleno-modal-close">Annulla</button>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Display calendar page
     */
    public function display_calendar_page() {
        $bookings = Baleno_Booking_DB::get_bookings(array('status' => 'approved'));
        $spaces = Baleno_Booking_DB::get_spaces();
        ?>
        <div class="wrap baleno-admin-page">
            <h1>Calendario Prenotazioni</h1>

            <div class="calendar-filters">
                <label for="calendar-space-filter">Filtra per Sala:</label>
                <select id="calendar-space-filter">
                    <option value="">Tutte le sale</option>
                    <?php foreach ($spaces as $space): ?>
                        <option value="<?php echo esc_attr($space->id); ?>">
                            <?php echo esc_html($space->space_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="baleno-calendar"></div>

            <div class="calendar-legend">
                <h3>Legenda</h3>
                <div class="legend-items">
                    <span class="legend-item pending">⏳ In Attesa</span>
                    <span class="legend-item approved">✅ Approvata</span>
                    <span class="legend-item rejected">❌ Rifiutata</span>
                </div>
            </div>
        </div>

        <script>
        var balenoBookingsData = <?php echo json_encode($bookings); ?>;
        </script>
        <?php
    }

    /**
     * Display spaces management page
     */
    public function display_spaces_page() {
        $spaces = Baleno_Booking_DB::get_spaces(false);
        ?>
        <div class="wrap baleno-admin-page">
            <h1>Gestione Spazi</h1>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Codice</th>
                        <th>Nome Sala</th>
                        <th>Categoria</th>
                        <th>Capienza</th>
                        <th>Dimensioni</th>
                        <th>Tariffe</th>
                        <th>Stato</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($spaces as $space): ?>
                        <tr>
                            <td><strong><?php echo esc_html($space->space_code); ?></strong></td>
                            <td><?php echo esc_html($space->space_name); ?></td>
                            <td><?php echo esc_html($space->space_category); ?></td>
                            <td><?php echo esc_html($space->capacity); ?> persone</td>
                            <td><?php echo esc_html($space->size_mq); ?> m²</td>
                            <td>
                                <small>
                                    1h: €<?php echo number_format($space->price_1h, 2); ?><br>
                                    2h: €<?php echo number_format($space->price_2h, 2); ?><br>
                                    ½ gg: €<?php echo number_format($space->price_half_day, 2); ?><br>
                                    1 gg: €<?php echo number_format($space->price_full_day, 2); ?>
                                </small>
                            </td>
                            <td>
                                <?php echo $space->is_active ? '<span class="status-badge active">Attivo</span>' : '<span class="status-badge inactive">Non Attivo</span>'; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * Display equipment management page
     */
    public function display_equipment_page() {
        $equipment = Baleno_Booking_DB::get_equipment(false);
        ?>
        <div class="wrap baleno-admin-page">
            <h1>Gestione Attrezzature</h1>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Nome Attrezzatura</th>
                        <th>Descrizione</th>
                        <th>Prezzo</th>
                        <th>Stato</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipment as $item): ?>
                        <tr>
                            <td><strong><?php echo esc_html($item->equipment_name); ?></strong></td>
                            <td><?php echo esc_html($item->description); ?></td>
                            <td>€ <?php echo number_format($item->price, 2, ',', '.'); ?></td>
                            <td>
                                <?php echo $item->is_active ? '<span class="status-badge active">Attivo</span>' : '<span class="status-badge inactive">Non Attivo</span>'; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * Display settings page
     */
    public function display_settings_page() {
        ?>
        <div class="wrap baleno-admin-page">
            <h1>Impostazioni Baleno Booking</h1>

            <form method="post" action="options.php">
                <?php
                settings_fields('baleno_booking_settings');
                do_settings_sections('baleno-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('baleno_booking_settings', 'baleno_booking_email_admin');
        register_setting('baleno_booking_settings', 'baleno_booking_caution_amount');
        register_setting('baleno_booking_settings', 'baleno_booking_auto_approve');

        add_settings_section(
            'baleno_general_settings',
            'Impostazioni Generali',
            null,
            'baleno-settings'
        );

        add_settings_field(
            'baleno_booking_email_admin',
            'Email Amministratore',
            array($this, 'email_admin_field'),
            'baleno-settings',
            'baleno_general_settings'
        );

        add_settings_field(
            'baleno_booking_caution_amount',
            'Importo Cauzione (€)',
            array($this, 'caution_amount_field'),
            'baleno-settings',
            'baleno_general_settings'
        );

        add_settings_field(
            'baleno_booking_auto_approve',
            'Approvazione Automatica',
            array($this, 'auto_approve_field'),
            'baleno-settings',
            'baleno_general_settings'
        );
    }

    public function email_admin_field() {
        $value = get_option('baleno_booking_email_admin', get_option('admin_email'));
        echo '<input type="email" name="baleno_booking_email_admin" value="' . esc_attr($value) . '" class="regular-text">';
        echo '<p class="description">Email per ricevere le notifiche delle nuove prenotazioni</p>';
    }

    public function caution_amount_field() {
        $value = get_option('baleno_booking_caution_amount', 50);
        echo '<input type="number" name="baleno_booking_caution_amount" value="' . esc_attr($value) . '" step="0.01" min="0">';
        echo '<p class="description">Importo della cauzione richiesta (default: €50)</p>';
    }

    public function auto_approve_field() {
        $value = get_option('baleno_booking_auto_approve', 0);
        echo '<input type="checkbox" name="baleno_booking_auto_approve" value="1" ' . checked(1, $value, false) . '>';
        echo '<p class="description">Se attivata, le prenotazioni saranno approvate automaticamente</p>';
    }

    /**
     * Get status badge HTML
     */
    private function get_status_badge($status) {
        $badges = array(
            'pending' => '<span class="status-badge pending">⏳ In Attesa</span>',
            'approved' => '<span class="status-badge approved">✅ Approvata</span>',
            'rejected' => '<span class="status-badge rejected">❌ Rifiutata</span>',
            'cancelled' => '<span class="status-badge cancelled">🚫 Cancellata</span>',
        );

        return isset($badges[$status]) ? $badges[$status] : $status;
    }

    /**
     * AJAX: Approve booking
     */
    public function approve_booking() {
        check_ajax_referer('baleno_admin_nonce', 'nonce');

        if (!current_user_can('approve_baleno_bookings')) {
            wp_send_json_error(array('message' => 'Permesso negato'));
            return;
        }

        $booking_id = intval($_POST['booking_id']);

        $result = Baleno_Booking_DB::update_booking_status($booking_id, 'approved', get_current_user_id());

        if ($result !== false) {
            Baleno_Booking_Email::send_approval_email($booking_id);
            wp_send_json_success(array('message' => 'Prenotazione approvata con successo!'));
        } else {
            wp_send_json_error(array('message' => 'Errore durante l\'approvazione'));
        }
    }

    /**
     * AJAX: Reject booking
     */
    public function reject_booking() {
        check_ajax_referer('baleno_admin_nonce', 'nonce');

        if (!current_user_can('approve_baleno_bookings')) {
            wp_send_json_error(array('message' => 'Permesso negato'));
            return;
        }

        $booking_id = intval($_POST['booking_id']);
        $reason = sanitize_textarea_field($_POST['reason']);

        $result = Baleno_Booking_DB::update_booking_status($booking_id, 'rejected', get_current_user_id(), $reason);

        if ($result !== false) {
            Baleno_Booking_Email::send_rejection_email($booking_id);
            wp_send_json_success(array('message' => 'Prenotazione rifiutata'));
        } else {
            wp_send_json_error(array('message' => 'Errore durante il rifiuto'));
        }
    }

    /**
     * AJAX: Delete booking
     */
    public function delete_booking() {
        check_ajax_referer('baleno_admin_nonce', 'nonce');

        if (!current_user_can('manage_baleno_bookings')) {
            wp_send_json_error(array('message' => 'Permesso negato'));
            return;
        }

        $booking_id = intval($_POST['booking_id']);

        $result = Baleno_Booking_DB::delete_booking($booking_id);

        if ($result !== false) {
            wp_send_json_success(array('message' => 'Prenotazione eliminata'));
        } else {
            wp_send_json_error(array('message' => 'Errore durante l\'eliminazione'));
        }
    }

    /**
     * AJAX: Get bookings
     */
    public function get_bookings_ajax() {
        check_ajax_referer('baleno_admin_nonce', 'nonce');

        $filters = array();

        if (isset($_POST['status']) && !empty($_POST['status'])) {
            $filters['status'] = sanitize_text_field($_POST['status']);
        }

        if (isset($_POST['date_from']) && !empty($_POST['date_from'])) {
            $filters['date_from'] = sanitize_text_field($_POST['date_from']);
        }

        if (isset($_POST['date_to']) && !empty($_POST['date_to'])) {
            $filters['date_to'] = sanitize_text_field($_POST['date_to']);
        }

        if (isset($_POST['space_id']) && !empty($_POST['space_id'])) {
            $filters['space_id'] = intval($_POST['space_id']);
        }

        $bookings = Baleno_Booking_DB::get_bookings($filters);

        wp_send_json_success(array('bookings' => $bookings));
    }
}
