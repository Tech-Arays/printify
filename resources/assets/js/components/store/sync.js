import { modal,tabs,tab } from 'vue-strap'
Vue.component('store-sync', {
  
    components: {
      modal,
      tabs,
      tab
    },
    
  
    ready() {
      
    },
    
    data() {
      return {
        showAddProductModal: false,
        updateProduct: null,
        addProductWizardLoading: false,
        activeTab:0,
  
      }
    },
    
    watch: {
      
    },
  
    
  });
 
 