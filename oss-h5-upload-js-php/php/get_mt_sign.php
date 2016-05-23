<?php
    include_once(dirname(__FILE__) . '/common.php');

    function gmt_iso8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new DateTime($dtStr);
        $expiration = $mydatetime->format(DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }


    $now = time();


    $HTTP_METHOD = 'GET';
    // 加入请求公共参数
    //$Action = 'SubmitSnapshotJob';
    $Action = 'SearchTemplate';
    $Version = '2014-06-18';
    $AccessKeyId = $id; //此处请替换成您自己的AccessKeyId
    //$TimeStamp = '2015-05-14T09:03:45Z';//此处将时间戳固定只是测试需要，这样此示例中生成的签名值就不会变，方便您对比验证，可变时间戳的生成需要用下边这句替换
    $TimeStamp = gmt_iso8601($now);
    $SignatureMethod = 'HMAC-SHA1';
    $SignatureVersion = '1.0';
    $SignatureNonce = '4902260a-516a-4b6a-a455-45b653cf6150';//此处将唯一随机数固定只是测试需要，这样此示例中生成的签名值就不会变，方便您对比验证，可变唯一随机数的生成需要用下边这句替换
    //$SignatureNonce = UUID.randomUUID().toString();
    $Format = 'XML';
    // 加入方法特有参数
    $PageSize = 2;
    $Input =  array('Bucket'=>$Bucket, 'Location'=>$Location, 'Object'=>$video_dir,$_GET['object']);

    $OutputFile = array('Bucket' => $Bucket, 'Location'=>$Location, 'Object'=>$thumb_dir,$_GET['object']);
    $SnapshotConfig = array('OutputFile' => $OutputFile, 'Time' => 5 );

    //$arr = array('AccessKeyId' => $AccessKeyId, 'Action' => $Action, 'Format' => $Format, 'HTTP_METHOD' => $HTTP_METHOD, 'Input' => $Input , 'SignatureMethod' => $SignatureMethod, 'SignatureNonce' => $SignatureNonce, 'SignatureVersion' => $SignatureVersion, 'SnapshotConfig' => $SnapshotConfig, 'TimeStamp' => $TimeStamp, 'Version' => $Version);
    $canonicalizedQueryString = 'AccessKeyId=' . rawurlencode($AccessKeyId) . '&Action=' . rawurlencode($Action) . '&Format=' . rawurlencode($Format) . '&PageSize=' . $PageSize;
    $canonicalizedQueryString .= '&SignatureMethod=' . rawurlencode($SignatureMethod);
    //$canonicalizedQueryString .= '&Input=' . rawurlencode(json_encode($Input)) . '&SignatureMethod=' . rawurlencode($SignatureMethod);
    $canonicalizedQueryString .= '&SignatureNonce=' . rawurlencode($SignatureNonce) . '&SignatureVersion='  . rawurlencode($SignatureVersion);
    //$canonicalizedQueryString .= '&SnapshotConfig=' . rawurlencode(json_encode($SnapshotConfig)) . '&TimeStamp=' . rawurlencode($TimeStamp) . '&Version=' . rawurlencode($Version);
    $canonicalizedQueryString .= '&TimeStamp=' . rawurlencode($TimeStamp) . '&Version=' . rawurlencode($Version);
    $string_to_sign = $HTTP_METHOD . '&' . rawurlencode('/') . '&' .  rawurlencode($canonicalizedQueryString);
    $base64_policy = base64_encode(hash_hmac('sha1', $string_to_sign, $key.'&', true));
    $signature = rawurlencode($base64_policy);
    $returnStr = $canonicalizedQueryString;

    $urlString = 'TimeStamp=' . rawurlencode($TimeStamp) . '&Format=' . rawurlencode($Format) . '&AccessKeyId=' . rawurlencode($AccessKeyId) . '&Action=' . rawurlencode($Action) ;
    //$urlString .= '&Input=' . rawurlencode(json_encode($Input)) . '&SignatureMethod=' . rawurlencode($SignatureMethod);
    $urlString .= '&SignatureMethod=' . rawurlencode($SignatureMethod);
    $urlString .= '&SignatureNonce=' . rawurlencode($SignatureNonce) . '&SignatureVersion='  . rawurlencode($SignatureVersion);
    //$urlString .= '&SnapshotConfig=' . rawurlencode(json_encode($SnapshotConfig)) . '&Version=' . rawurlencode($Version);
    $urlString .= '&Version=' . rawurlencode($Version)  . '&PageSize=' . $PageSize;


    $response = array();
    $response['policy'] = $urlString;
    $response['signature'] = $signature;
    echo json_encode($response);
?>
