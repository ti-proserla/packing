require('./bootstrap');

import Vue from 'vue'

import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css'
Vue.use(Vuetify);

import App from './App.vue'
import swal from 'sweetalert';
window.moment = require('moment');

var router = require('./router.js').default;

function errorCB(tx, err) {
  console.log(err);  
}
// Transaction success callback
function successCB() {
  alert("success!");
}

window.db = window.openDatabase("DB_PACKING", "1.0", "APP_PACKING", 200000);

db.transaction((tx)=>{
  tx.executeSql('CREATE TABLE IF NOT EXISTS PALET_SALIDA(LOTE_ID, PRODUCTO_ID)');
});


if (!window.indexedDB) {
  console.log("Your browser doesn't support a stable version of IndexedDB. Such and such feature will not be available.");
}


var request= window.indexedDB.open("DB_PACKING", 1.0);

request.onerror = function(event) {
  console.log("error: ");
};

window.BD_REQUEST = null;
request.onsuccess = function(event) {
  BD_REQUEST= request.result;
  console.log("success: "+ BD_REQUEST);
};

request.onupgradeneeded = function(event) { 
  var db = event.target.result;

  // Crea un almacén de objetos (objectStore) para esta base de datos
  var objectStore = db.createObjectStore("PALET_SALIDA", { autoIncrement : true });
  // objectStore.add({});
};

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

import VueExcelXlsx from "vue-excel-xlsx";
 
Vue.use(VueExcelXlsx);


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
