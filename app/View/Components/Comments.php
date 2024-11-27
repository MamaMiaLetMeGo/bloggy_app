<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Comments extends Component
{
    public $postId;
    public $commentsCount;

    public function __construct($postId, $commentsCount)
    {
        $this->postId = $postId;
        $this->commentsCount = $commentsCount;
    }

    public function render()
    {
        return view('components.comments');
    }
} 