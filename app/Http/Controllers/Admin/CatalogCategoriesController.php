<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use DataForm;
use Input;

use App\Http\Controllers\Admin\AdminController;
use App\Models\CatalogCategory;
use App\Models\File;

class CatalogCategoriesController extends AdminController
{
    use \App\Http\Controllers\Admin\Traits\RapydControllerTrait;

    public function edit(Request $request)
    {
        if (Input::get('delete')) {
            return $this->delete($request);
        }

        $id = Input::get('modify');

        $model = $this->getModelForEdit($id);

        if (!$model) {
            abort(404);
        }

        $form = DataForm::source($model);
        return $this->form($form);
    }

    public function delete(Request $request)
    {
        $id = Input::get('delete');
        $model = $this->getModelForDelete($id);

        if (!$model) {
            abort(404);
        }

        $form = DataForm::source($model);
        $form->action = 'delete';
        return $this->form($form);
    }

    protected function getModelForAdd()
    {
        
        return new CatalogCategory();
        
    }

    protected function getModelForEdit($id)
    {
        return CatalogCategory::find($id);
    }

    protected function getModelForDelete($id)
    {
        return CatalogCategory::find($id);
    }

    public function all() {
        $model = CatalogCategory::getDefaultRoot();
        $tree = \DataTree::source($model);
        $tree->add('name');
        $tree->edit('/admin/catalog-categories/edit', 'edit', 'modify'); // modify|delete
        $tree->submit(trans('actions.save_order'));

        return view('admin.pages.default.tree', [
            'title' => trans('labels.categories'),
            'tree' => $tree->build()
        ]);
    }

    private function recursiveReordering($categories, $root)
    {
        $currentCategory = CatalogCategory::find($categories[0]['id']);

        try {
            $currentCategory->makeFirstChildOf($root);
        } catch(\Exception $e) {
            \Log::error($e);
            \Bugsnag::notifyException($e);
        }

        foreach(array_slice($categories, 1) as $category) {
            $previousCategory = $currentCategory;
            $currentCategory = CatalogCategory::find($category['id']);
            $currentCategory->moveToRightOf($previousCategory);

            if (!empty($category['children'])) {
                $this->recursiveReordering($category['children'], $currentCategory);
            }
        }
    }

    public function saveOrder(Request $request)
    {
        $root = CatalogCategory::getDefaultRoot();
        $categories = json_decode($request->get('items'), JSON_OBJECT_AS_ARRAY);
        $root->makeTree($categories);
        
        if (!empty($categories)) {
            $this->recursiveReordering($categories, $root);
        }

        flash()->success(trans('messages.saved'));
        return redirect(url('/admin/catalog-categories'));
    }

    /**
     * Add/edit form
     */
    protected function form($form) {

        $categories = CatalogCategory::getNestedList('name', null, '&nbsp;&nbsp;&nbsp;&nbsp;');

        $form->attr('enctype', 'multipart/form-data');

        $form
            ->add('parent_id', trans('labels.parent_category'), 'select')
            ->options($categories)
            ->rule('required');

        $form->add('name', trans('labels.name'), 'text')
            ->rule('required');

        $form->add('slug', trans('labels.slug'), 'text')
            ->rule('required');

        $form->add('prepaid_amount', trans('labels.prepaid_amount'), 'text');

        $form->add('catalogAttributes.name', trans('labels.attributes'), 'tags');

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

        $form->link('/admin/catalog-categories', trans('actions.back_to_categories'));
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
                        'file_content_type' => File::TYPE_CATEGORY_PREVIEW
                    ]);
                    if ($preview) {
                        $form->model->preview_file_id = $preview->id;
                        $form->model->save();
                    }
                }

                if ($parent = CatalogCategory::find(Input::get('parent_id'))) {
                    $form->model->makeChildOf($parent);
                }
                else {
                    $form->model->makeChildOf(CatalogCategory::getDefaultRoot());
                }

                flash()->success(trans('messages.saved'));

                if ($create) {
                    return redirect(url('/admin/catalog-categories/edit?modify='.$form->model->id));
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
            'title' => trans('labels.categories'),
            'subtitle' => $subtitle,
            'form' => $formView
        ]);
    }

}
