function Dialog(mediaSource, closeText, okText, cancelText) {
    this.mediaSource = mediaSource;
    this.okText = okText;
    this.cancelText = cancelText;
    this.closeText = closeText;

    this.showDialog = function (type, titleBar, title, message, onClose) {
        $('div.dialogModal').remove();
        $('body').append(
            '<div id="errorModal" class="modal dialogModal" tabindex="-1" role="dialog">\n' +
            '    <div class="modal-dialog" role="document">\n' +
            '        <div class="modal-content">\n' +
            '            <div id="dialogTitleBar" class="modal-header">\n' +
            '                <h5 class="modal-title" id="dialogTitle"></h5>\n' +
            '                <button type="button" class="close" data-dismiss="modal" aria-label="Close">\n' +
            '                    <span aria-hidden="true">&times;</span>\n' +
            '                </button>\n' +
            '            </div>\n' +
            '            <div class="modal-body">\n' +
            '                <div class="container-fluid">\n' +
            '                    <div class="row">\n' +
            '                        <div class="col-md-3" style="text-align: center;">\n' +
            '                            <img style="width: 80%; max-width: 6rem; margin-bottom: 1em;"\n' +
            '                                 src="https://media.mct118.com/images/dialog/info.png"\n' +
            '                                 id="dialogIcon"/>\n' +
            '                        </div>\n' +
            '                        <div class="col-md-9 ml-auto">\n' +
            '                            <p id="dialogContent"></p>\n' +
            '                        </div>\n' +
            '                    </div>\n' +
            '                </div>\n' +
            '            </div>\n' +
            '            <div id="dialogButtonBar" class="modal-footer text-right">\n' +
            '                <button type="button" class="btn btn-secondary"\n' +
            '                    data-dismiss="modal" id="dialogCloseBtn">' + this.closeText + '</button>' +
            '            </div>\n' +
            '        </div>\n' +
            '    </div>\n' +
            '</div>');
        $('h5#dialogTitle').html(title);
        $('p#dialogContent').html(message);
        $("div#dialogTitleBar").removeClass();
        if (typeof (titleBar) !== 'string')
            titleBar = '';
        $("div#dialogTitleBar").addClass("modal-header" + (titleBar === '' ? '' : ' ' + titleBar));

        $('img#dialogIcon').attr("src", this.mediaSource + "/images/dialog/" + type + ".png");
        if (typeof (onClose) === 'function')
            $('button#dialogCloseBtn').click(onClose);
        $('div#errorModal').modal('show');
    };

    this.questionDialog = function (title, message, onOk, onCancel) {
        $('body').remove('div#errorModal');
        $('body').append('<!-- Dialog Modal -->\n' +
            '<div id="errorModal" class="modal" tabindex="-1" role="dialog">\n' +
            '    <div class="modal-dialog" role="document">\n' +
            '        <div class="modal-content">\n' +
            '            <div id="dialogTitleBar" class="modal-header bg-primary">\n' +
            '                <h5 class="modal-title" id="dialogTitle"></h5>\n' +
            '                <button type="button" class="close" data-dismiss="modal" aria-label="Close">\n' +
            '                    <span aria-hidden="true">&times;</span>\n' +
            '                </button>\n' +
            '            </div>\n' +
            '            <div class="modal-body">\n' +
            '                <div class="container-fluid">\n' +
            '                    <div class="row">\n' +
            '                        <div class="col-md-3" style="text-align: center;">\n' +
            '                            <img style="width: 80%; max-width: 6rem; margin-bottom: 1em;"\n' +
            '                                 src="' + this.mediaSource + '/images/dialog/question.png"' +
            '                                 id="dialogIcon"/>' +
            '                        </div>\n' +
            '                        <div class="col-md-9 ml-auto">\n' +
            '                            <p id="dialogContent"></p>\n' +
            '                        </div>\n' +
            '                    </div>\n' +
            '                </div>\n' +
            '            </div>\n' +
            '            <div id="dialogButtonBar" class="modal-footer text-right">\n' +
            '                <button type="button" class="btn btn-primary"\n' +
            '                    data-dismiss="modal" id="dialogOkBtn">' + this.okText + '</button>' +
            '                <button type="button" class="btn btn-secondary"\n' +
            '                    data-dismiss="modal" id="dialogCancelBtn">' + this.cancelText + '</button>' +
            '            </div>\n' +
            '        </div>\n' +
            '    </div>\n' +
            '</div>');
        $('h5#dialogTitle').html(title);
        $('p#dialogContent').html(message);
        if (typeof (onOk) === 'function')
            $('button#dialogOkBtn').click(onOk);
        if (typeof (onCancel) === 'function')
            $('button#dialogCancelBtn').click(onCancel);
        $('div#errorModal').modal('show');
    }
}

Dialog.prototype.info = function (title, message, onClose) {
    this.showDialog('info', 'bg-info', title, message, onClose);
};

Dialog.prototype.warning = function (title, message, onClose) {
    this.showDialog('warning', 'bg-warning', title, message, onClose);
};

Dialog.prototype.error = function (title, message, onClose) {
    this.showDialog('error', 'bg-danger', title, message, onClose);
};

Dialog.prototype.success = function (title, message, onClose) {
    this.showDialog('success', 'bg-success', title, message, onClose);
};

Dialog.prototype.question = function (title, message, onOk, onCancel) {
    this.questionDialog(title, message, onOk, onCancel);
};