<?php

function generateQuestion($apiKey, $model, $topic, $count = 5)
{
    $topic = <<<EOD
        Generate $count multiple-choice questions on the topic "$topic".
        Each question must be in the following JSON format:

        [
        {
            "question_text": "Your question here",
            "options": [
            { "option_text": "Option A", "is_correct": 1 },
            { "option_text": "Option B", "is_correct": 0 },
            { "option_text": "Option C", "is_correct": 0 },
            { "option_text": "Option D", "is_correct": 0 }
            ],
            "correct_option" : 0
        }
        ]

        Only return pure JSON without any explanation or formatting. and change the position of correct options randomly and put correct option index in correct_option field
        Do not repeat phrasing or structure between questions. Vary the question types (fact-based, conceptual, scenario-based).
    EOD;

    try {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $apiKey;
        $data = [
            "contents" => [
                [
                    "parts" => [
                        [
                            "text" => $topic
                        ]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.9,
                "topP" => 0.95
            ]
        ];

        // cURL Setup
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute request
        $response = curl_exec($ch);
        curl_close($ch);

        // Decode and display response
        $result = json_decode($response, true);
        if (isset($result['error'])) {
            throw new Exception($result['error']['message']);
        }
        return $result;
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
        exit();
    }
}
