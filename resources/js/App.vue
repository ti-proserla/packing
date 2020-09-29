<template>
  <v-app>
        <v-app-bar app
                    dense
                    elevate-on-scroll
                    >
            <v-app-bar-nav-icon @click="open=true">

            </v-app-bar-nav-icon>
            <v-toolbar-title>
                Sistema Packing
            </v-toolbar-title>
        </v-app-bar>
        <v-navigation-drawer
            dark 
            app
            v-model="open"
        >
            <v-list>
                <v-list-item>
                <v-list-item-avatar>
                    <v-img src="https://cdn.vuetifyjs.com/images/john.png"></v-img>
                </v-list-item-avatar>
                </v-list-item>

                <v-list-item link>
                    <v-list-item-content>
                        <v-list-item-title class="title">Diego Mendoza</v-list-item-title>
                        <!-- <v-list-item-subtitle>john@vuetifyjs.com</v-list-item-subtitle> -->
                    </v-list-item-content>

                    <v-list-item-action>
                        <v-icon>mdi-menu-down</v-icon>
                    </v-list-item-action>
                </v-list-item>
            </v-list>
            <v-list
            dense
            nav
            class="py-0"
            >
            <!-- <v-list-item two-line>
                <v-list-item-content>
                    SISTEMA PACKING
                </v-list-item-content>
            </v-list-item> -->
            <v-divider></v-divider>
                <v-list-item link to="/materia">
                    <v-list-item-icon>
                        <i class="far fa-building"></i>
                    </v-list-item-icon>
                    <v-list-item-content>
                        Materia
                    </v-list-item-content>
                </v-list-item>
                <v-list-item link to="/variedad">
                    <v-list-item-icon>
                        <i class="far fa-building"></i>
                    </v-list-item-icon>
                    <v-list-item-content>
                        Variedad
                    </v-list-item-content>
                </v-list-item>
                <v-list-item link to="/producto">
                    <v-list-item-icon>
                        <i class="far fa-building"></i>
                    </v-list-item-icon>
                    <v-list-item-content>
                        Producto
                    </v-list-item-content>
                </v-list-item>
                <v-list-item link to="/cliente">
                    <v-list-item-icon>
                        <i class="far fa-building"></i>
                    </v-list-item-icon>
                    <v-list-item-content>
                        Cliente
                    </v-list-item-content>
                </v-list-item>
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
                <v-list-item link to="/reporte/lote">
                    <v-list-item-icon>
                        <i class="far fa-building"></i>
                    </v-list-item-icon>
                    <v-list-item-content>
                        Reporte por Lote
                    </v-list-item-content>
                </v-list-item>
                <v-list-item-content>
                    <!-- <v-btn color="error" text small @click="cerrar">Cerrar Sistema</v-btn> -->
                </v-list-item-content>
            </v-list>
        </v-navigation-drawer>
        
        <v-main>
            <!-- <notifications group="foo" position="top center"/> -->
            <router-view class="pa-6"></router-view>
        </v-main>
  </v-app>
</template>
<style>
    .v-application.theme--light{
        background-color: rgba(203,203,210,.15)!important;
    }
</style>
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
