
@extends('super-admins.layouts.layout')
@section('super-admin-content')
<div>
    @livewire('super-admins.capacity-livewire')
</div>
@endsection
@push('super_script')
<script>
    window.addEventListener('close-modal', event => {
        $('#addCapacityModal').modal('hide');
        $('#updateCapacityModal').modal('hide');
        $('#deleteCapacityModal').modal('hide');
    })
</script>
@endpush