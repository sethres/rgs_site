import Vue from 'https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.esm.browser.js';

let productFilter = Vue.component('ProductFilter', {
  template: `<div class="pb-5" v-if="items.length > 0">
              <h6 :id="type" class="text-uppercase filtertype">
                {{ this.type }}
              </h6>
              <hr class="p-0 ml-0">
              <div :id="this.type">
                <ul class="list-unstyled">
                  <li v-for="item in items" class="mb-1" :key="item.URL">
                    <a href="#" style="font-size: .85rem;" :id="item.Value" v-on:click="clicked(item.Value, $event)">{{ item.Value }}</a>
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
