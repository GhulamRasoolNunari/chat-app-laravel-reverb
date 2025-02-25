<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Chat APP') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot ?? '' }}
            </main>
        </div>
        <script>

            let chatId = null;
            let chatType = null;
        
            // Click on User (One-to-One Chat)
            document.querySelectorAll('.user-item').forEach(item => {
                item.addEventListener('click', function() {
                    chatId = this.dataset.id;
                    chatType = 'user';
                    document.getElementById('chat-title').innerText = 'Chat with User ' + chatId;
                    loadMessages(chatId, 'user');
                });
            });
        
            // Click on Group (Group Chat)
            document.querySelectorAll('.group-item').forEach(item => {
                item.addEventListener('click', function() {
                    chatId = this.dataset.id;
                    chatType = 'group';
                    document.getElementById('chat-title').innerText = 'Group Chat ' + chatId;
                    loadMessages(chatId, 'group');
                });
            });
        
            // Load Messages
            function loadMessages(id, type) {
                fetch(`/messages/${id}?type=${type}`)
                    .then(response => response.json())
                    .then(data => {
                        let chatBox = document.getElementById('chat-box');
                        chatBox.innerHTML = '';
                        data.forEach(message => {
                            let alignment = message.sender_id == "{{ auth()->id() }}" ? 'text-end text-primary' : 'text-start text-dark';
                            chatBox.innerHTML += `<div class="${alignment}"><strong>${message.sender_name}:</strong> ${message.message}</div>`;
                        });
                        chatBox.scrollTop = chatBox.scrollHeight;
                    });
            }
        
            // Send Message
            document.getElementById('send-btn').addEventListener('click', function() {
                let message = document.getElementById('message-input').value;
                if (!message || !chatId) return;
        
                fetch('/send-message', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    },
                    body: JSON.stringify({ id: chatId, type: chatType, message: message })
                })
                .then(response => response.json())  // Parse JSON response
                .then(data => {console.log(data);
                
                    let chatBox = document.getElementById('chat-box');
                    // chatBox.innerHTML = '';  // Clear chat box
                    
                    chatBox.innerHTML += `
                    <div class="text-end text-primary">
                        <strong>${data.sender}:</strong> ${data.message.message}
                    </div>`;

                    chatBox.scrollTop = chatBox.scrollHeight; // Scroll to the latest message
                    
                })
                .catch(error => console.error('Error:', error));

        
                document.getElementById('message-input').value = '';
            });
        
            // Listen for Incoming Messages using Laravel Reverb
            
            document.addEventListener("DOMContentLoaded", function () {
                // Wait for Echo to be available
                let checkEcho = setInterval(() => {
                    if (typeof window.Echo !== "undefined") {

                        // Now subscribe to channels safely
                        Echo.channel('chat.{{ auth()->id() }}')
                        .listen('.message.sent', (e) => {
                            let chatBox = document.getElementById('chat-box');
                    
                            chatBox.innerHTML += `
                            <div class="text-start text-primary">
                                <strong>test:</strong> test
                            </div>`;

                            chatBox.scrollTop = chatBox.scrollHeight; // Scroll to the latest message
                        });

                        clearInterval(checkEcho); // Stop checking
                    }
                }, 500); // Check every 500ms
            });
        </script>
    </body>
</html>
