<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class ClearSeededData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clear-seeded-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all seeded data from the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Delete all seeded objects from the database
        User::where('email', 'admin@example.com')->delete();
        User::where('email', 'supplier@example.com')->delete();
        User::where('email', 'user@example.com')->delete();
        Product::truncate();
        Order::truncate();
        OrderItem::truncate();

        $this->info('All seeded data has been deleted successfully.');

        return 0;
    }
}
