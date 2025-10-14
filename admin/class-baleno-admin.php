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

    public function enqueue_styles($hook) {
        // Only load on Baleno pages
        if (strpos($hook, 'baleno') === false) {
            return;
        }

        wp_enqueue_style(
            $this->plugin_name,
            BALENO_BOOKING_PLUGIN_URL . 'assets/css/baleno-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    public function enqueue_scripts($hook) {
        // Only load on Baleno pages
        if (strpos($hook, 'baleno') === false) {
            return;
        }

        wp_enqueue_script(
            $this->plugin_name,
            BALENO_BOOKING_PLUGIN_URL . 'assets/js/baleno-admin.js',
            array('jquery'),
            $this->version,
            true
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

        // Submenu: New Booking
        add_submenu_page(
            'baleno-bookings',
            'Nuova Prenotazione',
            'Nuova Prenotazione',
            'manage_baleno_bookings',
            'baleno-new-booking',
            array($this, 'display_new_booking_page')
        );

        // Submenu: Edit Booking (hidden - no menu item)
        add_submenu_page(
            null,  // Hidden from menu
            'Modifica Prenotazione',
            'Modifica Prenotazione',
            'manage_baleno_bookings',
            'baleno-edit-booking',
            array($this, 'display_edit_booking_page')
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
                            <th>Creata/Modificata</th>
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
                                <?php
                                    // Check for conflicts
                                    $has_conflict = false;
                                    if ($booking->booking_status === 'pending') {
                                        $has_conflict = !Baleno_Booking_DB::check_availability(
                                            $booking->space_id,
                                            $booking->booking_date,
                                            $booking->start_time,
                                            $booking->end_time,
                                            $booking->id
                                        );
                                    }
                                    $conflict_class = $has_conflict ? 'has-conflict' : '';
                                ?>
                                <tr class="booking-row status-<?php echo esc_attr($booking->booking_status); ?> <?php echo $conflict_class; ?>">
                                    <td>
                                        <strong><?php echo esc_html($booking->booking_code); ?></strong>
                                        <?php if ($has_conflict): ?>
                                            <br><small style="color: #dc3545; font-weight: bold;">⚠️ CONFLITTO</small>
                                        <?php endif; ?>
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
                                    <td>
                                        <small>
                                            <strong>Creata:</strong><br>
                                            <?php echo date('d/m/Y H:i', strtotime($booking->created_at)); ?><br>
                                            <?php if ($booking->updated_at && $booking->updated_at !== $booking->created_at): ?>
                                                <strong style="color: #2B548E;">Modificata:</strong><br>
                                                <?php echo date('d/m/Y H:i', strtotime($booking->updated_at)); ?>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td class="actions">
                                        <button class="button button-small btn-view-details"
                                                data-booking-id="<?php echo esc_attr($booking->id); ?>">
                                            👁 Dettagli
                                        </button>
                                        <?php if ($booking->booking_status === 'pending'): ?>
                                            <a href="<?php echo admin_url('admin.php?page=baleno-edit-booking&id=' . $booking->id); ?>"
                                               class="button button-small" style="background: #2B548E; color: white;">
                                                ✏️ Modifica
                                            </a>
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

        // Get booking details
        $booking = Baleno_Booking_DB::get_booking($booking_id);

        if (!$booking) {
            wp_send_json_error(array('message' => 'Prenotazione non trovata'));
            return;
        }

        // Check for conflicts before approving
        $is_available = Baleno_Booking_DB::check_availability(
            $booking->space_id,
            $booking->booking_date,
            $booking->start_time,
            $booking->end_time,
            $booking_id  // Exclude this booking from the check
        );

        if (!$is_available) {
            // Find conflicting booking
            $conflicts = $this->find_conflicting_bookings($booking);
            $conflict_details = '';

            if (!empty($conflicts)) {
                $conflict = $conflicts[0];
                $conflict_details = sprintf(
                    'Conflitto con prenotazione %s (%s) - %s alle %s',
                    $conflict->booking_code,
                    $conflict->full_name,
                    date('d/m/Y', strtotime($conflict->booking_date)),
                    $conflict->start_time . '-' . $conflict->end_time
                );
            }

            wp_send_json_error(array(
                'message' => '⚠️ CONFLITTO RILEVATO: Esiste già una prenotazione approvata per questa sala nello stesso orario.',
                'conflict_details' => $conflict_details
            ));
            return;
        }

        // No conflicts, proceed with approval
        $result = Baleno_Booking_DB::update_booking_status($booking_id, 'approved', get_current_user_id());

        if ($result !== false) {
            Baleno_Booking_Email::send_approval_email($booking_id);
            wp_send_json_success(array('message' => 'Prenotazione approvata con successo!'));
        } else {
            wp_send_json_error(array('message' => 'Errore durante l\'approvazione'));
        }
    }

    /**
     * Find conflicting bookings for a given booking
     */
    private function find_conflicting_bookings($booking) {
        global $wpdb;
        $table = $wpdb->prefix . 'baleno_bookings';

        $sql = $wpdb->prepare(
            "SELECT * FROM $table
            WHERE space_id = %d
            AND booking_date = %s
            AND booking_status IN ('pending', 'approved')
            AND id != %d
            AND (
                (start_time < %s AND end_time > %s) OR
                (start_time < %s AND end_time > %s) OR
                (start_time >= %s AND end_time <= %s)
            )
            ORDER BY start_time ASC",
            $booking->space_id,
            $booking->booking_date,
            $booking->id,
            $booking->end_time,
            $booking->start_time,
            $booking->end_time,
            $booking->end_time,
            $booking->start_time,
            $booking->end_time
        );

        return $wpdb->get_results($sql);
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

    /**
     * Display new booking page
     */
    public function display_new_booking_page() {
        $spaces = Baleno_Booking_DB::get_spaces();
        $equipment = Baleno_Booking_DB::get_equipment();
        ?>
        <div class="wrap baleno-admin-page">
            <h1>Nuova Prenotazione Manuale</h1>
            <p class="description">Crea una nuova prenotazione direttamente dal backend. Tutti i campi contrassegnati con * sono obbligatori.</p>

            <form id="manual-booking-form" class="baleno-manual-booking-form">
                <?php wp_nonce_field('baleno_admin_nonce', 'nonce'); ?>

                <!-- Dati Personali -->
                <div class="form-section">
                    <h2>📋 Dati Personali</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name">Nome Completo *</label>
                            <input type="text" id="full_name" name="full_name" required>
                        </div>
                        <div class="form-group">
                            <label for="codice_fiscale">Codice Fiscale *</label>
                            <input type="text" id="codice_fiscale" name="codice_fiscale" maxlength="16" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Telefono *</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="address">Indirizzo *</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                        <div class="form-group">
                            <label for="city">Città *</label>
                            <input type="text" id="city" name="city" required>
                        </div>
                        <div class="form-group">
                            <label for="cap">CAP *</label>
                            <input type="text" id="cap" name="cap" maxlength="5" required>
                        </div>
                    </div>
                </div>

                <!-- Dati Organizzazione (Opzionale) -->
                <div class="form-section">
                    <h2>🏢 Dati Organizzazione (Opzionale)</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="organization_name">Nome Organizzazione</label>
                            <input type="text" id="organization_name" name="organization_name">
                        </div>
                        <div class="form-group">
                            <label for="organization_piva">P.IVA</label>
                            <input type="text" id="organization_piva" name="organization_piva" maxlength="11">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="organization_address">Indirizzo Organizzazione</label>
                        <input type="text" id="organization_address" name="organization_address">
                    </div>
                </div>

                <!-- Dettagli Prenotazione -->
                <div class="form-section">
                    <h2>🏠 Dettagli Prenotazione</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="space_id">Sala *</label>
                            <select id="space_id" name="space_id" required>
                                <option value="">Seleziona una sala</option>
                                <?php foreach ($spaces as $space): ?>
                                    <option value="<?php echo esc_attr($space->id); ?>"
                                            data-price-1h="<?php echo esc_attr($space->price_1h); ?>"
                                            data-price-2h="<?php echo esc_attr($space->price_2h); ?>"
                                            data-price-half="<?php echo esc_attr($space->price_half_day); ?>"
                                            data-price-full="<?php echo esc_attr($space->price_full_day); ?>">
                                        <?php echo esc_html($space->space_code . ' - ' . $space->space_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="booking_date">Data *</label>
                            <input type="date" id="booking_date" name="booking_date" required
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="time_slot">Fascia Oraria *</label>
                            <select id="time_slot" name="time_slot" required>
                                <option value="">Seleziona fascia oraria</option>
                                <option value="1h">1 ora</option>
                                <option value="2h">2 ore</option>
                                <option value="half_day">Mezza giornata (4 ore)</option>
                                <option value="full_day">Giornata intera (8 ore)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="start_time">Ora Inizio *</label>
                            <input type="time" id="start_time" name="start_time" required>
                        </div>
                        <div class="form-group">
                            <label for="end_time">Ora Fine *</label>
                            <input type="time" id="end_time" name="end_time" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="num_people">Numero Partecipanti *</label>
                            <input type="number" id="num_people" name="num_people" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="event_type">Tipo Evento *</label>
                            <select id="event_type" name="event_type" required>
                                <option value="">Seleziona tipo</option>
                                <option value="meeting">Riunione</option>
                                <option value="workshop">Workshop/Corso</option>
                                <option value="conference">Conferenza</option>
                                <option value="cultural">Evento Culturale</option>
                                <option value="social">Evento Sociale</option>
                                <option value="other">Altro</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="event_description">Descrizione Evento *</label>
                        <textarea id="event_description" name="event_description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="external_space" name="external_space" value="1">
                            Richiesta utilizzo spazi esterni
                        </label>
                    </div>
                </div>

                <!-- Attrezzature -->
                <div class="form-section">
                    <h2>🎥 Attrezzature Aggiuntive</h2>
                    <div class="equipment-list">
                        <?php foreach ($equipment as $item): ?>
                            <label class="equipment-item">
                                <input type="checkbox" name="equipment_ids[]" value="<?php echo esc_attr($item->id); ?>"
                                       data-price="<?php echo esc_attr($item->price); ?>">
                                <span class="equipment-name"><?php echo esc_html($item->equipment_name); ?></span>
                                <span class="equipment-price">€ <?php echo number_format($item->price, 2, ',', '.'); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Note Speciali -->
                <div class="form-section">
                    <h2>📝 Note Speciali</h2>
                    <div class="form-group">
                        <textarea id="special_notes" name="special_notes" rows="4" placeholder="Inserisci eventuali note o richieste speciali..."></textarea>
                    </div>
                </div>

                <!-- Riepilogo Costi -->
                <div class="form-section">
                    <h2>💰 Riepilogo Costi</h2>
                    <div class="price-summary">
                        <div class="price-row">
                            <span>Costo Sala:</span>
                            <span id="space-price">€ 0,00</span>
                        </div>
                        <div class="price-row">
                            <span>Attrezzature:</span>
                            <span id="equipment-price">€ 0,00</span>
                        </div>
                        <div class="price-row total">
                            <span><strong>Totale:</strong></span>
                            <span id="total-price"><strong>€ 0,00</strong></span>
                        </div>
                    </div>
                    <input type="hidden" id="total_price" name="total_price" value="0">
                </div>

                <!-- Stato Prenotazione -->
                <div class="form-section">
                    <h2>✅ Stato Prenotazione</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="booking_status">Stato *</label>
                            <select id="booking_status" name="booking_status" required>
                                <option value="pending">In Attesa</option>
                                <option value="approved" selected>Approvata</option>
                                <option value="rejected">Rifiutata</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="form-actions">
                    <button type="submit" class="button button-primary button-large">
                        💾 Salva Prenotazione
                    </button>
                    <a href="<?php echo admin_url('admin.php?page=baleno-bookings'); ?>" class="button button-large">
                        Annulla
                    </a>
                </div>

                <div id="form-message" class="form-message" style="display: none;"></div>
            </form>
        </div>
        <?php
    }

    /**
     * AJAX: Create manual booking
     */
    public function create_manual_booking() {
        check_ajax_referer('baleno_admin_nonce', 'nonce');

        if (!current_user_can('manage_baleno_bookings')) {
            wp_send_json_error(array('message' => 'Permesso negato'));
            return;
        }

        // Prepare booking data
        $booking_data = array(
            'full_name' => sanitize_text_field($_POST['full_name']),
            'codice_fiscale' => strtoupper(sanitize_text_field($_POST['codice_fiscale'])),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'address' => sanitize_text_field($_POST['address']),
            'city' => sanitize_text_field($_POST['city']),
            'cap' => sanitize_text_field($_POST['cap']),
            'organization_name' => sanitize_text_field($_POST['organization_name']),
            'organization_address' => sanitize_text_field($_POST['organization_address']),
            'organization_piva' => sanitize_text_field($_POST['organization_piva']),
            'space_id' => intval($_POST['space_id']),
            'external_space' => isset($_POST['external_space']) ? 1 : 0,
            'booking_date' => sanitize_text_field($_POST['booking_date']),
            'start_time' => sanitize_text_field($_POST['start_time']),
            'end_time' => sanitize_text_field($_POST['end_time']),
            'time_slot' => sanitize_text_field($_POST['time_slot']),
            'num_people' => intval($_POST['num_people']),
            'event_type' => sanitize_text_field($_POST['event_type']),
            'event_description' => sanitize_textarea_field($_POST['event_description']),
            'special_notes' => sanitize_textarea_field($_POST['special_notes']),
            'equipment_ids' => isset($_POST['equipment_ids']) ? array_map('intval', $_POST['equipment_ids']) : array(),
            'total_price' => floatval($_POST['total_price'])
        );

        // Check for conflicts if status is approved
        $status = sanitize_text_field($_POST['booking_status']);
        if ($status === 'approved') {
            $is_available = Baleno_Booking_DB::check_availability(
                $booking_data['space_id'],
                $booking_data['booking_date'],
                $booking_data['start_time'],
                $booking_data['end_time']
            );

            if (!$is_available) {
                wp_send_json_error(array(
                    'message' => '⚠️ CONFLITTO: Esiste già una prenotazione per questa sala nello stesso orario. Scegli un altro orario o crea la prenotazione con stato "In Attesa".'
                ));
                return;
            }
        }

        // Create booking
        $result = Baleno_Booking_DB::create_booking($booking_data);

        if ($result['success']) {
            // Update status if not pending
            if ($status !== 'pending') {
                Baleno_Booking_DB::update_booking_status($result['booking_id'], $status, get_current_user_id());
            }

            // Send confirmation email
            Baleno_Booking_Email::send_booking_confirmation($result['booking_id']);

            // Send admin notification
            Baleno_Booking_Email::send_admin_notification($result['booking_id']);

            if ($status === 'approved') {
                Baleno_Booking_Email::send_approval_email($result['booking_id']);
            }

            wp_send_json_success(array(
                'message' => 'Prenotazione creata con successo!',
                'booking_code' => $result['booking_code'],
                'booking_id' => $result['booking_id']
            ));
        } else {
            wp_send_json_error(array('message' => 'Errore durante la creazione della prenotazione: ' . $result['error']));
        }
    }
}
