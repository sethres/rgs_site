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
                <router-view :products="products" :page="page" :pages="pages" @changePage="handleChangePage"></router-view>
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
    page: 1,
    pages: 12,
    products: [],
    subcollections: [],
    subcollection: ''
  }),

  created () {
    if (typeof this.$route.query.p !== 'undefined' && !isNaN(this.$route.query.p)) {
      this.page = parseInt(this.$route.query.p);
    }
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
        (typeof collection !== 'undefined' && collection !== '' ? '&col=' + encodeURIComponent(collection) : '') + 
        '&prod=' + getProducts;
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
                  this.category = category;
                  this.collections = [];
                  this.collection = '';
                  this.subcollections = [];
                  this.subcollection = '';
                  this.collections = data.Filter;
                  break;
                case 'SubCollections':
                  this.collection = collection;
                  this.subcollections = [];
                  this.subcollection = '';
                  this.subcollections = data.Filter;
                  break;
              }
              if (typeof data.Product !== 'undefined') {
                this.products = data.Product.Results;
                this.pages = data.Product.Pages;
                this.page = 1;
              }
            }
          }
        }
      }, this)
    },

    getProducts (page, subcollection) {
      if (typeof page === 'undefined') {
        page = 1;
      }
      let url = 'Products?cat=' + encodeURIComponent(this.category) + '&col=' + encodeURIComponent(this.collection) + 
        '&sub=' + encodeURIComponent(this.subcollection) + '&p=' + page;
      API.get(url, {
        rollbarMessage: 'Error getting products',
        success: data => {
          if (typeof data !== 'undefined') {
            if (typeof data.Product !== 'undefined') {
              this.products = data.Product.Results;
              if (typeof data.Product.Pages !== 'undefined') {
                this.pages = data.Product.Pages;
              }
              this.page = page;
              if (typeof subcollection !== 'undefined') {
                this.subcollection = subcollection;
              }
            }
          }
        }
      }, this)
    },

    sameQuery (query1, query2) {
      const keys1 = Object.keys(query1);
      const keys2 = Object.keys(query2);
    
      if (keys1.length !== keys2.length) {
        return false;
      }
    
      for (let key of keys1) {
        if (query1[key] !== query2[key]) {
          return false;
        }
      }
    
      return true;
    },

    navigate (q) {
      let route = {
        name: 'ProductResults',
        query: q
      };

      if (route.name !== this.$router.currentRoute.name || !this.sameQuery(route.query, this.$router.currentRoute.query)) {
        this.$router.push(route);

        return true;
      }
    },

    handleCategoryClick (filter) {
      let q = {
        cat: filter
      };
      this.navigate(q);
      this.getFilter('Collections', filter);
    },

    handleCollectionClick (filter) {
      let q = {
        cat: this.category,
        col: filter
      };
      this.navigate(q);
      this.getFilter('SubCollections', this.category, filter);
    },

    handleSubCollectionClick (filter) {
      let q = {
        cat: this.category,
        col: this.collection,
        sub: this.subcollection
      };
      this.navigate(q);
      this.getProducts(1, filter);
    },

    handleChangePage (page) {
      let q = {};
      if (this.$route.query.cat) {
        q.cat = this.$route.query.cat;
  
        if (this.$route.query.col) {
          q.col = this.$route.query.col;
  
          if (this.$route.query.sub) {
            q.sub = this.$route.query.sub;
          }
        }
      }
      q.p = page;
      this.navigate(q);
      this.getProducts(page);
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
  