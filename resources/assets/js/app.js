
/*
 |--------------------------------------------------------------------------
 | Laravel Spark Bootstrap
 |--------------------------------------------------------------------------
 |
 | First, we will load all of the "core" dependencies for Spark which are
 | libraries such as Vue and jQuery. This also loads the Spark helpers
 | for things such as HTTP calls, forms, and form validation errors.
 |
 | Next, we'll create the root Vue application for Spark. This will start
 | the entire application and attach it to the DOM. Of course, you may
 | customize this script as you desire and load your own components.
 |
 */

require('spark-bootstrap');

require('./components/bootstrap');

const
  modal = require('vue-strap/dist/vue-strap.min').modal,
  { mapGetters, mapActions } = require('vuex');
  
const store = require('./vuex/store');

const sync = require('./components/store/sync.js');
require('./components/product/add-product-wizard.js');
var email = document.querySelector('#session').value;
Spark.forms.register = {
    email: email
};


var app = new Vue({
    mixins: [require('spark')],
    
    store,

    components: {
      modal
    },

    data: {
        showAddProductModal: false,
        
    },

  ready() {
    if (this.user) {
      App.models.product.getCategories((response) => {
        this.getProductCategories(response.data.categories);
        this.getCatalogAttributes(response.data.attributes);
      });
    }
  },

  methods: {
    ...mapActions([
      'getProductCategories',
      'getCatalogAttributes'
    ])
  }
});
//window.Vue.config.devtools = Config.get('app.debug');
//window.Vue.config.debug = Config.get('app.debug');
