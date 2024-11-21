const mix = require('laravel-mix');
require('dotenv').config();

mix.js('resources/js/app.js', 'public/js')
    .vue()
    .webpackConfig({
        plugins: [
            new webpack.DefinePlugin({
                'process.env': {
                    PUSHER_APP_KEY: JSON.stringify(process.env.PUSHER_APP_KEY),
                    PUSHER_APP_CLUSTER: JSON.stringify(process.env.PUSHER_APP_CLUSTER),
                }
            })
        ]
    });
