#!/usr/bin/env php
<?php

$url = 'http://map.az511.com/arcgis/rest/services/speed/MapServer/export'
        . '?dpi=300'
        . '&transparent=true'
        . '&format=png8'
        . '&bbox=-12459674,3929986,-12449850,3954744'
        . '&bboxSR=102100'
        . '&imageSR=102100'
        . '&size=300,720'
        . '&f=image';

$points = array(
  array(113,105,'Rio Salado'),
  array(113,125,'University'),
  array(113,145,'Apache'),
  array(113,165,'Broadway'),
  array(113,185,'S of Broadway'),
  array(113,195,'Southern'),
  array(113,210,'N of 60'),
  array(113,236,'60'),
  array(111,264,'Baseline'),
  array(108,278),
  array(108,290),
  array(108,310,'Guadalupe'),
  array(106,330),
  array(106,350),
  array(106,370,'Elliot'),
  array(106,390),
  array(108,410),
  array(108,430,'Warner'),
  array(108,450),
  array(107,470),
  array(105,490,'Ray'),
  array(103,510),
  array(103,530,'Chandler'),
  array(103,550),
  array(103,570,'Price'),
);

$image = new Imagick($url);

$colors = array();
foreach ( $points as $point )
{
  $pixel = $image->getImagePixelColor($point[0], $point[1]);
  $colors[] = colorForPixel($pixel);
}

$counts = counts();

$msg = "Red: " . $counts['red'] . ', Yellow: ' . $counts['yellow'] . ', Green: ' . $counts['green'] . "\n\n";
$msg .= asciiMap();

if ( isset($argv[1]) )
{
  $to = $argv[1];
  $subject = 'Traffic: ' . $counts['red'] . ' / ' . $counts['yellow'] . ' / ' . $counts['green'];
  mail($to, $subject, $msg);
}
else
{
  print $msg;
}

exit;

// -----------

function counts()
{
  global $colors;

  $counts = array();
  foreach ( $colors as $color )
  {
    if ( !isset($counts[$color]))
    {
      $counts[$color] = 0;
    }

    $counts[$color]++;
  }

  return $counts;
}

function asciiMap()
{
  global $points, $colors;

  $str = '';
  foreach ( $colors as $idx => $color )
  {
    switch ($color)
    {
      case 'red':     $str .= '|XX|'; break;
      case 'yellow':  $str .= '|..|'; break;
      case 'green':   $str .= '|  |'; break;
      default:        $str .= '|??|'; break;
    }

    $str .= '  ' . str_pad($color,6);
    if ( isset($points[$idx][2]) )
    {
      $str .= '  ' . $points[$idx][2];
    }

    $str .= "\n";
  }

  return $str;
}

function colorForPixel($pixel)
{
  $rgb = $pixel->getColor();
  $val = $rgb['r'] . '-' . $rgb['g'] . '-' . $rgb['b'];
  $color = '';

  switch ( $val )
  {
    case '222-11-2':  $color='red';     break;
    case '255-249-3': $color='yellow';  break;
    case '20-164-66': $color='green';   break;
    default: $color = '???: '. $val;
  }

  return $color;
}

?>
