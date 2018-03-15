<?php
declare(strict_types=1);

/**
 * Get response from the server
 * @return array
 * @throws Exception
 */
function getMessageData(): array
{
    return sendRequest(['method' => 'get']);
}

/**
 * Update message
 * @param string $message
 * @return array
 * @throws Exception
 */
function putMessage(string $message): array
{
    return sendRequest(['method' => 'update', 'message' => $message]);
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
 * @param array $postDate
 * @return array
 * @throws Exception
 */
function sendRequest(array $postDate): array
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, BASE_URL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postDate));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $serverOutput = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($serverOutput, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception('Invalid response format json');
    }
    
    if (!empty($responseData['errorCode'])) {
        $message = $responseData['errorMessage'] ?? 'Something goes wrong';
        throw new \Exception($message, $responseData['errorCode']);
    }

    if (empty($responseData['response'])) {
        throw new \Exception('There is no data in the server\'s response.');
    }

    return $responseData;
}

/**
 * Send email
 * @param string $message
 * @return bool
 */
function sendEmail(string $message): bool
{
    $headers = "From: Demon";

    return mail(EMAIL, "Error", $message, $headers);
}
