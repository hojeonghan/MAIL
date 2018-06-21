<?php

include "mta.php";

$mta = new Multipart_generator;

$boundary = $mta->boundary();
$mid = $mta->msgid();

$prevPage = $_SERVER['HTTP_REFERER'];
$from = $_POST["from"];
$to = $_POST["to"];
$cc = $_POST["cc"];
$bcc = $_POST["bcc"];
$subject = $mta->encode($_POST["subject"]);
$body = $_POST["body"];
$limit = 300;

$headers = $mta->header($mid, $from, $to, $boundary, $cc, $bcc);

$initbody = "--$boundary\r\n" . 
"Content-Type: text/html; charset=\"utf-8\"\r\n" .
"Content-Transfer-Encoding: base64\r\n\r\n" .
chunk_split(base64_encode($body), 76, "\r\n")."\r\n\r\n";


$file = $_FILES['myfile']['name'];
$count = count($file);

if( ! is_array ($file) )
	return '';
if( $count == 0 )
	return '';
for($i=0; $i < $count; $i++) {
	$name = $mta->encode($_FILES['myfile']['name'][$i]);
        $uploads_dir='./uploads';
        $path = $uploads_dir.'/'.$name;
        move_uploaded_file( $_FILES['myfile']['tmp_name'][$i], "$path");
	$type = $mta->mime($path);

	$bin_file = file_get_contents($path);
	$hex_file = base64_encode($bin_file);
	
	if($_FILES['myfile']['size'][$i] != 0){
	$status = isset($_FILES['myfile']['size'][$i]);	
        $filebody .= "--$boundary\r\n" 
		."Content-Type: ".$type."; name=\"".$name."\"\r\n"
		."Content-Transfer-Encoding: base64\r\n"
		."Content-Disposition: attachment; filename=\"".$name."\"\r\n\r\n"
		.chunk_split(($hex_file), 76, "\r\n")."\r\n\r\n";
	}
	
}

$mailbody = $initbody . $filebody;
echo "<pre>\n";
echo $headers;
echo $mailbody;
echo $boundary . "--";


if ( empty($cc) && empty($bcc) ){
	if(!mail($to,addslashes($subject),$mailbody,$headers)) back("이메일 발송해 실패 하였습니다.");
	else
	{
	        echo "<script>alert('메일을 발송하였습니다.');history.back(1);</script>";
	        exit;
	}
} else {
	if ( !empty($cc) ){
		if(!mail($cc,addslashes($subject),$mailbody,$headers)) back("이메일 발송해 실패 하였습니다.");
	        else
        	{
                	echo "<script>alert('메일을 발송하였습니다.');history.back(1);</script>";
	                exit;
        	}
		if ( !empty($bcc) ){
			if(!mail($bcc,addslashes($subject),$mailbody,$headers)) back("이메일 발송해 실패 하였습니다.");
        	        else
	                {
                	        echo "<script>alert('메일을 발송하였습니다.');history.back(1);</script>";
                        	exit;
                	}	
	
		}
	}

} 
?>
