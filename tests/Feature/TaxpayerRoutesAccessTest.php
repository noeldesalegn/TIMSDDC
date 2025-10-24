<?php

use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

function makeUserWithRole(string $role): User
{
    $u = User::factory()->create();
    $u->role = $role; // bypass mass assignment by setting directly
    $u->save();
    return $u;
}

it('allows taxpayer to access taxpayer routes', function () {
    $user = makeUserWithRole('taxpayer');
    actingAs($user);

    get('/taxpayer/summary')->assertOk();
    get('/taxpayer/payment')->assertOk();
    get('/taxpayer/complaints')->assertOk();
    get('/taxpayer/news')->assertOk();

    post('/taxpayer/payment', [
        'tin' => 'TIN123456',
        'bank_name' => 'Dire Dawa Bank',
        'account_number' => '1234567890',
        'amount' => 100.00,
    ])->assertRedirect(route('taxpayer.payment'));

    post('/taxpayer/complaints', [
        'subject' => 'Test Subject',
        'message' => 'Test complaint message.'
    ])->assertRedirect(route('taxpayer.complaints'));

    post('/taxpayer/news/1/comments', [
        'comment' => 'Nice update!'
    ])->assertRedirect(route('taxpayer.news'));
});

it('denies admin users from accessing taxpayer routes', function () {
    $user = makeUserWithRole('admin');
    actingAs($user);

    get('/taxpayer/summary')->assertStatus(403);
    get('/taxpayer/payment')->assertStatus(403);
    get('/taxpayer/complaints')->assertStatus(403);
    get('/taxpayer/news')->assertStatus(403);

    post('/taxpayer/payment', [])->assertStatus(403);
    post('/taxpayer/complaints', [])->assertStatus(403);
    post('/taxpayer/news/1/comments', [])->assertStatus(403);
});

it('denies interviewer users from accessing taxpayer routes', function () {
    $user = makeUserWithRole('interviewer');
    actingAs($user);

    get('/taxpayer/summary')->assertStatus(403);
    get('/taxpayer/payment')->assertStatus(403);
    get('/taxpayer/complaints')->assertStatus(403);
    get('/taxpayer/news')->assertStatus(403);

    post('/taxpayer/payment', [])->assertStatus(403);
    post('/taxpayer/complaints', [])->assertStatus(403);
    post('/taxpayer/news/1/comments', [])->assertStatus(403);
});
