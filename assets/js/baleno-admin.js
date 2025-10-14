jQuery(document).ready(function($) {
    'use strict';

    console.log('Baleno Admin JS loaded', balenoAdmin);

    // Approve booking
    $('.btn-approve').on('click', function() {
        var bookingId = $(this).data('booking-id');

        if (!confirm('Sei sicuro di voler approvare questa prenotazione?')) {
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).text('Verifica...');

        $.ajax({
            url: balenoAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'baleno_approve_booking',
                nonce: balenoAdmin.nonce,
                booking_id: bookingId
            },
            success: function(response) {
                if (response.success) {
                    alert('✅ ' + response.data.message);
                    location.reload();
                } else {
                    var errorMessage = '❌ ' + response.data.message;
                    if (response.data.conflict_details) {
                        errorMessage += '\n\n' + response.data.conflict_details;
                        errorMessage += '\n\nNon è possibile approvare questa prenotazione finché non risolvi il conflitto (rifiuta o elimina l\'altra prenotazione).';
                    }
                    alert(errorMessage);
                    $btn.prop('disabled', false).text('✓ Approva');
                }
            },
            error: function() {
                alert('❌ Errore durante l\'approvazione della prenotazione');
                $btn.prop('disabled', false).text('✓ Approva');
            }
        });
    });

    // Reject booking
    $('.btn-reject').on('click', function() {
        var bookingId = $(this).data('booking-id');
        $('#reject-booking-id').val(bookingId);
        $('#rejection-modal').fadeIn();
    });

    // Submit rejection form
    $('#rejection-form').on('submit', function(e) {
        e.preventDefault();

        var bookingId = $('#reject-booking-id').val();
        var reason = $('#rejection-reason').val();

        if (!reason.trim()) {
            alert('Inserisci un motivo per il rifiuto');
            return;
        }

        $.ajax({
            url: balenoAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'baleno_reject_booking',
                nonce: balenoAdmin.nonce,
                booking_id: bookingId,
                reason: reason
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert('Errore: ' + response.data.message);
                }
            },
            error: function() {
                alert('Errore durante il rifiuto della prenotazione');
            }
        });
    });

    // Delete booking
    $('.btn-delete').on('click', function() {
        if (!confirm('Sei sicuro di voler eliminare questa prenotazione? Questa azione non può essere annullata.')) {
            return;
        }

        var bookingId = $(this).data('booking-id');
        var $btn = $(this);
        $btn.prop('disabled', true);

        $.ajax({
            url: balenoAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'baleno_delete_booking',
                nonce: balenoAdmin.nonce,
                booking_id: bookingId
            },
            success: function(response) {
                if (response.success) {
                    $btn.closest('tr').fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    alert('Errore: ' + response.data.message);
                    $btn.prop('disabled', false);
                }
            },
            error: function() {
                alert('Errore durante l\'eliminazione della prenotazione');
                $btn.prop('disabled', false);
            }
        });
    });

    // View booking details
    $('.btn-view-details').on('click', function() {
        var bookingId = $(this).data('booking-id');
        var $row = $(this).closest('tr');

        // Build details HTML
        var detailsHtml = '<div id="booking-details-content">';

        detailsHtml += '<div class="detail-section">';
        detailsHtml += '<h3>Informazioni Cliente</h3>';
        detailsHtml += '<div class="detail-row"><span class="detail-label">Nome Completo:</span><span class="detail-value">' + $row.find('td:eq(1) strong').text() + '</span></div>';
        detailsHtml += '<div class="detail-row"><span class="detail-label">Codice Fiscale:</span><span class="detail-value">' + $row.find('td:eq(1) small').first().text().replace('CF: ', '') + '</span></div>';
        detailsHtml += '<div class="detail-row"><span class="detail-label">Email:</span><span class="detail-value">' + $row.find('td:eq(2) small').html().split('<br>')[0].replace('📧 ', '') + '</span></div>';
        detailsHtml += '<div class="detail-row"><span class="detail-label">Telefono:</span><span class="detail-value">' + $row.find('td:eq(2) small').html().split('<br>')[1].replace('📱 ', '') + '</span></div>';
        detailsHtml += '</div>';

        detailsHtml += '<div class="detail-section">';
        detailsHtml += '<h3>Dettagli Prenotazione</h3>';
        detailsHtml += '<div class="detail-row"><span class="detail-label">Codice Prenotazione:</span><span class="detail-value"><strong>' + $row.find('td:eq(0) strong').text() + '</strong></span></div>';
        detailsHtml += '<div class="detail-row"><span class="detail-label">Sala:</span><span class="detail-value">' + $row.find('td:eq(3) strong').text() + '</span></div>';
        detailsHtml += '<div class="detail-row"><span class="detail-label">Data:</span><span class="detail-value">' + $row.find('td:eq(4)').html().split('<br>')[0] + '</span></div>';
        detailsHtml += '<div class="detail-row"><span class="detail-label">Orario:</span><span class="detail-value">' + $row.find('td:eq(4) small').text() + '</span></div>';
        detailsHtml += '<div class="detail-row"><span class="detail-label">Numero Persone:</span><span class="detail-value">' + $row.find('td:eq(5)').text() + '</span></div>';
        detailsHtml += '<div class="detail-row"><span class="detail-label">Importo Totale:</span><span class="detail-value"><strong>' + $row.find('td:eq(6) strong').text() + '</strong></span></div>';
        detailsHtml += '<div class="detail-row"><span class="detail-label">Stato:</span><span class="detail-value">' + $row.find('td:eq(7)').html() + '</span></div>';
        detailsHtml += '</div>';

        detailsHtml += '<div style="text-align: center; margin-top: 20px;">';
        detailsHtml += '<button class="button button-primary" onclick="window.print()">Stampa Dettagli</button>';
        detailsHtml += '</div>';

        detailsHtml += '</div>';

        $('#booking-details-content').html(detailsHtml);
        $('#booking-details-modal').fadeIn();
    });

    // Close modal
    $('.baleno-modal-close').on('click', function() {
        $(this).closest('.baleno-modal').fadeOut();
    });

    // Close modal on outside click
    $('.baleno-modal').on('click', function(e) {
        if ($(e.target).hasClass('baleno-modal')) {
            $(this).fadeOut();
        }
    });

    // Calendar space filter
    $('#calendar-space-filter').on('change', function() {
        var spaceId = $(this).val();
        // Filter calendar events by space
        // This would integrate with a calendar library like FullCalendar
        console.log('Filter calendar by space:', spaceId);
    });

    // Simple calendar implementation (can be replaced with FullCalendar)
    if ($('#baleno-calendar').length && typeof balenoBookingsData !== 'undefined') {
        renderSimpleCalendar();
    }

    function renderSimpleCalendar() {
        var calendar = $('#baleno-calendar');
        var bookings = balenoBookingsData;

        // Group bookings by date
        var bookingsByDate = {};
        bookings.forEach(function(booking) {
            var date = booking.booking_date;
            if (!bookingsByDate[date]) {
                bookingsByDate[date] = [];
            }
            bookingsByDate[date].push(booking);
        });

        // Create calendar HTML
        var html = '<div class="baleno-simple-calendar">';
        html += '<h3>Prossime Prenotazioni</h3>';
        html += '<div class="calendar-list">';

        var sortedDates = Object.keys(bookingsByDate).sort();

        sortedDates.forEach(function(date) {
            var dateObj = new Date(date + 'T00:00:00');
            var dateStr = dateObj.toLocaleDateString('it-IT', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            html += '<div class="calendar-date-group">';
            html += '<h4>' + dateStr + '</h4>';
            html += '<div class="calendar-bookings">';

            bookingsByDate[date].forEach(function(booking) {
                var statusClass = booking.booking_status;
                var statusIcon = statusClass === 'approved' ? '✅' : (statusClass === 'pending' ? '⏳' : '❌');

                html += '<div class="calendar-booking-item status-' + statusClass + '">';
                html += '<div class="booking-time">' + booking.start_time + ' - ' + booking.end_time + '</div>';
                html += '<div class="booking-space">' + booking.space_name + '</div>';
                html += '<div class="booking-client">' + booking.full_name + '</div>';
                html += '<div class="booking-status">' + statusIcon + ' ' + booking.booking_status + '</div>';
                html += '</div>';
            });

            html += '</div></div>';
        });

        html += '</div></div>';

        calendar.html(html);

        // Add some styling
        $('<style>')
            .prop('type', 'text/css')
            .html(`
                .baleno-simple-calendar { padding: 20px; }
                .calendar-date-group { margin-bottom: 30px; }
                .calendar-date-group h4 {
                    background: #2c5aa0;
                    color: white;
                    padding: 10px 15px;
                    margin: 0 0 10px 0;
                    border-radius: 4px;
                }
                .calendar-bookings { display: grid; gap: 10px; }
                .calendar-booking-item {
                    padding: 15px;
                    border-radius: 4px;
                    border-left: 4px solid #ddd;
                    background: #f9f9f9;
                    display: grid;
                    grid-template-columns: auto 1fr auto auto;
                    gap: 15px;
                    align-items: center;
                }
                .calendar-booking-item.status-approved {
                    background: #e8f5e9;
                    border-left-color: #28a745;
                }
                .calendar-booking-item.status-pending {
                    background: #fff9e6;
                    border-left-color: #ffc107;
                }
                .calendar-booking-item.status-rejected {
                    background: #ffebee;
                    border-left-color: #dc3545;
                }
                .booking-time { font-weight: bold; color: #2c5aa0; }
                .booking-space { font-weight: 600; }
                .booking-client { color: #666; }
                .booking-status { font-size: 12px; }
            `)
            .appendTo('head');
    }

    // Export bookings (future feature)
    $('.btn-export').on('click', function() {
        // Export bookings to CSV or PDF
        console.log('Export bookings');
    });

    // Print functionality
    window.printBookingDetails = function() {
        window.print();
    };

    // ========== MANUAL BOOKING FORM ==========

    // Calculate total price
    function calculateTotalPrice() {
        var spacePrice = 0;
        var equipmentPrice = 0;

        // Get space price based on selected time slot
        var timeSlot = $('#time_slot').val();
        var selectedOption = $('#space_id option:selected');

        if (selectedOption.length && timeSlot) {
            switch(timeSlot) {
                case '1h':
                    spacePrice = parseFloat(selectedOption.data('price-1h')) || 0;
                    break;
                case '2h':
                    spacePrice = parseFloat(selectedOption.data('price-2h')) || 0;
                    break;
                case 'half_day':
                    spacePrice = parseFloat(selectedOption.data('price-half')) || 0;
                    break;
                case 'full_day':
                    spacePrice = parseFloat(selectedOption.data('price-full')) || 0;
                    break;
            }
        }

        // Calculate equipment price
        $('input[name="equipment_ids[]"]:checked').each(function() {
            equipmentPrice += parseFloat($(this).data('price')) || 0;
        });

        // Update display
        $('#space-price').text('€ ' + spacePrice.toFixed(2).replace('.', ','));
        $('#equipment-price').text('€ ' + equipmentPrice.toFixed(2).replace('.', ','));
        var total = spacePrice + equipmentPrice;
        $('#total-price').text('€ ' + total.toFixed(2).replace('.', ','));
        $('#total_price').val(total.toFixed(2));
    }

    // Trigger price calculation on change
    $('#space_id, #time_slot').on('change', calculateTotalPrice);
    $('input[name="equipment_ids[]"]').on('change', calculateTotalPrice);

    // Validate codice fiscale
    $('#codice_fiscale').on('blur', function() {
        var cf = $(this).val().toUpperCase();
        $(this).val(cf);

        if (cf.length > 0 && cf.length !== 16) {
            $(this).css('border-color', '#dc3545');
            alert('Il Codice Fiscale deve essere di 16 caratteri');
        } else {
            $(this).css('border-color', '');
        }
    });

    // Submit manual booking form
    $('#manual-booking-form').on('submit', function(e) {
        e.preventDefault();

        // Validate form
        if (!this.checkValidity()) {
            this.reportValidity();
            return;
        }

        // Check total price
        if (parseFloat($('#total_price').val()) <= 0) {
            alert('Seleziona una sala e una fascia oraria prima di procedere');
            return;
        }

        var $btn = $(this).find('button[type="submit"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('⏳ Salvataggio in corso...');

        // Prepare form data
        var formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'baleno_create_manual_booking' });
        formData.push({ name: 'nonce', value: balenoAdmin.nonce });

        $.ajax({
            url: balenoAdmin.ajaxurl,
            type: 'POST',
            data: $.param(formData),
            success: function(response) {
                if (response.success) {
                    $('#form-message')
                        .removeClass('error')
                        .addClass('success')
                        .html('✅ ' + response.data.message + '<br>Codice prenotazione: <strong>' + response.data.booking_code + '</strong>')
                        .fadeIn();

                    // Reset form
                    $('#manual-booking-form')[0].reset();
                    calculateTotalPrice();

                    // Redirect to bookings list after 2 seconds
                    setTimeout(function() {
                        window.location.href = balenoAdmin.ajaxurl.replace('admin-ajax.php', 'admin.php?page=baleno-bookings');
                    }, 2000);
                } else {
                    $('#form-message')
                        .removeClass('success')
                        .addClass('error')
                        .html('❌ ' + response.data.message)
                        .fadeIn();
                    $btn.prop('disabled', false).html(originalText);
                }
            },
            error: function() {
                $('#form-message')
                    .removeClass('success')
                    .addClass('error')
                    .html('❌ Errore durante il salvataggio della prenotazione')
                    .fadeIn();
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Auto-fill end time based on time slot
    $('#time_slot, #start_time').on('change', function() {
        var startTime = $('#start_time').val();
        var timeSlot = $('#time_slot').val();

        if (startTime && timeSlot) {
            var start = new Date('1970-01-01T' + startTime + ':00');
            var hours = 0;

            switch(timeSlot) {
                case '1h':
                    hours = 1;
                    break;
                case '2h':
                    hours = 2;
                    break;
                case 'half_day':
                    hours = 4;
                    break;
                case 'full_day':
                    hours = 8;
                    break;
            }

            if (hours > 0) {
                start.setHours(start.getHours() + hours);
                var endTime = start.toTimeString().substr(0, 5);
                $('#end_time').val(endTime);
            }
        }
    });
});
