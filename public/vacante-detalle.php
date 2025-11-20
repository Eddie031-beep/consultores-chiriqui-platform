                </div>
                <h1 class="vacante-titulo">
                    <?php echo htmlspecialchars($vacante['titulo']); ?>
                </h1>

                <div class="badges-group">
                    <span class="badge badge-modalidad">
                        <?php 
                            $modalidades = ['presencial' => '🏢 Presencial', 'remoto' => '🏠 Remoto', 'hibrido' => '🔄 Híbrido'];
                            echo $modalidades[$vacante['modalidad']] ?? $vacante['modalidad'];
                        ?>
                    </span>
                    <span class="badge badge-tipo">
                        <?php echo htmlspecialchars($vacante['tipo_contrato']); ?>
                    </span>
                    <?php if($vacante['sector']): ?>
                        <span class="badge badge-sector">
                            📊 <?php echo htmlspecialchars($vacante['sector']); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="vacante-info-rapida">
                    <div class="info-item">
                        <div class="info-item-icono">📍</div>
                        <div class="info-item-contenido">
                            <div class="info-item-label">Ubicación</div>
                            <div class="info-item-valor"><?php echo htmlspecialchars($vacante['ubicacion']); ?></div>
                        </div>
                    </div>

                    <?php if($vacante['salario_min'] && $vacante['salario_max']): ?>
                        <div class="info-item">
                            <div class="info-item-icono">💰</div>
                            <div class="info-item-contenido">
                                <div class="info-item-label">Salario</div>
                                <div class="info-item-valor">
                                    B/. <?php echo number_format($vacante['salario_min'], 2); ?> - 
                                    B/. <?php echo number_format($vacante['salario_max'], 2); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="info-item">
                        <div class="info-item-icono">📅</div>
                        <div class="info-item-contenido">
                            <div class="info-item-label">Publicada</div>
                            <div class="info-item-valor">
                                <?php echo date('d/m/Y', strtotime($vacante['fecha_publicacion'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerta de registro -->
            <div class="alerta-registro">
                <strong>⚠️ Nota importante:</strong> Para postularte a esta vacante deberás registrarte en la plataforma. El registro es rápido y obligatorio para poder aplicar a cualquier posición.
            </div>

            <!-- Descripción -->
            <div class="section">
                <h2 class="section-titulo">📋 Descripción de la Vacante</h2>
                <div class="section-contenido">
                    <?php echo nl2br(htmlspecialchars($vacante['descripcion'])); ?>
                </div>
            </div>

            <!-- Información de la Empresa -->
            <div class="section">
                <h2 class="section-titulo">🏢 Información de la Empresa</h2>
                <div class="empresa-info">
                    <h3><?php echo htmlspecialchars($vacante['empresa_nombre']); ?></h3>
                    
                    <div class="empresa-info-item">
                        <span>📊 Sector:</span>
                        <strong><?php echo htmlspecialchars($vacante['sector'] ?? 'No especificado'); ?></strong>
                    </div>

                    <?php if($vacante['telefono']): ?>
                        <div class="empresa-info-item">
                            <span>📞 Teléfono:</span>
                            <a href="tel:<?php echo htmlspecialchars($vacante['telefono']); ?>" style="color: #667eea; text-decoration: none;">
                                <?php echo htmlspecialchars($vacante['telefono']); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if($vacante['email_contacto']): ?>
                        <div class="empresa-info-item">
                            <span>📧 Email:</span>
                            <a href="mailto:<?php echo htmlspecialchars($vacante['email_contacto']); ?>" style="color: #667eea; text-decoration: none;">
                                <?php echo htmlspecialchars($vacante['email_contacto']); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if($vacante['sitio_web']): ?>
                        <div class="empresa-info-item">
                            <span>🌐 Sitio Web:</span>
                            <a href="<?php echo htmlspecialchars($vacante['sitio_web']); ?>" target="_blank" style="color: #667eea; text-decoration: none;">
                                <?php echo htmlspecialchars($vacante['sitio_web']); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Acciones -->
            <div class="acciones">
                <a href="vacantes.php" class="btn-volver">← Volver al Listado</a>
                <a href="../auth/postular.php?vacante_id=<?php echo $vacante['id']; ?>" class="btn-postular">
                    ✓ Postularme a esta Vacante
                </a>
            </div>
        </div>
    </div>
</body>
</html>