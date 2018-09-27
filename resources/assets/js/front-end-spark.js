require('spark-bootstrap');
require('./spark-components/bootstrap');
const
  modal = require('vue-strap/dist/vue-strap.min').modal,
  { mapGetters, mapActions } = require('vuex');

//const store = require('./vuex/store');

window.App.Vue = new window.Vue({
  mixins: [require('spark')],

  store,


  data: {
    showAddProductModal: false
  },

  components: {
    modal
  },
  ready() {
    /*if (this.user) {
      App.models.product.getCategories((response) => {
        this.getProductCategories(response.data.categories);
        this.getCatalogAttributes(response.data.attributes);
      });
    }*/
  },

  methods: {
    ...mapActions([
      'getProductCategories',
      'getCatalogAttributes'
    ])
  }
});
window.Vue.config.devtools = Config.get('app.debug');
window.Vue.config.debug = Config.get('app.debug');
