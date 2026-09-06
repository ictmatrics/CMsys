{{ $this->view('Themes/' . $theme_name . '/header', $data) }}
{{ $this->view('Themes/' . $theme_name . '/breadcrumbs', $data) }}

<?php
$nConfig = array_merge([
    'contact_email' => 'contact@ntech.com',
    'contact_phone' => '+1 (800) 555-0199',
    'contact_address' => '100 Innovation Way, Tech District, CA 94016',
    'contact_map_url' => 'https://maps.google.com/maps?q=California&t=&z=13&ie=UTF8&iwloc=&output=embed',
], $theme_config ?? []);
?>

<section class="py-5">
    <div class="container py-4">
        <div class="row g-5">
            <div class="col-lg-5">
                <span class="text-primary fw-bold text-uppercase small">Get In Touch</span>
                <h2 class="display-6 fw-bold mb-4 heading-font">We're Here to Help Your Business Grow</h2>
                <p class="text-muted mb-4">Have questions about our enterprise IT solutions or need custom software development? Reach out to our technical team today.</p>

                <div class="mb-4 d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle p-3 me-3">
                        <i class="fa-solid fa-location-dot fa-xl"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Our Location</h6>
                        <p class="text-muted mb-0">{{ $nConfig['contact_address'] }}</p>
                    </div>
                </div>

                <div class="mb-4 d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle p-3 me-3">
                        <i class="fa-solid fa-phone fa-xl"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Call Us Directly</h6>
                        <p class="text-muted mb-0">{{ $nConfig['contact_phone'] }}</p>
                    </div>
                </div>

                <div class="mb-4 d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle p-3 me-3">
                        <i class="fa-solid fa-envelope fa-xl"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Email Support</h6>
                        <p class="text-muted mb-0">{{ $nConfig['contact_email'] }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-lg p-4 rounded-4">
                    <h3 class="fw-bold mb-3 heading-font">Send Us A Message</h3>
                    <form action="#" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Full Name</label>
                                <input type="text" class="form-control py-2" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Email Address</label>
                                <input type="email" class="form-control py-2" placeholder="john@example.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label font-weight-bold">Subject</label>
                                <input type="text" class="form-control py-2" placeholder="Project Inquiry" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label font-weight-bold">Message</label>
                                <textarea class="form-control py-2" rows="5" placeholder="Tell us about your project requirements..." required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg px-4 rounded-pill fw-bold">Submit Message <i class="fa-solid fa-paper-plane ms-2"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-5 rounded-4 overflow-hidden shadow">
            <iframe src="{{ $nConfig['contact_map_url'] }}" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>

{{ $this->view('Themes/' . $theme_name . '/footer', $data) }}
