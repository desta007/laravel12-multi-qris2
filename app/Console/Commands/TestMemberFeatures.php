<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestMemberFeatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-member-features';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test member area features implementation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing member area features implementation...');
        
        // Test 1: Check if user model has bank fields
        $this->info('Test 1: Checking if user model has bank fields...');
        $user = new User();
        $fillable = $user->getFillable();
        $hasBankFields = in_array('bank_name', $fillable) && 
                         in_array('account_holder_name', $fillable) && 
                         in_array('account_number', $fillable) &&
                         in_array('phone', $fillable);

        if ($hasBankFields) {
            $this->info('✓ User model has all required bank fields');
        } else {
            $this->error('✗ User model is missing bank fields');
        }

        // Test 2: Check if dashboard page has rolling QRIS functionality
        $this->info('Test 2: Checking if dashboard page has rolling QRIS methods...');
        $dashboard = new \App\Filament\Member\Pages\Dashboard();
        $methods = get_class_methods($dashboard);

        $hasStaticQris = in_array('getStaticQrisProperty', $methods);
        $hasDynamicQris = in_array('getDynamicQrisProperty', $methods);

        if ($hasStaticQris && $hasDynamicQris) {
            $this->info('✓ Dashboard page has rolling QRIS methods');
        } else {
            $this->error('✗ Dashboard page is missing rolling QRIS methods');
        }

        // Test 3: Check if custom profile page exists
        $this->info('Test 3: Checking if custom profile page exists...');
        if (class_exists('\App\Filament\Member\Pages\MemberProfile')) {
            $this->info('✓ Custom profile page exists');
        } else {
            $this->error('✗ Custom profile page does not exist');
        }

        // Test 4: Check if custom edit profile page exists
        $this->info('Test 4: Checking if custom edit profile page exists...');
        if (class_exists('\App\Filament\Member\Pages\EditProfile')) {
            $this->info('✓ Custom edit profile page exists');
        } else {
            $this->error('✗ Custom edit profile page does not exist');
        }

        $this->info('All tests completed.');
    }
}
