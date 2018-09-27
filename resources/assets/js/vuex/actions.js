const
  { mapGetters, mapActions } = require('vuex');

module.exports = {
  
  getStores({ commit }) {
    return new Promise((resolve, reject) => {
      App.models.store.getAllForUser((response) => {
        commit('SET_STORES', {
          stores: response.data.stores
        });
        resolve();
      },
      () => reject());
    });
  },

  getProductCategories({ commit }, categories) {
    commit('SET_PRODUCT_CATEGORIES', {
      categories
    });
  },
  
  getCatalogAttributes({ commit }, catalogAttributes) {
    commit('SET_CATALOG_ATTRIBUTES', {
      catalogAttributes
    });
  },
  
  changeAddProductWizardPreviewDpi({ commit }, dpi) {
    commit('CHANGE_ADD_PRODUCT_WIZARD_PREVIEW_DPI', {
      dpi
    });
  }
};
