@extends('mains.layout.app')

@section('business-content')

<x-mains.components.shops.image-header-one />

<div class="mx-auto text-center bg-light my-3 p-3" 
style="max-width: 800px; border: 1px solid black"
>
    <h2>Complete Your Payment with FIB</h2>

    <!-- Payment Status Alert -->
    <div id="payment-status" class="alert alert-info mt-3">
        Waiting for payment...
    </div>

    <!-- QR Code Display -->
    <p><strong>Scan the QR Code:</strong></p>
    <div class="">
        <img src="{{ $qrCode }}" alt="FIB QR Code" class="my-3 mx-auto" width="250px">
        <h4>{{$readableCode}}</h4>
    </div>
    <p><b>or</b></p>
    <p>
        <b>If you have the First Iraqi Bank (FIB) app, tap the button above to complete your payment.</b>
    </p>
    <div class="col">
        <a href="{{ $personalAppLink }}" class="btn btn-primary btn-lg mt-3">Pay in App</a>
    </div>
    <div class="col">
        <button onclick="cancelUserPayment()" class="btn btn-danger btn-lg mt-1">Cancel Payment</button>
    </div>

</div>
<script>
    let paymentId = @json($paymentId);
    let startTime = Date.now(); 
    let maxWaitTime = 5 * 60 * 1000; 
console.log(paymentId)
    function checkPaymentStatus() {
        // console.log(`🔍 Checking payment status for: ${paymentId}`);

        fetch(`{{ route('payment.status', ['locale' => app()->getLocale(), 'paymentId' => '__PAYMENT_ID__', 'paymentMethod' => 'FIB' ])}}`.replace('__PAYMENT_ID__', paymentId))
            .then(response => response.json())
            .then(data => {
                // console.log(`📡 Payment Status Response for ${paymentId}:`, data);

                let currentTime = Date.now();
                let elapsedTime = currentTime - startTime;

                if (data.status === 'PAID') {
                    // console.log(`✅ Payment Successful! Redirecting...`);
                    document.getElementById('payment-status').innerHTML = 
                        '<div class="alert alert-success">✅ Payment Successful! Redirecting...</div>';
                    setTimeout(() => {
                        window.location.href = "{{ route('digit.payment.success', ['locale' => app()->getLocale()]) }}";
                    }, 3000);
                } else if (elapsedTime >= maxWaitTime) {
                    // console.log(`⏳ Timeout reached! Triggering payment cancellation...`);
                    document.getElementById('payment-status').innerHTML = 
                    '<div class="alert alert-danger">Time Out!</div>';
                    cancelPayment();
                    window.location.href = "{{ route('digit.payment.cancel', ['locale' => app()->getLocale()]) }}";
                } else {
                    // console.log(`🔄 Still waiting for payment... Retrying in 5 seconds.`);
                    setTimeout(checkPaymentStatus, 5000); // Retry every 5 seconds
                }
            })
            .catch(error => console.error(`❌ Error checking payment status:`, error));
    }

    function cancelPayment() {
    console.log(`🚨 Triggering cancelPayment() for Payment ID: ${paymentId}`);

    let url = `{{ route('time.payment.cancel', ['paymentId' => '__PAYMENT_ID__']) }}`.replace('__PAYMENT_ID__', paymentId);
    console.log(`📡 API Request URL: ${url}`);

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ reason: 'Time expired' })
    }).then(response => {
        console.log(`📡 HTTP Response Status: ${response.status}`);
        return response.text().then(text => { throw new Error(`HTTP Error: ${response.status}, Response: ${text}`); });
    }).catch(error => {
        console.error('❌ Fetch request failed 01:', error);
    });
}


    function cancelUserPayment() {
        // console.log('🚨 Triggering cancelPayment() for Payment ID:', paymentId);

        fetch(`{{ route('time.payment.cancel', ['paymentId' => '__PAYMENT_ID__']) }}`.replace('__PAYMENT_ID__', paymentId), {
            method: 'POST', // Ensure the method is POST
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}', // Include CSRF token for Laravel protection
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reason: 'User Cancelled' })
        }).then(response => {
            if (!response.ok) {
                throw new Error(`HTTP Error: ${response.status}`);
            }
            return response.json();
        }).then(data => {
            // console.log('🚫 Payment Cancellation Response:', data);
            document.getElementById('payment-status').innerHTML = 
                '<div class="alert alert-danger">Payment Cancelled By User</div>';
            setTimeout(() => {
                window.location.href = "{{ route('digit.payment.cancel', ['locale' => app()->getLocale()]) }}";
            }, 3000);
        })
        .catch(error => console.error('❌ Error updating payment status: aa', error));
    }

    setTimeout(() => {
        cancelPayment();
    }, maxWaitTime);

    setTimeout(checkPaymentStatus, 5000);
</script>


@endsection
