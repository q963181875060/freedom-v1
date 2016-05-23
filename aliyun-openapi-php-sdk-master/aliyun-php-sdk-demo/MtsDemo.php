<?php
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
include_once '../aliyun-php-sdk-core/Config.php';
use Mts\Request\V20140618 as Mts;

$iClientProfile = DefaultProfile::getProfile("cn-beijing", "d1ag0W9NOvcblxmE", "xc0tCLFGPMqBaoietIAQj0jFR4JuHd");
$client = new DefaultAcsClient($iClientProfile);
$input = "{\n" .
    "    \"Bucket\": \"mifit\",\n" .
    "    \"Location\": \"oss-cn-beijing\",\n" .
    "    \"Object\": \"test/dSbAECXS83.mp4\"\n" .
    "  }";

$SnapshotConfig = "{\n" .
    "    \"OutputFile\": \n" .
    "      {\n" .
    "        \"Bucket\": \"mifit\",\n" .
    "        \"Location\": \"oss-cn-beijing\",\n" .
    "        \"Object\": \"fuc111k.jpg\"\n" .
    "      }, \n" .
    "    \"Time\": \"5\"\n" .
    "  }";
try{
    $request = new Mts\SubmitSnapShotJobRequest();   
}catch(Exception $e){
    echo $e->getMessage();
}


$request->setInput($input);
$request->setMethod("GET");
$request->setSnapshotConfig($SnapshotConfig);

$response = $client->getAcsResponse($request);
print_r($response);