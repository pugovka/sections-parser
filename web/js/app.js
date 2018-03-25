requirejs.config({
    baseUrl: 'js/lib',
    paths: {
        app: '../app',
        Sections: '../app/sections'
    }
});

requirejs(['app/script']);