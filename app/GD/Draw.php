<?php

namespace App\GD;

class Draw
{
    private $main;
    public $replace = [];
    public $img = [];
    public $draw = [];
    public $server;

    public function __construct($server, $str)
    {
        $this->server = $server;
        $data = json_decode($str);
        $this->draw = $this->read($data);
    }

    public function main()
    {
        $this->plugin();
        $bg = imagecreatefromstring(file_get_contents(Base::res($this->draw['background'], 'image')));
        $this->main = imagecreatetruecolor($this->draw['width'], $this->draw['height']);
        imagesavealpha($this->main, true);
        imagefill($this->main, 0, 0, imagecolorallocatealpha($this->main, 0, 0, 0, 127));
        imagecolorallocatealpha($this->main, 0, 0, 0, 127);
        imagecopyresized(
            $this->main,
            $bg,
            0,
            0,
            0,
            0,
            $this->draw['width'],
            $this->draw['height'],
            imagesx($bg),
            imagesy($bg)
        );
        foreach ($this->draw['img'] as $img) {
            $src = imagecreatefromstring($this->img[$img['data']]);
            $this->imagecopymerge_alpha(
                $this->main,
                $src,
                $img['x'],
                $img['y'],
                0,
                0,
                $img['width'],
                $img['height'],
                imagesx($src),
                imagesy($src)
            );
        }
        foreach ($this->draw['draw'] as $draw) {
            $this->draw_color(
                $this->replace($draw['text']),
                $draw['x'],
                $draw['y'],
                $draw['size'],
                Base::res($draw['font'], 'font'),
                $draw['angle']
            );
        }
        ob_start();
        imagepng($this->main);
        $op = ob_get_contents();
        ob_end_clean();
        return $op;
    }

    private function read(&$object)
    {
        if (is_object($object)) {
            $arr = (array)($object);
        } else {
            $arr = &$object;
        }
        if (is_array($arr)) {
            foreach ($arr as $varName => $varValue) {
                $arr[$varName] = $this->read($varValue);
            }
        }
        return $arr;
    }

    private function plugin()
    {
        foreach ($this->draw['plugin'] as $plugin) {
            $plugin_class = '\\App\\Plugin\\' . $plugin['plugin'] . '\\Main';
            $p = new $plugin_class($this->server, $this);
            /* @var \App\GD\GDPlugin $p */
            foreach ($plugin['input'] as $key => $value) {
                $plugin['input'][$key] = $this->input($value);
            }
            $p->run($plugin['input']);
            foreach ($p->text as $key => $value) {
                if (!@is_string((string)$value)) {
                    $value = 'null';
                } else {
                    $value = (string)$value;
                }
                if (!is_null(@$plugin['name'])) {
                    $key = $plugin['name'] . '->' . $key;
                }
                $this->replace[$key] = $value;
            }
            foreach ($p->img as $key => $value) {
                if (!is_null(@$plugin['name'])) {
                    $key = $plugin['name'] . '->' . $key;
                }
                $this->img[$key] = $value;
            }
        }
    }

    private function input(string $str)
    {
        foreach ($_GET as $key => $value) {
            $str = str_ireplace('<!' . $key . '!>', $value, $str);
        }
        return $str;
    }

    public function replace(string $str, array $had = [])
    {
        foreach ($this->replace as $key => $value) {
            if (@$had[$key]) {
                continue;
            } else {
                if (strpos($str, '<?' . $key . '?>') === false) {
                    continue;
                } else {
                    $arr = $had;
                    $arr[$key] = true;
                    $str = str_ireplace('<?' . $key . '?>', $this->replace($value, $arr), $str);
                }
            }
        }
        return $str;
    }

    private function draw_color(string $color_raw, int $x = 12, int $y = 12, int $size = 12, string $font = null, int $angle = 0)
    {
        $lines = explode("\n", $color_raw);
        foreach ($lines as $line) {
            $raws = explode('§', $line);
            $color = imagecolorallocate($this->main, 255, 255, 255);
            $nx = $x;
            $ny = $y;
            $isf = true;
            foreach ($raws as $raw) {
                if (@Base::color_code(substr($raw, 0, 1)) && !$isf) {
                    $color_code = Base::color_code(substr($raw, 0, 1));
                    $color = imagecolorallocate(
                        $this->main,
                        Base::code_color($color_code)[0],
                        Base::code_color($color_code)[1],
                        Base::code_color($color_code)[2]
                    );
                    $text = substr($raw, 1);
                } elseif ($isf) {
                    $text = $raw;
                } else {
                    $text = '§' . $raw;
                }
                $isf = false;
                $box = imagettfbbox($size, $angle, $font, $text);
                imagefttext($this->main, $size, $angle, $nx, $ny - $box[7] + 2, $color, $font, $text);
                $nx += $box[2];
                $ny += $box[3] - $box[1];
            }
            $y += $size + 4;
        }
    }

    public function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $pct = 1)
    {
        if (!isset($pct)) {
            return false;
        }
        $w = imagesx($src_im);
        $h = imagesy($src_im);
        imagealphablending($src_im, false);
        $minalpha = 127;
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $alpha = (imagecolorat($src_im, $x, $y) >> 24) & 0xFF;
                if ($alpha < $minalpha) {
                    $minalpha = $alpha;
                }
            }
        }
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $colorxy = imagecolorat($src_im, $x, $y);
                $alpha = ($colorxy >> 24) & 0xFF;
                if ($minalpha !== 127) {
                    $alpha = 127 + 127 * $pct * ($alpha - 127) / (127 - $minalpha);
                } else {
                    $alpha += 127 * $pct;
                }
                $alphacolorxy = imagecolorallocatealpha($src_im, ($colorxy >> 16) & 0xFF, ($colorxy >> 8) & 0xFF, $colorxy & 0xFF, $alpha);
                if (!imagesetpixel($src_im, $x, $y, $alphacolorxy)) {
                    return false;
                }
            }
        }
        imagecopyresized($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        return true;
    }
}
