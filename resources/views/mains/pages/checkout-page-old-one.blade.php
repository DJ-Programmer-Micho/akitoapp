@extends('mains.layout.app')
@section('business-content')
<div class="main">
    <x-mains.components.account.image-header-one />
    <x-mains.components.account.nav-one />
    @livewire('cart.checkout-list-old-one-livewire', ['orderId' => $order->id])
</div>
@endsection