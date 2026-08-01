<?php

namespace App\Http\Controllers;

use App\Models\BandarPartner;
use App\Models\BandarProduct;
use App\Models\BandarTransaction;
use Illuminate\Http\Request;

class BandarController extends Controller
{
    // Dashboard Bandar
    public function index()
    {
        $products = BandarProduct::with('transactions')->get();
        $totalStock = $products->sum('stock');
        
        $totalInQty = BandarTransaction::where('type', 'in')->sum('quantity');
        $totalOutQty = BandarTransaction::where('type', 'out')->sum('quantity');
        $totalWastedQty = BandarTransaction::where('type', 'wasted')->sum('quantity');

        // Calculate stats per product
        $productStats = $products->map(function ($product) {
            $inQty = $product->transactions->where('type', 'in')->sum('quantity');
            $outQty = $product->transactions->where('type', 'out')->sum('quantity');
            $wastedQty = $product->transactions->where('type', 'wasted')->sum('quantity');

            return [
                'id' => $product->id,
                'name' => $product->name,
                'unit' => $product->unit,
                'stock' => $product->stock,
                'inQty' => $inQty,
                'outQty' => $outQty,
                'wastedQty' => $wastedQty,
            ];
        });
        
        return view('hydroponics.bandar.index', compact('products', 'productStats', 'totalStock', 'totalInQty', 'totalOutQty', 'totalWastedQty'));
    }

    // --- Partners ---
    public function partners()
    {
        $partners = BandarPartner::all();
        return view('hydroponics.bandar.partners', compact('partners'));
    }

    public function storePartner(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'type' => 'required|in:supplier,buyer',
            'phone' => 'nullable',
            'address' => 'nullable'
        ]);
        BandarPartner::create($validated);
        return redirect()->back()->with('success', 'Mitra berhasil ditambahkan.');
    }

    public function updatePartner(Request $request, $id)
    {
        $partner = BandarPartner::findOrFail($id);
        $partner->update($request->all());
        return redirect()->back()->with('success', 'Mitra berhasil diubah.');
    }

    public function destroyPartner($id)
    {
        BandarPartner::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Mitra berhasil dihapus.');
    }

    // --- Products ---
    public function products()
    {
        $products = BandarProduct::all();
        return view('hydroponics.bandar.products', compact('products'));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'unit' => 'required'
        ]);
        BandarProduct::create($validated);
        return redirect()->back()->with('success', 'Produk berhasil ditambahkan.');
    }

    public function updateProduct(Request $request, $id)
    {
        BandarProduct::findOrFail($id)->update($request->all());
        return redirect()->back()->with('success', 'Produk berhasil diubah.');
    }

    public function destroyProduct($id)
    {
        BandarProduct::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Produk berhasil dihapus.');
    }

    // --- Transactions ---
    public function transactions()
    {
        $transactions = BandarTransaction::with(['product', 'partner'])->orderBy('date', 'desc')->get();
        $products = BandarProduct::all();
        $partners = BandarPartner::all();
        return view('hydroponics.bandar.transactions', compact('transactions', 'products', 'partners'));
    }

    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:bandar_products,id',
            'partner_id' => 'nullable|exists:bandar_partners,id',
            'type' => 'required|in:in,out,wasted',
            'quantity' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable'
        ]);

        $validated['price'] = 0; // Default price to 0 since we only track physical stock

        $product = BandarProduct::findOrFail($validated['product_id']);
        
        if (in_array($validated['type'], ['out', 'wasted']) && $product->stock < $validated['quantity']) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi untuk dikeluarkan/dibuang.');
        }

        BandarTransaction::create($validated);

        if ($validated['type'] == 'in') {
            $product->stock += $validated['quantity'];
        } else {
            $product->stock -= $validated['quantity'];
        }
        $product->save();

        return redirect()->back()->with('success', 'Transaksi berhasil disimpan.');
    }

    public function destroyTransaction($id)
    {
        $transaction = BandarTransaction::findOrFail($id);
        $product = BandarProduct::find($transaction->product_id);
        
        if ($product) {
            // Revert stock
            if ($transaction->type == 'in') {
                $product->stock -= $transaction->quantity;
            } else {
                $product->stock += $transaction->quantity;
            }
            $product->save();
        }

        $transaction->delete();
        return redirect()->back()->with('success', 'Transaksi berhasil dibatalkan.');
    }
}
