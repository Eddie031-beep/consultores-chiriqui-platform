    </script>

        <!-- Filtros -->
        <div class="filtros">
            <h3>🔍 Filtrar Vacantes</h3>
            <form method="GET" action="">
                <div class="filtros-grid">
                    <div class="filtro-grupo">
                        <label for="busqueda">Buscar por título o descripción</label>
                        <input type="text" id="busqueda" name="busqueda" 
                               value="<?php echo htmlspecialchars($busqueda); ?>" 
                               placeholder="Ej: Desarrollador, Analista...">
                    </div>

                    <div class="filtro-grupo">
                        <label for="empresa">Empresa</label>
                        <select id="empresa" name="empresa">
                            <option value="">Todas las empresas</option>
                            <?php foreach($empresas as $emp): ?>
                                <option value="<?php echo $emp['id']; ?>" 
                                    <?php echo $filtro_empresa == $emp['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($emp['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filtro-grupo">
                        <label for="ubicacion">Ubicación</label>
                        <select id="ubicacion" name="ubicacion">
                            <option value="">Todas las ubicaciones</option>
                            <?php foreach($ubicaciones as $ubi): ?>
                                <option value="<?php echo htmlspecialchars($ubi['ubicacion']); ?>" 
                                    <?php echo $filtro_ubicacion == $ubi['ubicacion'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ubi['ubicacion']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filtro-grupo">
                        <label for="modalidad">Modalidad</label>
                        <select id="modalidad" name="modalidad">
                            <option value="">Todas las modalidades</option>
                            <option value="presencial" <?php echo $filtro_modalidad == 'presencial' ? 'selected' : ''; ?>>
                                Presencial
                            </option>
                            <option value="remoto" <?php echo $filtro_modalidad == 'remoto' ? 'selected' : ''; ?>>
                                Remoto
                            </option>
                            <option value="hibrido" <?php echo $filtro_modalidad == 'hibrido' ? 'selected' : ''; ?>>
                                Híbrido
                            </option>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 15px;">
                    <button type="submit" class="btn-buscar">🔍 Buscar</button>
                    <a href="vacantes.php" class="btn-limpiar">✕ Limpiar Filtros</a>
                </div>
            </form>
        </div>

        <!-- Contador -->
        <div class="contador-vacantes">
            Mostrando <strong><?php echo count($vacantes); ?></strong> vacante(s) disponible(s)
        </div>

        <!-- Listado de Vacantes -->
        <div class="vacantes-grid" style="margin-top: 20px;">
            <?php if(empty($vacantes)): ?>
                <div class="sin-resultados">
                    <h3>No se encontraron vacantes</h3>
                    <p>Intenta con otros criterios de búsqueda o regresa más tarde para nuevas oportunidades.</p>
                    <a href="vacantes.php" class="btn-buscar" style="display: inline-block; margin-top: 15px;">
                        Ver Todas las Vacantes
                    </a>
                </div>
            <?php else: ?>
                <?php foreach($vacantes as $vacante): ?>
                    <div class="vacante-card">
                        <div class="vacante-header">
                            <div class="vacante-empresa">
                                <?php echo htmlspecialchars($vacante['empresa_nombre']); ?>
                            </div>
                            <h2 class="vacante-titulo">
                                <?php echo htmlspecialchars($vacante['titulo']); ?>
                            </h2>
                        </div>

                        <div class="vacante-badges">
                            <span class="badge badge-modalidad">
                                <?php 
                                    $modalidades = ['presencial' => '🏢 Presencial', 'remoto' => '🏠 Remoto', 'hibrido' => '🔄 Híbrido'];
                                    echo $modalidades[$vacante['modalidad']] ?? $vacante['modalidad'];
                                ?>
                            </span>
                            <span class="badge badge-tipo">
                                <?php echo htmlspecialchars($vacante['tipo_contrato']); ?>
                            </span>
                        </div>

                        <div class="vacante-ubicacion">
                            <?php echo htmlspecialchars($vacante['ubicacion']); ?>
                        </div>

                        <?php if($vacante['salario_min'] && $vacante['salario_max']): ?>
                            <div class="vacante-salario">
                                <strong>💰 Salario:</strong> B/. <?php echo number_format($vacante['salario_min'], 2); ?> - 
                                B/. <?php echo number_format($vacante['salario_max'], 2); ?>
                            </div>
                        <?php endif; ?>

                        <p class="vacante-descripcion">
                            <?php echo htmlspecialchars(substr($vacante['descripcion'], 0, 150)); ?>...
                        </p>

                        <div class="vacante-acciones">
                            <a href="vacante-detalle.php?id=<?php echo $vacante['id']; ?>&slug=<?php echo htmlspecialchars($vacante['slug']); ?>" 
                               class="btn-detalles">
                                Ver Detalles
                            </a>
                            <a href="../auth/postular.php?vacante_id=<?php echo $vacante['id']; ?>" 
                               class="btn-postular">
                                Postularme
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>