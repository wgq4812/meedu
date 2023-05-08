<?php

/*
 * This file is part of the Qsnh/meedu.
 *
 * (c) 杭州白书科技有限公司
 */

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Services\Order\Services\OrderService;
use App\Services\Order\Interfaces\OrderServiceInterface;
use Symfony\Component\Console\Command\Command as CommandAlias;

class OrderTimeoutHandlerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:pay:timeout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '订单超时处理（自动置为已取消=无法继续支付）';

    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * OrderTimeoutHandlerCommand constructor.
     *
     * @param OrderServiceInterface $orderService
     */
    public function __construct(OrderServiceInterface $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    /**
     * @throws \App\Exceptions\ServiceException
     */
    public function handle(): int
    {
        // 超时一个小时未支付订单
        $now = Carbon::now()->subMinutes(60);
        $orders = $this->orderService->getTimeoutOrders($now->toDateTimeString());
        if (!$orders) {
            return CommandAlias::SUCCESS;
        }
        foreach ($orders as $order) {
            $this->line($order['order_id']);
            $this->orderService->cancel($order['id']);
        }

        return CommandAlias::SUCCESS;
    }
}
