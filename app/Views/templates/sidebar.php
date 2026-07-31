<style>
/* Estilos con alta especificidad para los títulos de sección interactivos */
.sidebar .nav-section-title {
    padding: 0.65rem 0.9rem !important;
    margin: 0.25rem 0.5rem !important;
    border-radius: 8px !important;
    font-size: 0.74rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.08em !important;
    color: rgba(255, 255, 255, 0.65) !important;
    cursor: pointer !important;
    user-select: none !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    background: rgba(255, 255, 255, 0.03) !important;
    border-left: 4px solid transparent !important;
}

/* Hover súper vistoso al pasar el mouse por encima del título */
.sidebar .nav-section-title:hover {
    color: #ffffff !important;
    background: rgba(255, 255, 255, 0.16) !important;
    transform: translateX(4px) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
}

/* Estado Activo / Desplegado permanente mientras esté abierto */
.sidebar .nav-section:not(.collapsed) .nav-section-title {
    color: #ffffff !important;
    background: linear-gradient(90deg, rgba(99, 102, 241, 0.35) 0%, rgba(31, 41, 55, 0.6) 100%) !important;
    border-left: 4px solid #6366f1 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2) !important;
}

/* Badge de conteo de ítems dentro del título */
.sidebar .nav-section-title .section-badge {
    font-size: 0.68rem !important;
    padding: 0.15rem 0.5rem !important;
    border-radius: 10px !important;
    background: rgba(255, 255, 255, 0.12) !important;
    color: rgba(255, 255, 255, 0.75) !important;
    font-weight: 600 !important;
    transition: all 0.2s ease !important;
}

.sidebar .nav-section-title:hover .section-badge,
.sidebar .nav-section:not(.collapsed) .nav-section-title .section-badge {
    background: #6366f1 !important;
    color: #ffffff !important;
    box-shadow: 0 0 8px rgba(99, 102, 241, 0.5) !important;
}
</style>

<aside class="sidebar">
    <?php helper('permission'); ?>
    
    <!-- Header de Sidebar con Buscador Integrado -->
    <div class="sidebar-header" style="padding: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
        <?php
        $companyName = model('SettingsModel')->getValue('company_name', 'Sistema Ventas 2026');
        ?>
        <div class="sidebar-logo" style="font-size: 1.35rem; font-weight: 700; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">🚀 <?= esc($companyName) ?></div>
        
        <!-- Buscador Cápsula Ultra-Estético -->
        <div style="position: relative; margin-top: 0.75rem;">
            <input type="text" id="sidebarMenuSearch" placeholder="🔍 Buscar en menú..." autocomplete="off"
                   style="width: 100%; padding: 0.45rem 2rem 0.45rem 0.85rem; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 8px; color: #ffffff; font-size: 0.82rem; outline: none; transition: all 0.2s ease;">
            <span id="sidebarSearchClear" style="position: absolute; right: 0.6rem; top: 50%; transform: translateY(-50%); color: rgba(255, 255, 255, 0.6); cursor: pointer; display: none; font-size: 0.85rem; font-weight: bold; background: rgba(255,255,255,0.15); border-radius: 50%; width: 18px; height: 18px; line-height: 16px; text-align: center;">✕</span>
        </div>
    </div>

    <nav class="sidebar-nav" id="sidebarNav" style="padding: 0.5rem 0;">
        <!-- Mensaje sin resultados de búsqueda -->
        <div id="noSearchResults" class="px-3 py-3 text-muted small text-center" style="display: none; background: rgba(0,0,0,0.2); margin: 0.5rem; border-radius: 8px;">
            🔍 No se encontraron menúes
        </div>

        <?php
        $currentUri = uri_string();
        
        // Helper para determinar si la sección debe cargarse abierta por tener el enlace activo
        $isPrincipalActive = ($currentUri == 'dashboard');
        $isOperacionesActive = (strpos($currentUri, 'sales') !== false || strpos($currentUri, 'purchases') !== false);
        $isInventarioActive = (strpos($currentUri, 'categories') !== false || (strpos($currentUri, 'products') !== false && strpos($currentUri, 'product-stock') === false) || strpos($currentUri, 'warehouses') !== false || strpos($currentUri, 'product-stock') !== false || strpos($currentUri, 'inventory-adjustments') !== false || strpos($currentUri, 'inventory-transfers') !== false);
        $isContactosActive = (strpos($currentUri, 'customers') !== false || strpos($currentUri, 'suppliers') !== false);
        $isFinanzasActive = (strpos($currentUri, 'cash-sessions') !== false || (strpos($currentUri, 'accounts') !== false && strpos($currentUri, 'cash-sessions') === false) || strpos($currentUri, 'collections') !== false || strpos($currentUri, 'payments') !== false || strpos($currentUri, 'expenses') !== false);
        $isReportesActive = (strpos($currentUri, 'reports') !== false);
        $isSistemaActive = (strpos($currentUri, 'settings') !== false || strpos($currentUri, 'users') !== false || strpos($currentUri, 'roles') !== false);
        ?>

        <!-- Enlace directo a Dashboard (Sin encabezado de sección) -->
        <?php if (can_view('dashboard')): ?>
            <div class="px-2 mb-2">
                <a href="<?= base_url('dashboard') ?>" class="nav-link <?= $currentUri == 'dashboard' ? 'active' : '' ?>" style="margin: 0; padding: 0.65rem 0.9rem; border-radius: 8px;">
                    <span class="nav-icon">📊</span>
                    <span>Dashboard</span>
                </a>
            </div>
        <?php endif; ?>

        <!-- Seccion: Operaciones -->
        <div class="nav-section <?= $isOperacionesActive ? '' : 'collapsed' ?>" data-section="operaciones">
            <div class="nav-section-title">
                <span>Operaciones</span>
                <span class="section-badge">2</span>
            </div>
            <div class="nav-section-items" style="<?= $isOperacionesActive ? 'display: block;' : 'display: none;' ?>">
                <?php if (can_view('sales')): ?>
                    <a href="<?= base_url('sales') ?>"
                        class="nav-link <?= strpos($currentUri, 'sales') !== false ? 'active' : '' ?>">
                        <span class="nav-icon">💰</span>
                        <span>Ventas</span>
                    </a>
                <?php endif; ?>

                <?php if (can_view('purchases')): ?>
                    <a href="<?= base_url('purchases') ?>"
                        class="nav-link <?= strpos($currentUri, 'purchases') !== false ? 'active' : '' ?>">
                        <span class="nav-icon">🛒</span>
                        <span>Compras</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Seccion: Inventario -->
        <div class="nav-section <?= $isInventarioActive ? '' : 'collapsed' ?>" data-section="inventario">
            <div class="nav-section-title">
                <span>Inventario</span>
                <span class="section-badge">6</span>
            </div>
            <div class="nav-section-items" style="<?= $isInventarioActive ? 'display: block;' : 'display: none;' ?>">
                <?php if (can_view('categories')): ?>
                    <a href="<?= base_url('categories') ?>"
                        class="nav-link <?= strpos($currentUri, 'categories') !== false ? 'active' : '' ?>">
                        <span class="nav-icon">📁</span>
                        <span>Categorías</span>
                    </a>
                <?php endif; ?>

                <?php if (can_view('products')): ?>
                    <a href="<?= base_url('products') ?>"
                        class="nav-link <?= strpos($currentUri, 'products') !== false && strpos($currentUri, 'product-stock') === false ? 'active' : '' ?>">
                        <span class="nav-icon">📦</span>
                        <span>Productos</span>
                    </a>
                <?php endif; ?>

                <?php if (can_view('warehouses')): ?>
                    <a href="<?= base_url('warehouses') ?>"
                        class="nav-link <?= strpos($currentUri, 'warehouses') !== false ? 'active' : '' ?>">
                        <span class="nav-icon">🏭</span>
                        <span>Depósitos</span>
                    </a>
                <?php endif; ?>

                <?php if (can_view('product_stock')): ?>
                    <a href="<?= base_url('product-stock') ?>"
                        class="nav-link <?= strpos($currentUri, 'product-stock') !== false ? 'active' : '' ?>">
                        <span class="nav-icon">📊</span>
                        <span>Stock de Productos</span>
                    </a>
                <?php endif; ?>

                <?php if (can_view('inventory_adjustments')): ?>
                    <a href="<?= base_url('inventory-adjustments') ?>"
                        class="nav-link <?= strpos($currentUri, 'inventory-adjustments') !== false ? 'active' : '' ?>">
                        <span class="nav-icon">⚖️</span>
                        <span>Ajustes de Inventario</span>
                    </a>
                <?php endif; ?>

                <?php if (can_view('inventory_transfers')): ?>
                    <a href="<?= base_url('inventory-transfers') ?>"
                        class="nav-link <?= strpos($currentUri, 'inventory-transfers') !== false ? 'active' : '' ?>">
                        <span class="nav-icon">🚚</span>
                        <span>Transferencias</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Seccion: Contactos -->
        <div class="nav-section <?= $isContactosActive ? '' : 'collapsed' ?>" data-section="contactos">
            <div class="nav-section-title">
                <span>Contactos</span>
                <span class="section-badge">2</span>
            </div>
            <div class="nav-section-items" style="<?= $isContactosActive ? 'display: block;' : 'display: none;' ?>">
                <?php if (can_view('customers')): ?>
                    <a href="<?= base_url('customers') ?>"
                        class="nav-link <?= strpos($currentUri, 'customers') !== false ? 'active' : '' ?>">
                        <span class="nav-icon">👥</span>
                        <span>Clientes</span>
                    </a>
                <?php endif; ?>

                <?php if (can_view('suppliers')): ?>
                    <a href="<?= base_url('suppliers') ?>"
                        class="nav-link <?= strpos($currentUri, 'suppliers') !== false ? 'active' : '' ?>">
                        <span class="nav-icon">🏢</span>
                        <span>Proveedores</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Seccion: Finanzas -->
        <div class="nav-section <?= $isFinanzasActive ? '' : 'collapsed' ?>" data-section="finanzas">
            <div class="nav-section-title">
                <span>Finanzas</span>
                <span class="section-badge">5</span>
            </div>
            <div class="nav-section-items" style="<?= $isFinanzasActive ? 'display: block;' : 'display: none;' ?>">
                <a href="<?= base_url('cash-sessions') ?>"
                    class="nav-link <?= strpos($currentUri, 'cash-sessions') !== false ? 'active' : '' ?>">
                    <span class="nav-icon">🔑</span>
                    <span>Sesiones / Arqueos</span>
                </a>

                <?php if (can_view('accounts')): ?>
                    <a href="<?= base_url('accounts') ?>"
                        class="nav-link <?= strpos($currentUri, 'accounts') !== false && strpos($currentUri, 'cash-sessions') === false ? 'active' : '' ?>">
                        <span class="nav-icon">🏦</span>
                        <span>Cuentas y Cajas</span>
                    </a>
                <?php endif; ?>

                <?php if (can_view('collections')): ?>
                    <a href="<?= base_url('collections') ?>"
                        class="nav-link <?= strpos($currentUri, 'collections') !== false ? 'active' : '' ?>">
                        <span class="nav-icon">💰</span>
                        <span>Cobranzas</span>
                    </a>
                <?php endif; ?>

                <?php if (can_view('payments')): ?>
                    <a href="<?= base_url('payments') ?>"
                        class="nav-link <?= strpos($currentUri, 'payments') !== false ? 'active' : '' ?>">
                        <span class="nav-icon">💳</span>
                        <span>Pagos</span>
                    </a>
                <?php endif; ?>

                <?php if (can_view('expenses')): ?>
                    <a href="<?= base_url('expenses') ?>"
                        class="nav-link <?= strpos($currentUri, 'expenses') !== false ? 'active' : '' ?>">
                        <span class="nav-icon">💸</span>
                        <span>Gastos</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Seccion: Reportes -->
        <div class="nav-section <?= $isReportesActive ? '' : 'collapsed' ?>" data-section="reportes">
            <div class="nav-section-title">
                <span>Reportes</span>
                <span class="section-badge">3</span>
            </div>
            <div class="nav-section-items" style="<?= $isReportesActive ? 'display: block;' : 'display: none;' ?>">
                <a href="<?= base_url('reports/sales') ?>"
                    class="nav-link <?= $currentUri == 'reports/sales' ? 'active' : '' ?>">
                    <span class="nav-icon">📄</span>
                    <span>Rep. Ventas</span>
                </a>
                <a href="<?= base_url('reports/profit-by-sale') ?>"
                    class="nav-link <?= $currentUri == 'reports/profit-by-sale' ? 'active' : '' ?>">
                    <span class="nav-icon">📈</span>
                    <span>Ganancia por Venta</span>
                </a>
                <a href="<?= base_url('reports/profit-by-item') ?>"
                    class="nav-link <?= $currentUri == 'reports/profit-by-item' ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span>
                    <span>Ganancia por Artículo</span>
                </a>
            </div>
        </div>

        <!-- Seccion: Sistema -->
        <?php if (can_view('settings') || can_view('roles') || can_view('users')): ?>
            <div class="nav-section <?= $isSistemaActive ? '' : 'collapsed' ?>" data-section="sistema">
                <div class="nav-section-title">
                    <span>Sistema</span>
                    <span class="section-badge">3</span>
                </div>
                <div class="nav-section-items" style="<?= $isSistemaActive ? 'display: block;' : 'display: none;' ?>">
                    <?php if (can_view('settings')): ?>
                        <a href="<?= base_url('settings') ?>"
                            class="nav-link <?= strpos($currentUri, 'settings') !== false ? 'active' : '' ?>">
                            <span class="nav-icon">⚙️</span>
                            <span>Configuración</span>
                        </a>
                    <?php endif; ?>

                    <?php if (can_view('users')): ?>
                        <a href="<?= base_url('users') ?>"
                            class="nav-link <?= strpos($currentUri, 'users') !== false ? 'active' : '' ?>">
                            <span class="nav-icon">👤</span>
                            <span>Usuarios</span>
                        </a>
                    <?php endif; ?>

                    <?php if (can_view('roles')): ?>
                        <a href="<?= base_url('roles') ?>"
                            class="nav-link <?= strpos($currentUri, 'roles') !== false ? 'active' : '' ?>">
                            <span class="nav-icon">🔐</span>
                            <span>Roles</span>
                        </a>
                    <?php endif; ?>

                    <?php if (can_view('settings')): ?>
                        <a href="<?= base_url('audit-logs') ?>"
                            class="nav-link <?= strpos($currentUri, 'audit-logs') !== false ? 'active' : '' ?>">
                            <span class="nav-icon">📜</span>
                            <span>Bitácora Auditoría</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <?= strtoupper(substr(session()->get('username'), 0, 1)) ?>
            </div>
            <div class="user-details">
                <a href="<?= base_url('profile') ?>" style="text-decoration: none; color: inherit;">
                    <p class="user-name"><?= esc(session()->get('username')) ?></p>
                </a>
                <p class="user-role"><?= ucfirst(session()->get('role_name') ?? session()->get('role') ?? '') ?></p>
            </div>
        </div>
        <a href="<?= base_url('auth/logout') ?>" class="btn btn-secondary btn-sm"
            style="margin-top: 1rem; width: 100%;">
            Cerrar Sesión
        </a>
    </div>
</aside>

<!-- Script para Secciones Colapsables y Buscador de Menú Instantáneo -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('sidebarMenuSearch');
    const searchClear = document.getElementById('sidebarSearchClear');
    const navSections = document.querySelectorAll('.nav-section');
    const noResultsMsg = document.getElementById('noSearchResults');

    // Manejo de Colapsado e Interacción de Secciones
    navSections.forEach(section => {
        const badge = section.querySelector('.section-badge');
        const visibleLinks = section.querySelectorAll('.nav-link');
        if (badge) {
            badge.textContent = visibleLinks.length;
        }

        const titleEl = section.querySelector('.nav-section-title');
        const itemsEl = section.querySelector('.nav-section-items');
        const sectionId = section.dataset.section;
        const hasActiveLink = section.querySelector('.nav-link.active') !== null;

        function setSectionState(isCollapsed) {
            if (isCollapsed) {
                section.classList.add('collapsed');
                if (itemsEl) itemsEl.style.display = 'none';
            } else {
                section.classList.remove('collapsed');
                if (itemsEl) itemsEl.style.display = 'block';
            }
        }

        // Función para cerrar todas las demás secciones (Modo Acordeón Único)
        function closeAllOtherSections() {
            navSections.forEach(otherSection => {
                if (otherSection !== section) {
                    otherSection.classList.add('collapsed');
                    const otherItems = otherSection.querySelector('.nav-section-items');
                    if (otherItems) otherItems.style.display = 'none';
                }
            });
        }

        // Cargar estado inicial
        const activeSectionStored = localStorage.getItem('sidebar_active_section');

        if (hasActiveLink) {
            closeAllOtherSections();
            setSectionState(false);
        } else if (activeSectionStored && activeSectionStored === sectionId) {
            setSectionState(false);
        } else {
            setSectionState(true);
        }

        // Clic en el título para abrir/cerrar sección (Acordeón Único)
        if (titleEl) {
            titleEl.addEventListener('click', function (e) {
                e.preventDefault();
                if (searchInput && searchInput.value.trim() !== '') return;

                const currentlyCollapsed = section.classList.contains('collapsed') || (itemsEl && itemsEl.style.display === 'none');

                if (currentlyCollapsed) {
                    // Plegar todas las demás secciones para que sólo 1 quede activa
                    closeAllOtherSections();
                    setSectionState(false);
                    localStorage.setItem('sidebar_active_section', sectionId);
                } else {
                    setSectionState(true);
                    localStorage.removeItem('sidebar_active_section');
                }
            });
        }
    });

    // Función auxiliar para normalizar texto (eliminar acentos y tildes)
    function normalizeStr(str) {
        return str ? str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase() : '';
    }

    // Buscador Instantáneo
    if (searchInput) {
        searchInput.addEventListener('focus', function() {
            this.style.background = 'rgba(0, 0, 0, 0.4)';
            this.style.borderColor = 'rgba(99, 102, 241, 0.7)';
            this.style.boxShadow = '0 0 12px rgba(99, 102, 241, 0.3)';
        });
        searchInput.addEventListener('blur', function() {
            if (!this.value) {
                this.style.background = 'rgba(255, 255, 255, 0.08)';
                this.style.borderColor = 'rgba(255, 255, 255, 0.15)';
                this.style.boxShadow = 'none';
            }
        });

        searchInput.addEventListener('input', function () {
            const query = normalizeStr(this.value.trim());

            if (query === '') {
                searchClear.style.display = 'none';
                noResultsMsg.style.display = 'none';

                // Restaurar estado
                navSections.forEach(section => {
                    section.style.display = '';
                    const itemsEl = section.querySelector('.nav-section-items');
                    const sectionId = section.dataset.section;
                    const hasActiveLink = section.querySelector('.nav-link.active') !== null;
                    const isCollapsedStored = localStorage.getItem('sidebar_section_' + sectionId);

                    if (hasActiveLink) {
                        section.classList.remove('collapsed');
                        if (itemsEl) itemsEl.style.display = 'block';
                    } else if (isCollapsedStored === 'false') {
                        section.classList.remove('collapsed');
                        if (itemsEl) itemsEl.style.display = 'block';
                    } else {
                        section.classList.add('collapsed');
                        if (itemsEl) itemsEl.style.display = 'none';
                    }

                    section.querySelectorAll('.nav-link').forEach(link => {
                        link.style.display = '';
                    });
                });
                return;
            }

            searchClear.style.display = 'block';
            let totalMatches = 0;

            navSections.forEach(section => {
                let sectionMatches = 0;
                const itemsEl = section.querySelector('.nav-section-items');
                const links = section.querySelectorAll('.nav-link');

                links.forEach(link => {
                    const text = normalizeStr(link.textContent);
                    if (text.includes(query)) {
                        link.style.display = '';
                        sectionMatches++;
                        totalMatches++;
                    } else {
                        link.style.display = 'none';
                    }
                });

                if (sectionMatches > 0) {
                    section.style.display = '';
                    section.classList.remove('collapsed');
                    if (itemsEl) itemsEl.style.display = 'block'; // Mostrar items al buscar
                } else {
                    section.style.display = 'none';
                }
            });

            if (totalMatches === 0) {
                noResultsMsg.style.display = 'block';
            } else {
                noResultsMsg.style.display = 'none';
            }
        });

        searchClear.addEventListener('click', function () {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
            searchInput.focus();
        });

        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                this.value = '';
                this.dispatchEvent(new Event('input'));
            }
        });
    }
});
</script>