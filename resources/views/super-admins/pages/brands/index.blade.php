
@extends('super-admins.layouts.layout')
@section('super-admin-content')
<div>
    @livewire('super-admins.brand-livewire')
</div>
@endsection
@push('super_script')
<script>
    window.addEventListener('close-modal', event => {
        $('#updateBrandModal').modal('hide');
        $('#deleteBrandModal').modal('hide');
    })
</script>
@endpush