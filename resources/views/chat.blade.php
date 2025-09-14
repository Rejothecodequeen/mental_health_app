<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Messenger</title>
    <style>
        body { display: flex; font-family: Arial, sans-serif; margin: 0; }
        .sidebar { width: 250px; background: #f1f1f1; height: 100vh; overflow-y: auto; padding: 10px; }
        .sidebar h3 { margin-top: 0; }
        .user { display: flex; align-items: center; justify-content: space-between; padding: 6px 8px; border-radius: 5px; }
        .user a { text-decoration: none; color: #333; flex: 1; }
        .status-dot {
            width: 10px; height: 10px; border-radius: 50%;
            margin-left: 5px;
        }
        .online { background: #28a745; }  /* green dot */
        .offline { background: #ccc; }   /* gray dot */

        .chat { flex: 1; display: flex; flex-direction: column; }
        .messages { flex: 1; padding: 10px; overflow-y: auto; background: #fafafa; display: flex; flex-direction: column; }
        
        /* Bubble styles */
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

        .input-area { display: flex; padding: 10px; background: #eee; }
        .input-area input { flex: 1; padding: 8px; border-radius: 18px; border: 1px solid #ccc; }
        .input-area button { margin-left: 10px; padding: 8px 16px; border-radius: 18px; border: none; background: #0078ff; color: white; cursor: pointer; }
        .input-area button:hover { background: #005fcc; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h3>Users</h3>
        <ul id="user-list" style="list-style:none; padding:0;">
            @foreach($users as $u)
                <li class="user" data-id="{{ $u->id }}">
                    <a href="{{ route('chat', $u->id) }}">{{ $u->name }}</a>
                    <span class="status-dot offline"></span>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Chat Section -->
    <div class="chat">
        @if($receiver)
            <div class="messages" id="messages"></div>
            <div class="input-area">
                <input id="message" type="text" placeholder="Type a message...">
                <button id="send">Send</button>
            </div>
        @else
            <p style="padding:20px;">Select a user to start chatting.</p>
        @endif
    </div>

    @vite('resources/js/app.js')

    @if($receiver)
    <script>
        const receiverId = {{ $receiver->id }};
        const authId = {{ Auth::id() }};
        const messagesList = document.getElementById("messages");
        const messageInput = document.getElementById("message");

        // Load old messages
        fetch(`/messages/${receiverId}`)
            .then(res => res.json())
            .then(data => {
                data.forEach(msg => {
                    let div = document.createElement("div");
                    div.classList.add("message");
                    if (msg.sender_id == authId) {
                        div.classList.add("me");
                        div.textContent = msg.message;
                    } else {
                        div.classList.add("other");
                        div.textContent = msg.message;
                    }
                    messagesList.appendChild(div);
                });
                messagesList.scrollTop = messagesList.scrollHeight;
            });

        // Send message and show immediately
        document.getElementById("send").addEventListener("click", () => {
            const text = messageInput.value.trim();
            if (!text) return;

            let div = document.createElement("div");
            div.classList.add("message", "me");
            div.textContent = text;
            messagesList.appendChild(div);
            messagesList.scrollTop = messagesList.scrollHeight;

            fetch("/messages", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    receiver_id: receiverId,
                    message: text
                })
            });

            messageInput.value = "";
        });

        // Listen for incoming messages
        window.Echo.private(`chat.${receiverId}.${authId}`) 
            .listen("PrivateMessageSent", (e) => {
                let div = document.createElement("div");
                div.classList.add("message");
                if (e.message.sender_id == authId) {
                    div.classList.add("me");
                    div.textContent = e.message.message;
                } else {
                    div.classList.add("other");
                    div.textContent = e.message.message;
                }
                messagesList.appendChild(div);
                messagesList.scrollTop = messagesList.scrollHeight;
            });

        // Presence channel for online/offline
        window.Echo.join("online")
            .here((users) => {
                users.forEach(u => updateStatus(u.id, true));
            })
            .joining((user) => {
                updateStatus(user.id, true);
            })
            .leaving((user) => {
                updateStatus(user.id, false);
            });

        function updateStatus(userId, isOnline) {
            let userEl = document.querySelector(`.user[data-id="${userId}"] .status-dot`);
            if (userEl) {
                userEl.classList.remove("online", "offline");
                userEl.classList.add(isOnline ? "online" : "offline");
            }
        }
    </script>
    @endif
</body>
</html>
