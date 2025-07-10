<x-layouts.app :title="__('Chat')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

        <div class="h-full bg-gray-50" x-data="chatApp()">
            <div class="h-full">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 h-full">

                    {{-- Mobile Sidebar --}}

                    {{-- Sidebar --}}
                    <div class="lg:col-span-4 xl:col-span-3 h-full">
                        <div class="bg-white rounded-lg shadow-sm h-full flex flex-col">

                            {{-- Header --}}
                            <div class="p-4 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="relative">
                                            <img src="https://images.unsplash.com/photo-1544725176-7c40e5a71c5e?w=50&h=50&fit=crop&crop=face"
                                                alt="Ninfa Monaldo" class="w-12 h-12 rounded-full object-cover">
                                            <div
                                                class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full">
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900">Ninfa Monaldo</h3>
                                            <p class="text-sm text-gray-500">Web Developer</p>
                                        </div>
                                    </div>
                                    <button class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Tabs --}}
                            <div class="px-4 pt-4">
                                <div class="flex space-x-1 bg-gray-100 rounded-lg p-1">
                                    <button @click="activeTab = 'chat'"
                                        :class="activeTab === 'chat' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600 hover:text-gray-900'"
                                        class="flex-1 flex items-center justify-center py-2 px-3 rounded-md text-sm font-medium transition-all">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                            </path>
                                        </svg>
                                        Chat
                                    </button>
                                    <button @click="activeTab = 'groups'"
                                        :class="activeTab === 'groups' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-600 hover:text-gray-900'"
                                        class="flex-1 flex items-center justify-center py-2 px-3 rounded-md text-sm font-medium transition-all">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                        Groups
                                    </button>
                                </div>
                            </div>

                            {{-- Contact List --}}
                            <div class="flex-1 overflow-y-auto px-4 py-4">
                                {{-- Chat Tab --}}
                                <div x-show="activeTab === 'chat'" class="space-y-2">
                                    <template x-for="contact in contacts" :key="contact.id">
                                        <div @click="selectContact(contact)"
                                            :class="selectedContact?.id === contact.id ? 'bg-blue-50 border-blue-200' : 'hover:bg-gray-50'"
                                            class="flex items-center p-3 rounded-lg cursor-pointer transition-colors border border-transparent">
                                            <div class="relative flex-shrink-0">
                                                <img :src="contact.avatar" :alt="contact.name"
                                                    class="w-12 h-12 rounded-full object-cover">
                                                <div x-show="contact.isOnline"
                                                    class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full">
                                                </div>
                                            </div>
                                            <div class="ml-3 flex-1 min-w-0">
                                                <div class="flex justify-between items-start">
                                                    <h4 class="text-sm font-semibold text-gray-900 truncate"
                                                        x-text="contact.name"></h4>
                                                    <span class="text-xs text-gray-500" x-text="contact.time"></span>
                                                </div>
                                                <div class="flex items-center mt-1">
                                                    <svg x-show="contact.isRead" class="w-4 h-4 text-blue-500 mr-1"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    <p :class="contact.isTyping ? 'text-green-500' : 'text-gray-500'"
                                                        class="text-sm truncate" x-text="contact.lastMessage"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                {{-- Groups Tab --}}
                                <div x-show="activeTab === 'groups'" class="space-y-2">
                                    <template x-for="group in groups" :key="group.id">
                                        <div @click="selectGroup(group)"
                                            class="flex items-center p-3 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                            <div class="flex -space-x-2 flex-shrink-0">
                                                <template x-for="(member, index) in group.members.slice(0, 2)"
                                                    :key="index">
                                                    <img :src="member"
                                                        class="w-10 h-10 rounded-full border-2 border-white object-cover">
                                                </template>
                                                <div
                                                    class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center text-white text-xs font-medium border-2 border-white">
                                                    <span x-text="group.memberCount + '+'"></span>
                                                </div>
                                            </div>
                                            <div class="ml-4 flex-1 min-w-0">
                                                <div class="flex justify-between items-start">
                                                    <h4 class="text-sm font-semibold text-gray-900 truncate"
                                                        x-text="group.name"></h4>
                                                    <span class="text-xs text-gray-500" x-text="group.time"></span>
                                                </div>
                                                <p class="text-sm text-green-500 truncate mt-1"
                                                    x-text="group.lastMessage"></p>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Add Contact Button --}}
                            <div class="p-4 border-t border-gray-200">
                                <div class="relative">
                                    <button @click="showDropdown = !showDropdown"
                                        class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors ml-auto">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                    <div x-show="showDropdown" @click.away="showDropdown = false"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="absolute bottom-14 right-0 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1">
                                        <button
                                            class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                </path>
                                            </svg>
                                            New Contact
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Chat Area --}}
                    <div class="lg:col-span-8 xl:col-span-9">
                        <div class="bg-white rounded-lg shadow-sm h-full flex flex-col">

                            {{-- Chat Header --}}
                            <div class="p-4 border-b border-gray-200" x-show="selectedContact">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <button @click="showSidebar = true"
                                            class="lg:hidden mr-3 p-2 text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 6h16M4 12h16M4 18h16"></path>
                                            </svg>
                                        </button>
                                        <div class="relative">
                                            <img :src="selectedContact?.avatar" :alt="selectedContact?.name"
                                                class="w-12 h-12 rounded-full object-cover">
                                            <div
                                                class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full">
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="font-semibold text-gray-900" x-text="selectedContact?.name"></h3>
                                            <p class="text-sm text-green-500">Online</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button @click="showCallModal = true"
                                            class="p-3 bg-green-50 text-green-600 rounded-full hover:bg-green-100 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                </path>
                                            </svg>
                                        </button>
                                        <button @click="showVideoModal = true"
                                            class="p-3 bg-blue-50 text-blue-600 rounded-full hover:bg-blue-100 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Messages Area --}}
                            <div class="flex-1 overflow-y-auto p-4 space-y-4" x-show="selectedContact">
                                <div class="text-center">
                                    <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm">Today</span>
                                </div>

                                {{-- Received Message --}}
                                <div class="flex items-start space-x-3">
                                    <img :src="selectedContact?.avatar" :alt="selectedContact?.name"
                                        class="w-10 h-10 rounded-full object-cover">
                                    <div class="flex-1">
                                        <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                                            <p class="text-gray-900">Great to hear!</p>
                                        </div>
                                        <div class="bg-gray-100 rounded-lg p-3 max-w-xs mt-1">
                                            <p class="text-gray-900">How about the testing phase? When do we plan to
                                                start that?</p>
                                        </div>
                                        <div class="flex items-center mt-1 text-xs text-gray-500">
                                            <svg class="w-4 h-4 text-blue-500 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            2:06 PM
                                        </div>
                                    </div>
                                </div>

                                {{-- Sent Message --}}
                                <div class="flex items-start space-x-3 justify-end">
                                    <div class="flex-1 flex justify-end">
                                        <div class="bg-blue-600 text-white rounded-lg p-3 max-w-xs">
                                            <p>We have it scheduled to start next Monday. That gives us a full week to
                                                iron out any issues before the final presentation.</p>
                                        </div>
                                    </div>
                                    <img src="https://images.unsplash.com/photo-1544725176-7c40e5a71c5e?w=50&h=50&fit=crop&crop=face"
                                        alt="You" class="w-10 h-10 rounded-full object-cover">
                                </div>
                                <div class="flex justify-end">
                                    <div class="flex items-center text-xs text-gray-500">
                                        <svg class="w-4 h-4 text-blue-500 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        2:08 PM
                                    </div>
                                </div>

                                {{-- Typing Indicator --}}
                                <div class="flex items-start space-x-3">
                                    <img :src="selectedContact?.avatar" :alt="selectedContact?.name"
                                        class="w-10 h-10 rounded-full object-cover">
                                    <div class="bg-gray-100 rounded-lg p-3">
                                        <div class="flex space-x-1">
                                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                                style="animation-delay: 0.1s"></div>
                                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                                style="animation-delay: 0.2s"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Empty State --}}
                            <div x-show="!selectedContact" class="flex-1 flex items-center justify-center">
                                <div class="text-center">
                                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                        </path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Select a conversation</h3>
                                    <p class="text-gray-500">Choose a contact to start chatting</p>
                                </div>
                            </div>

                            {{-- Message Input --}}
                            <div class="p-4 border-t border-gray-200" x-show="selectedContact">
                                <form @submit.prevent="sendMessage" class="flex items-center space-x-3">
                                    <button type="button"
                                        class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.01M15 10h1.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                    </button>
                                    <div class="flex-1">
                                        <input x-model="newMessage" type="text" placeholder="Type a message..."
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <button type="submit" :disabled="!newMessage.trim()"
                                        :class="newMessage.trim() ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed'"
                                        class="px-4 py-2 rounded-lg transition-colors flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        <span class="hidden sm:inline">Send</span>
                                    </button>
                                    <div class="hidden sm:flex items-center space-x-2">
                                        <button type="button"
                                            class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z">
                                                </path>
                                            </svg>
                                        </button>
                                        <button type="button"
                                            class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </button>
                                        <button type="button"
                                            class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="sm:hidden">
                                        <button @click="showMobileMenu = !showMobileMenu" type="button"
                                            class="p-2 text-gray-400 hover:text-gray-600 transition-colors relative">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                                </path>
                                            </svg>
                                        </button>
                                        <div x-show="showMobileMenu" @click.away="showMobileMenu = false"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute bottom-12 right-0 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1">
                                            <button
                                                class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z">
                                                    </path>
                                                </svg>
                                                Microphone
                                            </button>
                                            <button
                                                class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                Camera
                                            </button>
                                            <button
                                                class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                                    </path>
                                                </svg>
                                                Paperclip
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Call Modal --}}
            <div x-show="showCallModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div @click.away="showCallModal = false" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-90"
                    class="bg-white rounded-lg overflow-hidden shadow-xl max-w-sm w-full mx-4">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=500&fit=crop"
                            alt="Call Background" class="w-full h-96 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-center">
                            <h3 class="text-white text-2xl font-semibold mb-6" x-text="selectedContact?.name"></h3>
                            <div class="flex justify-center space-x-6">
                                <button @click="showCallModal = false"
                                    class="w-14 h-14 bg-green-500 rounded-full flex items-center justify-center text-white hover:bg-green-600 transition-colors animate-pulse">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                        </path>
                                    </svg>
                                </button>
                                <button @click="showCallModal = false"
                                    class="w-14 h-14 bg-red-500 rounded-full flex items-center justify-center text-white hover:bg-red-600 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 3l18 18"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Video Call Modal --}}
            <div x-show="showVideoModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50">
                <div @click.away="showVideoModal = false" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-90"
                    class="bg-black rounded-lg overflow-hidden shadow-xl max-w-4xl w-full mx-4 h-96 relative">
                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&h=600&fit=crop"
                        alt="Video Call" class="w-full h-full object-cover">
                    <div class="absolute top-4 right-4 w-32 h-24 bg-gray-800 rounded-lg overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1544725176-7c40e5a71c5e?w=200&h=150&fit=crop&crop=face"
                            alt="You" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 p-6">
                        <div class="flex justify-center space-x-4">
                            <button
                                class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-white hover:bg-opacity-30 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z">
                                    </path>
                                </svg>
                            </button>
                            <button @click="showVideoModal = false"
                                class="w-14 h-14 bg-red-500 rounded-full flex items-center justify-center text-white hover:bg-red-600 transition-colors animate-pulse">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 3l18 18"></path>
                                </svg>
                            </button>
                            <button
                                class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-white hover:bg-opacity-30 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18 21l-5.197-5.197m0 0L5.636 5.636M13.803 13.803L18 18">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function chatApp() {
    return {
        activeTab: 'chat',
        selectedContact: null,
        newMessage: '',
        showDropdown: false,
        showCallModal: false,
        showVideoModal: false,
        showMobileMenu: false,
        showSidebar: false,

        contacts: [
            {
                id: 1,
                name: 'Pete Sakes',
                avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=50&h=50&fit=crop&crop=face',
                lastMessage: 'Bye! see you soon',
                time: '12:30 PM',
                isOnline: true,
                isRead: true,
                isTyping: false
            },
            {
                id: 2,
                name: 'Fleta Walsh',
                avatar: 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=50&h=50&fit=crop&crop=face',
                lastMessage: 'Typing....',
                time: 'Now',
                isOnline: true,
                isRead: false,
                isTyping: true
            }
        ],

        groups: [
            {
                id: 1,
                name: 'client Group',
                members: [
                    'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=50&h=50&fit=crop&crop=face',
                    'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=50&h=50&fit=crop&crop=face'
                ],
                memberCount: 25,
                lastMessage: 'Typing...',
                time: 'Now'
            }
        ],

        messages: [],

        selectContact(contact) {
            this.selectedContact = contact;
            this.showSidebar = false;
            // Load messages for this contact
            this.loadMessages(contact.id);
        },

        selectGroup(group) {
            this.selectedContact = group;
            this.showSidebar = false;
            // Load messages for this group
            this.loadMessages(group.id);
        },

        loadMessages(contactId) {
            // Simulate loading messages
            this.messages = [
                {
                    id: 1,
                    senderId: contactId,
                    message: 'Great to hear!',
                    time: '2:06 PM',
                    isOwn: false
                },
                {
                    id: 2,
                    senderId: contactId,
                    message: 'How about the testing phase? When do we plan to start that?',
                    time: '2:06 PM',
                    isOwn: false
                },
                {
                    id: 3,
                    senderId: 'me',
                    message: 'We have it scheduled to start next Monday. That gives us a full week to iron out any issues before the final presentation.',
                    time: '2:08 PM',
                    isOwn: true
                }
            ];
        },

        sendMessage() {
            if (!this.newMessage.trim()) return;

            const message = {
                id: Date.now(),
                senderId: 'me',
                message: this.newMessage,
                time: new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
                isOwn: true
            };

            this.messages.push(message);
            this.newMessage = '';

            // Simulate auto-scroll to bottom
            this.$nextTick(() => {
                const chatContainer = document.querySelector('.overflow-y-auto');
                if (chatContainer) {
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                }
            });
        },

        init() {
            // Auto-select first contact on load
            if (this.contacts.length > 0) {
                this.selectContact(this.contacts[0]);
            }
        }
    }
}
    </script>

    @push('styles')
    <style>
        @keyframes bounce {

            0%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-6px);
            }
        }

        .animate-bounce {
            animation: bounce 1s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Custom scrollbar */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Mobile responsive */
        @media (max-width: 1024px) {
            .lg\:col-span-4 {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 40;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }

            .lg\:col-span-4.show {
                transform: translateX(0);
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @endpush
</x-layouts.app>