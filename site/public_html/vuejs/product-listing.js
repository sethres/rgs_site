import Vue from 'https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.esm.browser.js';
import router from './router/product-listing.router.js';

let app = Vue.component('App', {
  template: `<router-view></router-view>`
});

new Vue({
    el: '#app',
    router,
    template: '<App />',
    components: {
      app
    }
  });
  