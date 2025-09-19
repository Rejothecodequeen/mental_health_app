@extends('layouts.app')

@section('content')
<div class="d-flex" style="height: 100vh;">

    <!-- Sidebar: Users -->
    <div class="sidebar p-3 border-end" style="width: 280px; background: #f8f9fa;">
        <h4 class="mb-3">Users</h4>
        <input type="text" id="searchUser" class="form-control mb-3 rounded-pill" placeholder="Search users...">
        <ul id="user-list" class="list-unstyled" style="padding-left: 0;">
            @foreach($users as $u)
                <li class="user d-flex align-items-center justify-content-between p-2 rounded mb-1" 
                    data-id="{{ $u->id }}" style="cursor:pointer; transition: background 0.2s;">
                    <span>{{ $u->name }}</span>
                    <span class="status-dot offline"></span>
                </li>
            @endforeach
            <li class="user d-flex align-items-center justify-content-between p-2 rounded mb-1" 
                data-id="0" style="cursor:pointer; transition: background 0.2s;">
                <span>Mental Health Bot</span>
                <span class="status-dot online"></span>
            </li>
        </ul>
    </div>

    <!-- Chat Area -->
    <div class="chat flex-fill d-flex flex-column border-start">
        <div class="messages flex-fill p-3" id="messages" style="overflow-y:auto; background: #f9f9f9;"></div>
        <div class="input-area d-flex p-3 bg-white border-top">
            <input id="message" type="text" placeholder="Type a message..." class="flex-fill form-control me-2 rounded-pill">
            <button id="send" class="btn btn-primary rounded-pill">Send</button>
        </div>
    </div>
</div>

@vite('resources/js/app.js')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<script>
const messagesList = document.getElementById("messages");
const messageInput = document.getElementById("message");
const authId = {{ Auth::id() }};
let receiverId = null;
let currentChannel = null;

// Append message with proper bubble styling
function appendMessage(sender, text) {
    const div = document.createElement("div");
    div.classList.add("message");

    div.style.maxWidth = "70%";
    div.style.wordWrap = "break-word";
    div.style.padding = "10px 15px";
    div.style.margin = "5px 0";
    div.style.borderRadius = "20px";
    div.style.boxShadow = "0 1px 2px rgba(0,0,0,0.1)";
    div.style.display = "inline-block";
    
    if(sender === authId){
        div.style.alignSelf = "flex-end";
        div.style.background = "#0078ff";
        div.style.color = "#fff";
        div.style.borderBottomRightRadius = "5px";
    } else if(sender === 0) { // bot
        div.style.alignSelf = "flex-start";
        div.style.background = "#6f42c1"; // bot purple
        div.style.color = "#fff";
        div.style.borderBottomLeftRadius = "5px";
    } else {
        div.style.alignSelf = "flex-start";
        div.style.background = "#e5e5ea";
        div.style.color = "#000";
        div.style.borderBottomLeftRadius = "5px";
    }

    div.innerHTML = marked.parse(text);
    messagesList.appendChild(div);
    messagesList.scrollTop = messagesList.scrollHeight;
}

// Subscribe to a private chat channel
function joinChannel() {
    if (!receiverId || receiverId === 0) return; // Bot messages do not use Echo
    if (currentChannel) window.Echo.leaveChannel(currentChannel);

    currentChannel = `chat.${receiverId}.${authId}`;
    window.Echo.private(currentChannel)
        .listen('MessageSent', e => appendMessage(e.message.sender_id, e.message.message));
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
async function sendMessage() {
    const text = messageInput.value.trim();
    if(!text || receiverId === null) return;

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
    if(data.bot_message) appendMessage(0, data.bot_message.message);
}

// Click send button
document.getElementById("send").addEventListener("click", sendMessage);

// Press Enter to send
messageInput.addEventListener("keydown", function(e){
    if(e.key === "Enter" && !e.shiftKey){
        e.preventDefault();
        sendMessage();
    }
});

// Presence channel
if(window.Echo){
    window.Echo.join('online')
        .here(users => users.forEach(u => updateStatus(u.id, true)))
        .joining(user => updateStatus(user.id, true))
        .leaving(user => updateStatus(user.id, false));
}

function updateStatus(userId, isOnline){
    const dot = document.querySelector(`.user[data-id="${userId}"] .status-dot`);
    if(dot){
        dot.classList.remove("online", "offline");
        dot.classList.add(isOnline ? "online" : "offline");
    }
}

// Search users
document.getElementById("searchUser").addEventListener("input", function(){
    const query = this.value.toLowerCase();
    document.querySelectorAll('#user-list .user').forEach(user => {
        const name = user.querySelector('span').innerText.toLowerCase();
        user.style.display = name.includes(query) ? 'flex' : 'none';
    });
});
</script>

<style>
.messages {
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    background: #f9f9f9;
}

/* Scrollbar styling */
.messages::-webkit-scrollbar {
    width: 6px;
}
.messages::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,0.2);
    border-radius: 3px;
}

/* User sidebar hover */
.user:hover {
    background: #e2e6ea;
}

.status-dot {
    width: 10px; height: 10px;
    border-radius: 50%; margin-left: 5px;
}
.online { background: #28a745; }
.offline { background: #ccc; }
</style>
@endsection
