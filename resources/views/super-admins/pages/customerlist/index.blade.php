
@extends('super-admins.layouts.layout')
@section('super-admin-content')
<div>
    @livewire('auth.customer-list-livewire')
</div>
@endsection
@push('super_script')
<script>
    window.addEventListener('close-modal', event => {
        $('#updateCustomerModal').modal('hide');
        $('#deleteCustomerModal').modal('hide');
    })

    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Modal(document.getElementById('updateCustomerModal'), {
            backdrop: 'static',  // Prevent closing when clicking outside
            keyboard: false      // Prevent closing on escape key press
        });
        new bootstrap.Modal(document.getElementById('deleteCustomerModal'), {
            backdrop: 'static',  // Prevent closing when clicking outside
            keyboard: false      // Prevent closing on escape key press
        });
    });
</script>
@endpush