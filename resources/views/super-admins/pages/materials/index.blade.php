
@extends('super-admins.layouts.layout')
@section('super-admin-content')
<div>
    @livewire('super-admins.material-livewire')
</div>
@endsection
@push('super_script')
<script>
    window.addEventListener('close-modal', event => {
        $('#addMaterialModal').modal('hide');
        $('#updateMaterialModal').modal('hide');
        $('#deleteMaterialModal').modal('hide');
    })
</script>
@endpush