const path = require('path')

const resolve = dir => {
  return path.join(__dirname, dir)
}

module.exports = {
  baseUrl: '/',
  chainWebpack: config => {
    config.resolve.alias
      .set('@', resolve('src'))
      .set('_c', resolve('src/components'))
      .set('_conf', resolve('config'))
  },
  productionSourceMap: false
}
