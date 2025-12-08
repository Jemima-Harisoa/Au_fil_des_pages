<?php
namespace app\controllers;

use app\models\ChatbotModel;
use Flight;

class ChatBotController
{
    public function send()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');

        $raw = file_get_contents('php://input');
        error_log('ChatBotController::send raw=' . substr($raw,0,500));
        $data = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'error' => 'JSON invalide: ' . json_last_error_msg()]);
            return;
        }

        $question = trim($data['message'] ?? '');
        if ($question === '') {
            echo json_encode(['success' => false, 'error' => 'Message vide']);
            return;
        }

        try {
            $model = new ChatbotModel();
            $reply = $model->generateReply($question);
            echo json_encode(['success' => true, 'botReply' => $reply, 'date' => date('Y-m-d H:i:s')]);
        } catch (\Exception $e) {
            error_log('ChatBotController::send error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Erreur du bot: ' . $e->getMessage(), 'botReply' => 'Désolé, je ne peux pas répondre pour le moment.']);
        }
    }
}
