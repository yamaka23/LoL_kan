{{-- resources/views/posts/show.blade.php (例) --}}

<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h1 class="text-2xl font-bold">{{ $post->title }}</h1>
                    
                    {{-- ▼▼▼ チャンピオン画像の表示 ▼▼▼ --}}
                    <div class="mt-4 flex items-center gap-4">
                        @if ($post->champion)
                            <div>
                                <h3 class="font-semibold">使用チャンピオン</h3>
                                <img 
                                    src="https://ddragon.leagueoflegends.com/cdn/{{ $post->champion->version }}/img/champion/{{ $post->champion->image }}" 
                                    alt="{{ $post->champion->name }}"
                                    class="w-20 h-20 rounded-md">
                                <p class="text-center">{{ $post->champion->name }}</p>
                            </div>
                        @endif

                        @if ($post->vsChampion)
                            <span class="text-xl font-bold">VS</span>
                            <div>
                                <h3 class="font-semibold">対面チャンピオン</h3>
                                <img 
                                    src="https://ddragon.leagueoflegends.com/cdn/{{ $post->vsChampion->version }}/img/champion/{{ $post->vsChampion->image }}" 
                                    alt="{{ $post->vsChampion->name }}"
                                    class="w-20 h-20 rounded-md">
                                <p class="text-center">{{ $post->vsChampion->name }}</p>
                            </div>
                        @endif
                    </div>
                    

                    {{-- ▼▼▼ ルーンの表示 ▼▼▼ --}}
                    <div class="mt-6">
                        <h3 class="font-semibold mb-2">ルーン</h3>
                        <div class="flex gap-2">
                            {{-- 通常ルーン --}}
                            @foreach ($post->runes as $rune)
                                <img 
                                    src="{{ $rune->icon_path }}" {{-- ルーンはDBに完全なURLがあるのでそのまま表示 --}}
                                    alt="{{ $rune->name }}"
                                    class="w-12 h-12 bg-black rounded-full p-1">
                            @endforeach
                            {{-- ステータスルーン --}}
                             @foreach ($post->statRunes as $statRune)
                                <img 
                                    src="{{ $statRune->icon_path }}"
                                    alt="{{ $statRune->name }}"
                                    class="w-8 h-8">
                            @endforeach
                        </div>
                    </div>

                    {{-- ▼▼▼ アイテムビルドの表示 ▼▼▼ --}}
                    <div class="mt-6">
                        <h3 class="font-semibold mb-2">アイテムビルド</h3>
                        <div class="flex gap-2">
                            @foreach ($post->items as $item)
                                <img 
                                    src="https://ddragon.leagueoflegends.com/cdn/{{ $item->version }}/img/item/{{ $item->image }}" 
                                    alt="{{ $item->name }}"
                                    class="w-16 h-16 rounded-md">
                            @endforeach
                        </div>
                    </div>

                    {{-- 投稿内容 --}}
                    <div class="mt-6">
                        <h3 class="font-semibold">内容</h3>
                        <p class="mt-2 whitespace-pre-wrap">{{ $post->content }}</p>
                    </div>

                    <div class="text-center border-t dark:border-gray-700 pt-8 flex justify-center items-center gap-4">
                        <a href="{{ route('posts.index') }}" class="inline-block px-6 py-2 text-sm font-medium text-gray-400 hover:text-gray-200">
                            ← 投稿一覧に戻る
                        </a>

                        @can('update', $post)
                            <a href="{{ route('posts.edit', $post) }}" class="inline-block px-6 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-500">
                                この投稿を編集する
                            </a>
                        @endcan


                        @can('delete', $post)
                            <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-block px-6 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-500">
                                    削除
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>