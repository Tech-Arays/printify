<?php

namespace App\Http\Controllers;

use FractalManager;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use Traits\TransformersTrait;

    public function __construct() {}

    public $redirectIntent = null;
    public function redirectIntent($url)
    {
        $this->redirectIntent = $url;
    }

    public function returnSuccess($message = '')
    {
        if ($this->wantsJson()) {
            return response()->api($message);
        }
        else {
            if ($message) {
                flash()->success($message);
            }
            return ($this->redirectIntent ? redirect($this->redirectIntent) : redirect()->back());
        }
    }

    public function returnError($message = '')
    {
        if ($this->wantsJson()) {
            return response()->apiError($message);
        }
        else {
            if ($message) {
                flash()->error($message);
            }
            return ($this->redirectIntent ? redirect($this->redirectIntent) : redirect()->back());
        }
    }

    protected function wantsJson()
    {
        return (Request::ajax() || Request::wantsJson());
    }

    protected function serializeItem($model, $transformer)
    {
        return FractalManager::serializeItem($model, $transformer);
    }

    protected function serializeCollection($models, $transformer)
    {
        return FractalManager::serializeCollection($models, $transformer);
    }

    protected function serializePginator($query, $transformer, $perPage = 8)
    {
        $paginator = $query->paginate($perPage);
        return FractalManager::serializePaginator($paginator, $transformer);
    }
}
