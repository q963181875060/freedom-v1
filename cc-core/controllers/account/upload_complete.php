<?php

include_once dirname(__FILE__) . '/../../../aliyun-openapi-php-sdk-master/aliyun-php-sdk-core/Config.php';
include_once(dirname(__FILE__) . '/../ajax/alibaba/alibaba_config.php');
//include_once('../ajax/alibaba/alibaba_config.php');

use Mts\Request\V20140618 as Mts;
Plugin::triggerEvent('upload_complete.start');

// Verify if user registrations are enabled
$config = Registry::get('config');
if (!$config->enableUserUploads) App::throw404();

// Verify if user is logged in
$userService = new UserService();
$this->view->vars->loggedInUser = $userService->loginCheck();
Functions::RedirectIf($this->view->vars->loggedInUser, HOST . '/login/');

// Establish page variables, objects, arrays, etc
App::EnableUploadsCheck();

// Verify user completed upload process
if (isset($_SESSION['upload'])) {
    // Validate video
    $videoMapper = new VideoMapper();
    $video = $videoMapper->getVideoByCustom(array('video_id' => $_SESSION['upload'], 'status' => 'new'));
    if (!$video) {
        header('Location: ' . HOST . '/account/upload/');
        exit();
    }

    try {
        // Update video information
        $video->status = VideoMapper::APPROVED;
        //$video->filename = $_GET['filename'];

        // Validate video extension
        $extension = $_GET['extension'];
        if (!preg_match("/$extension/i", Functions::GetVideoTypes('fileDesc'))) {
            throw new Exception(Language::getText('error_upload_extension'));
        }
        $video->originalExtension = $extension;
        $videoMapper->save($video);

        $tmp = $regionId . $id .  $key;
        // generate thumb
        $iClientProfile = DefaultProfile::getProfile($regionId, $id, $key);
        $client = new DefaultAcsClient($iClientProfile);
        $request = new Mts\SubmitSnapshotJobRequest();

        $input = "{\n" .
            "    \"Bucket\": \"" . $Bucket . "\",\n" .
            "    \"Location\": \"" . $Location . "\",\n" .
            "    \"Object\": \"" . $video_dir . $video->filename . "\"\n" .
            "  }";
        $SnapshotConfig = "{\n" .
            "    \"OutputFile\": \n" .
            "      {\n" .
            "        \"Bucket\": \"" . $Bucket . "\",\n" .
            "        \"Location\": \"" . $Location . "\",\n" .
            "        \"Object\": \"" . $thumb_dir . $video->filename . ".jpg" . "\"\n" .
            "      }, \n" .
            "    \"Time\": \"2\"\n" .
            "  }";

        $request = new Mts\SubmitSnapShotJobRequest();
        $request->setInput($input);
        $request->setMethod("GET");
        $request->setSnapshotConfig($SnapshotConfig);
        $response = $client->getAcsResponse($request);
        //print_r($response);

        unset($_SESSION['upload']);
    }catch (Exception $e) {
        exit(json_encode(array('result' => false, 'message' => $e->getMessage())));
    }
} else {
    header('Location: ' . HOST . '/account/upload/video/');
    exit();
}

Plugin::triggerEvent('upload_complete.end');