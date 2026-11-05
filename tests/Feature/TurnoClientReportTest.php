<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\EstadoTurno;
use App\Models\Turno;
use App\Models\User;
use Carbon\CarbonImmutable;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TurnoClientReportTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_authorized_user_can_generate_last_year_report_for_one_client(): void
    {
        CarbonImmutable::setTestNow('2026-11-04 14:00:00');
        $this->seed(DatabaseSeeder::class);
        $administrator = User::query()->where('username', 'admin')->firstOrFail();
        $client = Cliente::query()->where('dni', '30111222')->firstOrFail();

        $this->actingAs($administrator)
            ->get(route('turnos.reports.client', ['cliente_id' => $client->id]))
            ->assertOk()
            ->assertSee('Turnos por cliente')
            ->assertSee('Martínez, Ana')
            ->assertSee('AB123CD')
            ->assertDontSee('AC456EF')
            ->assertDontSee('AD789GH')
            ->assertSee('Evolución mensual')
            ->assertSee('Distribución por estado')
            ->assertSee('Detalle operativo');
    }

    public function test_report_excludes_appointments_older_than_one_year(): void
    {
        CarbonImmutable::setTestNow('2026-11-04 14:00:00');
        $this->seed(DatabaseSeeder::class);
        $administrator = User::query()->where('username', 'admin')->firstOrFail();
        $client = Cliente::query()->where('dni', '30111222')->firstOrFail();
        $oldAppointmentState = EstadoTurno::query()->where('codigo', 'atendido')->firstOrFail();
        Turno::query()->create([
            'cliente_id' => $client->id,
            'vehiculo_id' => $client->vehiculos()->firstOrFail()->id,
            'estado_turno_id' => $oldAppointmentState->id,
            'registrado_por' => $administrator->id,
            'fecha' => '2025-10-01',
            'hora' => '10:00',
            'motivo' => 'TURNO FUERA DEL PERÍODO',
        ]);

        $this->actingAs($administrator)
            ->get(route('turnos.reports.client', ['cliente_id' => $client->id]))
            ->assertOk()
            ->assertDontSee('TURNO FUERA DEL PERÍODO');
    }

    public function test_client_without_appointments_in_last_year_receives_an_empty_state(): void
    {
        CarbonImmutable::setTestNow('2026-11-04 14:00:00');
        $this->seed(DatabaseSeeder::class);
        $administrator = User::query()->where('username', 'admin')->firstOrFail();
        $client = Cliente::query()->create([
            'dni' => '40123456',
            'nombre' => 'Elena',
            'apellido' => 'Torres',
            'telefono' => '1144445555',
            'activo' => true,
        ]);

        $this->actingAs($administrator)
            ->get(route('turnos.reports.client', ['cliente_id' => $client->id]))
            ->assertOk()
            ->assertSee('El cliente no posee turnos en el último año')
            ->assertDontSee('Detalle operativo');
    }

    public function test_report_rejects_an_unknown_client(): void
    {
        CarbonImmutable::setTestNow('2026-11-04 14:00:00');
        $this->seed(DatabaseSeeder::class);
        $administrator = User::query()->where('username', 'admin')->firstOrFail();

        $this->actingAs($administrator)
            ->from(route('turnos.reports.client'))
            ->get(route('turnos.reports.client', ['cliente_id' => 999999]))
            ->assertRedirect(route('turnos.reports.client'))
            ->assertSessionHasErrors('cliente_id');
    }

    public function test_report_requires_authentication_and_turns_role(): void
    {
        $this->get(route('turnos.reports.client'))->assertRedirect('/login');

        $this->actingAs(User::factory()->create())
            ->get(route('turnos.reports.client'))
            ->assertForbidden();
    }
}
