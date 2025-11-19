<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Helpers\PhoneHelper;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Если запрос от клиента (есть заголовок X-Customer-Phone)
        $phone = $request->header('X-Customer-Phone');
        
        if ($phone) {
            $phoneNormalized = PhoneHelper::normalize($phone);
            $customer = \App\Models\Customer::where('phone', $phoneNormalized)->first();
            
            if (!$customer) {
                return response()->json(['error' => 'Клиент не найден'], 404);
            }
            
            $orders = Order::where('customer_id', $customer->id)
                ->with(['items.product'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Админ видит все заказы
            $orders = Order::with(['items.product', 'customer'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.size' => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            $totalPrice = 0;
            $orderItems = [];
            
            // Проверяем, авторизован ли клиент
            $customerId = null;
            $phone = $request->header('X-Customer-Phone');
            if ($phone) {
                $phoneNormalized = PhoneHelper::normalize($phone);
                $customer = \App\Models\Customer::where('phone', $phoneNormalized)->first();
                if ($customer) {
                    $customerId = $customer->id;
                }
            }

            // Проверяем наличие товаров и резервируем
            foreach ($validated['items'] as $item) {
                $product = Product::with('variants')->findOrFail($item['product_id']);

                // Проверяем наличие с учетом размера
                if (isset($item['size']) && $item['size']) {
                    // Товар с размером - проверяем вариант
                    $variant = $product->variants()->where('size', $item['size'])->first();
                    if (!$variant) {
                        throw new \Exception("Размер {$item['size']} не найден для товара: {$product->name}");
                    }
                    
                    if ($variant->quantity < $item['quantity']) {
                        throw new \Exception("Недостаточно товара: {$product->name} (размер {$item['size']}). Доступно: {$variant->quantity}");
                    }
                    
                    // Резервируем товар
                    $variant->quantity -= $item['quantity'];
                    $variant->save();
                } else {
                    // Товар без размера - проверяем общее количество
                    $availableQuantity = $product->quantity;
                    
                    if ($availableQuantity < $item['quantity']) {
                        throw new \Exception("Недостаточно товара: {$product->name}. Доступно: {$availableQuantity}");
                    }
                    
                    // Резервируем товар
                    $product->quantity -= $item['quantity'];
                    $product->save();
                }

                // Вычисляем цену со скидкой
                $finalPrice = $product->discounted_price;
                $itemPrice = $finalPrice * $item['quantity'];
                $totalPrice += $itemPrice;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'size' => $item['size'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $finalPrice, // Сохраняем цену со скидкой
                ];
            }

            // Создаем заказ
            $order = Order::create([
                'customer_id' => $customerId,
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            // Создаем позиции заказа
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'size' => $item['size'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Заказ успешно создан',
                'order' => $order->load('items.product'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function show(Order $order)
    {
        return response()->json($order->load('items.product'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $order->status;
            $newStatus = $validated['status'];

            // Если заказ отменяется, возвращаем товары на склад
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        if ($item->size) {
                            $variant = $product->variants()->where('size', $item->size)->first();
                            if ($variant) {
                                $variant->quantity += $item->quantity;
                                $variant->save();
                            }
                        } else {
                            $product->quantity += $item->quantity;
                            $product->save();
                        }
                    }
                }
            }

            $order->status = $newStatus;
            $order->save();

            DB::commit();

            return response()->json([
                'message' => 'Статус заказа обновлен',
                'order' => $order->load('items.product'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy(Order $order)
    {
        try {
            DB::beginTransaction();

            // Возвращаем товары на склад, если заказ не был подтвержден
            if ($order->status === 'pending') {
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        if ($item->size) {
                            $variant = $product->variants()->where('size', $item->size)->first();
                            if ($variant) {
                                $variant->quantity += $item->quantity;
                                $variant->save();
                            }
                        } else {
                            $product->quantity += $item->quantity;
                            $product->save();
                        }
                    }
                }
            }

            $order->delete();

            DB::commit();

            return response()->json([
                'message' => 'Заказ удален',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
