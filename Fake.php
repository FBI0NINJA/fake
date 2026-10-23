
#!/usr/bin/env php
<?php

error_reporting(E_ALL);

function getInput(string $message): string
{
    echo $message;
    return trim(fgets(STDIN) ?: '');
}

echo "\033[31m";
echo "########################################\n";
echo "#          FakeCallCP - Demo            #\n";
echo "#          PHP Diagnostic Tool          #\n";
echo "########################################\n\n";
echo "\033[0m";

if (!extension_loaded('curl')) {
    fwrite(STDERR, "[!] PHP cURL extension is not enabled.\n");
    exit(1);
}

$url = getInput("[?] Enter API URL to test: ");

if (!filter_var($url, FILTER_VALIDATE_URL)
    || !in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true)) {
    fwrite(STDERR, "[!] Invalid HTTP/HTTPS URL.\n");
    exit(1);
}

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_NOBODY => true,
    CURLOPT_USERAGENT => 'PHP-Diagnostic-Tool/1.0',
]);

$response = curl_exec($ch);
$error = curl_error($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    echo "[!] Connection failed: {$error}\n";
} else {
    echo "[+] HTTP status: {$status}\n";

    if ($status >= 200 && $status < 400) {
        echo "[+] Server is reachable.\n";
    } else {
        echo "[!] Server returned an error status.\n";
    }
}

curl_close($ch);
