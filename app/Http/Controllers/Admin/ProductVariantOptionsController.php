<?php

namespace App\Http\Controllers\Admin;

use DataForm;
use Input;
use View;
use Exception;
use Log;
use Bugsnag;

use Illuminate\Http\Request;

use App\Components\Shopify;
use App\Models\Product;
use App\Models\ProductDesignerFile;
use App\Models\ProductVariant;
use App\Models\FileAttachment;
use App\Models\ProductModel;
use App\Http\Requests\Admin\Product\ProductSaveFormRequest;

class ProductVariantOptionsController extends AdminController
{
    use \App\Http\Controllers\Admin\Traits\RapydControllerTrait;

    protected function getModelForAdd()
    {
        return new ProductModel();
    }

    protected function getModelForEdit($id)
    {
        return Product::find($id);
    }

    protected function getModelForDelete($id)
    {
        return Product::find($id);
    }

    public function add(Request $request) {
        $form = DataForm::source($this->getModelForAdd());
        return $this->form($form);
    }

    public function edit($request, $id) {
        $model = $this->getModelForEdit($id);

        if (!$model) {
            abort(404);
        }

        $form = DataForm::source($model);
        return $this->form($form);
    }

    protected function form($form){
        
        $this->addProductVariationsForm($form);
        $formView = $form->view();
        return view('admin.pages.product-variant-options.show', [
            'title' => trans('labels.products'),
            'subtitle' => $form->model->name,
            'model' => $form->model,
            'form' => $formView,
            'formObject' => $form,
            'moderationStatusHistory' => $form->model->moderationStatusRevisionHistory,
            'moderationCommentHistory' => $form->model->moderationCommentRevisionHistory
        ]);
    }

    public function all(Request $request){

    }

    protected function addProductVariationsForm($form){

        $form->attr('enctype', 'multipart/form-data');

        $form->add('name', trans('labels.name'), 'text')
            ->attr('readonly', true)
            ->rule('required|string|max:255');

        $form->add('sku', trans('labels.sku'), 'text')
            ->attr('readonly', true);

        $form->submit(trans('actions.save'), 'BR');

        return $form;    

    }
}
