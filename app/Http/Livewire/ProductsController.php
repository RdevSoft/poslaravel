<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProductsController extends Component
{

    use WithPagination;
    use WithFileUploads;

    public $name, $barcode, $cost, $price, $stock, $alerts, $categoryid, $search, $image, $selected_id, $pageTitle, $componentName;
    private $pagination = 5;

    public function paginationView()
    {
        return 'vendor.livewire.bootstrap';
    }

    public function mount()
    {
        $this->pageTitle = 'Listado';
        $this->componentName = 'Productos';
        $this->categoryid = 'Elegir';
    }

    public function render()
    {
        if (strlen($this->search) > 0)
            $products = Product::join('categories as c', 'c.id', 'products.category_id')
                ->select('products.*', 'c.name as category')
                ->where('products.name', 'like', '%' . $this->search . '%')
                ->orWhere('products.barcode', 'like', '%' . $this->search . '%')
                ->orWhere('c.name', 'like', '%' . $this->search . '%')
                ->orderBy('products.name', 'asc')
                ->paginate($this->pagination);
        else
            $products = Product::join('categories as c', 'c.id', 'products.category_id')
                ->select('products.*', 'c.name as category')
                ->orderBy('products.name', 'asc')
                ->paginate($this->pagination);

        return view('livewire.products.component', [
            'data' => $products,
            'categories' => Category::orderBy('name', 'asc')->get()
        ])
            ->extends('layouts.theme.app')
            ->section('content');
    }

    public function Store()
    {
        $rules = [
            'name' => 'required|unique:products|min:3',
            'cost' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'alerts' => 'required',
            'categoryid' => 'required|not_in:Elegir'
        ];

        $messages = [
            'name.required' => 'Nofffmbre del producto requerido',
            'name.unique' => 'Ya existe un producto con ese nombre',
            'name.min' => 'El nombre del producto debe tener al menos 3 caracteres',
            'cost.required'      => 'Costo requerido',
            'price.required' => 'Precio requerido',
            'stock.required' => 'Stock requerido',
            'alerts.required' => 'Ingresa el valor minimo en existencias',
            'categoryid.not_in' => 'Selecciona un nombre de categoria diferente a Elegir',

        ];

        $this->validate($rules, $messages);

        //guardamos en una variable el producto que se va a registrar, con el metodo create de forma masiva registrar el producto, en este caso solo
        //sera uno, pero se puede registrar mas de uno, parte izquierda valores de la tabla, parte derecha los valores que se van a guardar
        $product = Product::create([
            'name' => $this->name, //$this->name es el nombre del campo que se esta enviando desde el formulario, el valor de name esta en la propiedad publica name
            'cost' => $this->cost,
            'price' => $this->price,
            'barcode' => $this->barcode,
            'stock' => $this->stock,
            'alerts' => $this->alerts,
            'category_id' => $this->categoryid //category_id es por el nombre de la columna de la tabla, categoryid es el nombre de la propiedad publica de esta clase
        ]);
        //validamos la imagen
        if ($this->image) {
            $customFileName = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('public/products', $customFileName);
            $product->image = $customFileName;
            $product->save();
        }

        $this->resetUI();
        $this->emit('product-added', 'Producto Registrado');
    }


    public function Edit(Product $product)
    {
        $this->selected_id = $product->id;
        $this->name = $product->name;
        $this->barcode = $product->barcode;
        $this->cost = $product->cost;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->alerts = $product->alerts;
        $this->categoryid = $product->category_id;
        $this->image = null;

        //una vez que seateamos la inforamcion emitimos un evento para que el fron reciba  y se dispare la ventana modal
        $this->emit('modal-show', 'Show Modal');
    }

    public function Update()
    {
        $rules = [
            'name' => "required|min:3|unique:products,name,{$this->selected_id}", //cambiamos de lugar el min, espicifcamos que validaremos por el nombre
            //cambiamos las comillas simples a dobles
            'cost' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'alerts' => 'required',
            'categoryid' => 'required|not_in:Elegir'
        ];

        $messages = [ //aca se deja igual
            'name.required' => 'Nombre del producto requerido',
            'name.unique' => 'Ya existe un producto con ese nombre',
            'name.min' => 'El nombre del producto debe tener al menos 3 caracteres',
            'cost.required'      => 'Costo requerido',
            'price.required' => 'Precio requerido',
            'stock.required' => 'Stock requerido',
            'alerts.required' => 'Ingresa el valor minimo en existencias',
            'categoryid.not_in' => 'Selecciona un nombre de categoria diferente a Elegir',

        ];

        $this->validate($rules, $messages);

        $product = Product::find($this->selected_id); //buscamos primero el producto, almacenamos el id en la variable $product

        $product->update([ //cambiamos a update, modificando
            'name' => $this->name,
            'cost' => $this->cost,
            'price' => $this->price,
            'barcode' => $this->barcode,
            'stock' => $this->stock,
            'alerts' => $this->alerts,
            'category_id' => $this->categoryid
        ]);
        //validamos la imagen
        if ($this->image) {
            $customFileName = uniqid() . '_.' . $this->image->extension();
            $this->image->storeAs('public/products', $customFileName);
            $imageTemp = $product->image; //guardamos de manera temporal la imagen anterior por si el usuario selecciona una nueva
            $product->image = $customFileName;
            $product->save();

            if ($imageTemp != null) //validamos si la imagen temp es diferente a null,
            {
                if (file_exists('storage/products/' . $imageTemp)) //validamos si existe la imagen fisicamente, y elminarla de forma real
                {
                    unlink('storage/products/' . $imageTemp); //eliminamos la imagen fisicamente
                }
            }
        }

        $this->resetUI();
        $this->emit('product-updated', 'Producto Actualizado');
    }

    public function resetUI() {
        $this->name ='';
		$this->barcode ='';
		$this->cost ='';
		$this->price ='';
		$this->stock ='';
		$this->alerts ='';
		$this->search ='';
		$this->categoryid ='Elegir';
		$this->image = null;
		$this->selected_id = 0;
    }

    protected $listeners=['deleteRow' => 'Destroy']; //este evento es ejecutado cuando se elimina una fila

    public function Destroy(Product $product)
    {
        $imageTemp = $product->image; //guardamos de manera temporal la imagen anterior por si el usuario selecciona una nueva
        $product->delete();//eliminamos el producto de la bd

        if ($imageTemp != null) //validamos si la imagen temp es diferente a null,
        {
            if (file_exists('storage/products/' . $imageTemp)) //validamos si existe la imagen fisicamente, y elminarla de forma real
            {
                unlink('storage/products/' . $imageTemp); //eliminamos la imagen fisicamente
            }
        }

        $this->resetUI();
        $this->emit('product-deleted', 'Producto Eliminado');
    }
}

