<?php

namespace App\Services;

use App\Models\OrangTuaChat;
use App\Models\OrangTuaChatMessage;
use App\Models\User;
use App\Support\ChatPlainText;
use Illuminate\Support\Collection;

class OrangTuaChatService
{
    private const HISTORY_LIMIT = 20;

    public function __construct(
        protected MonevSummaryService $monevService,
        protected OrangTuaChatContextBuilder $contextBuilder
    ) {}

    public function getOrCreateChat(User $user): OrangTuaChat
    {
        return OrangTuaChat::firstOrCreate(
            ['user_id' => $user->id],
            ['sekolah_id' => (int) $user->sekolah_id]
        );
    }

    /**
     * @return array{user_message: OrangTuaChatMessage, assistant_message: OrangTuaChatMessage}
     */
    public function sendMessage(User $user, string $content): array
    {
        $ai = $this->monevService->resolveAiServiceForUser($user);
        if (! $ai) {
            throw new \RuntimeException(
                'Pengaturan AI belum dikonfigurasi. Minta admin lembaga untuk mengisi API Key di menu Pengaturan AI.'
            );
        }

        $content = trim(strip_tags($content));
        if ($content === '') {
            throw new \InvalidArgumentException('Pesan tidak boleh kosong.');
        }

        $chat = $this->getOrCreateChat($user);

        $userMessage = $chat->messages()->create([
            'role'    => OrangTuaChatMessage::ROLE_USER,
            'content' => $content,
        ]);

        $history = $this->messagesForUser($chat)
            ->where('id', '!=', $userMessage->id)
            ->values();

        $apiMessages = [
            ['role' => 'system', 'content' => $this->contextBuilder->buildSystemPrompt($user)],
        ];

        foreach ($history->take(-self::HISTORY_LIMIT) as $msg) {
            $apiMessages[] = [
                'role'    => $msg->role,
                'content' => $msg->content,
            ];
        }

        $apiMessages[] = [
            'role'    => OrangTuaChatMessage::ROLE_USER,
            'content' => $content,
        ];

        $reply = ChatPlainText::fromMarkdown($ai->chatCompletion($apiMessages));

        $assistantMessage = $chat->messages()->create([
            'role'    => OrangTuaChatMessage::ROLE_ASSISTANT,
            'content' => $reply,
        ]);

        $chat->touch();

        return [
            'user_message'      => $userMessage,
            'assistant_message' => $assistantMessage,
        ];
    }

    public function clearHistory(User $user): void
    {
        $chat = $this->getOrCreateChat($user);
        $chat->update(['cleared_at' => now()]);
    }

    /**
     * @return Collection<int, OrangTuaChatMessage>
     */
    public function messagesForUser(OrangTuaChat $chat): Collection
    {
        return $chat->visibleMessagesForUser()->get();
    }

    /**
     * @return Collection<int, OrangTuaChatMessage>
     */
    public function messagesForAdmin(OrangTuaChat $chat): Collection
    {
        return $chat->messages()->get();
    }

    public function hasVisibleMessages(OrangTuaChat $chat): bool
    {
        return $this->messagesForUser($chat)->isNotEmpty();
    }
}
