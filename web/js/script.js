$(function () {
    var $sections = $('#sections');
    if ($sections.length > 0) {
        var sections = JSON.parse($sections.attr('data-sections'));
        var html = '<ul class="section-list">' + getSectionHTML(sections, true) + '</ul>';
        $sections.append(html);

        $('.section-switch').click(function () {
            $(this).toggleClass('expanded');
            $(this).next('.sub-section-list').slideToggle();
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