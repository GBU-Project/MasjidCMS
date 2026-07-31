<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePrayerTimesTable extends Migration
{
    public function up(): void
    {
        // Prayer times table for storing daily schedule
        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'auto_increment' => true],
            'prayer_name'   => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => false], // Subuh, Dzuhur, Ashar, Maghrib, Isya
            'prayer_time'   => ['type' => 'TIME', 'null' => false],
            'iqamah_time'   => ['type' => 'TIME', 'null' => true],
            'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'sort_order'    => ['type' => 'INT', 'constraint' => 3, 'default' => 0],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('prayer_name');
        $this->forge->createTable('prayer_times', true);

        // Insert default prayer times
        $defaultTimes = [
            ['prayer_name' => 'Subuh',   'prayer_time' => '04:38:00', 'iqamah_time' => '04:58:00', 'sort_order' => 1],
            ['prayer_name' => 'Dzuhur',  'prayer_time' => '12:05:00', 'iqamah_time' => '12:25:00', 'sort_order' => 2],
            ['prayer_name' => 'Ashar',   'prayer_time' => '15:20:00', 'iqamah_time' => '15:40:00', 'sort_order' => 3],
            ['prayer_name' => 'Maghrib', 'prayer_time' => '18:02:00', 'iqamah_time' => '18:12:00', 'sort_order' => 4],
            ['prayer_name' => 'Isya',    'prayer_time' => '19:14:00', 'iqamah_time' => '19:34:00', 'sort_order' => 5],
        ];

        if ($this->db->tableExists('prayer_times')) {
            foreach ($defaultTimes as $pt) {
                $check = $this->db->table('prayer_times')
                    ->where('prayer_name', $pt['prayer_name'])
                    ->get()
                    ->getRowArray();
                if (!$check) {
                    $pt['created_at'] = date('Y-m-d H:i:s');
                    $this->db->table('prayer_times')->insert($pt);
                }
            }
        }

        // Add prayer time settings to settings table
        if ($this->db->tableExists('settings')) {
            $prayerSettings = [
                'prayer_city'        => 'Kota Masjid',
                'prayer_method'      => 'KEMENAG',
                'prayer_auto_update' => '0',
                'prayer_latitude'    => '',
                'prayer_longitude'   => '',
                'prayer_timezone'    => 'Asia/Jakarta',
            ];
            foreach ($prayerSettings as $key => $val) {
                $check = $this->db->table('settings')->where('setting_key', $key)->get()->getRowArray();
                if (!$check) {
                    $data = [
                        'setting_key'   => $key,
                        'setting_value' => $val,
                        'setting_group' => 'prayer',
                    ];
                    $this->db->table('settings')->insert($data);
                }
            }
        }
    }

    public function down(): void
    {
        $this->forge->dropTable('prayer_times', true);
        if ($this->db->tableExists('settings')) {
            $keys = ['prayer_city', 'prayer_method', 'prayer_auto_update', 'prayer_latitude', 'prayer_longitude', 'prayer_timezone'];
            foreach ($keys as $k) {
                $this->db->table('settings')->where('setting_key', $k)->delete();
            }
        }
    }
}