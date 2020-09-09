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
    subcollection: ''
  }),

  created () {
    this.getFilter('Categories');
    if (this.$route.params.categoryURL) {
      this.category = this.$route.params.categoryURL;
      this.getFilter('Collections', this.category);
    }

    if (this.$route.params.collectionURL) {
      this.collection = this.$route.params.collectionURL;
      this.getFilter('SubCollections', this.category, this.collection);
    }

    if (this.$route.params.subcollectionURL) {
      this.subcollection = this.$route.params.subcollectionURL;
      this.getProducts();
    }
  },

  methods: {
    getFilter (filterType, category, collection) {
      let url = filterType + (typeof category !== 'undefined' ? '/' + category : '') + (typeof collection !== 'undefined' ? '/' + collection : '');
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
      let url = 'Products/' + this.category + '/' + this.collection + '/' + this.subcollection;
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
      this.getFilter('Collections', this.category);
      this.$router.push({
        name: 'ProductResults',
        params: {
          categoryURL: this.category
        }
      });
    },

    handleCollectionClick (filter) {
      this.collection = filter;
      this.getFilter('SubCollections', this.category, this.collection);
      this.$router.push({
        name: 'ProductResults',
        params: {
          categoryURL: this.category,
          collectionURL: this.collection
        }
      });
    },

    handleSubCollectionClick (filter) {
      this.subcollection = filter;
      this.getProducts();
      this.$router.push({
        name: 'ProductResults',
        params: {
          categoryURL: this.category,
          collectionURL: this.collection,
          subcollectionURL: this.subcollection
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
  