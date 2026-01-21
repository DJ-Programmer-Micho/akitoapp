{{-- resources/views/super-admins/pages/phenixreport/index.blade.php --}}
@extends('super-admins.layouts.layout')
@section('super-admin-content')
<div>
    @livewire('super-admins.phenix-report-livewire')
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