<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ isset($champion) ? $champion->name . ' の投稿一覧' : '全投稿一覧' }}
            </h2>

            @auth
                <a href="{{ route('posts.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    新規投稿を作成
                </a>
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none">
                    ログインして投稿する
                </a>
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @forelse ($posts as $post)
                        <div class="p-4 border border-gray-300 rounded mb-4">
                            <h3 class="text-lg font-bold">
                                <a href="{{ route('posts.show', $post) }}" class="text-blue-600 hover:underline">
                                    {{ $post->title }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-600">
                                投稿者: {{ optional($post->user)->name ?? '匿名' }} / {{ $post->created_at->format('Y/m/d') }}
                            </p>
                        </div>
                    @empty
                        <p class="text-gray-500">投稿はまだありません。</p>
                    @endforelse

                    <div class="mt-6">
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
