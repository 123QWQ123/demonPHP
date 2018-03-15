<?php

declare(strict_types=1);

const BASE_URL = 'https://syn.su/testwork.php';
const GET = 'get';
const UPDATE = 'update';

/**
 * Get response from the server
 * @return array
 * @throws Exception
 */
function getMessageData(): array
{
    $responseData = sendRequest(GET);

    if (empty($responseData['response'])) {
        throw new \Exception('There is no data in the server\'s response.');
    }

    return $responseData['response'];
}

/**
 * Send update message
 * @param string $message
 * @return bool
 * @throws Exception
 */
function sendUpdateMessage(string $message): bool
{
    $responseData = sendRequest(UPDATE, $message);

    return $responseData['response'] === 'Success';
}

/**
 * XOR encryption and decryption
 * @param $string
 * @param $key
 * @return string
 */
function xorCrypt($string, $key): string
{
    $outText = '';

    for ($i = 0; $i < strlen($string);) {
        for ($j = 0; ($j < strlen($key) && $i < strlen($string)); $j++, $i++) {
            $outText .= $string{$i} ^ $key{$j};
        }
    }

    return $outText;
}

/**
 * Send http request
 * @param string $method
 * @param string $message
 * @return array
 * @throws Exception
 */
function sendRequest(string $method, string $message = ''): array
{
    $queryData = buildQueryData($method, $message);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, BASE_URL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($queryData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $serverOutput = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($serverOutput, true);
    var_dump($responseData);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception('Invalid response format json');
    }

    if (!empty($responseData['errorCode'])) {
        $message = $responseData['errorMessage'] ?? 'Something goes wrong';
        throw new \Exception($message, $responseData['errorCode']);
    }

    return $responseData;
}

/**
 * Send email
 * @param string $email
 * @param string $message
 * @return bool
 */
function sendEmail(string $email, string $message): bool
{
    $headers = "From: Demon";

    return mail($email, "Error", $message, $headers);
}

/**
 * Get query array
 * @param string $method
 * @param string $message
 * @return array
 */
function buildQueryData(string $method, string $message): array
{
    $queryData = ['method' => $method];

    if (strlen($message) > 0) {
        $queryData['message'] = $message;
    }

    return $queryData;
}
