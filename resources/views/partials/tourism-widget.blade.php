<div id="tourism-widget" class="tourism-widget" data-chat-url="{{ route('chat.ask') }}" data-newsletter-url="{{ route('newsletter.store') }}">
    <div id="tourism-widget-panel" class="tourism-widget-panel" hidden>
        <div class="tourism-widget-header">
            <div class="flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white/15 text-xl"><i class="fa-solid fa-route"></i></span>
                <div><p class="font-black">Explora Tarija</p><p class="text-xs text-white/75">Asistente y novedades</p></div>
            </div>
            <button id="tourism-widget-close" type="button" class="widget-close" aria-label="Cerrar"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div class="tourism-widget-tabs">
            <button type="button" class="is-active" data-widget-tab="chat"><i class="fa-solid fa-comments"></i> Ayuda</button>
            <button type="button" data-widget-tab="newsletter"><i class="fa-solid fa-envelope-open-text"></i> Noticias</button>
        </div>

        <div id="widget-chat-pane" class="flex min-h-0 flex-1 flex-col">
            <div id="tourism-chat-messages" class="tourism-chat-messages">
                <div class="chat-message-bot">¡Hola! Soy tu guía virtual. Pregúntame por destinos, municipios, eventos o experiencias publicadas en este portal.</div>
            </div>
            <div class="px-4 pb-2">
                <div class="flex gap-2 overflow-x-auto pb-1">
                    <button type="button" class="chat-suggestion" data-question="¿Qué puedo visitar en Uriondo?">Uriondo</button>
                    <button type="button" class="chat-suggestion" data-question="¿Qué eventos hay?">Eventos</button>
                    <button type="button" class="chat-suggestion" data-question="Quiero conocer la naturaleza de Tarija">Naturaleza</button>
                </div>
            </div>
            <form id="tourism-chat-form" class="tourism-chat-form">
                <input id="tourism-chat-input" maxlength="500" placeholder="Pregunta por un lugar o actividad..." aria-label="Escribe tu pregunta">
                <button type="submit" aria-label="Enviar"><i class="fa-solid fa-paper-plane"></i></button>
            </form>
        </div>

        <div id="widget-newsletter-pane" class="newsletter-pane" hidden>
            <div class="newsletter-art"><i class="fa-solid fa-paper-plane"></i></div>
            <p class="text-xs font-black uppercase tracking-[.18em] text-amber-300">Noticias turísticas</p>
            <h3 class="mt-2 text-2xl font-black text-white">Historias de Tarija en tu correo</h3>
            <p class="mt-2 text-sm leading-6 text-white/75">Recibe nuevas rutas, eventos, fiestas y recomendaciones publicadas por nuestro equipo.</p>
            <form id="tourism-newsletter-form" class="mt-5 space-y-3">
                <input id="newsletter-name" class="newsletter-input" maxlength="100" placeholder="Tu nombre (opcional)">
                <input id="newsletter-email" class="newsletter-input" type="email" required placeholder="correo@ejemplo.com">
                <label class="flex items-start gap-2 text-xs leading-5 text-white/70"><input id="newsletter-consent" class="mt-1" type="checkbox" required> Acepto recibir novedades turísticas y podré cancelar la suscripción cuando quiera.</label>
                <button class="newsletter-submit" type="submit"><i class="fa-solid fa-envelope mr-2"></i>Quiero recibir noticias</button>
                <p id="newsletter-status" class="hidden rounded-xl px-3 py-2 text-sm"></p>
            </form>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <button class="tourism-widget-trigger" type="button" data-widget-open="chat" aria-label="Abrir ayuda turística">
            <span class="widget-pulse"></span><i class="fa-solid fa-comments"></i><span>Ayuda</span>
        </button>
        <button class="tourism-widget-trigger tourism-widget-news" type="button" data-widget-open="newsletter" aria-label="Abrir noticias y newsletter">
            <i class="fa-solid fa-envelope-open-text"></i><span>Noticias</span>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const widget = document.getElementById('tourism-widget');
    const panel = document.getElementById('tourism-widget-panel');
    const chatPane = document.getElementById('widget-chat-pane');
    const newsPane = document.getElementById('widget-newsletter-pane');
    const chatForm = document.getElementById('tourism-chat-form');
    const chatInput = document.getElementById('tourism-chat-input');
    const messages = document.getElementById('tourism-chat-messages');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    if (!widget || !panel || widget.dataset.windowBound === 'true') return;
    widget.dataset.windowBound = 'true';

    function showTab(tab) {
        chatPane.hidden = tab !== 'chat';
        newsPane.hidden = tab !== 'newsletter';
        widget.querySelectorAll('[data-widget-tab]').forEach(function (button) {
            button.classList.toggle('is-active', button.dataset.widgetTab === tab);
        });
    }

    function openWindow(tab) {
        showTab(tab);
        panel.hidden = false;
        panel.style.display = 'flex';
        panel.setAttribute('aria-hidden', 'false');
        widget.classList.add('is-open');
        document.body.classList.add('tourism-widget-open');
    }

    function closeWindow() {
        panel.hidden = true;
        panel.style.display = 'none';
        panel.setAttribute('aria-hidden', 'true');
        widget.classList.remove('is-open');
        document.body.classList.remove('tourism-widget-open');
    }

    widget.querySelectorAll('[data-widget-open]').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            openWindow(button.dataset.widgetOpen);
        });
    });

    widget.querySelectorAll('[data-widget-tab]').forEach(function (button) {
        button.addEventListener('click', function () { showTab(button.dataset.widgetTab); });
    });

    document.getElementById('tourism-widget-close').addEventListener('click', closeWindow);
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeWindow();
    });

    function addChatMessage(text, role, results) {
        const bubble = document.createElement('div');
        bubble.className = role === 'user' ? 'chat-message-user' : 'chat-message-bot';
        const message = document.createElement('p');
        message.textContent = text;
        bubble.appendChild(message);

        (results || []).forEach(function (result) {
            const link = document.createElement('a');
            link.href = result.url;
            link.className = 'chat-result mt-2';

            const type = document.createElement('span');
            type.className = 'text-[10px] font-black uppercase tracking-wider text-red-700';
            type.textContent = result.type;

            const title = document.createElement('strong');
            title.className = 'block text-sm';
            title.textContent = result.title;

            const summary = document.createElement('span');
            summary.className = 'mt-1 block text-xs leading-5 text-gray-500';
            summary.textContent = result.summary || '';

            link.appendChild(type);
            link.appendChild(title);
            link.appendChild(summary);
            bubble.appendChild(link);
        });

        messages.appendChild(bubble);
        messages.scrollTop = messages.scrollHeight;
        return bubble;
    }

    async function askPortal(question) {
        const text = question.trim();
        if (text.length < 2 || chatForm.dataset.loading === 'true') return;

        addChatMessage(text, 'user');
        chatInput.value = '';
        chatForm.dataset.loading = 'true';
        const loading = addChatMessage('Buscando información publicada...', 'bot');

        try {
            const response = await fetch(widget.dataset.chatUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ message: text })
            });
            const data = await response.json();
            loading.remove();

            if (!response.ok) {
                throw new Error(data.message || 'No se pudo realizar la búsqueda.');
            }

            addChatMessage(data.answer, 'bot', data.results);
        } catch (error) {
            loading.remove();
            addChatMessage(error.message || 'No pude conectarme. Intenta nuevamente.', 'bot');
        } finally {
            chatForm.dataset.loading = 'false';
            chatInput.focus();
        }
    }

    chatForm.addEventListener('submit', function (event) {
        event.preventDefault();
        event.stopImmediatePropagation();
        askPortal(chatInput.value);
    });

    widget.querySelectorAll('[data-question]').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.stopImmediatePropagation();
            askPortal(button.dataset.question);
        });
    });

    closeWindow();
});
</script>
