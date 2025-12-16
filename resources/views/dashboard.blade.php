<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="">
            <input type="text" class="p-5 border-green-400 border-2 " id="typingInput">
            <img src="{{ asset('typing.gif') }}" alt="" class="w-10" id="typingImage">
        </div>
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <a href="{{ route('chat.index') }}">
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 flex justify-center items-center text-center">
                    <div>
                        <h1 class="text-2xl font-black">Active Users</h1>
                        <p class="text-xl mt-2 text-green-500">1</p>
                    </div>
                </div>
            </a>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>

        </div>
        <div
            class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
    <style>
        #typingImage {
            display: none
        }
    </style>

    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            Echo.private('chat-channel.' + 1).listen('.message.event', (e) => {
                console.log('Message Receive:', e);
            })

            Echo.private('conversation-channel.' + 1).listen('.conversation.event', (e) => {
                console.log('Conversation and Unread Message count:', e);
            })

            Echo.join(`online-status-channel`)
                .here(users => {
                    console.log('Active Users:', [users]);
                })
                .joining(user => {
                    console.log('User Joined:', [user]);
                })
                .leaving(user => {
                    console.log('User Left:', [user]);
                });

            const typingInput = document.getElementById('typingInput');
            const typingImage = document.getElementById('typingImage');

            let typingTimeout;

            // Send typing event when user types
            typingInput.addEventListener('input', function () {
                Echo.private('typing-indicator-channel.1')
                    .whisper('typing', { user: 'RKB' });

                console.log('Typing Indicator Sent');
            });

            // Listen for typing event from others
            Echo.private('typing-indicator-channel.1')
                .listenForWhisper('typing', (e) => {
                    console.log('Typing Indicator Received:', e.user);

                    // Show typing image
                    typingImage.style.display = 'block';

                    // Clear previous timeout if user keeps typing
                    clearTimeout(typingTimeout);

                    // Hide typing image 2 seconds after last typing event
                    typingTimeout = setTimeout(() => {
                        typingImage.style.display = 'none';
                    }, 1000);
                });

        });
    </script>
</x-layouts.app>