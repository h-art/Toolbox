<?php
namespace Hart\Utility;

class StringUtils
{
  
  public static function removeEmoji($text) {

    $clean_text = "";

    // Match Emoticons
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clean_text = preg_replace($regexEmoticons, '', $text);

    // Match Miscellaneous Symbols and Pictographs
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clean_text = preg_replace($regexSymbols, '', $clean_text);

    // Match Transport And Map Symbols
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clean_text = preg_replace($regexTransport, '', $clean_text);

    //Match enclosed characters
    $regexSymbols = '/[\x{1F170}-\x{1F251}]/u';
    $clean_text = preg_replace($regexSymbols, '', $clean_text);

    return $clean_text;
  }

  public static function sluggify($text)
  {
    $text = str_replace(" ", "-", $text);
    $text = preg_replace('/[^\w\d\-\_]/i', '', $text);
    return strtolower($text);
  }
  
}