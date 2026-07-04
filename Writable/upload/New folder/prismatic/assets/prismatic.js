(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        module.exports = factory(require('jquery'));
    } else {
        factory(window.jQuery);
    }
}(function ($) {
    $.extend($.summernote.plugins, {
        'prismatic': function (context) {
            var self = this;
            var ui = $.summernote.ui;

            context.memo('button.prismatic', function () {
                var button = ui.button({
                    contents: '<i class="fa fa-code"/> Code Snippet',
                    tooltip: 'Insert Prismatic Code Snippet',
                    click: function () {
                        self.show();
                    }
                });
                return button.render();
            });

            this.initialize = function () {
                var $container = context.layoutInfo.editor;
                var body = '<div class="form-group">' +
                           '<label>Language</label>' +
                           '<select class="form-control prismatic-lang">' +
                           '<option value="php">PHP</option>' +
                           '<option value="javascript">JavaScript</option>' +
                           '<option value="html">HTML</option>' +
                           '<option value="css">CSS</option>' +
                           '<option value="sql">SQL</option>' +
                           '<option value="bash">Bash</option>' +
                           '</select>' +
                           '</div>' +
                           '<div class="form-group">' +
                           '<label>Code</label>' +
                           '<textarea class="form-control prismatic-code" rows="10"></textarea>' +
                           '</div>';
                
                var footer = '<button type="button" class="btn btn-primary prismatic-btn-insert">Insert Code</button>';

                this.$dialog = ui.dialog({
                    title: 'Insert Code Snippet',
                    body: body,
                    footer: footer
                }).render().appendTo($container);
            };

            this.show = function () {
                context.invoke('editor.saveRange');
                var $dialog = this.$dialog;
                var $code = $dialog.find('.prismatic-code');
                var $lang = $dialog.find('.prismatic-lang');
                var $insertBtn = $dialog.find('.prismatic-btn-insert');

                ui.onDialogShown(this.$dialog, function () {
                    context.triggerEvent('dialog.shown');
                    $code.val('');
                    $code.focus();

                    $insertBtn.off('click').on('click', function (e) {
                        e.preventDefault();
                        var codeVal = $code.val();
                        var langVal = $lang.val();

                        // Escape HTML entities
                        var escapedCode = codeVal
                            .replace(/&/g, "&amp;")
                            .replace(/</g, "&lt;")
                            .replace(/>/g, "&gt;")
                            .replace(/"/g, "&quot;")
                            .replace(/'/g, "&#039;");

                        var node = document.createElement('pre');
                        var codeNode = document.createElement('code');
                        codeNode.className = 'language-' + langVal;
                        codeNode.innerHTML = escapedCode;
                        node.appendChild(codeNode);

                        context.invoke('editor.restoreRange');
                        context.invoke('editor.focus');
                        context.invoke('editor.insertNode', node);
                        
                        // Insert an empty paragraph after to allow typing
                        var p = document.createElement('p');
                        p.innerHTML = '<br>';
                        context.invoke('editor.insertNode', p);
                        
                        ui.hideDialog($dialog);
                    });
                });

                ui.showDialog(this.$dialog);
            };

            this.destroy = function () {
                ui.hideDialog(this.$dialog);
                this.$dialog.remove();
            };
        }
    });
}));
