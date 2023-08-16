<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Meedu\ServiceV2\Services\OrderServiceInterface;
use Symfony\Component\Console\Command\Command as CommandAlias;

class OrderTimeoutHandlerCommand extends Command
{
    protected $signature = 'order:pay:timeout';

    protected $description = '订单超时处理';

    private $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    public function handle(): int
    {
        $orders = $this->orderService->getTimeoutOrders(Carbon::now()->subMinutes(60)->toDateTimeString());
        if (!$orders) {
            $this->line('无超时订单需要处理');
            return CommandAlias::SUCCESS;
        }
        foreach ($orders as $order) {
            $this->line(sprintf('订单[%s]已超时', $order['order_id']));
            $this->orderService->cancelOrder($order);
        }
        return CommandAlias::SUCCESS;
    }
}
