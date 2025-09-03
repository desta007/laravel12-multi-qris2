<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Qris;
use App\Models\Transaction;
use App\Models\MemberBalance;
use App\Http\Controllers\Api\PaymentController;
use App\Services\QrisDistributionService;
use Illuminate\Http\Request;
use Database\Seeders\RolePermissionSeeder;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $qris;
    protected $paymentController;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->seed(RolePermissionSeeder::class);

        // Create test user
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $this->user->assignRole('member');

        // Create test bank
        $bank = \App\Models\Bank::factory()->create([
            'name' => 'BCA',
            'code' => 'bca'
        ]);

        // Create test QRIS entries
        $this->qris = Qris::factory()->create([
            'name' => 'BCA Static QRIS',
            'bank_id' => $bank->id,
            'qris_code' => 'DUMMY_QRIS_CODE_BCA_STATIC_001',
            'type' => 'static',
            'is_active' => true,
            'fee_percentage' => 0.7,
        ]);

        $mandiriBank = \App\Models\Bank::factory()->create([
            'name' => 'Mandiri',
            'code' => 'mandiri'
        ]);

        Qris::factory()->create([
            'name' => 'Mandiri Dynamic QRIS',
            'bank_id' => $mandiriBank->id,
            'qris_code' => 'DUMMY_QRIS_CODE_MANDIRI_DYNAMIC_001',
            'type' => 'dynamic',
            'is_active' => true,
            'fee_percentage' => 0.8,
        ]);

        $bniBank = \App\Models\Bank::factory()->create([
            'name' => 'BNI',
            'code' => 'bni'
        ]);

        Qris::factory()->create([
            'name' => 'BNI Static QRIS',
            'bank_id' => $bniBank->id,
            'qris_code' => 'DUMMY_QRIS_CODE_BNI_STATIC_001',
            'type' => 'static',
            'is_active' => true,
            'fee_percentage' => 0.6,
        ]);

        // Initialize the payment controller with its dependencies
        $qrisDistributionService = new QrisDistributionService();
        $this->paymentController = new PaymentController($qrisDistributionService);
    }

    /** @test */
    public function it_can_generate_qris_with_random_strategy()
    {
        $this->actingAs($this->user);

        $request = Request::create('/api/payment/generate-qris', 'POST', [
            'amount' => 100000,
            'description' => 'Test payment',
            'distribution_strategy' => 'random'
        ]);

        $response = $this->paymentController->generateQris($request);
        $data = $response->getData(true);

        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('transaction_id', $data['data']);
        $this->assertArrayHasKey('amount', $data['data']);
        $this->assertArrayHasKey('fee', $data['data']);
        $this->assertArrayHasKey('total_amount', $data['data']);
        $this->assertEquals(100000, $data['data']['amount']);

        // Verify transaction was created
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'amount' => 100000,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function it_can_generate_qris_with_specific_strategy()
    {
        $this->actingAs($this->user);

        $request = Request::create('/api/payment/generate-qris', 'POST', [
            'amount' => 75000,
            'description' => 'Specific QRIS payment',
            'distribution_strategy' => 'specific',
            'qris_id' => $this->qris->id
        ]);

        $response = $this->paymentController->generateQris($request);
        $data = $response->getData(true);

        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('data', $data);
        $this->assertEquals('BCA', $data['data']['bank_name']);

        // Verify transaction was created with specific QRIS
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'qris_id' => $this->qris->id,
            'amount' => 75000
        ]);
    }

    /** @test */
    public function it_can_get_transaction_status()
    {
        $this->actingAs($this->user);

        // Create a transaction first
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'qris_id' => $this->qris->id,
            'transaction_id' => 'TRX-UNITTEST123',
            'amount' => 150000,
            'fee' => 1050,
            'status' => 'pending',
            'description' => 'Unit test transaction'
        ]);

        $response = $this->paymentController->getTransactionStatus('TRX-UNITTEST123');
        $data = $response->getData(true);

        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('data', $data);
        $this->assertEquals('TRX-UNITTEST123', $data['data']['transaction_id']);
        $this->assertEquals(150000, $data['data']['amount']);
        $this->assertEquals('pending', $data['data']['status']);
    }

    /** @test */
    public function it_returns_error_for_nonexistent_transaction()
    {
        $this->actingAs($this->user);

        $response = $this->paymentController->getTransactionStatus('NONEXISTENT123');
        $data = $response->getData(true);

        $this->assertFalse($data['success']);
        $this->assertEquals('Transaction not found', $data['message']);
    }

    /** @test */
    public function it_can_get_qris_list()
    {
        $this->actingAs($this->user);

        $response = $this->paymentController->getQrisList();
        $data = $response->getData(true);

        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('data', $data);
        $this->assertIsArray($data['data']);
        $this->assertGreaterThanOrEqual(3, count($data['data']));

        // Check that each QRIS has the required fields
        foreach ($data['data'] as $qris) {
            $this->assertArrayHasKey('id', $qris);
            $this->assertArrayHasKey('name', $qris);
            $this->assertArrayHasKey('bank_name', $qris);
            $this->assertArrayHasKey('type', $qris);
        }
    }

    /** @test */
    public function it_handles_callback_requests()
    {
        $this->actingAs($this->user);

        // Create a transaction first
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'qris_id' => $this->qris->id,
            'transaction_id' => 'TRX-CALLBACK123',
            'amount' => 100000,
            'fee' => 700,
            'status' => 'pending'
        ]);

        // Verify initial balance doesn't exist
        $this->assertDatabaseMissing('member_balances', [
            'user_id' => $this->user->id
        ]);

        $request = Request::create('/api/payment/callback', 'POST', [
            'transaction_id' => 'TRX-CALLBACK123',
            'status' => 'success',
            'payment_method' => 'qris'
        ]);

        $response = $this->paymentController->handleCallback($request);
        $data = $response->getData(true);

        $this->assertTrue($data['success']);
        $this->assertEquals('Payment processed successfully', $data['message']);

        // Verify transaction was updated (check without exact datetime)
        $this->assertDatabaseHas('transactions', [
            'transaction_id' => 'TRX-CALLBACK123',
            'status' => 'success'
        ]);
        $this->assertNotNull($transaction->refresh()->paid_at);

        // Verify member balance was updated
        $this->assertDatabaseHas('member_balances', [
            'user_id' => $this->user->id,
            'balance' => 100000,
            'total_income' => 100000
        ]);
    }

    /** @test */
    public function it_returns_error_for_callback_with_invalid_transaction()
    {
        $this->actingAs($this->user);

        $request = Request::create('/api/payment/callback', 'POST', [
            'transaction_id' => 'INVALID_TRANSACTION_ID',
            'status' => 'success'
        ]);

        $response = $this->paymentController->handleCallback($request);
        $data = $response->getData(true);

        $this->assertFalse($data['success']);
        $this->assertEquals('Transaction not found', $data['message']);
    }
}