var Colorpicker = require('vue-color');
Vue.component('chrome-colorpicker', Colorpicker.Chrome);

Vue.component('update-color-form', {
  data() {
    return {
      colors: {
        hex: App.data.CurrentForm.color
      }
    }
  }
});
