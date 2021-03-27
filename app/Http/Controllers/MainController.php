<?php

namespace App\Http\Controllers;

use Goutte\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use IvoPetkov\HTML5DOMDocument;

class MainController extends Controller
{
    public $dom;

    public function __construct()
    {
        set_time_limit(8000000);
        $this->dom = new HTML5DOMDocument();
    }

    public function index()
    {
        $site = 'https://daryo.uz';
        $response = Http::get($site);
        $this->dom->loadHTML($response);
        $array = [];
        $as = $this->dom->querySelectorAll('ul li a');
        for ($i = 0; $i < count($as); $i++) {
            $url = $as[$i]->getAttribute('href');
            if ($url && !in_array($url, $array) && str_contains($url, 'category') && !str_contains($url, 'reklama')) {
                $array[] = $url;
            }
        }

        sleep(1);

        $array_posts = [];
        foreach ($array as $cat) {
            $respon = Http::get($site . $cat);
            if ($respon->ok()) {
                $this->dom->loadHTML($respon);
                $itemD = $this->dom->querySelectorAll('.itemDatas .itemData');
                for ($k = 0; $k < count($itemD); $k++) {
                    $data = $itemD[$k]->querySelector('span')->innerHTML;
                    if($data && !in_array($data, $array_posts) && str_contains($data, 'Bugun')) {
                        $array_posts[] = $data;
                    }
                }
//                $as = $this->dom->querySelectorAll('.items .itemTitle a');
//                for ($j = 0; $j < count($as); $j++) {
//                    $url = $as[$j]->getAttribute('href');
//                    if ($url && !in_array($url, $array_posts)) {
//                        $array_posts[] = $url;
//                    }
//                }
            }
            sleep(0.5);
        }
//
//        sleep(1);
//
//        $array_post = [];
//        foreach ($array_posts as $post) {
//            $res = Http::get($site . $post);
//            if($res->ok()) {
//                $this->dom->loadHTML($res);
//                $title = $this->dom->querySelector('h1')->innerHTML;
//                if ($title && !in_array($title, $array_post)) {
//                    $array_post[] = $title;
//                }
//            }
//            sleep(0.5);
//        }


        return $array_posts;


//        dd($crawler_data);
    }
}
