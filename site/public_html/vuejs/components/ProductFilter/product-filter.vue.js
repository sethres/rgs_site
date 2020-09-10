import Vue from 'https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.esm.browser.js';

let productFilter = Vue.component('ProductFilter', {
  template: `<div class="pb-5" v-if="items.length > 0">
              <button :id="type" class="btn btn-outline-dark shadow-none" type="button" data-toggle="collapse" :data-target="'#collapse' + this.type" aria-expanded="false" aria-controls="'collapse' + this.type">
                {{ this.type }} <i class="fas fa-caret-down"></i>
              </button>
              <hr class="p-0 ml-0">
              <div :id="'collapse' + this.type" class="collapse">
                <ul class="list-unstyled">
                  <li v-for="item in items" class="mb-1" :key="item.URL">
                    <a href="#" :id="item.Value" style="font-size: .85rem;" v-on:click="clicked(item.Value, $event)">{{ item.Value }}</a>
                  </li>
                </ul>
              </div>
            </div>`,

            props: {
              type: { type: String, default: 'Category' },
              items: { type: Array, default: () => [] }
            },

            methods: {
              clicked (filter, e) {
                e.preventDefault();
                this.$emit('clicked', filter);
              }
            }
});

export default productFilter;