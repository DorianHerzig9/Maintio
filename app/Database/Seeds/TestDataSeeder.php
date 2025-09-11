<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Users erstellen
        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@maintio.de',
                'password' => 'admin123',
                'first_name' => 'Max',
                'last_name' => 'Administrator',
                'role' => 'administrator',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'techniker1',
                'email' => 'techniker1@maintio.de',
                'password' => 'tech123',
                'first_name' => 'Anna',
                'last_name' => 'Müller',
                'role' => 'techniker',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'techniker2',
                'email' => 'techniker2@maintio.de',
                'password' => 'tech123',
                'first_name' => 'Peter',
                'last_name' => 'Schmidt',
                'role' => 'techniker',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Hash passwords
        foreach ($users as &$user) {
            $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
        }

        $this->db->table('users')->insertBatch($users);

        // Assets erstellen
        $assets = [
            [
                'name' => 'Produktionslinie A1',
                'asset_number' => 'PLA-001',
                'type' => 'Produktionslinie',
                'location' => 'Halle 1, Bereich A',
                'status' => 'operational',
                'priority' => 'high',
                'manufacturer' => 'Siemens',
                'model' => 'S7-1500',
                'serial_number' => 'SN001234',
                'installation_date' => '2020-01-15',
                'description' => 'Hauptproduktionslinie für Automobil-Komponenten',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Kompressor System B2',
                'asset_number' => 'KMP-002',
                'type' => 'Kompressor',
                'location' => 'Halle 2, Bereich B',
                'status' => 'maintenance',
                'priority' => 'critical',
                'manufacturer' => 'Atlas Copco',
                'model' => 'GA 55',
                'serial_number' => 'AC567890',
                'installation_date' => '2019-03-20',
                'description' => 'Druckluft-System für gesamte Produktion',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Roboter Arm C3',
                'asset_number' => 'ROB-003',
                'type' => 'Roboter',
                'location' => 'Halle 1, Bereich C',
                'status' => 'operational',
                'priority' => 'medium',
                'manufacturer' => 'KUKA',
                'model' => 'KR 10 R1100',
                'serial_number' => 'KU789012',
                'installation_date' => '2021-06-10',
                'description' => 'Schweißroboter für Karosserie-Arbeiten',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Förderband D4',
                'asset_number' => 'FBD-004',
                'type' => 'Förderband',
                'location' => 'Halle 3, Bereich D',
                'status' => 'out_of_order',
                'priority' => 'high',
                'manufacturer' => 'Dematic',
                'model' => 'FB-2000',
                'serial_number' => 'DM345678',
                'installation_date' => '2018-11-05',
                'description' => 'Hauptförderband für Materialfluss',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('assets')->insertBatch($assets);

        // Work Orders erstellen
        $workOrders = [
            [
                'work_order_number' => 'WO202509110001',
                'title' => 'Routinewartung Produktionslinie A1',
                'description' => 'Monatliche Inspektion und Wartung der Produktionslinie inkl. Schmierung und Kalibrierung',
                'type' => 'preventive',
                'status' => 'completed',
                'priority' => 'medium',
                'asset_id' => 1,
                'assigned_user_id' => 2,
                'created_by_user_id' => 1,
                'estimated_duration' => 240,
                'actual_duration' => 220,
                'scheduled_date' => date('Y-m-d 08:00:00', strtotime('-2 days')),
                'started_at' => date('Y-m-d 08:15:00', strtotime('-2 days')),
                'completed_at' => date('Y-m-d 11:55:00', strtotime('-2 days')),
                'notes' => 'Wartung erfolgreich abgeschlossen. Alle Parameter im Normbereich.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'work_order_number' => 'WO202509110002',
                'title' => 'Kritische Reparatur Kompressor B2',
                'description' => 'Kompressor zeigt Druckabfall und ungewöhnliche Geräusche. Sofortige Reparatur erforderlich.',
                'type' => 'emergency',
                'status' => 'in_progress',
                'priority' => 'critical',
                'asset_id' => 2,
                'assigned_user_id' => 3,
                'created_by_user_id' => 1,
                'estimated_duration' => 480,
                'actual_duration' => null,
                'scheduled_date' => date('Y-m-d 09:00:00'),
                'started_at' => date('Y-m-d 09:30:00'),
                'completed_at' => null,
                'notes' => 'Ersatzteile bestellt. Reparatur läuft.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'work_order_number' => 'WO202509110003',
                'title' => 'Software Update Roboter C3',
                'description' => 'Firmware-Update auf neueste Version für verbesserte Präzision',
                'type' => 'corrective',
                'status' => 'open',
                'priority' => 'low',
                'asset_id' => 3,
                'assigned_user_id' => 2,
                'created_by_user_id' => 1,
                'estimated_duration' => 120,
                'actual_duration' => null,
                'scheduled_date' => date('Y-m-d 14:00:00', strtotime('+1 day')),
                'started_at' => null,
                'completed_at' => null,
                'notes' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'work_order_number' => 'WO202509110004',
                'title' => 'Förderband Reparatur',
                'description' => 'Förderband D4 ist komplett ausgefallen. Motor und Steuerung prüfen.',
                'type' => 'emergency',
                'status' => 'open',
                'priority' => 'critical',
                'asset_id' => 4,
                'assigned_user_id' => null,
                'created_by_user_id' => 1,
                'estimated_duration' => 360,
                'actual_duration' => null,
                'scheduled_date' => date('Y-m-d 07:00:00', strtotime('+1 day')),
                'started_at' => null,
                'completed_at' => null,
                'notes' => 'Hohe Priorität - Produktion betroffen',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'work_order_number' => 'WO202509110005',
                'title' => 'Inspektion Sicherheitssysteme',
                'description' => 'Jährliche Inspektion aller Sicherheitssysteme und Notaus-Schalter',
                'type' => 'inspection',
                'status' => 'on_hold',
                'priority' => 'medium',
                'asset_id' => null,
                'assigned_user_id' => 2,
                'created_by_user_id' => 1,
                'estimated_duration' => 480,
                'actual_duration' => null,
                'scheduled_date' => date('Y-m-d 08:00:00', strtotime('+3 days')),
                'started_at' => null,
                'completed_at' => null,
                'notes' => 'Warten auf Zertifizierungsingenieur',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('work_orders')->insertBatch($workOrders);

        echo "Testdaten erfolgreich eingefügt!\n";
        echo "- " . count($users) . " Benutzer\n";
        echo "- " . count($assets) . " Anlagen\n";
        echo "- " . count($workOrders) . " Arbeitsaufträge\n";
    }
}
