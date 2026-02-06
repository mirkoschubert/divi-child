import path from 'path'
import { fileURLToPath } from 'url'
import webpack from 'webpack'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

export default (env, argv) => {
  const isDevelopment = argv.mode === 'development'

  return {
    entry: './admin-app/src/index.tsx',
    output: {
      path: path.resolve(__dirname, 'admin-app/build'),
      filename: 'admin-app.js',
      publicPath: '/wp-content/themes/divi-child/admin-app/build/',
      clean: true,
    },

    plugins: [
      new webpack.DefinePlugin({
        'process.env.NODE_ENV': JSON.stringify(argv.mode || 'development'),
      }),
    ],

    externals: {
      '@wordpress/element': 'wp.element',
      '@wordpress/components': 'wp.components',
      '@wordpress/api-fetch': 'wp.apiFetch',
      '@wordpress/i18n': 'wp.i18n',
      react: 'React',
      'react-dom': 'ReactDOM',
    },

    module: {
      rules: [
        {
          test: /\.tsx?$/,
          exclude: /node_modules/,
          use: [
            {
              loader: 'babel-loader',
              options: {
                presets: [
                  '@babel/preset-env',
                  ['@babel/preset-react', { runtime: 'automatic' }],
                  '@babel/preset-typescript',
                ],
              },
            },
          ],
        },
        {
          test: /\.css$/,
          use: ['style-loader', 'css-loader'],
        },
        {
          test: /\.styl$/,
          use: [
            'style-loader',
            'css-loader',
            {
              loader: 'stylus-loader',
              options: {
                stylusOptions: {
                  compress: !isDevelopment,
                  'include css': true,
                },
              },
            },
          ],
        },
      ],
    },

    resolve: {
      extensions: ['.tsx', '.ts', '.js', '.jsx', '.styl', '.css'],
      alias: {
        '@': path.resolve(__dirname, 'admin-app/src'),
        '@/components': path.resolve(__dirname, 'admin-app/src/components'),
        '@/hooks': path.resolve(__dirname, 'admin-app/src/hooks'),
        '@/utils': path.resolve(__dirname, 'admin-app/src/utils'),
        '@/types': path.resolve(__dirname, 'admin-app/src/types'),
        '@/styles': path.resolve(__dirname, 'admin-app/src/styles'),
      },
    },

    devtool: isDevelopment ? 'source-map' : false,

    devServer: {
      static: {
        directory: path.join(__dirname, 'admin-app/build'),
      },
      port: 3000,
      hot: true,
      open: false,
    },
  }
}
