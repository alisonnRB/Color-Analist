<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use League\ColorExtractor\Client;
use League\ColorExtractor\Image;

class AnalistController extends Controller
{
    protected $image;
    protected $paletteHex;
    protected $paletteRgb;

    protected $mostPredominanceHEX;
    protected $mostPredominanceRGB;
    protected $representative;

    public function index(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $this->image = $this->Imageformatter($request->file('image'));
            if ($this->image) {
                $this->paletteCreate();
                $this->mostAndLessPredominance();

            } else {
                return 'imagem de formato inválido';
            }

            $response = [
                "palette-HEX" => $this->paletteHex,
                "palette-RGB" => $this->paletteRgb,
                "MostPredominanceHEX" => $this->mostPredominanceHEX,
                "MostPredominanceRGB" => $this->mostPredominanceRGB,
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
        $image = new Image($this->image);
        $palette = $image->extract(5);

        $RgbList = [];
        foreach ($palette as $color) {
            $color = $this->toRgb($color);
            array_push($RgbList, $color);
        }

        $this->paletteHex = $palette;
        $this->paletteRgb = $RgbList;
    }

    private function mostAndLessPredominance()
    {
        $image = new Image($this->image);
        $colors = $image->extract(500);
        $colorcount = array_count_values($colors);

        $mostFrequent = array_search(max($colorcount), $colorcount);
        $this->mostPredominanceHEX = $mostFrequent;
        $this->mostPredominanceRGB = $this->toRgb($mostFrequent);

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
