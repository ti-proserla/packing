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

// var socket = io.connect('http://localhost:3000', { 'forceNew': true });
  
// socket.on('balanza:data', function (dataSerial) {
//   store.state.peso=Number(dataSerial.value);
// });

new Vue({
  el: '#app',
  vuetify: new Vuetify({
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
