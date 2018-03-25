requirejs.config({
    baseUrl: 'js/lib',
    paths: {
        app: '../app',
        'sections-renderer': '../app/sections-renderer'
    }
});

requirejs(['app/script']);
