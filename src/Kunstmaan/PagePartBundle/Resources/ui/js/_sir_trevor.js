var kunstmaanPagePartBundle = kunstmaanPagePartBundle || {};

kunstmaanPagePartBundle.sirTrevor = (function(window, undefined) {

    var init,
        $submit = $('.js-sir-trevor-submit');

    init = function() {
        $(document).on('focus click', '.js-sir-trevor-form', function() {
            $submit.removeClass('hidden');
        });

        $(document).on('submit', '.js-sir-trevor-form', function(e) {
            e.preventDefault();


            var $instance = $(this).find('.js-st-instance');
            var data = {
                'value': '',
                'pagePartId': $instance.data('pagepartid'),
                'field': $instance.data('pagepartfield'),
                'nodeTranslationId': $instance.data('nodetranslation'),
                'class': $instance.data('pagepartclass'),
            };

            $.each($(this).serializeArray(), function(key, value) {
                var parsed = JSON.parse(value.value);
                $.each(parsed.data, function(key, value) {
                    data['value'] += value.data.text;
                });
            });

            $.ajax({
                type: "POST",
                url: $(this).attr('action'),
                data: data,
                success: function(data) {
                    $submit.addClass('hidden');
                }
            });
        });
    };

    return {
        init: init
    };

})(window);
