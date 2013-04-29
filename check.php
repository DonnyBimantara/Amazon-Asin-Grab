<?php
/************************************************************************/
/* PHP ASIN TOOL                                                        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2013 oleh Donny Bimantara                              */
/* Email: donny.digimedia@gmail.com                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
$url = $_REQUEST['url'];

$respon = array();

$html="";
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

$html = getTag($html,'h2',"id","resultCount");
$html = str_replace(' ',"",$html);
$html = str_replace('<h2style=""class="resultCount"id="resultCount">',"",$html);
$html = str_replace('</h2>',"",$html);
$html = str_replace('<span>',"",$html);
$html = str_replace('</span>',"",$html);
$html = str_replace('Showing',"",$html);
$html = str_replace('Results',"",$html);
$html = str_replace('\n',"",$html);
$html = str_replace('\r',"",$html);

$exp = explode("of",$html);
$total = str_replace(',',"",$exp[1]);

$exp2 = explode("-",$exp[0]);

if(!isset($exp[1])){
	$total = $exp[0];
	$perpage = $exp[0];
}else{
	$perpage = (int)$exp2[1] - (int)$exp2[0] + 1;
}

$respon['item'] = $perpage;
$respon['total'] = $total;
//echo "Item per Page = $perpage dan Total Page = $total";

echo json_encode($respon);



function getTag($html, $tagName, $attrName, $attrValue ){
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
