<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use DataForm;
use Input;

use App\Http\Controllers\Admin\AdminController;
use App\Models\GarmentGroup;
use App\Models\File;

class GarmentGroupsController extends AdminController
{
    use \App\Http\Controllers\Admin\Traits\RapydControllerTrait;
    
    protected function getModelForAdd()
    {
        return new GarmentGroup();
    }
    
    protected function getModelForEdit($id)
    {
        return GarmentGroup::find($id);
    }
    
    protected function getModelForDelete($id)
    {
        return GarmentGroup::find($id);
    }

    public function all() {
        $model = new GarmentGroup();
        
        $grid = \DataGrid::source($model);

        $grid->add('name', trans('labels.name'), false);
        
        $grid->add('id', trans('labels.actions'))->cell(function($value, $row) {
            return '
                <a class="btn btn-xs btn-primary" href="'.url('/admin/garment-groups/'.$value.'/edit').'">
                    <i class="fa fa-edit"></i>
                    '.trans('actions.edit').'
                </a>
            ';
        });
     
        $grid->orderBy('name','asc');
        $grid->paginate(20);
        
        return view('admin.pages.default.grid', [
            'title' => trans('labels.garment_groups'),
            'grid' => $grid
        ]);
    }
    
    /**
     * Add/edit form
     */
    protected function form($form) {
        $form->attr('enctype', 'multipart/form-data');
        
        $form->add('name', trans('labels.name'), 'text')
            ->rule('required');
            
        //$form->add('preview', null, 'container')->content(
        //    '<div class="form-group clearfix">
        //        <label class="col-sm-2 control-label">'.trans('labels.preview').'</label>
        //        <div class="col-sm-10">
        //            '.view('widgets.fileinput', [
        //                'file' => $form->model->preview ? $form->model->preview->url() : null,
        //                'name' => 'preview',
        //                'mode' => 'image'
        //            ])->render().'
        //        </div>
        //    </div>
        //    <hr />'
        //);
        
        $form->link('/admin/garment-groups', trans('actions.back'));
        $form->submit(trans('actions.save'), 'BR');
        
        $form->saved(function() use ($form) {
            
            if ($form->action == 'delete') {
                flash()->success(trans('messages.deleted'));
            }
            else {
                $create = ($form->action == 'insert');
                
                if (
                    Input::hasFile('preview')
                    && Input::file('preview')->isValid()
                ) {
                    $preview = File::create([
                        'file' => Input::file('preview'),
                        'type' => File::TYPE_GARMENT_PREVIEW
                    ]);
                    if ($preview) {
                        $form->model->preview_file_id = $preview->id;
                        $form->model->save();
                    }
                }
                
                flash()->success(trans('messages.saved'));
                
                if ($create) {
                    return redirect(url('/admin/garment-groups/edit?modify='.$form->model->id));
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
            'title' => trans('labels.garment_groups'),
            'subtitle' => $subtitle,
            'form' => $formView
        ]);
    }
    
}
