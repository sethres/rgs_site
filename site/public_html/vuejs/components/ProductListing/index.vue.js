import Vue from 'https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.esm.browser.js';
import API from '../../api/index.js';
import productFilter from './product-filter.vue.js'
import productResults from './product-results.vue.js'

let productListing = Vue.component('ProductListing', {
  template: `<div class="row py-5" v-if="initialized">
                <div class="col-12 col-lg-2 py-3">
                  <productFilter type="Categories" :items="this.categories" @clicked="handleCategoryClick" />
                  <productFilter type="Collections" :items="this.collections" @clicked="handleCollectionClick" />
                  <productFilter type="Sub-Collections" :items="this.subcollections" @clicked="handleSubCollectionClick" />
                </div>
                <productResults :products="products" :page="page" :pages="pages" @changePage="handleChangePage" />
             </div>`,

  components: {
    productFilter,
    productResults
  },

  beforeRouteUpdate (to, from, next) {
    this.loadData(to);
    next();
  },

  data: () => ({
    categories: [],
    category:'',
    collections: [],
    collection: '',
    initialized: false,
    page: 1,
    pages: 0,
    products: [],
    subcollections: [],
    subcollection: ''
  }),

  created () {
    this.loadData(this.$route, true);
  },

  methods: {
    loadData (newRoute, initialLoad) {
      if (typeof newRoute.query.p !== 'undefined' && !isNaN(newRoute.query.p)) {
        this.page = parseInt(newRoute.query.p);
      } else {
        this.page = 1;
      }
      if (this.categories.length === 0) { //initial page load
        this.getFilter('Categories', '', '', (this.category === '' ? true : false));
      }
      if (newRoute.query.cat) { //category exists in the querystring
        if (newRoute.query.cat !== this.category) { //category changed so get collections
          this.category = newRoute.query.cat;
          this.getFilter('Collections', this.category);
        }
          if (newRoute.query.col) { //collection exists in the querystring
            if (newRoute.query.col !== this.collection) { //collection changed so get subcollections
              this.collection = newRoute.query.col;
              this.getFilter('SubCollections', this.category, this.collection);
            }
              if (newRoute.query.sub) { //subcollection exists in the querystring
                this.subcollection = newRoute.query.sub;
              } else {
                this.subcollection = '';
              }
          } else {
            this.collection = '';
            this.subcollection = '';
            this.subcollections = [];
          }
      } else {
        this.category = '';
        this.collection = '';
        this.collections = [];
        this.subcollection = '';
        this.subcollections = [];
      }
      this.getProducts(this.page, initialLoad);
    },

    getFilter (filterType, category, collection) {      
      let url = filterType + (typeof category !== 'undefined' && category !== '' ? '?cat=' + encodeURIComponent(category) : '?') + 
        (typeof collection !== 'undefined' && collection !== '' ? '&col=' + encodeURIComponent(collection) : '');
      API.get(url, {
        errorMessage: 'Error getting ' + filterType,
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
          }
        },
        error: () => {
          alert('Eror getting ' + filterType);
        }
      }, this)
    },

    getProducts (page, initialLoad) {
      if (typeof page === 'undefined') {
        page = 1;
      }
      let url = 'Products?cat=' + encodeURIComponent(this.category) + '&col=' + encodeURIComponent(this.collection) + 
        '&sub=' + encodeURIComponent(this.subcollection) + '&p=' + page + (initialLoad === true ? '&pgs=true' : '');
      API.get(url, {
        errorMessage: 'Error getting products',
        success: data => {
          if (typeof data !== 'undefined') {
            if (typeof data.Product !== 'undefined') {
              this.products = data.Product.Results;
              if (typeof data.Product.Pages !== 'undefined') {
                this.pages = data.Product.Pages;
              }
            }
          }
        },
        error: () => {
          alert('Error getting products');
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
      let query = Object.assign({}, this.$route.query);
      delete query.col;

      let route = {
        query: q
      };

      if (!this.sameQuery(route.query, this.$router.currentRoute.query)) {
        this.$router.push(route);

        return true;
      }
    },

    handleCategoryClick (filter) {
      let q = {
        cat: filter
      };
      this.navigate(q);
    },

    handleCollectionClick (filter) {
      let q = {
        cat: this.category,
        col: filter
      };
      this.navigate(q);
    },

    handleSubCollectionClick (filter) {
      let q = {
        cat: this.category,
        col: this.collection,
        sub: filter
      };
      this.navigate(q);
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
    }
  }
});

export default productListing;