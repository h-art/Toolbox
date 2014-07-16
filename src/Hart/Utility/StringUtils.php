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
    $text = self::removeEmoji($text);
    $text = self::replaceAccentedCharacters($text);
    $text = str_replace(" ", "-", trim($text));
    $text = preg_replace('/[^\w\d\-\_]/i', '', $text);

    return strtolower($text);
  }

  /**
 * Replace language-specific characters by ASCII-equivalents.
 * @param string $s
 * @return string
 */
  public static function replaceAccentedCharacters($s) {
      $replace = array(
          'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'Ae', 'Å'=>'A', 'Æ'=>'A', 'Ă'=>'A', 'Ą' => 'A', 'ą' => 'a',
          'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'ae', 'å'=>'a', 'ă'=>'a', 'æ'=>'ae',
          'þ'=>'b', 'Þ'=>'B',
          'Ç'=>'C', 'ç'=>'c', 'Ć' => 'C', 'ć' => 'c',
          'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ę' => 'E', 'ę' => 'e',
          'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 
          'Ğ'=>'G', 'ğ'=>'g',
          'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'İ'=>'I', 'ı'=>'i', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
          'Ł' => 'L', 'ł' => 'l',
          'Ñ'=>'N', 'Ń' => 'N', 'ń' => 'n',
          'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'Oe', 'Ø'=>'O', 'ö'=>'oe', 'ø'=>'o',
          'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
          'Š'=>'S', 'š'=>'s', 'Ş'=>'S', 'ș'=>'s', 'Ș'=>'S', 'ş'=>'s', 'ß'=>'ss', 'Ś' => 'S', 'ś' => 's',
          'ț'=>'t', 'Ț'=>'T',
          'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'Ue',
          'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'ue', 
          'Ý'=>'Y',
          'ý'=>'y', 'ý'=>'y', 'ÿ'=>'y',
          'Ž'=>'Z', 'ž'=>'z', 'Ż' => 'Z', 'ż' => 'z', 'Ź' => 'Z', 'ź' => 'z'
      );
      return strtr($s, $replace);
  }
  
}