<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $modo === 'editar' ? 'Editar vacante' : 'Nueva vacante' ?></title>
    <style>
        body{font-family:system-ui;background:#020617;color:#e5e7eb;margin:0;padding:2rem;}
        .card{max-width:720px;margin:0 auto;background:#0f172a;border-radius:1rem;padding:2rem;border:1px solid #1e293b;box-shadow:0 25px 50px rgba(15,23,42,.7);}
        label{display:block;font-size:.85rem;margin-bottom:.25rem;}
        input,textarea,select{width:100%;padding:.5rem .6rem;border-radius:.5rem;border:1px solid #334155;background:#020617;color:#e5e7eb;margin-bottom:.75rem;font-size:.9rem;}
        textarea{min-height:120px;resize:vertical;}
        button{padding:.6rem 1.2rem;border-radius:.5rem;border:none;background:#22c55e;color:#022c22;font-weight:600;cursor:pointer;}
        .error{color:#fca5a5;font-size:.8rem;margin-top:-.5rem;margin-bottom:.5rem;}
        a{color:#38bdf8;font-size:.9rem;}
        .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;}
    </style>
</head>
<body>
<div class="card">
    <div class="top">
        <h1 style="margin:0;font-size:1.4rem;">
            <?= $modo === 'editar' ? 'Editar vacante' : 'Registrar nueva vacante' ?>
        </h1>
        <a href="<?= ENV_APP['BASE_URL'] ?>/empresa/vacantes">Volver al listado</a>
    </div>

    <?php
    // Evitar warnings de variable no asignada
    $vacante = $vacante ?? null;
    $old     = $old ?? [];
    $errores = $errores ?? [];

    $v = $vacante ?? [];
    $valor = function(string $campo, $default = '') use ($v, $old) {
        if (isset($old[$campo])) return $old[$campo];
        return $v[$campo] ?? $default;
    };
    ?>


    <form method="post" action="">
        <?php if ($modo === 'editar'): ?>
            <input type="hidden" name="id" value="<?= (int)($v['id'] ?? 0) ?>">
        <?php endif; ?>

        <label for="titulo">Título de la vacante</label>
        <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($valor('titulo')) ?>" required>
        <?php if (!empty($errores['titulo'])): ?><div class="error"><?= htmlspecialchars($errores['titulo']) ?></div><?php endif; ?>

        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" required><?= htmlspecialchars($valor('descripcion')) ?></textarea>
        <?php if (!empty($errores['descripcion'])): ?><div class="error"><?= htmlspecialchars($errores['descripcion']) ?></div><?php endif; ?>

        <label for="tipo_contrato">Tipo de contrato</label>
        <input type="text" id="tipo_contrato" name="tipo_contrato" placeholder="Tiempo completo, medio tiempo, etc."
               value="<?= htmlspecialchars($valor('tipo_contrato')) ?>" required>
        <?php if (!empty($errores['tipo_contrato'])): ?><div class="error"><?= htmlspecialchars($errores['tipo_contrato']) ?></div><?php endif; ?>

        <label for="salario_min">Salario mínimo (opcional)</label>
        <input type="number" step="0.01" id="salario_min" name="salario_min"
               value="<?= htmlspecialchars($valor('salario_min')) ?>">

        <label for="salario_max">Salario máximo (opcional)</label>
        <input type="number" step="0.01" id="salario_max" name="salario_max"
               value="<?= htmlspecialchars($valor('salario_max')) ?>">

        <label for="ubicacion">Ubicación</label>
        <input type="text" id="ubicacion" name="ubicacion" value="<?= htmlspecialchars($valor('ubicacion')) ?>" required>
        <?php if (!empty($errores['ubicacion'])): ?><div class="error"><?= htmlspecialchars($errores['ubicacion']) ?></div><?php endif; ?>

        <!-- COST INPUT -->
        <label for="costo">Costo por Vista ($):</label>
        <input type="number" id="costo" name="costo_por_vista" step="0.01" min="1.00" max="10.00" value="<?= htmlspecialchars($valor('costo_por_vista', '1.00')) ?>" required>
        <small style="display:block;margin-top:-0.5rem;margin-bottom:0.75rem;color:#94a3b8;font-size:0.8rem;">El costo debe estar entre B/. 1.00 y B/. 10.00</small>
        <!-- END COST INPUT -->

        <label for="modalidad">Modalidad</label>
        <select id="modalidad" name="modalidad">
            <?php
            $mod = $valor('modalidad', 'presencial');
            ?>
            <option value="presencial" <?= $mod === 'presencial' ? 'selected' : '' ?>>Presencial</option>
            <option value="remoto" <?= $mod === 'remoto' ? 'selected' : '' ?>>Remoto</option>
            <option value="hibrido" <?= $mod === 'hibrido' ? 'selected' : '' ?>>Híbrido</option>
        </select>

        <button type="submit">
            <?= $modo === 'editar' ? 'Guardar cambios' : 'Crear vacante' ?>
        </button>
    </form>
</div>
</body>
</html>
