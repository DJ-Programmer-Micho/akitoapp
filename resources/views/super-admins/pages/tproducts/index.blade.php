@extends('super-admins.layouts.layout')
@section('super-admin-content')
<div>
    @livewire('super-admins.t-product-livewire')
</div>
@endsection
@push('super_script')
<script>
    window.addEventListener('close-modal', event => {
        $('#deleteProductModal').modal('hide');
    })
</script>
@endpush