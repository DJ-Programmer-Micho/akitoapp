
@extends('super-admins.layouts.layout')
@section('super-admin-content')
<div>
    @livewire('driver.driver-task-livewire')
</div>
@endsection
@push('super_script')
<script>
    window.addEventListener('close-modal', event => {
        $('#addSizeModal').modal('hide');
        $('#updateSizeModal').modal('hide');
        $('#deleteSizeModal').modal('hide');
    })
</script>
@endpush