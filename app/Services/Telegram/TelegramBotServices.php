<?php

namespace App\Services\Telegram;
use Illuminate\Support\Facades\Http;

class TelegramBotServices
{
    public static function getBot($token) : BotGetMeResponse
    {
        $response = Http::get("https://api.telegram.org/bot{$token}/getMe");

        if ($response->failed() || !$response->json('ok')) {
            return new BotGetMeResponse(
                false,
                new Bot('', '', '', false, false, false, false, false)
            );
        }

        $botData = $response->json('result');

        return new BotGetMeResponse(
            true,
            new Bot(
                $botData['id'],
                $botData['first_name'],
                $botData['username'],
                $botData['can_join_groups'],
                $botData['can_read_all_group_messages'],
                $botData['supports_inline_queries'],
                $botData['can_connect_to_business'],
                $botData['has_main_web_app']
            )
        );
    }

    public static function sendMessage($token, $chatId, $message) : SendMessageResponse
    {
        $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
        ]);

        if ($response->failed() || !$response->json('ok')) {
            return new SendMessageResponse(
                false,
                new TelegramMessage(0, new TelegramChat(0, '', ''), 0, ''),
                $response->json('description'),
                $response->json('error_code')
            );
        }
        $messageData = $response->json('result');
        $chatData = $messageData['chat'];
        return new SendMessageResponse(
            true,
            new TelegramMessage(
                $messageData['message_id'],
                new TelegramChat(
                    $chatData['id'],
                    $chatData['title'] ?? '',
                    $chatData['type']
                ),
                $messageData['date'],
                $messageData['text'] ?? ''
            )
        );
    }
}

class BotGetMeResponse
{
    public function __construct(
        public bool $ok,
        public Bot $result,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            ok: $data['ok'],
            result: Bot::fromArray($data['result']),
        );
    }
}

class Bot
{
    public function __construct(
        public string $id,
        public string $first_name,
        public string $username,
        public bool $can_join_groups,
        public bool $can_read_all_group_messages,
        public bool $supports_inline_queries,
        public bool $can_connect_to_business,
        public bool $has_main_web_app,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) $data['id'],
            first_name: $data['first_name'],
            username: $data['username'],
            can_join_groups: $data['can_join_groups'],
            can_read_all_group_messages: $data['can_read_all_group_messages'],
            supports_inline_queries: $data['supports_inline_queries'],
            can_connect_to_business: $data['can_connect_to_business'],
            has_main_web_app: $data['has_main_web_app'] ?? false,
        );
    }
}

class SendMessageResponse
{
    public function __construct(
        public bool $ok,
        public TelegramMessage $result,
        public ?string $description = null,
        public ?int $error_code = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            ok: $data['ok'],
            result: TelegramMessage::fromArray($data['result']),
            description: $data['description'] ?? null,
            error_code: $data['error_code'] ?? null,
        );
    }
}

class TelegramMessage
{
    public function __construct(
        public int $message_id,
        public TelegramChat $chat,
        public int $date,
        public string $text,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            message_id: $data['message_id'],
            chat: TelegramChat::fromArray($data['chat']),
            date: $data['date'],
            text: $data['text'] ?? '',
        );
    }
}

class TelegramChat
{
    public function __construct(
        public int|string $id,
        public string $title,
        public string $type,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            title: $data['title'] ?? '',
            type: $data['type'],
        );
    }
}
