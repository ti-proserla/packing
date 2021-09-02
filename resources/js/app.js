require('./bootstrap');

import Vue from 'vue'

import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css'
Vue.use(Vuetify);

import App from './App.vue'
import swal from 'sweetalert';
window.moment = require('moment');

var router = require('./router.js').default;
/**
 * VUEX
 */

import Vuex from 'vuex'
Vue.use(Vuex)

window.store=new Vuex.Store({
  state: {
    peso: 0,
  },
});

// import VueExcelXlsx from "vue-excel-xlsx";
 
// Vue.use(VueExcelXlsx);


import { VTextField } from 'vuetify/lib';
import { VSelect } from 'vuetify/lib';

Vue.component('VTextField', {
  extends: VTextField,
  props: {
    outlined: {
      type: Boolean,
      default: true
    },
    dense: {
      type: Boolean,
      default: true
    },
    'hide-details': {
      type: String,
      default: 'auto'
    },
  }
})
Vue.component('VSelect', {
  extends: VSelect,
  props: {
    outlined: {
      type: Boolean,
      default: true
    },
    dense: {
      type: Boolean,
      default: true
    },
    'hide-details': {
      type: String,
      default: 'auto'
    },
  }
})

new Vue({
  el: '#app',
  vuetify: new Vuetify({
    icons: {
      iconfont: 'mdi', // default - only for display purposes
    },
    theme:{
      themes: {
        light: {
          primary: '#3472F7',
          info: '#00bbff',
          success: '#87cb16',
          error: '#fb404b',
          warning: '#ffa534'
        }
      }
    }
  }),
  router,
  store,
  render: h => h(App),
})
