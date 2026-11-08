<?php

use App\Models\Field;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(fn () => $this->actingAsAdmin());

test('fields index page lists fields and marks sidebar active', function () {
    config()->set('app.is_stisla_demo', false);

    $field = Field::factory()->create([
        'code' => 'FLD-001',
        'name' => 'Bidang Sains',
    ]);

    $this->get(route('fields.index'))
        ->assertOk()
        ->assertViewIs('modules.fields.index')
        ->assertSee('id="table-fields"', false)
        ->assertSee('FLD-001')
        ->assertSee($field->name)
        ->assertSee('<li class="active"><a class="nav-link" href="'.route('fields.index').'">', false);
});

test('can store a new field', function () {
    $payload = [
        'code' => 'FLD-101',
        'name' => 'Bidang Teknik',
        'description' => 'Bidang teknik dan rekayasa',
    ];

    $this->post(route('fields.store'), $payload)
        ->assertRedirect(route('fields.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('fields', $payload);
});

test('storing a field validates required and unique code', function (array $overrides, string $errorField) {
    Field::factory()->create(['code' => 'FLD-EXISTING']);

    $payload = array_merge([
        'code' => 'FLD-202',
        'name' => 'Bidang Seni',
        'description' => null,
    ], $overrides);

    $this->from(route('fields.create'))
        ->post(route('fields.store'), $payload)
        ->assertRedirect(route('fields.create'))
        ->assertSessionHasErrors($errorField);

    $this->assertDatabaseMissing('fields', ['name' => 'Bidang Seni']);
})->with([
    'missing code' => [['code' => ''], 'code'],
    'duplicate code' => [['code' => 'FLD-EXISTING'], 'code'],
    'missing name' => [['name' => ''], 'name'],
]);

test('updating a field keeps its own code valid and changes the name', function () {
    $field = Field::factory()->create(['code' => 'FLD-303', 'name' => 'Bidang Lama']);

    $this->put(route('fields.update', $field), [
        'code' => 'FLD-303',
        'name' => 'Bidang Baru',
        'description' => null,
    ])
        ->assertRedirect(route('fields.index'))
        ->assertSessionHas('success');

    expect($field->refresh()->name)->toBe('Bidang Baru');
});

test('can delete a field', function () {
    $field = Field::factory()->create();

    $this->delete(route('fields.destroy', $field))
        ->assertRedirect(route('fields.index'));

    $this->assertModelMissing($field);
});

test('user without fields permissions receives 403 forbidden', function () {
    $this->actingAsUser();

    $this->get(route('fields.index'))->assertForbidden();
    $this->get(route('fields.create'))->assertForbidden();
});
