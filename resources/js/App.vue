<template>
  <div id="wrapper">
    <div class="sidebar">
      <div class="sidebar-content">
        <hr>
        <h5 class="text-center">Acopio</h5>
        <hr>
        <router-link class="nav-link" to="/lote/new">Nuevo Lote</router-link>
        <router-link class="nav-link" to="/lote/acopio">Lotes en Acopio</router-link>
        <hr>
        <h5 class="text-center">Paletizado</h5>
        <hr>
        <router-link class="nav-link" to="/paletizado/new">Nuevo Palet</router-link>
        <router-link class="nav-link" to="/paletizado">Paletizado</router-link>
      </div>
    </div>
    <div class="content">
      <div class="container-fluid">
        <router-view></router-view>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'app',
  data () {
    return {
      // msg: 'Welcome to Your Vue.js App'
    }
  }
}
</script>
