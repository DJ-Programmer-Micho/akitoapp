
@extends('super-admins.layouts.layout')
@section('super-admin-content')
<div>
    @livewire('super-admins.setting.logo-livewire')
</div>
@endsection
@push('super_script')
<script>
    window.addEventListener('close-modal', event => {
        $('#modal_logo_image').modal('hide');
        $('#modal_logo_image').modal('hide');
    })
</script>
@endpush