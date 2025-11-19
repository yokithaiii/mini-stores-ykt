<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withCount('orders')
            ->with(['orders' => function ($query) {
                $query->latest()->limit(5);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($customers);
    }

    public function show(Customer $customer)
    {
        $customer->load(['orders.items.product']);
        $customer->loadCount('orders');
        
        return response()->json($customer);
    }
}
