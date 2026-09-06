{{ $this->view('Themes/' . $theme_name . '/header', $data) }}
{{ $this->view('Themes/' . $theme_name . '/breadcrumbs', $data) }}

<?php
$srvObj = isset($service) ? (is_object($service) ? $service : (object)$service) : null;
$srvTitle = $srvObj->title ?? 'Service Details';
$srvContent = $srvObj->content ?? 'Comprehensive engineering overview, execution roadmap, and service level agreements.';
?>

<section class="py-5">
    <div class="container py-4">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="p-4 bg-light rounded-4 text-center mb-4 border">
                    <i class="fa-solid fa-microchip fa-6x text-primary mb-3"></i>
                    <h2 class="fw-bold heading-font">{{ htmlspecialchars($srvTitle, ENT_QUOTES, 'UTF-8') }}</h2>
                </div>

                <div class="content-body mb-4">
                    <h4 class="fw-bold mb-3">Service Execution Strategy</h4>
                    <p class="text-muted lead">{{ $srvContent }}</p>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="p-4 border rounded-4 h-100 bg-light">
                            <h5 class="fw-bold text-primary mb-2">1. Discovery & Design</h5>
                            <p class="text-muted small mb-0">Detailed architecture blueprints, API specification, and technical risk assessment.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 border rounded-4 h-100 bg-light">
                            <h5 class="fw-bold text-primary mb-2">2. Engineering & QA</h5>
                            <p class="text-muted small mb-0">Agile sprint cycles, continuous automated integration tests, and security reviews.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 rounded-4 sticky-top" style="top: 100px;">
                    <h4 class="fw-bold mb-3">Engage This Service</h4>
                    <p class="text-muted small mb-4">Ready to accelerate your project with dedicated N-Tech engineers?</p>
                    <a href="{{ pathto('contact') }}" class="btn btn-primary btn-lg rounded-pill fw-bold w-100 mb-3">Book Service Consultation</a>
                    <a href="{{ pathto('services') }}" class="btn btn-outline-secondary rounded-pill w-100">All Services</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{ $this->view('Themes/' . $theme_name . '/footer', $data) }}
