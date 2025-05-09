<?php 
namespace App\Services\Telegram;
use Illuminate\Support\Facades\Http;


class BotGetMeResponse
{
    public function __construct(
        public bool $ok,
        public Bot $result,
    ) {}

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
    ) {}

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
    ) {}

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

class SendPhotoResponse
{
    public function __construct(
        public bool $ok,
        public ?TelegramMessage $result,
        public ?string $description = null,
        public ?int $error_code = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            ok: $data['ok'],
            result: isset($data['result']) ? TelegramMessage::fromArray($data['result']) : null,
            description: $data['description'] ?? null,
            error_code: $data['error_code'] ?? null,
        );
    }
}

class SendAnimationResponse
{
    public function __construct(
        public bool $ok,
        public ?TelegramMessage $result,
        public ?string $description = null,
        public ?int $error_code = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            ok: $data['ok'],
            result: isset($data['result']) ? TelegramMessage::fromArray($data['result']) : null,
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
    ) {}

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
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            title: $data['title'] ?? '',
            type: $data['type'],
        );
    }
}


class TelegramBotServices
{
    public static function getBot($token): BotGetMeResponse
    {
        $response = Http::get("https://api.telegram.org/bot{$token}/getMe");

        if ($response->failed() || !$response->json('ok')) {
            return new BotGetMeResponse(
                false,
                new Bot('', '', '', false, false, false, false, false)
            );
        }

        return BotGetMeResponse::fromArray($response->json());
    }

    public static function sendMessage($token, $chatId, $message): SendMessageResponse
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

        return SendMessageResponse::fromArray($response->json());
    }

    public static function sendPhoto($token, $chatId, $photoUrlOrPath, $caption = ''): SendPhotoResponse
    {
        $url = "https://api.telegram.org/bot{$token}/sendPhoto";
        $isFile = file_exists($photoUrlOrPath);

        if ($isFile) {
            $response = Http::attach(
                'photo', file_get_contents($photoUrlOrPath), basename($photoUrlOrPath)
            )->post($url, [
                'chat_id' => $chatId,
                'caption' => $caption?? '',
            ]);
        } else {
            $response = Http::post($url, [
                'chat_id' => $chatId,
                'photo' => $photoUrlOrPath,
                'caption' => $caption?? '',
            ]);
        }

        if ($response->failed() || !$response->json('ok')) {
            return new SendPhotoResponse(false, null, $response->json('description'), $response->json('error_code'));
        }

        return SendPhotoResponse::fromArray($response->json());
    }

    public static function sendAnimation($token, $chatId, $gifUrlOrPath, $caption = ''): SendAnimationResponse
    {
        $url = "https://api.telegram.org/bot{$token}/sendAnimation";
        $isFile = file_exists($gifUrlOrPath);

        if ($isFile) {
            $response = Http::attach(
                'animation', file_get_contents($gifUrlOrPath), basename($gifUrlOrPath)
            )->post($url, [
                'chat_id' => $chatId,
                'caption' => $caption ?? '',
            ]);
        } else {
            $response = Http::post($url, [
                'chat_id' => $chatId,
                'animation' => $gifUrlOrPath,
                'caption' => $caption?? '',
            ]);
        }

        if ($response->failed() || !$response->json('ok')) {
            return new SendAnimationResponse(false, null, $response->json('description'), $response->json('error_code'));
        }

        return SendAnimationResponse::fromArray($response->json());
    }

    public static function sendDocument($token, $chatId, $documentUrlOrPath, $caption = ''): SendPhotoResponse
    {
        $url = "https://api.telegram.org/bot{$token}/sendDocument";
        $isFile = file_exists($documentUrlOrPath);

        if ($isFile) {
            $response = Http::attach(
                'document', file_get_contents($documentUrlOrPath), basename($documentUrlOrPath)
            )->post($url, [
                'chat_id' => $chatId,
                'caption' => $caption?? '',
            ]);
        } else {
            $response = Http::post($url, [
                'chat_id' => $chatId,
                'document' => $documentUrlOrPath,
                'caption' => $caption?? '',
            ]);
        }

        if ($response->failed() || !$response->json('ok')) {
            return new SendPhotoResponse(false, null, $response->json('description'), $response->json('error_code'));
        }

        return SendPhotoResponse::fromArray($response->json());
    }

    public static function sendAudio($token, $chatId, $audioUrlOrPath, $caption = ''): SendPhotoResponse
    {
        $url = "https://api.telegram.org/bot{$token}/sendAudio";
        $isFile = file_exists($audioUrlOrPath);

        if ($isFile) {
            $response = Http::attach(
                'audio', file_get_contents($audioUrlOrPath), basename($audioUrlOrPath)
            )->post($url, [
                'chat_id' => $chatId,
                'caption' => $caption?? '',
            ]);
        } else {
            $response = Http::post($url, [
                'chat_id' => $chatId,
                'audio' => $audioUrlOrPath,
                'caption' => $caption?? '',
            ]);
        }

        if ($response->failed() || !$response->json('ok')) {
            return new SendPhotoResponse(false, null, $response->json('description'), $response->json('error_code'));
        }

        return SendPhotoResponse::fromArray($response->json());
    }

    public static function sendVideo($token, $chatId, $videoUrlOrPath, $caption = ''): SendPhotoResponse
    {
        $url = "https://api.telegram.org/bot{$token}/sendVideo";
        $isFile = file_exists($videoUrlOrPath);

        if ($isFile) {
            $response = Http::attach(
                'video', file_get_contents($videoUrlOrPath), basename($videoUrlOrPath)
            )->post($url, [
                'chat_id' => $chatId,
                'caption' => $caption?? '',
            ]);
        } else {
            $response = Http::post($url, [
                'chat_id' => $chatId,
                'video' => $videoUrlOrPath,
                'caption' => $caption?? '',
            ]);
        }

        if ($response->failed() || !$response->json('ok')) {
            return new SendPhotoResponse(false, null, $response->json('description'), $response->json('error_code'));
        }

        return SendPhotoResponse::fromArray($response->json());
    }
}
