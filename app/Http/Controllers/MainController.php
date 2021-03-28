<?php

namespace App\Http\Controllers;

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

        // get cats

        $site = 'https://daryo.uz';
        $response = Http::get($site);
        $this->dom->loadHTML($response);
        $array = [];
        $cat_links = $this->dom->getElementsByTagName('a');
        foreach ($cat_links as $cat_link) {
            $url = $cat_link->getAttribute('href');
            if ($url && !in_array($url, $array) && str_contains($url, 'category') && !str_contains($url, 'reklama')) {
                $array[] = $url;
            }
        }

        sleep(1);

        //get posts_links in cats

        $array_posts = [];
        foreach ($array as $cat) {
            $respon = Http::get($site . $cat);
            if ($respon->ok()) {
                $this->dom->loadHTML($respon);
                $post_links = $this->dom->getElementsByTagName('a');
                foreach ($post_links as $post_link) {
                    $url = $post_link->getAttribute('href');
                    if ($url && !in_array($url, $array_posts) && str_contains($url, date('Y/m/d'))) {
                        $array_posts[] = $url;
                    }
                }
            }
            sleep(0.5);
        }

        sleep(1);

        //get title, description, img for posts
        $array_post_data = [];
        foreach ($array_posts as $key => $post) {
            $res = Http::get($site . $post);
            if($res->ok()) {
                $this->dom->loadHTML($res, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
                $item = [
                    'title' =>  $this->dom->querySelector('.articleCont .title-1')->innerHTML,
                    'description' => $this->dom->querySelector('.articleCont .postContent')->innerHTML,
                    'image' => $this->dom->querySelector('.articleCont img')->getAttribute('src')
                    ];

                if ($item && !in_array($item, $array_post_data) && !str_contains($item['description'], 'Reklama')) {

                    array_push($array_post_data, $item);
                }
            }
            sleep(0.5);
        }


        return $array_post_data;

    }
}
