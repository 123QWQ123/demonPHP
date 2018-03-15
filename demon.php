<?php
include_once('functions.php');

const BASE_URL = 'https://syn.su/testwork.php';
const EMAIL = 'email@email.email';
const ONE_HOUR = 3600;

try {
    $responseData = getMessageData();
    $message = $responseData['response']['message'] ?? null;
    $key = $responseData['response']['key'] ?? null;

    if ($message && $key) {
        while (true) {
            $signal = base64_encode(xorCrypt($message, $key));
            putMessage($signal);
            sleep(ONE_HOUR);
        }
    }
} catch (\Throwable $exception) {
    sendEmail(sprintf('Error message: %s. Error code: %s', $exception->getMessage(), $exception->getCode()));
}
