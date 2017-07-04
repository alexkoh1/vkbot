<?php

namespace AppBundle\Service;

use CURLFile;
use getjump\Vk\Core;
use Symfony\Component\HttpFoundation\Request;

class PhotoService
{
    private $vk;
    public function __construct()
    {
        $this->vk = Core::getInstance()->apiVersion('5.5')->setToken('7dca88bbe4a250e8be05a85a27afda0a882c3efeb21860c83e689824ed6c781038e0ad6f0c07cd2addc61');
    }
    private function getUploadUrl() {
        return $this->vk->request('photos.getWallUploadServer')->fetchData()->getResponse()->upload_url;
    }

    public function uploadPhoto($filePath) {
        $uploadUrl = $this->getUploadUrl();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uploadUrl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['photo' => new CURLFile($filePath)]);
        $data = json_decode(curl_exec($ch));
        curl_close($ch);
        $params = ['user_id' => '413686536', 'photo' => $data->photo, 'server' => $data->server, 'hash' => $data->hash];
        $response = $this->vk->request('photos.saveWallPhoto', $params)->fetchData();
        $photoId = 'photo'.$response->items[0]->owner_id.'_'.$response->items[0]->id;

        return $photoId;
        //print_r($this->vk->request('wall.post', ['owner_id' => '413686536', 'message' => 'test text', 'attachments' => $attachments])->fetchData());
    }
}