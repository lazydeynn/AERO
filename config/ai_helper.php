<?php
function getEnvVar($key)
{
    $envPath = __DIR__ . '/../.env';

    if (!file_exists($envPath)) {
        return getenv($key);
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Skip comments

        list($name, $value) = explode('=', $line, 2);
        if (trim($name) === $key) {
            return trim($value);
        }
    }
    return null;
}

function generateQuizFromAI($context)
{
    $apiKey = getEnvVar('GROQ_API_KEY');

    if (!$apiKey) {
        return ['error' => 'API Key is missing. Check your .env file.'];
    }

    $url = "https://api.groq.com/openai/v1/chat/completions";

    $prompt = "
    Create a quiz with 5 multiple-choice questions based on this text: \"$context\".
    Return ONLY a raw JSON array. Do not use Markdown formatting (no ```json).
    Format:
    [
      {
        \"question_text\": \"Question?\",
        \"option_a\": \"A\",
        \"option_b\": \"B\",
        \"option_c\": \"C\",
        \"option_d\": \"D\",
        \"correct_option\": \"A\"
      }
    ]
    ";

    $data = [
        "model" => "llama-3.1-8b-instant",
        "messages" => [
            ["role" => "system", "content" => "You are a teacher."],
            ["role" => "user", "content" => $prompt]
        ],
        "temperature" => 0.7
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return ['error' => 'Curl error: ' . curl_error($ch)];
    }

    curl_close($ch);
    $json = json_decode($response, true);

    if (isset($json['error'])) {
        return ['error' => 'API Error: ' . $json['error']['message']];
    }

    $content = $json['choices'][0]['message']['content'] ?? '';

    $content = str_replace("```json", "", $content);
    $content = str_replace("```", "", $content);

    $quizData = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => 'Failed to parse AI response.'];
    }

    return $quizData;
}
