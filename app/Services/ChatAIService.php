<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatAIService
{
    protected string $sessionId;
    protected int $maxMessages = 10;

    public function __construct()
    {
        $this->sessionId = session()->getId();
    }

    public function send(string $userMessage): string
    {
        ChatMessage::create([
            'session_id' => $this->sessionId,
            'role' => 'user',
            'content' => $userMessage,
        ]);


        $history = $this->getHistory();
        $messages = array_merge([
            [
                'role' => 'system',
                'content' => $this->getContext()
            ]
        ], $history);

        $response = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
            'temperature' => 0.7,
        ]);

        $answer = $response['choices'][0]['message']['content'] ?? 'Desculpe, não consegui entender sua pergunta no momento.';

        ChatMessage::create([
            'session_id' => $this->sessionId,
            'role' => 'assistant',
            'content' => $answer,
        ]);

        return $answer;
    }

    protected function getHistory(): array
    {
        return ChatMessage::where('session_id', $this->sessionId)
            ->orderBy('created_at')
            ->take($this->maxMessages)
            ->get()
            ->map(fn($msg) => [
                'role' => $msg->role,
                'content' => $msg->content,
            ])
            ->toArray();
    }

    public function reset(): void
    {
        ChatMessage::where('session_id', $this->sessionId)->delete();
    }

    private function getContext(): string
    {
        $context = "Você é uma atendente da loja de roupas fitness LECA. Sempre responda de forma objetiva, educada, clara e formal. quanto for lista produtos, utilize estrutura de listagem.";

        $productIndex = 1;
        Product::with(['variations', 'category'])
            ->get()
            ->map(function($product) use (&$productIndex, &$context) {

                $context .= "$productIndex. $product->name: $product->description da categoria {$product->category->name}.
                Preço: $product->price.
                Cores disponíveis: {$product->variations->pluck('color')->filter()->unique()->map(fn($color) => $this->translateHexColor($color))->implode(', ')}.
                Tamanhos disponíveis {$product->variations->pluck('size')->filter()->unique()->implode(', ')}.
                Quando a pessoa quiser ver ou comprar o produto, ou quando julgar cabível, envie o link do produto em questão. A url é montado com https://www.leca.oficial.com/produto/{nome_produto como string amigavel}, por ex: o link da Calça Legging é leca.oficial.com/produto/calca-legging. (não envie a url entre parenteses ou com aspas. precisa ser um link clicavel)
                Se você não conseguir atender a expectativa do cliente ou ele quiser conversar com um humano direcione para conversar via whatsapp pelo link: https://wa.me/5511962163422?text={texto resumindo dúvida do cliente}
                ";

                $productIndex++;
                return $context;
            });
        return $context;
    }

    private function translateHexColor(string $color): string
    {
        $ref = [
            '#FFA500' => 'Laranja',
            '#800080' => 'Roxo',
            '#FFFF00' => 'Amarelo',
            '#008000' => 'Verde',
            '#0000FF' => 'Azul',
            '#FFC0CB' => 'Rosa',
            '#FF0000' => 'Vermelho',
            '#808080' => 'Cinza',
            '#FFFFFF' => 'Branco',
            '#000000' => 'Preto',
        ];

        return $ref[$color] ?? $color;
    }
}
