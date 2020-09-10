import Vue from 'https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.esm.browser.js';

let productResults = Vue.component('ProductResults', {
    template: `<div class="col-lg-10">
                <div class="row no-gutters" id="result">
                    <div class="col-12 pagenumbers p-3">
                        Page:
                    </div>
                    <div class="col-12 col-md-3 p-3 text-center" v-for="product in products" :key="product.SKU">
                      <div class="card h-100">
                          <div style="" class="card-img-top img-fluid">
                            <img :src="product.Image" style="object-fit: contain; padding: 1rem; height: 20rem; width: 100%" class="img-responsive" :alt="product.Image.replace('/images/products/', '')">
                          </div>
                          <div class="card-body">
                          <h6 class="card-title mb-2" style="font-size: .9375rem; font-weight: 600;">{{ product.Name }}</h6>
                          <a class="stretched-link" style="font-size: .75rem" :href="'/details.php?skuvar=' + product.Prefix">View Product <i class="fa fa-angle-double-right"></i></a>
                          </div>
                      </div>
                    </div>
                    <?php
                }
                ?>
                <div class="col-12 pagenumbers p-3">
                    Page:
                </div>
                </div>
            </div>`,
  
              props: {
                products: { type: Array, default: () => [] }
              },
  });

export default productResults;