define(function (require) {
    $(function() {
        var Sections = require('Sections');
        var sections = new Sections('#sections');
        sections.getSections();
    });
});