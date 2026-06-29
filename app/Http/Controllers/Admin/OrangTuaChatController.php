<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrangTuaChat;
use App\Services\OrangTuaChatService;
use App\Support\PaginationPerPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrangTuaChatController extends Controller
{
    public function __construct(
        protected OrangTuaChatService $chatService
    ) {}

    public function index(Request $request)
    {
        $sekolahId = auth()->user()->sekolah_id;

        $chats = OrangTuaChat::query()
            ->where('sekolah_id', $sekolahId)
            ->with(['user.anaks.kelas'])
            ->withCount('messages')
            ->with(['messages' => fn ($q) => $q->latest()->limit(1)])
            ->orderByDesc('updated_at')
            ->paginate(PaginationPerPage::resolve($request))->withQueryString();

        return view('admin.orangtua_chat.index', compact('chats'));
    }

    public function show(OrangTuaChat $orangtua_chat): View
    {
        abort_if((int) $orangtua_chat->sekolah_id !== (int) auth()->user()->sekolah_id, 404);

        $orangtua_chat->load(['user.anaks.kelas']);
        $messages = $this->chatService->messagesForAdmin($orangtua_chat);

        return view('admin.orangtua_chat.show', [
            'chat' => $orangtua_chat,
            'messages' => $messages,
        ]);
    }
}
