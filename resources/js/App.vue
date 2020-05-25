<template>
  <div id="wrapper">
    <div class="sidebar">
      <div class="sidebar-content">
        <router-link class="nav-link" to="/lote/new">Nuevo Lote</router-link>
        <router-link class="nav-link" to="/">Home</router-link>
        <router-link class="nav-link" to="acopio">Acopio</router-link>
        <router-link class="nav-link" to="/paletizado/new">+</router-link>
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
