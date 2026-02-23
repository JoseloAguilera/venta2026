<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://npmcdn.com/select2@4.0.13/dist/js/i18n/es.js"></script>

<script>
    $(document).ready(function () {
        // Inicializar Select2 en todos los elementos select
        $('select').select2({
            language: "es",
            width: '100%' // Ajustar al ancho del contenedor
        });
    });
</script>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
<?php if (isset($extraJS)): ?>
    <?php foreach ($extraJS as $js): ?>
        <?php if (strpos($js, 'http') === 0): ?>
            <script src="<?= $js ?>"></script>
        <?php else: ?>
            <script src="<?= base_url($js) ?>"></script>
        <?php endif; ?>
    <?php endforeach; ?>
    <script>
                // Configuración global para DataTables en español
                if ($.fn.dataTable) {
                    $.extend(true, $.fn.dataTable.defaults, {
                        language: {
                            "decimal": "",
                            "emptyTable": "No hay información",
                            "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                            "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                            "infoPostFix": "",
                            "thousands": ",",
                            "lengthMenu": "Mostrar _MENU_ Entradas",
                            "loadingRecords": "Cargando...",
                            "processing": "Procesando...",
                            "search": "Buscar:",
                            "zeroRecords": "Sin resultados encontrados",
                            "paginate": {
                                "first": "Primero",
                                "last": "Ultimo",
                                "next": "›",
                                "previous": "‹"
                            }
                        }
                    });
                }
    </script>
<?php endif; ?>

<?php if (isset($scripts)): ?>
    <?= $scripts ?>
<?php endif; ?>
</body>

</html>