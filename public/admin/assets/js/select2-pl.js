$.fn.select2.amd.define('select2/i18n/pl', [], function () {
    return {
        errorLoading: function () { return 'Nie udało się wczytać wyników.'; },
        inputTooLong: function () { return 'Wpisz mniej znaków.'; },
        inputTooShort: function () { return 'Wpisz więcej znaków.'; },
        loadingMore: function () { return 'Wczytywanie kolejnych wyników…'; },
        maximumSelected: function (args) { return 'Limit wyboru: ' + args.maximum; },
        noResults: function () { return 'Brak wyników'; },
        searching: function () { return 'Wyszukiwanie…'; },
        removeAllItems: function () { return 'Wyczyść wybór'; }
    };
});
$.fn.select2.defaults.set('language', 'pl');
