/* Auto-detect Base URL from stylesheet path */
(() => {
  const link = document.querySelector('link[href*="css/style.css"]')?.getAttribute('href');
  window.BASE_URL = link && link.indexOf('css/style.css') !== -1
    ? link.split('css/style.css')[0]
    : window.location.origin + '/';
})();

/* First letter Capital */
function ucwords(str) {
  return str
    .split(" ")
    .map(function (word) {
      return word.charAt(0).toUpperCase() + word.slice(1);
    })
    .join(" ");
}

/* Flash Message */

const flash = (msg, type = "info", delay = 3000) => {
  $("#flash-message")
    .stop(true, true)
    .html(`<div class="alert alert-${type} shadow">${msg}</div>`)
    .fadeIn(200)
    .delay(delay)
    .fadeOut(400);
};

/* ====== Menu button for page redirect*/
/* how to use 
# data-action for to get $_POST['action]
# data-p for form action=''
# class = menu  !important to run this function
<a href="#" data-action="Action" data-p="URL" class="menu  btn btn-success">Page Name</a>
*/
$(function () {
  $(".menu").on("click", function (e) {
    e.preventDefault();

    const $el = $(this);
    const form = $("<form>", {
      method: "POST",
      action: $el.data("p"),
    });

    $.each($el.data(), (key, value) =>
      form.append($("<input>", { type: "hidden", name: key, value })),
    );

    $("body").append(form).trigger("submit");
  });
});

/* ===== Save Data ===== */
/* Usage: saveData('#formID'); */
function saveData(formID) {
  $(document)
    .off("submit", formID)
    .on("submit", formID, function (e) {
      e.preventDefault();

      const $frm = $(this);

      $.ajax({
        type: "POST",
        url: "", // safe fallback
        data: $frm.serialize(),
        dataType: "json", // ✅ IMPORTANT
        success: function (res) {
          if (res.status === "success") {
            flash("Saved successfully", "success");
            $frm[0].reset();
          } else if (res.status === "duplicate") {
            flash("Data already exists", "warning");
          } else {
            flash(res.message || "Save failed", "warning");
          }
        },
        error: function () {
          flash("Network / Server error", "danger");
        },
      });

      return false; // extra safety
    });
}

/* ===== Delete Data ===== */
/* Required:
 * data-id="1"
 * data-action="delete"
 * row id="row1"
 */
$(document).on("click", ".delete", function (e) {
  e.preventDefault();

  const $btn = $(this);
  const { id, action, url } = $btn.data();

  if (!id || !action) return console.error("Missing data attributes");
  if (!confirm("Are you sure you want to delete this record?")) return;

  $.ajax({
    type: "POST",
    url: url || "",
    data: { id, action },
    dataType: "json",
    beforeSend: () => {
      flash("Deleting...", "info");
      $btn.prop("disabled", true);
    },
    success: (res) => {
      if (res.status === "success") {
        $(`#row${id}`).fadeOut(300, function () {
          $(this).remove();
        });
        flash("Deleted successfully", "danger");
      } else {
        flash(res.message || "Delete failed", "warning");
      }
    },
    error: () => flash("Server error occurred", "warning"),
    complete: () => $btn.prop("disabled", false),
  });
});

/* $(document).on('hidden.bs.modal', '.modal', function () {
    if (this.id !== 'mediaSelectModal') {
        location.reload();
    }
}); */

/* Modal open and close by data-modal */
/**
 * Open modal by ID
 * @param {string} modalId
 */
function openModal(modalId) {
  const modalEl = document.getElementById(modalId);
  if (!modalEl) return;

  const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
  modalInstance.show();
}

/**
 * Close modal by ID
 * @param {string} modalId
 */
function closeModal(modalId) {
  const modalEl = document.getElementById(modalId);
  if (!modalEl) return;

  const modalInstance = bootstrap.Modal.getInstance(modalEl);
  if (modalInstance) {
    modalInstance.hide();
  }
}
/**
 * Reusable Media Library Image Selector
 *
 * Usage:
 * Add class 'btn-select-media' and 'data-target="input_id"' to any button:
 * <button class="btn btn-primary btn-select-media" data-target="featured_image">Select Image</button>
 *
 * Optional:
 * Add an img element with ID 'input_id_preview' to show the preview automatically.
 */
function chooseImageFromLibrary(targetFieldId, filterType = 'all') {
  let mediaModalEl = document.getElementById('mediaSelectModal');
  if (!mediaModalEl) {
    const modalHtml = `
      <div class="modal fade" id="mediaSelectModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content shadow-lg border-0 rounded-4">
                  <div class="modal-header bg-light border-bottom-0 pb-0">
                      <h5 class="modal-title font-weight-bold text-dark">
                          <i class="fa-solid fa-images text-primary me-2"></i> Select Asset from Media Library
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body p-4" style="max-height: 500px; overflow-y: auto;">
                      <!-- Drag & Drop Zone -->
                      <div id="mediaModalDropzone" class="border border-2 border-primary border-dashed rounded-3 p-4 mb-3 text-center bg-light position-relative" style="border-style: dashed !important; transition: all 0.3s ease; cursor: pointer;">
                          <input type="file" id="mediaModalFileField" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer; z-index: 10;">
                          <div class="dropzone-prompt">
                              <i class="fa-solid fa-cloud-arrow-up fa-2x text-primary mb-2"></i>
                              <p class="mb-1 fw-bold text-secondary">Drag and drop file here, or click to select</p>
                              <span class="text-muted small">Supports JPG, PNG, GIF, WEBP, MP4, PDF</span>
                          </div>
                          <div class="dropzone-loading d-none">
                              <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                              <span class="text-secondary small fw-bold">Uploading file...</span>
                          </div>
                      </div>

                      <div class="row row-cols-2 row-cols-md-4 g-3" id="modalMediaList"></div>
                  </div>
              </div>
          </div>
      </div>
    `;
    $("body").append(modalHtml);
    mediaModalEl = document.getElementById('mediaSelectModal');
  }

  window._mediaTarget = targetFieldId;
  window._mediaFilterType = filterType;

  let modalInstance = bootstrap.Modal.getInstance(mediaModalEl);
  if (!modalInstance) {
    modalInstance = new bootstrap.Modal(mediaModalEl, { backdrop: true, keyboard: true });
  }

  $('#modalMediaList').html(
      '<div class="col-12 text-center py-5">' +
      '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading…</span></div>' +
      '<p class="text-muted mt-2 small">Loading media assets…</p>' +
      '</div>'
  );

  modalInstance.show();

  const mediaUrl = (window.BASE_URL || '') + 'admin/media';
  $.ajax({
      url: mediaUrl,
      type: 'GET',
      dataType: 'json',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      success: function (data) {
          $('#modalMediaList').empty();
          let items = (data || []).filter(item => item.mime_type && (item.mime_type.startsWith('image/') || item.mime_type.startsWith('video/')));

          if (window._mediaFilterType === 'image') {
              items = items.filter(item => item.mime_type.startsWith('image/'));
          } else if (window._mediaFilterType === 'video') {
              items = items.filter(item => item.mime_type.startsWith('video/'));
          }

          if (items.length === 0) {
              $('#modalMediaList').html(
                  '<div class="col-12 text-center text-muted py-4">' +
                  '<i class="fa-solid fa-image fa-2x mb-2 d-block opacity-50"></i>' +
                  'No matching media assets found in your Media Library.' +
                  '</div>'
              );
              return;
          }

          items.forEach(function (item) {
              const safeName = $('<span>').text(item.original_name).html();
              const isVideo = item.mime_type && item.mime_type.startsWith('video/');
              const previewHtml = isVideo
                  ? '<div class="d-flex align-items-center justify-content-center bg-light text-muted" style="height:100px;"><i class="fa-solid fa-file-video fa-2x text-secondary"></i></div>'
                  : '<img src="' + item.path + '" class="card-img-top p-1" style="height:100px;object-fit:contain;" loading="lazy">';

              const $card = $(
                  '<div class="col text-center">' +
                      '<div class="card h-100 shadow-sm border select-media-card rounded-3 overflow-hidden"' +
                          ' data-path="' + item.path + '" data-mime="' + item.mime_type + '" style="cursor:pointer;" role="button" tabindex="0">' +
                          previewHtml +
                          '<div class="card-footer p-1 bg-light">' +
                              '<span class="text-truncate d-block small px-1">' + safeName + '</span>' +
                          '</div>' +
                      '</div>' +
                  '</div>'
              );
              $('#modalMediaList').append($card);
          });
      },
      error: function () {
          $('#modalMediaList').html(
              '<div class="col-12 text-center text-danger py-4">' +
              '<i class="fa-solid fa-circle-exclamation fa-2x mb-2 d-block"></i>' +
              'Failed to load media assets. Please try again.' +
              '</div>'
          );
      }
  });
}

// Click handler to open the media library selector
$(document).on('click', '.btn-select-media', function (e) {
  e.preventDefault();
  const targetId = $(this).attr('data-target');
  if (targetId) {
    chooseImageFromLibrary(targetId);
  }
});

// Card selection handler inside modal
$(document).on('click', '.select-media-card', function () {
  const fullPath = $(this).attr('data-path');
  const mimeType = $(this).attr('data-mime') || '';
  const targetId = window._mediaTarget;
  if (!fullPath || !targetId) { return; }

  if (targetId === 'summernote') {
    const context = window._activeSummernoteContext;
    if (context) {
      const isVideo = mimeType.startsWith('video/');
      let html = '';
      if (isVideo) {
        html = `<video src="${fullPath}" controls style="max-width: 100%; display: block; margin: 10px 0;"></video>`;
      } else {
        html = `<img src="${fullPath}" class="img-fluid" style="max-width: 100%; display: block; margin: 10px 0;" />`;
      }
      context.invoke('editor.pasteHTML', html);
    }
  } else {
    const baseUrl = window.BASE_URL || '';
    const cleanedPath = (baseUrl && fullPath.startsWith(baseUrl))
        ? fullPath.slice(baseUrl.length).replace(/^\/+/, '')
        : fullPath;

    $('#' + targetId).val(cleanedPath);
    $('#' + targetId + '_preview').attr('src', fullPath).show();
    $('#' + targetId + '_placeholder').hide();
  }

  const modalEl = document.getElementById('mediaSelectModal');
  if (modalEl) {
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) { modalInstance.hide(); }
  }
});

// Clear button handler for media inputs
$(document).on('click', '.btn-clear-media', function (e) {
  e.preventDefault();
  const targetId = $(this).attr('data-target');
  if (targetId) {
    $('#' + targetId).val('');
    $('#' + targetId + '_preview').attr('src', '').hide();
    $('#' + targetId + '_placeholder').show();
  }
});

// Drag/drop upload handlers
function uploadMediaFile(file) {
  if (!file) return;

  const dropzone = $('#mediaModalDropzone');
  const prompt = dropzone.find('.dropzone-prompt');
  const loading = dropzone.find('.dropzone-loading');

  prompt.addClass('d-none');
  loading.removeClass('d-none');

  const data = new FormData();
  data.append("file", file);

  const uploadUrl = (window.BASE_URL || '') + 'admin/media/upload';
  $.ajax({
      url: uploadUrl,
      type: 'POST',
      data: data,
      cache: false,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function(res) {
          prompt.removeClass('d-none');
          loading.addClass('d-none');
          
          if (res.status === 'success') {
              const fullUrl = res.url;
              const targetId = window._mediaTarget;
              
              if (targetId) {
                  if (targetId === 'summernote') {
                      const context = window._activeSummernoteContext;
                      if (context) {
                          const isVideo = file.type && file.type.startsWith('video/');
                          let html = isVideo
                              ? `<video src="${fullUrl}" controls style="max-width: 100%; display: block; margin: 10px 0;"></video>`
                              : `<img src="${fullUrl}" class="img-fluid" style="max-width: 100%; display: block; margin: 10px 0;" />`;
                          context.invoke('editor.pasteHTML', html);
                      }
                  } else {
                      const baseUrl = window.BASE_URL || '';
                      const cleanedPath = (baseUrl && fullUrl.startsWith(baseUrl))
                          ? fullUrl.slice(baseUrl.length).replace(/^\/+/, '')
                          : fullUrl;

                      $('#' + targetId).val(cleanedPath);
                      $('#' + targetId + '_preview').attr('src', fullUrl).show();
                      $('#' + targetId + '_placeholder').hide();
                  }
                  
                  const modalEl = document.getElementById('mediaSelectModal');
                  if (modalEl) {
                      const modalInstance = bootstrap.Modal.getInstance(modalEl);
                      if (modalInstance) { modalInstance.hide(); }
                  }
                  
                  $('#' + targetId).trigger('input');
              }
          } else {
              alert("Upload failed: " + res.message);
          }
      },
      error: function() {
          prompt.removeClass('d-none');
          loading.addClass('d-none');
          alert("Network / Server error during file upload");
      }
  });
}

$(document).on('change', '#mediaModalFileField', function(e) {
  const file = e.target.files[0];
  uploadMediaFile(file);
});

$(document).on('dragenter dragover', '#mediaModalDropzone', function(e) {
  e.preventDefault();
  e.stopPropagation();
  $(this).addClass('bg-primary-subtle border-primary-emphasis').css('transform', 'scale(1.01)');
});

$(document).on('dragleave', '#mediaModalDropzone', function(e) {
  e.preventDefault();
  e.stopPropagation();
  $(this).removeClass('bg-primary-subtle border-primary-emphasis').css('transform', 'none');
});

$(document).on('drop', '#mediaModalDropzone', function(e) {
  e.preventDefault();
  e.stopPropagation();
  $(this).removeClass('bg-primary-subtle border-primary-emphasis').css('transform', 'none');
  
  const files = e.originalEvent?.dataTransfer?.files || e.dataTransfer?.files;
  if (files && files.length > 0) {
    uploadMediaFile(files[0]);
  }
});

/* ====== Theme Mode Switching Controller (Light, Dark, Sepia) ====== */
$(document).on('click', '[data-theme]', function(e) {
  e.preventDefault();
  const theme = $(this).data('theme');
  $('[data-theme]').removeClass('active');
  $(`[data-theme="${theme}"]`).addClass('active');

  $('body').removeClass('theme-light theme-dark theme-sepia').addClass(`theme-${theme}`);
  
  // Persist preference across pages
  localStorage.setItem('codies_theme_mode', theme);
  
  if (typeof triggerToast === 'function') {
    triggerToast(`Visual environment set to: ${theme.toUpperCase()}`);
  }
});

// Synchronize button active states on page load
$(function() {
  const savedTheme = localStorage.getItem('codies_theme_mode');
  if (savedTheme) {
    $('[data-theme]').removeClass('active');
    $(`[data-theme="${savedTheme}"]`).addClass('active');
  }
});
