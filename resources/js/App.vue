<template>
  <v-app>
        <v-app-bar app
         absolute
         >
            <v-app-bar-nav-icon @click="open=true"></v-app-bar-nav-icon>
            <v-toolbar-title>PACKING</v-toolbar-title>
        </v-app-bar>
        <v-navigation-drawer
            absolute
            light
            color="primary"
            app
            v-model="open"
        >
            <v-list
            dense
            nav
            class="py-0"
            >
            <v-list-item two-line>
                <v-list-item-avatar>
                    <img src="http://172.16.1.112:80/storage/logotipo.png">
                </v-list-item-avatar>

                <v-list-item-content>
                    <!-- <v-list-item-title>{{ cuenta.nombre.toUpperCase()+' '+cuenta.apellido.toUpperCase() }}</v-list-item-title> -->
                </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
                <v-list-item link to="/acopio/lote">
                    <v-list-item-icon>
                        <i class="far fa-building"></i>
                    </v-list-item-icon>
                    <v-list-item-content>
                        Lotes Ingreso
                    </v-list-item-content>
                </v-list-item>
                <v-list-item link to="/paletizado">
                    <v-list-item-icon>
                        <i class="far fa-building"></i>
                    </v-list-item-icon>
                    <v-list-item-content>
                        Palets de Salida
                    </v-list-item-content>
                </v-list-item>
                <v-list-item-content>
                    <!-- <v-btn color="error" text small @click="cerrar">Cerrar Sistema</v-btn> -->
                </v-list-item-content>
            </v-list>
        </v-navigation-drawer>
        
        <v-main>
            <!-- <notifications group="foo" position="top center"/> -->
            <router-view></router-view>
        </v-main>
  </v-app>
</template>

<script>
export default {
  name: 'app',
  data() {
      return {
          open: true
      }
  },
  methods: {
      cerrar(){
          // this.$store.commit('auth_close');
          // this.$router.push({path: "/login"} );
      },
  
  }
}
</script>
