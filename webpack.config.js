const Encore = require('@symfony/webpack-encore');
const copyPlugin = require('copy-webpack-plugin');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .autoProvidejQuery()
    .autoProvideVariables({
        Popper: ['popper.js', 'default'],
    })
    .enableSassLoader()
    .enablePostCssLoader()
    .enableVersioning()
    .enableSourceMaps(!Encore.isProduction())
    .addEntry('app', [
        './assets/js/app.js',
        './assets/scss/app.scss',
    ])
    .createSharedEntry('vendors', [
        // js
        'jquery',
        'bootstrap',
        'popper.js',
        'select2',
        // styles
        './assets/scss/vendors.scss',
    ])
    .addPlugin(new copyPlugin([{
        from: './assets/images',
        to: 'images',
    }]))
;

module.exports = Encore.getWebpackConfig();
