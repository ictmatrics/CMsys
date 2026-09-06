{{ $this->view('Themes/' . $theme_name . '/header', $data) }}
{{ $this->view('Themes/' . $theme_name . '/breadcrumbs', $data) }}

<?php
$prodObj = isset($product) ? (is_object($product) ? $product : (object)$product) : null;
$prodTitle = $prodObj->title ?? 'Product Details';
$prodContent = $prodObj->content ?? 'Comprehensive enterprise product documentation and specifications.';
?>

<section class="py-5">
    <div class="container py-4">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="p-4 bg-light rounded-4 text-center mb-4 border">
                    <i class="fa-solid fa-box-open fa-6x text-primary mb-3"></i>
                    <h2 class="fw-bold heading-font">{{ htmlspecialchars($prodTitle, ENT_QUOTES, 'UTF-8') }}</h2>
                </div>

                <div class="content-body mb-4">
                    <h4 class="fw-bold mb-3">Product Overview</h4>
                    <p class="text-muted lead">{{ $prodContent }}</p>
                </div>

                <div class="p-4 bg-dark text-white rounded-4 mb-4">
                    <h5 class="fw-bold text-primary mb-3">Key Technical Capabilities</h5>
                    <ul class="list-unstyled text-white-50 mb-0">
                        <li class="mb-2"><i class="fa-solid fa-check text-primary me-2"></i> High Availability Cloud Deployment</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-primary me-2"></i> RESTful & GraphQL Native Integration</li>
                        <li class="mb-2"><i class="fa-solid fa-check text-primary me-2"></i> End-to-End Encryption & GDPR Compliance</li>
                        <li class="mb-0"><i class="fa-solid fa-check text-primary me-2"></i> Dedicated SLA Technical Support</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 rounded-4 sticky-top" style="top: 100px;">
                    <h4 class="fw-bold mb-3">Product Inquiry</h4>
                    <p class="text-muted small mb-4">Interested in deploying this product in your infrastructure? Request a demo today.</p>
                    <a href="{{ pathto('contact') }}" class="btn btn-primary btn-lg rounded-pill fw-bold w-100 mb-3">Request Product Demo</a>
                    <a href="{{ pathto('products') }}" class="btn btn-outline-secondary rounded-pill w-100">Back to Products</a>
                </div>
            </div>
        </div>
    </div>
</section>

{{ $this->view('Themes/' . $theme_name . '/footer', $data) }}
