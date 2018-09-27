<modal :value="showAddProductModal" :large="true" :backdrop="false" >
  <div slot="modal-header" class="modal-header text-center">
    <h4 class="modal-title font-weight-light"> @lang('labels.choose_category')</h4>
    <button type="button" class="close" @click="showAddProductModal = false">
      <i class="fa fa-times"></i>
    </button>
  </div>
  <div slot="modal-body" class="modal-body">
  <add-product-wizard
    inline-template="true"
  >
    @include('widgets.dashboard.product.add-product-wizard')
  </add-product-wizard>
    
  </div>
  <div slot="modal-footer" class="modal-footer d-n"></div>
</modal>