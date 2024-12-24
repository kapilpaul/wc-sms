const path = require('path');
const ReactRefreshWebpackPlugin = require('@pmmmwh/react-refresh-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const isDevelopment = process.env.NODE_ENV !== 'production';

const config = {
	mode: isDevelopment ? 'development' : 'production',
	entry: {
		'admin-app': './assets/src/admin/index.js',
	},
	output: {
		path: path.resolve(__dirname, './assets/js/'),
		filename: '[name].js',
	},
	devServer: {
		hot: true, // Enable HMR
		client: { overlay: false },
		static: path.join(__dirname, 'dist'),
		headers: {
			'Access-Control-Allow-Origin': '*',
			'Access-Control-Allow-Methods':
				'GET, POST, PUT, DELETE, PATCH, OPTIONS',
			'Access-Control-Allow-Headers':
				'X-Requested-With, content-type, Authorization',
		},
		// Enable WebSocket connection for HMR
		client: {
			webSocketURL: {
				hostname: 'localhost',
				port: 8080, // Default port, adjust as necessary
			},
		},
	},
	externals: {
		'@wordpress/api-fetch': ['wp', 'apiFetch'],
	},
	module: {
		rules: [
			{
				test: /\.(js|jsx)$/,
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader',
					options: {
						plugins: [
							isDevelopment &&
								require.resolve('react-refresh/babel'),
						].filter(Boolean),
					},
				},
			},
			{
				test: /\.[jt]sx?$/,
				exclude: /node_modules/,
				use: [
					{
						loader: require.resolve('babel-loader'),
						options: {
							plugins: [
								isDevelopment &&
									require.resolve('react-refresh/babel'),
							].filter(Boolean),
						},
					},
				],
			},
			{
				test: /\.css$/,
				use: [
					MiniCssExtractPlugin.loader,
					'css-loader',
					'postcss-loader',
				],
			},
			{
				test: /\.scss$/i,
				use: [
					MiniCssExtractPlugin.loader,
					'css-loader',
					'postcss-loader',
					{
						loader: 'sass-loader',
						options: {
							api: 'modern-compiler',
							implementation: require('sass'),
						},
					},
				],
			},
			{
				test: /\.svg$/,
				use: 'file-loader',
			},
			{
				test: /\.png$/,
				use: [
					{
						loader: 'url-loader',
						options: {
							mimetype: 'image/png',
						},
					},
				],
			},
			{
				test: /\.gif$/,
				use: [
					{
						loader: 'url-loader',
						options: {
							mimetype: 'image/gif',
						},
					},
				],
			},
		],
	},
	plugins: [isDevelopment && new ReactRefreshWebpackPlugin()].filter(Boolean),
	resolve: {
		extensions: ['.js', '.jsx'],
		fallback: {
			path: require.resolve('path-browserify'),
		},
	},
	optimization: {
		runtimeChunk: 'single',
		splitChunks: {
			cacheGroups: {
				vendor: {
					test: /[\\/]node_modules[\\/]/,
					name: 'vendors',
					chunks: 'all',
				},
			},
		},
	},
};

module.exports = (env, argv) => {
	let cssFileName = '../css/[name].css';

	if (argv.hot) {
		// Cannot use 'contenthash' when hot reloading is enabled.
		config.output.filename = '[name].js';
		cssFileName = '[name].css';
	}

	config.plugins = [
		...config.plugins,
		new MiniCssExtractPlugin({
			filename: cssFileName,
		}),
	];

	return config;
};
