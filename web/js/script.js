$(function () {
    var $sections = $('#sections');
    if ($sections.length > 0) {
        $.ajax({
            url: '/get-sections',
            type: 'POST',
            dataType: 'json',
            success: function (sectionsData) {
                var sections = JSON.parse(sectionsData);
                var html = '<ul class="section-list">' + getSectionHTML(sections, true) + '</ul>';
                $sections.html(html);
                $('.section-switch').click(function () {
                    $(this).toggleClass('expanded');
                    $(this).next('.sub-section-list').slideToggle();
                });
            }
        });
    }

    function getSectionHTML(section, isRoot) {
        if (!section) {
            return '';
        }
        var subSections = section.subSections;
        if ($.isArray(subSections) && subSections.length > 0) {
            var subSectionListHTML = subSections.reduce(function (accumulator, subSection) {
                return accumulator + getSectionHTML(subSection);
            }, '');
            return (isRoot) ? subSectionListHTML :
                '<li class="section">' +
                '<div class="section-switch section-title">' + section.name + '</div>' +
                '<ul class="sub-section-list">' + subSectionListHTML + '</ul></li>';
        } else {
            return '<li class="section section-title">' + section.name + '</li>';
        }
    }
});