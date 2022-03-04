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
    defaultPrinter: {},
    
  },
  mutations: { 
    getDefaultPrinter(state){
      BrowserPrint.getDefaultDevice("printer", function(device){
        state.defaultPrinter=device;
      });
    },
  }
});

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

import VueApexCharts from 'vue-apexcharts'

Vue.use(VueApexCharts)

new Vue({
  el: '#app',
  vuetify: new Vuetify({
    icons: {
      iconfont: 'mdi', // default - only for display purposes
    },
    theme:{
      themes: {
        light: {
          primary: '#1867c0',
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
Object.defineProperty(Array.prototype, 'chunk', {
  value: function(chunkSize) {
    var R = [];
    for (var i = 0; i < this.length; i += chunkSize)
      R.push(this.slice(i, i + chunkSize));
    return R;
  }
});

console.log(
  [1, 2, 3, 4, 5, 6, 7].chunk(3)
)