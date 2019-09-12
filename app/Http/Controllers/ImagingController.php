<?php

namespace App\Http\Controllers;

use App\Helpers\Prince;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Illuminate\Http\Request;

class ImagingController extends Controller
{


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

    public function convert(Request $request, $format)
    {
        $uri = $request->url;
        if ($uri) {
            $cookies = $request->get('cookies');
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
            return $this->convertFile($file, $format);
        } else if ($request->isMethod('post') && $request->hasFile('page')) {
            $page = $request->file('page');
            $stylesheets = $request->file('stylesheets');
            if (is_array($stylesheets)) {
                $sheets = collect($stylesheets)->map(function ($k) {
                    return $k->path();
                });
                return $this->convertFile($page->path(), $format, $sheets);
            }
            return $this->convertFile($page->path(), $format, []);
        } else {
            abort(404, "Bad request");
        }
    }
    protected function convertFile($file, $format = 'pdf', $stylesheets = [], $directDownload = true, $page = 1)
    {
        $prince = new Prince('prince');
        $output = [];
        $id = uniqid('');
        $dir = public_path('resources/' . $id);
        @mkdir($dir, 0777, true);
        $out = "$dir/rendered.out";
        foreach ($stylesheets as $styleSheet) {
            $prince->addStyleSheet($styleSheet);
        }
        if ($format == 'pdf') {
            $prince->convert_file_to_file($file, "$out.orig");
            $prince->setPDFAuthor(config('app.name'));
            $prince->setPDFCreator(config('app.name'));

            if (!env('HAS_NO_LIB_CAM_PERL')) {
                exec("rewritepdf -C '$out.orig' '$out' 2>&1 >/dev/null", $output, $status);
            } else {
                $out = "$out.orig";
            }
            if (!file_exists($out) || filesize($out) < 1) {
                abort(500, 'Apologies, unknown error!');
            }
        } else if ('png') {
            $msgs = [];
            $data = [];
            $out = "$dir";
            $prince->convert_file_to_image($file, "$out", $msgs, $data, $format);
            $out = "$out/image1." . $format;
        }
        return response()->file($out)->deleteFileAfterSend(true);
    }
}
