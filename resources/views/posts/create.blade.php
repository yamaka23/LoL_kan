<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('新しい投稿を作成') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- エラー表示 --}}
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">おっと！</strong>
                            <span class="block sm:inline">入力内容にいくつか問題があるようです。</span>
                            <ul class="mt-3 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('posts.store') }}" method="POST">
                        @csrf

                        <div class="space-y-6">
                            {{-- タイトル --}}
                            <div>
                                <x-input-label for="title" :value="__('タイトル')" />
                                <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            </div>

                            {{-- チャンピオンとレーン --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="champion_id" :value="__('使用チャンピオン')" />
                                    <select id="champion_id" name="champion_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="">選択してください</option>
                                        @foreach ($champions as $champion)
                                            <option value="{{ $champion->id }}" @selected(old('champion_id') == $champion->id)>{{ $champion->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="lane_id" :value="__('レーン')" />
                                    <select id="lane_id" name="lane_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                        <option value="">選択してください</option>
                                        @foreach ($lanes as $lane)
                                            <option value="{{ $lane->id }}" @selected(old('lane_id') == $lane->id)>{{ $lane->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            {{-- 対面チャンピオン --}}
                            <div>
                                <x-input-label for="vs_champion_id" :value="__('対面チャンピオン（任意）')" />
                                <select id="vs_champion_id" name="vs_champion_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">選択しない</option>
                                    @foreach ($champions as $champion)
                                        <option value="{{ $champion->id }}" @selected(old('vs_champion_id') == $champion->id)>{{ $champion->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- ルーン --}}
                            {{-- ここにルーン選択のフォーム要素が入ります --}}
                            {{-- ※元のコードが複雑なため、一旦省略しています。必要に応じて追加してください。 --}}


                            {{-- アイテム --}}
                            {{-- ここにアイテム選択のフォーム要素が入ります --}}
                            {{-- ※元のコードが複雑なため、一旦省略しています。必要に応じて追加してください。 --}}


                            {{-- 内容 --}}
                            <div>
                                <x-input-label for="body" :value="__('内容')" />
                                <textarea id="body" name="body" rows="10" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('body') }}</textarea>
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <x-primary-button>
                                    {{ __('投稿する') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
