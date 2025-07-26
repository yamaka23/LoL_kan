@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">投稿作成</h1>

    @if ($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>・{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('posts.store') }}" method="POST">
        @csrf

        {{-- タイトル --}}
        <div class="mb-4">
            <label class="block mb-1 font-semibold">タイトル</label>
            <input type="text" name="title" class="w-full border rounded p-2" value="{{ old('title') }}" required>
        </div>

        {{-- 使用チャンピオン --}}
        <div class="mb-4">
            <label class="block mb-1 font-semibold">使用チャンピオン</label>
            <select name="champion_id" class="w-full border rounded p-2" required>
                <option value="">選択してください</option>
                @foreach ($champions as $champion)
                    <option value="{{ $champion->id }}" @selected(old('champion_id') == $champion->id)>
                        {{ $champion->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- 対面チャンピオン --}}
        <div class="mb-4">
            <label class="block mb-1 font-semibold">対面チャンピオン（任意）</label>
            <select name="vs_champion_id" class="w-full border rounded p-2">
                <option value="">選択しない</option>
                @foreach ($champions as $champion)
                    <option value="{{ $champion->id }}" @selected(old('vs_champion_id') == $champion->id)>
                        {{ $champion->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- レーン --}}
        <div class="mb-4">
            <label class="block mb-1 font-semibold">レーン</label>
            <select name="lane_id" class="w-full border rounded p-2" required>
                <option value="">選択してください</option>
                @foreach ($lanes as $lane)
                    <option value="{{ $lane->id }}" @selected(old('lane_id') == $lane->id)>
                        {{ $lane->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ルーン（複数可） --}}
        {{-- ▼メインパス --}}
        <div class="mb-4">
            <label class="block mb-1 font-semibold">メインパス</label>
            <select name="main_rune_path" class="w-full border rounded p-2" required>
                @foreach ($runes as $rune)
                    <option value="{{ $rune->id }}" @selected(old('main_rune_path') == $rune->id)>
                        {{ $rune->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ▼メインルーン 4つ --}}
        <div class="mb-4">
            <label class="block mb-1 font-semibold">メインルーン（4つ）</label>
            @for ($i = 0; $i < 4; $i++)
                <select name="main_runes[]" class="w-full border rounded p-2 mb-2">
                    <option value="">選択しない</option>
                    @foreach ($runes as $rune)
                        <option value="{{ $rune->id }}" @selected(in_array($rune->id, old('main_runes', [])))>
                            {{ $rune->name }}
                        </option>
                    @endforeach
                </select>
            @endfor
        </div>

        {{-- ▼サブパス --}}
        <div class="mb-4">
            <label class="block mb-1 font-semibold">サブパス</label>
            <select name="sub_rune_path" class="w-full border rounded p-2" required>
                @foreach ($runes as $rune)
                    <option value="{{ $rune->id }}" @selected(old('sub_rune_path') == $rune->id)>
                        {{ $rune->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- ▼サブルーン 2つ --}}
        <div class="mb-4">
            <label class="block mb-1 font-semibold">サブルーン（2つ）</label>
            @for ($i = 0; $i < 2; $i++)
                <select name="sub_runes[]" class="w-full border rounded p-2 mb-2">
                    <option value="">選択しない</option>
                    @foreach ($runes as $rune)
                        <option value="{{ $rune->id }}" @selected(in_array($rune->id, old('sub_runes', [])))>
                            {{ $rune->name }}
                        </option>
                    @endforeach
                </select>
            @endfor
        </div>

        {{-- ▼ステータスルーン 3つ --}}
        <div class="mb-4">
            <label class="block mb-1 font-semibold">ステータスルーン（攻撃 / フレックス / 防御）</label>
            @for ($i = 0; $i < 3; $i++)
                <select name="stat_runes[]" class="w-full border rounded p-2 mb-2">
                    <option value="">選択しない</option>
                    @foreach ($runes as $rune)
                        <option value="{{ $rune->id }}" @selected(in_array($rune->id, old('stat_runes', [])))>
                            {{ $rune->name }}
                        </option>
                    @endforeach
                </select>
            @endfor
        </div>



        {{-- アイテム（順序あり） --}}
        <div class="mb-4">
            <label class="block mb-1 font-semibold">アイテム（最大6つまで）</label>
            @for ($i = 0; $i < 6; $i++)
                <select name="items[{{ $i }}]" class="w-full border rounded p-2 mb-2">
                    <option value="">選択しない</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" @selected(old("items.$i") == $item->id)>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            @endfor
        </div>


        {{-- 内容 --}}
        <div class="mb-4">
            <label class="block mb-1 font-semibold">内容</label>
            <textarea name="content" rows="5" class="w-full border rounded p-2">{{ old('content') }}</textarea>
        </div>

        <div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">投稿する</button>
        </div>
    </form>
</div>
@endsection
