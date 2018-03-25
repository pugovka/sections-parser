define(function (require) {
    $(function() {
        var $sectionsContainer = $('#sections');
        var url = '/get-sections';
        renderSectionsFromUrl($sectionsContainer, url);
    });

    function renderSectionsFromUrl($sectionsContainer, url) {
        var SectionsRenderer = require('sections-renderer');
        var preloaderHTML = '<div class="spinner">' +
            '<div class="bounce1"></div>' +
            '<div class="bounce2"></div>' +
            '<div class="bounce3"></div>' +
            '</div>';
        $sectionsContainer.html(preloaderHTML);
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                new SectionsRenderer($sectionsContainer, JSON.parse(data));
            },
            error: function () {
                var html = '<div class="error-block">' +
                    '<span class="error-text">Ошибка при загрузке разделов</span>' +
                    '<button class="get-sections">Повторить</button>' +
                    '</div>';
                $sectionsContainer.html(html);

                $('.get-sections').click(function () {
                    renderSectionsFromUrl($sectionsContainer, url, sections);
                });
            }
        });
    }
});
