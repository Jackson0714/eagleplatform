import Vue from 'vue'
import Router from 'vue-router'
import Hello from '@/pages/hello/hello'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/hello',
      name: 'hello',
      component: Hello
    }
  ]
})
