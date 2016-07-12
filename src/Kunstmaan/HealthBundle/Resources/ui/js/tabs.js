$(function () {
    var tab;

    // show first tab or if tab selected the selected.
    if ($('.activeId').length > 0) {
        tab = $('.health-tabs__item.activeId > a');
    }
    else {
        tab = $('.health-tabs__item:first-child > a');
    }
    switchTab(tab.attr('data-id'), tab.attr('data-path'));

    function switchTab(id, url) {
        $('#data_overview').addClass('dashboard__content--loading');
        $('.health-tabs__item').removeClass('active');
        $('#tab' + id).addClass('active');

        $.ajax({
            type: 'get',
            url: url,
            cache: false,
            success: function (data) {
                if (data) {
                    $('#data_content').html(data);
                    $('#data_overview').removeClass('dashboard__content--loading');
                }
            }
        });
    }

    // Tab switcher
    $('.health-tabs__item').on('click', function () {
        var id = $(this).find('.health-tabs__controller').attr('data-id');
        var url = $(this).find('.health-tabs__controller').attr('data-path');
        switchTab(id, url);
    });

});
