<?
if (!session_id()) {
    //session_save_path('/home/cr16038/tmp');
	session_start();
}

$captcha_string = '';
$captcha_one = rand(1000, 17500);
$captcha_two = rand(1000, 12000);
$captcha_result = $captcha_one + $captcha_two;
$captcha_string .= implode(' ',str_split($captcha_result));
$_SESSION['captcha'] = $captcha_result;

$text = $captcha_string;

header('Content-Type: image/png');

// Создание изображения
$im = imagecreatetruecolor(200, 50);

// Создание цветов
$white = imagecolorallocate($im, 255, 255, 255);
$grey = imagecolorallocate($im, 128, 128, 128);
$black = imagecolorallocate($im, 0, 0, 0);
$dark_red = imagecolorallocate($im, 139, 0, 0);

imagefilledrectangle($im, 0, 0, 199, 49, $white);

// Замена пути к шрифту на пользовательский
$font = $_SERVER['DOCUMENT_ROOT'] . '/fonts/columbia.ttf';

imagettftext($im, 32, 2, 9, 46, $grey, $font, $text);
imagettftext($im, 32, 2, 8, 45, $black, $font, $text);

for ($i = 0; $i < 35; $i++) {
	if ($i > 25) {
		$color = $dark_red;
	} else {
		$color = $i < 15 ? $black : $grey;
	}
	imagesetpixel($im, rand(0, 196),rand(0, 46), $color);
	imageline($im, rand(0, 196), rand(0, 46), rand(0, 196), rand(0, 46), $color);
}

imagepng($im);
imagedestroy($im);