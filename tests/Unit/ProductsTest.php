<?php

namespace Tests\Unit;


use App\Models\MoonshineUser;
use App\MoonShine\Pages\Dashboard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function test_user_sees_only_his_products(): void
    {
        //
    }
}
