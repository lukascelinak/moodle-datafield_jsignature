define(['jquery', 'mod_printit/jqueryjsignature'], function ($) {
    return {
        init: function (field_id, color, backgroundColor) {
            let signatureInput = $("#" + field_id).attr('style','display:none !important');
            let defaultSignature = signatureInput.val()
            let signatureWidget = $('<div class="jsignaturefield_editor printit-jsignature-editor" id="jsignature' + field_id + '">')
                .insertAfter(signatureInput)
                .jSignature({
                    'background-color': backgroundColor,
                    'sizeRatio' : 3,
                    'color': color,
                    'readOnly': signatureInput.is(':disabled'),
                    'signatureLine': true,
                    'showUndoButton': true,
                })
            if (defaultSignature) {
                signatureWidget.jSignature('setData', defaultSignature, 'base30')
            }
            signatureWidget.bind('change', function () {
                let data = signatureWidget.jSignature("getData", "base30")[1]
                signatureInput.val(data)
            })
        }
    }
})
