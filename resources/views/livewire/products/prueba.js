public function getImageAttribute($image)
{
    if($this->$image == null)

        return 'noimg.jpeg';

    if(file_exists('storage/categories/' .$image))
        return $image;
    else
        return 'noimg.jpeg';

}
}
ahora que tenemos el accesor vamos a la vista categories.blade
y bajo esto por los botones de  <img src="{{ asset('storage/categories/' .$category->image) }}" alt="imagen de ejemplo"

ponemos {{$category->image}}

para obtener la imagen a traves del propio registro desde el modelo
public function getImagenAttribute()
{
    if($this->$image == null)

        return 'noimg.jpeg';

    if(file_exists('storage/categories/' .$this->image))
    return $this->image;
else
    return 'noimg.jpeg';

}
