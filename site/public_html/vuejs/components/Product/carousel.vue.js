import Vue from 'https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.esm.browser.js';
// needs set up to only use the first 6
let carousel = Vue.component('Carousel', {
    template: `<div id="detCarousel" class="detcarousel carousel slide carousel-fade w-100" data-ride="carousel">
                  <div class="carousel-inner">
                      <div :class="['carousel-item', (index === 0 ? 'active' : ''), 'bg-white border']"
                        :key="image" :data-slide-number="index" v-for="(image, index) in images">
                        <a class="stretched-link" :href="image" target="_blank">
                          <img class="d-block w-100" :src="image" :alt="image">
                        </a>
                      </div>
                  </div>
                  <ul class="carousel-indicators list-inline mb-0 mt-3 mx-auto">
                      <li :class="['list-inline-item', (index === 0 ? 'active' : ''), 'border']" v-for="(image, index) in images">
                        <a id="carousel-selector-0" class="selected" :key="image" :data-slide-to="index" data-target="#detCarousel" v-on:click="clicked(p, $event)">
                          <img class="d-block" :src="image" :alt="image">
                        </a>
                      </li>
                  </ul>
                </div>`,
  
    props: {
      imagesProp: { type: Array, default: () => [] }
    },

    computed: {
      images: function () {
        if (this.imagesProp.length === 0) {
          return ['https://via.placeholder.com/3000/ffffff/212529?text=IMG+Coming+Soon'];
        }

        return this.imagesProp;
      }
    },

    methods: {
      clicked (page, e) {
        e.preventDefault();
        this.$emit('pageClicked', page);
      }
    }
  });

export default carousel