import Vue from 'https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.esm.browser.js';
// needs set up to only use the first 6
let options = Vue.component('Options', {
    template: `<div div style="font-size: 0" v-if="options.length > 0">
                <button style="font-size: .85rem"
                  :class="['btn btn-outline-secondary shadow-none py-3 px-4 m-1', (option === selected ? 'active' : '')]" 
                  :disabled="disabled.includes(option)"
                  v-for="option in options"
                  v-on:click="optionClick(option, $event)"
                >{{ option }}</button>
              </div>`,

    props: {
      options: { type: Array, default: () => [] },
      disabled: { type: Array, default: () => [] },
      selected: { type: String, default: '' }
    },

    methods: {
      optionClick (option, e) {
        e.preventDefault();
        this.$emit('optionClicked', option);
      }
    }
  });

export default options;