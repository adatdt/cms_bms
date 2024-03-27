<?php

/*
  Document   : nutech_helper
  Created on : Jul 25, 2018 5:18:36 PM
  Author     : Andedi
  Description: Purpose of the PHP File follows.
 */

function idr_currency($nominal) {
  return number_format($nominal, 0, ',', '.');
}

function format_time($time)
{
	return date("H:i",strtotime ($time));
}

function format_date($date)
{
	return date("d F Y ", strtotime($date));
}

function format_dateTime($date)
{
	return date("d F Y H:i", strtotime($date));
}
function format_dateTimeSlash($date)
{
  return date("d/m/Y H:i:s", strtotime($date));
}
function format_recent($seconds){
  $string = "";

  $days = intval(intval($seconds) / (3600*24));
  $hours = (intval($seconds) / 3600) % 24;
  $minutes = (intval($seconds) / 60) % 60;
  $seconds = (intval($seconds)) % 60;

  if($days> 0){
      $string .= "$days days ";
  }
  if($hours > 0){
      $string .= "$hours hours ";
  }
  if($minutes > 0){
      $string .= "$minutes mins ";
  }
  if ($seconds > 0){
      //$string .= "$seconds seconds";
  }

  return $string;
}
function success_color($text)
{
	return "<font color='#00FF00'><b>".$text."</font></b>";
}
function failed_color($text)
{
  return "<font color='red'><b>".$text."</font></b>";
}
function pending_color($text)
{
	return "<font color='orange'><b>".$text."</font></b>";
}

function is_phone_number($phone) {
  $prefix = '+628';
  if (substr($phone, 0, 4) == $prefix) {
    return TRUE;
  } else {
    return FALSE;
  }
}

function save_base64img($base64_string, $save_path) {
  $imageData = base64_decode($base64_string);
  $source = imagecreatefromstring($imageData);
  $save = imagejpeg($source, $save_path, 86);
  imagedestroy($source);

  return $save;
}

function generate_ymd_dir($path, $y = '', $m = '', $d = '') {
  $this->load->helper('file');


  if (!file_exists($path . '/index.html')) {
    write_file($path . '/index.html', '');
  }

  $y = ($y == '') ? date('Y') : $y;
  $y = $path . '/' . $y;
  if (!is_dir($y)) {
    if (mkdir($y, 0755, TRUE))
      write_file($y . '/index.html', '');
  }

  $m = ($m == '') ? date('m') : $m;
  $ym = $y . '/' . $m;
  if (!is_dir($ym)) {
    if (mkdir($ym, 0755, TRUE))
      write_file($ym . '/index.html', '');
  }

  $d = ($d == '') ? date('d') : $d;
  $ymd = $y . '/' . $m . '/' . $d;
  if (!is_dir($ymd)) {
    if (mkdir($ymd, 0755, TRUE))
      write_file($ymd . '/index.html', '');
  }

  return $ymd;
}

?>
