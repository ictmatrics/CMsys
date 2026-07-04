{{ $this->view('admin/layout/header', ['title' => $title]) }}

<div class="row wow animate__animated animate__fadeInUp">
    <!-- Upload Area -->
    <div class="col-md-12 mb-4">
        <div class="card card-custom">
            <div class="card-header card-custom-header">
                <h5 class="mb-0"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Upload New Media</h5>
            </div>
            <div class="card-body card-custom-body">
                <div id="drop-zone" class="border border-secondary border-2 border-dashed rounded p-5 text-center bg-light" style="cursor:pointer; border-style: dashed !important;">
                    <i class="fa-solid fa-cloud-arrow-up fa-3x text-muted mb-3"></i>
                    <h6>Drag and drop your file here, or click to upload</h6>
                    <small class="text-muted">Supports images, documents, and videos up to 10MB</small>
                    <input type="file" id="media_file_input" class="d-none">
                </div>
                <div class="progress mt-3" style="display:none; height: 8px;">
                    <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Media Gallery list -->
    <div class="col-md-12">
        <div class="card card-custom">
            <div class="card-header card-custom-header">
                <h5 class="mb-0"><i class="fa-solid fa-photo-film me-2"></i> Media Gallery</h5>
            </div>
            <div class="card-body card-custom-body">
                <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-4" id="media-gallery">
                    <?php if (empty($media)) { ?>
                        <div class="col-12 text-center text-muted py-5">
                            <i class="fa-solid fa-images fa-3x mb-3"></i>
                            <p>No media files found in your library.</p>
                        </div>
                    <?php } else { ?>
                        <?php foreach ($media as $item) { ?>
                            <div class="col" id="row{{ $item['id'] }}">
                                <div class="card h-100 shadow-sm border media-item-card" data-id="{{ $item['id'] }}" data-path="{{ pathto($item['path']) }}" data-name="{{ htmlspecialchars($item['original_name'], ENT_QUOTES, 'UTF-8') }}">
                                    <div class="position-relative">
                                        <?php if (str_starts_with($item['mime_type'], 'image/')) { ?>
                                            <img src="{{ pathto($item['path']) }}" class="card-img-top p-1" style="height: 120px; object-fit: cover; border-radius: 8px;">
                                        <?php } else { ?>
                                            <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="height: 120px; border-radius: 8px;">
                                                <i class="fa-solid fa-file-lines fa-3x"></i>
                                            </div>
                                        <?php } ?>
                                        <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 delete" data-id="{{ $item['id'] }}" data-action="delete" data-url="{{ pathto('admin/media/delete') }}" style="padding: 2px 6px; font-size: 11px;"><i class="fa-solid fa-times"></i></button>
                                    </div>
                                    <div class="card-body p-2 bg-light text-center border-top">
                                        <span class="text-truncate d-block small font-weight-bold" style="max-width: 100%;" title="{{ htmlspecialchars($item['original_name'], ENT_QUOTES, 'UTF-8') }}">{{ htmlspecialchars($item['original_name'], ENT_QUOTES, 'UTF-8') }}</span>
                                        <button type="button" class="btn btn-xs btn-link p-0 text-muted btn-copy-url" data-url="{{ pathto($item['path']) }}" style="font-size:10px;"><i class="fa-solid fa-copy me-1"></i> Copy URL</button>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('media_file_input');

        // Click triggers input
        dropZone.addEventListener('click', () => fileInput.click());

        // Drag events
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.add('bg-secondary-light');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropZone.classList.remove('bg-secondary-light');
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                handleFileUpload(files[0]);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileUpload(e.target.files[0]);
            }
        });

        function handleFileUpload(file) {
            const formData = new FormData();
            formData.append('file', file);

            $('.progress').show();
            $('#upload-progress-bar').css('width', '0%').attr('aria-valuenow', 0);

            $.ajax({
                url: "{{ pathto('admin/media/upload') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                            $('#upload-progress-bar').css('width', percentComplete + '%').attr('aria-valuenow', percentComplete);
                        }
                    }, false);
                    return xhr;
                },
                success: function(res) {
                    $('.progress').hide();
                    if (res.status === 'success') {
                        flash('File uploaded successfully!', 'success');
                        setTimeout(() => { location.reload(); }, 800);
                    } else {
                        flash(res.message || 'Upload failed', 'warning');
                    }
                },
                error: function() {
                    $('.progress').hide();
                    flash('Network error during upload', 'danger');
                }
            });
        }

        // Copy URL handler (with HTTP/Non-secure context fallback)
        $(document).on('click', '.btn-copy-url', function() {
            const url = $(this).attr('data-url');
            if (!url) return;

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(() => {
                    flash('URL copied to clipboard!', 'info');
                }).catch(() => {
                    fallbackCopyText(url);
                });
            } else {
                fallbackCopyText(url);
            }
        });

        function fallbackCopyText(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.opacity = "0";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    flash('URL copied to clipboard!',  'info');
                } else {
                    flash('Failed to copy URL', 'warning');
                }
            } catch (err) {
                flash('Failed to copy URL', 'danger');
            }
            document.body.removeChild(textArea);
        }
    });
</script>

<style>
    .bg-secondary-light { background-color: rgba(108, 117, 125, 0.15) !important; }
    .btn-xs { padding: 1px 5px; font-size: 10px; }
</style>

{{ $this->view('admin/layout/footer') }}
