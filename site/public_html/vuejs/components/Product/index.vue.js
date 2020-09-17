import Vue from 'https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.esm.browser.js';
import API from '../../api/index.js';
import carousel from './carousel.vue.js';
import options from './options.vue.js';

let product = Vue.component('Product', {
  template: `<div v-if="initialized">
                <section class="py-5 pro-info">
                <div class="container-fluid widecont">
                  <div class="row">
                    <div class="col-lg-6 pb-5 pb-lg-0">
                      <div class="row px-3">
                        <carousel :imagesProp="images" />
                      </div>
                    </div>
                    <div class="col-lg-6 mb-3">
                      <h1 style="font-weight: 700" class="mb-3">{{ product.Name }}</h1>
                      <p class="m-0" style="font-weight: 400; color: #6c757d;">SKU Variation: <b>{{ product.Prefix }}</b></p>
                      <p class="m-0" style="font-weight: 400; color: #6c757d;">Current SKU: <b>{{ product.SKU }}</b></p>
                      <p class="m-0" style="font-weight: 400; color: #6c757d;">List Price: <b>{{ '$' + product.List_Price }}</b></p>
                      <p class="m-0" style="font-weight: 400; color: #6c757d;">Current Stock: <b>UNKWN</b></p>
                      <p class="my-4" style="font-weight: 400; color: #6c757d;">{{ (product.Description !== '' ? product.Description : 'Description Currently Unavailable') }}</p>
                      <p>Color:</p>
                      <hr class="p-0 ml-0">
                      <options :options="colors" :disabled="disabledColors" :selected="color" @optionClicked="handleColorClick" />
                      <p class='pt-5' style='font-weight: 400; color: #6c757d;' v-if="configurations.length === 0">No Configurations Available</p>
                      <hr v-if="configurations.length === 0">
                      <p class="mt-5" v-if="configurations.length > 0">Configuration:</p>
                      
                      <hr class="p-0 ml-0" v-if="configurations.length > 0">
                      <options :options="configurations" :disabled="disabledConfigs" :selected="configuration" @optionClicked="handleConfigClick" />
                    </div>
                  </div>
                </div>
              </section>
              <section id="moreinfo" class="more-pro-info pb-5">
                <div class="accordion container-fluid widecont" id="accordionExample">
                  <div class="card">
                    <div class="card-header" id="headingOne">
                      <h2 class="mb-0">
                        <button class="btn btn-link shadow-none stretched-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                          Downloads
                        </button>
                      </h2>
                    </div>
                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                      <div class="card-body">
                        LOL
                      </div>
                    </div>
                  </div>
                  <div class="card">
                    <div class="card-header" id="headingTwo">
                      <h2 class="mb-0">
                        <button class="btn btn-link collapsed shadow-none stretched-link" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                          Collapsible Group Item #2
                        </button>
                      </h2>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                      <div class="card-body">
                        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                      </div>
                    </div>
                  </div>
                  <div class="card">
                    <div class="card-header" id="headingThree">
                      <h2 class="mb-0">
                        <button class="btn btn-link collapsed shadow-none stretched-link" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                          Collapsible Group Item #3
                        </button>
                      </h2>
                    </div>
                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                      <div class="card-body">
                        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                      </div>
                    </div>
                  </div>
                </div>
              </section>
             </div>
             <div v-else>
              <section class="py-5 pro-info">
                <div class="container-fluid widecont">
                  <div class="row">
                    <div class="col-lg-6 pb-5 pb-lg-0">
                      <div class="row px-3">
                        Product data not found
                      </div>
                    </div>
                  </div>
                </div>
              </section>
             </div>`,

  components: {
    carousel,
    options
  },

  beforeRouteUpdate (to, from, next) {
    this.color = to.query.color;
    this.configuration = to.query.config;
    this.getProduct();
    next();
  },

  data: () => ({
    colors: [],
    color:'',
    configurations: [],
    configuration: '',
    disabledColors: [],
    disabledConfigs: [],
    initialized: false,
    product: [],
    images: []
  }),

  created () {
    this.getProductOptions(this.$route);
  },

  methods: {
    getProductOptions () {
      if (typeof this.$route.params.prefix !== 'undefined') {
        let getDefault = false;
        if (typeof this.$route.query.color === 'undefined' || typeof this.$route.query.config === 'undefined') {
          getDefault = true;
        }
        let url = 'ProductOptions/' + encodeURIComponent(this.$route.params.prefix) + (getDefault ? '/1' : '/0');
        API.get(url, {
          errorMessage: 'Error getting product options',
          success: data => {
            if (typeof data !== 'undefined') {
              if (typeof data.Colors !== 'undefined') {
                this.colors = data.Colors;
              }
              if (typeof data.Configurations !== 'undefined') {
                this.configurations = data.Configurations;
              }
              if (typeof data.DefaultConfig !== 'undefined') {
                let q = {
                        color: data.DefaultConfig.Color,
                        config: data.DefaultConfig.Configuration
                      };
                this.navigate(q);
              } else {
                this.color = this.$route.query.color;
                this.configuration = this.$route.query.config;
                this.getProduct();
              }
            }
          },
          error: () => {
            alert('Error getting product options');
          }
        }, this)
      }
    },

    getProduct () {
      let url = 'Product/' + encodeURIComponent(this.$route.params.prefix) + '?color=' + encodeURIComponent(this.color) + 
        '&config=' + encodeURIComponent(this.configuration);
      API.get(url, {
        errorMessage: 'Error getting product data',
        success: data => {
          if (typeof data !== 'undefined') {
            this.product = data.Product;
            this.images = data.Images;
            this.disabledColors = data.Disable.Colors;
            this.disabledConfigs = data.Disable.Configurations;
            this.initialized = true;
            document.title = 'Products: ' + this.$route.params.prefix;
          }
        },
        error: () => {
          alert('Error getting product data');
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
        query: q
      };

      if (!this.sameQuery(route.query, this.$router.currentRoute.query)) {
        this.$router.push(route);

        return true;
      }
    },

    handleColorClick (color) {
      let q = {
        color: color,
        config: this.configuration
      };
      this.navigate(q);
    },

    handleConfigClick (configuration) {
      let q = {
        color: this.color,
        config: configuration
      };
      this.navigate(q);
    }
  }
});

export default product;