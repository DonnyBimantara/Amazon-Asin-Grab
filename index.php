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
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<title>Amazon</title>
	
	<script type="text/javascript" src="jquery-1.7.1.min.js"></script>
</head>

<body style="background:#f7f7f7;margin: 0;padding: 0;" dir="ltr">

<style>
/* progress bar container */
#progressbar{
        border:1px solid black;
        width:500px;
        height:30px;
        position:relative;
        color:black; 
}
/* color bar */
#progressbar div.progress{
        position:absolute;
        width:0;
        height:100%;
        overflow:hidden;
        background-color:#369;
}
/* text on bar */
#progressbar div.progress .text{
        position:absolute;
        text-align:center;
        color:white;
}
/* text off bar */
#progressbar div.text{
        position:absolute;
        width:100%;
        height:100%;
        text-align:center;
}

#monitoring
{
	font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
	font-size: 12px;
	background: #fff;
	margin: 45px 5px 10px 5px;
	width: 100%;
	border-collapse: collapse;
	text-align: left;
}
#monitoring th
{
	font-size: 14px;
	font-weight: normal;
	color: #039;
	padding: 10px 8px;
	border-bottom: 2px solid #6678b1;
}
#monitoring td
{
	border-bottom: 1px solid #ccc;
	color: #669;
	padding: 3px 4px;
}
#monitoring tbody tr:hover td
{
	color: #009;
}
</style>
<table width="50%" border="0" cellspacing="0" cellpadding="40" style="margin-left:20%">
	<tr>
		<td bgcolor="#f7f7f7" width="100%" style="font-family:'lucida grande',tahoma,verdana,arial,sans-serif;">
			<table cellpadding="0" cellspacing="0" border="0" width="620">
				<tr>
					<td style="background:#3b5998;color:#FFFFFF;font-weight:bold;font-family:'lucida grande',tahoma,verdana,arial,sans-serif;vertical-align:middle;padding:4px 8px; font-size: 16px; letter-spacing: -0.03em; text-align:left;">
						Amazon ASIN Grab
					</td>
					<td style="background:#3b5998;color:#FFFFFF;font-weight:bold;font-family:'lucida grande',tahoma,verdana,arial,sans-serif;vertical-align:middle;padding:4px 8px;font-size: 11px; text-align: right;">
					</td>
				</tr>
				
				<tr>
					<td colspan="2" style="background-color: #FFFFFF; border-bottom: 1px solid #3b5998; border-left: 1px solid #CCCCCC; border-right: 1px solid #CCCCCC; font-family:'lucida grande',tahoma,verdana,arial,sans-serif; padding: 15px;" valign="top">
						<table width="100%">
							<tr>
								<td width="100%" style="font-size:12px;" valign="top" align="left">
									<div style="margin-bottom:15px; font-size:12px;">
										<form id="form" action="grab.php">
											<table>
												<tr>
													<td >URL</td>
													<td colspan=3><input type="text" id="url" name="url" size="80" title="Masukkan alamat url dari Amazon sesuai pencarian Anda" /></td>
												</tr>
												<tr>
													<td>File&nbsp;Name</td>
													<td><input type="text" id="file" name="file" size="10" title="Masukan nama file" /></td>
												</tr>
												<tr>
													<td colspan=4 align="right">
														<a id="download" href="" target="_blank" style="background:#3b5998;cursor:pointer; border:none; font-size: 11px; line-height: 26px; font-weight: bold; text-transform: uppercase; color: #fff; display: inline-block; padding: 0 12px;">Download</a>
														<input type="submit" value="GET ASIN" style="background:#3b5998;cursor:pointer; border:none; font-size: 11px; line-height: 26px; font-weight: bold; text-transform: uppercase; color: #fff; display: inline-block; padding: 0 12px;" />
													</td>
												</tr>
											</table>
										</form>
									</div>
									<div id="result" style="margin-bottom:15px;">
										<center>
											<p id="textprogress"></p>
											<div id="progressbar"></div>
										</center>
									</div>
									
									<div id="monitor" style="display:none;margin-bottom:15px; margin: 0;">
										<table id="monitoring" width="100%">
											<thead>
												<th>Page</th>
												<th>URL</th>
												<th>HTTP Respon</th>
												<th>ASIN</th>
												<th>STATUS</th>
											</thead>
												
											<tbody>
											
											</tbody>
										</table>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<script>

(function($) {	
	//Main Method
	$.fn.reportprogress = function(val,maxVal) {			
		var max=100;
		if(maxVal)
			max=maxVal;
		return this.each(
			function(){		
				var div=$(this);
				var innerdiv=div.find(".progress");
				
				if(innerdiv.length!=1){						
					innerdiv=$("<div class='progress'></div>");					
					div.append("<div class='text'>&nbsp;</div>");
					$("<span class='text'>&nbsp;</span>").css("width",div.width()).appendTo(innerdiv);					
					div.append(innerdiv);					
				}
				var width=Math.round(val/max*100);
				innerdiv.css("width",width+"%");	
				div.find(".text").html(width+" %");
			}
		);
	};
})(jQuery);

var set, run=0, loop=0,temp=0,process=false;

$(document).ready(function(){
	$("#download").hide();
	$("#progressbar").reportprogress(0);
	
	$("#form").submit(function(){
		if(process == false){
			$("#download").hide();
			process = true;
			
			$("#progressbar").reportprogress(0);
				
			if(isValidURL($("#url").val())){
				check($("#url").val());
			}else{
				alert("URL yang Anda masukan tidak valid.");
				process = false;
			}
			
		}else{
			alert("Maaf, Proses sedang berlangsung...");
		}
		
		return false;
	});
});

function check(url){
	$.ajax({
		url: "check.php",
		data: {url:$("#url").val()},
		beforeSend : function(){
			$("#textprogress").text("Melakukan pengecekan URL ...");
		},
		success: function(respon){
			var obj = jQuery.parseJSON(respon);
			//console.log(respon);
			
			if(obj.status == 'success'){
			
				var limit = parseInt(obj.item);
				var total = parseInt(obj.total);
				var page = (total%limit)==0?parseInt((total/limit)):parseInt((total/limit))+1;
				var param = getParam($("#url").val());
				var file = $("#file").val();
				
				loop = page;
				temp = 0;
				run = 0;
				
				//console.log(page);
				
				$("#monitor").show();
				$("table#monitoring tbody").children().remove();
				for(var i=1;i<=page;i++){
					$("table#monitoring tbody").append('<tr id="'+i+'">'+td(i, getUrlTarget($("#url").val(),i), "", "", "Loading...")+'</tr>');
					grab(i,limit,param,file);
				}
			}else{
				$("#textprogress").text("Maaf, URL yang Anda masukan tidak memiliki hasil pencarian...");
				process = false;
			}
		},
		error: function(jqXHR,error, errorThrown) {
			check(url);
		},
		type: "POST"
	});
}

function td(page, url, code, asin, status){
	var html = "<td>"+page+"</td>"
				+"<td><a href='"+url+"' target='_blank'>Halaman "+page+"</a></td>"
				+"<td>"+code+"</td>"
				+"<td>"+asin+"</td>"
				+"<td>"+status+"</td>";
		
	return html;
}

function grab(page,limit,param,file){
	$.ajax({
		url: "grab.php",
		data: {page:page,limit:limit,param:param,file:file},
		beforeSend : function(){
			$("#textprogress").text("Sedang mendapatkan ASIN ...");
		},
		success: function(respon){
			var obj = jQuery.parseJSON(respon);
			//console.log(respon);
			
			if(obj.status == 'success'){
				$("tr#"+obj.page).children().remove();
				$("tr#"+obj.page).append(td(obj.page, obj.url, obj.http_code, obj.asin+" ASIN", obj.status));
				
				temp = temp + 1;
				//console.log(temp);
				run = (temp/loop)*100;
				//console.log(run);
				$("#progressbar").reportprogress(run);
				
				if(run == 100){
					clearInterval(set);
					$("a#download").attr("href", "result/"+file+".txt");
					$("a#download").show();
					//$("#download").append('<a href="result/'+file+'.txt" target="_blank">Download Result '+file+'.txt</a><br />');
					$("#textprogress").text("Proses Selesai ...");
					process = false;
				}
			}else{
				grab(page,limit,param,file);
			}
		},
		error: function(jqXHR,error, errorThrown) {
				grab(page,limit,param,file);
        },
		type: "POST"
	});
}

function getParam(url) {
    var vars = [], hash;
    var hashes = url.slice(url.indexOf('?') + 1);
    return hashes;
}

function getUrlTarget(url, page) {
    var hash;
    var hashes = url.slice(url.indexOf('?') + 1).split('&');
	var param="";
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
		if(hash[0] != "page")
			param = param+hash[0]+"="+hash[1]+"&";
    }
	url = "http://www.amazon.com/gp/search/ref=sr_pg_"+page+"?"+param+"page="+page;
	
    return url;
}

function isValidURL(url)
{
	var RegExp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
	
	if(RegExp.test(url)){
		return true;
	}else{
		return false;
	}
}
</script>
</body>
</html>
