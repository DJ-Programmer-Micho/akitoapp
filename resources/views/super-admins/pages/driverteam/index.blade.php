
@extends('super-admins.layouts.layout')
@section('super-admin-content')
<div>
    @livewire('auth.driver-team-livewire')
</div>
@endsection
@push('super_script')
<script>
    window.addEventListener('close-modal', event => {
        $('#addTeamModal').modal('hide');
        $('#updateTeamModal').modal('hide');
    })

    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Modal(document.getElementById('addTeamModal'), {
            backdrop: 'static',  // Prevent closing when clicking outside
            keyboard: false      // Prevent closing on escape key press
        });
        new bootstrap.Modal(document.getElementById('updateTeamModal'), {
            backdrop: 'static',  // Prevent closing when clicking outside
            keyboard: false      // Prevent closing on escape key press
        });
    });
</script>
@endpush