<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
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

    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            Echo.private('chat-channel.' + 1).listen('MessageSentEvent', (e) => {
                console.log('Message Receive:', e);
            })

            Echo.private('conversation-channel.' + 1).listen('ConversationEvent', (e) => {
                console.log('Conversation and Unread Message count:', e);
            })
        });
    </script>
</x-layouts.app>
