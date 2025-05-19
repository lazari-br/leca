<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\ChatAIService;

class ChatAIController extends Controller
{
    public function __construct(public ChatAIService $chatAIService) {}

    public function respond(Request $request): JsonResponse
    {
        $response = $this->chatAIService->send($request->input('message'));
        return response()->json(['response' => $response]);
    }

    public function reset(): JsonResponse
    {
        $this->chatAIService->reset();
        return response()->json(['status' => 'ok']);
    }

    public function history(): JsonResponse
    {
        $messages = ChatMessage::where('session_id', session()->getId())
            ->orderBy('created_at')
            ->get()
            ->map(fn($msg) => [
                'sender' => $msg->role === 'user' ? 'Você' : 'LECA IA',
                'text' => $msg->content,
            ])
            ->values();

        return response()->json(['history' => $messages]);
    }
}
