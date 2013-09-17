<?php
/*!

# You Comment I Making
  Remove Somebody in HTML, CSS and Javascript. 
  [Getting Started](http://comment.cxm.tw) [GitHub project](https://github.com/syuemingfang/syuemingfang-comment)

****************************************************************************************************/

/*!
+ Version: 0.1.0.0
+ Copyright © 2013 [Syue](mailtot:syuemingfang@gmail.com). All rights reserved.
+ Date: *Tue Aug 06 2013 16:47:48 GMT+0800 (Central Standard Time)*
+ Includes:
  + PHP Markdown & Extra by Michel Fortin
  + PclZip

****************************************************************************************************/
//!
//!## Class
class Comment{
  //!### Comment
  public $filename_zip;
  public $filename_html;
  public $filename_markdown;  
  public $temp_dir;
  public function __construct(){
  }
  public function readFile($filename){
    //!+ **readFile**
    $str=null;
    if(file_exists($filename)){
      $file=fopen($filename, 'r');
      if($file != null){
        while(!feof($file)){
          $str.=fgets($file);
        }
        fclose($file);
      }
    }
    return $str;
  } 
  public function createMarkdown($filename, $str){
    //!+ **createMarkdown**
    // Start //
    $source=htmlentities($str, ENT_COMPAT, 'UTF-8');
    // Get Comment //
    $comment=$str;
    $p='/[\/]+[\*]{1,2}[\!]+(.*?)[\*]{0,2}[\/]+[\r\n]+|[\/]+[\!]+(.*?)[\r\n]+/is';
    preg_match_all($p, $comment, $match[0]);
    for($i=0; $i < count($match[0][1]); $i++){
      if($match[0][1][$i]) $raw_html.="\r\n".$match[0][1][$i];
      if($match[0][2][$i]) $raw_html.="\r\n".$match[0][2][$i];
    }
    // Write Markdown //
    $this->writeFile($this->temp_dir.'/'.$filename, $raw_html);
    return $raw_html;
  }
  public function createBootstrap($filename, $raw_html){
    //!+ **createBootstrap**
    $p=null;
    $r=null;
    $match=array();
    $html=null;
    // Comment to HTML //
    include_once 'markdown.php';
    $raw_html=Markdown($raw_html);
    // HTML To Bootstrap //
    $p='/<h1>(.*?)<\/h1>/is';
    preg_match_all($p, $raw_html, $match[1]);
    $p='/<h2>(.*?)<\/h2>/is';
    preg_match_all($p, $raw_html, $match[2]);
    // Get EM //
    $p='/<em>(.*?)<\/em>/is';
    preg_match_all($p, $raw_html, $match[3]);
    // Get Link //
    $p='/<a href=\"(.*?)\">(.*?)<\/a>/is';
    preg_match_all($p, $raw_html, $match[4]);
    $p='/(<h1>.*?)<hr \/>/is';
    $r='';
    preg_match_all($p, $raw_html, $match[5]);
    $raw_html=preg_replace($p, $r, $raw_html);
    $p='/<a href=\"(.*?)\">(.*?)<\/a>/is';
    $r='<a href="$1" class="btn btn-default">$2</a>';
    $match[5][1][0]=preg_replace($p, $r, $match[5][1][0]);
    $p='/<p>(.*?)<\/p>/is';
    $r='<p class="lead">$1</p>';
    $match[5][1][0]=preg_replace($p, $r, $match[5][1][0]);
    $GLOBALS['count']=-1;
    function replaceHead($r){
      $GLOBALS['count']++;
      return '<h2 id="head'.str_pad($GLOBALS['count'], 0, '0', STR_PAD_LEFT).'">'.$r[0].'</h2>';
    }
    $p='/<h2>(.*?)<\/h2>/is';
    $raw_html=preg_replace_callback($p, 'replaceHead', $raw_html);
    // Bootstrap //
    $html="<!DOCTYPE HTML>
    <html>
    <head>
      <meta charset='utf-8'>
      <title>You Comment I Making</title>
      <link rel='stylesheet' href='css/bootstrap.min.css' type='text/css'>
    </head>
    <body>
      <div class='navbar navbar-inverse navbar-fixed-top'>
        <div class='container'>
        <button class='navbar-toggle' type='button' data-toggle='collapse' data-target='.nav-collapse'>
          <span class='icon-bar'></span>
          <span class='icon-bar'></span>
          <span class='icon-bar'></span>
        </button>
          <div class='navbar-header'><a href='#' class='navbar-brand'>{$match[1][1][0]}</a></div>
          <div class='nav-collapse collapse'>
            <ul class='nav navbar-nav'>";
              for($i=0; $i < count($match[2][1]); $i++){
                $html.='<li><a href="#head'.$i.'">'.$match[2][1][$i].'</a></li>';
              }
              $html.="        </ul>
            </div>
          </div>
        </div>
        <div class='jumbotron'>
          <div class='container'>
          <div class='row'><div class='col-12'>&nbsp;</div></div>
            <div class='row'>
              <div class='col-12'>
                {$match[5][1][0]}
              </div>
            </div>
          </div>
          <div class='row'><div class='col-12'>&nbsp;</div></div>
        </div>
        <div class='container'>
          <div class='row'>
            <div class='col-12'>
              $raw_html
            </div>
          </div>
        </div>
        <script src='js/jquery.js'></script>
        <script src='js/bootstrap.min.js'></script>
      <div class='container'>
    <div class='container' role='contentinfo'>
    <div class='row'>
    <hr />
        <ul class='breadcrumb'>";
              for($i=0; $i < count($match[2][1]); $i++){
                $html.='<li><a href="#head'.$i.'">'.$match[2][1][$i].'</a></li>';
              }
  $html.="
        </ul>
      </div>
      </div>
    </body>
    </html>";
    // Write HTML //
    $this->writeFile($this->temp_dir.'/'.$filename, $html);
    return $html;
  }
  public function createZIP($filename, $files){
    //!+ **createZIP**
    unlink($filename);
    require_once('pclzip.lib.php');
    $archive=new PclZip($filename);
    for($i=0; $i < count($files); $i++){
      $archive->add($files[$i], PCLZIP_OPT_REMOVE_PATH, $this->temp_dir);
    }
  } 
  public function writeFile($filename, $str){
    //!+ **writeFile**
    $file=fopen($filename, 'w');
    fwrite($file, $str);
    fclose($file);
  } 
  public function getContent($url){
    //!+ **getContent**
    $ch=curl_init();
    $options=array(CURLOPT_URL => $url, CURLOPT_HEADER => false, CURLOPT_RETURNTRANSFER => true, CURLOPT_USERAGENT => "Google Bot", CURLOPT_SSL_VERIFYPEER => false, CURLOPT_FOLLOWLOCATION => true);
    curl_setopt_array($ch, $options);
    $content=curl_exec($ch)."\n";
    curl_close($ch);
    return $content;
  }
  public function start(){
    //!+ **start**
    $source=null;
    $comment=null;
    $raw_html=null;
    $arr=array();
    $filename=isset($_REQUEST['filename'])?$_REQUEST['filename']:false;
    $url=isset($_REQUEST['url'])?$_REQUEST['url']:false;
    $dir=isset($_REQUEST['dir'])?$_REQUEST['dir']:false;
    $md=isset($_REQUEST['md'])?$_REQUEST['md']:false;
    // Get Str
    if($filename){
      // by File
      $str=$this->readFile($this->temp_dir.'/'.$filename);
    } else if($url){
      // by URL
      $p='/comment\.json/is';
      preg_match_all($p, $url, $match);
      if($match){
        $p='/(http.*)\/.*/is';
        $r='$1';
        $dir=preg_replace($p, $r, $url);
        $json_str=$this->getContent($url);
        $json=json_decode($json_str, true);
        $str='';
        for($i=0; $i < count($json); $i++){
          $str.=$this->getContent($dir.'/'.$json[$i]);
        }
      } else{
        $str=$this->getContent($url);
      }
    } else if($md){
      $str=$this->readFile($this->temp_dir.'/'.$this->filename_markdown);
    } else{
      // by Upload
      foreach($_FILES['ff']['error'] as $key => $error){
          if($error == UPLOAD_ERR_OK){
              $tmp_name=$_FILES['ff']['tmp_name'][$key];
              $name=$_FILES['ff']['name'][$key];
              move_uploaded_file($tmp_name, $this->temp_dir.'/'.$name);
              array_push($arr, $this->temp_dir.'/'.$name);
          }
      }
      for($i=0; $i < count($arr); $i++){
        $file=fopen($arr[$i], 'r');
        if($file != null){
          while(!feof($file)){
            $str.=fgets($file);
          }
        fclose($file);
        }        
      }
      $this->writeFile($this->temp_dir.'/'.$this->filename_markdown, $str);
      exit;
    }
    $raw_html=$this->createMarkdown($this->filename_markdown, $str);
    $html=$this->createBootstrap($this->filename_html, $raw_html);
    $files=array($this->temp_dir.'/'.$this->filename_html, $this->temp_dir.'/'.$this->filename_markdown, 'js/jquery.js', 'js/bootstrap.min.js', 'css/bootstrap.min.css');
    $this->createZIP($this->temp_dir.'/'.$this->filename_zip, $files);
    echo $html;
    echo "<div class='row visible-lg'>
        <div class='col-7'>
          <strong>Power by Comment</strong><br />
          Thank you for useing our service.<br />
          if you want to Markdown or HTML file please click on the button.
        </div>
      <div class='col-5 text-right'>
        <a href='".$this->temp_dir.'/'.$this->filename_markdown."' class='btn btn-default'>Get Markdown</a>
        <a href='".$this->temp_dir.'/'.$this->filename_zip."' class='btn btn-info'>Get Source</a>
      </div>
    </div>";
  }
}

if(!isset($_REQUEST['ff']) && !isset($_REQUEST['md']) && !isset($_REQUEST['url'])){
  header('Content-type: text/html');
  require('main.html');
} else{
  $main=new Comment();
  $main->temp_dir='temp';
  $main->filename_zip='html.zip';
  $main->filename_markdown='README.md';  
  $main->filename_html='info.html';    
  $main->start();
}
?>