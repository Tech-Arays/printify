const
  Vuex = require('vuex'),
  actions = require('./actions');

module.exports = new Vuex.Store({
  state: {
    stores: [],
    productCategories: [],
    catalogAttributes: []
  },

  mutations: {
    SET_STORES(state, payload) {
      state.stores = payload.stores.slice();
    },

    SET_PRODUCT_CATEGORIES(state, payload) {
      state.productCategories = payload.categories.slice();
    },

    SET_CATALOG_ATTRIBUTES(state, payload) {
      state.catalogAttributes = _.indexBy(payload.catalogAttributes.slice(), (o) => {
        return o.id;
      });
    },

    CHANGE_ADD_PRODUCT_WIZARD_PREVIEW_DPI(state, payload) {
      state.addProductWizardPreviewDPI = payload.dpi;
    }

  },
  actions
});
