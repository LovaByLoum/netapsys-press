var WPAU_Upload;
jQuery(document).ready(function ($) {
    WPAU_Upload = {
        init:function (id) {
            window.wpauUploadCount = typeof(window.wpauUploadCount) == 'undefined' ? 0 : window.wpauUploadCount;
            this.maxFiles = parseInt(wpau_upload.number);

            $('.wpau-upload-imagelist', $('#wpau-upload-container-'+id)).on('click', 'a.action-delete', this.removeUploads);

            this.attach(id);
            this.hideUploader(id);
        },
        attach:function (id) {
            // wordpress plupload if not found
            if (typeof(plupload) === 'undefined') {
                return;
            }

            if (wpau_upload.upload_enabled !== '1') {
                return
            }

            wpau_upload.plupload.browse_button = 'wpau-uploader-'+id;
            wpau_upload.plupload.container = 'wpau-upload-container-'+id;
            //wpau_upload.plupload.file_data_name = 'wpau_upload_file_'+id;
            var uploader = new plupload.Uploader(wpau_upload.plupload);

            $('#wpau-uploader-'+id).click(function (e) {
                uploader.start();
                // To prevent default behavior of a tag
                e.preventDefault();
            });

            //initilize  wp plupload
            uploader.init();

            uploader.bind('FilesAdded', function (up, files) {
                $.each(files, function (i, file) {
                    $('.wpau-upload-imagelist', $('#wpau-upload-container-'+id)).append(
                        '<div id="' + file.id + '">' +
                            file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
                            '</div>');
                });

                up.refresh(); // Reposition Flash/Silverlight
                uploader.start();
            });

            uploader.bind('UploadProgress', function (up, file) {
                $('#' + file.id + " b").html(file.percent + "%");
            });

            // On erro occur
            uploader.bind('Error', function (up, err) {
                $('.wpau-upload-imagelist', $('#wpau-upload-container-'+id)).append("<div>Error: " + err.code +
                    ", Message: " + err.message +
                    (err.file ? ", File: " + err.file.name : "") +
                    "</div>"
                );

                up.refresh(); // Reposition Flash/Silverlight
            });

            uploader.bind('FileUploaded', function (up, file, response) {
                var result = $.parseJSON(response.response);

                _html='<li class="wpau-uploaded-files">'+
                        '<img src="' + result.path + '/' + result.image + '" name="' + result.postname + '" />'+
                          '<br /><a href="#" class="action-delete" data-upload_id="' + result.attach_id +'">Supprimer</a></span>'+
                            '<input type="hidden" name="wpau_image_id_'+id+'[]" value="' + result.attach_id+ '" />' +
                            '<br /><a href="javascript:;" class="action-validate" data-upload_id="' + result.attach_id+'">OK</a></span>'+
                    '</li>';

                $('#' + file.id).remove();
                if (result.success) {
                    window.wpauUploadCount += 1;
                    $('.wpau-upload-imagelist ul', $('#wpau-upload-container-'+id)).append(_html);

                    WPAU_Upload.hideUploader(id);
                }
            });


        },

        hideUploader:function (id) {

            if (WPAU_Upload.maxFiles !== 0 && window.wpauUploadCount >= WPAU_Upload.maxFiles) {
                $('#wpau-uploader-'+id).hide();
            }
        },

        removeUploads:function (e) {
            e.preventDefault();

            //if (confirm(wpau_upload.confirmMsg)) {

                var el = $(this),
                    data = {
                        'attach_id':el.data('upload_id'),
                        'nonce':wpau_upload.remove,
                        'action':'wpau_delete'
                    };

                $.post(wpau_upload.ajaxurl, data, function () {
                    el.parent().remove();

                    window.wpauUploadCount -= 1;
                    if (WPAU_Upload.maxFiles !== 0 && window.wpauUploadCount < WPAU_Upload.maxFiles) {
                        el.parents('.wpau-upload-container').find('.wpau_button').show();
                    }
                });
            }
        //}

    };

    $('.wpau_button').livequery(function(){
        WPAU_Upload.init($(this).data('id'));
    });
});