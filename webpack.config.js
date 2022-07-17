let fs = require('fs');
let path = require('path');
let _ = require('lodash');
let Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/assets/')
    .setPublicPath('/assets')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    // .enableSingleRuntimeChunk()
    .disableSingleRuntimeChunk()

    .splitEntryChunks()
    .configureSplitChunks(function (splitChunks) {
        // change the configuration
        splitChunks.minSize = 0;
    })

    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    .configureTerserPlugin((options) => {
        options.terserOptions = {
            output: {
                comments: false,
            },
        };
    })

    .enableSassLoader()
    .enableVueLoader()

    .enableIntegrityHashes(Encore.isProduction())

    .copyFiles([{
        from: './assets/images',

        // optional target path, relative to the output dir
        to: 'images/[path][name].[hash:8].[ext]',

        // only copy files matching this pattern
        //pattern: /\.(png|jpg|jpeg)$/
    }, {
        from: './assets/fonts',

        // optional target path, relative to the output dir
        to: 'fonts/[path][name].[ext]',
    }])
;

if (fs.existsSync(__dirname + '/assets/ckeditor-plugins')) {
    Encore.copyFiles({
        from: __dirname + '/assets/ckeditor-plugins',
        to: 'ckeditor-plugins/[path][name].[ext]',
    });
}

function getFiles(dir, exclude, fileType) {
    let files = fs.readdirSync(dir)
        .filter(function (file) {
            return -1 === exclude.indexOf(file) && !fs.statSync(path.join(dir, file)).isDirectory();
        });

    return _.map(files, function (filename) {
        let lastDotPosition = filename.lastIndexOf('.');
        let ext;
        if (lastDotPosition !== -1) {
            ext = filename.substr(lastDotPosition + 1);
            if (ext === fileType) {
                return filename.substr(0, lastDotPosition);
            }
        }
    });
}

let jsFiles = getFiles(__dirname + '/assets/js', [], 'js');
_.forEach(jsFiles, function (fileName) {
    Encore.addEntry('js/' + fileName, ['./assets/js/' + fileName + '.js']);
});

let scssFiles = getFiles(__dirname + '/assets/scss', [], 'scss');
_.forEach(scssFiles, function (fileName) {
    Encore.addStyleEntry('css/' + fileName, ['./assets/scss/' + fileName + '.scss']);
});

module.exports = Encore.getWebpackConfig();
