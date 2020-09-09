import Vue from 'https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.esm.browser.js';
import Router from 'https://cdn.jsdelivr.net/npm/vue-router@3.4.3/dist/vue-router.esm.browser.js';
import productResults from '../components/ProductFilter/product-results.vue.js';

Vue.use(Router);

const router = new Router({
  mode: 'history',
  base: '/products',
  routes: [
    {
      path: '/:categoryURL([A-Za-z0-9\-]+)?/:collectionURL([A-Za-z0-9\-]+)?/:subcollectionURL([A-Za-z0-9\-]+)?',
      name: 'ProductResults',
      component: productResults
    },
    {
      path: '/index.php', redirect: { name: 'ProductResults' }
    }
    /*
    { // may want to improve 404 handling in the future but works for now, just not the best for internal links.
      path: '*',
      component: PageNotFound
    }*/
  ],
  scrollBehavior (to, from, savedPosition) {
    if (to.hash) {
      return {
        selector: to.hash
        // , offset: { x: 0, y: 10 }
      }
    } else {
      return { x: 0, y: 0 }
    }
  }
});

router.afterEach((to, from) => {
  /*window.dataLayer.push({
    'event': 'VirtualPageview',
    'virtualPageURL': to.path,
    'virtualPageTitle': to.name.replace(/([A-Z])/g, ' $1').trim()
  })*/
});

export default router;