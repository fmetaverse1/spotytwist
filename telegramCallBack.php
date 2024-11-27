<?php

// Define your bot token
$botToken = "7764284417:AAE9-ADJoUIFoXjNuZclwFI8yJOpKRcINMQ"; // Replace with your bot's API token

// Define the Telegram API URL
$telegramApiUrl = "https://api.telegram.org/bot$botToken";

// Function to log button clicks to a file
function logButtonClick($uniqueId, $buttonAction)
{
    $logFile = "button_responses.txt"; // File to save responses
    $timestamp = date("Y-m-d H:i:s");
    $logEntry = "Timestamp: $timestamp | Unique_ID: $uniqueId | Action: $buttonAction\n";

    // Append the log entry to the file
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

// Function to send a response back to Telegram
function sendTelegramResponse($callbackQueryId, $message)
{
    global $telegramApiUrl;

    $data = [
        'callback_query_id' => $callbackQueryId,
        'text' => $message,
        'show_alert' => false, // Set to true if you want to display an alert instead of a toast
    ];

    $url = $telegramApiUrl . "/answerCallbackQuery";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log("CURL error: " . curl_error($ch));
    }

    curl_close($ch);

    return $response;
}

// Handle webhook updates
$update = json_decode(file_get_contents("php://input"), true);

if (!$update) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

// Check if the update contains a callback query
if (isset($update['callback_query'])) {
    $callbackQuery = $update['callback_query'];
    $callbackData = $callbackQuery['data']; // Data associated with the button
    $callbackQueryId = $callbackQuery['id']; // Unique ID for the callback query
    $fromUser = $callbackQuery['from']['username'] ?? 'unknown'; // Username of the user

    // Extract unique ID and button action
    $uniqueId = explode("_", $callbackData)[1] ?? 'unknown';
    $buttonAction = explode("_", $callbackData)[0] ?? 'unknown';

    // Log the button click
    logButtonClick($uniqueId, $buttonAction);

    // Send a response back to Telegram
    $responseMessage = "Button '$buttonAction' clicked for ID '$uniqueId' by @$fromUser.";
    sendTelegramResponse($callbackQueryId, $responseMessage);

    // Respond with a success message
    echo json_encode(['status' => 'success', 'message' => 'Callback processed']);
} else {
    // Respond with an error if no callback query is found
    http_response_code(400);
    echo json_encode(['error' => 'No callback query found']);
}
