<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Meedu\ServiceV2\Services;

use App\Meedu\ServiceV2\Dao\OtherDaoInterface;
use App\Meedu\ServiceV2\Services\Traits\HashIdTrait;

class AnnouncementService implements AnnouncementServiceInterface
{
    use HashIdTrait;

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


}
