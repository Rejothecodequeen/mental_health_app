@extends('layouts.app')

@section('content')
<div class="d-flex" style="height: 100vh;">
    <div class="sidebar">
        <h3>Users</h3>
        <ul id="user-list" style="list-style:none; padding:0;">
            @foreach($users as $u)
                <li class="user d-flex align-items-center justify-content-between" data-id="{{ $u->id }}">
                    <a href="{{ route('chat', $u->id) }}">{{ $u->name }}</a>
                    <span class="status-dot offline"></span>
                </li>
            @endforeach
            <li class="user d-flex align-items-center justify-content-between" data-id="0">
                <a href="#">Mental Health Bot</a>
                <span class="status-dot online"></span>
            </li>
        </ul>
    </div>

    <div class="chat flex-fill d-flex flex-column">
        <div class="messages flex-fill d-flex flex-column" id="messages" style="overflow-y:auto;"></div>
        <div class="input-area d-flex p-2 bg-light">
            <input id="message" type="text" placeholder="Type a message..." class="flex-fill form-control me-2 rounded-pill">
            <button id="send" class="btn btn-primary rounded-pill">Send</button>
        </div>
    </div>
</div>

@vite('resources/js/app.js')

<!-- Marked.js for Markdown parsing -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<script>
const messagesList = document.getElementById("messages");
const messageInput = document.getElementById("message");
const authId = {{ Auth::id() }};
let receiverId = null;
let currentChannel = null;

// Append message helper (with Markdown support)
function appendMessage(sender, text) {
    const div = document.createElement("div");
    div.classList.add("message", sender === authId ? "me" : "other");
    // Parse markdown → HTML
    div.innerHTML = marked.parse(text);
    messagesList.appendChild(div);
    messagesList.scrollTop = messagesList.scrollHeight;
}

// Subscribe to a private chat channel
function joinChannel() {
    if (!receiverId || receiverId === 0) return; // Bot messages do not use Echo

    if (currentChannel) {
        window.Echo.leaveChannel(currentChannel);
    }

    currentChannel = `chat.${receiverId}.${authId}`;
    window.Echo.private(currentChannel)
        .listen('MessageSent', e => {
            appendMessage(e.message.sender_id, e.message.message);
        });
}

// Click user to select chat
document.querySelectorAll('.user').forEach(u => {
    u.addEventListener('click', async e => {
        e.preventDefault();
        receiverId = parseInt(u.dataset.id);
        messagesList.innerHTML = '';

        if (receiverId === 0) {
            appendMessage(0, "Hello! I am your mental health chatbot. How can I help you today?");
        } else {
            const res = await fetch(`/messages/${receiverId}`);
            const data = await res.json();
            data.forEach(msg => appendMessage(msg.sender_id, msg.message));
        }

        joinChannel();
    });
});

// Send message
document.getElementById("send").addEventListener("click", async () => {
    const text = messageInput.value.trim();
    if (!text || receiverId === null) return;

    appendMessage(authId, text);
    messageInput.value = "";

    const res = await fetch("/messages/send", {
        method: "POST",
        headers:{
            "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content,
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ receiver_id: receiverId, message: text })
    });

    const data = await res.json();
    if (data.bot_message) {
        appendMessage(0, data.bot_message.message);
    }
});

// Presence channel
if (window.Echo) {
    window.Echo.join('online')
        .here(users => users.forEach(u => updateStatus(u.id, true)))
        .joining(user => updateStatus(user.id, true))
        .leaving(user => updateStatus(user.id, false));
}

function updateStatus(userId, isOnline) {
    const dot = document.querySelector(`.user[data-id="${userId}"] .status-dot`);
    if (dot) {
        dot.classList.remove("online", "offline");
        dot.classList.add(isOnline ? "online" : "offline");
    }
}
</script>

<style>
.message { 
    max-width: 60%; 
    padding: 10px 15px; 
    margin: 5px 0; 
    border-radius: 18px; 
    font-size: 14px; 
    line-height: 1.4; 
    display: inline-block; 
    clear: both; 
}
.message.me { 
    align-self: flex-end; 
    background: #0078ff; 
    color: #fff; 
    border-bottom-right-radius: 5px; 
}
.message.other { 
    align-self: flex-start; 
    background: #e5e5ea; 
    color: #000; 
    border-bottom-left-radius: 5px; 
}

/* Style lists and formatting inside messages */
.message ul, .message ol {
    margin: 5px 0 5px 20px;
    padding: 0;
}
.message strong {
    font-weight: bold;
}
.message em {
    font-style: italic;
}

.status-dot { width: 10px; height: 10px; border-radius: 50%; margin-left: 5px; }
.online { background: #28a745; }
.offline { background: #ccc; }
</style>
@endsection
