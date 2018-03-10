$(function () {
    $('.section-switch').click(function() {
        $(this).toggleClass('expanded');
        $(this).next('.sub-section-list').slideToggle();
    });
});