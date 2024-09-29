<div class="page-content">
    <div class="dashboard">
        <div class="container">
            <div class="row">
                <aside class="col-md-2">
                    <ul class="nav nav-dashboard flex-column mb-3 mb-md-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link" id="tab-dashboard-link" data-toggle="tab" href="#tab-dashboard" role="tab" aria-controls="tab-dashboard" aria-selected="true">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-orders-link" data-toggle="tab" href="#tab-orders" role="tab" aria-controls="tab-orders" aria-selected="false">Orders</a>
                        </li>
                        <li class="nav-item">
                            {{-- <a class="nav-link" id="tab-downloads-link" data-toggle="tab" href="#tab-downloads" role="tab" aria-controls="tab-downloads" aria-selected="false">Downloads</a> --}}
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-address-link" data-toggle="tab" href="#tab-address" role="tab" aria-controls="tab-address" aria-selected="false">Adresses</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-account-link" data-toggle="tab" href="#tab-account" role="tab" aria-controls="tab-account" aria-selected="false">Account Details</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-password-link" data-toggle="tab" href="#tab-password" role="tab" aria-controls="tab-password" aria-selected="false">Change Password</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Sign Out</a>
                        </li>
                    </ul>
                </aside><!-- End .col-lg-3 -->

                <div class="col-md-10">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-dashboard" role="tabpanel" aria-labelledby="tab-dashboard-link">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="card card-dashboard">
                                        <div class="card-head">
                                            <h3 class="card-title p-2">Orders</h3><!-- End .card-title -->
                                        </div>
                                        <div class="card-body p-1">
                                            <p class="card-title">2</p><!-- End .card-title -->
                                        </div><!-- End .card-body -->
                                    </div><!-- End .card-dashboard -->
                                </div>
                                <div class="col-4">
                                    <div class="card card-dashboard">
                                        <div class="card-head">
                                            <h3 class="card-title p-2">Pending</h3><!-- End .card-title -->
                                        </div>
                                        <div class="card-body p-1">
                                            <p class="card-title">1</p><!-- End .card-title -->
                                        </div><!-- End .card-body -->
                                    </div><!-- End .card-dashboard -->
                                </div>
                                <div class="col-4">
                                    <div class="card card-dashboard">
                                        <div class="card-head">
                                            <h3 class="card-title p-2">Shipping</h3><!-- End .card-title -->
                                        </div>
                                        <div class="card-body p-1">
                                            <p class="card-title">1</p><!-- End .card-title -->
                                        </div><!-- End .card-body -->
                                    </div><!-- End .card-dashboard -->
                                </div>
                            </div>

                        </div><!-- .End .tab-pane -->

                        
                        {{-- <div class="tab-pane fade" id="tab-orders" role="tabpanel" aria-labelledby="tab-orders-link">
                            <p>No order has been made yet.</p>
                            <a href="{{ route('business.home', ['locale' => app()->getLocale()]) }}" class="btn btn-outline-primary-2"><span>GO SHOP</span><i class="icon-long-arrow-right"></i></a>
                        </div><!-- .End .tab-pane --> --}}
                        <div class="tab-pane fade" id="tab-orders" role="tabpanel" aria-labelledby="tab-orders-link">
                            @livewire('account.cart-list-one-livewire')
                        </div><!-- .End .tab-pane -->




                        {{-- <div class="tab-pane fade" id="tab-downloads" role="tabpanel" aria-labelledby="tab-downloads-link">
                            <p>No downloads available yet.</p>
                            <a href="category.html" class="btn btn-outline-primary-2"><span>GO SHOP</span><i class="icon-long-arrow-right"></i></a>
                        </div><!-- .End .tab-pane --> --}}

                        <div class="tab-pane fade" id="tab-address" role="tabpanel" aria-labelledby="tab-address-link">
                            <x-mains.components.account.address-one />
                        </div><!-- .End .tab-pane -->

                        <div class="tab-pane fade" id="tab-account" role="tabpanel" aria-labelledby="tab-account-link">
                            @livewire('account.detail-one')
                            
                        </div><!-- .End .tab-pane -->

                        <div class="tab-pane fade" id="tab-password" role="tabpanel" aria-labelledby="tab-password-link">
                            <x-mains.components.account.password-one/>
                        </div><!-- .End .tab-pane -->
                    </div>
                </div><!-- End .col-lg-9 -->
            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .dashboard -->
@push('tab-script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get the active tab from localStorage
        const activeTab = localStorage.getItem('activeTab') || 'tab-dashboard';

        // Hide all tab panes and remove active class from all links
        document.querySelectorAll('.tab-pane').forEach(tab => {
            tab.classList.remove('show', 'active');
        });
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });

        // Show the active tab based on localStorage
        const selectedTab = document.querySelector(`#${activeTab}`);
        if (selectedTab) {
            selectedTab.classList.add('show', 'active');
            const activeLink = document.querySelector(`a[href="#${activeTab}"]`);
            activeLink.classList.add('active');
        }

        // Add event listeners for tab links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function () {
                const selectedTabId = this.getAttribute('href').substring(1);
                localStorage.setItem('activeTab', selectedTabId);
            });
        });
    });
</script>

@endpush
</div><!-- End .page-content -->