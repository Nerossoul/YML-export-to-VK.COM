import Vue from 'vue'
import Router from 'vue-router'
import Home from '@/components/home'
import Settings from '@/components/settings'
import Process from '@/components/process'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '',
      name: 'Home',
      component: Home
    },
    {
      path: '/settings',
      name: 'Settings',
      component: Settings
    },
    {
      path: '/process',
      name: 'Process',
      component: Process
    }
  ],
  mode: 'history'
})
