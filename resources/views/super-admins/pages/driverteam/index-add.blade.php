
@extends('super-admins.layouts.layout')
@section('super-admin-content')
    @livewire('auth.combine-driver-team-add-livewire')
@endsection
@push('super_script')
<script>
    // window.addEventListener('close-modal', event => {
    //     $('#updateUserModal').modal('hide');
    //     $('#deleteUserModal').modal('hide');
    // })

    // document.addEventListener('DOMContentLoaded', function () {
    //     new bootstrap.Modal(document.getElementById('updateUserModal'), {
    //         backdrop: 'static',  // Prevent closing when clicking outside
    //         keyboard: false      // Prevent closing on escape key press
    //     });
    //     new bootstrap.Modal(document.getElementById('deleteUserModal'), {
    //         backdrop: 'static',  // Prevent closing when clicking outside
    //         keyboard: false      // Prevent closing on escape key press
    //     });
    // });
</script>
@endpush