<script>
    var listener = new window.keypress.Listener();

    listener.simple_combo("f9", function(){
        livewire.emit('saveSale')
    })

    listener.simple_combo("f8", function(){
        document.getElementById('cash').value = '' /**limpia la caja de texta*/
        document.getElementById('cash').focus() /**pone el foco en la caja de texto*/
    })

    listener.simple_combo("f4", function(){
        var total = parseFloat(document.getElementById('hiddenTotal')) /**obtiene el valor del total obtenido de total.blade*/
        if(total > 0){
            Confirm(0, 'clearCart', 'Seguro de eliminar el carrito?')
        }else{
            noty('AGREGA PRODUCTOS A LA VENTA')
        }
    })

</script>
