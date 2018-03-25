define(['jquery.min'], function () {
    function Sections(sectionsContainerSelector) {
        this.html = '';
        this.$sectionsContainer = $(sectionsContainerSelector);
    }

    Sections.prototype.getSections = function () {
        var self = this;
        this.showPreloader();

        $.ajax({
            url: '/get-sections',
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                var sectionsArray = JSON.parse(data);
                self.setSectionsHTML(sectionsArray);
                self.$sectionsContainer.html(self.getSectionsHTML());

                $('.section-switch').click(function () {
                    $(this).toggleClass('expanded');
                    $(this).next('.sub-section-list').stop().slideToggle();
                });
            },
            error: function () {
                var html = '<div class="error-block">' +
                    '<span class="error-text">Ошибка при загрузке разделов</span>' +
                    '<button class="get-sections">Повторить</button>' +
                    '</div>';
                self.$sectionsContainer.html(html);

                $('.get-sections').click(function () {
                    self.getSections();
                });
            }
        });
    };

    Sections.prototype.getSectionsHTML = function () {
        return this.html ? this.html : '';
    };

    Sections.prototype.setSectionsHTML = function (sectionsArray) {
        this.html = '<ul class="section-list">' + this.getSectionHTML(sectionsArray, true) + '</ul>';
    };

    Sections.prototype.getSectionHTML = function (section, isRoot) {
        if (isRoot) {
            return this.getSubSectionsHTML(section.subSections);
        }

        if ($.isArray(section.subSections) && section.subSections.length > 0) {
            return '<li class="section">' +
                '<div class="section-switch section-title">' + section.name + '</div>' +
                '<ul class="sub-section-list">' + this.getSubSectionsHTML(section.subSections) + '</ul>' +
                '</li>';
        }

        return '<li class="section section-title">' + section.name + '</li>';
    };

    Sections.prototype.getSubSectionsHTML = function (subSections) {
        var self = this;
        return subSections.reduce(function (accumulator, subSection) {
            return accumulator + self.getSectionHTML(subSection);
        }, '');
    };

    Sections.prototype.showPreloader = function () {
        var preloaderHTML = '<div class="spinner">' +
            '<div class="bounce1"></div>' +
            '<div class="bounce2"></div>' +
            '<div class="bounce3"></div>' +
            '</div>';
        this.$sectionsContainer.html(preloaderHTML);
    };

    return Sections;
});