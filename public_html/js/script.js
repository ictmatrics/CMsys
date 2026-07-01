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
