<?php

use App\Models\Marca;
use App\Models\QrScan;
use App\Models\ScanGroup;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->exhibidor = User::factory()->create();
    $this->exhibidor->assignRole('Exhibidor');

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrador');
});

test('guests are redirected to login from the scanner', function () {
    $this->get(route('admin.scan'))->assertRedirect(route('login'));
});

test('users without the scan permission cannot access the scanner', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('admin.scan'))->assertForbidden();
});

test('users with the scan permission can view the scanner page', function () {
    $this->actingAs($this->exhibidor)
        ->get(route('admin.scan'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('admin/scanner/Scan')->has('marcas'));
});

test('a single scan is stored with its marcas', function () {
    $marca = Marca::create(['nombre' => 'Marca Uno']);

    $response = $this->actingAs($this->exhibidor)->postJson(route('admin.scan.store'), [
        'nombre' => 'Juan',
        'apellidos' => 'Pérez',
        'empresa' => 'ACME',
        'correo' => 'juan@acme.com',
        'campos_adicionales' => ['extra 1'],
        'marcas' => [['id' => $marca->id, 'comentarios' => 'Interesado en demo']],
    ]);

    $response->assertCreated();

    $scan = QrScan::firstWhere('correo', 'juan@acme.com');
    expect($scan)->not->toBeNull()
        ->and($scan->user_id)->toBe($this->exhibidor->id)
        ->and($scan->campos_adicionales)->toBe(['extra 1'])
        ->and($scan->marcas)->toHaveCount(1)
        ->and($scan->marcas->first()->pivot->comentarios)->toBe('Interesado en demo');
});

test('a duplicate email returns 409 with the existing scan id', function () {
    $existing = QrScan::create([
        'user_id' => $this->admin->id,
        'nombre' => 'Juan',
        'correo' => 'juan@acme.com',
    ]);

    $this->actingAs($this->exhibidor)
        ->postJson(route('admin.scan.store'), ['nombre' => 'Juan', 'correo' => 'juan@acme.com'])
        ->assertStatus(409)
        ->assertJson(['existingScanId' => $existing->id]);
});

test('a group of scans is stored together in one operation', function () {
    $marca = Marca::create(['nombre' => 'Marca Uno']);

    $scans = collect(range(1, 5))->map(fn (int $i) => [
        'nombre' => "Contacto {$i}",
        'correo' => "contacto{$i}@empresa.com",
    ])->all();

    $response = $this->actingAs($this->exhibidor)->postJson(route('admin.scan.store-group'), [
        'empresa' => 'Empresa Grupal',
        'notas' => 'Visita de stand',
        'scans' => $scans,
        'marcas' => [['id' => $marca->id, 'comentarios' => 'Todos interesados']],
    ]);

    $response->assertCreated()->assertJson(['saved' => 5]);

    $group = ScanGroup::first();
    expect($group)->not->toBeNull()
        ->and($group->empresa)->toBe('Empresa Grupal')
        ->and($group->scans)->toHaveCount(5)
        ->and($group->scans->every(fn (QrScan $scan) => $scan->marcas->count() === 1))->toBeTrue();
});

test('group scans skip duplicates but save the rest', function () {
    QrScan::create([
        'user_id' => $this->admin->id,
        'nombre' => 'Ya Existe',
        'correo' => 'existente@empresa.com',
    ]);

    $response = $this->actingAs($this->exhibidor)->postJson(route('admin.scan.store-group'), [
        'empresa' => 'ACME',
        'scans' => [
            ['nombre' => 'Nuevo', 'correo' => 'nuevo@empresa.com'],
            ['nombre' => 'Ya Existe', 'correo' => 'existente@empresa.com'],
            ['nombre' => 'Repetido Interno', 'correo' => 'nuevo@empresa.com'],
        ],
    ]);

    $response->assertCreated()->assertJson(['saved' => 1]);
    expect($response->json('duplicates'))->toHaveCount(2)
        ->and(QrScan::count())->toBe(2);
});

test('a group where every contact is duplicated returns 409 and stores nothing', function () {
    QrScan::create([
        'user_id' => $this->admin->id,
        'nombre' => 'Ya Existe',
        'correo' => 'existente@empresa.com',
    ]);

    $this->actingAs($this->exhibidor)->postJson(route('admin.scan.store-group'), [
        'scans' => [['nombre' => 'Ya Existe', 'correo' => 'existente@empresa.com']],
    ])->assertStatus(409);

    expect(ScanGroup::count())->toBe(0);
});

test('non-admin users only see their own captured scans', function () {
    QrScan::create(['user_id' => $this->exhibidor->id, 'nombre' => 'Mío']);
    QrScan::create(['user_id' => $this->admin->id, 'nombre' => 'De Otro']);

    $this->actingAs($this->exhibidor)
        ->get(route('admin.usuarios-capturados.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/capturados/Index')
            ->has('scans.data', 1)
            ->where('scans.data.0.nombre', 'Mío'));

    $this->actingAs($this->admin)
        ->get(route('admin.usuarios-capturados.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('scans.data', 2));
});

test('bulk delete only removes scans owned by non-admin users', function () {
    $mine = QrScan::create(['user_id' => $this->exhibidor->id, 'nombre' => 'Mío']);
    $other = QrScan::create(['user_id' => $this->admin->id, 'nombre' => 'De Otro']);

    $this->actingAs($this->exhibidor)
        ->delete(route('admin.usuarios-capturados.destroy-many'), [
            'ids' => [$mine->id, $other->id],
        ])
        ->assertRedirect();

    expect(QrScan::pluck('id')->all())->toBe([$other->id]);
});
