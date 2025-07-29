<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{-- 投稿のタイトルをヘッダーに表示 --}}
            {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- 投稿者やチャンピオン情報 --}}
                    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        <span>投稿者: {{ $post->user->name }}</span> |
                        <span>チャンピオン: {{ $post->champion->name }}</span> |
                        <span>レーン: {{ $post->lane->name }}</span> |
                        <span>{{ $post->created_at->format('Y/m/d H:i') }}</span>
                    </div>

                    {{-- 投稿本文 --}}
                    <div class="prose dark:prose-invert max-w-none">
                        {!! nl2br(e($post->body)) !!}
                    </div>

                    {{-- 戻るボタン --}}
                    <div class="mt-6">
                        <a href="{{ route('posts.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            ← 投稿一覧に戻る
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
