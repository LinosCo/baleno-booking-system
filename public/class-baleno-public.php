<?php
/**
 * The public-facing functionality of the plugin
 */
class Baleno_Public {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            BALENO_BOOKING_PLUGIN_URL . 'assets/css/baleno-public.css',
            array(),
            $this->version,
            'all'
        );
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            BALENO_BOOKING_PLUGIN_URL . 'assets/js/baleno-public.js',
            array('jquery'),
            $this->version,
            false
        );

        wp_localize_script($this->plugin_name, 'balenoAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('baleno_booking_nonce')
        ));
    }

    public function register_shortcodes() {
        add_shortcode('baleno_booking_form', array($this, 'booking_form_shortcode'));
        add_shortcode('baleno_spaces', array($this, 'spaces_shortcode'));
    }

    /**
     * Booking form shortcode
     */
    public function booking_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'space_id' => '',
        ), $atts);

        ob_start();
        $this->render_booking_form($atts['space_id']);
        return ob_get_clean();
    }

    /**
     * Spaces list shortcode
     */
    public function spaces_shortcode($atts) {
        $spaces = Baleno_Booking_DB::get_spaces();

        ob_start();
        ?>
        <div class="baleno-spaces-list">
            <h2>Spazi Disponibili</h2>
            <?php foreach ($spaces as $space): ?>
                <div class="baleno-space-card">
                    <h3><?php echo esc_html($space->space_name); ?></h3>
                    <p class="space-category"><?php echo esc_html($space->space_category); ?></p>
                    <p class="space-description"><?php echo esc_html($space->description); ?></p>
                    <div class="space-info">
                        <span class="capacity">👥 Capienza: <?php echo esc_html($space->capacity); ?> persone</span>
                        <span class="size">📐 Dimensioni: <?php echo esc_html($space->size_mq); ?> m²</span>
                    </div>
                    <div class="space-pricing">
                        <h4>Tariffe:</h4>
                        <ul>
                            <?php if ($space->price_1h > 0): ?>
                            <li>1 ora: € <?php echo number_format($space->price_1h, 2, ',', '.'); ?></li>
                            <?php endif; ?>
                            <?php if ($space->price_2h > 0): ?>
                            <li>2 ore: € <?php echo number_format($space->price_2h, 2, ',', '.'); ?></li>
                            <?php endif; ?>
                            <?php if ($space->price_half_day > 0): ?>
                            <li>Mezza giornata: € <?php echo number_format($space->price_half_day, 2, ',', '.'); ?></li>
                            <?php endif; ?>
                            <?php if ($space->price_full_day > 0): ?>
                            <li>Giornata intera: € <?php echo number_format($space->price_full_day, 2, ',', '.'); ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <a href="#" class="btn-book-space" data-space-id="<?php echo esc_attr($space->id); ?>">
                        Prenota Questo Spazio
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render booking form
     */
    private function render_booking_form($space_id = '') {
        $spaces = Baleno_Booking_DB::get_spaces();
        $equipment = Baleno_Booking_DB::get_equipment();
        ?>
        <div class="baleno-booking-form-container">
            <h2>Richiesta Prenotazione Spazi Baleno</h2>

            <form id="baleno-booking-form" class="baleno-form">
                <?php wp_nonce_field('baleno_booking_nonce', 'baleno_nonce'); ?>

                <div class="form-section">
                    <h3>1. Dati Personali</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name">Nome e Cognome *</label>
                            <input type="text" id="full_name" name="full_name" required>
                        </div>

                        <div class="form-group">
                            <label for="codice_fiscale">Codice Fiscale *</label>
                            <input type="text" id="codice_fiscale" name="codice_fiscale"
                                   pattern="[A-Za-z]{6}[0-9]{2}[A-Za-z][0-9]{2}[A-Za-z][0-9]{3}[A-Za-z]"
                                   maxlength="16" required
                                   placeholder="Es: RSSMRA80A01H501U">
                            <small>16 caratteri alfanumerici</small>
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
                            <input type="text" id="cap" name="cap" pattern="[0-9]{5}" maxlength="5" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>2. Dati Organizzazione (Opzionale)</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="organization_name">Nome Organizzazione/Gruppo/Azienda</label>
                            <input type="text" id="organization_name" name="organization_name">
                        </div>

                        <div class="form-group">
                            <label for="organization_piva">P.IVA</label>
                            <input type="text" id="organization_piva" name="organization_piva" maxlength="11">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="organization_address">Sede Organizzazione</label>
                        <input type="text" id="organization_address" name="organization_address">
                    </div>
                </div>

                <div class="form-section">
                    <h3>3. Dettagli Prenotazione</h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="space_id">Sala da Prenotare *</label>
                            <select id="space_id" name="space_id" required>
                                <option value="">Seleziona una sala</option>
                                <?php foreach ($spaces as $space): ?>
                                    <option value="<?php echo esc_attr($space->id); ?>"
                                            <?php selected($space_id, $space->id); ?>
                                            data-capacity="<?php echo esc_attr($space->capacity); ?>">
                                        <?php echo esc_html($space->space_name); ?> - <?php echo esc_html($space->space_category); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="external_space" name="external_space" value="1">
                                Richiesta Spazio Esterno
                            </label>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="booking_date">Data Prenotazione *</label>
                            <input type="date" id="booking_date" name="booking_date"
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                        </div>

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
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_time">Ora Inizio *</label>
                            <input type="time" id="start_time" name="start_time" required>
                            <small>Orario di apertura: 9:00 - 18:00</small>
                        </div>

                        <div class="form-group">
                            <label for="end_time">Ora Fine *</label>
                            <input type="time" id="end_time" name="end_time" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="num_people">Numero Persone *</label>
                            <input type="number" id="num_people" name="num_people" min="1" required>
                            <small class="capacity-info"></small>
                        </div>

                        <div class="form-group">
                            <label for="event_type">Tipo di Evento *</label>
                            <select id="event_type" name="event_type" required>
                                <option value="">Seleziona tipo evento</option>
                                <option value="Riunione">Riunione</option>
                                <option value="Corso/Formazione">Corso/Formazione</option>
                                <option value="Laboratorio">Laboratorio</option>
                                <option value="Evento Culturale">Evento Culturale</option>
                                <option value="Festa/Celebrazione">Festa/Celebrazione</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Attività Bambini">Attività Bambini</option>
                                <option value="Altro">Altro</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="event_description">Descrizione Evento/Attività *</label>
                        <textarea id="event_description" name="event_description" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="special_notes">Note Particolari/Richieste Speciali</label>
                        <textarea id="special_notes" name="special_notes" rows="3"></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h3>4. Attrezzature Aggiuntive</h3>
                    <p class="section-description">Seleziona le attrezzature necessarie (costi aggiuntivi)</p>

                    <div class="equipment-list">
                        <?php foreach ($equipment as $item): ?>
                            <div class="equipment-item">
                                <label>
                                    <input type="checkbox" name="equipment_ids[]"
                                           value="<?php echo esc_attr($item->id); ?>"
                                           data-price="<?php echo esc_attr($item->price); ?>">
                                    <strong><?php echo esc_html($item->equipment_name); ?></strong>
                                    - € <?php echo number_format($item->price, 2, ',', '.'); ?>
                                    <small><?php echo esc_html($item->description); ?></small>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-section price-summary">
                    <h3>5. Riepilogo Costi</h3>
                    <div class="price-breakdown">
                        <div class="price-row">
                            <span>Costo Sala:</span>
                            <span class="space-price">€ 0,00</span>
                        </div>
                        <div class="price-row">
                            <span>Attrezzature:</span>
                            <span class="equipment-price">€ 0,00</span>
                        </div>
                        <div class="price-row total">
                            <span>Totale:</span>
                            <span class="total-price">€ 0,00</span>
                        </div>
                        <div class="price-row caution">
                            <span>Cauzione (restituibile):</span>
                            <span>€ 50,00</span>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>6. Conferma e Invio</h3>

                    <div class="terms-box">
                        <label>
                            <input type="checkbox" id="accept_terms" name="accept_terms" required>
                            Dichiaro di aver letto e accettato il
                            <a href="#" target="_blank">Regolamento per l'uso delle sale</a> *
                        </label>
                    </div>

                    <div class="info-box">
                        <h4>📋 Informazioni Importanti:</h4>
                        <ul>
                            <li>La prenotazione sarà confermata solo dopo l'approvazione da parte dello staff</li>
                            <li>Riceverai una email di conferma entro 48 ore</li>
                            <li>Il pagamento dovrà essere effettuato prima del ritiro delle chiavi</li>
                            <li>Le chiavi possono essere ritirate max 2 giorni prima dell'evento</li>
                            <li>Cauzione di € 50,00 restituibile se rispetti il regolamento</li>
                        </ul>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">Invia Richiesta di Prenotazione</button>
                        <div class="form-message"></div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    /**
     * AJAX: Submit booking
     */
    public function submit_booking() {
        check_ajax_referer('baleno_booking_nonce', 'nonce');

        // Validate and sanitize all inputs
        $data = array(
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

        // Validate Codice Fiscale format
        if (!preg_match('/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/', $data['codice_fiscale'])) {
            wp_send_json_error(array('message' => 'Codice Fiscale non valido'));
            return;
        }

        // Check availability
        $available = Baleno_Booking_DB::check_availability(
            $data['space_id'],
            $data['booking_date'],
            $data['start_time'],
            $data['end_time']
        );

        if (!$available) {
            wp_send_json_error(array('message' => 'Lo spazio non è disponibile per la data e orario selezionati.'));
            return;
        }

        // Create booking
        $result = Baleno_Booking_DB::create_booking($data);

        if ($result['success']) {
            // Send emails
            Baleno_Booking_Email::send_booking_confirmation($result['booking_id']);
            Baleno_Booking_Email::send_admin_notification($result['booking_id']);

            wp_send_json_success(array(
                'message' => 'Richiesta di prenotazione inviata con successo!',
                'booking_code' => $result['booking_code']
            ));
        } else {
            wp_send_json_error(array('message' => 'Errore durante la creazione della prenotazione.'));
        }
    }

    /**
     * AJAX: Check availability
     */
    public function check_availability() {
        check_ajax_referer('baleno_booking_nonce', 'nonce');

        $space_id = intval($_POST['space_id']);
        $date = sanitize_text_field($_POST['date']);
        $start_time = sanitize_text_field($_POST['start_time']);
        $end_time = sanitize_text_field($_POST['end_time']);

        $available = Baleno_Booking_DB::check_availability($space_id, $date, $start_time, $end_time);

        wp_send_json_success(array('available' => $available));
    }

    /**
     * AJAX: Get space price
     */
    public function get_space_price() {
        check_ajax_referer('baleno_booking_nonce', 'nonce');

        $space_id = intval($_POST['space_id']);
        $time_slot = sanitize_text_field($_POST['time_slot']);
        $equipment_ids = isset($_POST['equipment_ids']) ? array_map('intval', $_POST['equipment_ids']) : array();

        $price = Baleno_Booking_DB::calculate_price($space_id, $time_slot, $equipment_ids);

        wp_send_json_success(array('price' => $price));
    }
}
