
@php
    $allSessionData = session()->all();
@endphp


<section class="nav-header">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="cta-logo-section">
                            <div class="cta-logo-image">
                                <img src="images/cta-logo.png" alt="CTA Logo">
                            </div>
                            <div class="cta-logo-text">
                                <h3>CTA</h3>
                                <p>Management System</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="cta-right-section">
                            <div class="login-profile">
                                <div class="profile-icon">
                                    <img src="images/profile-icon.png" alt="Profile Icon">
                                </div>
                                <div class="profile-name">
                                    <h5>{{ session('user_name') }}</h5>
                                    @if(session('role') == '1')
                                    <p>Approver</p>
                                    @else
                                    <p>Developer</p>
                                    @endif
                                    <!-- <pre>
    {{ print_r($allSessionData, true) }}
</pre> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>