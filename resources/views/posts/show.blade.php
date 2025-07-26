@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">{{ $post->title }}</h1>

    <p class="text-sm text-gray-600 mb-2">
        投稿者: {{ $post->user->summoner_name ?? '不明' }}｜投稿日: {{ $post->created_at->format('Y/m/d') }}
    </p>

    <p><strong>使用チャンピオン:</strong> {{ $post->champion->name }}</p>
    @if($post->vs_champion_id)
        <p><strong>対面チャンピオン:</strong> {{ $post->vsChampion->name }}</p>
    @endif
    <p><strong>レーン:</strong> {{ $post->lane->name }}</p>

    @if($post->content)
        <p class="mt-4"><strong>内容:</strong><br>{{ $post->content }}</p>
    @endif

    <div class="mt-6">
        <a href="{{ route('posts.index') }}" class="text-blue-500 hover:underline">← 戻る</a>
    </div>
</div>
@endsection
