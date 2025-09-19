<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WorkOrderComponentsSeeder extends Seeder
{
    public function run()
    {
        // Finde einen existierenden Arbeitsauftrag oder erstelle einen
        $workOrderModel = new \App\Models\WorkOrderModel();
        $workOrder = $workOrderModel->first();
        
        if (!$workOrder) {
            // Erstelle einen Beispiel-Arbeitsauftrag falls keiner existiert
            $workOrderId = $workOrderModel->insert([
                'title' => 'Routinewartung Produktionslinie',
                'description' => 'Monatliche Inspektion und Wartung der Produktionslinie inkl. Schmierung und Kalibrierung',
                'type' => 'preventive',
                'status' => 'in_progress',
                'priority' => 'medium',
                'created_by_user_id' => 1,
                'estimated_duration' => 240
            ]);
        } else {
            $workOrderId = $workOrder['id'];
        }

        // KKS-Komponenten Daten
        $components = [
            [
                'work_order_id' => $workOrderId,
                'kks_number' => 'FBD-001',
                'component_name' => 'Förderband Eingang',
                'description' => 'Inspektion der Lager und Antriebsriemen',
                'status' => 'completed',
                'order_position' => 1
            ],
            [
                'work_order_id' => $workOrderId,
                'kks_number' => 'FBD-002',
                'component_name' => 'Förderband Ausgang',
                'description' => 'Schmierung der Lager, Kontrolle der Spannung',
                'status' => 'in_progress',
                'order_position' => 2
            ],
            [
                'work_order_id' => $workOrderId,
                'kks_number' => 'SEN-001',
                'component_name' => 'Positionssensor',
                'description' => 'Kalibrierung und Funktionstest',
                'status' => 'pending',
                'order_position' => 3
            ],
            [
                'work_order_id' => $workOrderId,
                'kks_number' => 'MOT-001',
                'component_name' => 'Hauptantriebsmotor',
                'description' => 'Überprüfung der Wicklungen und Isolation',
                'status' => 'pending',
                'order_position' => 4
            ],
            [
                'work_order_id' => $workOrderId,
                'kks_number' => 'HYD-001',
                'component_name' => 'Hydraulikzylinder',
                'description' => 'Dichtungen prüfen, Öl wechseln',
                'status' => 'pending',
                'order_position' => 5
            ],
            [
                'work_order_id' => $workOrderId,
                'kks_number' => 'PNE-001',
                'component_name' => 'Pneumatikventil',
                'description' => 'Funktion und Dichtheit prüfen',
                'status' => 'pending',
                'order_position' => 6
            ]
        ];

        // Lösche existierende Komponenten für diesen Arbeitsauftrag
        $db = \Config\Database::connect();
        $db->table('work_order_components')->where('work_order_id', $workOrderId)->delete();

        // Füge neue Komponenten hinzu
        $db->table('work_order_components')->insertBatch($components);

        echo "KKS-Komponenten für Arbeitsauftrag #{$workOrderId} wurden erfolgreich angelegt.\n";
    }
}