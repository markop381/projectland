<?php

namespace App\Http;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Helper
{
    public static function jsonError($message){
        $response = array();
        $response["message"] = $message;

        return json_encode($response);
    }

    public static function prettyResponse($message){

        return json_decode(json_encode($message), true);
    }

    /**
     * @param $url
     * @param $path - path to be save, based on disk
     * @param string $disk
     * @return bool
     */

    public static function uploadFileFromUrl($url, $path, $disk = 'public', $options = null)
    {
        $info = pathinfo($url);

        $contents = file_get_contents($url);

        $filePath = '/tmp/' . $info['basename'];
        file_put_contents($filePath, $contents);
        $uploadedFile = new UploadedFile($filePath, $info['basename']);

        return Storage::disk($disk)->put($path, $uploadedFile, $options);
    }

}
