<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

use Hashids\Hashids;
use App\Meedu\ServiceV2\Dao\OtherDaoInterface;

class AnnouncementService implements AnnouncementServiceInterface
{

    private $dao;

    public function __construct(OtherDaoInterface $dao)
    {
        $this->dao = $dao;
    }

    public function paginate(int $page, int $size): array
    {
        $data = $this->dao->announcementPaginate($page, $size);
        if ($data['data']) {
            foreach ($data['data'] as $key => $tmpItem) {
                $data['data'][$key]['id'] = $this->idEncode($tmpItem['id']);
            }
        }
        return $data;
    }

    public function find(int $id): array
    {
        $data = $this->dao->findAnnouncement($id);
        if ($data) {
            $data['id'] = $this->idEncode($data['id']);
        }
        return $data;
    }

    public function idEncode(int $id): string
    {
        $hashId = new Hashids(__CLASS__, 5);
        return $hashId->encode($id);
    }

    public function idDecode(string $str): int
    {
        $hashId = new Hashids(__CLASS__, 5);
        return (int)($hashId->decode($str)[0] ?? 0);
    }


}
