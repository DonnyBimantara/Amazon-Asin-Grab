<?php
/************************************************************************/
/* PHP ASIN TOOL                                                        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2012 oleh Donny Bimantara                              */
/* Email: donny.digimedia@gmail.com                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
$page = $_REQUEST['page'];
$limit = $_REQUEST['limit'];
$param = $_REQUEST['param'];
$file = $_REQUEST['file'];

$respon = array();
$arr_param = array();

$expparam = explode("&",$param);
foreach($expparam as $row){
  $itemparam = explode("=",$row);
	
	if(count($itemparam) == 2)
		$arr_param[$itemparam[0]] = $itemparam[1];
}

$arr_param['page'] = $page;

$param="";
foreach($arr_param as $key=>$value){
	$param .= "$key=$value&";
}

$param = rtrim($param,"&");

$url = str_replace(" ","","http://www.amazon.com/gp/search/ref=sr_pg_$page?$param");

//$html = file_get_contents("data.txt");

$html="";
$respon['url'] = $url;

$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $url); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//curl_setopt($ch,CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);	
$html = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
//var_dump($info);

$respon['http_code'] = $info['http_code'];

if($info['http_code'] == 200){
	$respon['status'] = "success";
}else{
	$respon['status'] = "error";
}

$check=false;
$asin="Page $page\n";
$count = 0;
for($i=($page-1)*$limit;$i<$limit*$page;$i++){
	$string = getAsin($html,'div',"id","result_$i");

	preg_match('~name="([^"]+)"~', $string, $matches);
	if(isset($matches[1]) && $matches[1] != ''){
		$asin .= "$matches[1]\n";
		$check = true;
		$count++;
	}
}

//echo $asin;
if($check){
	createFile($file,$asin);
}else{
	$respon['status'] = "error";
}

$respon['page'] = $page;
$respon['asin'] = $count;

echo json_encode($respon);



function getAsin($html, $tagName, $attrName, $attrValue ){
	$dom = new DOMDocument('1.0', 'utf-8');
	@$dom->loadHTML($html);
    $domxpath = new DOMXPath($dom);
    $newDom = new DOMDocument;
    $newDom->formatOutput = true;

    $filtered = $domxpath->query("//$tagName" . '[@' . $attrName . "='$attrValue']");
    // $filtered =  $domxpath->query('//div[@class="className"]');
    // '//' when you don't know 'absolute' path

    // since above returns DomNodeList Object
    // I use following routine to convert it to string(html); copied it from someone's post in this site. Thank you.
    $i = 0;
    while( $myItem = $filtered->item($i++) ){
        $node = $newDom->importNode( $myItem, true );    // import node
        $newDom->appendChild($node);                    // append node
    }
    $html = $newDom->saveHTML();
	
    return $html;
}

function createFile($name,$asin){

	$file=dirname(__FILE__)."/result/".$name.".txt";
	if(!is_file($file)){
		$fp = fopen($file, 'w');
		fwrite($fp, $asin);
		fclose($fp);
	}else{
		$fp = fopen($file, 'a');
		fwrite($fp, $asin);
		fclose($fp);
	}
}
