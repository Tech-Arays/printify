const
  { mapGetters } = require('vuex');



Vue.component('add-product-wizard', {

    data() {
        return {
          store: App.data.Store,
          selectedCategories: [{ name: 'Foo' },
          { name: 'Bar' }],
          
        }
        
      },
    
});
