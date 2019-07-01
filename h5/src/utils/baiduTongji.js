import config from '../../config'

(function () {
  var hm = document.createElement('script')
  hm.src = 'https://hm.baidu.com/hm.js?' + config.baiduTongjiParam
  var s = document.getElementsByTagName('script')[0]
  s.parentNode.insertBefore(hm, s)
})()
