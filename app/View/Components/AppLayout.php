<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * コンポーネントを表すビュー/コンテンツを取得します。
     */
    public function render(): View
    {
        
        return view('layouts.app');
    }
}
