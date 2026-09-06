{{ $this->view('Themes/' . $theme_name . '/header', $data) }}

<section class="py-5 bg-light text-center min-vh-100 d-flex align-items-center">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <h1 class="display-1 fw-bold text-primary mb-0 heading-font" style="font-size: 120px;">404</h1>
                <h3 class="fw-bold mb-3 heading-font">Page Not Found</h3>
                <p class="text-muted mb-4">The requested page or enterprise asset could not be located on the server. Please check the URL or return to the homepage.</p>
                <a href="{{ pathto('') }}" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold"><i class="fa-solid fa-house me-2"></i>Return To Home</a>
            </div>
        </div>
    </div>
</section>

{{ $this->view('Themes/' . $theme_name . '/footer', $data) }}
