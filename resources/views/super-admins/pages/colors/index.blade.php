
@extends('super-admins.layouts.layout')
@section('super-admin-content')
<div>
    @livewire('super-admins.color-livewire')
</div>
@endsection
@push('super_script')
<script>
    window.addEventListener('close-modal', event => {
        $('#addColorModal').modal('hide');
        $('#updateColorModal').modal('hide');
        $('#deleteColorModal').modal('hide');
    })
</script>
@endpush