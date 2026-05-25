# public/js/chat.js

```javascript
// =====================================
// ⚙ CONFIG
// =====================================
const API_URL = '/webhook/support';

const FEEDBACK_API =
    '/api/ticket/feedback';

const STORAGE_KEY =
    'chat_history';

// =====================================
// 📦 DOM
// =====================================
const messagesContainer =
    document.getElementById(
        'messagesContainer'
    );

const messageForm =
    document.getElementById(
        'messageForm'
    );

const messageInput =
    document.getElementById(
        'messageInput'
    );

const sendBtn =
    document.getElementById(
        'sendBtn'
    );

const clearBtn =
    document.getElementById(
        'clearBtn'
    );

// =====================================
// 🖼 IMAGE INPUT
// =====================================
const imageInput =
    document.getElementById(
        'imageInput'
    );

// =====================================
// 🖼 IMAGE PREVIEW
// =====================================
const imagePreview =
    document.getElementById(
        'imagePreview'
    );

const previewImg =
    document.getElementById(
        'previewImg'
    );

// =====================================
// 🎤 VOICE BUTTON
// =====================================
const voiceBtn =
    document.getElementById(
        'voiceBtn'
    );

// =====================================
// ✏ EDIT MODE
// =====================================
let editingMessageId = null;

// =====================================
// 🚀 INIT
// =====================================
document.addEventListener(
    'DOMContentLoaded',
    () => {

        console.log('✅ Chat Ready');

        if (!messageForm) {

            console.error(
                '❌ Form not found'
            );

            return;
        }

        messageForm.addEventListener(
            'submit',
            handleSendMessage
        );

        loadHistory();

        initializeImagePreview();

        initializeVoiceInput();

        initializeEnterSend();
    }
);

// =====================================
// 🖼 IMAGE PREVIEW
// =====================================
function initializeImagePreview()
{
    if (!imageInput) return;

    imageInput.addEventListener(
        'change',
        function ()
    {
        const file =
            this.files[0];

        if (!file) {

            imagePreview.style.display =
                'none';

            return;
        }

        const reader =
            new FileReader();

        reader.onload =
            function (e)
        {
            previewImg.src =
                e.target.result;

            imagePreview.style.display =
                'block';
        };

        reader.readAsDataURL(file);
    });
}

// =====================================
// 🎤 VOICE INPUT
// =====================================
function initializeVoiceInput()
{
    if (

        !('webkitSpeechRecognition'
        in window)

        ||

        !voiceBtn

    ) {
        return;
    }

    const recognition =
        new webkitSpeechRecognition();

    recognition.lang = 'fr-FR';

    recognition.continuous = false;

    recognition.interimResults = false;

    voiceBtn.addEventListener(
        'click',
        () => {

            recognition.start();

            voiceBtn.innerHTML =
                '🔴';
        }
    );

    recognition.onresult =
        function(event)
    {
        messageInput.value =

            event.results[0][0]
                .transcript;

        voiceBtn.innerHTML =
            '🎤';
    };

    recognition.onerror =
        function()
    {
        voiceBtn.innerHTML =
            '🎤';
    };

    recognition.onend =
        function()
    {
        voiceBtn.innerHTML =
            '🎤';
    };
}

// =====================================
// ⌨ ENTER SEND
// =====================================
function initializeEnterSend()
{
    if (!messageInput) return;

    messageInput.addEventListener(
        'keydown',
        function (e)
    {
        if (

            e.key === 'Enter'

            &&

            !e.shiftKey

        ) {

            e.preventDefault();

            messageForm.dispatchEvent(

                new Event(
                    'submit',
                    {
                        cancelable: true
                    }
                )
            );
        }
    });
}

// =====================================
// ❌ CLOSE MENUS
// =====================================
document.addEventListener(
    'click',
    (e) => {

        if (
            !e.target.closest(
                '.message-menu-container'
            )
        ) {

            document
                .querySelectorAll(
                    '.message-menu.active'
                )
                .forEach(menu => {

                    menu.classList.remove(
                        'active'
                    );

                });
        }
    }
);

// =====================================
// 📤 SEND MESSAGE
// =====================================
async function handleSendMessage(e)
{
    e.preventDefault();

    const message =
        messageInput.value.trim();

    if (

        !message

        &&

        !imageInput?.files[0]

    ) {

        return;
    }

    // =====================================
    // ✏ UPDATE MESSAGE
    // =====================================
    if (editingMessageId) {

        updateMessage(
            editingMessageId,
            message
        );

        resetEditingState();

        return;
    }

    // =====================================
    // 👤 USER MESSAGE
    // =====================================
    const userMessage = {

        id: createId(),

        text:
            message

            ||

            '📷 Screenshot envoyé',

        sender: 'user',

        timestamp: Date.now(),

        image_url:

            imageInput?.files[0]

            ?

            URL.createObjectURL(
                imageInput.files[0]
            )

            :

            null
    };

    renderMessage(userMessage);

    saveToHistory(userMessage);

    messageInput.value = '';

    showTyping(true);

    sendBtn.disabled = true;

    sendBtn.innerHTML = '⏳';

    try {

        const data =
            await sendMessage(

                message,

                imageInput.files[0]
            );

        console.log(
            '✅ API RESPONSE',
            data
        );

        // =====================================
        // 🤖 BOT MESSAGE
        // =====================================
        const botMessage = {

            id: createId(),

            text: formatResponse(data),

            sender: 'bot',

            timestamp: Date.now(),

            ticket_id:
                data.ticket_id || null,

            source:
                data.source || 'ai',

            image_url:
                data.image_url || null
        };

        renderMessage(botMessage);

        saveToHistory(botMessage);

        // =====================================
        // 🔊 VOICE RESPONSE
        // =====================================
        speakMessage(botMessage.text);

    } catch (error) {

        console.error(error);

        const errorMessage = {

            id: createId(),

            text:
                '❌ Erreur serveur',

            sender: 'bot',

            timestamp: Date.now()
        };

        renderMessage(errorMessage);

        saveToHistory(errorMessage);
    }

    showTyping(false);

    sendBtn.disabled = false;

    sendBtn.innerHTML =
        '<span class="send-icon">➤</span>';

    if (imageInput) {

        imageInput.value = '';
    }

    if (imagePreview) {

        imagePreview.style.display =
            'none';
    }
}

// =====================================
// 🌐 API REQUEST
// =====================================
async function sendMessage(message, imageFile = null)
{
    const formData = new FormData();

    formData.append(
        'message',
        message
    );

    formData.append(
        'source',
        'web'
    );

    if (imageFile) {

        formData.append(
            'image',
            imageFile
        );
    }

    const response = await fetch(
        API_URL,
        {

            method: 'POST',

            headers: {

                'X-CSRF-TOKEN':
                    document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content
            },

            body: formData
        }
    );

    if (!response.ok) {

        throw new Error(
            'Erreur API'
        );
    }

    return await response.json();
}

// =====================================
// 💬 RENDER MESSAGE
// =====================================
function renderMessage(message)
{
    const messageDiv =
        document.createElement('div');

    messageDiv.classList.add(
        'message'
    );

    messageDiv.dataset.id =
        message.id;

    // =====================================
    // 👤 TYPE
    // =====================================
    if (message.sender === 'user') {

        messageDiv.classList.add(
            'user-message'
        );

    } else {

        messageDiv.classList.add(
            'bot-message'
        );
    }

    // =====================================
    // 👤 AVATAR
    // =====================================
    const avatar =
        document.createElement('div');

    avatar.classList.add(
        'message-avatar'
    );

    avatar.textContent =
        message.sender === 'user'
            ? 'U'
            : 'A';

    // =====================================
    // 📦 CONTENT
    // =====================================
    const messageContent =
        document.createElement('div');

    messageContent.classList.add(
        'message-content'
    );

    // =====================================
    // 💬 BUBBLE
    // =====================================
    const bubble =
        document.createElement('div');

    bubble.classList.add(
        'message-bubble'
    );

    // =====================================
    // 🖼 IMAGE
    // =====================================
    if (message.image_url) {

        bubble.innerHTML += `

            <img
                src="${message.image_url}"
                class="chat-image"
            >

        `;
    }

    bubble.innerHTML +=
        formatText(message.text);

    // =====================================
    // 🏷 META
    // =====================================
    const metadata =
        document.createElement('div');

    metadata.classList.add(
        'message-meta'
    );

    metadata.innerHTML = `

        <span class="message-sender">
            ${message.sender === 'user'
                ? 'Vous'
                : 'Assistant'}
        </span>

        <span class="message-timestamp">
            ${formatTimestamp(
                message.timestamp
            )}
        </span>
    `;

    // =====================================
    // 🎫 BOT EXTRA
    // =====================================
    if (
        message.sender === 'bot'
        &&
        message.ticket_id
    ) {

        const extra =
            document.createElement('div');

        extra.classList.add(
            'bot-extra'
        );

        extra.innerHTML = `

            <div class="ticket-box">

                <span>
                    🎫 ${message.ticket_id}
                </span>

                <span class="source-badge">
                    ${message.source.toUpperCase()}
                </span>

            </div>

            <div class="feedback-buttons">

                <button
                    class="feedback-btn positive"
                    onclick="sendFeedback(
                        '${message.ticket_id}',
                        'resolved'
                    )">

                    👍 Résolu

                </button>

                <button
                    class="feedback-btn negative"
                    onclick="sendFeedback(
                        '${message.ticket_id}',
                        'unresolved'
                    )">

                    👎 Non résolu

                </button>

            </div>
        `;

        messageContent.append(
            bubble,
            metadata,
            extra
        );

    } else {

        messageContent.append(
            bubble,
            metadata
        );
    }

    // =====================================
    // ✏ USER MENU
    // =====================================
    if (message.sender === 'user') {

        const menuContainer =
            document.createElement('div');

        menuContainer.classList.add(
            'message-menu-container'
        );

        const menuButton =
            document.createElement('button');

        menuButton.type = 'button';

        menuButton.classList.add(
            'message-menu-btn'
        );

        menuButton.textContent = '⋮';

        const menu =
            document.createElement('div');

        menu.classList.add(
            'message-menu'
        );

        // =====================================
        // ✏ EDIT
        // =====================================
        const editBtn =
            document.createElement('button');

        editBtn.classList.add(
            'menu-item'
        );

        editBtn.textContent =
            '✎ Éditer';

        editBtn.addEventListener(
            'click',
            () => {

                editMessage(message.id);

                menu.classList.remove(
                    'active'
                );
            }
        );

        // =====================================
        // 🔁 RESEND
        // =====================================
        const resendBtn =
            document.createElement('button');

        resendBtn.classList.add(
            'menu-item'
        );

        resendBtn.textContent =
            '↻ Renvoyer';

        resendBtn.addEventListener(
            'click',
            () => {

                resendMessage(
                    message.id
                );

                menu.classList.remove(
                    'active'
                );
            }
        );

        menu.append(
            editBtn,
            resendBtn
        );

        menuButton.addEventListener(
            'click',
            (e) => {

                e.stopPropagation();

                menu.classList.toggle(
                    'active'
                );
            }
        );

        menuContainer.append(
            menuButton,
            menu
        );

        messageDiv.appendChild(
            menuContainer
        );
    }

    // =====================================
    // 📥 APPEND
    // =====================================
    messageDiv.append(
        avatar,
        messageContent
    );

    messagesContainer.appendChild(
        messageDiv
    );

    // =====================================
    // 🔽 AUTO SCROLL
    // =====================================
    messagesContainer.scrollTop =
        messagesContainer.scrollHeight;
}

// =====================================
// 👍 👎 FEEDBACK
// =====================================
async function sendFeedback(
    ticketId,
    feedback
)
{
    try {

        const response = await fetch(

            FEEDBACK_API,

            {

                method: 'POST',

                headers: {

                    'Content-Type':
                        'application/json',

                    'X-CSRF-TOKEN':
                        document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content
                },

                body: JSON.stringify({

                    ticket_id: ticketId,

                    feedback: feedback
                })
            }
        );

        const data =
            await response.json();

        alert(data.message);

    } catch (error) {

        console.error(error);
    }
}

// =====================================
// 🧠 FORMAT RESPONSE
// =====================================
function formatResponse(data)
{
    if (!data) {

        return '❌ Aucune réponse';
    }

    let solution =

        data.solution

        || data.response

        || data.text

        || 'Aucune solution trouvée';

    return stripMarkdown(solution);
}

// =====================================
// 🎨 FORMAT TEXT
// =====================================
function formatText(text)
{
    return text.replace(
        /\n/g,
        '<br>'
    );
}

// =====================================
// 🧹 STRIP MARKDOWN
// =====================================
function stripMarkdown(text)
{
    if (!text) return '';

    return text

        .replace(/\*\*/g, '')

        .replace(/__/g, '')

        .replace(/`/g, '')

        .replace(/#{1,6}\s*/g, '')

        .replace(/\*/g, '')

        .trim();
}

// =====================================
// ⌨ TYPING
// =====================================
function showTyping(show)
{
    const typing =
        document.getElementById(
            'typingIndicator'
        );

    if (typing) {

        typing.style.display =
            show ? 'flex' : 'none';
    }
}

// =====================================
// 🆔 ID
// =====================================
function createId()
{
    return `${Date.now()}-${Math.floor(
        Math.random() * 100000
    )}`;
}

// =====================================
// 🕓 TIME
// =====================================
function formatTimestamp(value)
{
    return new Date(value)
        .toLocaleTimeString(
            'fr-FR',
            {

                hour: '2-digit',

                minute: '2-digit'
            }
        );
}

// =====================================
// 💾 STORAGE
// =====================================
function saveToHistory(message)
{
    const history = getHistory();

    history.push(message);

    localStorage.setItem(

        STORAGE_KEY,

        JSON.stringify(history)
    );
}

function getHistory()
{
    return JSON.parse(

        localStorage.getItem(
            STORAGE_KEY
        )

    ) || [];
}

function loadHistory()
{
    const history = getHistory();

    if (!history.length) return;

    messagesContainer.innerHTML = '';

    history.forEach(renderMessage);
}

// =====================================
// ✏ UPDATE HISTORY
// =====================================
function updateHistory(id, text)
{
    const history = getHistory();

    const item =
        history.find(
            msg => msg.id === id
        );

    if (!item) return;

    item.text = text;

    item.timestamp = Date.now();

    localStorage.setItem(

        STORAGE_KEY,

        JSON.stringify(history)
    );
}

// =====================================
// ✏ UPDATE MESSAGE
// =====================================
function updateMessage(id, text)
{
    const messageDiv =
        document.querySelector(

            `.message[data-id="${id}"]`
        );

    if (!messageDiv) return;

    const bubble =
        messageDiv.querySelector(
            '.message-bubble'
        );

    bubble.innerHTML =
        formatText(text);

    updateHistory(id, text);
}

// =====================================
// ✏ EDIT
// =====================================
function editMessage(id)
{
    const history = getHistory();

    const message =
        history.find(
            item => item.id === id
        );

    if (!message) return;

    editingMessageId = id;

    messageInput.value =
        message.text;

    messageInput.focus();

    sendBtn.classList.add(
        'editing'
    );

    sendBtn.innerHTML =
        'Update';
}

// =====================================
// 🔁 RESEND
// =====================================
function resendMessage(id)
{
    const history = getHistory();

    const message =
        history.find(
            item => item.id === id
        );

    if (!message) return;

    messageInput.value =
        message.text;

    const submitEvent =
        new Event('submit', {

            cancelable: true
        });

    messageForm.dispatchEvent(
        submitEvent
    );
}

// =====================================
// ♻ RESET
// =====================================
function resetEditingState()
{
    editingMessageId = null;

    messageInput.value = '';

    sendBtn.classList.remove(
        'editing'
    );

    sendBtn.innerHTML =
        '<span class="send-icon">➤</span>';
}

// =====================================
// 🗑 CLEAR
// =====================================
if (clearBtn) {

    clearBtn.addEventListener(
        'click',
        () => {

            localStorage.removeItem(
                STORAGE_KEY
            );

            messagesContainer.innerHTML =
                '';

            resetEditingState();
        }
    );
}

// =====================================
// 🔇 STOP SPEECH
// =====================================
function stopSpeech()
{
    window.speechSynthesis.cancel();
}

// =====================================
// 🔊 SPEAK MESSAGE
// =====================================
function speakMessage(text)
{
    if (!window.speechSynthesis) {
        return;
    }

    const speech =
        new SpeechSynthesisUtterance(
            stripMarkdown(text)
        );

    speech.lang = 'fr-FR';

    speech.rate = 1;

    speech.pitch = 1;

    stopSpeech();

    window.speechSynthesis.speak(
        speech
    );
}
```

