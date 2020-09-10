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
    subcollections: [],
    subcollection: '',
  }),

  created () {
    if (this.$route.query.cat) {
      this.category = this.$route.query.cat;

      if (this.$route.query.col) {
        this.collection = this.$route.query.col;

        if (this.$route.query.sub) {
          this.subcollection = this.$route.query.sub;
          this.getProducts();
        }
        this.getFilter('SubCollections', this.category, this.collection, (this.subcollection === '' ? true : false));
      }
      this.getFilter('Collections', this.category, '', (this.collection === '' ? true : false));
    }
    this.getFilter('Categories', '', '', (this.category === '' ? true : false));
  },

  methods: {
    getFilter (filterType, category, collection, getProducts) {
      if (typeof getProducts === 'undefined') {
        getProducts = true;
      }
      let url = filterType + (typeof category !== 'undefined' && category !== '' ? '?cat=' + encodeURIComponent(category) : '?') + 
        (typeof collection !== 'undefined' && collection !== '' ? '&col=' + encodeURIComponent(collection) : '') + '&prod=' + getProducts;
      API.get(url, {
        rollbarMessage: 'Error getting ' + filterType,
        success: data => {
          if (typeof data !== 'undefined') {
            this.initialized = true;
            if (typeof data.Filter !== 'undefined') {
              switch (filterType) {
                case 'Categories':
                  this.categories = data.Filter;
                  break;
                case 'Collections':
                  this.collections = data.Filter;
                  break;
                case 'SubCollections':
                  this.subcollections = data.Filter;
                  break;
              }
            }
            if (typeof data.Products !== 'undefined') {
              this.products = data.Products
            }
          }
        }
      }, this)
    },

    getProducts () {
      let url = 'Products?cat=' + encodeURIComponent(this.category) + '&col=' + encodeURIComponent(this.collection) + '&sub=' + encodeURIComponent(this.subcollection);
      API.get(url, {
        rollbarMessage: 'Error getting products',
        success: data => {
          if (typeof data !== 'undefined') {
            if (typeof data.Products !== 'undefined') {
              this.products = data.Products
            }
          }
        }
      }, this)
    },

    handleCategoryClick (filter) {
      this.category = filter;
      this.collections = [];
      this.collection = '';
      this.subcollections = [];
      this.subcollection = '';
      this.getFilter('Collections', this.category);
      this.$router.push({
        name: 'ProductResults',
        query: {
          cat: this.category
        }
      });
    },

    handleCollectionClick (filter) {
      this.collection = filter;
      this.subcollections = [];
      this.subcollection = '';
      this.getFilter('SubCollections', this.category, this.collection);
      this.$router.push({
        name: 'ProductResults',
        query: {
          cat: this.category,
          col: this.collection
        }
      });
    },

    handleSubCollectionClick (filter) {
      this.subcollection = filter;
      this.getProducts();
      this.$router.push({
        name: 'ProductResults',
        query: {
          cat: this.category,
          col: this.collection,
          sub: this.subcollection
        }
      });
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
  