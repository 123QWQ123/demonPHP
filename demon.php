<?php

declare(strict_types=1);

require_once('functions.php');

const EMAIL = 'email@email.email';
const ONE_HOUR = 3600;

try {
    $responseData = getMessageData();

    $message = $responseData['message'] ?? null;
    $key = $responseData['key'] ?? null;

    if (null !== $message && null !== $key) {
        while (true) {
            $signal = base64_encode(xorCrypt($message, $key));
            sendUpdateMessage($signal);
            sleep(ONE_HOUR);
        }
    }
} catch (\Throwable $exception) {
    $emailMessage = sprintf('Error message: %s. Error code: %s', $exception->getMessage(), $exception->getCode());
    sendEmail(EMAIL, $emailMessage);
}
