<?php
/**
 * Email notifications for Baleno Booking System
 */
class Baleno_Booking_Email {

    /**
     * Send booking confirmation email to user
     */
    public static function send_booking_confirmation($booking_id) {
        $booking = Baleno_Booking_DB::get_booking($booking_id);
        if (!$booking) {
            return false;
        }

        $space = Baleno_Booking_DB::get_space($booking->space_id);

        $to = $booking->email;
        $subject = 'Richiesta di prenotazione ricevuta - Baleno Casa di Quartiere';

        $message = self::get_email_template('user_confirmation', array(
            'booking' => $booking,
            'space' => $space
        ));

        $headers = array('Content-Type: text/html; charset=UTF-8');

        return wp_mail($to, $subject, $message, $headers);
    }

    /**
     * Send booking notification to admin
     */
    public static function send_admin_notification($booking_id) {
        $booking = Baleno_Booking_DB::get_booking($booking_id);
        if (!$booking) {
            return false;
        }

        $space = Baleno_Booking_DB::get_space($booking->space_id);
        $admin_email = get_option('baleno_booking_email_admin', get_option('admin_email'));

        $to = $admin_email;
        $subject = 'Nuova richiesta di prenotazione - ' . $booking->booking_code;

        $message = self::get_email_template('admin_notification', array(
            'booking' => $booking,
            'space' => $space
        ));

        $headers = array('Content-Type: text/html; charset=UTF-8');

        return wp_mail($to, $subject, $message, $headers);
    }

    /**
     * Send booking approval email to user
     */
    public static function send_approval_email($booking_id) {
        $booking = Baleno_Booking_DB::get_booking($booking_id);
        if (!$booking) {
            return false;
        }

        $space = Baleno_Booking_DB::get_space($booking->space_id);

        $to = $booking->email;
        $subject = 'Prenotazione Approvata - ' . $booking->booking_code;

        $message = self::get_email_template('booking_approved', array(
            'booking' => $booking,
            'space' => $space
        ));

        $headers = array('Content-Type: text/html; charset=UTF-8');

        return wp_mail($to, $subject, $message, $headers);
    }

    /**
     * Send booking rejection email to user
     */
    public static function send_rejection_email($booking_id) {
        $booking = Baleno_Booking_DB::get_booking($booking_id);
        if (!$booking) {
            return false;
        }

        $space = Baleno_Booking_DB::get_space($booking->space_id);

        $to = $booking->email;
        $subject = 'Prenotazione Non Approvata - ' . $booking->booking_code;

        $message = self::get_email_template('booking_rejected', array(
            'booking' => $booking,
            'space' => $space
        ));

        $headers = array('Content-Type: text/html; charset=UTF-8');

        return wp_mail($to, $subject, $message, $headers);
    }

    /**
     * Get email template
     */
    private static function get_email_template($template_name, $data) {
        extract($data);

        ob_start();

        switch ($template_name) {
            case 'user_confirmation':
                ?>
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: #2c5aa0; color: white; padding: 20px; text-align: center; }
                        .content { background: #f9f9f9; padding: 20px; }
                        .booking-details { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #2c5aa0; }
                        .booking-details h3 { margin-top: 0; color: #2c5aa0; }
                        .detail-row { margin: 10px 0; }
                        .label { font-weight: bold; color: #555; }
                        .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
                        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>Baleno Casa di Quartiere</h1>
                            <p>San Zeno - Verona</p>
                        </div>
                        <div class="content">
                            <h2>Richiesta di Prenotazione Ricevuta</h2>
                            <p>Gentile <?php echo esc_html($booking->full_name); ?>,</p>
                            <p>Abbiamo ricevuto la tua richiesta di prenotazione. Ti confermiamo i dettagli:</p>

                            <div class="booking-details">
                                <h3>Dettagli Prenotazione</h3>
                                <div class="detail-row">
                                    <span class="label">Codice Prenotazione:</span>
                                    <strong><?php echo esc_html($booking->booking_code); ?></strong>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Sala:</span>
                                    <?php echo esc_html($space->space_name); ?>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Data:</span>
                                    <?php echo date('d/m/Y', strtotime($booking->booking_date)); ?>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Orario:</span>
                                    <?php echo esc_html($booking->start_time); ?> - <?php echo esc_html($booking->end_time); ?>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Numero Persone:</span>
                                    <?php echo esc_html($booking->num_people); ?>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Tipo Evento:</span>
                                    <?php echo esc_html($booking->event_type); ?>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Importo Totale:</span>
                                    <strong>€ <?php echo number_format($booking->total_price, 2, ',', '.'); ?></strong>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Cauzione:</span>
                                    <strong>€ 50,00</strong>
                                </div>
                            </div>

                            <div class="warning">
                                <strong>⚠ Attenzione:</strong> La tua prenotazione è in attesa di approvazione.
                                Riceverai una email di conferma o rifiuto entro 48 ore.
                            </div>

                            <h3>Cosa fare ora:</h3>
                            <ol>
                                <li>Attendi l'email di conferma dell'approvazione</li>
                                <li>Una volta approvata, procedi con il pagamento</li>
                                <li>Ritira le chiavi secondo gli orari concordati</li>
                            </ol>

                            <h3>Informazioni di Contatto</h3>
                            <p>
                                <strong>Baleno Casa di Quartiere</strong><br>
                                Via Re Pipino 3/A - San Zeno, Verona<br>
                                Email: info@balenosanzeno.it<br>
                                Orari: Lun-Ven 9:00-13:00 e 14:00-18:00
                            </p>
                        </div>
                        <div class="footer">
                            <p>Questa è una email automatica, per favore non rispondere.</p>
                            <p>&copy; <?php echo date('Y'); ?> Baleno Casa di Quartiere - San Zeno, Verona</p>
                        </div>
                    </div>
                </body>
                </html>
                <?php
                break;

            case 'admin_notification':
                ?>
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 700px; margin: 0 auto; padding: 20px; }
                        .header { background: #d9534f; color: white; padding: 20px; }
                        .content { background: #f9f9f9; padding: 20px; }
                        .booking-details { background: white; padding: 15px; margin: 15px 0; border: 1px solid #ddd; }
                        .detail-row { margin: 8px 0; }
                        .label { font-weight: bold; color: #555; min-width: 150px; display: inline-block; }
                        .action-btn { display: inline-block; padding: 10px 20px; margin: 10px 5px; text-decoration: none; color: white; border-radius: 4px; }
                        .approve { background: #5cb85c; }
                        .reject { background: #d9534f; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>🔔 Nuova Richiesta di Prenotazione</h1>
                        </div>
                        <div class="content">
                            <h2>Codice: <?php echo esc_html($booking->booking_code); ?></h2>

                            <div class="booking-details">
                                <h3>Dati Richiedente</h3>
                                <div class="detail-row">
                                    <span class="label">Nome Completo:</span>
                                    <?php echo esc_html($booking->full_name); ?>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Codice Fiscale:</span>
                                    <?php echo esc_html($booking->codice_fiscale); ?>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Email:</span>
                                    <a href="mailto:<?php echo esc_attr($booking->email); ?>"><?php echo esc_html($booking->email); ?></a>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Telefono:</span>
                                    <?php echo esc_html($booking->phone); ?>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Indirizzo:</span>
                                    <?php echo esc_html($booking->address); ?>, <?php echo esc_html($booking->city); ?> <?php echo esc_html($booking->cap); ?>
                                </div>
                                <?php if ($booking->organization_name): ?>
                                <div class="detail-row">
                                    <span class="label">Organizzazione:</span>
                                    <?php echo esc_html($booking->organization_name); ?>
                                </div>
                                <div class="detail-row">
                                    <span class="label">P.IVA:</span>
                                    <?php echo esc_html($booking->organization_piva); ?>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="booking-details">
                                <h3>Dettagli Prenotazione</h3>
                                <div class="detail-row">
                                    <span class="label">Sala:</span>
                                    <strong><?php echo esc_html($space->space_name); ?></strong>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Data:</span>
                                    <?php echo date('d/m/Y', strtotime($booking->booking_date)); ?>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Orario:</span>
                                    <?php echo esc_html($booking->start_time); ?> - <?php echo esc_html($booking->end_time); ?>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Fascia Oraria:</span>
                                    <?php echo esc_html($booking->time_slot); ?>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Numero Persone:</span>
                                    <?php echo esc_html($booking->num_people); ?>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Tipo Evento:</span>
                                    <?php echo esc_html($booking->event_type); ?>
                                </div>
                                <?php if ($booking->event_description): ?>
                                <div class="detail-row">
                                    <span class="label">Descrizione:</span><br>
                                    <?php echo nl2br(esc_html($booking->event_description)); ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($booking->special_notes): ?>
                                <div class="detail-row">
                                    <span class="label">Note Particolari:</span><br>
                                    <?php echo nl2br(esc_html($booking->special_notes)); ?>
                                </div>
                                <?php endif; ?>
                                <div class="detail-row">
                                    <span class="label">Spazio Esterno:</span>
                                    <?php echo $booking->external_space ? 'Sì' : 'No'; ?>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Importo Totale:</span>
                                    <strong style="font-size: 18px;">€ <?php echo number_format($booking->total_price, 2, ',', '.'); ?></strong>
                                </div>
                            </div>

                            <div style="text-align: center; margin: 20px 0;">
                                <a href="<?php echo admin_url('admin.php?page=baleno-bookings&booking_id=' . $booking->id); ?>"
                                   class="action-btn approve">
                                    Gestisci Prenotazione
                                </a>
                            </div>
                        </div>
                    </div>
                </body>
                </html>
                <?php
                break;

            case 'booking_approved':
                ?>
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: #5cb85c; color: white; padding: 20px; text-align: center; }
                        .content { background: #f9f9f9; padding: 20px; }
                        .success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 15px 0; }
                        .booking-details { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #5cb85c; }
                        .important { background: #fff3cd; padding: 15px; margin: 15px 0; border-left: 4px solid #ffc107; }
                        .detail-row { margin: 10px 0; }
                        .label { font-weight: bold; color: #555; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>✅ Prenotazione Approvata!</h1>
                        </div>
                        <div class="content">
                            <div class="success">
                                <strong>Ottima notizia!</strong> La tua prenotazione è stata approvata.
                            </div>

                            <p>Gentile <?php echo esc_html($booking->full_name); ?>,</p>
                            <p>Siamo lieti di comunicarti che la tua richiesta di prenotazione è stata approvata!</p>

                            <div class="booking-details">
                                <h3>Dettagli Prenotazione</h3>
                                <div class="detail-row">
                                    <span class="label">Codice:</span>
                                    <strong><?php echo esc_html($booking->booking_code); ?></strong>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Sala:</span>
                                    <?php echo esc_html($space->space_name); ?>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Data:</span>
                                    <?php echo date('d/m/Y', strtotime($booking->booking_date)); ?>
                                </div>
                                <div class="detail-row">
                                    <span class="label">Orario:</span>
                                    <?php echo esc_html($booking->start_time); ?> - <?php echo esc_html($booking->end_time); ?>
                                </div>
                            </div>

                            <div class="important">
                                <h3>📋 Prossimi Passi:</h3>
                                <ol>
                                    <li><strong>Pagamento:</strong> Importo totale € <?php echo number_format($booking->total_price, 2, ',', '.'); ?></li>
                                    <li><strong>Cauzione:</strong> € 50,00 (sarà restituita)</li>
                                    <li><strong>Ritiro Chiavi:</strong> Massimo 2 giorni prima dell'evento o il giorno stesso</li>
                                    <li><strong>Riconsegna Chiavi:</strong> Entro 2 giorni dall'utilizzo</li>
                                </ol>
                            </div>

                            <h3>Modalità di Pagamento</h3>
                            <p>Puoi effettuare il pagamento:</p>
                            <ul>
                                <li>In contanti presso la nostra sede</li>
                                <li>Tramite bonifico bancario (richiedi IBAN)</li>
                            </ul>
                            <p>Ricorda di portare la ricevuta di pagamento quando ritiri le chiavi.</p>

                            <h3>Informazioni di Contatto</h3>
                            <p>
                                <strong>Baleno Casa di Quartiere</strong><br>
                                Via Re Pipino 3/A - San Zeno, Verona<br>
                                Email: info@balenosanzeno.it<br>
                                Orari: Lun-Ven 9:00-13:00 e 14:00-18:00
                            </p>

                            <p>Ti aspettiamo!</p>
                        </div>
                    </div>
                </body>
                </html>
                <?php
                break;

            case 'booking_rejected':
                ?>
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: #d9534f; color: white; padding: 20px; text-align: center; }
                        .content { background: #f9f9f9; padding: 20px; }
                        .rejected { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 15px 0; }
                        .booking-details { background: white; padding: 15px; margin: 15px 0; }
                        .detail-row { margin: 10px 0; }
                        .label { font-weight: bold; color: #555; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h1>Prenotazione Non Approvata</h1>
                        </div>
                        <div class="content">
                            <p>Gentile <?php echo esc_html($booking->full_name); ?>,</p>

                            <div class="rejected">
                                Ci dispiace informarti che la tua richiesta di prenotazione <strong><?php echo esc_html($booking->booking_code); ?></strong>
                                non è stata approvata.
                            </div>

                            <?php if ($booking->rejection_reason): ?>
                            <div class="booking-details">
                                <h3>Motivo:</h3>
                                <p><?php echo nl2br(esc_html($booking->rejection_reason)); ?></p>
                            </div>
                            <?php endif; ?>

                            <p>Per ulteriori informazioni o per effettuare una nuova prenotazione,
                            ti invitiamo a contattarci:</p>

                            <p>
                                <strong>Baleno Casa di Quartiere</strong><br>
                                Via Re Pipino 3/A - San Zeno, Verona<br>
                                Email: info@balenosanzeno.it<br>
                                Orari: Lun-Ven 9:00-13:00 e 14:00-18:00
                            </p>
                        </div>
                    </div>
                </body>
                </html>
                <?php
                break;
        }

        return ob_get_clean();
    }
}
