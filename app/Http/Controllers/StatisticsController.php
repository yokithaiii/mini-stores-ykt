<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 30); // По умолчанию 30 дней
        $startDate = Carbon::now()->subDays($period);

        // Общая статистика
        $totalRevenue = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $startDate)
            ->sum('total_price');

        $totalOrders = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $startDate)
            ->count();

        $averageCheck = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Общие счетчики (не зависят от периода)
        $totalProducts = Product::count();
        $totalCategories = \App\Models\Category::count();
        $totalCustomers = \App\Models\Customer::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $confirmedOrders = Order::where('status', 'confirmed')->count();

        // Топ товар
        $topProduct = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->whereHas('order', function ($query) use ($startDate) {
                $query->where('status', '!=', 'cancelled')
                    ->where('created_at', '>=', $startDate);
            })
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->with('product')
            ->first();

        // График продаж по дням
        $salesByDay = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_price) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Топ-5 товаров
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->whereHas('order', function ($query) use ($startDate) {
                $query->where('status', '!=', 'cancelled')
                    ->where('created_at', '>=', $startDate);
            })
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->with('product')
            ->get();

        // Продажи по категориям
        $salesByCategory = OrderItem::select('products.category_id', 'categories.name as category_name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereHas('order', function ($query) use ($startDate) {
                $query->where('status', '!=', 'cancelled')
                    ->where('created_at', '>=', $startDate);
            })
            ->groupBy('products.category_id', 'categories.name')
            ->get();

        // Статусы заказов
        $ordersByStatus = Order::where('created_at', '>=', $startDate)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'summary' => [
                'total_revenue' => round($totalRevenue, 2),
                'total_orders' => $totalOrders,
                'average_check' => round($averageCheck, 2),
                'total_products' => $totalProducts,
                'total_categories' => $totalCategories,
                'total_customers' => $totalCustomers,
                'pending_orders' => $pendingOrders,
                'confirmed_orders' => $confirmedOrders,
                'top_product' => $topProduct ? [
                    'name' => $topProduct->product->name,
                    'sold' => $topProduct->total_sold,
                ] : null,
            ],
            'sales_by_day' => $salesByDay,
            'top_products' => $topProducts->map(function ($item) {
                return [
                    'name' => $item->product->name,
                    'sold' => $item->total_sold,
                ];
            }),
            'sales_by_category' => $salesByCategory->map(function ($item) {
                return [
                    'category' => $item->category_name ?? 'Без категории',
                    'sold' => $item->total_sold,
                ];
            }),
            'orders_by_status' => $ordersByStatus->map(function ($item) {
                $statusLabels = [
                    'pending' => 'В обработке',
                    'confirmed' => 'Подтвержден',
                    'cancelled' => 'Отменен',
                ];
                return [
                    'status' => $statusLabels[$item->status] ?? $item->status,
                    'count' => $item->count,
                ];
            }),
        ]);
    }
}
