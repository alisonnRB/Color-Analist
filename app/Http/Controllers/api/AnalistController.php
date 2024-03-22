<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use ColorThief\ColorThief;

class AnalistController extends Controller
{
    protected $image;
    protected $imageSize;

    protected $paletteHEX;
    protected $paletteRGB;

    protected $predominaceHEX;
    protected $predominaceRGB;

    protected $representativeHEX;

    protected $representativeRBG;

    protected $distintHex;
    protected $distintRGB;


    public function index(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $this->image = $this->Imageformatter($request->file('image'));
            $this->imageSize = getimagesize($request->file('image'));

            if ($this->image) {
                $this->paletteCreate();
                $this->PredominanceColor();
                $this->RepresentativeColor();
                $this->distintColor();

            } else {
                return 'imagem de formato inválido';
            }

            $response = [
                "palette-HEX" => $this->paletteHEX,
                "palette-RGB" => $this->paletteRGB,
                "predomince-HEX" => $this->predominaceHEX,
                "predomince-RGB" => $this->predominaceRGB,
                "representative-HEX" => $this->representativeHEX,
                "representative-RBB" => $this->representativeRBG,
                "distinct-HEX" => $this->distintHex,
                "distinct-RGB" => $this->distintRGB
            ];

            return $response;
        } else {
            return 'Nenhuma imagem enviada';
        }
    }

    private function Imageformatter($image)
    {
        $imageInfo = getimagesize($image->path());
        switch ($imageInfo['mime']) {
            case "image/jpeg":
                return imagecreatefromjpeg($image->path());
            case "image/png":
                return imagecreatefrompng($image->path());
            case "image/gif":
                return imagecreatefromgif($image->path());
            default:
                return false;
        }
    }


    private function paletteCreate()
    {
        $image = new ColorThief;
        $palette = $image->getPalette($this->image, 5, 5, null, 'hex');

        $RGBlist = [];
        foreach ($palette as $color) {
            array_push($RGBlist, $this->toRgb($color));
        }

        $this->paletteHEX = $palette;
        $this->paletteRGB = $RGBlist;

    }

    private function PredominanceColor()
    {
        $imagem = new ColorThief;
        $color = $imagem->getPalette($this->image, 2, 10, null, 'hex');

        $this->predominaceHEX = $color[1];
        $this->predominaceRGB = $this->toRgb($color[1]);

    }

    private function distintColor()
    {
        $imagem = new ColorThief;
        $color = $imagem->getPalette($this->image, 5, 10, null, 'hex');

        $this->distintHex = $color[3];
        $this->distintRGB = $this->toRgb($color[3]);

    }

    private function RepresentativeColor()
    {
        $colorThief = new ColorThief;
        $whidth = intval(($this->imageSize[0] * 65) / 100);
        $height = intval(($this->imageSize[1] * 65) / 100);

        $w = intval(10);
        $h = intval(10);

        $representativeColor = $colorThief->getColor($this->image, 10, ['x' => $whidth, 'y' => $height, 'h' => $h, 'w' => $w], 'hex');

        $this->representativeHEX = $representativeColor;
        $this->representativeRBG = $this->toRgb($representativeColor);
    }


    public function toRgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array("R" => $r, "G" => $g, "B" => $b);

        return $rgb;
    }

}
