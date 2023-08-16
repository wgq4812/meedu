<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Console\Commands;

use App\Bus\RefundBus;
use Illuminate\Console\Command;
use App\Meedu\ServiceV2\Services\OrderServiceInterface;
use Symfony\Component\Console\Command\Command as CommandAlias;

class RefundQueryCommand extends Command
{
    protected $signature = 'meedu:refund:query';

    protected $description = '退款订单状态查询命令';

    private $orderService;
    private $refundBus;

    public function __construct(OrderServiceInterface $orderService, RefundBus $refundBus)
    {
        parent::__construct();
        $this->orderService = $orderService;
        $this->refundBus = $refundBus;
    }

    public function handle(): int
    {
        $refundOrders = $this->orderService->takeProcessingRefundOrders(10);
        if (!$refundOrders) {
            $this->line('暂无退款订单需要处理');
            return CommandAlias::SUCCESS;
        }

        foreach ($refundOrders as $refundOrder) {
            $this->line(sprintf('查询退款订单[%s][%s]', $refundOrder['payment'], $refundOrder['refund_no']));
            $this->refundBus->queryHandler($refundOrder);
        }

        return CommandAlias::SUCCESS;
    }
}
