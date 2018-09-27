<?php

namespace App\Http\Controllers\Admin\Traits;

use Illuminate\Http\Request;

use DataForm;

trait RapydControllerTrait
{

    /**
     * Get all availavle items
     */
    abstract public function all(Request $request);
    
    /**
     * Item add/edit form
     */
    abstract protected function form($form);
    
    abstract protected function getModelForAdd();
    abstract protected function getModelForEdit($id);
    abstract protected function getModelForDelete($id);
    
    /**
     * Add state
     */
    public function add(Request $request)
    {
        $form = DataForm::source($this->getModelForAdd());
        return $this->form($form);
    }
    
    public function edit(Request $request, $id)
    {
        $model = $this->getModelForEdit($id);
        
        if (!$model) {
            abort(404);
        }
        
        $form = DataForm::source($model);
        return $this->form($form);
    }
    
    public function delete(Request $request, $id)
    {
        $model = $this->getModelForDelete($id);
        
        if (!$model) {
            abort(404);
        }
        
        $form = DataForm::source($model);
        $form->action = 'delete';
        return $this->form($form);
    }
}
