@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-white min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Section -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-12">
            <div class="md:flex">
                <!-- Profile Image -->
                <div class="md:w-1/3">
                    <img src="{{ asset('images/profile.jpg') }}" 
                         alt="Your Name" 
                         class="w-full h-full object-cover"
                    >
                </div>
                
                <!-- Profile Info -->
                <div class="p-8 md:w-2/3">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">Let's Connect.</h1>
                    <p class="text-lg text-gray-600 mb-6">
                        I'm interested in hearing from anyone who stubbles upon my blog, especially recovering drunks. 
                        Whether you have a question, suggestion, or just have something to say, 
                        feel free to reach out.
                    </p>
                    
                    <!-- Social Links -->
                    <div class="flex space-x-6">
                        <a href="https://twitter.com/yourusername" 
                           class="text-gray-400 hover:text-blue-500 transition-colors duration-300"
                           target="_blank"
                        >
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>

                        <a href="https://instagram.com/yourusername" 
                           class="text-gray-400 hover:text-pink-500 transition-colors duration-300"
                           target="_blank"
                        >
                            <span class="sr-only">Instagram</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Quickly talk with my personal bot to get ahold of me.</h2>

                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Chat Interface -->
                <div x-data="chatBot()" class="relative">
                    <!-- Start Chat Overlay -->
                    <div x-show="!chatStarted" 
                         class="absolute inset-0 bg-blue-500/95 rounded-lg flex flex-col items-center justify-center space-y-4 z-10"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95">
                        <p class="text-white text-lg text-center px-4">Ready to chat? I'm here to help connect you with Charles.</p>
                        <button @click="startChat" 
                                class="px-6 py-3 bg-white text-blue-500 rounded-full font-semibold hover:bg-blue-50 transform transition hover:scale-105">
                            Start Chat
                        </button>
                        
                        @auth
                            @if(auth()->user()->is_admin)
                                <div class="flex justify-center mt-4">
                                    <button @click="testEmail" 
                                            class="px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/30"
                                            :class="{ 'opacity-50 cursor-not-allowed': isTestingEmail }"
                                            :disabled="isTestingEmail"
                                            x-text="testEmailStatus || 'Test Email Configuration'">
                                    </button>
                                </div>
                            @endif
                        @endauth
                    </div>

                    <!-- Chat Messages -->
                    <div class="space-y-4 h-96 overflow-y-auto p-4 bg-gray-50 rounded-lg" id="chat-messages">
                        <template x-for="message in messages" :key="message.id">
                            <div :class="{'flex justify-end': message.type === 'user', 'flex justify-start': message.type === 'bot'}">
                                <div :class="{
                                    'bg-blue-500 text-white': message.type === 'user',
                                    'bg-gray-200 text-gray-900': message.type === 'bot'
                                }" class="max-w-[80%] rounded-lg px-4 py-2">
                                    <p x-text="message.text"></p>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Loading indicator -->
                        <template x-if="isTyping">
                            <div class="flex justify-start">
                                <div class="bg-gray-200 text-gray-900 max-w-[80%] rounded-lg px-4 py-2">
                                    <div class="flex space-x-2">
                                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce"></div>
                                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                        <div class="w-2 h-2 bg-gray-500 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- User Input -->
                    <div class="mt-4 flex space-x-4">
                        <input type="text" 
                               x-model="userInput"
                               @keyup.enter="handleUserInput"
                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Type your message..."
                               :disabled="currentStep === 'complete' || isTyping"
                        >
                        <button @click="handleUserInput"
                                :disabled="!userInput || currentStep === 'complete' || isTyping"
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed">
                            Send
                        </button>
                    </div>
                </div>

                @push('scripts')
                <script>
                    function chatBot() {
                        return {
                            messages: [],
                            userInput: '',
                            currentStep: 'name',
                            userName: '',
                            userEmail: '',
                            userMessage: '',
                            isTyping: false,
                            chatStarted: false,
                            isTestingEmail: false,
                            testEmailStatus: null,

                            startChat() {
                                this.chatStarted = true;
                                this.addBotMessage("Hi! I'd love to help connect you with Charles. What's your name?");
                            },

                            async testEmail() {
                                if (this.isTestingEmail) return;
                                
                                this.isTestingEmail = true;
                                this.testEmailStatus = 'Sending test email...';
                                
                                try {
                                    const response = await fetch('{{ route('contact.test-email') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                        }
                                    });
                                    
                                    let data;
                                    const contentType = response.headers.get('content-type');
                                    if (contentType && contentType.includes('application/json')) {
                                        data = await response.json();
                                    } else {
                                        throw new Error('Server returned non-JSON response');
                                    }
                                    
                                    if (!response.ok) {
                                        throw new Error(data.message || 'Failed to send test email');
                                    }
                                    
                                    this.testEmailStatus = 'Test email sent!';
                                    setTimeout(() => {
                                        this.testEmailStatus = null;
                                    }, 3000);
                                } catch (error) {
                                    console.error('Error sending test email:', error);
                                    this.testEmailStatus = error.message;
                                    setTimeout(() => {
                                        this.testEmailStatus = null;
                                    }, 3000);
                                } finally {
                                    this.isTestingEmail = false;
                                }
                            },

                            addMessage(text, type = 'bot') {
                                this.messages.push({
                                    id: Date.now(),
                                    text: text,
                                    type: type
                                });
                                
                                this.$nextTick(() => {
                                    const container = document.getElementById('chat-messages');
                                    container.scrollTop = container.scrollHeight;
                                });
                            },

                            addBotMessage(text, delay = 1000) {
                                this.isTyping = true;
                                setTimeout(() => {
                                    this.isTyping = false;
                                    this.addMessage(text, 'bot');
                                }, delay);
                            },

                            handleUserInput() {
                                if (!this.userInput || this.isTyping) return;

                                const userMessage = this.userInput.trim();
                                this.addMessage(userMessage, 'user');
                                this.userInput = '';

                                switch (this.currentStep) {
                                    case 'name':
                                        this.userName = userMessage;
                                        this.currentStep = 'message';
                                        this.addBotMessage(`Nice to meet you, ${this.userName}! Would you like to leave a message for Charles? (optional)`);
                                        break;

                                    case 'message':
                                        this.userMessage = userMessage;
                                        this.currentStep = 'email';
                                        this.addBotMessage("Great! What's your email address so Charles can get back to you?");
                                        break;

                                    case 'email':
                                        this.userEmail = userMessage;
                                        this.currentStep = 'complete';
                                        this.submitContact();
                                        break;
                                }
                            },

                            async submitContact() {
                                try {
                                    const response = await fetch('{{ route('contact.submit') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                        },
                                        body: JSON.stringify({
                                            name: this.userName,
                                            email: this.userEmail,
                                            message: this.userMessage || 'No message provided'
                                        })
                                    });

                                    const data = await response.json();

                                    if (!response.ok) {
                                        if (data.type === 'duplicate') {
                                            this.addBotMessage(data.message);
                                            // Don't reset the chat for duplicate submissions
                                            this.currentStep = 'complete';
                                        } else {
                                            throw new Error(data.message || 'Failed to submit contact form');
                                        }
                                        return;
                                    }

                                    this.addBotMessage("Thanks for reaching out! Charles will get back to you soon at " + this.userEmail);
                                } catch (error) {
                                    console.error('Error:', error);
                                    this.addBotMessage("Sorry, there was a problem sending your message. Please try again later.");
                                    this.currentStep = 'name';
                                }
                            }
                        }
                    }
                </script>
                @endpush
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush

@endsection