// Custom Interactive Toast Notifier
function triggerToast(message) {
    const $toast = $('#globalToast');
    $('#toastMessage').text(message);
    $toast.stop(true, true).fadeIn(300).delay(2500).fadeOut(400);
}

// Copy current code panel to clipboard function
function copyActiveCode() {
    const $activePanel = $('.code-tab-panel.active code');
    const text = $activePanel.text();
    
    const $temp = $("<textarea>");
    $("body").append($temp);
    $temp.val(text).select();
    document.execCommand("copy");
    $temp.remove();

    triggerToast("Success! Code copied to clipboard.");
}

// Subscribe to newsletter dynamic simulation
function subscribeNewsletter() {
    const email = $('#newsletterEmail').val();
    if (!email || !email.includes('@')) {
        triggerToast("Please provide a valid email!");
        return;
    }
    triggerToast(`Success! Verified ${email} registered to database.`);
    $('#newsletterEmail').val('');
}

// Interactive Sandbox State Synchronizer Emulator
function syncSandbox(newVal) {
    $('#simValueDisplay').text(newVal);
    $('.simValueDisplayMirror').text(newVal);
    $('#simInputA').val(newVal);
    $('#simInputB').val(newVal);
    $('#simConsoleOut').text(`> LocalStorage.setItem('profileName', '${newVal}');`);
}

// jQuery Interactions on Document Ready
$(document).ready(function() {
    
    // 1. Prismatic Multi-Tab Switcher Controller
    $('.code-tab-btn').on('click', function() {
        const tabId = $(this).data('tab');
        $('.code-tab-btn').removeClass('active');
        $(this).addClass('active');

        $('.code-tab-panel').removeClass('active');
        $(`#${tabId}`).addClass('active');
    });

    // 2. Local Storage Sandbox triggers
    $('#simInputA').on('keyup input', function() {
        syncSandbox($(this).val());
    });

    // 3. Quiz assessment checker
    $('.quiz-option').on('click', function() {
        const answer = $(this).data('answer');
        $('.quiz-option').removeClass('correct incorrect');
        $('.quiz-option i').removeClass('fa-circle-check fa-circle-xmark').addClass('fa-regular fa-circle');

        if (answer === 'correct') {
            $(this).addClass('correct');
            $(this).find('i').removeClass('fa-regular fa-circle').addClass('fa-solid fa-circle-check');
            $('#quizFeedback').removeClass('d-none').fadeIn();
            triggerToast("Grade check: 100% correct!");
        } else {
            $(this).addClass('incorrect');
            $(this).find('i').removeClass('fa-regular fa-circle').addClass('fa-solid fa-circle-xmark');
            $('#quizFeedback').addClass('d-none');
            triggerToast("Incorrect. Let's try again!");
        }
    });

    // 6. Reading Progress & Back to Top Scroll Actions
    const sections = $('article.article-body section');
    const $tocLinks = $('.toc-link');

    $(window).on('scroll', function() {
        const scrollTop = $(this).scrollTop();
        const docHeight = $(document).height() - $(window).height();
        
        // Progress Bar
        if ($('#readingProgress').length > 0 && docHeight > 0) {
            const readPercentage = (scrollTop / docHeight) * 100;
            $('#readingProgress').css('width', `${readPercentage}%`);
        }

        // Back to Top button fade
        if (scrollTop > 300) {
            $('#backToTop').css('display', 'flex').addClass('animate__animated animate__fadeInUp');
        } else {
            $('#backToTop').fadeOut();
        }

        // Active section spy for Table of Contents
        if (sections.length > 0) {
            let activeId = '';
            sections.each(function() {
                const sectionTop = $(this).offset().top - 120;
                if (scrollTop >= sectionTop) {
                    activeId = $(this).attr('id');
                }
            });

            if (activeId) {
                $tocLinks.removeClass('active');
                $(`.toc-link[href="#${activeId}"]`).addClass('active');
            }
        }
    });

    $('#backToTop').on('click', function() {
        $('html, body').animate({ scrollTop: 0 }, 500);
        triggerToast("Returned to head container.");
    });

});
