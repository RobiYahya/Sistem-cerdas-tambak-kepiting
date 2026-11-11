<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test alur autentikasi dan dashboard multi-device.
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_login_page_loads_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Login');
        $response->assertSee('Masuk sebagai admin atau operator');
    }

    public function test_new_user_sees_empty_state_after_login(): void
    {
        $response = $this->withSession([
            'is_logged_in' => true,
            'devices'      => [],
        ])->get('/');

        $response->assertStatus(200);
        $response->assertSee('Monitoring Kualitas Air Laut');
        $response->assertSee('Belum ada perangkat tambak kepiting');
        $response->assertSee('Tambahkan perangkat pertama');
        $response->assertSee('Tambah Perangkat');
    }

    public function test_user_with_devices_can_see_device_grid(): void
    {
        $sampleDevices = [
            [
                'id' => 'CRAB-ESP32-001',
                'nama' => 'Tambak A - Pembesaran',
                'lokasi' => 'Blok Utara #01',
                'status' => 'online',
                'last_seen' => 'Baru saja',
                'kualitas' => 'BAIK',
                'indeks' => 84.2,
                'ph' => '8,12',
                'suhu' => '29,1 °C',
                'do' => '5,82',
                'salinitas' => '24,8 ppt',
            ],
        ];

        $response = $this->withSession([
            'is_logged_in' => true,
            'devices'      => $sampleDevices,
            'has_device'   => true,
        ])->get('/');

        $response->assertStatus(200);
        $response->assertSee('Tambak A - Pembesaran');
        $response->assertSee('CRAB-ESP32-001');
        $response->assertSee('ONLINE');
        $response->assertSee('Lihat Detail');
    }

    public function test_user_can_view_single_device_detail_monitoring(): void
    {
        $sampleDevices = [
            [
                'id' => 'CRAB-ESP32-001',
                'nama' => 'Tambak A - Pembesaran',
                'lokasi' => 'Blok Utara #01',
                'status' => 'online',
                'last_seen' => 'Baru saja',
                'kualitas' => 'BAIK',
                'indeks' => 84.2,
                'ph' => '8,12',
                'suhu' => '29,1 °C',
                'do' => '5,82',
                'salinitas' => '24,8 ppt',
            ],
        ];

        $response = $this->withSession([
            'is_logged_in' => true,
            'devices'      => $sampleDevices,
            'has_device'   => true,
        ])->get('/?device=CRAB-ESP32-001');

        $response->assertStatus(200);
        $response->assertSee('Status Kualitas Air');
        $response->assertSee('Tren Enam Parameter');
        $response->assertSee('Kembali ke Semua Perangkat');
    }

    public function test_login_submit_creates_session_and_redirects_to_dashboard(): void
    {
        $response = $this->post('/login');

        $response->assertRedirect('/');
        $response->assertSessionHas('is_logged_in', true);
    }

    public function test_riwayat_sensor_page_loads_successfully(): void
    {
        $response = $this->withSession([
            'is_logged_in' => true,
            'has_device'   => true,
        ])->get('/riwayat-sensor');

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
