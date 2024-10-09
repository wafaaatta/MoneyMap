<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TransactionsTest extends TestCase
{
    use RefreshDatabase;
    public function test_get_transactions_cannot_be_accessed_by_an_unauthenticated_user()
    {
        $response = $this->get('/api/transactions');
        $response -> assertRedirect('login');
    }

    public function test_authenticated_user_can_get_his_transactions()
    {
        $user = User::create([
            'username' => 'John Doe',
            'email' => 's7bI0@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this -> actingAs($user) -> get('/api/transactions');
        dump($response);

        $response -> assertOk();

        $response -> assertJsonPath('0.user_id', $user -> id);
    }

    public function test_admin_can_get_all_transactions()
    {
        $user = User::create([
            'username' => 'John Doe',
            'email' => 's7bI0@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $this->actingAs($user)->get('/api/transactions')->assertOk();
    }

    public function test_delete_transaction()
    {
        $user = User::create([
            'username' => 'John Doe',
            'email' => 's7bI0@example.com',
            'password' => bcrypt('password'),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'amount' => 5,
            'type' => 'income',
            'description' => 'test',
            'transaction_date' => '2022-01-01',
        ]);

        $this->actingAs($user)->delete('/api/transactions/1')->assertOk();

        $this->assertNull(Transaction::find(1));
    }

    public function test_update_transaction()
    {
        $user = User::create([
            'username' => 'John Doe',
            'email' => 's7bI0@example.com',
            'password' => bcrypt('password'),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'amount' => 5,
            'type' => 'income',
            'description' => 'test',
            'transaction_date' => '2022-01-01',
        ]);

        $response = $this->actingAs($user)->put('/api/transactions/1', [
            'amount' => 10,
            'type' => 'expense',
            'description' => 'test updated',
            'transaction_date' => '2022-01-02',
        ]);

        $response->assertOk();

        $response->assertJsonPath('amount', 10);
        $response->assertJsonPath('type', 'expense');
        $response->assertJsonPath('description', 'test updated');
        $response->assertJsonPath('transaction_date', '2022-01-02');
    }

    public function test_non_admin_cannot_delete_transactions()
    {
        $user = User::create([
            'username' => 'John Doe',
            'email' => 's7bI0@example.com',
            'password' => bcrypt('password'),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'amount' => 5,
            'type' => 'income',
            'description' => 'test',
            'transaction_date' => '2022-01-01',
        ]);

        $this->actingAs($user)->delete('/api/transactions/1')->assertForbidden();
    }

    public function test_user_cannot_delete_other_user_transactions()
    {
        $user = User::create([
            'username' => 'John Doe',
            'email' => 's7bI0@example.com',
            'password' => bcrypt('password'),
        ]);

        $otherUser = User::create([
            'username' => 'Another User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->delete('/api/transactions/1')->assertRedirect('login');

        Transaction::create([
            'user_id' => $user->id,
            'amount' => 5,
            'type' => 'income',
            'description' => 'test',
            'transaction_date' => '2022-01-01',
        ]);

        $this -> post('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);


        $this->actingAs($otherUser)->delete('/api/transactions/1')->assertForbidden();
    }
}
