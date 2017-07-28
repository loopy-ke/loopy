<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Illuminate\Http\Request;

class ImagingController extends Controller
{

    public $requiresAuth = false;

    public function __construct()
    {
        $this->middleware('auth.api', ['except' => ['getImage']]);
    }

    public function getThumbnail()
    {
        $filename = '';
        $path = storage_path('blobs/') . $filename;
        if (file_exists($path)) {
            $key = "thumb-nail-$path";
            if (\Cache::has($key)) {
                $blob = cache()->get($key);
                $ext = cache()->get("$key.ext", 'png');
            } else {
                if (class_exists('\Imagick')) {
                    $icon = stream_get_meta_data(tmpfile())["uri"];
                    try {
                        $img = new \Imagick($path . "[0]");
                    } catch (\Exception $e) {
                        $img = new \Imagick('');
                    }
                    $img->setImageFormat("png");
                    $size = min($img->getImageWidth(), $img->getImageHeight());
                    $img->cropImage($size, $size, 0, 0);
                    $img->thumbnailImage(600, 600);
                    $img->writeImage($icon);
                    $ext = 'png';
                    $blob = @file_get_contents($icon);
                    cache()->forever("$key.ext", $ext);
                    cache()->forever($key, $blob);
                } else {
                    $blob = '';
                    $ext = 'svg';
                }
            }
            return response($blob, 200, ['Content-Type' => "image/$ext", 'Content-Disposition' => 'inline;filename="thumbnail.' . $ext . '"']);
        } else {
            abort(404, "File $filename not found");
        }
    }

    public function printPdf(Request $request)
    {
        $uri = $request->url;

        $format = $request->get('format', 'pdf');

        if (!$uri) {
            abort(400, 'Bad request. Missing report ?url=foo');
        }

        $out = tempnam('/tmp', 'pdf');
        $cookies = [];
        if (count($cookies) == 0) {
            $cookies = $_COOKIE;
        }

        $client = new Client();
        $jar = new CookieJar(false);
        foreach ($cookies as $key => $cookie) {
            $jar->setCookie((new SetCookie(['Name' => $key,
                'Value' => $cookie,
                'Domain' => request()->getHost(),
                'Expires' => Carbon::now()->addDay(1)->toCookieString()])));
        }

        $response = $client->get($uri, ['cookies' => $jar]);
        $file = tempnam('/tmp', 'html');

        file_put_contents($file, $response->getBody()->getContents());

        $output = [];

        exec("prince '$file'  -o '$out.orig' 2>&1 >/dev/null", $output, $status);

        if (!env('HAS_NO_LIB_CAM_PERL')) {
            exec("rewritepdf -C '$out.orig' '$out' 2>&1 >/dev/null", $output, $status);
        } else {
            $out = "$out.orig";
        }

        if (!file_exists($out) || filesize($out) < 1) {
            abort(500, 'Apologies, unknown error!');
        }
        return response()->file($out)->deleteFileAfterSend(true);
    }
}
