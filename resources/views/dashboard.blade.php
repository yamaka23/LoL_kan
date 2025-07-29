<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-4">{{ __("You're logged in!") }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">LoL Kanbanへようこそ！あなたのチャンピオンビルドを共有しましょう。</p>
                        
                        <div class="flex space-x-4">
                            <a href="{{ route('posts.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 dark:hover:bg-blue-400 focus:bg-blue-500 dark:focus:bg-blue-400 active:bg-blue-700 dark:active:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                📝 新しい投稿を作成
                            </a>
                            
                            <a href="{{ route('posts.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                📋 投稿一覧を見る
                            </a>
                            
                            <a href="{{ route('champions.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 dark:hover:bg-green-400 focus:bg-green-500 dark:focus:bg-green-400 active:bg-green-700 dark:active:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                🏆 チャンピオン一覧
                            </a>
                        </div>
                    </div>

                    <!-- チャンピオン一覧セクション -->
                    @if($champions->count() > 0)
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">チャンピオン一覧</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($champions as $champion)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150">
                                <a href="{{ route('champions.posts', $champion->id) }}" class="block text-center">
                                    @if($champion->image)
                                        <img src="https://ddragon.leagueoflegends.com/cdn/{{ $champion->version }}/img/champion/{{ $champion->image }}" 
                                             alt="{{ $champion->name }}" 
                                             class="w-16 h-16 mx-auto rounded-full object-cover mb-2">
                                    @else
                                        <div class="w-16 h-16 mx-auto rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center mb-2">
                                            <span class="text-gray-500 dark:text-gray-400 text-xs">No Image</span>
                                        </div>
                                    @endif
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $champion->name }}</span>
                                </a>
                            </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-4">
                            <a href="{{ route('champions.index') }}" 
                               class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                すべてのチャンピオンを見る →
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="mt-8 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                        <h3 class="text-lg font-semibold mb-2 text-yellow-800 dark:text-yellow-200">チャンピオンデータがありません</h3>
                        <p class="text-yellow-700 dark:text-yellow-300 mb-4">チャンピオン一覧を表示するには、先にデータを同期する必要があります。</p>
                        @can('sync-data')
                        <a href="{{ route('sync.champions') }}" 
                           class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            データを同期する
                        </a>
                        @endcan
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
