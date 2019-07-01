// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import VueRouter from 'vue-router'
import App from './App'
import config from '../config'
import router from './router/index'
import { AjaxPlugin, ConfirmPlugin, ToastPlugin, LoadingPlugin, WechatPlugin } from 'vux'
import Vant from 'vant'
import 'vant/lib/index.css'
// import './utils/baiduTongji'

Vue.use(ToastPlugin)
Vue.use(LoadingPlugin)
Vue.use(VueRouter)
Vue.use(AjaxPlugin)
Vue.use(ConfirmPlugin)
Vue.use(ToastPlugin)
Vue.use(WechatPlugin)
Vue.use(Vant)

Vue.prototype.$http.defaults.baseURL = config.baseURL

// const signParams = {
//   url: location.href.split('#')[0]
// }

// Vue.http.get('we/jssdk-sign', { params: signParams }).then(({ data }) => {
//   const sign = data.data

//   Vue.wechat.config({
//     appId: sign.appid,
//     timestamp: sign.timestamp,
//     nonceStr: sign.noncestr,
//     signature: sign.signature,
//     jsApiList: ['scanQRCode', 'openLocation', 'closeWindow', 'onMenuShareAppMessage', 'onMenuShareTimeline', 'hideOptionMenu']
//   })
// })

Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
  router,
  render: h => h(App)
}).$mount('#app-box')

// router.beforeEach((to, from, next) => {
//   if (to.path) {
//     if (window._hmt) {
//       window._hmt.push(['_setAutoPageview', false])
//       window._hmt.push(['_trackPageview', '/#' + to.path])
//     }
//   }
//   next()
// })
