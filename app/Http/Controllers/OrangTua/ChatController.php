<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Services\OrangTuaChatContextBuilder;
use App\Services\OrangTuaChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(
        protected OrangTuaChatService $chatService,
        protected OrangTuaChatContextBuilder $contextBuilder
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $chat = $this->chatService->getOrCreateChat($user);
        $messages = $this->chatService->messagesForUser($chat);
        $anaks = $this->contextBuilder->approvedAnaks($user);

        return view('orangtua.chat.index', compact('chat', 'messages', 'anaks'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $result = $this->chatService->sendMessage(auth()->user(), $validated['content']);

            return response()->json([
                'messages' => [
                    [
                        'id'         => $result['user_message']->id,
                        'role'       => $result['user_message']->role,
                        'content'    => $result['user_message']->content,
                        'created_at' => $result['user_message']->created_at->toIso8601String(),
                    ],
                    [
                        'id'         => $result['assistant_message']->id,
                        'role'       => $result['assistant_message']->role,
                        'content'    => $result['assistant_message']->content,
                        'created_at' => $result['assistant_message']->created_at->toIso8601String(),
                    ],
                ],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\RuntimeException $e) {
            $status = str_contains($e->getMessage(), 'Pengaturan AI') ? 422 : 500;

            return response()->json(['error' => $e->getMessage()], $status);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Gagal menghubungi layanan AI: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(): JsonResponse
    {
        $this->chatService->clearHistory(auth()->user());

        return response()->json(['success' => true]);
    }
}
