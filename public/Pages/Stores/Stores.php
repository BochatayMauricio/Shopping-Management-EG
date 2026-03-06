<?php
// Inicialización (sesión, logout, usuario)
include_once __DIR__ . '/../../../app/init.php';
include_once __DIR__ . '/../../../app/Services/promotions.services.php';
include_once __DIR__ . '/../../../app/Services/stores.services.php';
include_once __DIR__ . '/../../../app/controllers/store.controller.php';

// Identificar locales del dueño
$myStoreIds = [];
if ($user && $user['type'] === 'owner') {
    $userId = $user['cod'] ?? $user['id'] ?? 0;
    $myStoresData = getStoresByOwner($userId);
    foreach ($myStoresData as $ms) {
        $myStoreIds[] = $ms['id']; 
    }
}

// Lógica de Filtros
$filterCategory = $_GET['category'] ?? 'all';
$filterFloor = $_GET['ubication'] ?? 'all';
$searchName = trim($_GET['search'] ?? '');

// ========== PAGINACIÓN ==========
$cantPorPag = 6;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;

$inicio = ($pagina - 1) * $cantPorPag;

$totalRegistros = getTotalStores($filterCategory, $filterFloor, $searchName);
$totalPaginas = ceil($totalRegistros / $cantPorPag);

$filteredStores = getStoresPaginated($inicio, $cantPorPag, $filterCategory, $filterFloor, $searchName);
// ================================

$hay_filtros = ($filterCategory !== 'all' || $filterFloor !== 'all' || !empty($searchName));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locales - Shopping Rosario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../Shared/globalStyles.css">
    <link rel="stylesheet" href="stores.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">



</head>
<body>
    <?php include_once __DIR__ . '/../../Components/navbar/NavBar.php'; ?>

    <main class="main-content container-custom">
        <header class="stores-header d-flex justify-content-between align-items-end mb-4 pt-4">
            <div>
                <h1 class="stores-title mb-0">Locales</h1>
                <p class="text-muted mb-0">Explora las mejores tiendas del Shopping Rosario</p>
            </div>
            <?php if ($user && $user['type'] === 'admin'): ?>
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow" data-bs-toggle="modal" data-bs-target="#addStoreModal">
                    <i class="fas fa-plus-circle me-2"></i> Nuevo Local
                </button>
            <?php endif; ?>
        </header>

        <section class="filter-dropdown-group p-3 mb-4 bg-white rounded-4 shadow-sm border">
            <div class="row align-items-center g-3 w-100">
                <div class="col-auto">
                    <span class="filter-label fw-bold"><i class="fas fa-filter text-primary me-1"></i> Filtrar:</span>
                </div>
                <div class="col-auto">
                    <div class="dropdown-custom">
                        <input type="checkbox" id="drop-cat" class="dropdown-checkbox">
                        <label for="drop-cat" class="dropdown-toggle-custom">Rubro <i class="fas fa-chevron-down ms-2"></i></label>
                        <div class="dropdown-menu-custom shadow border-0">
                            <a href="?category=all&ubication=<?= $filterFloor ?>&search=<?= $searchName ?>" class="dropdown-item-custom">Todos</a>
                            <a href="?category=gastronomia&ubication=<?= $filterFloor ?>&search=<?= $searchName ?>" class="dropdown-item-custom">Gastronomía</a>
                            <a href="?category=ropa&ubication=<?= $filterFloor ?>&search=<?= $searchName ?>" class="dropdown-item-custom">Ropa</a>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="dropdown-custom">
                        <input type="checkbox" id="drop-floor" class="dropdown-checkbox">
                        <label for="drop-floor" class="dropdown-toggle-custom">Piso <i class="fas fa-chevron-down ms-2"></i></label>
                        <div class="dropdown-menu-custom shadow border-0">
                            <a href="?ubication=all&category=<?= $filterCategory ?>&search=<?= $searchName ?>" class="dropdown-item-custom">Todos</a>
                            <a href="?ubication=Planta Baja&category=<?= $filterCategory ?>&search=<?= $searchName ?>" class="dropdown-item-custom">Planta Baja</a>
                            <a href="?ubication=Primer Piso&category=<?= $filterCategory ?>&search=<?= $searchName ?>" class="dropdown-item-custom">Primer Piso</a>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <form method="GET" class="d-flex border rounded-pill px-3 py-1 bg-light">
                        <input type="hidden" name="category" value="<?= $filterCategory ?>">
                        <input type="hidden" name="ubication" value="<?= $filterFloor ?>">
                        <input type="text" name="search" class="form-control border-0 bg-transparent" placeholder="Buscar local..." value="<?= htmlspecialchars($searchName) ?>">
                        <button type="submit" class="btn btn-link text-primary p-0"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
        </section>

        <?php if ($hay_filtros): ?>
            <div class="active-filters-container mb-4 d-flex align-items-center gap-2 flex-wrap">
                <span class="small text-muted">Filtros activos:</span>
                <?php if ($filterCategory !== 'all'): ?>
                    <div class="filter-badge">Rubro: <?= ucfirst($filterCategory) ?> <a href="?category=all&ubication=<?= $filterFloor ?>&search=<?= $searchName ?>">×</a></div>
                <?php endif; ?>
                <?php if ($filterFloor !== 'all'): ?>
                    <div class="filter-badge">Piso: <?= $filterFloor ?> <a href="?ubication=all&category=<?= $filterCategory ?>&search=<?= $searchName ?>">×</a></div>
                <?php endif; ?>
                <?php if ($searchName): ?>
                    <div class="filter-badge">"<?= htmlspecialchars($searchName) ?>" <a href="?search=&category=<?= $filterCategory ?>&ubication=<?= $filterFloor ?>">×</a></div>
                <?php endif; ?>
                <a href="Stores.php" class="clear-all-filters small ms-2">Limpiar todo</a>
            </div>
        <?php endif; ?>

        <section class="stores-grid-container">
            <?php foreach ($filteredStores as $store): 
                $isMine = in_array($store['id'], $myStoreIds);
                renderStoreCard($store, $isMine); 
            endforeach; ?>
        </section>

        <?php if ($totalPaginas > 1): ?>
        <nav class="pagination-container mt-4 d-flex justify-content-center">
            <ul class="pagination">
                <?php 
                $queryParams = $_GET;
                unset($queryParams['pagina']);
                $baseUrl = '?' . http_build_query($queryParams) . (empty($queryParams) ? '' : '&');
                ?>
                
                <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $baseUrl ?>pagina=<?= $pagina - 1 ?>">Anterior</a>
                </li>
                
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                        <a class="page-link" href="<?= $baseUrl ?>pagina=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                
                <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $baseUrl ?>pagina=<?= $pagina + 1 ?>">Siguiente</a>
                </li>
            </ul>
        </nav>
        <p class="text-center text-muted small">Mostrando página <?= $pagina ?> de <?= $totalPaginas ?> (<?= $totalRegistros ?> locales)</p>
        <?php endif; ?>
    </main>

    <?php if ($user && $user['type'] === 'admin'): ?>
    <div class="modal fade" id="addStoreModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-header bg-primary text-white p-4">
                        <h5 class="modal-title fw-bold">Registrar Nuevo Local</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-4 text-center border-end d-flex flex-column align-items-center">
                                <div id="createLogoPreview" class="rounded-4 border bg-light mb-2 d-flex align-items-center justify-content-center" style="width:150px; height:150px; overflow:hidden;">
                                    <i class="fas fa-image text-muted fa-3x"></i>
                                </div>
                                <small class="text-muted">Vista previa del logo</small>
                            </div>
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">URL del Logo</label>
                                        <input type="url" id="input_url" name="logo_icon" class="form-control rounded-pill px-3" placeholder="https://...">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">O subir archivo</label>
                                        <input type="file" id="input_file" name="logo_file" class="form-control" accept="image/*">
                                    </div>

                                    <div class="col-md-7">
                                        <label class="form-label small fw-bold">Nombre del Local</label>
                                        <input type="text" name="name" class="form-control rounded-pill px-3" required placeholder="Ej: Samsung Store">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label small fw-bold">Rubro / Categoría</label>
                                        <select name="category" class="form-select rounded-pill px-3" required>
                                            <option value="" selected disabled>Seleccionar...</option>
                                            <option value="tecnologia">Tecnología</option>
                                            <option value="gastronomia">Gastronomía</option>
                                            <option value="ropa">Ropa</option>
                                            <option value="hogar">Hogar</option>
                                            <option value="otros">Otros</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Ubicación</label>
                                        <select name="ubication" class="form-select rounded-pill px-3">
                                            <option value="Planta Baja">Planta Baja</option>
                                            <option value="Primer Piso">Primer Piso</option>
                                            <option value="Segundo Piso">Segundo Piso</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">N° Local</label>
                                        <input type="text" name="local_number" class="form-control rounded-pill px-3" required placeholder="Ej: L-45">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small fw-bold">Color de Marca</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="color" id="input_color" name="color" class="form-control form-control-color rounded-circle border-0" value="#0d6efd" title="Elegí el color del local" style="width: 45px; height: 45px; cursor: pointer;">
                                            <small class="text-muted">Se usará en el borde de la tarjeta.</small>
                                        </div>
                                    </div>

                                    <div class="col-12 border-top pt-3 mt-3">
                                        <label class="form-label small fw-bold mb-2 d-block">Dueño Responsable</label>
                                        <div class="d-flex gap-3 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="owner_mode" id="owner_mode_existing" value="existing" checked onchange="toggleOwnerFields()">
                                                <label class="form-check-label small" for="owner_mode_existing">Seleccionar Existente</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="owner_mode" id="owner_mode_new" value="new" onchange="toggleOwnerFields()">
                                                <label class="form-check-label small" for="owner_mode_new">Crear Nuevo Dueño</label>
                                            </div>
                                        </div>

                                        <div id="div_existing_owner">
                                            <select name="id_owner" id="id_owner_select" class="form-select rounded-pill px-3" required>
                                                <option value="" selected disabled>Asignar un dueño...</option>
                                                <?php $owners = getAllOwners(); foreach($owners as $owner): ?>
                                                    <option value="<?= $owner['cod'] ?>"><?= $owner['name'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div id="div_new_owner" class="d-none">
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <input type="text" name="new_owner_name" id="new_owner_name" class="form-control rounded-pill px-3" placeholder="Nombre completo">
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="email" name="new_owner_email" id="new_owner_email" class="form-control rounded-pill px-3" placeholder="Correo electrónico">
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <input type="password" name="new_owner_password" id="new_owner_password" class="form-control rounded-pill px-3" placeholder="Contraseña provisoria (Mín. 6 caract.)">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4">
                        <button type="submit" name="btnCreateStore" class="btn btn-primary rounded-pill px-4">Crear Local</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="modal fade" id="manageStoreModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="" method="POST">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold">Gestionar Local</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="store_id" id="edit_store_id">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nombre del Local</label>
                            <input type="text" name="name" id="edit_name" class="form-control rounded-pill" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Ubicación</label>
                            <select name="ubication" id="edit_ubication" class="form-select rounded-pill">
                                <option value="Planta Baja">Planta Baja</option>
                                <option value="Primer Piso">Primer Piso</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Número de Local</label>
                            <input type="text" name="local_number" id="edit_local_number" class="form-control rounded-pill">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" name="btnUpdateStore" class="btn btn-dark rounded-pill px-4">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/../../Components/footer/Footer.php'; ?>

    <script>
        // Lógica de Vista Previa Dinámica
        const inputUrl = document.getElementById('input_url');
        const inputFile = document.getElementById('input_file');
        const preview = document.getElementById('createLogoPreview');

        if(inputUrl) {
            inputUrl.addEventListener('input', () => {
                if(inputUrl.value) preview.innerHTML = `<img src="${inputUrl.value}" style="width:100%; height:100%; object-fit:contain;">`;
                else preview.innerHTML = '<i class="fas fa-image text-muted fa-3x"></i>';
            });
        }

        if(inputFile) {
            inputFile.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => preview.innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:contain;">`;
                    reader.readAsDataURL(file);
                }
            });
        }

        // Lógica de Toggles para Crear/Asignar Dueño
        function toggleOwnerFields() {
            const mode = document.querySelector('input[name="owner_mode"]:checked').value;
            const divExisting = document.getElementById('div_existing_owner');
            const divNew = document.getElementById('div_new_owner');
            const selectExisting = document.getElementById('id_owner_select');
            const inputName = document.getElementById('new_owner_name');
            const inputEmail = document.getElementById('new_owner_email');
            const inputPass = document.getElementById('new_owner_password');

            if (mode === 'existing') {
                divExisting.classList.remove('d-none');
                divNew.classList.add('d-none');
                selectExisting.setAttribute('required', 'required');
                inputName.removeAttribute('required');
                inputEmail.removeAttribute('required');
                inputPass.removeAttribute('required');
            } else {
                divExisting.classList.add('d-none');
                divNew.classList.remove('d-none');
                selectExisting.removeAttribute('required');
                inputName.setAttribute('required', 'required');
                inputEmail.setAttribute('required', 'required');
                inputPass.setAttribute('required', 'required');
            }
        }

        // Abrir modal de gestión
        function openManageModal(id, name, ubication, number) {
            document.getElementById('edit_store_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_ubication').value = ubication;
            document.getElementById('edit_local_number').value = number;
            new bootstrap.Modal(document.getElementById('manageStoreModal')).show();
        }

        // Reabrir modal si hay error de validación
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const openModal = urlParams.get('openModal');
            
            if (openModal === 'addStore') {
                const modal = document.getElementById('addStoreModal');
                if (modal) {
                    // Activar modo "nuevo dueño" si veníamos de crear uno
                    const newOwnerRadio = document.getElementById('owner_mode_new');
                    if (newOwnerRadio) {
                        newOwnerRadio.checked = true;
                        toggleOwnerFields();
                    }
                    new bootstrap.Modal(modal).show();
                    
                    // Limpiar el parámetro de la URL sin recargar
                    const newUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, newUrl);
                }
            }
        });
    </script>
</body>
</html>

<?php
function renderStoreCard($store, $isMine) {
    $logo_db = $store['logo'] ?? '';
    $final_url = filter_var($logo_db, FILTER_VALIDATE_URL) ? $logo_db : "../../../assets/stores/" . ($logo_db ?: 'default_logo.png');
    $brand_color = $store['color'] ?? '#0d6efd';
    $mineClass = $isMine ? 'is-mine-card' : '';
    // Placeholder SVG en base64 (funciona offline)
    $placeholder = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNTAiIGhlaWdodD0iMTUwIiB2aWV3Qm94PSIwIDAgMTUwIDE1MCI+PHJlY3QgZmlsbD0iI2VlZSIgd2lkdGg9IjE1MCIgaGVpZ2h0PSIxNTAiLz48dGV4dCB4PSI1MCUiIHk9IjUwJSIgZG9taW5hbnQtYmFzZWxpbmU9Im1pZGRsZSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0iI2FhYSIgZm9udC1mYW1pbHk9InNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTQiPkxvZ288L3RleHQ+PC9zdmc+";
    ?>
    <article class="store-card-modern shadow-sm <?= $mineClass ?>" style="border-left: 5px solid <?= $brand_color ?>;">
        <div class="store-card-body">
            <div class="store-logo-wrapper" style="background-color: <?= $brand_color ?>10;">
                <img src="<?= $final_url ?>" class="store-img" onerror="this.onerror=null; this.src='<?= $placeholder ?>';">
            </div>
            
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h3 class="h6 fw-bold mb-0"><?= htmlspecialchars($store['name']) ?></h3>
                    <?php if($isMine): ?> <span class="badge rounded-pill bg-warning text-dark" style="font-size: 0.6rem;">MÍO</span> <?php endif; ?>
                </div>
                <div class="store-info-meta small text-muted"><i class="fas fa-map-marker-alt me-1"></i> <?= $store['ubication'] ?></div>
                <div class="store-info-meta small text-muted"><i class="fas fa-door-open me-1"></i> L-<?= $store['local_number'] ?></div>
            </div>
        </div>
        <div class="store-card-footer d-flex gap-2">
            <a href="../Promotions/Promotions.php?store=<?= urlencode($store['name']) ?>" class="btn-modern btn-modern-primary flex-grow-1">Ver Promociones</a>
            <?php if($isMine): ?>
                <button onclick="openManageModal('<?= $store['id'] ?>', '<?= addslashes($store['name']) ?>', '<?= $store['ubication'] ?>', '<?= $store['local_number'] ?>')" class="btn-modern btn-modern-dark">Gestionar</button>
            <?php endif; ?>
        </div>
    </article>
<?php } ?>