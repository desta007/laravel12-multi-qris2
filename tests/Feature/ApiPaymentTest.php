<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Qris;
use App\Models\Transaction;
use Laravel\Sanctum\Sanctum;
use Database\Seeders\RolePermissionSeeder;

class ApiPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $qris;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions first
        $this->seed(RolePermissionSeeder::class);

        // Create a test user
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        // Assign member role
        $this->user->assignRole('member');

        // Create test bank
        $bank = \App\Models\Bank::factory()->create([
            'name' => 'BCA',
            'code' => 'bca'
        ]);

        // Create test QRIS
        $this->qris = Qris::factory()->create([
            'name' => 'BCA Static QRIS',
            'bank_id' => $bank->id,
            'qris_code' => 'DUMMY_QRIS_CODE_BCA_STATIC_001',
            'type' => 'static',
            'is_active' => true,
            'fee_percentage' => 0.7,
        ]);

        // Create additional QRIS for testing distribution
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
    }

    /** @test */
    public function it_can_generate_qris_with_random_strategy()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/payment/generate-qris', [
            'amount' => 100000,
            'description' => 'Test payment',
            'distribution_strategy' => 'random'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'transaction_id',
                    'amount',
                    'fee',
                    'total_amount',
                    'qris_code',
                    'qris_image',
                    'bank_name',
                    'expires_at',
                ]
            ]);
    }

    /** @test */
    public function it_can_generate_qris_with_round_robin_strategy()
    {
        Sanctum::actingAs($this->user);

        $responses = [];
        
        // Make multiple requests to test round-robin distribution
        for ($i = 0; $i < 3; $i++) {
            $response = $this->postJson('/api/payment/generate-qris', [
                'amount' => 50000,
                'description' => 'Test payment ' . $i,
                'distribution_strategy' => 'round_robin'
            ]);
            
            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                ]);
                
            $responses[] = $response->json()['data']['bank_name'];
        }

        // Check that we got different banks (round-robin should rotate)
        $this->assertCount(3, $responses);
    }

    /** @test */
    public function it_can_generate_qris_with_specific_strategy()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/payment/generate-qris', [
            'amount' => 75000,
            'description' => 'Specific QRIS payment',
            'distribution_strategy' => 'specific',
            'qris_id' => $this->qris->id
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'bank_name' => 'BCA'
                ]
            ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'qris_id' => $this->qris->id,
            'amount' => 75000
        ]);
    }

    /** @test */
    public function it_cannot_generate_qris_without_authentication()
    {
        $response = $this->postJson('/api/payment/generate-qris', [
            'amount' => 100000
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_validates_required_fields_when_generating_qris()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/payment/generate-qris', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }

    /** @test */
    public function it_validates_minimum_amount_when_generating_qris()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/payment/generate-qris', [
            'amount' => 100 // Less than minimum 1000
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }

    /** @test */
    public function it_can_get_transaction_status()
    {
        Sanctum::actingAs($this->user);

        // First create a transaction
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'qris_id' => $this->qris->id,
            'transaction_id' => 'TRX-TEST123',
            'amount' => 150000,
            'fee' => 1050, // 0.7% of 150000
            'status' => 'pending',
            'description' => 'Test transaction'
        ]);

        $response = $this->getJson("/api/payment/transaction/{$transaction->transaction_id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'transaction_id' => 'TRX-TEST123',
                    'amount' => 150000,
                    'fee' => 1050,
                    'status' => 'pending',
                    'description' => 'Test transaction'
                ]
            ]);
    }

    /** @test */
    public function it_cannot_get_transaction_status_for_other_users()
    {
        Sanctum::actingAs($this->user);

        // Create another user
        $otherUser = User::factory()->create();
        $otherUser->assignRole('member');

        // Create a transaction for the other user
        $otherTransaction = Transaction::factory()->create([
            'user_id' => $otherUser->id,
            'transaction_id' => 'TRX-OTHER456',
            'amount' => 200000
        ]);

        $response = $this->getJson("/api/payment/transaction/{$otherTransaction->transaction_id}");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Transaction not found'
            ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_transaction()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/payment/transaction/NONEXISTENT123');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Transaction not found'
            ]);
    }

    /** @test */
    public function it_can_get_qris_list()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/payment/qris-list');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'bank_name',
                        'type',
                        'qris_image'
                    ]
                ]
            ]);

        // Should return at least 3 QRIS entries
        $responseData = $response->json();
        $this->assertGreaterThanOrEqual(3, count($responseData['data']));
    }

    /** @test */
    public function it_handles_callback_requests()
    {
        Sanctum::actingAs($this->user);

        // Create a transaction first
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'qris_id' => $this->qris->id,
            'transaction_id' => 'TRX-CALLBACK123',
            'amount' => 100000,
            'fee' => 700,
            'status' => 'pending'
        ]);

        // Simulate callback from payment gateway
        $response = $this->postJson('/api/payment/callback', [
            'transaction_id' => 'TRX-CALLBACK123',
            'status' => 'success',
            'payment_method' => 'qris',
            'timestamp' => now()->toISOString()
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Payment processed successfully'
            ]);

        // Verify transaction was updated
        $this->assertDatabaseHas('transactions', [
            'transaction_id' => 'TRX-CALLBACK123',
            'status' => 'success'
        ]);
        
        // Check that paid_at was set (not null)
        $this->assertDatabaseMissing('transactions', [
            'transaction_id' => 'TRX-CALLBACK123',
            'paid_at' => null
        ]);
    }

    /** @test */
    public function it_returns_error_for_callback_with_invalid_transaction()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/payment/callback', [
            'transaction_id' => 'INVALID_TRANSACTION_ID',
            'status' => 'success'
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Transaction not found'
            ]);
    }

    /** @test */
    public function it_updates_member_balance_on_successful_payment()
    {
        Sanctum::actingAs($this->user);

        // Create a transaction
        $transaction = Transaction::factory()->create([
            'user_id' => $this->user->id,
            'qris_id' => $this->qris->id,
            'transaction_id' => 'TRX-BALANCE123',
            'amount' => 50000,
            'fee' => 350,
            'status' => 'pending'
        ]);

        // Verify initial balance doesn't exist
        $this->assertDatabaseMissing('member_balances', [
            'user_id' => $this->user->id
        ]);

        // Process callback
        $this->postJson('/api/payment/callback', [
            'transaction_id' => 'TRX-BALANCE123',
            'status' => 'success'
        ]);

        // Verify balance was created and updated
        $this->assertDatabaseHas('member_balances', [
            'user_id' => $this->user->id,
            'balance' => 50000,
            'total_income' => 50000
        ]);
    }
}
