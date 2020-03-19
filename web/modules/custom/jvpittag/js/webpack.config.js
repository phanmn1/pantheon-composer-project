const dotenv = require('dotenv-webpack')
const webpack = require('webpack')
module.exports = () =>{ 

   return {
    entry: ['babel-polyfill','whatwg-fetch', './App.js'],
    output: {
      path: __dirname,
      filename: 'app.bundle.js'
    },
    module: {
      loaders: [
        {test: /\.js$/, exclude: /node_modules/, loader: 'babel-loader'},
        { test: /\.css$/, loader: "style-loader!css-loader" }
      ]
    }, 
    plugins: [
      new dotenv()
    ]
  }
}