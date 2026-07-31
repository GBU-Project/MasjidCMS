<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class FixBidangSection extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'fix:bidang';
    protected $description = 'Fix bidang section visibility in homepage settings';

    public function run(array $params)
    {
        $db = Database::connect();

        // 1. Fix homepage_section_order to include 'bidang' and 'agenda'
        $order = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'homepage_section_order'")->getRowArray();
        if ($order) {
            $decoded = json_decode($order['setting_value'], true);
            CLI::write('Current order: ' . json_encode($decoded), 'yellow');
            if (is_array($decoded)) {
                $changed = false;
                if (!in_array('bidang', $decoded)) {
                    $pos = array_search('layanan', $decoded);
                    if ($pos !== false) {
                        array_splice($decoded, $pos + 1, 0, 'bidang');
                    } else {
                        $decoded[] = 'bidang';
                    }
                    $changed = true;
                }
                if (!in_array('agenda', $decoded)) {
                    $decoded[] = 'agenda';
                    $changed = true;
                }
                if ($changed) {
                    $db->query("UPDATE settings SET setting_value = ? WHERE setting_key = 'homepage_section_order'", [json_encode($decoded)]);
                    CLI::write('Updated homepage_section_order: ' . json_encode($decoded), 'green');
                } else {
                    CLI::write('bidang and agenda already in order.', 'green');
                }
            }
        } else {
            CLI::error('homepage_section_order not found!');
        }

        // 2. Add limit_bidang if missing
        $limitBidang = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'limit_bidang'")->getRowArray();
        if (!$limitBidang) {
            $db->query("INSERT INTO settings (setting_key, setting_value, setting_group) VALUES ('limit_bidang', '6', 'homepage')");
            CLI::write('Added limit_bidang = 6', 'green');
        } else {
            CLI::write('limit_bidang already exists: ' . $limitBidang['setting_value'], 'green');
        }

        // 3. Add show_agenda_section if missing
        $showAgenda = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'show_agenda_section'")->getRowArray();
        if (!$showAgenda) {
            $db->query("INSERT INTO settings (setting_key, setting_value, setting_group) VALUES ('show_agenda_section', '1', 'homepage')");
            CLI::write('Added show_agenda_section = 1', 'green');
        } else {
            CLI::write('show_agenda_section already exists: ' . $showAgenda['setting_value'], 'green');
        }

        // 4. Add limit_agenda if missing
        $limitAgenda = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'limit_agenda'")->getRowArray();
        if (!$limitAgenda) {
            $db->query("INSERT INTO settings (setting_key, setting_value, setting_group) VALUES ('limit_agenda', '5', 'homepage')");
            CLI::write('Added limit_agenda = 5', 'green');
        } else {
            CLI::write('limit_agenda already exists: ' . $limitAgenda['setting_value'], 'green');
        }

        CLI::write('Fix completed!', 'green');
    }
}