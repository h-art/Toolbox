<?php

namespace Hart\Utility;

class SimpleMobileDetector
{
  const TYPE_MOBILE = 'mobile';
  const TYPE_DESKTOP = 'desktop';

  public static function getType()
  {
    $ua = $_SERVER['HTTP_USER_AGENT'];

    if(preg_match('/(Android|BlackBerry|iPhone|iPod|iPad|IEMobile)/i', $ua))
    {
      return self::TYPE_MOBILE;
    }
    else
    {
      return self::TYPE_DESKTOP;
    }
  }
}