<?php

namespace App\Http\Controllers\Admin;

use DataForm;
use Input;

use Illuminate\Http\Request;

use App\Models\CatalogAttribute;
use App\Models\CatalogAttributeOption;

class CatalogAttributeOptionsController extends AdminController
{
    use \App\Http\Controllers\Admin\Traits\RapydControllerTrait;

    protected function getModelForAdd()
    {
        return new CatalogAttributeOption();
    }

    protected function getModelForEdit($id)
    {
        return CatalogAttributeOption::find($id);
    }

    protected function getModelForDelete($id)
    {
        return CatalogAttributeOption::find($id);
    }

    public function all(Request $request)
    {}

    /**
     * Add state
     */
    public function add(Request $request, $attribute_id)
    {
        $model = $this->getModelForAdd();
        $model->attribute_id = $attribute_id;
        $model->attribute = CatalogAttribute::find($attribute_id);

        $form = DataForm::source($model);
        return $this->form($form);
    }

    public function edit(Request $request, $attribute_id, $id)
    {
        $model = $this->getModelForEdit($id);

        if (!$model) {
            abort(404);
        }

        $model->attribute_id = $attribute_id;
        $model->attribute = CatalogAttribute::find($attribute_id);
        $form = DataForm::source($model);
        return $this->form($form);
    }

    public function delete(Request $request, $attribute_id, $id)
    {
        $model = $this->getModelForDelete($id);

        if (!$model) {
            abort(404);
        }

        $form = DataForm::source($model);
        $form->action = 'delete';
        return $this->form($form);
    }

    /**
     * All patients
     */
    public function getByAttribute(Request $request, $attribute_id)
    {
        $attribute = CatalogAttribute::find($attribute_id);
        $model = CatalogAttributeOption::where('attribute_id', $attribute_id);

        $filter = \DataFilter::source($model);
        $filter->add('name', trans('labels.name'), 'text');
        $filter->submit(trans('actions.search'));
        $filter->reset(trans('actions.reset'));
        $filter->build();

        $grid = \DataGrid::source($filter);

        $grid->add('name', trans('labels.name'), false);

        $grid->add('id', trans('labels.actions'))->cell(function($value, $row) use($attribute_id) {
            return '
                <a class="btn btn-xs btn-primary" href="'.url('/admin/catalog-attributes/'.$attribute_id.'/options/'.$value.'/edit').'">
                    <i class="fa fa-edit"></i>
                    '.trans('actions.edit').'
                </a>
                <a class="btn btn-xs btn-danger" href="'.url('/admin/catalog-attributes/'.$attribute_id.'/options/'.$value.'/delete').'">
                    <i class="fa fa-times"></i>
                    '.trans('actions.delete').'
                </a>
            ';
        });

        $grid->link('/admin/catalog-attributes/'.$attribute_id.'/options/add', trans('actions.add'), 'TR');
        $grid->orderBy('name','asc');
        $grid->paginate(10);

        return view('admin.pages.default.grid', [
            'title' => trans('labels.options').' > '.$attribute->name,
            'grid' => $grid,
            'filter' => $filter
        ]);
    }

    protected function theForm($form)
    {
        $form
            ->add('name', trans('labels.name'), 'text')
            ->rule('required|string|max:255');

        if ($form->model->catalogAttribute->name == \App\Models\CatalogAttribute::ATTRIBUTE_COLOR) {
            $form
                ->add('value', trans('labels.color'), 'text')
                ->attr('v-model', 'colors.hex')
                ->attr('class', 'hidden')
                ->rule('string');

            $form->add('chrome-colorpicker', null, 'container')
                ->content('
                    <div class="form-group clearfix">
                        <label for="visibility" class="col-sm-2 control-label"></label>
                        <div class="col-sm-10">
                            <chrome-colorpicker :colors.sync="colors"></chrome-colorpicker>
                        </div>
                    </div>
                ');
        }
        else {
            $form
                ->add('value', trans('labels.value'), 'text')
                ->rule('string');
        }

        $form->link('/admin/catalog-attributes/'.$form->model->attribute_id.'/options', trans('actions.back_to_attribute_options'), 'BL', [
            'class' => 'btn btn-default ml-10'
        ]);

        $form->submit(trans('actions.save'), 'BR');

        return $form;
    }

    protected function form($form)
    {
        $create = ($form->action == 'insert');

        $form = $this->theForm($form);

        $form->saved(function() use ($form, $create) {

            if ($form->action == 'delete') {
                flash()->success(trans('messages.deleted'));
            }
            else {
                $form->model->save();

                flash()->success(trans('messages.saved'));

                if ($create) {
                    return redirect(url('/admin/catalog-attributes/'.$create.'/options/'.$form->model->id.'/edit'));
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

        return view('admin.pages.product-model-templates.update-color', [
            'title' => trans('labels.options').' > '.$form->model->attribute->name,
            'subtitle' => $subtitle,
            'form' => $formView,
            'formObject' => $form
        ]);
    }

}
