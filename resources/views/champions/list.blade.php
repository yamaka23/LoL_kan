<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            チャンピオン一覧
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if(isset($message))
                        <div class="text-center text-lg text-gray-600 my-6">
                            {{ $message }}
                        </div>
                    @endif

                    @if($champions->isNotEmpty())
                        {{-- スマホ1列、タブレット2列、PC3-4列 --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach($champions as $champion)
                                <div class="bg-white border border-gray-200 rounded-lg shadow hover:shadow-md transition p-4 text-center">
                                    @if($champion->image && $champion->version)
                                        <a href="/champions/{{ $champion->id }}/posts" class="block mb-2">
                                            <img 
                                                src="https://ddragon.leagueoflegends.com/cdn/{{ $champion->version }}/img/champion/{{ $champion->image }}" 
                                                alt="{{ $champion->name }}"
                                                class="w-28 h-28 mx-auto object-cover rounded-md"
                                            >
                                        </a>
                                    @else
                                        <div class="w-28 h-28 mx-auto bg-gray-200 flex items-center justify-center rounded-md mb-2">
                                            <span class="text-gray-500">No Image</span>
                                        </div>
                                    @endif

                                    <h3 class="text-md font-semibold text-gray-800 mt-2">{{ $champion->name }}</h3>
                                </div>
                            @endforeach
                        </div>
                    @else
                        @unless(isset($message))
                            <p class="text-center text-gray-600">表示できるチャンピオンがいません。</p>
                        @endunless
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
