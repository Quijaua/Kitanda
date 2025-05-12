<?php
    function sendWhatsAppMessage($number, $message) {
        $postData = json_encode([
            "number" => $number,
            "text"   => $message
        ]);

        $ch = curl_init($_ENV['EVOLUTION_URL'] . "message/sendText/" . $_ENV['EVOLUTION_INSTANCE']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "apikey: " . $_ENV['EVOLUTION_APIKEY']
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }