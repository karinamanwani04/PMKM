<?php

$apiKey = "";

$input = json_decode(file_get_contents("php://input"), true);
$text = $input['text'] ?? "";

$data = [
    "model" => "openai/gpt-3.5-turbo",
    "messages" => [
        [
            "role" => "user",
            "content" => "Explain this company in 40-50 lines:\n\n" . $text
        ]
    ]
];

$ch = curl_init("https://openrouter.ai/api/v1/chat/completions");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer " . $apiKey
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (isset($result['choices'][0]['message']['content'])) {
    echo nl2br($result['choices'][0]['message']['content']);
} else {
    print_r($result);
}
