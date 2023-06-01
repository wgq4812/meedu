<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Console\Commands;

use App\Bus\RefundBus;
use Illuminate\Console\Command;
use App\Services\Order\Services\OrderService;
use App\Services\Order\Interfaces\OrderServiceInterface;
use Symfony\Component\Console\Command\Command as CommandAlias;

class RefundQueryCommand extends Command
{
    protected $signature = 'meedu:refund:query';

    protected $description = '退款订单状态查询命令';

    public function handle(): int
    {
        /**
         * @var OrderService $orderService
         */
        $orderService = app()->make(OrderServiceInterface::class);
        // 一次处理10个订单
        $refundOrders = $orderService->takeProcessingRefundOrders(10);
        if (!$refundOrders) {
            $this->line('暂无退款订单需要处理');
            return CommandAlias::SUCCESS;
        }

        /**
         * @var RefundBus $refundBus
         */
        $refundBus = app()->make(RefundBus::class);

        foreach ($refundOrders as $refundOrder) {
            $this->line(sprintf('查询退款订单[%s][%s]', $refundOrder['payment'], $refundOrder['refund_no']));
            $refundBus->queryHandler($refundOrder);
        }

        return CommandAlias::SUCCESS;
    }
}
