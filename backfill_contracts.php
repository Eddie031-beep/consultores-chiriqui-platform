<?php
// backfill_contracts.php
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/database.php';

$db = db_connect('local');

echo "<h1>Backfilling Missing Contracts</h1>";
echo "<pre>";

try {
    // 1. Find companies without contracts
    $sql = "
        SELECT e.* 
        FROM empresas e 
        LEFT JOIN contratos_empresas c ON e.id = c.empresa_id 
        WHERE c.id IS NULL AND e.estado = 'activa'
    ";
    $stmt = $db->query($sql);
    $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($empresas) . " companies without contracts.\n";
    echo "---------------------------------------------------\n";

    if (empty($empresas)) {
        echo "No action needed.\n";
        exit;
    }

    $count = 0;
    foreach ($empresas as $emp) {
        echo "Processing: " . $emp['nombre'] . " (ID: " . $emp['id'] . ")...\n";

        // Logic copied from ConsultoraController::crearContratoInterno
        $contrato = "ACUERDO DE SERVICIO COMERCIAL (Términos y Condiciones)\n\n";
        $contrato .= "ENTRE: Consultores Chiriquí S.A. (La Plataforma)\n";
        $contrato .= "Y: " . $emp['nombre'] . " (El Cliente)\n";
        $contrato .= "RUC: " . $emp['ruc'] . "-" . $emp['dv'] . "\n\n";
        $contrato .= "OBJETO: Prestación de servicios de intermediación laboral y publicidad de vacantes.\n\n";
        $contrato .= "TARIFAS VIGENTES (MODELO DE PEAJE):\n";
        $contrato .= "1. Visualización de Vacante: B/. 1.50\n";
        $contrato .= "2. Postulación (Click Apply): B/. 5.00\n";
        $contrato .= "3. Consulta IA (Chatbot): B/. 2.50\n\n";
        $contrato .= "CONDICIONES DE PAGO:\n";
        $contrato .= "Las facturas se emitirán mensualmente según el consumo real registrado por el sistema.\n";
        $contrato .= "El cliente acepta los registros electrónicos como prueba de servicio.\n\n";
        $contrato .= "ACEPTACIÓN:\n";
        $contrato .= "Fecha de Registro Automático: " . date('Y-m-d H:i:s') . "\n";
        $contrato .= "Nota: Contrato generado retroactivamente para regularización.\n\n";
        $contrato .= "-- Documento generado electrónicamente --";

        $sqlContrato = "INSERT INTO contratos_empresas 
                        (empresa_id, version_contrato, texto_resumen, ip_aceptacion, fecha_aceptacion)
                        VALUES (?, 'v1.0-BACKFILL', ?, 'SYSTEM', NOW())";
        
        $stmtContrato = $db->prepare($sqlContrato);
        $stmtContrato->execute([$emp['id'], $contrato]);
        
        echo "✅ Contract created.\n";
        $count++;
    }

    echo "---------------------------------------------------\n";
    echo "Done! Generated $count contracts.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

echo "</pre>";
