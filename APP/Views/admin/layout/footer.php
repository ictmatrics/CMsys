        </div> <!-- End of #content -->
        </div> <!-- End of .wrapper -->

        <!-- Core Javascript Files -->
        <script src="{{ pathto('js/jquery3.7.1.min.js') }}"></script>
        <script src="{{ pathto('js/bootstrap5.3.8.bundle.min.js') }}"></script>
        <script src="{{ pathto('js/script.js') }}?v={{ time() }}"></script>

        <!-- CDN JS files for rich admin capabilities -->
        <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>


        <script>
            $(document).ready(function() {
                // Initialize WOW.js for entrance transitions
                if (typeof WOW !== 'undefined') {
                    new WOW().init();
                }

                // Initialize default DataTable
                if ($.fn.DataTable) {
                    $('.datatable').DataTable({
                        responsive: true,
                        pageLength: 10,
                        language: {
                            search: "_INPUT_",
                            searchPlaceholder: "Search records..."
                        }
                    });
                }

                // Initialize default WYSIWYG Editor
                if ($.fn.summernote) {
                    $('.editor').summernote({
                        height: 300,
                        dialogsInBody: true,
                        toolbar: [
                            ['style', ['style']],
                            ['font', ['bold', 'underline', 'clear']],
                            ['color', ['color']],
                            ['custom', ['mediaLibrary']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['table', ['table']],
                            ['insert', ['link', 'imageLibrary', 'videoLibrary'<?php echo apply_filters('summernote_insert_buttons', ''); ?>]],
                            ['view', ['fullscreen', 'codeview', 'help']]
                        ],
                        buttons: {
                            imageLibrary: function(context) {
                                const ui = $.summernote.ui;
                                return ui.button({
                                    contents: '<i class="note-icon-picture"></i>',
                                    tooltip: 'Choose Image from Media Library',
                                    click: function() {
                                        window._activeSummernoteContext = context;
                                        chooseImageFromLibrary('summernote', 'image');
                                    }
                                }).render();
                            },
                            videoLibrary: function(context) {
                                const ui = $.summernote.ui;
                                return ui.button({
                                    contents: '<i class="note-icon-video"></i>',
                                    tooltip: 'Choose Video from Media Library',
                                    click: function() {
                                        window._activeSummernoteContext = context;
                                        chooseImageFromLibrary('summernote', 'video');
                                    }
                                }).render();
                            },
                            mediaLibrary: function(context) {
                                const ui = $.summernote.ui;
                                const button = ui.button({
                                    contents: '<i class="fa-solid fa-photo-film"></i> Media Library',
                                    tooltip: 'Choose asset from Media Library',
                                    click: function() {
                                        window._activeSummernoteContext = context;
                                        chooseImageFromLibrary('summernote', 'all');
                                    }
                                });
                                return button.render();
                            }
                        },
                        callbacks: {
                            onImageUpload: function(files) {
                                uploadEditorImage(files[0], this);
                            }
                        }
                    });
                }

                // Summernote Dynamic Image Upload Handler
                function uploadEditorImage(file, editor) {
                    let data = new FormData();
                    data.append("file", file);
                    $.ajax({
                        url: "{{ pathto('admin/media/upload') }}",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: data,
                        type: "POST",
                        dataType: 'json',
                        success: function(url) {
                            if (url.status === 'success') {
                                $(editor).summernote('insertImage', url.url);
                            } else {
                                alert("Image upload failed: " + url.message);
                            }
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    });
                }
            });
        </script>
        <?php do_action('admin_footer'); ?>
        </body>

        </html>