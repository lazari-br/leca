<div x-data="chatWidget()" x-init="init()" class="fixed bottom-6 right-6 z-50">
    <div class="fixed bottom-6 right-6 z-50">
        <!-- Botão flutuante -->
        <button @click="open = !open" class="bg-pink-600 hover:bg-pink-700 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center">
            💬
        </button>

        <!-- Janela do chat -->
        <div
            x-show="open"
            x-transition
            x-ref="chatWindow"
            class="absolute bottom-20 right-6 bg-white border border-gray-300 rounded-lg shadow-lg p-4 flex flex-col"
            style="width: 320px; height: 400px;"
        >
            <!-- Alça de redimensionamento -->
            <div
                class="w-4 h-4 bg-pink-500 cursor-nwse-resize absolute top-0 left-0 z-50"
                @mousedown.prevent="startResizing"
                style="cursor: nwse-resize;"
            ></div>

            <div id="chat-messages" class="flex-1 overflow-y-auto text-sm text-gray-800 mb-2 space-y-2"></div>
            <input type="text" id="chat-input" class="w-full border p-2 rounded" placeholder="Digite sua pergunta...">
        </div>
    </div>

    <script>
        function chatWidget() {
            return {
                open: false,
                messages: [],
                resizing: false,
                startX: 0,
                startY: 0,
                startWidth: 0,
                startHeight: 0,

                init() {
                    fetch('/ia-chat/history')
                        .then(res => res.json())
                        .then(data => {
                            this.messages = (data.history && Array.isArray(data.history)) ? data.history : [];

                            if (this.messages.length === 0) {
                                this.messages.push({
                                    sender: 'LECA IA',
                                    text: 'Olá, eu sou a Leca, inteligência artificial da loja LECA. Como posso te ajudar?'
                                });
                            }

                            this.renderMessages();
                        })
                        .catch(err => {
                            console.error('Erro ao carregar histórico:', err);
                        });

                    const input = document.getElementById('chat-input');
                    input?.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter' && input.value.trim() !== '') {
                            const message = input.value.trim();
                            input.value = '';

                            this.messages.push({ sender: 'Você', text: message });
                            this.renderMessages();

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
                                    this.messages.push({ sender: 'LECA IA', text: data.response });
                                    this.renderMessages();
                                });
                        }
                    });

                    window.addEventListener('mousemove', (e) => this.resizeChat(e));
                    window.addEventListener('mouseup', () => this.stopResizing());
                },

                renderMessages() {
                    const container = document.getElementById('chat-messages');
                    container.innerHTML = '';

                    const urlRegex = /(https?:\/\/[^\s]+)/g;

                    this.messages.forEach(msg => {
                        const alignment = msg.sender === 'Você' ? 'text-right' : 'text-left';

                        // Transforma URLs em links clicáveis
                        const msgTextWithLinks = msg.text.replace(urlRegex, url => {
                            return `<a href="${url}" target="_blank" class="text-blue-500 underline">${url}</a>`;
                        });

                        container.innerHTML += `<div class="${alignment} whitespace-pre-line"><strong>${msg.sender}:</strong> ${msgTextWithLinks}</div>`;
                    });

                    container.scrollTop = container.scrollHeight;
                },

                startResizing(e) {
                    this.resizing = true;
                    this.startX = e.clientX;
                    this.startY = e.clientY;
                    const win = this.$refs.chatWindow;
                    this.startWidth = win.offsetWidth;
                    this.startHeight = win.offsetHeight;
                },

                resizeChat(e) {
                    if (!this.resizing) return;
                    const dx = this.startX - e.clientX;
                    const dy = this.startY - e.clientY;
                    const win = this.$refs.chatWindow;
                    win.style.width = `${this.startWidth + dx}px`;
                    win.style.height = `${this.startHeight + dy}px`;
                },

                stopResizing() {
                    this.resizing = false;
                }
            }
        }
    </script>
</div>
