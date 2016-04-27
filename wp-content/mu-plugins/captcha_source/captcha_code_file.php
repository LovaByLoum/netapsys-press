<?php 
/*
*
* this code is based on captcha code by Simon Jarvis 
* http://www.white-hat-web-design.co.uk/articles/php-captcha.php
*
* This program is free software; you can redistribute it and/or 
* modify it under the terms of the GNU General Public License 
* as published by the Free Software Foundation
*
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of 
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
* GNU General Public License for more details: 
* http://www.gnu.org/licenses/gpl.html
*/

@session_start();
//Settings: You can customize the captcha here
$image_width = 358;
$image_height = 108;
$characters_on_image = 6;
$font1 = './monofont.ttf';
$font2 = './monofont2.ttf';
$font3 = './monofont3.ttf';
$font4 = './monofont4.ttf';
$font5 = './monofont5.ttf';
$font6 = './monofont6.ttf';
$font7 = './monofont7.ttf';


//The characters that can be used in the CAPTCHA code.
//avoid confusing characters (l 1 and i for example)
$possible_letters = '23456789bcdfghjkmnpqrstvwxyz';
$random_dots = 0;
$random_lines = 6;
$captcha_text_color="0x142864";

$captcha_noice_color = "0x142864";

$code = '';


$image = @imagecreate($image_width, $image_height);


/* setting the background, text and noise colours here */
$background_color = imagecolorallocate($image, 255, 255, 255);

$arr_noice_color = hexrgb($captcha_noice_color);
$image_noise_color = imagecolorallocate($image, $arr_noice_color['red'], 
		$arr_noice_color['green'], $arr_noice_color['blue']);


/* generating the dots randomly in background */
for( $i=0; $i<$random_dots; $i=$i+5 ) {
    imagefilledellipse($image, mt_rand(0,$image_width),
    mt_rand(0,$image_height), 2, 3, $image_noise_color);
}

//add backgroud ellipse
$ratio = $image_width / $image_height;
$col_ellipse = imagecolorallocate($image, 144, 192, 240);
$i = 1;
do{
    imageellipse($image,$image_width/2 , $image_height/2, $ratio*$i, $i, $col_ellipse);
    if ((intval($i)%7) == 0){
        $i = $i+4;
    }
    $i = $i + 0.0005;
} while($i < $image_height+200 );


/* generating lines randomly in background of image */
for( $i=0; $i<$random_lines; $i++ ) {
    imageline($image, mt_rand(0,$image_width), mt_rand(0,$image_height),
    mt_rand(0,$image_width), mt_rand(0,$image_height), $image_noise_color);
}


$i = 0;
$font_size_old = 0;
while ($i < $characters_on_image) {
    switch($i%5){
        case 0:
            $font = $font5;
            break;
        case 1 :
            $font = $font6;
            break;
        case 2 :
            $font = $font7;
            break;
        case 3 :
            $font = $font1;
            break;
        case 4 :
            $font = $font3;
            break;
        default:
            break;
    }
    $multi = (rand(0,1)) ? 4 : 5;
    $font_size = $image_height * 0.11 * $multi;
    $rand = array('3', '4', '5', '6', '7', '8', '9');
    $captcha_text_color = '#'.$rand[rand(0,6)].$rand[rand(0,6)].$rand[rand(0,6)].$rand[rand(0,6)].$rand[rand(0,6)].$rand[rand(0,6)];
    $arr_text_color = hexrgb($captcha_text_color);
    $text_color = imagecolorallocate($image, $arr_text_color['red'],
        $arr_text_color['green'], $arr_text_color['blue']);
    $text = substr($possible_letters, mt_rand(0, strlen($possible_letters)-1), 1);
    /* create a text box and add 6 letters code in it */
    $textbox = imagettfbbox($font_size, 0, $font, $text);
    $y = ($image_height - $textbox[5])/2;
    $x = (($image_width - ($image_height * 0.5 * $random_lines))/2) + $font_size_old;
    $font_size_old = $font_size_old + $font_size;

    imagettftext($image, $font_size, 0, $x, $y, $text_color, $font , $text);
    $code .= $text;
    $i++;
}



/* Show captcha image in the page html page */
header('Content-Type: image/jpeg');// defining the image type to be shown in browser widow
imagejpeg($image);//showing the image
imagedestroy($image);//destroying the image instance
$_SESSION['captcha'] = $code;
function hexrgb ($hexstr)
{
  $int = hexdec($hexstr);

  return array("red" => 0xFF & ($int >> 0x10),
               "green" => 0xFF & ($int >> 0x8),
               "blue" => 0xFF & $int);
}
?>