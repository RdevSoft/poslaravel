<div>
    <style></style>

    <div class="row layout-top-spacing">

        <div class="col-sm-12 col-md-8">
            <!--Detalles-->
            @include('livewire.pos.partials.detail')
        </div>

        <div class="col-sm-12 col-md-4">
            <!--TOTAl-->
        @include('livewire.pos.partials.total')

        <!--Denomination/Coins-->
            @include('livewire.pos.partials.coins')
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/keypress.js') }}"></script>
<script src="{{ asset('assets/js/onscan.js') }}"></script>

<script>

</script>

@include('livewire.pos.scripts.shortcuts')
@include('livewire.pos.scripts.events')
@include('livewire.pos.scripts.general')
