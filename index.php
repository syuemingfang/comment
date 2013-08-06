<?php
/*!
# Comment to Markdown to HTML v0.1.0.0
 Copyright © 2013 MingFang. All rights reserved.
 syuemningfang@gmail.com
 Date: Tue Aug 06 2013 16:47:48 GMT+0800 (Central Standard Time)

## How to Use
   Copy code from the <head> and </head> tags and paste it on your page.
  <link href='gird100.css' /> 
****************************************************************************************************/
$str=null;
$matchStr=null;
$yourCode=null;
$markdown=null;
$html=null;
$filename=$_GET['filename'];
if(file_exists($filename)){
  $file=fopen($filename, 'r');
  if($file != null){
    while(!feof($file)){
      $str.=fgets($file);
    }
    fclose($file);
  }
}
$yourCode=$str;
// Markdown //
$markdown=$str;
//$markdown=preg_replace('/\n/i', '<br />', $str);
$p='/[\/]+[\*]+[\!]+(.*?)[\*]+[\/]+/is';
preg_match_all($p, $markdown, $matchStr);

// HTML //
for($i=0; $i < count($matchStr); $i++){
//$html.=preg_replace('/######(.*?)/i', '<h6>$1</h6>', $matchStr[1][$i]);
//$html.=preg_replace('/#####(.*?)/i', '<h5>$1</h5>', $matchStr[1][$i]);
//$html.=preg_replace('/####(.*?)/i', '<h4>$1</h4>', $matchStr[1][$i]);
//$html.=preg_replace('/###(.*?)/i', '<h3>$1</h3>', $matchStr[1][$i]);
$html.=preg_replace('/##(.*?)/i', '<h2>$1</h2>', $matchStr[1][$i]);
$html.=preg_replace('/#(.*?)/i', '<h1>$1</h1>', $matchStr[1][$i]);
}

/*
$file=fopen($filename.'_markdown', "a+");
fwrite($file, $newStr);
fclose($file);
*/
?>
<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8">
  <title>Comment to Markdown</title>
</head>
<body>
<h1>Comment to Markdown</h1>
<h2>Your Code</h2>
<pre>
<?php
  echo $yourCode;
?>
</pre>
<hr />
<h2>Markdown</h2>
<pre>
<?php
for($i=0; $i < count($matchStr); $i++){
  echo $matchStr[1][$i];
}
?>
</pre>
<hr />
<h2>HTML</h2>
<?php
  echo $html;
?>
</body>
</html>
