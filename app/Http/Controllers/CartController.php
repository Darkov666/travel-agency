<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CartController extends Controller
{
    private function getCart()
    {
        $user = Auth::user();
        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        } else {
            $sessionId = session()->getId();
            $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        }
        return $cart;
    }

    public function index()
    {
        $cart = $this->getCart();
        $cart = $this->getCart();
        $cart->load(['items.providerService.provider', 'items.providerService.service', 'items.providerService.zone']);

        return Inertia::render('Shop/Cart', [
            'cart' => $cart
        ]);
    }

    public function add(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Cart Add Request:', $request->all());
        $request->validate([
            'id' => 'required|exists:provider_services,id',
            'pax' => 'required|integer',
            'date' => 'nullable|date',
            'return_date' => 'nullable|date',
            'units' => 'nullable|integer',
            'price' => 'nullable|numeric'
        ]);

        $cart = $this->getCart();

        // Check if exact same config exists (date, pax, etc), otherwise add new line
        // Actually for travel, usually every addition is a distinct line item unless identical
        // Let's assume distinct line items for simplicity or check all fields

        $cart->items()->create([
            'provider_service_id' => $request->id,
            'quantity' => 1, // Usually 1 "booking" which contains X units
            'pax' => $request->pax,
            'date' => $request->date,
            'return_date' => $request->return_date,
            'units' => $request->units ?? 1,
            'price' => $request->price
        ]);

        return redirect('/')->with('success', 'Transfer added to cart.');
    }

    public function remove($cartItem)
    {
        \Illuminate\Support\Facades\Log::info("Cart Remove Request: ItemID: {$cartItem}");

        $item = CartItem::find($cartItem);

        if (!$item) {
            \Illuminate\Support\Facades\Log::warning("Cart Remove: Item {$cartItem} not found in DB.");
            return redirect()->back()->with('error', 'El artículo no existe.');
        }

        $cart = $this->getCart();

        // Verify ownership
        if ($item->cart_id !== $cart->id) {
            \Illuminate\Support\Facades\Log::warning("Cart Remove: Unauthorized. ItemCart: {$item->cart_id}, UserCart: {$cart->id}");
            return redirect()->back()->with('error', 'No autorizado.');
        }

        $item->delete();
        \Illuminate\Support\Facades\Log::info("Cart Remove: Success.");

        return redirect()->back()->with('success', 'Item eliminado del carrito.');
    }

    public function update($cartItem, Request $request)
    {
        \Illuminate\Support\Facades\Log::info("Cart Update Request: ItemID: {$cartItem}, Qty: {$request->quantity}");
        $request->validate(['quantity' => 'required|integer|min:1']);

        $item = CartItem::find($cartItem);

        if (!$item) {
            return redirect()->back()->with('error', 'El artículo no existe.');
        }

        $cart = $this->getCart();

        if ($item->cart_id !== $cart->id) {
            return redirect()->back()->with('error', 'No autorizado.');
        }

        $item->quantity = $request->quantity;
        $item->save();

        return redirect()->back()->with('success', 'Cantidad actualizada.');
    }
}
