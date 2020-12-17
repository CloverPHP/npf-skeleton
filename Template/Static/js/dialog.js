function Dialog(mediaSource, closeText, okText, cancelText) {
    this.mediaSource = mediaSource;
    this.okText = okText;
    this.cancelText = cancelText;
    this.closeText = closeText;

    this.showDialog = function (mode, type, title, message, onClose, onOk, onCancel) {
        $('div.dialogModal').remove();
        if (['primary', 'success', 'info', 'warning', 'danger'].indexOf(type) < 0)
            type = 'primary';
        var dialogButtonGroup = '';
        switch (mode) {
            case 'question':
                dialogButtonGroup =
                    '                   <button type="button" class="btn btn-primary"\n' +
                    '                       data-dismiss="modal" id="dialogOkBtn">' + this.okText + '</button>' +
                    '                   <button type="button" class="btn btn-secondary"\n' +
                    '                       data-dismiss="modal" id="dialogCancelBtn">' + this.cancelText + '</button>';
                break;
            default:
                dialogButtonGroup =
                    '                <button type="button" class="btn btn-secondary"\n' +
                    '                    data-dismiss="modal" id="dialogCloseBtn">' + this.closeText + '</button>';
        }
        $('body').append(
            '<div class="modal fade dialogModal" tabindex="-1" role="dialog" ' +
            '    aria-labelledby="' + type + '-header-modalLabel" aria-hidden="true">\n' +
            '    <div class="modal-dialog modal-dialog-centered">\n' +
            '        <div class="modal-content">\n' +
            '            <div class="modal-header modal-colored-header bg-' + type + '">\n' +
            '                <h4 class="modal-title">' + title + '</h4>\n' +
            '                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" aria-label="Close">x</button>\n' +
            '            </div>\n' +
            '            <div class="modal-body">\n' +
            '                <div class="container-fluid">\n' +
            '                    <div class="row">\n' +
            '                        <div class="col-md-3" style="text-align: center;">\n' +
            '                            <img style="width: 80%; max-width: 6rem; margin-bottom: 1em;"\n' +
            '                                 class="dialogIcon" alt=""/>\n' +
            '                        </div>\n' +
            '                        <div class="col-md-9 ml-auto">\n' +
            '                            <p>' + message + '</p>\n' +
            '                        </div>\n' +
            '                    </div>\n' +
            '                </div>\n' +
            '            </div>\n' +
            '            <div class="modal-footer text-right">\n' +
            dialogButtonGroup +
            '            </div>\n' +
            '        </div>\n' +
            '    </div>\n' +
            '</div>');
        if (type === 'primary')
            type = 'info';
        $('div.dialogModal img.dialogIcon').attr("src", this.mediaSource + "/images/dialog/" + type + ".png");
        $('div.dialogModal').on('hidden.bs.modal', function (e) {
            if (mode === 'modal' && typeof (onClose) === 'function')
                onClose(e);
        });

        switch (mode) {
            case 'question':
                if (typeof (onOk) === 'function')
                    $('button#dialogOkBtn').click(onOk);
                if (typeof (onCancel) === 'function')
                    $('button#dialogCancelBtn').click(onCancel);
                break;
        }
        $('div.dialogModal').modal('show');
    };
}

Dialog.prototype.primary = function (title, message, onClose) {
    this.showDialog('modal', 'primary', title, message, onClose);
};

Dialog.prototype.info = function (title, message, onClose) {
    this.showDialog('modal', 'info', title, message, onClose);
};

Dialog.prototype.warning = function (title, message, onClose) {
    this.showDialog('modal', 'warning', title, message, onClose);
};

Dialog.prototype.danger = function (title, message, onClose) {
    this.showDialog('modal', 'danger', title, message, onClose);
};

Dialog.prototype.success = function (title, message, onClose) {
    this.showDialog('modal', 'success', title, message, onClose);
};

Dialog.prototype.primaryQuest = function (title, message, onOk, onCancel, onClose) {
    this.showDialog('question', 'primary', title, message, onClose, onOk, onCancel);
};

Dialog.prototype.infoQuest = function (title, message, onOk, onCancel, onClose) {
    this.showDialog('question', 'info', title, message, onClose, onOk, onCancel);
};

Dialog.prototype.warningQuest = function (title, message, onOk, onCancel, onClose) {
    this.showDialog('question', 'warning', title, message, onClose, onOk, onCancel);
};

Dialog.prototype.dangerQuest = function (title, message, onOk, onCancel, onClose) {
    this.showDialog('question', 'danger', title, message, onClose, onOk, onCancel);
};

Dialog.prototype.successQuest = function (title, message, onOk, onCancel, onClose) {
    this.showDialog('question', 'success', title, message, onClose, onOk, onCancel);
};