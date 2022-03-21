<?php

namespace App\Http\Livewire;

use App\Models\Denomination;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use DB;
use Livewire\Component;

class PosController extends Component
{
    public $total, $itemQuantity, $efectivo, $change;

    public function mount()
    {
        $this->efectivo = 0;
        $this->change = 0;
        $this->total = Cart::getTotal();
        $this->itemQuantity = Cart::getTotalQuanity();
    }

    public function render()

    {

        return view('livewire.pos.component', [
            'denominations' => Denomination::orderBy('value', 'desc')->get(),
            'cart' => Cart::getContent()->sortBy('name')
        ])
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function Acash($value)
    {
        $this->efectivo += ($value == 0 ? $this->total : $value);
        $this->change = $this->efectivo - $this->total;
    }

    protected $listeners = [
        'scan-code' => 'scanCode',
        'removeItem' => 'removeItem',
        'clearCart' => 'clearCart',
        'saveSale' => 'saveSale'
    ];

    public function ScanCode($barcode, $cant = 1)
    {
        $product = Product::where('barcode', $barcpde)->first();
        if ($product == null || empty($empty)) {
            $this->emit('scan-notfound', 'Producto no encontrado');
        } else {
            if ($this->InCart($product->id)) {
                $this->increasyQty($product->id);
                return;
            }

            if ($product->stock < 1) {
                $this->emit('no-stock', 'Stock insufficient:/');
                return;
            }


            Cart::add($product->id, $product->name, $product->price, $cant, $product->image);
            $this->total = Cart::getTotal();
            $this->emit('scan-ok', 'Producto agregado');
        }
    }

    public function InCart($productId)
        /** obtener el id del producto en el carrito */
    {
        $exist = Cart::get($productId);
        if ($exist)

            return true;
        else
            return false;
    }

    /**actualizar existencia del producto en el carrito*/

    public function increasyQty($productId)
    {
        $exist = Cart::get($productId, $cant = 1);
        {
            $title = '';
            $product = Product::find($productId);
            $exist = Cart::get($productId);
            if ($exist)
                $title = 'Cantidad actualizada';
            else
                $title = 'Producto agregado';
            if ($exist) {
                if ($product->stock < ($cant + $exist->quantity)) /**si las existencia producto son menos a la suma de la cantidad que se
                 * quiere agregar mas lo que ya hay en el carrito*/ {
                    $this->emit('no-stock', 'Stock insuficiente:/');
                    return;
                }
            }
            Cart:
            add($product->id, $product->name, $product->price, $cant, $product->image);

            /**actualizar el total del carrito*/

            $this->total = Cart::getTotal();
            $this->itemQuantity = Cart::getTotalQuanity();

            $this->emit('scan-ok', $title);
        }
    }

    /**funcion que actualiza por completo el carrito*/
    public function updateQuantity($productId, $cant = 1)
    {
        $title = '';
        $product = Product::find($productId);
        $exist = Cart::get($productId);
        if ($exist)
            $title = 'Cantidad actualizada';
        else
            $title = 'Producto agregado';
        if ($exist) {
            /** si la existencia del producto en stock es menor a la cantidad */
            if ($product->stock < $cant) {
                $this->emit('no-stock', 'Stock insuficiente:/');
                return;
            }
        }

        $this->removeItem($productId);

        if ($cant > 0) {
            Cart::add($product->id, $product->name, $product->price, $cant, $product->image);
            $this->total = Cart::getTotal();
            $this->itemQuantity = Cart::getTotalQuanity();
            $this->emit('scan-ok', $title);
        } else
            $this->emit('scan-error', 'La cantidad debe ser mayor a 0');
    }

    public function removeItem($productId)
    {
        Cart::remove($productId);
        $this->total = Cart::getTotal();
        $this->itemQuantity = Cart::getTotalQuanity();
        $this->emit('scan-ok', 'Producto eliminado');
    }

    public function decreaseQty($productId)
    {
        $item = Cart::get($productId);
        Cart::remove($productId);

        $newQty = ($item->quantity) - 1;
        if ($newQty > 0) {
            Cart::add($item->id, $item->name, $item->price, $newQty, $item->image);
            $this->total = Cart::getTotal();
            $this->itemQuantity = Cart::getTotalQuanity();
            $this->emit('scan-ok', 'Cantidad actualizada');


        }
    }

        /**metodo para limpiar carrito*/

        public function clearCart()
    {
        Cart::clear();
        $this->efectivo = 0;
        $this->change = 0;
        $this->total = 0;
        $this->total = Cart::getTotal();
        $this->itemQuantity = Cart::getTotalQuanity();
        $this->emit('scan-ok', 'Carrito vaciado');
    }

    /**metodo para guardar la venta*/

    public function saveSale()
    {
        if($this->total <=0)
            {
                $this->emit('sale-error', 'AGREGA PRODUCTOS AL CARRITO');
                return;
            }

        if($this->efectivo <=0)
        {
            $this->emit('sale-error', 'INGRESA EL EFECTIVO');
            return;
        }
            if($this->total > $this->efectivo)
            {
                $this->emit('sale-error', 'EFECTIVO DEBE SER MAYOR O IGUAL AL TOTAL');
                return;
            }

            DB::beginTransaction();
            try {
                $sale = Sale::create([
                    'total' => $this->total,
                    'items' => $this->itemQuantity,
                    'cash' => $this->efectivo,
                    'change' => $this->change,
                    'user_id' => Auth()->user()->id
                ]);
                /**validamos si se guardo la venta*/
                if($sale)
                {
                    $items = Cart::getContent();
                    foreach($items as $item)
                    {
                       /** saleDetail nombre del modelo */
                        saleDetail::create([
                            'price' => $item->price,
                            'quantity' => $item->quantity,
                            'product_id' => $item->id,
                            'sale_' => $sale->id,
                        ]);

                        //update stock
                        $product = Product::find($item->id);
                        $product->stock = $product->stock - $item->quantity;
                        $product->save();
                    }
                }
                //confirmacion  de la transaccion en la BD
                DB::commit();
                Cart::clear();
                $this->efectivo = 0;
                $this->change = 0;
                $this->total = Cart::getTotal();
                $this->itemQuantity = Cart::getTotalQuanity();
                $this->emit('sale-ok', 'Venta registrada con exito!');
                $this->emit('print-ticket', $sale->id);

            } catch (\Exception $e) {
                DB::rollback();
                $this->emit('sale-error', $e->getMessage());
                return;
            }
        }

        /**Metodo para imprimir el ticket*/

    public function printTicket($saleId)
        {
            return Redirect::to("print://$sale->id");
        }

}

