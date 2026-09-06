{{ $this->view('Themes/' . $theme_name . '/header', $data) }}
{{ $this->view('Themes/' . $theme_name . '/breadcrumbs', $data) }}

<section class="py-5">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="text-primary fw-bold text-uppercase small">What We Offer</span>
            <h2 class="display-6 fw-bold mb-3 heading-font">Our Engineering & Consulting Services</h2>
            <p class="text-muted" style="max-width: 600px; margin: 0 auto;">We provide end-to-end software development, infrastructure scaling, and security consulting tailored to enterprise needs.</p>
        </div>

        <div class="row g-4">
            <?php
            $servicesList = $services ?? [];
            if (!empty($servicesList)) {
                foreach ($servicesList as $srv) {
                    $srvObj = is_object($srv) ? $srv : (object)$srv;
            ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 border-0 shadow-sm rounded-4 p-4 hover-top">
                            <div class="mb-3 text-primary">
                                <i class="fa-solid fa-gears fa-3x"></i>
                            </div>
                            <h4 class="fw-bold mb-2">
                                <a href="{{ pathto('service/' . $srvObj->slug) }}" class="text-dark text-decoration-none">{{ htmlspecialchars($srvObj->title, ENT_QUOTES, 'UTF-8') }}</a>
                            </h4>
                            <p class="text-muted mb-3">Enterprise software engineering, architectural design, and system optimization.</p>
                            <a href="{{ pathto('service/' . $srvObj->slug) }}" class="fw-bold text-primary text-decoration-none">Read Service Details <i class="fa-solid fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
            <?php 
                } 
            } else { 
            ?>
                <!-- Default Services Catalog -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4">
                        <div class="mb-3 text-primary"><i class="fa-solid fa-laptop-code fa-3x"></i></div>
                        <h4 class="fw-bold">Custom Web Engineering</h4>
                        <p class="text-muted mb-3">Scalable web portals, CMS extensions, and high-performance API services.</p>
                        <a href="{{ pathto('contact') }}" class="fw-bold text-primary text-decoration-none">Inquire Service <i class="fa-solid fa-arrow-right ms-1"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4">
                        <div class="mb-3 text-primary"><i class="fa-solid fa-cloud-bolt fa-3x"></i></div>
                        <h4 class="fw-bold">Cloud Infrastructure & DevOps</h4>
                        <p class="text-muted mb-3">AWS, GCP, and Azure cloud migrations, infrastructure as code, and auto-scaling.</p>
                        <a href="{{ pathto('contact') }}" class="fw-bold text-primary text-decoration-none">Inquire Service <i class="fa-solid fa-arrow-right ms-1"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4">
                        <div class="mb-3 text-primary"><i class="fa-solid fa-shield-halved fa-3x"></i></div>
                        <h4 class="fw-bold">Cybersecurity & Compliance</h4>
                        <p class="text-muted mb-3">Penetration testing, vulnerability assessments, and regulatory compliance alignment.</p>
                        <a href="{{ pathto('contact') }}" class="fw-bold text-primary text-decoration-none">Inquire Service <i class="fa-solid fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

{{ $this->view('Themes/' . $theme_name . '/footer', $data) }}
