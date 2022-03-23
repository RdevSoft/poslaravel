<script>
    document.addEventListener('DOMContentLoaded', function() {

        window.livewire.on('scan-ok', msg => {
            noty(Msg)
        })

        window.livewire.on('scan-notfound', msg => {
            noty(Msg, 2)
        })

        window.livewire.on('no-stock', msg => {
            noty(Msg, 2)
        })

        {{--al escanear el producto se quita el foco de la caja de texto--}}
        window.livewire.on('sale-error', msg => {
            noty(Msg)
        })

        window.livewire.on('print-ticket', saleId =>{
            windows.open("print://" + saleId, '_blank')
        })

    });
</script>
