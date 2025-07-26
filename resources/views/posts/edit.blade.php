{{-- edit.blade.php --}}
@extends('layouts.app')
@section('content')
<h1>投稿編集</h1>
@endsection
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">投稿編集</h1>

    @if ($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>・{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('posts.update', $post->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- タイトル --}}
        <div class="mb-4">
            <label class="block mb-1 font-semibold">タイトル</label>
            <input type="text" name="title" class="w-full border rounded p-2" value="{{ old('title', $post->title) }}" required>
        </div>

        {{-- 使用チャンピオン --}}
        <div class="mb-4">
            <label class="block mb-1 font-semibold">使用チャンピオン</label>
            <select name="champion_id" class="w-full border rounded p-2" required>
                @foreach ($champions as $champion)
                    <option value="{{ $champion->id }}" @selected(old('champion_id', $post->champion_id) == $champion->id)>
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
                    <option value="{{ $champion->id }}" @selected(old('vs_champion_id', $post->vs_champion_id) == $champion->id)>
                        {{ $champion->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- レーン --}}
        <div class="mb-4">
            <label class="block mb-1 font-semibold">レーン</