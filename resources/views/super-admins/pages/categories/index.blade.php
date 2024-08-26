
@extends('super-admins.layouts.layout')
@section('super-admin-content')
<div>
    @livewire('super-admins.category-livewire')
</div>
@endsection
@push('super_script')
<script>
    window.addEventListener('close-modal', event => {
        $('#setCategoryModal').modal('hide');
        $('#updateCategoryModal').modal('hide');
        $('#deleteCategoryModal').modal('hide');
        $('#deleteSubCategoryModal').modal('hide');
    })
</script>
@endpush