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

class ProductModelTemplatesController extends AdminController
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
        $filter->add('sku', trans('labels.sku'), 'text');
        $filter->add('category.name', trans('labels.category'), 'tags');
        $filter->add('garment.name', trans('labels.garment'), 'tags');
        $filter->submit(trans('actions.search'));
        $filter->reset(trans('actions.reset'));
        $filter->build();

        $grid = \DataGrid::source($filter);

        $grid->add('preview', trans('labels.preview'))
            ->cell(function($value, $row) {
                if ($row->preview) {
                    return '
                        <img class="h-50" src="'.$row->preview->url('thumb').'" alt="" />
                    ';
                }
                else {
                    return '
                        <img class="h-50" src="'.url('img/placeholders/placeholder-300x200.png').'" alt="" />
                    ';
                }
            });
        $grid->add('name', trans('labels.name'), false);
        $grid->add('sku', trans('labels.sku'), false);
        $grid->add('category.name', trans('labels.category'), false);
        $grid->add('garment.garmentGroup.name', trans('labels.garment_group'), false);
        $grid->add('garment.name', trans('labels.garment'), false);
        $grid->add('visibility', trans('labels.visibility'), false)
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
                <a class="btn btn-xs btn-primary" href="'.url('/admin/product-models/'.$value.'/edit').'">
                    <i class="fa fa-edit"></i>
                    '.trans('actions.edit').'
                </a>
                <a class="btn btn-xs btn-danger" href="'.url('/admin/product-models/'.$value.'/delete').'">
                    <i class="fa fa-times"></i>
                    '.trans('actions.delete').'
                </a>
            ';
        });

        $grid->orderBy('name','asc');
        $grid->paginate(10);

        $grid->link('/admin/product-models', trans('labels.paged'), 'TR', [
            'class' => 'btn btn-primary mr-5'
        ]);
        $grid->link('/admin/product-models/digital-catalog', trans('labels.digital_catalog'), 'TR', [
            'class' => 'btn btn-default'
        ]);
        $grid->link('/admin/product-models/add', trans('labels.add_product_model'), 'TR', [
            'class' => 'btn btn-info'
        ]);

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

    public function complete(Request $request)
    {
        $model = ProductModelTemplate::with('category')
            ->with('garment')
            ->with('garment.garmentGroup')
            ->complete();

        return $this->grid($request, $model, trans('labels.complete_product_models'));
    }

    public function available(Request $request)
    {
        $model = ProductModelTemplate::with('category')
            ->with('garment')
            ->with('garment.garmentGroup')
            ->complete()
            ->visible();

        return $this->grid($request, $model, trans('labels.available_product_models'));
    }

    public function incompleteSourceTemplates(Request $request)
    {
        $model = ProductModelTemplate::with('category')
            ->with('garment')
            ->with('garment.garmentGroup')
            ->incompleteSourceTemplates();

        return $this->grid($request, $model, trans('labels.incomplete_source_templates'));
    }

    public function incompleteOverlays(Request $request)
    {
        $model = ProductModelTemplate::with('category')
            ->with('garment')
            ->with('garment.garmentGroup')
            ->incompleteOverlays();

        return $this->grid($request, $model, trans('labels.incomplete_overlays'));
    }

    public function incompletePrices(Request $request)
    {
        $model = ProductModelTemplate::with('category')
            ->with('garment')
            ->with('garment.garmentGroup')
            ->incompletePrices();

        return $this->grid($request, $model, trans('labels.incomplete_prices'));
    }

    /**
     * All product templates as digital catalog
     */
    public function allAsDigitalCatalog(Request $request)
    {
        $model = ProductModelTemplate::with('category')
            ->with('garment')
            ->with('garment.garmentGroup')
            ->with('models');

        $filter = \DataFilter::source($model);
        $filter->add('name', trans('labels.name'), 'text');
        $filter->add('category.name', trans('labels.category'), 'tags');
        $filter->add('garment.name', trans('labels.garment'), 'tags');
        $filter->submit(trans('actions.search'));
        $filter->reset(trans('actions.reset'));
        $filter->build();

        $grid = \DataGrid::source($filter);

        $grid->add('image', trans('labels.image'))
            ->cell(function($value, $row) {
                if ($row->image) {
                    return '
                        <img src="'.$row->image->url('thumb').'" alt="" />
                    ';
                }
                else {
                    return '
                        <img class="h-50" src="'.url('img/placeholders/placeholder-300x200.png').'" alt="" />
                    ';
                }
            });
        $grid->add('sku', trans('labels.sku'), false);
        $grid->add('name', trans('labels.name'), false);
        $grid->add('price', trans('labels.price_modifiers'))
            ->cell(function($value, $template) {
                if ($template->priceModifiers && $template->priceModifiers->count()) {
                    return '<div class="label label-primary">'.$template->priceModifiers->count().'</div>';
                }
            });

        $grid->add('id', trans('labels.actions'))->cell(function($value, $row) {
            return '
                <a class="btn btn-xs btn-primary" href="'.url('/admin/product-models/'.$value.'/edit').'">
                    <i class="fa fa-edit"></i>
                    '.trans('actions.edit').'
                </a>
            ';
        });

        $grid->orderBy('name','asc');
        $grid->paginate(100000);

        $grid->link('/admin/product-models', trans('labels.paged'), 'TR', [
            'class' => 'btn btn-default'
        ]);
        $grid->link('/admin/product-models/digital-catalog', trans('labels.digital_catalog'), 'TR', [
            'class' => 'btn btn-primary'
        ]);

        return view('admin.pages.default.grid', [
            'title' => trans('labels.product_models'),
            'grid' => $grid,
            'filter' => $filter
        ]);
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

    protected function addProductModelForm($form)
    {
        $form->attr('enctype', 'multipart/form-data');

        $form->add('name', trans('labels.name'), 'text')
            ->rule('required|string|max:255');

        $form->add('sku', trans('labels.sku'), 'text');

        $form->add('garment_id',trans('labels.garment_group'),'select')
            ->options(\App\Models\Garment::all()->pluck("full_garment", "id"));

        $form->add('visibility', trans('labels.visibility'), 'select')
            ->options(ProductModelTemplate::listVisibilities());

        $form->add('product_title', trans('labels.product_title'), 'text')
            ->rule('string');

        $form->add('product_description', trans('labels.product_description'), 'textarea')
            ->rule('string');

        // $form->add('mockup_format', trans('labels.mockup_format'), 'text')
        //     ->rule('string');

        $categories = CatalogCategory::getNestedList('name', null, '&nbsp;&nbsp;&nbsp;&nbsp;');
        $form->add('category_id', trans('labels.category'), 'select')
            ->options($categories);

        if ($form->model->category) {

            /*foreach ($form->model->catalogAttributes() as $attr) {
                
                $form->add($attr->value.'.name', $attr->name, 'select')
                    ->options(
                        $form->model->catalogAttribute($attr->value)->pluck('value','id')->all();
                    );
            }*/
            
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

        $form->add('image', null, 'container')->content(
            '<div class="form-group clearfix">
                <label class="col-sm-2 control-label">'.trans('labels.image').'</label>
                <div class="col-sm-10">
                    '.view('widgets.fileinput', [
                        'file' => $form->model->image ? $form->model->image->url() : null,
                        'name' => 'image',
                        'mode' => 'image'
                    ])->render().'
                </div>
            </div>
            <hr />'
        );

        $form->add('image_back', null, 'container')->content(
            '<div class="form-group clearfix">
                <label class="col-sm-2 control-label">'.trans('labels.back_image').'</label>
                <div class="col-sm-10">
                    '.view('widgets.fileinput', [
                        'file' => $form->model->imageBack ? $form->model->imageBack->url() : null,
                        'name' => 'image_back',
                        'mode' => 'image'
                    ])->render().'
                </div>
            </div>
            <hr />'
        );

        $form->add('example', null, 'container')->content(
            '<div class="form-group clearfix">
                <label class="col-sm-2 control-label">'.trans('labels.source_template').'</label>
                <div class="col-sm-10">
                    '.view('widgets.fileinput', [
                        'file'      => $form->model->example
                            ? $form->model->example
                            : null,
                        'name'      => 'example',
                        'mode'      => 'file',
                        'deleteUrl' => $form->model->example
                            ? url('/admin/files/'.$form->model->example->id.'/delete')
                            : null
                    ])->render().'
                </div>
            </div>
            <hr />'
        );

        $form->add('overlay', null, 'container')->content(
            '<div class="form-group clearfix">
                <label class="col-sm-2 control-label">'.trans('labels.overlay').'</label>
                <div class="col-sm-10">
                    '.view('widgets.fileinput', [
                        'file'      => $form->model->overlay
                            ? $form->model->overlay->url()
                            : null,
                        'name'      => 'overlay',
                        'mode'      => 'image',
                        'deleteUrl' => $form->model->overlay
                            ? url('/admin/files/'.$form->model->overlay->id.'/delete')
                            : null
                    ])->render().'
                </div>
            </div>
            <hr />'
        );

        $form->add('overlay_back', null, 'container')->content(
            '<div class="form-group clearfix">
                <label class="col-sm-2 control-label">'.trans('labels.overlay_back').'</label>
                <div class="col-sm-10">
                    '.view('widgets.fileinput', [
                        'file'      => $form->model->overlayBack
                            ? $form->model->overlayBack->url()
                            : null,
                        'name'      => 'overlay_back',
                        'mode'      => 'image',
                        'deleteUrl' => $form->model->overlayBack
                            ? url('/admin/files/'.$form->model->overlayBack->id.'/delete')
                            : null
                    ])->render().'
                </div>
            </div>
            <hr />'
        );

        // ---------------

        $form->link('/admin/product-models', trans('actions.back_to_product_models'), 'BL', [
            'class' => 'btn btn-default ml-10'
        ]);

        $form->submit(trans('actions.save'), 'BR');

        return $form;
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

                $form->model->save();

                flash()->success(trans('messages.saved'));
                if ($create) {
                    return redirect(url('/admin/product-models/'.$form->model->id.'/edit'));
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

        return view('admin.pages.product-model-templates.edit', [
            'title' => trans('labels.product_models'),
            'subtitle' => $subtitle,
            'form' => $formView,
            'formObject' => $form,
            'priceModifiers' => $priceModifiers,
            'users' => $users
        ]);
    }

    /**
     * Initiate import new product models templates from KZ
     */
    public function pullProductModelTemplates(Request $request)
    {
        $output = null;

        if (Input::isMethod('post')) {
            Cache::forget('ProductModelTemplateImportJob_result');
            Cache::forever('ProductModelTemplateImportJob_processing', true);
            dispatch(new ProductModelTemplateImportJob());
            flash()->success(trans('labels.import_started'));
        }

        return view('admin.pages.product-model-templates.import', [
            'title' => trans('labels.product_models_import'),
            'processing' => Cache::get('ProductModelTemplateImportJob_processing', false),
            'output' => Cache::get('ProductModelTemplateImportJob_result', null)
        ]);
    }

    public function pullProductModelTemplatesProcessing()
    {
        return response()->api(null, [
            'processing' => Cache::get('ProductModelTemplateImportJob_processing', false)
        ]);
    }

    /**
     * Initiate product models sync from KZ
     */
    public function pullVariants(Request $request, $id)
    {
        $template = $this->getModelForEdit($id);

        Artisan::call('kz:product-model-template-variants-import', [
            '--id' => $template->id
        ]);

        flash()->success(trans('messages.synced'));

        return redirect()->back();
    }

}
