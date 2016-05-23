<?php
    include_once(dirname(__FILE__) . '/alibaba_config.php');

    function gmt_iso8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new DateTime($dtStr);
        $expiration = $mydatetime->format(DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        if($pos != false){
            $expiration = substr($expiration, 0, $pos);
        }else{

            $expiration = substr($expiration, 0, 10) . 'T' . ((int)substr($expiration, 11, 12) + 12) . substr($expiration, 13, -5);
        }
        return $expiration."Z";
    }

    // Verify if user registrations are enabled
    $config = Registry::get('config');
    if (!$config->enableUserUploads) App::throw404();

    // Verify if user is logged in
    $userService = new UserService();
    $loggedInUser = $userService->loginCheck();
    Functions::RedirectIf($loggedInUser, HOST . '/login/');

    // Establish page variables, objects, arrays, etc
    App::EnableUploadsCheck();
    $videoMapper = new VideoMapper();
    $this->view->options->disableView = true;
    $config = Registry::get('config');

    // Retrieve video information
    if (!isset ($_SESSION['upload'])) App::Throw404();

    // Validate video
    $video = $videoMapper->getVideoByCustom(array('video_id' => $_SESSION['upload'], 'status' => 'new'));
    if (!$video) {
        header('Location: ' . HOST . '/account/upload/');
        exit();
    }

    $now = time();
    $expire = 30; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
    $end = $now + $expire;
    $expiration = gmt_iso8601($end);

    $dir = $video_dir;

    //最大文件大小.用户可以自己设置
    $condition = array(0=>'content-length-range', 1=>0, 2=>1048576000);//$config->videoSizeLimit);
    $conditions[] = $condition; 

    //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
    $start = array(0=>'starts-with', 1=>'$key', 2=>$dir);
    $conditions[] = $start; 


    $arr = array('expiration'=>$expiration,'conditions'=>$conditions);
    //echo json_encode($arr);
    //return;
    $policy = json_encode($arr);
    $base64_policy = base64_encode($policy);
    $string_to_sign = $base64_policy;
    $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

    $response = array();
    $response['accessid'] = $id;
    $response['host'] = $host;
    $response['policy'] = $base64_policy;
    $response['signature'] = $signature;
    $response['expire'] = $end;
    //这个参数是设置用户上传指定的前缀
    $response['dir'] = $dir;
    $response['expiration'] = $expiration;
    $response['filename'] = $video->filename;
    echo json_encode($response);
?>
