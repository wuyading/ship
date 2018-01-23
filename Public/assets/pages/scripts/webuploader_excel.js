jQuery(function() {
    $list = $('#thelist'),
        $btn = $('#ctlBtn'),
        state = 'pending',
        uploader;

    uploader = WebUploader.create({
        // 不压缩image
        resize: false,
        // swf文件路径
        swf: '__PUBLIC__/assets/global/plugins/webuploader/uploader.swf',
        // 文件接收服务端。
        server: UPLOAD_URL,
        // 选择文件的按钮。可选。
        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
        pick: '#picker',
        chunked: true,
        chunkSize: 2 * 1024 * 1024,
        auto: true,
        accept: {
            title: '表格上传',
            extensions: 'xlsx,xls',
            mimeTypes: 'application/msexcel'
        }
    });

    uploader.on( 'fileQueued', function( file ) {
        $list.append( '<div id="' + file.id + '" class="file-item thumbnail">' +
            '<input type="hidden" class="file_path" name="files[]" value="">' +
            '<div class="info">' + file.name + '</div>' +
            '</div>' );
    });

    // 文件上传过程中创建进度条实时显示。
    uploader.on( 'uploadProgress', function( file, percentage ) {
        var $li = $( '#'+file.id ),
            $percent = $li.find('.progress .progress-bar');

        // 避免重复创建
        if ( !$percent.length ) {
            $percent = $('<div class="progress progress-striped active">' +
                '<div class="progress-bar" role="progressbar" style="width: 0%">' +
                '</div>' +
                '</div>').appendTo( $li ).find('.progress-bar');
        }

        $li.find('p.state').text('上传中');

        $percent.css( 'width', percentage * 100 + '%' );
    });

    // 文件上传成功，给item添加成功class, 用样式标记上传成功。
    uploader.on( 'uploadSuccess', function( file ,response) {
        $( '#'+file.id ).addClass('upload-state-done');
        $( '#'+file.id ).find('.img_path').val(response.img);
    });

    // 文件上传失败，显示上传出错。
    uploader.on( 'uploadError', function( file ) {
        console.log(file.id);
        var $li = $( '#'+file.id ),
            $error = $li.find('div.error');

        // 避免重复创建
        if ( !$error.length ) {
            $error = $('<div class="error"></div>').appendTo( $li );
        }

        $error.text('上传失败');
    });

    // 完成上传完了，成功或者失败，先删除进度条。
    uploader.on( 'uploadComplete', function( file ) {
        $( '#'+file.id ).find('.progress').remove();
    });
    $btn.on( 'click', function() {
        console.log("上传...");
        uploader.upload();
        console.log("上传成功");
    });
});