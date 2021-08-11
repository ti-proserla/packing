<template>
  <v-app>
        <v-app-bar app
                    dense
                    elevate-on-scroll
                    >
            <v-app-bar-nav-icon name="btn-navbar" @click="open=true"></v-app-bar-nav-icon>
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
            <v-divider></v-divider>
            <v-list-item link to="/impresora">
                <v-list-item-icon>
                    <i class="fas fa-print"></i>
                </v-list-item-icon>
                <v-list-item-content>
                    Impresoras
                </v-list-item-content>
            </v-list-item>
            <v-list-item link to="/fundo">
                <v-list-item-icon>
                    <i class="fas fa-seedling"></i>
                </v-list-item-icon>
                <v-list-item-content>
                    Fundo
                </v-list-item-content>
            </v-list-item>
            <v-list-item link to="/materia">
                <v-list-item-icon>
                    <i class="fas fa-apple-alt"></i>
                </v-list-item-icon>
                <v-list-item-content>
                    Materia
                </v-list-item-content>
            </v-list-item>
            <v-list-item link to="/variedad">
                <v-list-item-icon>
                    <i class="fas fa-seedling"></i>
                </v-list-item-icon>
                <v-list-item-content>
                    Variedad
                </v-list-item-content>
            </v-list-item>
            <v-list-item link to="/calibre">
                <v-list-item-icon>
                    <i class="fas fa-ruler-vertical"></i>
                </v-list-item-icon>
                <v-list-item-content>
                    Calibre
                </v-list-item-content>
            </v-list-item>
            <v-list-item link to="/tipo">
                <v-list-item-icon>
                    <i class="fas fa-leaf"></i>
                </v-list-item-icon>
                <v-list-item-content>
                    Tipo
                </v-list-item-content>
            </v-list-item>
            <!-- <v-list-item link to="/producto">
                <v-list-item-icon>
                    <i class="far fa-building"></i>
                </v-list-item-icon>
                <v-list-item-content>
                    Producto
                </v-list-item-content>
            </v-list-item> -->
            <v-list-item link to="/cliente">
                <v-list-item-icon>
                    <i class="fas fa-user-alt"></i>
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
                    Acopio de Lotes
                </v-list-item-content>
            </v-list-item>
                <v-list-item link to="/acopio/sublote">
                    <v-list-item-icon>
                        <i class="far fa-building"></i>
                    </v-list-item-icon>
                    <v-list-item-content>
                        Acopio - Sub Lotes
                    </v-list-item-content>
                </v-list-item>
                <v-list-item link to="/lanzado">
                    <v-list-item-icon>
                        <i class="fas fa-eject"></i>
                    </v-list-item-icon>
                    <v-list-item-content>
                        Lanzado
                    </v-list-item-content>
                </v-list-item>
                <v-list-item link to="/paletizado">
                    <v-list-item-icon>
                        <i class="fas fa-pallet"></i>
                    </v-list-item-icon>
                    <v-list-item-content>
                        Paletizado
                    </v-list-item-content>
                </v-list-item>
                <v-list-item link to="/frio">
                    <v-list-item-icon>
                        <i class="fas fa-icicles"></i>
                    </v-list-item-icon>
                    <v-list-item-content>
                        Cámara de Frío
                    </v-list-item-content>
                </v-list-item>
                <v-list-item link to="/despacho">
                    <v-list-item-icon>
                        <i class="fas fa-icicles"></i>
                    </v-list-item-icon>
                    <v-list-item-content>
                        Despacho
                    </v-list-item-content>
                </v-list-item>
                <v-list-group
                    :value="false"
                    no-action>
                    <template v-slot:activator>
                            <v-list-item-icon>
                                <i class="far fa-file-alt"></i>
                            </v-list-item-icon>
                        <v-list-item-content>Reportes</v-list-item-content>
                    </template>
                    <v-list-item link to="/reporte/acopio">
                        <v-list-item-content>
                            Reporte Acopio
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item link to="/reporte/personal">
                        <v-list-item-content>
                            Rendimiento Personal
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item link to="/reporte/lote">
                        <v-list-item-content>
                            Reporte por Lote
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item link to="/reporte/linea">
                        <v-list-item-content>
                            Reporte por Linea
                        </v-list-item-content>
                    </v-list-item>
                </v-list-group>
                <!-- </v-list-group> -->
                <v-list-item link to="/dispensador">
                    <v-list-item-icon>
                        <i class="far fa-building"></i>
                    </v-list-item-icon>
                    <v-list-item-content>
                        Dispensador de Bar Code
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>
        
        <v-main>
            <!-- <notifications group="foo" position="top center"/> -->
            <router-view class="pa-6"></router-view>
        </v-main>
        <audio id="myAudio">
            <!-- <source src="horse.ogg" type="audio/ogg"> -->
            <source src="/mp3/error.mp3" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
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
          open: (this.$vuetify.breakpoint.name == 'lg') ? true :false
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
