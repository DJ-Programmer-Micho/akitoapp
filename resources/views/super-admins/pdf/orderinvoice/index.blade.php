@extends('super-admins.layouts.layout')
@section('super-admin-content')
<div>
    @livewire('pdf.order-invoice-livewire',['id'=>$p_id])
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