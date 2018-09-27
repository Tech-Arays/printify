<?php

namespace App\Http\Controllers\Admin;

use DataForm;
use Input;
use Artisan;
use Cache;

use Illuminate\Http\Request;

use App\Models\ProductModelTemplate;
use App\Models\ProductModel;
use App\Models\CatalogCategory;
use App\Models\File;
use App\Models\User;
use App\Models\FileAttachment;
use App\Http\Requests\Admin\ProductModel\ProductModelSaveFormRequest;
use App\Transformers\User\UserBriefTransformer;
use App\Transformers\PriceModifier\PriceModifierBriefTransformer;
use App\Jobs\ProductModelTemplate\ProductModelTemplateImportJob;

class VariantAttributesController extends AdminController
{
    use \App\Http\Controllers\Admin\Traits\RapydControllerTrait;

    protected function getModelForAdd()
    {
        return new ProductModelTemplate();
    }

    protected function getModelForEdit($id)
    {
        return ProductModelTemplate::find($id);
    }

    protected function getModelForDelete($id)
    {
        return ProductModelTemplate::find($id);
    }

    protected function grid(Request $request, $model, $title)
    {
        
        $filter = \DataFilter::source($model);
        $filter->add('name', trans('labels.name'), 'text');
        $filter->add('title', trans('labels.title'), 'text');
        $filter->submit(trans('actions.search'));
        $filter->reset(trans('actions.reset'));
        $filter->build();
        
        $grid = \DataGrid::source($filter);

        $grid->add('pos', trans('labels.pos'), false);
        $grid->add('product_title', trans('labels.title'), false);
        $grid->add('name', trans('labels.name'), false);
        $grid->add('price', trans('labels.price_modifier_type'), false);
        $grid->add('weight', trans('labels.weight_modifier_type'), false);
        $grid->add('visibility', trans('labels.status'), false)
            ->cell(function($value, $row) {
                return $row->getVisibilityName();
            });
        $grid->add('updated_created', trans('labels.updated_created'))
            ->cell(function($value, $row) {
                return '
                    <time
                        datetime="'.$row->updatedAtTZ().'"
                        title="'.$row->updatedAtTZ().'"
                        data-format="">
                        '.$row->updatedAtTZ().'
                    </time>
                    /
                    <time
                        datetime="'.$row->createdAtTZ().'"
                        title="'.$row->createdAtTZ().'"
                        data-format="">
                        '.$row->createdAtTZ().'
                    </time>
                ';
            });

        $grid->add('id', trans('labels.actions'))->cell(function($value, $row) {
            return '
                <a class="btn btn-xs btn-primary" href="'.url('/admin/variant-options/'.$value.'/edit').'">
                    <i class="fa fa-edit"></i>
                    '.trans('actions.edit').'
                </a>
                <a class="btn btn-xs btn-warning" href="'.url('/admin/variation-options/'.$value.'/add').'">
                    <i class="fa fa-plus"></i>
                    '.trans('actions.add-variations').'
                </a>
            ';
        });

        $grid->orderBy('name','asc');
        $grid->paginate(10);

        return view('admin.pages.default.grid', [
            'title' => $title,
            'grid' => $grid,
            'filter' => $filter
        ]);
    }

    /**
     * All product templates
     */
    public function all(Request $request)
    {
        $model = ProductModelTemplate::with('category')
            ->with('garment')
            ->with('garment.garmentGroup');

        return $this->grid($request, $model, trans('labels.product_models'));
    }

    public function add(ProductModelSaveFormRequest $request)
    {
        $form = DataForm::source($this->getModelForAdd());
        return $this->form($form);
    }

    public function edit(ProductModelSaveFormRequest $request, $id)
    {
        $model = $this->getModelForEdit($id);

        if (!$model) {
            abort(404);
        }

        $form = DataForm::source($model);
        return $this->form($form);
    }

    protected function form($form)
    {

        $create = ($form->action == 'insert');

        $form = $this->addProductModelForm($form);

        if (Input::isMethod('post')) {
            $inventory_statuses = Input::get('inventory_statuses');
            if (!empty($inventory_statuses)) {
                $inStockModels = collect([]);
                $outOfStockModels = collect([]);
                foreach($form->model->models as $model) {
                    if ($inventory_statuses[$model->id] == ProductModel::INVENTORY_STATUS_OUT_OF_STOCK) {
                        $outOfStockModels->push($model);
                    }
                    else if ($inventory_statuses[$model->id] == ProductModel::INVENTORY_STATUS_IN_STOCK) {
                        $inStockModels->push($model);
                    }
                }

                ProductModel::manageInventoryInStock($inStockModels);
                ProductModel::manageInventoryOutOfStock($outOfStockModels);
            }
        }

        $form->saved(function() use ($form, $create) {
            
            if ($form->action == 'delete') {
                flash()->success(trans('messages.deleted'));
            }
            else {

                // preview
                if (
                    Input::hasFile('preview')
                    && Input::file('preview')->isValid()
                ) {
                    $preview = File::create([
                        'file' => Input::file('preview'),
                        'file_content_type' => File::TYPE_MODEL_PREVIEW
                    ]);
                    if ($preview) {
                        $form->model->preview_file_id = $preview->id;
                    }
                }

                // image
                if (
                    Input::hasFile('image')
                    && Input::file('image')->isValid()
                ) {
                    $image = File::create([
                        'file' => Input::file('image'),
                        'content_file_type' => File::TYPE_MODEL_IMAGE
                    ]);
                    if ($image) {
                        $form->model->image_file_id = $image->id;
                    }
                }

                // image_back
                if (
                    Input::hasFile('image_back')
                    && Input::file('image_back')->isValid()
                ) {
                    $image = File::create([
                        'file' => Input::file('image_back'),
                        'content_file_type' => File::TYPE_MODEL_IMAGE_BACK
                    ]);
                    if ($image) {
                        $form->model->image_back_file_id = $image->id;
                    }
                }

                // example
                    if (
                        Input::hasFile('example')
                        && Input::file('example')->isValid()
                    ) {
                        $exampleFile = FileAttachment::create([
                            'file' => Input::file('example'),
                            'content_file_type' => File::TYPE_MODEL_EXAMPLE
                        ]);
                        if ($exampleFile) {
                            $form->model->example_file_id = $exampleFile->id;
                        }
                    }

                // overlay
                    if (
                        Input::hasFile('overlay')
                        && Input::file('overlay')->isValid()
                    ) {
                        $overlayFile = File::create([
                            'file' => Input::file('overlay'),
                            'content_file_type' => File::TYPE_MODEL_OVERLAY
                        ]);
                        if ($overlayFile) {
                            $form->model->overlay_file_id = $overlayFile->id;
                        }
                    }

                // overlay_back
                    if (
                        Input::hasFile('overlay_back')
                        && Input::file('overlay_back')->isValid()
                    ) {
                        $overlayBackFile = File::create([
                            'file' => Input::file('overlay_back'),
                            'content_file_type' => File::TYPE_MODEL_OVERLAY_BACK
                        ]);
                        if ($overlayBackFile) {
                            $form->model->overlay_back_file_id = $overlayBackFile->id;
                        }
                    }

                    if(Input::has('product_meta')){
                        $form->model->product_meta = json_encode(Input::get('product_meta'));
                    }

                $form->model->save();

                flash()->success(trans('messages.saved'));
                if ($create) {
                    return redirect(url('/admin/variant-options/'.$form->model->id.'/edit'));
                }
            }
            return redirect()->back();
        });

        if ($form->model->id) {
            $subtitle = trans('labels.editing').' - #'.$form->model->id;
        }
        else {
            $subtitle = trans('labels.adding');
        }

        $formView = $form->view();

        if ($form->hasRedirect()) {
            return $form->getRedirect();
        }

        $users = User::getNotBanned();
        $users = $this->serializeCollection($users, new UserBriefTransformer);

        $priceModifiers = [];
        if ($form->model->id) {
            $priceModifiers = $this->serializeCollection(
                $form->model->priceModifiers, new PriceModifierBriefTransformer
            );
        }

        return view('admin.pages.product-variant-options.edit', [
            'title' => trans('labels.product_models'),
            'subtitle' => $subtitle,
            'form' => $formView,
            'formObject' => $form,
            'priceModifiers' => $priceModifiers,
            'users' => $users
        ]);
    }

    protected function addProductModelForm($form)
    {
        $form->attr('enctype', 'multipart/form-data');

        $form->add('name', trans('labels.name'), 'text')->mode('readonly')
            ->rule('required|string|max:255');

        $form->add('sku', trans('labels.sku'), 'text');

        $form->add('garment_id',trans('labels.garment_group'),'select')
            ->options(\App\Models\Garment::all()->pluck("full_garment", "id"));

        $form->add('visibility', trans('labels.visibility'), 'select')
            ->options(ProductModelTemplate::listVisibilities());

        $form->add('product_title', trans('labels.product_title'), 'text')
            ->rule('string');


        $form->add('preview', null, 'container')->content(
            '<div class="form-group clearfix">
                <label class="col-sm-2 control-label">'.trans('labels.preview').'</label>
                <div class="col-sm-10">
                    '.view('widgets.fileinput', [
                        'file' => $form->model->preview ? $form->model->preview->url() : null,
                        'name' => 'preview',
                        'mode' => 'image'
                    ])->render().'
                </div>
            </div>
            <hr />'
        );

        $form->link('/admin/product-models', trans('actions.back_to_product_models'), 'BL', [
            'class' => 'btn btn-default ml-10'
        ]);
        $color_arr = [];
        $size_arr = [];
        if($form->model->product_meta){
            foreach(json_decode($form->model->product_meta) as $meta_key=>$meta_value){
                if($meta_key == 'color'){
                    $color_arr  = $meta_value;
                }
                if($meta_key=='size'){
                    $size_arr = $meta_value;
                }
            }
        }    
        
       
        if ($form->model->category) {
           
            foreach ($form->model->catalogAttributes() as $attr) {
                
                if($attr->value=='color'){
            
                    $colors = ''; 
                    $count  = 0; 
                    foreach($form->model->catalogAttributeOptions($attr->id) as $attr_id => $options){
                        if($count == 100){
                            break;
                        }
                        if(in_array($options,$color_arr)){
                            $colors .= '<li class=""><label class="btn btn-primary active" style="background:'.$options.'"><input type="checkbox" name="product_meta[color][]" id="item4" value="'.$options.'" class="hidden color-check my-fav" autocomplete="off" checked>	<span class="glyphicon glyphicon-ok"></span></label></li>';
                            $count++;
                        }
                        else{
                            $colors .= '<li class=""><label class="btn btn-primary" style="background:'.$options.'"><input type="checkbox" name="product_meta[color][]" id="item4" value="'.$options.'" class="hidden color-check" autocomplete="off"><span class="glyphicon glyphicon-ok"></span></label></li>';
                            $count++;
                        }
                       
                    }     
                    $form->add($attr->value.'.name', $attr->name, 'container')->content(
                        '<div class="form-group clearfix">
                            <div class="col-sm-10">
                                <ul class="color_box_ul">'.
                                $colors
                                .'</ul>
                            </div>
                        </div>
                        <hr/>'
                    );
                }
                elseif($attr->value=='size'){
                    $count  = 0; 
                    $size = '';
                    foreach($form->model->catalogAttributeOptions($attr->id) as $attr_id => $options){
                        if($count == 100){
                            break;
                        }
                        if(in_array($attr_id,$size_arr)){
                            $size .='<li>
                                        <label class="btn btn-primary check"><span class="size-check">'.$options.'</span><input type="checkbox" name="product_meta[size][]" id="item4" value="'.$attr_id.'" class="hidden" autocomplete="off" checked></label>
                                    </li>';
                            $count++;
                        }
                        else{
                            $size .='<li>
                                        <label class="btn btn-primary"><span class="size-check">'.$options.'</span><input type="checkbox" name="product_meta[size][]" id="item4" value="'.$attr_id.'" class="hidden" autocomplete="off"></label>
                                    </li>';
                            $count++;
                        }    
                    }     
                    $form->add($attr->value.'.name', $attr->name, 'container')->content(
                        '<div class="form-group clearfix">
                            <div class="col-sm-10">
                                <ul class="size-attributes">'.
                                $size
                                .'</ul>
                            </div>
                        </div>
                        <hr/>'
                    );
                }
                else{
                    $form->add($attr->value.'.name', $attr->name, 'select')
                   ->options(
                    $form->model->catalogAttribute($attr->value)
                    );
                }

            }
        }   
        else {
            $form->add('preview', null, 'container')->content(
                '<div class="form-group clearfix">
                    <label class="col-sm-2 control-label">'.trans('labels.attributes').'</label>
                    <div class="col-sm-10">
                        '.trans('labels.attributes_will_be_available_after_adding_category').'
                    </div>
                </div>'
            );
        }

        $form->submit(trans('actions.save'), 'BR');

        return $form;
    }
}
