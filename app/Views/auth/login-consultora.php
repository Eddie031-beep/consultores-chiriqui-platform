<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Consultora</title>
    <style>
        body{font-family: system-ui; background:#020617; color:#e5e7eb; display:flex; align-items:center; justify-content:center; height:100vh; margin:0;}
        .card{background:#0f172a; padding:2rem; border-radius:1rem; width:320px; box-shadow:0 20px 40px rgba(15,23,42,.7); border:1px solid rgba(148,163,184,.3);}
        h1{margin-top:0; font-size:1.4rem;}
        label{font-size:.9rem;}
        input{width:100%; padding:.5rem .6rem; border-radius:.5rem; border:1px solid #334155; background:#020617; color:#e5e7eb; margin-top:.25rem; margin-bottom:.75rem;}
        button{width:100%; padding:.6rem; border-radius:.5rem; border:none; background:#38bdf8; color:#0f172a; font-weight:600; cursor:pointer;}
        .error{background:#7f1d1d; padding:.5rem .75rem; border-radius:.5rem; font-size:.85rem; margin-bottom:.75rem;}
        a{color:#38bdf8; font-size:.85rem; text-decoration:none;}
    </style>
</head>
<body>
<div class="card">
    <h1>Login Consultora</h1>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="email">Correo</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Entrar</button>
    </form>

    <p style="margin-top:.75rem;">
        <a href="<?= ENV_APP['BASE_URL'] ?>/">Volver al inicio</a>
    </p>
</div>
</body>
</html>
