<?php

namespace App\Http\Controllers\Admin;

use DataForm;
use Input;

use Illuminate\Http\Request;

use App\Models\CatalogAttribute;

class CatalogAttributesController extends AdminController
{
    use \App\Http\Controllers\Admin\Traits\RapydControllerTrait;
    
    protected function getModelForAdd()
    {
        return new CatalogAttribute();
    }
    
    protected function getModelForEdit($id)
    {
        return CatalogAttribute::find($id);
    }
    
    protected function getModelForDelete($id)
    {
        return CatalogAttribute::find($id);
    }
    
    /**
     * All patients
     */
    public function all(Request $request)
    {
        $model = new CatalogAttribute();
        
        $filter = \DataFilter::source($model);
        $filter->add('name', trans('labels.name'), 'text');
        $filter->submit(trans('actions.search'));
        $filter->reset(trans('actions.reset'));
        $filter->build();
        
        $grid = \DataGrid::source($filter);

        $grid->add('name', trans('labels.name'), false);
        
        $grid->add('id', trans('labels.actions'))->cell(function($value, $row) {
            return '
                <a class="btn btn-xs btn-info" href="'.url('/admin/catalog-attributes/'.$value.'/options').'">
                    <i class="fa fa-tags"></i>
                    '.trans('actions.options').'
                </a>
                <a class="btn btn-xs btn-primary" href="'.url('/admin/catalog-attributes/'.$value.'/edit').'">
                    <i class="fa fa-edit"></i>
                    '.trans('actions.edit').'
                </a>
                <a class="btn btn-xs btn-danger" href="'.url('/admin/catalog-attributes/'.$value.'/delete').'">
                    <i class="fa fa-times"></i>
                    '.trans('actions.delete').'
                </a>
            ';
        });
     
        $grid->link('/admin/catalog-attributes/add', trans('actions.add'), 'TR');
        $grid->orderBy('name','asc');
        $grid->paginate(10);
        
        return view('admin.pages.default.grid', [
            'title' => trans('labels.catalog_attributes'),
            'grid' => $grid,
            'filter' => $filter
        ]);
    }
    
    protected function theForm($form)
    {
        $form
            ->add('name', trans('labels.name'), 'text')
            ->rule('required|string|max:255');
            
        $form
            ->add('value', trans('labels.value'), 'text')
            ->rule('required|string|max:255');
        
        $form->link('/admin/catalog-attributes', trans('actions.back_to_catalog_attributes'), 'BL', [
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
                    return redirect(url('/admin/catalog-attributes/'.$form->model->id.'/edit'));
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
        
        return view('admin.pages.default.edit', [
            'title' => trans('labels.catalog_attributes'),
            'subtitle' => $subtitle,
            'form' => $formView,
            'formObject' => $form
        ]);
    }
    
}
