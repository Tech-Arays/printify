<?php

namespace App\Widgets\Admin;

use Arrilot\Widgets\AbstractWidget;

class LeftNav extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        //

        return view('admin.widgets.left-nav', [
            'config' => $this->config,
        ]);
    }
}
