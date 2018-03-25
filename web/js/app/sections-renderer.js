define(['jquery.min'], function () {
    function SectionsRenderer($container, section) {
        $container.html(this.renderSection(section, true));

        $container.find('.section-switch').click(function () {
            $(this).toggleClass('expanded');
            $(this).next('.sub-section-list').stop().slideToggle();
        });
    }

    SectionsRenderer.prototype.renderSection = function (section, isRoot) {
        if (!section) {
            return '';
        }

        var self = this;
        if (Array.isArray(section.subSections) && section.subSections.length > 0) {
            var subSectionListHTML = section.subSections.reduce(function (accumulator, subSection) {
                return accumulator + self.renderSection(subSection);
            }, '');
            return (isRoot) ? subSectionListHTML :
                this.renderParentSection(section.name, subSectionListHTML);
        } else {
            return this.renderLeafSection(section.name);
        }
    };

    SectionsRenderer.prototype.renderParentSection = function(sectionName, subSectionListHTML) {
        return '<li class="section">' +
            '<div class="section-switch section-title">' + sectionName + '</div>' +
            '<ul class="sub-section-list">' + subSectionListHTML + '</ul>' +
            '</li>';
    };

    SectionsRenderer.prototype.renderLeafSection = function(sectionName) {
        return '<li class="section section-title">' + sectionName + '</li>';
    };

    return SectionsRenderer;
});
