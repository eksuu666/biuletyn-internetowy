if (window.jQuery && jQuery.validator) {
    jQuery.extend(jQuery.validator.messages, {
        required: 'To pole jest wymagane.', email: 'Podaj prawidłowy adres e-mail.', url: 'Podaj prawidłowy adres URL.',
        date: 'Podaj prawidłową datę.', number: 'Podaj liczbę.', digits: 'Wpisz wyłącznie cyfry.', equalTo: 'Wartości muszą być takie same.',
        maxlength: jQuery.validator.format('Maksymalnie {0} znaków.'), minlength: jQuery.validator.format('Minimum {0} znaków.'),
        max: jQuery.validator.format('Maksymalna wartość: {0}.'), min: jQuery.validator.format('Minimalna wartość: {0}.')
    });
}
