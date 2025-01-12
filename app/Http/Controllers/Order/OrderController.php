<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        $user = Auth::user();
        $phone = $user->phone ?? $request->phone;

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User Not Found'],401);
        }
        $validator = Validator::make($request->all(),[
            // 'user_id' => 'required|string|exists:users,id',
            'quatation_id' => 'required|string|exists:quatations,id',
            'phone' => 'nullable|string',
            'image' => 'nullable|array',
            'video' => 'nullable|array'
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>false, 'message'=>$validator->errors()],401);
        }
        $order = Order::create([
            'user_id' => $user->id,
            'quatation_id' => $request->quatation_id,
            'phone' => $phone,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order created successfully',
            'order' => [
                'user_name' => $order->user->name,
                'phone' => $order->phone,
                'quotation_cost' => $order->quatation->cost,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ],
        ]);

    }
    public function orderList(Request $request, $id = null)
    {
        $perPage = $request->input('per_page', 10);

        $query = Order::with(['user', 'quatation']); // Corrected this part

        if ($id) {
            $query->where('user_id', $id);
        }

        $orders = $query->paginate($perPage);

        $data = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'user_id' => $order->user->id,
                'phone' => $order->phone,
                'name' => $order->user->name,
                'cost' => $order->quatation->cost ?? 'N/A',
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Orders List.',
            'data' => $data,
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'last_page' => $orders->lastPage(),
            ],
        ], 200);
    }

    public function orderDetails(Request $request,$id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json(['status'=>false, 'message'=>'Order Not Found']);
        }

        return response()->json(['status' => true, 'message'=>$order],200);
    }
}
