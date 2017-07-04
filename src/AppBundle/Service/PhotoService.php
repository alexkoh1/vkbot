<?php

namespace AppBundle\Service;

use AppBundle\Entity\Album;
use CURLFile;
use Doctrine\ORM\EntityManager;
use getjump\Vk\Core;
use Symfony\Component\HttpFoundation\Request;

class PhotoService
{
    private $vk;
    private $entityManager;
    public function __construct(Core $vk, EntityManager $entityManager)
    {
        $this->vk = $vk;
        $this->entityManager = $entityManager;
    }
    private function getUploadUrl(Core $vk) {
        return $vk->request('photos.getWallUploadServer')->fetchData()->getResponse()->upload_url;
    }

    public function uploadPhoto(string $filePath, Core $vk, string $ownerId) {
        $uploadUrl = $this->getUploadUrl($vk);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uploadUrl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['photo' => new CURLFile($filePath)]);
        $data = json_decode(curl_exec($ch));
        curl_close($ch);
        $params = [
            'user_id' => $ownerId,
            'photo'   => $data->photo,
            'server'  => $data->server,
            'hash'    => $data->hash
        ];

        $response = $vk->request('photos.saveWallPhoto', $params)->fetchData();
        $photoId = 'photo'.$response->items[0]->owner_id.'_'.$response->items[0]->id;

        return $photoId;
        //print_r($this->vk->request('wall.post', ['owner_id' => '413686536', 'message' => 'test text', 'attachments' => $attachments])->fetchData());
    }

    /**
     * Скачивает фото в локальную ФС по ID
     *
     * @param $id
     *
     * @return string
     */
    public function downloadPhoto(string $id, Core $vk) {
        $params = [
            'photos' => $id,
            'photo_sizes' => 1,
        ];

        $res = $vk->request('photos.getById', $params)->getResponse();
        $photoUrl = end($res[0]->sizes)->src;
        $tempFilePath = '/tmp/'.$id.'.jpg';
        file_put_contents($tempFilePath, fopen($photoUrl, 'r'));
        return $tempFilePath;
    }

    public function getAlbums(int $ownerId, Core $vk) {
        $params = [
            'owner_id' => $ownerId,
        ];
        $album = $vk->request('photos.getAlbums', $params)->getResponse();

        print 'hui';
    }
}