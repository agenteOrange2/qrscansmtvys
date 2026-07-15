<?php

use App\Jobs\SendScanGroupToBitrix;
use App\Jobs\SendScanToBitrix;
use App\Models\Marca;
use App\Models\QrScan;
use App\Models\ScanGroup;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrador');

    $this->exhibidor = User::factory()->create();
    $this->exhibidor->assignRole('Exhibidor');
});

function enableBitrix(): void
{
    Setting::set('bitrix.enabled', '1');
    Setting::set('bitrix.webhook', 'https://example.bitrix24.com/rest/1/token');
    Setting::set('bitrix.category_id', '13');
}

test('users without the integraciones permission cannot open the settings page', function () {
    $this->actingAs($this->exhibidor)
        ->get(route('admin.integraciones.bitrix.edit'))
        ->assertForbidden();
});

test('admins can open the bitrix settings page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.integraciones.bitrix.edit'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/integraciones/Bitrix')
            ->has('settings')
            ->has('stats'));
});

test('the settings are saved', function () {
    $this->actingAs($this->admin)
        ->put(route('admin.integraciones.bitrix.update'), [
            'enabled' => true,
            'webhook' => 'https://smtvys.bitrix24.com/rest/1/abc123/',
            'category_id' => 13,
            'stage_id' => 'C13:NEW',
            'responsible_id' => 7,
        ])
        ->assertRedirect();

    expect(Setting::getBool('bitrix.enabled'))->toBeTrue()
        ->and(Setting::get('bitrix.webhook'))->toBe('https://smtvys.bitrix24.com/rest/1/abc123/')
        ->and(Setting::get('bitrix.category_id'))->toBe('13')
        ->and(Setting::get('bitrix.stage_id'))->toBe('C13:NEW')
        ->and(Setting::get('bitrix.responsible_id'))->toBe('7');
});

test('enabling the integration without a webhook is rejected', function () {
    $this->actingAs($this->admin)
        ->put(route('admin.integraciones.bitrix.update'), [
            'enabled' => true,
            'webhook' => '',
        ])
        ->assertSessionHasErrors('webhook');
});

test('a scan sends the bitrix deal right after the response', function () {
    Bus::fake();
    enableBitrix();

    $this->actingAs($this->exhibidor)->postJson(route('admin.scan.store'), [
        'nombre' => 'Laura',
        'apellidos' => 'Gómez',
        'correo' => 'laura@acme.com',
    ])->assertCreated();

    $scan = QrScan::firstWhere('correo', 'laura@acme.com');

    expect($scan->bitrix_status)->toBe(QrScan::BITRIX_PENDING);

    Bus::assertDispatchedAfterResponse(
        SendScanToBitrix::class,
        fn (SendScanToBitrix $job) => $job->scan->is($scan) && $job->force === false,
    );
});

test('a scan does not dispatch the bitrix job when the integration is disabled', function () {
    Bus::fake();

    $this->actingAs($this->exhibidor)->postJson(route('admin.scan.store'), [
        'nombre' => 'Pedro',
        'correo' => 'pedro@acme.com',
    ])->assertCreated();

    expect(QrScan::firstWhere('correo', 'pedro@acme.com')->bitrix_status)->toBeNull();

    Bus::assertNotDispatched(SendScanToBitrix::class);
});

test('a group scan dispatches a single group job instead of one per contact', function () {
    Bus::fake();
    enableBitrix();

    $this->actingAs($this->exhibidor)->postJson(route('admin.scan.store-group'), [
        'empresa' => 'ACME',
        'scans' => [
            ['nombre' => 'Ana', 'correo' => 'ana@acme.com'],
            ['nombre' => 'Luis', 'correo' => 'luis@acme.com'],
        ],
    ])->assertCreated();

    $group = ScanGroup::latest('id')->first();

    expect($group->bitrix_status)->toBe(QrScan::BITRIX_PENDING)
        ->and($group->scans()->pluck('bitrix_status')->unique()->all())->toBe([QrScan::BITRIX_PENDING]);

    Bus::assertDispatchedAfterResponse(
        SendScanGroupToBitrix::class,
        fn (SendScanGroupToBitrix $job) => $job->group->is($group),
    );
    Bus::assertNotDispatched(SendScanToBitrix::class);
});

test('a group with an already registered contact includes it in the single deal', function () {
    Bus::fake();
    enableBitrix();

    $existing = QrScan::create([
        'user_id' => $this->exhibidor->id,
        'nombre' => 'Laura',
        'correo' => 'laura@acme.com',
    ]);

    $this->actingAs($this->exhibidor)->postJson(route('admin.scan.store-group'), [
        'empresa' => 'ACME',
        'scans' => [
            ['nombre' => 'Ana', 'correo' => 'ana@acme.com'],
            ['nombre' => 'Laura', 'correo' => 'laura@acme.com'],
        ],
    ])->assertCreated();

    Bus::assertDispatchedAfterResponse(
        SendScanGroupToBitrix::class,
        fn (SendScanGroupToBitrix $job) => $job->extraScanIds === [$existing->id],
    );
});

test('the group job creates a single deal containing every contact', function () {
    enableBitrix();

    Http::fake([
        '*crm.duplicate.findbycomm.json' => Http::response(['result' => []]),
        '*crm.contact.add.json' => Http::sequence()
            ->push(['result' => 55])
            ->push(['result' => 56]),
        '*crm.deal.add.json' => Http::response(['result' => 99]),
    ]);

    $marca = Marca::create(['nombre' => 'VJ Electronix']);

    $group = ScanGroup::create(['user_id' => $this->exhibidor->id, 'empresa' => 'ACME', 'notas' => 'Visita en expo']);

    $ana = QrScan::create([
        'user_id' => $this->exhibidor->id,
        'scan_group_id' => $group->id,
        'nombre' => 'Ana',
        'apellidos' => 'Ruiz',
        'puesto' => 'Compras',
        'correo' => 'ana@acme.com',
    ]);
    $luis = QrScan::create([
        'user_id' => $this->exhibidor->id,
        'scan_group_id' => $group->id,
        'nombre' => 'Luis',
        'correo' => 'luis@acme.com',
    ]);

    $ana->marcas()->attach($marca->id, ['comentarios' => 'Quiere demo']);
    $luis->marcas()->attach($marca->id, ['comentarios' => 'Quiere demo']);

    (new SendScanGroupToBitrix($group))->handle();

    $group->refresh();

    expect($group->bitrix_deal_id)->toBe(99)
        ->and($group->bitrix_status)->toBe(QrScan::BITRIX_SENT)
        ->and($ana->refresh()->bitrix_deal_id)->toBe(99)
        ->and($ana->bitrix_contact_id)->toBe(55)
        ->and($luis->refresh()->bitrix_deal_id)->toBe(99)
        ->and($luis->bitrix_contact_id)->toBe(56);

    $dealRequests = Http::recorded(fn ($request) => str_contains($request->url(), 'crm.deal.add.json'));

    expect($dealRequests)->toHaveCount(1);

    Http::assertSent(function ($request) {
        if (! str_contains($request->url(), 'crm.deal.add.json')) {
            return false;
        }

        $fields = $request['fields'];

        return $fields['CONTACT_IDS'] === [55, 56]
            && $fields['CATEGORY_ID'] === 13
            && str_contains($fields['TITLE'], 'Grupal')
            && str_contains($fields['COMMENTS'], 'Ana Ruiz')
            && str_contains($fields['COMMENTS'], 'Luis')
            && str_contains($fields['COMMENTS'], 'VJ Electronix — Quiere demo')
            && str_contains($fields['COMMENTS'], 'Notas del grupo: Visita en expo');
    });
});

test('a duplicate scan no longer sends a deal automatically', function () {
    Bus::fake();
    enableBitrix();

    $existing = QrScan::create([
        'user_id' => $this->exhibidor->id,
        'nombre' => 'Laura',
        'correo' => 'laura@acme.com',
    ]);

    $this->actingAs($this->exhibidor)->postJson(route('admin.scan.store'), [
        'nombre' => 'Laura',
        'correo' => 'laura@acme.com',
    ])->assertStatus(409)->assertJson([
        'existingScanId' => $existing->id,
        'bitrixEnabled' => true,
    ]);

    Bus::assertNotDispatched(SendScanToBitrix::class);
});

test('confirming a rescan updates the record and sends a new deal', function () {
    Bus::fake();
    enableBitrix();

    $marca = Marca::create(['nombre' => 'Sam']);

    $existing = QrScan::create([
        'user_id' => $this->exhibidor->id,
        'nombre' => 'Laura',
        'apellidos' => 'Gómez',
        'puesto' => 'Gerente',
        'correo' => 'laura@acme.com',
        'campos_adicionales' => ['línea previa'],
    ]);
    $existing->forceFill(['bitrix_deal_id' => 50, 'bitrix_status' => QrScan::BITRIX_SENT])->save();

    $this->actingAs($this->exhibidor)->postJson(route('admin.scan.rescan', $existing), [
        'nombre' => 'Laura',
        'apellidos' => '',
        'puesto' => 'Directora',
        'telefono' => '5559998877',
        'correo' => 'laura@acme.com',
        'campos_adicionales' => ['línea nueva'],
        'marcas' => [['id' => $marca->id, 'comentarios' => 'Interesada en demo']],
    ])->assertOk();

    $existing->refresh();

    expect($existing->apellidos)->toBe('Gómez')
        ->and($existing->puesto)->toBe('Directora')
        ->and($existing->telefono)->toBe('5559998877')
        ->and($existing->campos_adicionales)->toBe(['línea previa', 'línea nueva'])
        ->and($existing->rescan_count)->toBe(1)
        ->and($existing->last_scanned_at)->not->toBeNull()
        ->and($existing->marcas)->toHaveCount(1)
        ->and($existing->marcas->first()->pivot->comentarios)->toBe('Interesada en demo')
        ->and($existing->bitrix_status)->toBe(QrScan::BITRIX_PENDING);

    Bus::assertDispatchedAfterResponse(
        SendScanToBitrix::class,
        fn (SendScanToBitrix $job) => $job->force === true && $job->scan->is($existing),
    );
});

test('the resend button dispatches a forced deal without touching the record', function () {
    Bus::fake();
    enableBitrix();

    $scan = QrScan::create([
        'user_id' => $this->exhibidor->id,
        'nombre' => 'Mario',
        'puesto' => 'Compras',
        'correo' => 'mario@acme.com',
    ]);
    $scan->forceFill(['bitrix_deal_id' => 50, 'bitrix_status' => QrScan::BITRIX_SENT])->save();

    $this->actingAs($this->admin)
        ->postJson(route('admin.usuarios-capturados.resend-bitrix', $scan))
        ->assertOk();

    $scan->refresh();

    expect($scan->puesto)->toBe('Compras')
        ->and($scan->rescan_count)->toBe(0)
        ->and($scan->bitrix_status)->toBe(QrScan::BITRIX_PENDING);

    Bus::assertDispatchedAfterResponse(
        SendScanToBitrix::class,
        fn (SendScanToBitrix $job) => $job->force === true && $job->scan->is($scan),
    );
});

test('the resend button is rejected when the integration is disabled', function () {
    $scan = QrScan::create([
        'user_id' => $this->exhibidor->id,
        'nombre' => 'Mario',
        'correo' => 'mario2@acme.com',
    ]);

    $this->actingAs($this->admin)
        ->postJson(route('admin.usuarios-capturados.resend-bitrix', $scan))
        ->assertStatus(422);
});

test('the force job creates a new deal even when the scan already has one', function () {
    enableBitrix();

    Http::fake([
        '*crm.duplicate.findbycomm.json' => Http::response(['result' => ['CONTACT' => [42]]]),
        '*crm.deal.add.json' => Http::response(['result' => 99]),
    ]);

    $scan = QrScan::create([
        'user_id' => $this->exhibidor->id,
        'nombre' => 'Laura',
        'correo' => 'laura@acme.com',
    ]);
    $scan->forceFill(['bitrix_deal_id' => 50, 'bitrix_status' => QrScan::BITRIX_SENT])->save();

    (new SendScanToBitrix($scan, true))->handle();

    expect($scan->refresh()->bitrix_deal_id)->toBe(99);

    Http::assertSent(function ($request) {
        if (! str_contains($request->url(), 'crm.deal.add.json')) {
            return false;
        }

        return str_contains($request['fields']['TITLE'], 'Re-escaneo')
            && str_contains($request['fields']['COMMENTS'], 'deal anterior #50');
    });
});

test('the job creates the contact and the deal and marks the scan as sent', function () {
    enableBitrix();

    Http::fake([
        '*crm.duplicate.findbycomm.json' => Http::response(['result' => []]),
        '*crm.contact.add.json' => Http::response(['result' => 55]),
        '*crm.deal.add.json' => Http::response(['result' => 99]),
    ]);

    $scan = QrScan::create([
        'user_id' => $this->exhibidor->id,
        'nombre' => 'Juan',
        'apellidos' => 'Pérez',
        'empresa' => 'ACME',
        'correo' => 'juan@acme.com',
        'telefono' => '5551234567',
    ]);

    (new SendScanToBitrix($scan))->handle();

    $scan->refresh();

    expect($scan->bitrix_deal_id)->toBe(99)
        ->and($scan->bitrix_contact_id)->toBe(55)
        ->and($scan->bitrix_status)->toBe(QrScan::BITRIX_SENT)
        ->and($scan->bitrix_error)->toBeNull()
        ->and($scan->bitrix_synced_at)->not->toBeNull();

    Http::assertSent(function ($request) {
        if (! str_contains($request->url(), 'crm.deal.add.json')) {
            return false;
        }

        $fields = $request['fields'];

        return $fields['CATEGORY_ID'] === 13
            && $fields['CONTACT_ID'] === 55
            && str_contains($fields['TITLE'], 'Juan Pérez');
    });
});

test('the job reuses an existing bitrix contact', function () {
    enableBitrix();

    Http::fake([
        '*crm.duplicate.findbycomm.json' => Http::response(['result' => ['CONTACT' => [42]]]),
        '*crm.deal.add.json' => Http::response(['result' => 100]),
    ]);

    $scan = QrScan::create([
        'user_id' => $this->exhibidor->id,
        'nombre' => 'Rosa',
        'correo' => 'rosa@acme.com',
    ]);

    (new SendScanToBitrix($scan))->handle();

    expect($scan->refresh()->bitrix_contact_id)->toBe(42);

    Http::assertNotSent(fn ($request) => str_contains($request->url(), 'crm.contact.add.json'));
});

test('the job records the error when bitrix rejects the deal', function () {
    enableBitrix();

    Http::fake([
        '*crm.duplicate.findbycomm.json' => Http::response(['result' => []]),
        '*crm.contact.add.json' => Http::response(['result' => 55]),
        '*crm.deal.add.json' => Http::response([
            'error' => 'ACCESS_DENIED',
            'error_description' => 'Access denied.',
        ], 403),
    ]);

    $scan = QrScan::create([
        'user_id' => $this->exhibidor->id,
        'nombre' => 'Mario',
        'correo' => 'mario@acme.com',
    ]);

    expect(fn () => (new SendScanToBitrix($scan))->handle())->toThrow(RuntimeException::class);

    $scan->refresh();

    expect($scan->bitrix_status)->toBe(QrScan::BITRIX_FAILED)
        ->and($scan->bitrix_error)->toBe('Access denied.')
        ->and($scan->bitrix_deal_id)->toBeNull();
});

test('the test connection endpoint returns the webhook owner', function () {
    Http::fake([
        '*profile.json' => Http::response(['result' => ['ID' => 1, 'NAME' => 'Marco', 'LAST_NAME' => 'Hernández']]),
    ]);

    $this->actingAs($this->admin)
        ->postJson(route('admin.integraciones.bitrix.test'), [
            'webhook' => 'https://example.bitrix24.com/rest/1/token/',
        ])
        ->assertOk()
        ->assertJson(['ok' => true, 'user' => 'Marco Hernández']);
});

test('the test connection endpoint surfaces bitrix errors', function () {
    Http::fake([
        '*profile.json' => Http::response(['error' => 'INVALID_CREDENTIALS', 'error_description' => 'Invalid webhook.'], 401),
    ]);

    $this->actingAs($this->admin)
        ->postJson(route('admin.integraciones.bitrix.test'), [
            'webhook' => 'https://example.bitrix24.com/rest/1/bad/',
        ])
        ->assertStatus(422)
        ->assertJson(['ok' => false, 'error' => 'Invalid webhook.']);
});

test('sync pending resends unsent scans and groups', function () {
    Bus::fake();
    enableBitrix();

    QrScan::create(['user_id' => $this->exhibidor->id, 'nombre' => 'Uno', 'correo' => 'uno@acme.com']);
    QrScan::create([
        'user_id' => $this->exhibidor->id,
        'nombre' => 'Dos',
        'correo' => 'dos@acme.com',
    ])->forceFill(['bitrix_status' => QrScan::BITRIX_FAILED, 'bitrix_error' => 'x'])->save();
    QrScan::create([
        'user_id' => $this->exhibidor->id,
        'nombre' => 'Tres',
        'correo' => 'tres@acme.com',
    ])->forceFill(['bitrix_deal_id' => 5, 'bitrix_status' => QrScan::BITRIX_SENT])->save();

    $group = ScanGroup::create(['user_id' => $this->exhibidor->id, 'empresa' => 'ACME']);
    QrScan::create([
        'user_id' => $this->exhibidor->id,
        'scan_group_id' => $group->id,
        'nombre' => 'Cuatro',
        'correo' => 'cuatro@acme.com',
    ]);

    $this->actingAs($this->admin)
        ->postJson(route('admin.integraciones.bitrix.sync'))
        ->assertOk()
        ->assertJson(['ok' => true, 'queued' => 3]);

    Bus::assertDispatchedAfterResponse(SendScanToBitrix::class, 2);
    Bus::assertDispatchedAfterResponse(SendScanGroupToBitrix::class, 1);
});

test('sync pending is rejected when the integration is disabled', function () {
    $this->actingAs($this->admin)
        ->postJson(route('admin.integraciones.bitrix.sync'))
        ->assertStatus(422);
});
