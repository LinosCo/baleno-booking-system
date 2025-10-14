jQuery(document).ready(function($) {
    'use strict';

    // Approve booking
    $('.btn-approve').on('click', function() {
        var bookingId = $(this).data('booking-id');

        if (!confirm('Sei sicuro di voler approvare questa prenotazione?')) {
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).text('Approvazione...');

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
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert('Errore: ' + response.data.message);
                    $btn.prop('disabled', false).text('✓ Approva');
                }
            },
            error: function() {
                alert('Errore durante l\'approvazione della prenotazione');
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
});
