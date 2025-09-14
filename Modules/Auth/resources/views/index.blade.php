<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Turnstile Token Generator</title>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body>
<h1>Generate Turnstile Token</h1>

<div class="cf-turnstile" data-sitekey="0x4AAAAAAB1H8RAkYhELgbvN" data-callback="onTokenGenerated"></div>

<p>Token:</p>
<textarea id="token" cols="100" rows="3"></textarea>

<script>
    function onTokenGenerated(token) {
        document.getElementById('token').value = token;
    }
</script>
</body>
</html>
