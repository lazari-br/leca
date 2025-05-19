@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto mt-10">
        <h1 class="text-xl font-bold mb-4">Chat com IA - LECA</h1>
        <div id="chat-log" class="bg-gray-100 p-4 rounded h-64 overflow-y-auto text-sm mb-4"></div>
        <input type="text" id="question" class="w-full border rounded p-2" placeholder="Digite sua pergunta...">
    </div>

    <script>
        document.getElementById('question').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const message = this.value;
                this.value = '';
                const log = document.getElementById('chat-log');
                log.innerHTML += `<div><strong>Você:</strong> ${message}</div>`;

                fetch('/ia-chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ message })
                })
                    .then(res => res.json())
                    .then(data => {
                        log.innerHTML += `<div><strong>LECA IA:</strong> ${data.response}</div>`;
                        log.scrollTop = log.scrollHeight;
                    });
            }
        });
    </script>
@endsection
