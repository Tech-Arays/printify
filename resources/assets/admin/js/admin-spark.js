if (typeof window.App == 'undefined') {
  window.App = {};
}

require('spark-bootstrap');

require('../../js/components/bootstrap');
require('./components/product-model/update-color-form.js');
window.App.Vue = new window.Vue({
  mixins: [require('spark')]
});
