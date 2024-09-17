@extends('super-admins.auth.layout')
@section('super-admin-auth')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-5">
        <div class="card mt-4">
            <div class="card-body p-4 text-center">
                <script src="https://cdn.lordicon.com/lordicon.js"></script>

                <lord-icon src="https://cdn.lordicon.com/usownftb.json" trigger="loop" colors="primary:#cc0022,secondary:#66d7ee" style="width:250px;height:250px">
                </lord-icon>

                <div class="mt-4 pt-2">
                    <h5>Your account is Suspended</h5>
                    <p class="text-muted">Thank you for your Hard work</p>
                </div>
            </div>
            <!-- end card body -->
        </div>
        <!-- end card -->
    </div>
</div>
@endsection