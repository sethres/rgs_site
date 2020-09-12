import Vue from 'https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.esm.browser.js';

let paging = Vue.component('Paging', {
    template: `<div class="col-12 pagenumbers p-3" v-if="pages > 1">
                    Page:
                    <a href="#" style="font-size: 1rem; margin: 0 .5rem;" 
                      v-for="p in pages" 
                      :key="p" :class="{ curPage: p == page }" 
                      v-on:click="clicked(p, $event)">
                      {{ p }}
                    </a>
                </div>`,
  
    props: {
      page: { type: Number, default: 1 },
      pages: { type: Number, default: 0 }
    },

    methods: {
      clicked (page, e) {
        e.preventDefault();
        this.$emit('pageClicked', page);
      }
    }
  });

export default paging;