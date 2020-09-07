import Vue from 'https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.esm.browser.js';
import router from './router/product-filter.router.js';
import API from './api/index.js';
import productFilter from './components/ProductFilter/product-filter.vue.js'

let app = Vue.component('ProductFilters', {
  template: `<div class="row py-5" v-if="initialized">
                <div class="col-12 col-lg-2 py-3">
                  <productFilter type="Categories" :items="this.categories" @clicked="handleCategoryClick" />
                  <productFilter type="Collections" :items="this.collections" @clicked="handleCollectionClick" />
                  <productFilter type="Sub-Collections" :items="this.subcollections" @clicked="handleSubCollectionClick" />
                </div>
                <router-view :products="products"></router-view>
             </div>`,

  components: {
    productFilter
  },

  data: () => ({
    categories: [],
    category:'',
    collections: [],
    collection: '',
    initialized: false,
    products: [],
    subcollections: []
  }),

  created () {
    this.getFilter('Categories');
  },

  methods: {
    getFilter (filterType, data) {
      API.get(filterType, {
        data: data,
        rollbarMessage: 'Error getting ' + filterType,
        success: data => {
          if (typeof data !== undefined) {
            this.initialized = true;
            if (typeof data.Filter !== undefined) {
              if (filterType === 'Categories') {
                this.categories = data.Filter;
              }
            }
            if (typeof data.Products !== undefined) {
              this.products = data.Products
            }
          }
        }
      }, this)
    },

    handleCategoryClick (filter) {
      this.category = filter;
      this.getFilter('Collections', {
        'categoryURL': this.category
      })
    },

    handleCollectionClick (filter) {

    },

    handleSubCollectionClick (filter) {

    }
  }
})

new Vue({
    el: '#app',
    router,
    template: '<ProductFilters />',
    components: {
      app
    }
  });
  