<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('新しい投稿を作成') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{--エラーメッセージの表示--}}
                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">入力エラー</strong>
                            <ul class="mt-2 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                    <form action="{{ route('posts.store') }}" method="POST">
                        @csrf
                        <div class="space-y-8" 
                             x-data="formController({
                                champions: {{ json_encode($champions) }},
                                runePaths: {{ json_encode($runePaths) }},
                                runesByPath: {{ json_encode($runesByPath) }},
                                statRunes: {{ json_encode($statRunes) }},
                                oldChampionId: '{{ old('champion_id') }}',
                                oldVsChampionId: '{{ old('vs_champion_id') }}'
                             })">


                            {{-- 基本情報セクション --}}
                            <div class="space-y-6">

                                {{-- タイトル --}}
                                <div>
                                    <x-input-label for="title" :value="__('タイトル')" />
                                    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                                </div>

                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                    {{-- 使用チャンピオン --}}
                                    <div>
                                        <x-input-label for="champion_id" :value="__('使用チャンピオン')" />
                                        <input type="hidden" name="champion_id" x-model="selectedChampionId">
                                        <button type="button" @click="openChampionModal('main')" class="mt-1 w-full h-16 flex items-center p-2 bg-gray-100 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-left">
                                            <template x-if="selectedChampion">
                                                <div class="flex items-center">
                                                    <img :src="getChampionImage(selectedChampion.image, selectedChampion.version)" class="w-12 h-12 rounded-md mr-4">
                                                    <span class="font-semibold" x-text="selectedChampion.name"></span>
                                                </div>
                                            </template>
                                            <template x-if="!selectedChampion">
                                                <span class="text-gray-500">チャンピオンを選択...</span>
                                            </template>
                                        </button>
                                    </div>
                                    
                                    {{-- 対戦相手チャンピオン --}}
                                    <div>
                                        <x-input-label for="vs_champion_id" :value="__('対面チャンピオン（任意）')" />
                                        <input type="hidden" name="vs_champion_id" x-model="selectedVsChampionId">
                                        <button type="button" @click="openChampionModal('vs')" class="mt-1 w-full h-16 flex items-center p-2 bg-gray-100 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-left">
                                            <template x-if="selectedVsChampion">
                                                <div class="flex items-center">
                                                    <img :src="getChampionImage(selectedVsChampion.image, selectedVsChampion.version)" class="w-12 h-12 rounded-md mr-4">
                                                    <span class="font-semibold" x-text="selectedVsChampion.name"></span>
                                                </div>
                                            </template>
                                            <template x-if="!selectedVsChampion">
                                                <span class="text-gray-500">チャンピオンを選択...</span>
                                            </template>
                                        </button>
                                    </div>

                                    {{-- レーン選択 --}}
                                    <div>
                                        <x-input-label for="lane_id" :value="__('レーン')" />
                                        <select id="lane_id" name="lane_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 dark:border-gray-700" required>
                                            <option value="">選択してください</option>
                                            @foreach ($lanes as $lane)
                                                <option value="{{ $lane->id }}" @selected(old('lane_id') == $lane->id)>{{ $lane->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- ルーン選択セクション --}}
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                                <h3 class="text-lg font-bold mb-8 text-center">ルーンを選択</h3>
                                
                                <div class="flex justify-center items-start gap-12 md:gap-20">
                                    {{-- メインパス（左側） --}}
                                    <div class="flex flex-col items-center space-y-4">
                                        <div class="bg-gray-900 bg-opacity-90 rounded-xl p-4 border-2 border-yellow-500 shadow-lg">
                                            <div class="text-yellow-400 text-sm font-bold text-center mb-3">メイン</div>
                                            <div class="flex space-x-2 justify-center">
                                                <template x-for="path in runePaths" :key="path.id">
                                                    <button type="button" @click="selectMainPath(path.id)" class="p-1.5 rounded-lg transition-all duration-200 hover:scale-105" :class="{'ring-2 ring-yellow-400 bg-yellow-400 bg-opacity-20 scale-105': mainPathId == path.id, 'opacity-50 hover:opacity-80 grayscale hover:grayscale-0': mainPathId != null && mainPathId != path.id}">
                                                        <img :src="path.icon_path" :alt="path.name" class="w-10 h-10">
                                                    </button>
                                                </template>
                                            </div>
                                        

                                            <div class="space-y-5" x-show="mainPathId" x-transition>
                                                <template x-for="(tier, tierIndex) in mainPathTiers" :key="tierIndex">
                                                    <div class="flex justify-center space-x-3">
                                                        {{-- ▼▼▼ キーストーン用の表示 ▼▼▼ --}}
                                                        <template x-if="tierIndex === 0">
                                                            <template x-for="rune in tier" :key="rune.id">
                                                                <button type="button" @click="selectRune('main', tierIndex, rune.id)" class="p-1.5 rounded-lg transition-all duration-200 hover:scale-110" :class="{'ring-2 ring-yellow-400 scale-110 bg-yellow-400 bg-opacity-20': isSelected('main', tierIndex, rune.id), 'opacity-30 grayscale hover:opacity-70 hover:grayscale-0': !isSelected('main', tierIndex, rune.id) && selectedMainRunes[tierIndex] != undefined}">
                                                                    <img :src="rune.icon_path" :alt="rune.name" class="w-10 h-10">
                                                                </button>
                                                            </template>
                                                        </template>
                                                        {{-- ▼▼▼ 通常ルーン用の表示 ▼▼▼ --}}
                                                        <template x-if="tierIndex > 0">
                                                            <template x-for="rune in tier" :key="rune.id">
                                                                <button type="button" @click="selectRune('main', tierIndex, rune.id)" class="p-1.5 rounded-lg transition-all duration-200 hover:scale-110" :class="{'ring-2 ring-yellow-400 scale-110 bg-yellow-400 bg-opacity-20': isSelected('main', tierIndex, rune.id), 'opacity-30 grayscale hover:opacity-70 hover:grayscale-0': !isSelected('main', tierIndex, rune.id) && selectedMainRunes[tierIndex] != undefined}">
                                                                    <img :src="rune.icon_path" :alt="rune.name" class="w-9 h-9">
                                                                </button>
                                                            </template>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        
                                        </div>
                                    </div>

                                    {{-- 右側エリア --}}
                                    <div class="flex flex-col space-y-6">
                                        {{-- サブパス（右上） --}}
                                        <div class="bg-gray-900 bg-opacity-90 rounded-xl p-4 border-2 border-cyan-500 shadow-lg">
                                            <div class="text-cyan-400 text-sm font-bold text-center mb-3">サブ</div>
                                            <div class="flex flex-wrap justify-center gap-2 mb-4">
                                                <template x-for="path in availableSubPaths" :key="path.id">
                                                    <button type="button" @click="selectSubPath(path.id)" class="p-1.5 rounded-lg transition-all duration-200 hover:scale-105" :class="{'ring-2 ring-cyan-400 bg-cyan-400 bg-opacity-20 scale-105': subPathId == path.id, 'opacity-50 hover:opacity-80 grayscale hover:grayscale-0': subPathId != null && subPathId != path.id}">
                                                        <img :src="path.icon_path" :alt="path.name" class="w-10 h-10">
                                                    </button>
                                                </template>
                                            </div>
                                            <div class="space-y-3" x-show="subPathId" x-transition>
                                                <template x-for="(tier, tierIndex) in subPathTiers" :key="tierIndex">
                                                    <div class="flex justify-center space-x-4 transition-opacity" :class="{'opacity-30 grayscale': isSubTierDisabled(tierIndex + 1)}">
                                                        <template x-for="rune in tier" :key="rune.id">
                                                            <button type="button" @click="selectRune('sub', tierIndex + 1, rune.id)" 
                                                                    class="p-1.5 rounded-lg transition-all duration-200 hover:scale-110" 
                                                                    :class="{
                                                                        'ring-2 ring-cyan-400 scale-110 bg-cyan-400 bg-opacity-20': isSelected('sub', tierIndex + 1, rune.id),
                                                                        'opacity-30 grayscale hover:opacity-70 hover:grayscale-0': !isSelected('sub', tierIndex + 1, rune.id) && selectedSubRunes.hasOwnProperty(tierIndex + 1)
                                                                    }">
                                                                <img :src="rune.icon_path" :alt="rune.name" class="w-8 h-8">
                                                            </button>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- ステータスルーン（右下） --}}
                                        <div class="bg-gray-900 bg-opacity-90 rounded-xl p-4 border-2 border-purple-500 shadow-lg">
                                            <div class="text-purple-400 text-sm font-bold text-center mb-3">ステータス</div>
                                            <div class="space-y-3">
                                                <template x-for="(tier, tierIndex) in statRunes" :key="tierIndex">
                                                    <div class="flex justify-center space-x-3">
                                                        <template x-for="rune in tier" :key="rune.id">
                                                            <button type="button" @click="selectRune('stat', tierIndex, rune.id)" class="p-1.5 rounded-lg transition-all duration-200 hover:scale-110" :class="{'ring-2 ring-purple-400 scale-110 bg-purple-400 bg-opacity-20': isSelected('stat', tierIndex, rune.id), 'opacity-30 grayscale hover:opacity-70 hover:grayscale-0': !isSelected('stat', tierIndex, rune.id) && selectedStatRunes[tierIndex] != undefined}">
                                                                <img :src="rune.icon_path" :alt="rune.name" class="w-5 h-5">
                                                            </button>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <input type="hidden" name="runes" :value="JSON.stringify(selectedRuneIds)">
                            </div>

                            {{-- 内容入力欄と投稿ボタン --}}
                            <div class="space-y-6">
                                <div>
                                    <x-input-label for="content" :value="__('内容')" />
                                    <textarea id="content" name="content" rows="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 dark:border-gray-700">{{ old('content') }}</textarea>
                                </div>
                                <div class="flex items-center justify-end">
                                    <x-primary-button>
                                        {{ __('投稿する') }}
                                    </x-primary-button>
                                </div>
                            </div>

                            {{-- チャンピオン選択モーダル --}}
                            <div x-show="isModalOpen" 
                                x-trap.inert.noscroll="isModalOpen"
                                @keydown.escape.window="isModalOpen = false" 
                                class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4" 
                                x-transition:enter="ease-out duration-300"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="ease-in duration-200"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                style="display: none;">
                                <div @click.away="isModalOpen = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl max-h-[80vh] flex flex-col">
                                    <div class="p-4 border-b dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-800">
                                        <input type="text" x-model="searchQuery" placeholder="チャンピオンを検索..." class="w-full p-2 rounded-md bg-gray-100 dark:bg-gray-900 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div class="p-4 overflow-y-auto flex-1 min-h-0">
                                        <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-4">
                                            <template x-for="champion in filteredChampions" :key="champion.id">
                                                <button type="button" @click="selectChampion(champion)" class="flex flex-col items-center space-y-1 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition text-center">
                                                    <img :src="getChampionImage(champion.image, champion.version)" class="w-16 h-16 rounded-md object-cover">
                                                    <span class="text-xs truncate w-full" x-text="champion.name"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>


    {{--スクリプト--}}
    <script>
        function formController(data){
            return {
                debugStatus: '初期化中...', //デバッグステータス
                //チャンピオンセレクトプロパティ
                allChampions: data.champions,
                isModalOpen: false,
                searchQuery: '',
                modalType: 'main', // 'main' or 'vs'
                selectedChampionId: data.oldChampionId || null,
                selectedVsChampionId: data.oldVsChampionId || null,

                //ルーンセレクトプロパティ
                runePaths: data.runePaths,
                runesByPath: data.runesByPath,
                statRunes: data.statRunes,
                mainPathId: null,
                subPathId: null,
                mainPathTiers: [],
                subPathTiers: [],
                availableSubPaths: [],
                selectedMainRunes: {},      
                selectedSubRunes: {},     
                subSelectionOrder: [],      
                selectedStatRunes: {},      

                init() {
                    this.availableSubPaths = this.runePaths;
                    this.debugStatus = 'Ready!';
                    console.log('formController Initialized Successfully!');
                },

                //チャンピオンセレクトメゾット
                get selectedChampion() { return this.allChampions.find(c => c.id == this.selectedChampionId); },
                get selectedVsChampion() { return this.allChampions.find(c => c.id == this.selectedVsChampionId); },
                get filteredChampions() {
                    if (this.searchQuery === '') return this.allChampions;
                    const queryLower = this.searchQuery.toLowerCase();
                    const queryKana = this.hiraToKana(queryLower); 
                    return this.allChampions.filter(c => {
                        const nameLower = c.name.toLowerCase();
                        return nameLower.includes(queryLower) || nameLower.includes(queryKana);
                    });
                },
                hiraToKana(str) {
                    return str.replace(/[\u3041-\u3096]/g, function(match) {
                        const chr = match.charCodeAt(0) + 0x60;
                        return String.fromCharCode(chr);
                    });
                },
                openChampionModal(type) {
                    this.modalType = type;
                    this.isModalOpen = true;
                },
                selectChampion(champion) {
                    if (this.modalType === 'main') {
                        this.selectedChampionId = champion.id;
                    } else {
                        this.selectedVsChampionId = champion.id;
                    }
                    this.isModalOpen = false;
                    this.searchQuery = '';
                },
                getChampionImage(image, version) {
                    return `https://ddragon.leagueoflegends.com/cdn/${version}/img/champion/${image}`;
                },

                //ルーンセレクトメゾット
                get selectedRuneIds() { 
                    return [...Object.values(this.selectedMainRunes), ...Object.values(this.selectedSubRunes), ...Object.values(this.selectedStatRunes)];
                },
                init() { this.availableSubPaths = this.runePaths; },
                selectMainPath(pathId) {
                    if (this.mainPathId === pathId) return;
                    this.mainPathId = pathId;
                    this.mainPathTiers = this.runesByPath[pathId];
                    this.selectedMainRunes = {};
                    this.availableSubPaths = this.runePaths.filter(p => p.id !== pathId);
                    if (this.subPathId === pathId) {
                        this.subPathId = null;
                        this.subPathTiers = [];
                        this.selectedSubRunes = {};
                        this.subSelectionOrder = [];
                    }
                },
                selectSubPath(pathId) {
                    if (this.subPathId === pathId) return;
                    this.subPathId = pathId;
                    this.subPathTiers = this.runesByPath[pathId].slice(1);
                    this.selectedSubRunes = {};
                    this.subSelectionOrder = [];
                },
                selectRune(type, tierIndex, runeId) {
                    if (type === 'main') {
                        this.selectedMainRunes[tierIndex] = runeId;
                    } else if (type === 'sub') {
                        const isAlreadySelectedInThisTier = this.selectedSubRunes[tierIndex] === runeId;
                        const isTierSelectedAtAll = this.selectedSubRunes.hasOwnProperty(tierIndex);

                        if (isAlreadySelectedInThisTier) {
                            // 同じルーンを再度クリック => 選択解除
                            delete this.selectedSubRunes[tierIndex];
                            this.subSelectionOrder = this.subSelectionOrder.filter(t => t !== tierIndex);
                        } else if (isTierSelectedAtAll) {
                            // 同じ段の違うルーンをクリック => 選択の上書き
                            this.selectedSubRunes[tierIndex] = runeId;
                            // 選択順は変更しない
                        } else if (Object.keys(this.selectedSubRunes).length < 2) {
                            // 新しい段で、まだ2つ選択されていない場合 => 新規選択
                            this.selectedSubRunes[tierIndex] = runeId;
                            this.subSelectionOrder.push(tierIndex);
                        } else {
                            // 新しい段で、すでに2つ選択済みの場合 => 一番古いものを削除して新規選択
                            const oldestTier = this.subSelectionOrder.shift();
                            delete this.selectedSubRunes[oldestTier];
                            this.selectedSubRunes[tierIndex] = runeId;
                            this.subSelectionOrder.push(tierIndex);
                        }
                    } else if (type === 'stat') {
                        this.selectedStatRunes[tierIndex] = runeId;
                    }
                },
                isSelected(type, tierIndex, runeId) {                   
                    if (type === 'main') return this.selectedMainRunes[tierIndex] === runeId;
                    if (type === 'sub') return this.selectedSubRunes[tierIndex] === runeId;
                    if (type === 'stat') return this.selectedStatRunes[tierIndex] === runeId;
                    return false;
                },
                isSubTierDisabled(tierIndex) {
                    const selectedCount = Object.keys(this.selectedSubRunes).length;
                    if (selectedCount < 2) return false;
                    return !this.selectedSubRunes.hasOwnProperty(tierIndex);
                }
            }
        }
    </script>
</x-app-layout>
