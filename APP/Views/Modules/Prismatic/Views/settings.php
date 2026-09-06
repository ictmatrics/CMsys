{{ $this->view('admin/layout/header', ['title' => $title]) }}

<?php
// Extract settings from the config array
$settings = [];
if (!empty($config['custom_css_code'])) {
    $settings = json_decode($config['custom_css_code'], true) ?: [];
}

$pTheme = $settings['theme'] ?? 'prism-tomorrow';
$pBgColor = $settings['bg_color'] ?? '#2d2d2d';
$pFontSize = $settings['font_size'] ?? '14px';
$pBorderRadius = $settings['border_radius'] ?? '8px';
$pPadding = $settings['padding'] ?? '16px';
$pLineNumbers = (int)($settings['line_numbers'] ?? 0);
$pEnableCopy = (int)($settings['enable_copy'] ?? 1);
$pColorPreview = (int)($settings['color_preview'] ?? 1);
?>

<!-- Load Prism CSS dynamically based on active settings theme -->
<link id="prism_theme_css" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/<?php echo $pTheme === 'prism-tomorrow' ? 'prism-tomorrow' : ($pTheme === 'prism-default' ? 'prism' : ($pTheme === 'prism-solarizedlight' ? 'prism-solarized-light' : $pTheme)); ?>.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.css" rel="stylesheet" />

<!-- Load Prism JS and Plugins -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js" data-manual></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.js"></script>

<style id="preview_dynamic_styles">
    /* Styling overrides for live preview alignment */
    #preview_pre.line-numbers {
        padding-left: 3.8em !important;
    }
    #preview_pre .line-numbers-rows {
        border-right: 1px solid rgba(255, 255, 255, 0.15) !important;
        padding: <?php echo htmlspecialchars($pPadding, ENT_QUOTES, 'UTF-8'); ?> 0 !important;
        left: 10px !important;
    }
    #preview_pre .line-numbers-rows > span::before {
        color: rgba(255, 255, 255, 0.4) !important;
    }
</style>

<div class="container-fluid px-4 py-3">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">{{ $title }}</h1>
            <p class="text-muted mb-0 small fw-medium">Modify the settings for the Prismatic Syntax Highlighter module.</p>
        </div>
        <div>
            <?php redirectto('admin/modules', 'Back to Modules', 'btn btn-secondary px-4 py-2 fw-semibold shadow-sm'); ?>
        </div>
    </div>

    <!-- Customization Form -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                    <h5 class="mb-0 text-dark font-weight-bold">
                        <i class="fa-solid fa-sliders text-primary me-2"></i> Prismatic Configuration
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ pathto('admin/theme/customize/' . $theme_name) }}" method="POST">
                        <!-- Theme Selection -->
                        <div class="mb-3">
                            <label for="p_theme" class="form-label font-weight-bold text-dark">Prism JS Theme</label>
                            <select id="p_theme" name="theme" class="form-select rounded-3">
                                <option value="prism-tomorrow" <?php if ($pTheme === 'prism-tomorrow') { echo 'selected'; } ?>>Tomorrow Night (Default Dark)</option>
                                <option value="prism-dark" <?php if ($pTheme === 'prism-dark') { echo 'selected'; } ?>>Prism Dark</option>
                                <option value="prism-okaidia" <?php if ($pTheme === 'prism-okaidia') { echo 'selected'; } ?>>Okaidia (Neon Dark)</option>
                                <option value="prism-twilight" <?php if ($pTheme === 'prism-twilight') { echo 'selected'; } ?>>Twilight (Muted Dark)</option>
                                <option value="prism-coy" <?php if ($pTheme === 'prism-coy') { echo 'selected'; } ?>>Coy (Light with Border)</option>
                                <option value="prism-solarizedlight" <?php if ($pTheme === 'prism-solarizedlight') { echo 'selected'; } ?>>Solarized Light</option>
                                <option value="prism-default" <?php if ($pTheme === 'prism-default') { echo 'selected'; } ?>>Default (Light)</option>
                            </select>
                        </div>

                        <!-- Background Color Picker -->
                        <div class="mb-3">
                            <label for="p_bg_color" class="form-label font-weight-bold text-dark">Custom Background Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color border-end-0 rounded-start-3" id="p_bg_picker" value="<?php echo htmlspecialchars($pBgColor, ENT_QUOTES, 'UTF-8'); ?>" style="max-width: 55px; height: 38px; padding: 4px;">
                                <input type="text" id="p_bg_color" name="bg_color" class="form-control rounded-end-3" value="<?php echo htmlspecialchars($pBgColor, ENT_QUOTES, 'UTF-8'); ?>" placeholder="#2d2d2d" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="p_font_size" class="form-label font-weight-bold text-dark">Font Size</label>
                                <input type="text" id="p_font_size" name="font_size" class="form-control rounded-3" value="<?php echo htmlspecialchars($pFontSize, ENT_QUOTES, 'UTF-8'); ?>" placeholder="14px" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="p_border_radius" class="form-label font-weight-bold text-dark">Border Radius</label>
                                <input type="text" id="p_border_radius" name="border_radius" class="form-control rounded-3" value="<?php echo htmlspecialchars($pBorderRadius, ENT_QUOTES, 'UTF-8'); ?>" placeholder="8px" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="p_padding" class="form-label font-weight-bold text-dark">Padding</label>
                            <input type="text" id="p_padding" name="padding" class="form-control rounded-3" value="<?php echo htmlspecialchars($pPadding, ENT_QUOTES, 'UTF-8'); ?>" placeholder="16px" required>
                        </div>

                        <hr class="my-4 text-muted opacity-25">

                        <h6 class="font-weight-bold text-dark mb-3"><i class="fa-solid fa-list-check me-2"></i> Features & Plugins</h6>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="p_line_numbers" name="line_numbers" value="1" <?php if ($pLineNumbers === 1) { echo 'checked'; } ?>>
                            <label class="form-check-label font-weight-bold text-dark" for="p_line_numbers">Show Line Numbers</label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="p_enable_copy" name="enable_copy" value="1" <?php if ($pEnableCopy === 1) { echo 'checked'; } ?>>
                            <label class="form-check-label font-weight-bold text-dark" for="p_enable_copy">Show "Copy Code" Button</label>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="p_color_preview" name="color_preview" value="1" <?php if ($pColorPreview === 1) { echo 'checked'; } ?>>
                            <label class="form-check-label font-weight-bold text-dark" for="p_color_preview">Color Swatch Previews</label>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <?php redirectto('admin/modules', 'Cancel', 'btn btn-outline-secondary rounded-3 px-4 me-md-2'); ?>
                            <button type="submit" class="btn btn-primary rounded-3 px-4"><i class="fa-solid fa-floppy-disk me-2"></i> Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 text-dark font-weight-bold">
                        <i class="fa-solid fa-eye text-success me-2"></i> Live Pro Preview
                    </h5>
                </div>
                <div class="card-body p-4 bg-light d-flex flex-column justify-content-center">
                    <div id="preview_container" style="position: relative; margin: 0; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 8px 24px rgba(0,0,0,0.15); transition: all 0.3s ease; border-radius: <?php echo htmlspecialchars($pBorderRadius, ENT_QUOTES, 'UTF-8'); ?>;">
                        <div id="preview_header" style="display: flex; justify-content: space-between; align-items: center; padding: 8px 16px; background: #1e1e1e; border-bottom: 1px solid rgba(255,255,255,0.05); user-select: none;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="display: flex; gap: 6px;">
                                    <span style="width: 10px; height: 10px; border-radius: 50%; background-color: rgb(255, 95, 86);"></span>
                                    <span style="width: 10px; height: 10px; border-radius: 50%; background-color: rgb(255, 189, 46);"></span>
                                    <span style="width: 10px; height: 10px; border-radius: 50%; background-color: rgb(39, 201, 63);"></span>
                                </div>
                                <span style="font-size: 11px; font-weight: 600; color: rgb(136, 136, 136); text-transform: uppercase; letter-spacing: 0.5px;">CSS</span>
                            </div>
                            <button id="preview_copy" type="button" style="background: none; border: none; color: rgb(136, 136, 136); font-size: 12px; cursor: pointer; padding: 4px 8px; border-radius: 4px; <?php if ($pEnableCopy !== 1) { echo 'display: none;'; } ?>"><i class="fa-regular fa-copy"></i> Copy</button>
                        </div>
                        <pre id="preview_pre" class="line-numbers language-css" style="margin: 0; border: none; font-family: Consolas, Monaco, monospace; transition: all 0.3s ease; background-color: <?php echo htmlspecialchars($pBgColor, ENT_QUOTES, 'UTF-8'); ?>; padding: <?php echo htmlspecialchars($pPadding, ENT_QUOTES, 'UTF-8'); ?>; font-size: <?php echo htmlspecialchars($pFontSize, ENT_QUOTES, 'UTF-8'); ?>;"><code id="preview_code" class="language-css" style="font-family: inherit; white-space: pre-wrap; display: block; font-size: <?php echo htmlspecialchars($pFontSize, ENT_QUOTES, 'UTF-8'); ?>;">.highlight-box {
    background: #27c93f;
    border-color: rgb(255, 99, 71);
    font-size: 14px;
}</code></pre>
                    </div><!-- /preview_container -->
                </div><!-- /card-body -->
            </div><!-- /card -->
        </div><!-- /col-lg-6 right -->
    </div><!-- /row -->
</div><!-- /container-fluid -->

<script>
document.addEventListener("DOMContentLoaded", function() {
    // References
    var bgInput = document.getElementById("p_bg_color");
    var bgPicker = document.getElementById("p_bg_picker");
    var sizeInput = document.getElementById("p_font_size");
    var radiusInput = document.getElementById("p_border_radius");
    var paddingInput = document.getElementById("p_padding");
    var lineNumbersCheck = document.getElementById("p_line_numbers");
    var enableCopyCheck = document.getElementById("p_enable_copy");
    var colorPreviewCheck = document.getElementById("p_color_preview");

    // Elements of Preview
    var previewContainer = document.getElementById("preview_container");
    var previewPre = document.getElementById("preview_pre");
    var previewCode = document.getElementById("preview_code");
    var previewCopy = document.getElementById("preview_copy");

    // Raw CSS code block template
    var rawCSS = `.highlight-box {
    background: #27c93f;
    border-color: rgb(255, 99, 71);
    font-size: 14px;
}`;

    // Color Pickers Sync
    bgPicker.addEventListener("input", function() {
        bgInput.value = bgPicker.value;
        updatePreview();
    });

    bgInput.addEventListener("input", function() {
        if (/^#[0-9a-fA-F]{6}$/.test(bgInput.value)) {
            bgPicker.value = bgInput.value;
        }
        updatePreview();
    });

    // Inputs update
    [sizeInput, radiusInput, paddingInput, document.getElementById("p_theme")].forEach(function(input) {
        input.addEventListener("input", updatePreview);
    });

    [lineNumbersCheck, enableCopyCheck, colorPreviewCheck].forEach(function(check) {
        check.addEventListener("change", updatePreview);
    });

    function updatePreview() {
        var bgVal = bgInput.value || "#2d2d2d";
        var sizeVal = sizeInput.value || "14px";
        var radiusVal = radiusInput.value || "8px";
        var paddingVal = paddingInput.value || "16px";
        var themeVal = document.getElementById("p_theme").value || "prism-tomorrow";

        // Sync theme stylesheet
        var themeFileName = themeVal === 'prism-tomorrow' ? 'prism-tomorrow' : (themeVal === 'prism-default' ? 'prism' : (themeVal === 'prism-solarizedlight' ? 'prism-solarized-light' : themeVal));
        document.getElementById("prism_theme_css").href = "https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/" + themeFileName + ".min.css";

        // Apply styles to preview
        previewContainer.style.borderRadius = radiusVal;
        previewPre.style.backgroundColor = bgVal;
        previewPre.style.padding = paddingVal;
        previewPre.style.fontSize = sizeVal;
        previewCode.style.fontSize = sizeVal;

        // Toggle Copy Button
        if (enableCopyCheck.checked) {
            previewCopy.style.display = "inline-block";
        } else {
            previewCopy.style.display = "none";
        }

        // Toggle Line Numbers Class on the pre tag
        if (lineNumbersCheck.checked) {
            previewPre.classList.add("line-numbers");
        } else {
            previewPre.classList.remove("line-numbers");
        }

        // Reset content and trigger highlight
        previewCode.textContent = rawCSS;
        if (typeof Prism !== "undefined") {
            Prism.highlightElement(previewCode);
        }

        // Dynamic alignment style update for preview
        var dynamicStyles = document.getElementById("preview_dynamic_styles");
            dynamicStyles.innerHTML = `
                #preview_pre.line-numbers {
                    padding-left: 3.8em !important;
                }
                #preview_pre .line-numbers-rows {
                    left: -3.8em !important;
                    width: 3em !important;
                    border-right: 1px solid rgba(255, 255, 255, 0.15) !important;
                    padding-top: ${paddingVal} !important;
                    box-sizing: border-box !important;
                }
                #preview_pre .line-numbers-rows > span::before {
                    color: rgba(255, 255, 255, 0.4) !important;
                }
            `;

        // Toggle Color Previews
        document.querySelectorAll(".preview-color-dot").forEach(function(dot) { dot.remove(); });
        if (colorPreviewCheck.checked) {
            document.querySelectorAll("#preview_pre .token.color, #preview_pre .token.number, #preview_pre .token.function").forEach(function(token) {
                var text = token.textContent;
                var match = text.match(/#(?:[0-9a-fA-F]{3,4}){1,2}\b|rgba?\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*(?:,\s*[0-9.]+\s*)?\)/i);
                if (match) {
                    var dot = document.createElement("span");
                    dot.className = "preview-color-dot";
                    dot.style.display = "inline-block";
                    dot.style.width = "10px";
                    dot.style.height = "10px";
                    dot.style.borderRadius = "50%";
                    dot.style.backgroundColor = match[0];
                    dot.style.marginRight = "4px";
                    dot.style.border = "1px solid rgba(255,255,255,0.3)";
                    token.insertBefore(dot, token.firstChild);
                }
            });
        }
    }

    // Run initially
    updatePreview();
});
</script>

{{ $this->view('admin/layout/footer') }}
