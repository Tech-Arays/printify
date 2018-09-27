<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

////use App\Models\Order;
//use App\Models\User;
//use App\Models\Product;
//use App\Models\Store;

class DashboardController extends AdminController
{
    
    /**
     * Dashboard
     */
    public function index(Request $request)
    {
        /*return view('admin.pages.dashboard.page', [
            'ordersCount' => Order::count(),
            'usersCount' => User::count(),
            'productsCount' => Product::count(),
            'storesCount' => Store::count()
        ]);*/
        return view('admin.pages.dashboard.index');
    }
    
}
