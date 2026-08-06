<?php
if (!session_id()) {
	session_start();
}

/* SVG не требует расширения GD и работает в стандартной установке PHP. */
$captchaResult = random_int(1000, 17500) + random_int(1000, 12000);
$_SESSION['captcha'] = (string) $captchaResult;
$captchaText = implode(' ', str_split((string) $captchaResult));

header('Content-Type: image/svg+xml; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
?>
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="50" viewBox="0 0 200 50" role="img" aria-label="Код подтверждения">
	<rect width="200" height="50" fill="#fff"/>
	<path d="M5 10 L195 42 M15 46 L180 8 M45 3 L155 47" stroke="#8b0000" stroke-width="1" opacity=".45"/>
	<text x="8" y="37" fill="#111" font-family="Arial, sans-serif" font-size="30" letter-spacing="2"><?=htmlspecialchars($captchaText, ENT_QUOTES, 'UTF-8')?></text>
</svg>
