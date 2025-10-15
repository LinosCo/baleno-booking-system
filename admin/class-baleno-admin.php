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

    /**
     * Allow administrators to customize the plugin information shown in the plugins list
     */
    public function filter_plugin_details($plugins) {
        if (!is_array($plugins)) {
            return $plugins;
        }

        $plugin_file = BALENO_BOOKING_PLUGIN_BASENAME;

        if (!isset($plugins[$plugin_file])) {
            return $plugins;
        }

        $details = get_option('baleno_booking_plugin_details', array());

        if (!empty($details['name'])) {
            $plugins[$plugin_file]['Name'] = $details['name'];
        }

        if (!empty($details['description'])) {
            $plugins[$plugin_file]['Description'] = $details['description'];
        }

        if (!empty($details['version'])) {
            $plugins[$plugin_file]['Version'] = $details['version'];
        }

        if (!empty($details['author'])) {
            $plugins[$plugin_file]['AuthorName'] = $details['author'];

            if (!empty($details['author_uri'])) {
                $plugins[$plugin_file]['Author'] = sprintf(
                    '<a href="%s">%s</a>',
                    esc_url($details['author_uri']),
                    esc_html($details['author'])
                );
                $plugins[$plugin_file]['AuthorURI'] = $details['author_uri'];
            } else {
                $plugins[$plugin_file]['Author'] = esc_html($details['author']);
                $plugins[$plugin_file]['AuthorURI'] = '';
            }
        }

        if (!empty($details['plugin_uri'])) {
            $plugins[$plugin_file]['PluginURI'] = $details['plugin_uri'];
        }

        return $plugins;
    }

    /**
     * Normalize price input allowing comma as decimal separator
     */
    private function normalize_price_input($price) {
        if (is_string($price)) {
            $price = str_replace(array('€', ' '), '', $price);
            $price = str_replace(',', '.', $price);
        }

        return floatval($price);
    }

    /**
     * Redirect helper for equipment management
     */
    private function redirect_to_equipment_page($args = array()) {
        $url = add_query_arg(array_merge(array('page' => 'baleno-equipment'), $args), admin_url('admin.php'));
        wp_safe_redirect($url);
        exit;
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
            'nonce' => wp_create_nonce('baleno_admin_nonce'),
            'bookingsUrl' => admin_url('admin.php?page=baleno-bookings')
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
        $edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
        $equipment_to_edit = $edit_id ? Baleno_Booking_DB::get_equipment_by_id($edit_id) : null;
        ?>
        <div class="wrap baleno-admin-page">
            <h1>Gestione Attrezzature</h1>

            <?php if (isset($_GET['baleno_message'])): ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php echo esc_html(sanitize_text_field(wp_unslash($_GET['baleno_message']))); ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['baleno_error'])): ?>
                <div class="notice notice-error">
                    <p><?php echo esc_html(sanitize_text_field(wp_unslash($_GET['baleno_error']))); ?></p>
                </div>
            <?php endif; ?>

            <?php if ($edit_id && !$equipment_to_edit): ?>
                <div class="notice notice-error">
                    <p>L'attrezzatura selezionata non esiste.</p>
                </div>
            <?php endif; ?>

            <div class="baleno-equipment-form">
                <h2><?php echo $equipment_to_edit ? 'Modifica Attrezzatura' : 'Aggiungi Nuova Attrezzatura'; ?></h2>

                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php if ($equipment_to_edit): ?>
                        <input type="hidden" name="action" value="baleno_update_equipment">
                        <input type="hidden" name="equipment_id" value="<?php echo esc_attr($equipment_to_edit->id); ?>">
                        <?php wp_nonce_field('baleno_update_equipment'); ?>
                    <?php else: ?>
                        <input type="hidden" name="action" value="baleno_add_equipment">
                        <?php wp_nonce_field('baleno_add_equipment'); ?>
                    <?php endif; ?>

                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="equipment_name">Nome</label></th>
                            <td>
                                <input type="text" name="equipment_name" id="equipment_name" class="regular-text" required value="<?php echo esc_attr($equipment_to_edit ? $equipment_to_edit->equipment_name : ''); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="equipment_description">Descrizione</label></th>
                            <td>
                                <textarea name="description" id="equipment_description" class="large-text" rows="3"><?php echo esc_textarea($equipment_to_edit ? $equipment_to_edit->description : ''); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="equipment_price">Prezzo (€)</label></th>
                            <td>
                                <input type="text" name="price" id="equipment_price" class="regular-text" required value="<?php echo esc_attr($equipment_to_edit ? number_format($equipment_to_edit->price, 2, ',', '.') : ''); ?>">
                                <p class="description">Inserisci il costo aggiuntivo da applicare alla prenotazione.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Stato</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="is_active" value="1" <?php checked(!$equipment_to_edit || $equipment_to_edit->is_active); ?>>
                                    Attiva
                                </label>
                            </td>
                        </tr>
                    </table>

                    <p class="submit">
                        <button type="submit" class="button button-primary">
                            <?php echo $equipment_to_edit ? 'Aggiorna Attrezzatura' : 'Aggiungi Attrezzatura'; ?>
                        </button>
                        <?php if ($equipment_to_edit): ?>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=baleno-equipment')); ?>" class="button">Annulla</a>
                        <?php endif; ?>
                    </p>
                </form>
            </div>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Nome Attrezzatura</th>
                        <th>Descrizione</th>
                        <th>Prezzo</th>
                        <th>Stato</th>
                        <th class="column-actions">Azioni</th>
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
                            <td>
                                <a href="<?php echo esc_url(add_query_arg(array('page' => 'baleno-equipment', 'edit' => $item->id), admin_url('admin.php'))); ?>" class="button">Modifica</a>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline">
                                    <input type="hidden" name="action" value="baleno_toggle_equipment">
                                    <input type="hidden" name="equipment_id" value="<?php echo esc_attr($item->id); ?>">
                                    <input type="hidden" name="status" value="<?php echo $item->is_active ? 0 : 1; ?>">
                                    <?php wp_nonce_field('baleno_toggle_equipment_' . $item->id); ?>
                                    <button type="submit" class="button">
                                        <?php echo $item->is_active ? 'Disattiva' : 'Attiva'; ?>
                                    </button>
                                </form>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline" onsubmit="return confirm('Sei sicuro di voler eliminare questa attrezzatura?');">
                                    <input type="hidden" name="action" value="baleno_delete_equipment">
                                    <input type="hidden" name="equipment_id" value="<?php echo esc_attr($item->id); ?>">
                                    <?php wp_nonce_field('baleno_delete_equipment_' . $item->id); ?>
                                    <button type="submit" class="button button-link-delete">Elimina</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * Handle adding new equipment
     */
    public function handle_add_equipment() {
        if (!current_user_can('manage_baleno_bookings')) {
            wp_die(__('Non hai i permessi per eseguire questa azione.', 'baleno-booking'));
        }

        check_admin_referer('baleno_add_equipment');

        $price = isset($_POST['price']) ? $this->normalize_price_input(wp_unslash($_POST['price'])) : 0;

        $result = Baleno_Booking_DB::create_equipment(array(
            'equipment_name' => isset($_POST['equipment_name']) ? wp_unslash($_POST['equipment_name']) : '',
            'description'    => isset($_POST['description']) ? wp_unslash($_POST['description']) : '',
            'price'          => $price,
            'is_active'      => isset($_POST['is_active']) ? 1 : 0,
        ));

        if (is_wp_error($result)) {
            $this->redirect_to_equipment_page(array('baleno_error' => $result->get_error_message()));
        }

        $this->redirect_to_equipment_page(array('baleno_message' => 'Attrezzatura aggiunta correttamente.'));
    }

    /**
     * Handle equipment update
     */
    public function handle_update_equipment() {
        if (!current_user_can('manage_baleno_bookings')) {
            wp_die(__('Non hai i permessi per eseguire questa azione.', 'baleno-booking'));
        }

        check_admin_referer('baleno_update_equipment');

        $equipment_id = isset($_POST['equipment_id']) ? intval(wp_unslash($_POST['equipment_id'])) : 0;

        if (!$equipment_id) {
            $this->redirect_to_equipment_page(array('baleno_error' => 'Attrezzatura non valida.'));
        }

        $price = isset($_POST['price']) ? $this->normalize_price_input(wp_unslash($_POST['price'])) : 0;

        $result = Baleno_Booking_DB::update_equipment($equipment_id, array(
            'equipment_name' => isset($_POST['equipment_name']) ? wp_unslash($_POST['equipment_name']) : '',
            'description'    => isset($_POST['description']) ? wp_unslash($_POST['description']) : '',
            'price'          => $price,
            'is_active'      => isset($_POST['is_active']) ? 1 : 0,
        ));

        if (is_wp_error($result)) {
            $this->redirect_to_equipment_page(array('baleno_error' => $result->get_error_message()));
        }

        $this->redirect_to_equipment_page(array('baleno_message' => 'Attrezzatura aggiornata con successo.'));
    }

    /**
     * Handle equipment deletion
     */
    public function handle_delete_equipment() {
        if (!current_user_can('manage_baleno_bookings')) {
            wp_die(__('Non hai i permessi per eseguire questa azione.', 'baleno-booking'));
        }

        $equipment_id = isset($_POST['equipment_id']) ? intval(wp_unslash($_POST['equipment_id'])) : 0;
        check_admin_referer('baleno_delete_equipment_' . $equipment_id);

        if (!$equipment_id) {
            $this->redirect_to_equipment_page(array('baleno_error' => 'Attrezzatura non valida.'));
        }

        $result = Baleno_Booking_DB::delete_equipment($equipment_id);

        if (is_wp_error($result)) {
            $this->redirect_to_equipment_page(array('baleno_error' => $result->get_error_message()));
        }

        $this->redirect_to_equipment_page(array('baleno_message' => 'Attrezzatura eliminata.'));
    }

    /**
     * Handle equipment activation toggle
     */
    public function handle_toggle_equipment() {
        if (!current_user_can('manage_baleno_bookings')) {
            wp_die(__('Non hai i permessi per eseguire questa azione.', 'baleno-booking'));
        }

        $equipment_id = isset($_POST['equipment_id']) ? intval(wp_unslash($_POST['equipment_id'])) : 0;
        $status = isset($_POST['status']) ? intval(wp_unslash($_POST['status'])) : 0;

        check_admin_referer('baleno_toggle_equipment_' . $equipment_id);

        if (!$equipment_id) {
            $this->redirect_to_equipment_page(array('baleno_error' => 'Attrezzatura non valida.'));
        }

        $result = Baleno_Booking_DB::set_equipment_status($equipment_id, $status);

        if (is_wp_error($result)) {
            $this->redirect_to_equipment_page(array('baleno_error' => $result->get_error_message()));
        }

        $message = $status ? 'Attrezzatura attivata.' : 'Attrezzatura disattivata.';
        $this->redirect_to_equipment_page(array('baleno_message' => $message));
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
        register_setting(
            'baleno_booking_settings',
            'baleno_booking_plugin_details',
            array('sanitize_callback' => array($this, 'sanitize_plugin_details'))
        );

        add_settings_section(
            'baleno_plugin_details',
            'Dettagli Plugin',
            array($this, 'plugin_details_section_info'),
            'baleno-settings'
        );

        add_settings_field(
            'baleno_booking_plugin_details_name',
            'Nome Plugin',
            array($this, 'plugin_details_field'),
            'baleno-settings',
            'baleno_plugin_details',
            array(
                'label_for' => 'baleno_booking_plugin_details_name',
                'key' => 'name',
                'type' => 'text',
                'description' => 'Testo mostrato come nome del plugin nella lista dei plugin.'
            )
        );

        add_settings_field(
            'baleno_booking_plugin_details_description',
            'Descrizione',
            array($this, 'plugin_details_field'),
            'baleno-settings',
            'baleno_plugin_details',
            array(
                'label_for' => 'baleno_booking_plugin_details_description',
                'key' => 'description',
                'type' => 'textarea',
                'description' => 'Descrizione mostrata nella lista dei plugin.'
            )
        );

        add_settings_field(
            'baleno_booking_plugin_details_version',
            'Versione',
            array($this, 'plugin_details_field'),
            'baleno-settings',
            'baleno_plugin_details',
            array(
                'label_for' => 'baleno_booking_plugin_details_version',
                'key' => 'version',
                'type' => 'text',
                'description' => 'Numero di versione mostrato nella lista dei plugin.'
            )
        );

        add_settings_field(
            'baleno_booking_plugin_details_author',
            'Autore',
            array($this, 'plugin_details_field'),
            'baleno-settings',
            'baleno_plugin_details',
            array(
                'label_for' => 'baleno_booking_plugin_details_author',
                'key' => 'author',
                'type' => 'text',
                'description' => 'Nome dell\'autore mostrato nella lista dei plugin.'
            )
        );

        add_settings_field(
            'baleno_booking_plugin_details_author_uri',
            'Sito Autore',
            array($this, 'plugin_details_field'),
            'baleno-settings',
            'baleno_plugin_details',
            array(
                'label_for' => 'baleno_booking_plugin_details_author_uri',
                'key' => 'author_uri',
                'type' => 'url',
                'description' => 'URL del sito dell\'autore.'
            )
        );

        add_settings_field(
            'baleno_booking_plugin_details_plugin_uri',
            'Sito Plugin',
            array($this, 'plugin_details_field'),
            'baleno-settings',
            'baleno_plugin_details',
            array(
                'label_for' => 'baleno_booking_plugin_details_plugin_uri',
                'key' => 'plugin_uri',
                'type' => 'url',
                'description' => 'URL della pagina del plugin.'
            )
        );

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

    public function plugin_details_section_info() {
        echo '<p>Personalizza le informazioni del plugin visualizzate nella schermata Plugin di WordPress.</p>';
    }

    public function plugin_details_field($args) {
        $details = get_option('baleno_booking_plugin_details', array());
        $defaults = array(
            'name' => '',
            'description' => '',
            'version' => '',
            'author' => '',
            'author_uri' => '',
            'plugin_uri' => ''
        );
        $details = wp_parse_args($details, $defaults);

        $key = isset($args['key']) ? $args['key'] : '';
        $type = isset($args['type']) ? $args['type'] : 'text';
        $id = isset($args['label_for']) ? $args['label_for'] : '';
        $description = isset($args['description']) ? $args['description'] : '';

        if ($type === 'textarea') {
            printf(
                '<textarea id="%1$s" name="baleno_booking_plugin_details[%2$s]" rows="3" class="large-text">%3$s</textarea>',
                esc_attr($id),
                esc_attr($key),
                esc_textarea($details[$key])
            );
        } else {
            printf(
                '<input type="%4$s" id="%1$s" name="baleno_booking_plugin_details[%2$s]" value="%3$s" class="regular-text">',
                esc_attr($id),
                esc_attr($key),
                esc_attr($details[$key]),
                esc_attr($type)
            );
        }

        if (!empty($description)) {
            printf('<p class="description">%s</p>', esc_html($description));
        }
    }

    public function sanitize_plugin_details($input) {
        $output = array();

        if (isset($input['name'])) {
            $output['name'] = sanitize_text_field($input['name']);
        }

        if (isset($input['description'])) {
            $output['description'] = sanitize_textarea_field($input['description']);
        }

        if (isset($input['version'])) {
            $output['version'] = sanitize_text_field($input['version']);
        }

        if (isset($input['author'])) {
            $output['author'] = sanitize_text_field($input['author']);
        }

        if (isset($input['author_uri'])) {
            $output['author_uri'] = esc_url_raw($input['author_uri']);
        }

        if (isset($input['plugin_uri'])) {
            $output['plugin_uri'] = esc_url_raw($input['plugin_uri']);
        }

        return $output;
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
     * Display edit booking page
     */
    public function display_edit_booking_page() {
        // Get booking ID from URL
        $booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if (!$booking_id) {
            echo '<div class="wrap"><h1>Errore</h1><p>ID prenotazione non valido.</p></div>';
            return;
        }

        // Get booking data
        $booking = Baleno_Booking_DB::get_booking($booking_id);

        if (!$booking) {
            echo '<div class="wrap"><h1>Errore</h1><p>Prenotazione non trovata.</p></div>';
            return;
        }

        // Only allow editing pending bookings
        if ($booking->booking_status !== 'pending') {
            echo '<div class="wrap"><h1>Errore</h1><p>Solo le prenotazioni in attesa possono essere modificate.</p></div>';
            return;
        }

        // Get spaces and equipment
        $spaces = Baleno_Booking_DB::get_spaces();
        $equipment = Baleno_Booking_DB::get_equipment();

        // Get current booking equipment (decode JSON)
        $current_equipment_ids = !empty($booking->equipment_ids) ? json_decode($booking->equipment_ids, true) : array();
        ?>
        <div class="wrap baleno-admin-page">
            <h1>Modifica Prenotazione <?php echo esc_html($booking->booking_code); ?></h1>
            <p class="description">Modifica i dettagli della prenotazione. Le prenotazioni in attesa possono essere modificate anche in caso di conflitti.</p>

            <?php
            // Check for conflicts
            $has_conflict = !Baleno_Booking_DB::check_availability(
                $booking->space_id,
                $booking->booking_date,
                $booking->start_time,
                $booking->end_time,
                $booking->id
            );

            if ($has_conflict): ?>
                <div class="notice notice-warning">
                    <p><strong>⚠️ ATTENZIONE:</strong> Questa prenotazione è in conflitto con altre prenotazioni. Puoi comunque modificarla, ma non potrà essere approvata finché non risolvi il conflitto.</p>
                </div>
            <?php endif; ?>

            <form id="edit-booking-form" class="baleno-manual-booking-form" method="post" action="" onsubmit="return false;">
                <?php wp_nonce_field('baleno_admin_nonce', 'nonce'); ?>
                <input type="hidden" name="booking_id" value="<?php echo esc_attr($booking_id); ?>">

                <!-- Dati Personali -->
                <div class="form-section">
                    <h2>📋 Dati Personali</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name">Nome Completo *</label>
                            <input type="text" id="full_name" name="full_name" value="<?php echo esc_attr($booking->full_name); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="codice_fiscale">Codice Fiscale *</label>
                            <input type="text" id="codice_fiscale" name="codice_fiscale" maxlength="16" value="<?php echo esc_attr($booking->codice_fiscale); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="<?php echo esc_attr($booking->email); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Telefono *</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo esc_attr($booking->phone); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="address">Indirizzo *</label>
                            <input type="text" id="address" name="address" value="<?php echo esc_attr($booking->address); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="city">Città *</label>
                            <input type="text" id="city" name="city" value="<?php echo esc_attr($booking->city); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="cap">CAP *</label>
                            <input type="text" id="cap" name="cap" maxlength="5" value="<?php echo esc_attr($booking->cap); ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Dati Organizzazione (Opzionale) -->
                <div class="form-section">
                    <h2>🏢 Dati Organizzazione (Opzionale)</h2>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="organization_name">Nome Organizzazione</label>
                            <input type="text" id="organization_name" name="organization_name" value="<?php echo esc_attr($booking->organization_name); ?>">
                        </div>
                        <div class="form-group">
                            <label for="organization_piva">P.IVA</label>
                            <input type="text" id="organization_piva" name="organization_piva" maxlength="11" value="<?php echo esc_attr($booking->organization_piva); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="organization_address">Indirizzo Organizzazione</label>
                        <input type="text" id="organization_address" name="organization_address" value="<?php echo esc_attr($booking->organization_address); ?>">
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
                                            data-price-full="<?php echo esc_attr($space->price_full_day); ?>"
                                            <?php selected($booking->space_id, $space->id); ?>>
                                        <?php echo esc_html($space->space_code . ' - ' . $space->space_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="booking_date">Data *</label>
                            <input type="date" id="booking_date" name="booking_date" value="<?php echo esc_attr($booking->booking_date); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="time_slot">Fascia Oraria *</label>
                            <select id="time_slot" name="time_slot" required>
                                <option value="">Seleziona fascia oraria</option>
                                <option value="1h" <?php selected($booking->time_slot, '1h'); ?>>1 ora</option>
                                <option value="2h" <?php selected($booking->time_slot, '2h'); ?>>2 ore</option>
                                <option value="half_day" <?php selected($booking->time_slot, 'half_day'); ?>>Mezza giornata (4 ore)</option>
                                <option value="full_day" <?php selected($booking->time_slot, 'full_day'); ?>>Giornata intera (8 ore)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="start_time">Ora Inizio *</label>
                            <input type="time" id="start_time" name="start_time" value="<?php echo esc_attr($booking->start_time); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="end_time">Ora Fine *</label>
                            <input type="time" id="end_time" name="end_time" value="<?php echo esc_attr($booking->end_time); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="num_people">Numero Partecipanti *</label>
                            <input type="number" id="num_people" name="num_people" min="1" value="<?php echo esc_attr($booking->num_people); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="event_type">Tipo Evento *</label>
                            <select id="event_type" name="event_type" required>
                                <option value="">Seleziona tipo</option>
                                <option value="meeting" <?php selected($booking->event_type, 'meeting'); ?>>Riunione</option>
                                <option value="workshop" <?php selected($booking->event_type, 'workshop'); ?>>Workshop/Corso</option>
                                <option value="conference" <?php selected($booking->event_type, 'conference'); ?>>Conferenza</option>
                                <option value="cultural" <?php selected($booking->event_type, 'cultural'); ?>>Evento Culturale</option>
                                <option value="social" <?php selected($booking->event_type, 'social'); ?>>Evento Sociale</option>
                                <option value="other" <?php selected($booking->event_type, 'other'); ?>>Altro</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="event_description">Descrizione Evento *</label>
                        <textarea id="event_description" name="event_description" rows="3" required><?php echo esc_textarea($booking->event_description); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="external_space" name="external_space" value="1" <?php checked($booking->external_space, 1); ?>>
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
                                       data-price="<?php echo esc_attr($item->price); ?>"
                                       <?php checked(in_array($item->id, $current_equipment_ids)); ?>>
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
                        <textarea id="special_notes" name="special_notes" rows="4" placeholder="Inserisci eventuali note o richieste speciali..."><?php echo esc_textarea($booking->special_notes); ?></textarea>
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
                    <input type="hidden" id="total_price" name="total_price" value="<?php echo esc_attr($booking->total_price); ?>">
                </div>

                <!-- Submit -->
                <div class="form-actions">
                    <button type="submit" class="button button-primary button-large">
                        💾 Aggiorna Prenotazione
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

    /**
     * AJAX: Update booking
     */
    public function update_booking() {
        check_ajax_referer('baleno_admin_nonce', 'nonce');

        if (!current_user_can('manage_baleno_bookings')) {
            wp_send_json_error(array('message' => 'Permesso negato'));
            return;
        }

        $booking_id = intval($_POST['booking_id']);

        // Verify booking exists and is pending
        $booking = Baleno_Booking_DB::get_booking($booking_id);
        if (!$booking) {
            wp_send_json_error(array('message' => 'Prenotazione non trovata'));
            return;
        }

        if ($booking->booking_status !== 'pending') {
            wp_send_json_error(array('message' => 'Solo le prenotazioni in attesa possono essere modificate'));
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

        // Update booking (no conflict check needed for pending bookings)
        $result = Baleno_Booking_DB::update_booking($booking_id, $booking_data);

        if ($result['success']) {
            wp_send_json_success(array(
                'message' => 'Prenotazione aggiornata con successo!',
                'booking_id' => $booking_id
            ));
        } else {
            wp_send_json_error(array('message' => 'Errore durante l\'aggiornamento della prenotazione: ' . $result['error']));
        }
    }
}
