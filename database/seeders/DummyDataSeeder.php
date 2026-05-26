<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\Report;
use App\Models\Task;
use App\Models\Technician;
use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Fixed admin account
        $admin = User::firstOrCreate(
            ['email' => 'admin@siprt.com'],
            ['name' => 'Administrator', 'password' => Hash::make('password')]
        );
        $admin->syncRoles(['admin']);

        $sales = User::firstOrCreate(
            ['email' => 'sales@siprt.com'],
            ['name' => 'Sales User', 'password' => Hash::make('password')]
        );
        $sales->syncRoles(['sales']);

        $teknisi = User::firstOrCreate(
            ['email' => 'teknisi@siprt.com'],
            ['name' => 'Technician User', 'password' => Hash::make('password')]
        );
        $teknisi->syncRoles(['teknisi']);

        // Fixed customer portal account linked to a named customer record
        $customerRecord = Customer::firstOrCreate(
            ['email' => 'pt.majujaya@example.com'],
            [
                'name'    => 'PT Maju Jaya Abadi',
                'phone'   => '0812-3456-7890',
                'address' => 'Jl. Industri No. 45, Batam',
            ]
        );
        $customerPortalUser = CustomerUser::firstOrCreate(
            ['email' => 'customer@siprt.com'],
            [
                'name'        => 'PT Maju Jaya Abadi',
                'password'    => Hash::make('password'),
                'customer_id' => $customerRecord->id,
            ]
        );
        if (!$customerPortalUser->customer_id) {
            $customerPortalUser->update(['customer_id' => $customerRecord->id]);
        }

        // ── Templates ─────────────────────────────────────────────────────
        $templates = [
            [
                'name'       => 'Template CCTV Standard',
                'created_by' => $admin->id,
                'fields'     => [
                    [
                        'id'     => 's1',
                        'title'  => 'Detail Pekerjaan',
                        'fields' => [
                            ['id' => 'f1', 'type' => 'text',     'label' => 'Kondisi Lokasi',    'placeholder' => 'Contoh: Baik / Kurang pencahayaan', 'required' => true,  'options' => ''],
                            ['id' => 'f2', 'type' => 'textarea', 'label' => 'Deskripsi Pekerjaan','placeholder' => 'Jelaskan pekerjaan yang dilakukan',  'required' => true,  'options' => ''],
                            ['id' => 'f3', 'type' => 'number',   'label' => 'Jumlah Kamera',     'placeholder' => '0',                                  'required' => true,  'options' => ''],
                        ],
                    ],
                    [
                        'id'     => 's2',
                        'title'  => 'Checklist Instalasi',
                        'fields' => [
                            ['id' => 'f4', 'type' => 'checkbox', 'label' => 'Semua kamera terpasang dan berfungsi', 'placeholder' => '', 'required' => false, 'options' => ''],
                            ['id' => 'f5', 'type' => 'checkbox', 'label' => 'DVR/NVR dikonfigurasi',                'placeholder' => '', 'required' => false, 'options' => ''],
                            ['id' => 'f6', 'type' => 'checkbox', 'label' => 'Uji remote viewing berhasil',          'placeholder' => '', 'required' => false, 'options' => ''],
                        ],
                    ],
                    [
                        'id'     => 's3',
                        'title'  => 'Material Digunakan',
                        'fields' => [
                            ['id' => 'f7', 'type' => 'text',   'label' => 'Jenis Kabel',         'placeholder' => 'Contoh: Coaxial RG6 / UTP Cat6', 'required' => false, 'options' => ''],
                            ['id' => 'f8', 'type' => 'number', 'label' => 'Jumlah Konektor (pcs)','placeholder' => '0',                              'required' => false, 'options' => ''],
                        ],
                    ],
                    [
                        'id'     => 's4',
                        'title'  => 'Dokumentasi',
                        'fields' => [
                            ['id' => 'f9',  'type' => 'photo', 'label' => 'Foto Sebelum Pemasangan', 'placeholder' => '', 'required' => false, 'options' => ''],
                            ['id' => 'f10', 'type' => 'photo', 'label' => 'Foto Sesudah Pemasangan', 'placeholder' => '', 'required' => false, 'options' => ''],
                        ],
                    ],
                    [
                        'id'     => 's5',
                        'title'  => 'Persetujuan',
                        'fields' => [
                            ['id' => 'f11', 'type' => 'signature', 'label' => 'Tanda Tangan Teknisi',      'placeholder' => '', 'required' => true, 'options' => ''],
                            ['id' => 'f12', 'type' => 'signature', 'label' => 'Tanda Tangan PIC Customer', 'placeholder' => '', 'required' => true, 'options' => ''],
                        ],
                    ],
                ],
            ],
            [
                'name'       => 'Template Jaringan LAN',
                'created_by' => $admin->id,
                'fields'     => [
                    [
                        'id'     => 's1',
                        'title'  => 'Kondisi Jaringan',
                        'fields' => [
                            ['id' => 'f1', 'type' => 'text',     'label' => 'Topologi Jaringan',  'placeholder' => 'Contoh: Star, Ring, Bus',        'required' => true,  'options' => ''],
                            ['id' => 'f2', 'type' => 'number',   'label' => 'Jumlah Titik Jaringan','placeholder' => '0',                            'required' => true,  'options' => ''],
                            ['id' => 'f3', 'type' => 'textarea', 'label' => 'Kondisi Awal',        'placeholder' => 'Jelaskan kondisi jaringan awal', 'required' => false, 'options' => ''],
                        ],
                    ],
                    [
                        'id'     => 's2',
                        'title'  => 'Pekerjaan Dilakukan',
                        'fields' => [
                            ['id' => 'f4', 'type' => 'textarea', 'label' => 'Deskripsi Pekerjaan', 'placeholder' => 'Jelaskan tindakan yang diambil',    'required' => true,  'options' => ''],
                            ['id' => 'f5', 'type' => 'text',     'label' => 'Perangkat Diganti',   'placeholder' => 'Contoh: Switch 24-port, Patch panel', 'required' => false, 'options' => ''],
                        ],
                    ],
                    [
                        'id'     => 's3',
                        'title'  => 'Hasil Pengujian',
                        'fields' => [
                            ['id' => 'f6', 'type' => 'text',   'label' => 'Hasil Ping',              'placeholder' => 'Contoh: < 5ms semua node', 'required' => false, 'options' => ''],
                            ['id' => 'f7', 'type' => 'number', 'label' => 'Kecepatan Download (Mbps)','placeholder' => '0',                       'required' => false, 'options' => ''],
                            ['id' => 'f8', 'type' => 'number', 'label' => 'Kecepatan Upload (Mbps)', 'placeholder' => '0',                       'required' => false, 'options' => ''],
                        ],
                    ],
                    [
                        'id'     => 's4',
                        'title'  => 'Dokumentasi',
                        'fields' => [
                            ['id' => 'f9', 'type' => 'photo', 'label' => 'Foto Instalasi Jaringan', 'placeholder' => '', 'required' => false, 'options' => ''],
                        ],
                    ],
                    [
                        'id'     => 's5',
                        'title'  => 'Persetujuan',
                        'fields' => [
                            ['id' => 'f10', 'type' => 'signature', 'label' => 'Tanda Tangan Teknisi',      'placeholder' => '', 'required' => true, 'options' => ''],
                            ['id' => 'f11', 'type' => 'signature', 'label' => 'Tanda Tangan PIC Customer', 'placeholder' => '', 'required' => true, 'options' => ''],
                        ],
                    ],
                ],
            ],
            [
                'name'       => 'Template Instalasi Access Point',
                'created_by' => $admin->id,
                'fields'     => [
                    [
                        'id'     => 's1',
                        'title'  => 'Spesifikasi Perangkat',
                        'fields' => [
                            ['id' => 'f1', 'type' => 'text',   'label' => 'Merek / Model AP',     'placeholder' => 'Contoh: Ubiquiti UAP-AC-Pro', 'required' => true,  'options' => ''],
                            ['id' => 'f2', 'type' => 'number', 'label' => 'Jumlah Unit',           'placeholder' => '0',                           'required' => true,  'options' => ''],
                            ['id' => 'f3', 'type' => 'text',   'label' => 'Lokasi Pemasangan',     'placeholder' => 'Contoh: Lantai 2, Ruang Meeting', 'required' => true, 'options' => ''],
                        ],
                    ],
                    [
                        'id'     => 's2',
                        'title'  => 'Konfigurasi Jaringan',
                        'fields' => [
                            ['id' => 'f4', 'type' => 'text',   'label' => 'SSID',          'placeholder' => 'Nama jaringan WiFi',    'required' => true,  'options' => ''],
                            ['id' => 'f5', 'type' => 'select', 'label' => 'Channel',        'placeholder' => '',                     'required' => false, 'options' => "1\n6\n11\nAuto"],
                            ['id' => 'f6', 'type' => 'select', 'label' => 'Keamanan WiFi',  'placeholder' => '',                     'required' => true,  'options' => "WPA2\nWPA3\nOpen"],
                        ],
                    ],
                    [
                        'id'     => 's3',
                        'title'  => 'Hasil Pengujian',
                        'fields' => [
                            ['id' => 'f7', 'type' => 'select', 'label' => 'Kekuatan Sinyal', 'placeholder' => '', 'required' => true,  'options' => "Kuat\nSedang\nLemah"],
                            ['id' => 'f8', 'type' => 'text',   'label' => 'Coverage Area',   'placeholder' => 'Contoh: Seluruh lantai 2 (± 300 m²)', 'required' => false, 'options' => ''],
                        ],
                    ],
                    [
                        'id'     => 's4',
                        'title'  => 'Dokumentasi',
                        'fields' => [
                            ['id' => 'f9',  'type' => 'photo', 'label' => 'Foto Posisi AP Terpasang', 'placeholder' => '', 'required' => false, 'options' => ''],
                            ['id' => 'f10', 'type' => 'photo', 'label' => 'Foto Layar Konfigurasi',   'placeholder' => '', 'required' => false, 'options' => ''],
                        ],
                    ],
                    [
                        'id'     => 's5',
                        'title'  => 'Persetujuan',
                        'fields' => [
                            ['id' => 'f11', 'type' => 'signature', 'label' => 'Tanda Tangan Teknisi',      'placeholder' => '', 'required' => true, 'options' => ''],
                            ['id' => 'f12', 'type' => 'signature', 'label' => 'Tanda Tangan PIC Customer', 'placeholder' => '', 'required' => true, 'options' => ''],
                        ],
                    ],
                ],
            ],
            [
                'name'       => 'Template Servis Perangkat Umum',
                'created_by' => $admin->id,
                'fields'     => [
                    [
                        'id'     => 's1',
                        'title'  => 'Identifikasi Perangkat',
                        'fields' => [
                            ['id' => 'f1', 'type' => 'text',     'label' => 'Jenis Perangkat',  'placeholder' => 'Contoh: Laptop, Printer, Switch',     'required' => true,  'options' => ''],
                            ['id' => 'f2', 'type' => 'text',     'label' => 'Merek / Model',    'placeholder' => 'Contoh: HP EliteBook 840 G8',          'required' => true,  'options' => ''],
                            ['id' => 'f3', 'type' => 'textarea', 'label' => 'Keluhan Pelanggan','placeholder' => 'Jelaskan keluhan atau kerusakan awal',  'required' => true,  'options' => ''],
                        ],
                    ],
                    [
                        'id'     => 's2',
                        'title'  => 'Penanganan',
                        'fields' => [
                            ['id' => 'f4', 'type' => 'textarea', 'label' => 'Tindakan yang Diambil', 'placeholder' => 'Jelaskan langkah-langkah perbaikan',          'required' => true,  'options' => ''],
                            ['id' => 'f5', 'type' => 'text',     'label' => 'Komponen Diganti',      'placeholder' => 'Contoh: RAM 8GB, Baterai, Toner cartridge',   'required' => false, 'options' => ''],
                            ['id' => 'f6', 'type' => 'date',     'label' => 'Estimasi Selesai',      'placeholder' => '',                                           'required' => false, 'options' => ''],
                        ],
                    ],
                    [
                        'id'     => 's3',
                        'title'  => 'Hasil Akhir',
                        'fields' => [
                            ['id' => 'f7', 'type' => 'select',   'label' => 'Status Akhir', 'placeholder' => '', 'required' => true,  'options' => "Selesai\nPerlu Follow-up\nTidak dapat diperbaiki"],
                            ['id' => 'f8', 'type' => 'textarea', 'label' => 'Catatan Tambahan', 'placeholder' => 'Catatan atau rekomendasi untuk pelanggan', 'required' => false, 'options' => ''],
                        ],
                    ],
                    [
                        'id'     => 's4',
                        'title'  => 'Persetujuan',
                        'fields' => [
                            ['id' => 'f9',  'type' => 'signature', 'label' => 'Tanda Tangan Teknisi',      'placeholder' => '', 'required' => true, 'options' => ''],
                            ['id' => 'f10', 'type' => 'signature', 'label' => 'Tanda Tangan PIC Customer', 'placeholder' => '', 'required' => true, 'options' => ''],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($templates as $tpl) {
            Template::firstOrCreate(
                ['name' => $tpl['name'], 'created_by' => $tpl['created_by']],
                ['fields' => $tpl['fields']]
            );
        }

        // 2 sales users
        $salesUsers = User::factory(2)->create()->each(function ($user) {
            $user->assignRole('sales');
        });

        // 5 teknisi users with technician profiles
        $teknisiUsers = User::factory(5)->create()->each(function ($user) {
            $user->assignRole('teknisi');
            Technician::factory()->create(['user_id' => $user->id]);
        });

        // 10 customers
        Customer::factory(10)->create();

        // 20 tasks spread across teknisi
        Task::factory(20)->create();

        // 1–2 reports per completed or in-progress task
        Task::whereIn('status', ['in_progress', 'completed'])->get()->each(function ($task) {
            $count = $task->status === 'completed' ? 2 : 1;
            Report::factory($count)->create([
                'task_id' => $task->id,
                'user_id' => $task->assigned_to,
            ]);
        });
    }
}
