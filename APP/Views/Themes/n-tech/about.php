{{ $this->view('Themes/' . $theme_name . '/breadcrumbs', $data) }}

<?php
$nConfig = array_merge([
    'about_heading' => 'Empowering Digital Evolution Through High-Performance Engineering',
    'about_lead' => 'N-Tech is a premier technology consulting firm specializing in high-throughput enterprise architectures, cloud migration, AI deployment, and robust cybersecurity.',
    'about_description' => 'Founded by veteran software architects and system engineers, our team partners with mid-market enterprises and tech innovators to turn complex challenges into competitive advantages.',
    'about_mission' => 'To deliver resilient, scalable, and secure software platforms that accelerate enterprise growth and safeguard mission-critical data.',
    'about_stat1_num' => '99.99%',
    'about_stat1_label' => 'Uptime & Service Reliability',
    'about_stat2_num' => '150+',
    'about_stat2_label' => 'Enterprise Deployments',
], $theme_config ?? []);
?>

<section class="py-5">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 wow animate__animated animate__fadeInLeft">
                <span class="text-primary fw-bold text-uppercase small">About N-Tech</span>
                <h2 class="display-6 fw-bold mb-3 heading-font">{{ $nConfig['about_heading'] }}</h2>
                <p class="lead text-muted mb-4">{{ $nConfig['about_lead'] }}</p>
                <p class="text-muted mb-4">{{ $nConfig['about_description'] }}</p>
                
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 border-start border-primary border-4">
                            <h3 class="fw-bold text-primary mb-0">{{ $nConfig['about_stat1_num'] }}</h3>
                            <small class="text-muted">{{ $nConfig['about_stat1_label'] }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 border-start border-primary border-4">
                            <h3 class="fw-bold text-primary mb-0">{{ $nConfig['about_stat2_num'] }}</h3>
                            <small class="text-muted">{{ $nConfig['about_stat2_label'] }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 wow animate__animated animate__fadeInRight">
                <div class="p-5 bg-dark text-white rounded-4 shadow-lg position-relative">
                    <h3 class="fw-bold mb-3 heading-font text-primary">Our Core Mission</h3>
                    <p class="text-white-50 mb-4">{{ $nConfig['about_mission'] }}</p>
                    
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-3"><i class="fa-solid fa-circle-check text-primary me-2"></i> Zero-Trust Security Paradigm</li>
                        <li class="mb-3"><i class="fa-solid fa-circle-check text-primary me-2"></i> Agile Engineering & Continuous Delivery</li>
                        <li class="mb-3"><i class="fa-solid fa-circle-check text-primary me-2"></i> 24/7 Global SLA Infrastructure</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
