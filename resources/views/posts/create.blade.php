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
                                items: {{ json_encode($items) }},
                                runePaths: {{ json_encode($runePaths) }},
                                runesByPath: {{ json_encode($runesByPath) }},
                                statRunes: {{ json_encode($statRunes) }},
                                oldChampionId: '{{ old('champion_id') }}',
                                oldVsChampionId: '{{ old('vs_champion_id') }}',
                                itemTagMap: {{ json_encode($itemTagMap ?? []) }}
                            })"
                            x-init="init()">


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
                                
                                <div id="rune-hidden-inputs" class="hidden">
                                    <template x-for="runeId in normalRuneIds">
                                        <input type="hidden" name="runes[]" :value="runeId">
                                    </template>
                                    <template x-for="statRuneId in statRuneIds">
                                        <input type="hidden" name="stat_runes[]" :value="statRuneId">
                                    </template>
                                </div>
                            </div>

                            {{-- アイテム選択セクション --}}
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                                <h3 class="text-lg font-bold mb-4 text-center">アイテムビルド</h3>
                                <div class="flex justify-center gap-2 md:gap-4 p-4 bg-gray-900 rounded-lg">
                                    <template x-for="i in 6" :key="i">
                                        <div @drop.prevent="handleItemDrop($event, i - 1); $el.classList.remove('border-yellow-400')"
                                            
                                            @dragover.prevent
                                            @dragenter.prevent="$el.classList.add('border-yellow-400')"
                                            @dragleave.prevent="$el.classList.remove('border-yellow-400')"
                                            class="drop-zone w-16 h-16 bg-gray-800 border-2 border-dashed border-gray-700 rounded-md flex items-center justify-center transition-colors">
                                            
                                            <template x-if="selectedItems[i - 1]">
                                                <div @dragstart="handleItemDragStart($event, i - 1)" draggable="true" class="w-full h-full cursor-grab">
                                                    <img :src="getItemImage(selectedItems[i - 1].image, selectedItems[i - 1].version)" :alt="selectedItems[i - 1].name" class="w-full h-full object-cover rounded-md pointer-events-none">
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                                <div class="text-center mt-4">
                                    <button type="button" @click="isItemModalOpen = true" class="text-indigo-400 hover:text-indigo-300 font-semibold">
                                        アイテム一覧から選択
                                    </button>
                                </div>
                                <template x-for="(item, index) in selectedItems">
                                     <input type="hidden" :name="`items[${index}]`" :value="item ? item.id : ''">
                                </template>
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
                            <div x-show="isChampionModalOpen" 
                                x-trap.inert.noscroll="isChampionModalOpen"
                                @keydown.escape.window="isChampionModalOpen = false" 
                                class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4" 
                                x-transition:enter="ease-out duration-300"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="ease-in duration-200"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                style="display: none;">
                                <div @click.away="isChampionModalOpen = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl max-h-[80vh] flex flex-col">
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

                            {{-- アイテム選択モーダル --}}
                            <div x-show="isItemModalOpen" @keydown.escape.window="isItemModalOpen = false" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4" style="display: none;" x-transition>
                                <div @click.away="isItemModalOpen = false" class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-6xl max-h-[90vh] flex flex-col">
                                    <div class="p-4 border-b dark:border-gray-700">
                                        <h3 class="text-lg font-bold">アイテムビルドを編集</h3>
                                    </div>
                                    <div class="p-4 bg-gray-900 border-b dark:border-gray-700">
                                        <div class="flex justify-center gap-3">
                                            <template x-for="(item, index) in selectedItems" :key="index">
                                                <div
                                                    @drop.prevent="handleItemDrop($event, index)"
                                                    @dragover.prevent
                                                    @dragenter.prevent="$el.classList.add('border-yellow-400')"
                                                    @dragleave.prevent="$el.classList.remove('border-yellow-400')"
                                                    class="drop-zone w-16 h-16 bg-gray-800 border-2 border-dashed border-gray-700 rounded-md flex items-center justify-center relative transition-colors">
                                                    <div x-if="item" 
                                                        draggable="true" 
                                                        @dragstart="handleItemDragStart($event, index, null)"
                                                        class="w-full h-full cursor-grab relative group">
                                                        <img :src="getItemImage(item.image, item.version)" :alt="item.name" class="w-full h-full object-cover rounded-md pointer-events-none">
                                                        <button type="button" @click="removeItemFromBuild(index)" class="absolute top-0 right-0 -mt-2 -mr-2 w-5 h-5 bg-red-600 text-white rounded-full flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity z-10">&times;</button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="flex flex-1 min-h-0">
                                        <div class="w-1/4 md:w-1/5 p-4 border-r dark:border-gray-700 overflow-y-auto">
                                            <h4 class="font-bold mb-2">フィルター</h4>
                                            <div class="space-y-1">
                                                <template x-for="(japanese, english) in itemTagMap" :key="english">
                                                    <label class="flex items-center space-x-2 cursor-pointer">
                                                        <input type="checkbox" x-model="selectedItemTags" :value="english" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                                        <span x-text="japanese"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="w-3/4 md:w-4/5 flex flex-col">
                                            <div class="p-4 border-b dark:border-gray-700">
                                                <input type="text" x-model="itemSearchQuery" placeholder="アイテムを検索 (ひらがな可)..." class="w-full p-2 rounded-md bg-gray-100 dark:bg-gray-900 border-gray-300 dark:border-gray-600">
                                            </div>
                                            <div class="p-4 overflow-y-auto flex-1 min-h-0">
                                                <div class="grid grid-cols-5 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-2">
                                                    <template x-for="item in filteredItems" :key="item.id">
                                                        {{-- ▼▼▼ @clickとdraggable/@dragstartを両方設定 ▼▼▼ --}}
                                                        <div 
                                                            @click="addItemToBuild(item)" 
                                                            draggable="true"
                                                            @dragstart="handleItemDragStart($event, null, item)"
                                                            class="p-1 rounded-md hover:bg-gray-700 dark:hover:bg-gray-700 transition cursor-grab">
                                                            <img :src="getItemImage(item.image, item.version)" class="w-12 h-12 rounded-md object-cover pointer-events-none">
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-4 border-t dark:border-gray-700 text-right">
                                        <button type="button" @click="isItemModalOpen = false" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-md font-semibold">
                                            完了
                                        </button>
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
                debugStatus: '初期化中...',
                //チャンピオンセレクトプロパティ
                allChampions: data.champions,
                isChampionModalOpen: false,
                searchQuery: '',
                modalType: 'main',
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

                //アイテムセレクトプロパティ
                allItems: data.items,
                itemTagMap: data.itemTagMap,
                isItemModalOpen: false,
                itemSearchQuery: '',
                selectedItemTags: [],
                selectedItems: Array(6).fill(null),

                //【修正】重複していたinit()を1つに統合
                init() {
                    this.availableSubPaths = this.runePaths;
                    this.debugStatus = 'Ready!';
                    console.log('formController Initialized Successfully!');
                },

                //チャンピオンセレクトメゾット
                //【修正】エラーを回避するため、IDが存在しない場合はnullを返すように変更
                get selectedChampion() {
                    if (!this.selectedChampionId) return null;
                    return this.allChampions.find(c => c.id == this.selectedChampionId);
                },
                get selectedVsChampion() {
                    if (!this.selectedVsChampionId) return null;
                    return this.allChampions.find(c => c.id == this.selectedVsChampionId);
                },
                get filteredChampions() {
                    if (this.searchQuery === '') return this.allChampions;
                    const queryLower = this.searchQuery.toLowerCase();
                    const queryKana = this.hiraToKana(queryLower);
                    return this.allChampions.filter(c => {
                        if (!c || !c.name) return false;
                        const nameLower = c.name.toLowerCase();
                        return nameLower.includes(queryLower) || nameLower.includes(queryKana);
                    });
                },
                openChampionModal(type) {
                    this.modalType = type;
                    this.isChampionModalOpen = true;
                },
                selectChampion(champion) {
                    if (this.modalType === 'main') {
                        this.selectedChampionId = champion.id;
                    } else {
                        this.selectedVsChampionId = champion.id;
                    }
                    this.isChampionModalOpen = false;
                    this.searchQuery = '';
                },
                getChampionImage(image, version) {
                    return `https://ddragon.leagueoflegends.com/cdn/${version}/img/champion/${image}`;
                },

                //ルーンセレクトメゾット
                get normalRuneIds() {
                    return [...Object.values(this.selectedMainRunes), ...Object.values(this.selectedSubRunes)];
                },
                get statRuneIds() {
                    return Object.values(this.selectedStatRunes);
                },
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
                            delete this.selectedSubRunes[tierIndex];
                            this.subSelectionOrder = this.subSelectionOrder.filter(t => t !== tierIndex);
                        } else if (isTierSelectedAtAll) {
                            this.selectedSubRunes[tierIndex] = runeId;
                        } else if (Object.keys(this.selectedSubRunes).length < 2) {
                            this.selectedSubRunes[tierIndex] = runeId;
                            this.subSelectionOrder.push(tierIndex);
                        } else {
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
                },


                //アイテムセレクトメゾット
                addItemToBuild(item) {
                    const firstEmptyIndex = this.selectedItems.indexOf(null);
                    if (firstEmptyIndex !== -1) {
                        this.selectedItems[firstEmptyIndex] = item;
                    } else {
                        alert('ビルドがいっぱいです。');
                    }
                },
                removeItemFromBuild(index) {
                    this.selectedItems[index] = null;
                },
                handleItemDragStart(event, fromIndex = null, item = null) {
                    const dragData = item ? { type: 'new', item: item } : { type: 'move', fromIndex: fromIndex };
                    event.dataTransfer.setData('application/json', JSON.stringify(dragData));
                    event.dataTransfer.effectAllowed = 'move';
                },
                handleItemDrop(event, toIndex) {
                    const dragData = JSON.parse(event.dataTransfer.getData('application/json'));
                    if (dragData.type === 'new') {
                        this.selectedItems[toIndex] = dragData.item;
                    } else if (dragData.type === 'move') {
                        const fromIndex = dragData.fromIndex;
                        if (fromIndex === toIndex) return;
                        [this.selectedItems[fromIndex], this.selectedItems[toIndex]] = [this.selectedItems[toIndex], this.selectedItems[fromIndex]];
                    }
                    event.target.closest('.drop-zone').classList.remove('border-yellow-400');
                },
                getItemImage(image, version) {
                    return `https://ddragon.leagueoflegends.com/cdn/${version}/img/item/${image}`;
                },
                get filteredItems() {
                    let items = this.allItems;
                    if (this.selectedItemTags.length > 0) {
                        items = items.filter(item =>
                            this.selectedItemTags.every(tag => item.tags && item.tags.includes(tag))
                        );
                    }
                    if (this.itemSearchQuery.trim() !== '') {
                        const queryLower = this.itemSearchQuery.toLowerCase();
                        const queryKana = this.hiraToKana(queryLower);
                        items = items.filter(item => {
                            if (!item || !item.name) return false;
                            const nameLower = item.name.toLowerCase();
                            return nameLower.includes(queryLower) || nameLower.includes(queryKana);
                        });
                    }
                    return items;
                },

               
                hiraToKana(str) {
                    return str.replace(/[\u3041-\u3096]/g, function(match) {
                        const chr = match.charCodeAt(0) + 0x60;
                        return String.fromCharCode(chr);
                    });
                },
            }
        }
    </script>
</x-app-layout>