<?php


namespace App\GD;

use App\Generate;

class GDPlugin
{
    public $text = [];
    public $img = [];
    /* @var Generate $generate */
    protected $generate;
    protected $draw;

    public function __construct(Generate $generate, Draw $draw)
    {
        $this->generate = $generate;
        $this->draw = $draw;
        $this->init();
    }

    public function run($input)
    {
    }

    public function init()
    {
    }
}
