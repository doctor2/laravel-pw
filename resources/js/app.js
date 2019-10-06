/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

$(function () {
    let successMessage = $('[data-success]');
    let errorMessage = $('[data-error]');

    if (successMessage.length != 0) {
        setTimeout(() => (successMessage.fadeOut()), 7000);
    }

    if (errorMessage.length != 0) {
        setTimeout(() => (errorMessage.fadeOut()), 7000);
    }

    $('[data-int]').on('keyup', function () {
        $(this).val( $(this).val().replace(/\D/g, ""));
    });
});
