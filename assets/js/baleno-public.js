jQuery(document).ready(function($) {
    'use strict';

    // Price calculation
    function updatePrice() {
        var spaceId = $('#space_id').val();
        var timeSlot = $('#time_slot').val();
        var equipmentIds = [];

        $('input[name="equipment_ids[]"]:checked').each(function() {
            equipmentIds.push($(this).val());
        });

        if (!spaceId || !timeSlot) {
            return;
        }

        $.ajax({
            url: balenoAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'baleno_get_space_price',
                nonce: balenoAjax.nonce,
                space_id: spaceId,
                time_slot: timeSlot,
                equipment_ids: equipmentIds
            },
            success: function(response) {
                if (response.success) {
                    var price = parseFloat(response.data.price);
                    var equipmentPrice = 0;

                    $('input[name="equipment_ids[]"]:checked').each(function() {
                        equipmentPrice += parseFloat($(this).data('price'));
                    });

                    var spacePrice = price - equipmentPrice;

                    $('.space-price').text('€ ' + spacePrice.toFixed(2).replace('.', ','));
                    $('.equipment-price').text('€ ' + equipmentPrice.toFixed(2).replace('.', ','));
                    $('.total-price').text('€ ' + price.toFixed(2).replace('.', ','));
                }
            }
        });
    }

    // Update capacity info
    function updateCapacityInfo() {
        var spaceId = $('#space_id').val();
        var numPeople = $('#num_people').val();

        if (!spaceId) {
            $('.capacity-info').text('');
            return;
        }

        var capacity = $('#space_id option:selected').data('capacity');

        if (numPeople && capacity) {
            if (parseInt(numPeople) > parseInt(capacity)) {
                $('.capacity-info').html('<span style="color: red;">⚠ Attenzione: il numero di persone supera la capienza massima (' + capacity + ')</span>');
            } else {
                $('.capacity-info').html('<span style="color: green;">✓ Capienza massima: ' + capacity + ' persone</span>');
            }
        }
    }

    // Check availability
    function checkAvailability() {
        var spaceId = $('#space_id').val();
        var date = $('#booking_date').val();
        var startTime = $('#start_time').val();
        var endTime = $('#end_time').val();

        if (!spaceId || !date || !startTime || !endTime) {
            return;
        }

        $.ajax({
            url: balenoAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'baleno_check_availability',
                nonce: balenoAjax.nonce,
                space_id: spaceId,
                date: date,
                start_time: startTime,
                end_time: endTime
            },
            success: function(response) {
                if (response.success) {
                    if (!response.data.available) {
                        alert('Lo spazio non è disponibile per la data e orario selezionati. Per favore scegli un altro orario.');
                        $('#booking_date').val('');
                    }
                }
            }
        });
    }

    // Validate Codice Fiscale
    function validateCodiceFiscale(cf) {
        var regex = /^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/;
        return regex.test(cf.toUpperCase());
    }

    // Event listeners
    $('#space_id, #time_slot').on('change', function() {
        updatePrice();
    });

    $('input[name="equipment_ids[]"]').on('change', function() {
        updatePrice();
    });

    $('#space_id, #num_people').on('change', function() {
        updateCapacityInfo();
    });

    $('#booking_date, #start_time, #end_time').on('change', function() {
        checkAvailability();
    });

    // Uppercase Codice Fiscale
    $('#codice_fiscale').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });

    // Calculate end time based on time slot
    $('#time_slot, #start_time').on('change', function() {
        var timeSlot = $('#time_slot').val();
        var startTime = $('#start_time').val();

        if (timeSlot && startTime) {
            var start = startTime.split(':');
            var hours = parseInt(start[0]);
            var minutes = parseInt(start[1]);

            var duration = 0;
            switch(timeSlot) {
                case '1h':
                    duration = 1;
                    break;
                case '2h':
                    duration = 2;
                    break;
                case 'half_day':
                    duration = 4;
                    break;
                case 'full_day':
                    duration = 8;
                    break;
            }

            hours += duration;
            var endTime = String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0');
            $('#end_time').val(endTime);
        }
    });

    // Form submission
    $('#baleno-booking-form').on('submit', function(e) {
        e.preventDefault();

        // Validate Codice Fiscale
        var cf = $('#codice_fiscale').val();
        if (!validateCodiceFiscale(cf)) {
            alert('Codice Fiscale non valido. Deve essere nel formato: RSSMRA80A01H501U');
            return;
        }

        // Check if terms are accepted
        if (!$('#accept_terms').is(':checked')) {
            alert('Devi accettare il regolamento per procedere.');
            return;
        }

        var $submitBtn = $('.btn-submit');
        var $message = $('.form-message');

        $submitBtn.prop('disabled', true).text('Invio in corso...');
        $message.removeClass('success error').hide();

        var formData = $(this).serializeArray();
        formData.push({name: 'action', value: 'baleno_submit_booking'});
        formData.push({name: 'nonce', value: balenoAjax.nonce});

        // Get total price
        var totalPrice = $('.total-price').text().replace('€ ', '').replace(',', '.');
        formData.push({name: 'total_price', value: totalPrice});

        $.ajax({
            url: balenoAjax.ajaxurl,
            type: 'POST',
            data: $.param(formData),
            success: function(response) {
                if (response.success) {
                    $message.addClass('success').html(
                        '<strong>✓ Successo!</strong><br>' +
                        response.data.message +
                        '<br><br>Il tuo codice prenotazione è: <strong>' + response.data.booking_code + '</strong>' +
                        '<br><br>Riceverai una email di conferma all\'indirizzo fornito.' +
                        '<br>La prenotazione sarà confermata entro 48 ore.'
                    ).show();

                    // Reset form
                    $('#baleno-booking-form')[0].reset();
                    $('.space-price, .equipment-price, .total-price').text('€ 0,00');

                    // Scroll to message
                    $('html, body').animate({
                        scrollTop: $message.offset().top - 100
                    }, 500);
                } else {
                    $message.addClass('error').html(
                        '<strong>✗ Errore!</strong><br>' +
                        response.data.message
                    ).show();
                }

                $submitBtn.prop('disabled', false).text('Invia Richiesta di Prenotazione');
            },
            error: function() {
                $message.addClass('error').html(
                    '<strong>✗ Errore!</strong><br>' +
                    'Si è verificato un errore durante l\'invio della richiesta. Riprova.'
                ).show();

                $submitBtn.prop('disabled', false).text('Invia Richiesta di Prenotazione');
            }
        });
    });

    // Book space button
    $('.btn-book-space').on('click', function(e) {
        e.preventDefault();
        var spaceId = $(this).data('space-id');

        // Scroll to form or redirect
        if ($('#baleno-booking-form').length) {
            $('#space_id').val(spaceId).trigger('change');
            $('html, body').animate({
                scrollTop: $('#baleno-booking-form').offset().top - 50
            }, 500);
        } else {
            // Redirect to booking page with space ID
            window.location.href = '?page=booking&space_id=' + spaceId;
        }
    });
});
