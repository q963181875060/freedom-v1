<?php
include_once '../aliyun-php-sdk-core/Config.php';
use Mts\Request\V20140618 as Mts;

$iClientProfile = DefaultProfile::getProfile("cn-hangzhou", "d1ag0W9NOvcblxmE", "xc0tCLFGPMqBaoietIAQj0jFR4JuHd");
$client = new DefaultAcsClient($iClientProfile);
$request = new Mts\SubmitSnapshotJobRequest();

$input = "{\n" .
    "    \"Bucket\": \"freedom-owen\",\n" .
    "    \"Location\": \"oss-cn-hangzhou\",\n" .
    "    \"Object\": \"video/test.mp4\"\n" .
    "  }";

$SnapshotConfig = "{\n" .
    "    \"OutputFile\": \n" .
    "      {\n" .
    "        \"Bucket\": \"freedom-owen\",\n" .
    "        \"Location\": \"oss-cn-hangzhou\",\n" .
    "        \"Object\": \"thumb/test.jpg\"\n" .
    "      }, \n" .
    "    \"Time\": \"5\"\n" .
    "  }";

$request = new Mts\SubmitSnapShotJobRequest();
$request->setInput($input);
$request->setMethod("GET");
$request->setSnapshotConfig($SnapshotConfig);
$response = $client->getAcsResponse($request);
print_r($response);
?>