<?php

namespace Tests\Feature;

use Tapsilat\Laravel\Tests\TestCase;
use Tapsilat\Laravel\Facades\Tapsilat;
use Tapsilat\Models\OrderCreateDTO;
use Tapsilat\Models\BuyerDTO;
use Tapsilat\Models\BillingAddressDTO;
use Tapsilat\Models\BasketItemDTO;

class OrderIntegrationTest extends TestCase
{
    public function test_create_order_integration()
    {
        if (!env('TAPSILAT_API_KEY')) {
            $this->markTestSkipped('TAPSILAT_API_KEY not set');
        }

        // Create DTOs first
        $buyer = new BuyerDTO();
        $buyer->id = '123';
        $buyer->name = 'John';
        $buyer->surname = 'Doe';
        $buyer->email = 'john@doe.com';
        $buyer->gsm_number = '5321234567';
        $buyer->identity_number = '11111111111';
        $buyer->city = 'Istanbul';
        $buyer->country = 'Turkey';
        $buyer->ip = '127.0.0.1';

        $billing = new BillingAddressDTO();
        $billing->contact_name = 'John Doe';
        $billing->city = 'Istanbul';
        $billing->country = 'Turkey';
        $billing->address = 'Test Address';
        $billing->zip_code = '34000';

        $item1 = new BasketItemDTO();
        $item1->id = '1';
        $item1->name = 'Item 1';
        $item1->price = 5.0;
        $item1->item_type = 'PHYSICAL'; // Ensure correct case
        $item1->category1 = 'Test';

        $item2 = new BasketItemDTO();
        $item2->id = '2';
        $item2->name = 'Item 2';
        $item2->price = 5.0;
        $item2->item_type = 'PHYSICAL';
        $item2->category1 = 'Test';

        // Initialize Request with Constructor
        $request = new OrderCreateDTO(
            10.0,
            'TRY',
            'tr',
            $buyer,
            [$item1, $item2]
        );
        $request->billing_address = $billing;

        // Ensure token is set in config (test environment)
        config(['tapsilat.api_key' => env('TAPSILAT_API_KEY')]);

        // Execute via Facade
        $response = Tapsilat::createOrder($request);

        $this->assertNotNull($response->referenceId);
        $this->assertNotEmpty($response->checkoutUrl);
    }
}
