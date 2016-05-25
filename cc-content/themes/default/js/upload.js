
accessid = ''
accesskey = ''
host = ''
policyBase64 = ''
signature = ''
callbackbody = ''
filename = ''
extension = ''
dir = ''
expire = 0
//g_object_name = ''
//g_object_name_type = ''
expiration = ''

mt_policy = '';
mt_signature = '';

now = timestamp = Date.parse(new Date()) / 1000;

/**
 * Resets progress bar, enables browse button,
 * clears file title, and hides progress bar
 */
function resetProgress()
{
    $('#upload_status .fill').removeClass('in-progress');
    $('#upload_status').hide();
    $('#upload_status .title').text('');
    $('#upload_status .fill').css('width', '0%');
    $('#upload_status .percentage').text('0%');
}

function send_oss_sign_request()
{
    var xmlhttp = null;
    if (window.XMLHttpRequest)
    {
        xmlhttp=new XMLHttpRequest();
    }
    else if (window.ActiveXObject)
    {
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  
    if (xmlhttp!=null)
    {
        serverUrl = cumulusClips.baseUrl + '/ajax/alibaba/get-oss-sign';//?filename='+filename;
        xmlhttp.open( "GET", serverUrl, false );
        xmlhttp.send( null );
        return xmlhttp.responseText
    }
    else
    {
        alert("Your browser does not support XMLHTTP.");
    }
};

function get_oss_signature()
{
    //可以判断当前expire是否超过了当前时间,如果超过了当前时间,就重新取一下.3s 做为缓冲
    now = timestamp = Date.parse(new Date()) / 1000; 
    if (expire < now + 3)
    {
        body = send_oss_sign_request();
        var obj = eval ("(" + body + ")");
        host = obj['host']
        policyBase64 = obj['policy']
        accessid = obj['accessid']
        signature = obj['signature']
        expire = parseInt(obj['expire'])
        expiration = obj['expiration']
        callbackbody = obj['callback'] 
        dir = obj['dir']
        filename = obj['filename']
        return true;
    }
    return false;
};

/*
function random_string(len) {
　　len = len || 32;
　　var chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';   
　　var maxPos = chars.length;
　　var pwd = '';
　　for (i = 0; i < len; i++) {
    　　pwd += chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
}

function get_suffix(filename) {
    pos = filename.lastIndexOf('.')
    suffix = ''
    if (pos != -1) {
        suffix = filename.substring(pos)
    }
    return suffix;
}*/

/*function calculate_object_name(filename)
{
    if (g_object_name_type == 'local_name')
    {
        g_object_name += "${filename}"
    }
    else if (g_object_name_type == 'random_name')
    {
        suffix = get_suffix(filename)
        g_object_name = key + random_string(10) + suffix
    }
    return ''
}

function get_uploaded_object_name(filename)
{
    if (g_object_name_type == 'local_name')
    {
        tmp_name = g_object_name
        tmp_name = tmp_name.replace("${filename}", filename);
        return tmp_name
    }
    else if(g_object_name_type == 'random_name')
    {
        return g_object_name
    }
}*/

function set_upload_param(up, filename, ret)
{
    if (ret == false)
    {
        ret = get_oss_signature();
    }
    /*g_object_name = key;
    if (filename != '') {
        suffix = get_suffix(filename)
        calculate_object_name(filename)
    }*/
    new_multipart_params = {
        'key' : dir + filename,
        'policy': policyBase64,
        'OSSAccessKeyId': accessid, 
        'success_action_status' : '200', //让服务端返回200,不然，默认会返回204
        'callback' : callbackbody,
        'signature': signature,
    };

    up.setOption({
        'url': host,
        'multipart_params': new_multipart_params
    });

    up.start();
}

var uploader = new plupload.Uploader({
	runtimes : 'html5,flash,silverlight,html4',
	browse_button : 'upload-select-file',
    multi_selection: false,
	//container: document.getElementById('container'),
	//flash_swf_url : 'lib/plupload-2.1.2/js/Moxie.swf',
	//silverlight_xap_url : 'lib/plupload-2.1.2/js/Moxie.xap',
    url : 'http://oss.aliyuncs.com',

    filters: {
        mime_types : [ //只允许上传图片和zip,rar文件
        //{ title : "Image files", extensions : "jpg,gif,png,bmp" },
        //{ title : "Zip files", extensions : "zip,rar" },
        { title : "Video files", extensions : "flv,wmv,avi,ogg,mpg,mp4,mov,m4v,3gp,rmvb" }
        ],
        max_file_size : '100mb', //最大只能上传10mb的文件
        prevent_duplicates : true //不允许选取重复文件
    },

	init: {
		PostInit: function() {
			//document.getElementById('ossfile').innerHTML = '';
			document.getElementById('upload_button').onclick = function() {
                if(uploader.files.length < 1) return false;
                set_upload_param(uploader, '', false);
                return false;
			};
		},

		FilesAdded: function(up, data) {
			/*plupload.each(files, function(file) {
				document.getElementById('ossfile').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ')<b></b>'
				+'<div class="progress"><div class="progress-bar" style="width: 0%"></div></div>'
				+'</div>';
			});*/
            cumulusClips.uploadFileData = data;
            var file = data[0];
            /*var filesizeLimit;
            var filename = '';
            var callback;

            // Validate file type
            var filenameLower = file.name.toLowerCase();
            var matches = filenameLower.match(/\.[a-z0-9]+$/i);

            var fileTypes = $.parseJSON($('#file-types').val());
            var filesizeLimit = $('#upload-limit').val();
            if (!matches || $.inArray(matches[0].substr(1),fileTypes) == -1) {
                callback = function(data){
                    displayMessage(false, data);
                    window.scrollTo(0, 0);
                }
                getText(callback, 'error_upload_extension');
                return false;
            }

            // Validate filesize
            if (file.size > filesizeLimit) {
                callback = function(data){
                    displayMessage(false, data);
                    window.scrollTo(0, 0);
                }
                getText(callback, 'error_upload_filesize');
                return false;
            }*/

            // Prepare upload progress box
            $('.message').hide();
            $('#upload_status').show();
            $('#upload_status .fill').css('width', '0%');
            $('#upload_status .percentage').text('0%');

            // Set upload filename
            filename = file.name;
            extension = file.name.substring(file.name.lastIndexOf('.')+1);
            if (!cumulusClips.ie9) filename += ' (' + formatBytes(file.size, 0) + ')';
            $('#upload_status .title').text(filename);
		},

		BeforeUpload: function(up, file) {
            //check_object_radio();
            set_upload_param(up, filename, true);
        },

		UploadProgress: function(up, data) {
			/*var d = document.getElementById(file.id);
			d.getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
            var prog = d.getElementsByTagName('div')[0];
			var progBar = prog.getElementsByTagName('div')[0]
			progBar.style.width= 2*file.percent+'px';
			progBar.setAttribute('aria-valuenow', file.percent);*/
            var progress = parseInt(data.percent, 10);
            $('#upload_status .percentage').text(progress + '%');
            $('#upload_status .fill').css('width', progress + '%');
		},

		FileUploaded: function(up, data) {
           /* if (info.status == 200)
            {
                //document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = 'upload to oss success, object name:' + get_uploaded_object_name(file.name);
                document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = ;
            }
            else
            {
                document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = info.response;
            } */
            // Determine result from server validation
            /*response = data.result;
            if (data.result.result === true) {*/
                // Perform success actions based on what was being uploaded
                if ($('#upload-type').val() == 'avatar') {
                    resetProgress();
                    displayMessage(true, data.result.message);
                    window.scroll(0,0);
                    $('.avatar img').attr('src', data.result.other);
                } else {
                    top.location.href = cumulusClips.baseUrl + '/account/upload/complete?extension='+extension;
                }
            /*} else {
                resetProgress();
                displayMessage(false, data.result.message);
            }*/
		},
        FileFiltered: function(up, data) {
            up.splice(0, up.files.length-1);
        },

		Error: function(up, data) {
            /*if (err.code == -600) {
                document.getElementById('console').appendChild(document.createTextNode("\n选择的文件太大了,可以根据应用情况，在upload.js 设置一下上传的最大大小"));
            }
            else if (err.code == -601) {
                document.getElementById('console').appendChild(document.createTextNode("\n选择的文件后缀不对,可以根据应用情况，在upload.js进行设置可允许的上传文件类型"));
            }
            else if (err.code == -602) {
                document.getElementById('console').appendChild(document.createTextNode("\n这个文件已经上传过一遍了"));
            }
            else 
            {
                document.getElementById('console').appendChild(document.createTextNode("\nError xml:" + err.response));
            }*/
            var textEntry;
            var replacements = {host:cumulusClips.baseUrl};

            //Clean upload queue
            up.splice(0, up.files.length);

            // Determine reason for failure
            if (data.errorThrown === 'abort') {
                // Upload was cancelled (either via API or by user)
                return false;
            } else {
                textEntry = 'error_upload_system';
            }

            // Retrieve and output corresponding error text from language xml
            var callback = function(data){
                resetProgress();
                displayMessage(false, data);
                window.scroll(0,0);
            }
            getText(callback, textEntry, replacements);
		}
	}

});

uploader.init();

$(function(){
    // Attach upload event to upload button
    /*$('#upload_button').click(function(event){
        if (cumulusClips.uploadFileData !== undefined) {
            $('#upload_status .fill').addClass('in-progress');
            cumulusClips.jqXHR = cumulusClips.uploadFileData.submit();
        }
        event.preventDefault();
    });

    // Attach cancel event to cance button
    $('#upload_status a').click(function(event){
        if (cumulusClips.jqXHR !== undefined) {
            cumulusClips.jqXHR.abort();
        }
        resetProgress();
        $('#upload').val('');
        cumulusClips.jqXHR = undefined;
        cumulusClips.uploadFileData = undefined;
        event.preventDefault();
    });*/

    // Detect IE9
    if ($('meta[name="ie9"]').length > 0) {
        $('body').addClass('ie9');
        cumulusClips.ie9 = true;
        $('#upload_status .percentage').hide();
    } else {
        cumulusClips.ie9 = false;
    }
});