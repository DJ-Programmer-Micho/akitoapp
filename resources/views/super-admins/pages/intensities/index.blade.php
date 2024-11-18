
@extends('super-admins.layouts.layout')
@section('super-admin-content')
<div>
    @livewire('super-admins.intensity-livewire')
</div>
@endsection
@push('super_script')
<script>
    window.addEventListener('close-modal', event => {
        $('#addIntensityModal').modal('hide');
        $('#updateIntensityModal').modal('hide');
        $('#deleteIntensityModal').modal('hide');
    })
    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Modal(document.getElementById('addIntensityModal'), {
            backdrop: 'static',  // Prevent closing when clicking outside
            keyboard: false      // Prevent closing on escape key press
        });
        new bootstrap.Modal(document.getElementById('updateIntensityModal'), {
            backdrop: 'static',  // Prevent closing when clicking outside
            keyboard: false      // Prevent closing on escape key press
        });
        new bootstrap.Modal(document.getElementById('deleteIntensityModal'), {
            backdrop: 'static',  // Prevent closing when clicking outside
            keyboard: false      // Prevent closing on escape key press
        });
    });
</script>
@endpush