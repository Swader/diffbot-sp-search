$(document).ready(function () {

    $('form.submit-once').submit(function(e){
        if( $(this).hasClass('form-submitted') ){
            e.preventDefault();
            return;
        }
        $(this).addClass('form-submitted');
        $('#submit').addClass('disabled');
    });

    $('.date').daterangepicker({
        format: 'DD.MM.YYYY',
        singleDatePicker: true,
        maxDate: moment(),
        showDropdowns: true
    });

    //var dsg = $('.detailed-search-group');
    //var ms = $('.main-search');
    //
    //if (localStorage.getItem('detailed-on') == "true") {
    //    dsg.show();
    //    ms.hide();
    //} else {
    //    dsg.hide();
    //    ms.show();
    //}
    //
    //$(".detailed-search").click(function (e) {
    //    ms.toggle();
    //    dsg.toggle();
    //    localStorage.setItem('detailed-on', dsg.is(':visible'));
    //});

    $('a.hamburger-details').click(function(e) {
        $('.HolyGrail-nav').toggle();
        $('#q').prop('disabled', function(i, v) { return !v; });
    });

});