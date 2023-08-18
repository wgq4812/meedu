<?php

namespace Tests\ServicesV2;

use PHPUnit\Framework\TestCase;
use App\Meedu\ServiceV2\Services\AnnouncementService;
use App\Meedu\ServiceV2\Dao\OtherDaoInterface;

class AnnouncementServiceTest extends TestCase
{
    protected $announcementService;
    protected $otherDaoMock;

    protected function setUp(): void
    {
        $this->otherDaoMock = $this->createMock(OtherDaoInterface::class);
        $this->announcementService = new AnnouncementService($this->otherDaoMock);
    }

    public function testPaginate()
    {
        $this->otherDaoMock->expects($this->once())
            ->method('announcementPaginate')
            ->with(1, 10)
            ->willReturn([
                'data' => [
                    ['id' => 1, 'title' => 'Announcement 1'],
                    ['id' => 2, 'title' => 'Announcement 2'],
                ],
                'total' => 2,
            ]);

        $page = 1;
        $size = 10;
        $result = $this->announcementService->paginate($page, $size);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('total', $result);
    }

    public function testFind()
    {
        $this->otherDaoMock->expects($this->exactly(2))
            ->method('findAnnouncement')
            ->willReturnCallback(function ($id) {
                if ($id === 1) {
                    return [
                        'id' => 1,
                        'title' => '测试标题',
                    ];
                }

                return [];
            });

        $id = 1;
        $result = $this->announcementService->find($id);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);

        $result = $this->announcementService->find(2);
        $this->assertEmpty($result);
    }
}