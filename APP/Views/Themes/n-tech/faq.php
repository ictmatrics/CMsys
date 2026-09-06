{{ $this->view('Themes/' . $theme_name . '/header', $data) }}
{{ $this->view('Themes/' . $theme_name . '/breadcrumbs', $data) }}

<section class="py-5">
    <div class="container py-4">
        <div class="text-center mb-5">
            <span class="text-primary fw-bold text-uppercase small">Knowledge Base</span>
            <h2 class="display-6 fw-bold mb-3 heading-font">Frequently Asked Questions</h2>
            <p class="text-muted" style="max-width: 600px; margin: 0 auto;">Find instant solutions and queries regarding our software engineering, consultation process, and support SLAs.</p>
        </div>

        <!-- Search Bar -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8">
                <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden">
                    <span class="input-group-text bg-white border-0 ps-4 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" id="faqSearchInput" class="form-control border-0 py-3" placeholder="Search questions or keywords...">
                </div>
            </div>
        </div>

        <!-- FAQ List Container -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="accordion accordion-flush" id="faqAccordion">
                    <?php 
                    $faqsList = $faqs ?? [];
                    if (!empty($faqsList)) {
                        $idx = 0;
                        foreach ($faqsList as $faq) {
                            $idx++;
                            $faqObj = is_object($faq) ? $faq : (object)$faq;
                    ?>
                            <div class="accordion-item mb-3 border rounded-3 overflow-hidden shadow-sm faq-item">
                                <h2 class="accordion-header" id="heading{{ $idx }}">
                                    <button class="accordion-button {{ $idx > 1 ? 'collapsed' : '' }} fw-bold py-3 px-4 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $idx }}">
                                        <i class="fa-solid fa-circle-question text-primary me-3"></i>{{ htmlspecialchars($faqObj->question, ENT_QUOTES, 'UTF-8') }}
                                        <?php if (!empty($faqObj->category)) { ?>
                                            <span class="badge bg-light text-primary ms-auto me-3 font-monospace small">{{ htmlspecialchars($faqObj->category, ENT_QUOTES, 'UTF-8') }}</span>
                                        <?php } ?>
                                    </button>
                                </h2>
                                <div id="collapse{{ $idx }}" class="accordion-collapse collapse {{ $idx === 1 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body px-4 py-3 bg-light text-secondary">
                                        {{ htmlspecialchars($faqObj->answer, ENT_QUOTES, 'UTF-8') }}
                                    </div>
                                </div>
                            </div>
                    <?php 
                        } 
                    } else { 
                    ?>
                        <div class="text-center text-muted py-5">
                            <i class="fa-solid fa-inbox fa-3x mb-3 text-secondary"></i>
                            <p>No FAQ items available right now.</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('faqSearchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const query = this.value.toLowerCase().trim();
                const faqItems = document.querySelectorAll('.faq-item');
                faqItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(query)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
    });
</script>

{{ $this->view('Themes/' . $theme_name . '/footer', $data) }}
