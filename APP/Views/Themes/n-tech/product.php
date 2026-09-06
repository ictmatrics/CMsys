{{ $this->view('Themes/' . $theme_name . '/header', $data) }}
{{ $this->view('Themes/' . $theme_name . '/breadcrumbs', $data) }}

<section class="py-5">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="text-primary fw-bold text-uppercase small">Our Offerings</span>
            <h2 class="display-6 fw-bold mb-3 heading-font">Enterprise Digital Products</h2>
            <p class="text-muted" style="max-width: 600px; margin: 0 auto;">Discover turnkey enterprise platforms, cloud modules, and developer tooling built by N-Tech.</p>
        </div>

        <div class="row g-4">
            <?php
            $productsList = $products ?? [];
            if (!empty($productsList)) {
                foreach ($productsList as $prod) {
                    $prodObj = is_object($prod) ? $prod : (object)$prod;
            ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden hover-top">
                            <div class="p-4 bg-light text-center border-bottom">
                                <i class="fa-solid fa-box-archive fa-4x text-primary mb-2"></i>
                            </div>
                            <div class="card-body p-4">
                                <span class="badge bg-primary bg-opacity-10 text-primary mb-2">Product</span>
                                <h4 class="fw-bold mb-2">
                                    <a href="{{ pathto('product/' . $prodObj->slug) }}" class="text-dark text-decoration-none">{{ htmlspecialchars($prodObj->title, ENT_QUOTES, 'UTF-8') }}</a>
                                </h4>
                                <p class="text-muted small mb-3">Enterprise-grade solution designed for security, high throughput, and developer ergonomics.</p>
                                <a href="{{ pathto('product/' . $prodObj->slug) }}" class="btn btn-outline-primary btn-sm rounded-pill fw-bold">View Product Details <i class="fa-solid fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
            <?php 
                } 
            } else { 
            ?>
                <!-- Fallback Product Showcase -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden p-4">
                        <i class="fa-solid fa-server fa-3x text-primary mb-3"></i>
                        <h4 class="fw-bold">N-Tech Cloud Director</h4>
                        <p class="text-muted mb-3">Centralized multi-cloud management dashboard with automated billing & orchestration.</p>
                        <a href="{{ pathto('contact') }}" class="btn btn-outline-primary rounded-pill btn-sm">Inquire Now</a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden p-4">
                        <i class="fa-solid fa-shield-virus fa-3x text-primary mb-3"></i>
                        <h4 class="fw-bold">N-Shield Security Suite</h4>
                        <p class="text-muted mb-3">Real-time threat detection, API security gateway, and automated compliance auditing.</p>
                        <a href="{{ pathto('contact') }}" class="btn btn-outline-primary rounded-pill btn-sm">Inquire Now</a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden p-4">
                        <i class="fa-solid fa-diagram-project fa-3x text-primary mb-3"></i>
                        <h4 class="fw-bold">N-Pipeline CI/CD Engine</h4>
                        <p class="text-muted mb-3">High-speed containerized build server with parallelized integration testing.</p>
                        <a href="{{ pathto('contact') }}" class="btn btn-outline-primary rounded-pill btn-sm">Inquire Now</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

{{ $this->view('Themes/' . $theme_name . '/footer', $data) }}
