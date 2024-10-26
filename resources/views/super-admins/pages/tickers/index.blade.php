
@extends('super-admins.layouts.layout')
@section('super-admin-content')
<div>
    @livewire('super-admins.ticker-livewire')
</div>
@endsection
@push('super_script')
<script>
    window.addEventListener('close-modal', event => {
        $('#updateTickerModal').modal('hide');
        $('#deleteTickerModal').modal('hide');
    })

    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Modal(document.getElementById('updateTickerModal'), {
            backdrop: 'static',  // Prevent closing when clicking outside
            keyboard: false      // Prevent closing on escape key press
        });
        new bootstrap.Modal(document.getElementById('deleteTickerModal'), {
            backdrop: 'static',  // Prevent closing when clicking outside
            keyboard: false      // Prevent closing on escape key press
        });
    });
</script>
@endpush