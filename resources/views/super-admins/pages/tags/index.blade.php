
@extends('super-admins.layouts.layout')
@section('super-admin-content')
<div>
    @livewire('super-admins.tag-livewire')
</div>
@endsection
@push('super_script')
<script>
    window.addEventListener('close-modal', event => {
        $('#addTagModal').modal('hide');
        $('#updateTagModal').modal('hide');
        $('#deleteTagModal').modal('hide');
    })
</script>
@endpush