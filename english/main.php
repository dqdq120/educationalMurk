<?php
function query_hf_model($url, $data, $max_retries = 5, $wait_seconds = 5) {
    for ($i = 0; $i < $max_retries; $i++) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        // If model still loading → wait and retry
        if (isset($result["error"]) && str_contains($result["error"], "loading")) {
            sleep($wait_seconds); // wait before retrying
            continue;
        }

        return $result;
    }
    return ["error" => "Max retries reached"];
}

$response_text = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_message = trim($_POST["message"]);

    if (!empty($user_message)) {
        $url = "https://api-inference.huggingface.co/models/facebook/blenderbot-400M-distill";
        $data = ["inputs" => $user_message];

        $result = query_hf_model($url, $data);

        if (isset($result[0]["generated_text"])) {
            $response_text = $result[0]["generated_text"];
        } elseif (isset($result["generated_text"])) {
            $response_text = $result["generated_text"];
        } elseif (isset($result["error"])) {
            $response_text = "⚠️ " . $result["error"];
        } else {
            $response_text = "⚠️ Unknown response format.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Free PHP Chat AI</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .chat-box {
            width: 500px;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }
        textarea, input[type="text"] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .ai-response {
            background: #e9f5ff;
            padding: 12px;
            border-radius: 8px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="chat-box">
        <h2>💬 Chat with AI (Free)</h2>
        <form method="POST">
            <input type="text" name="message" placeholder="Type your message..." required>
            <input type="submit" value="Send">
        </form>

        <?php if (!empty($response_text)): ?>
            <div class="ai-response">
                <strong>AI:</strong> <?= htmlspecialchars($response_text) ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
