<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_riwayat_sensor_page_loads_successfully(): void
    {
        $response = $this->get('/riwayat-sensor');

        $response->assertStatus(200);
        $response->assertSee('Riwayat Sensor');
        $response->assertSee('Riwayat Parameter Air');
        $response->assertSee('From');
        $response->assertSee('To');
        $response->assertSee('48 entri');
        $response->assertSee('Unduh CSV');
    }

    public function test_riwayat_sensor_export_csv_streams_correctly(): void
    {
        $response = $this->get('/riwayat-sensor/export-csv');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=utf-8');
    }
}
