<?php
    $user = $_SESSION['user'] ?? null;
?>

<html>
    <link rel="stylesheet" href="../../Components/navbar/navbar.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <nav class="navbar">
        <div class="navbar-container">
            <!-- Logo -->
            <a href="#" class="navbar-logo">
                <i class="fas fa-shopping-center logo-icon"></i>
                <span class="logo-text">Shopping Rosario</span>
            </a>

            <!-- Menu Principal -->
            <ul class="navbar-menu">
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="fas fa-home"></i>
                        <span>Inicio</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="fas fa-store"></i>
                        <span>Tiendas</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="fas fa-tags"></i>
                        <span>Ofertas</span>
                    </a>
                </li>
                <?php if (!$user): ?>
                <li class="menu-item">
                    <a href="../../Pages/Login/login.php" class="menu-link highlight">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Ingresar</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>

            
                <!-- Sección Usuario (mostrar solo si está logueado) -->
                <div class="user-section" style="display: none;">
                    <div class="user-dropdown">
                        <a href="#" class="user-trigger">
                            <div class="user-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="user-info">
                                <span class="user-name">Nombre Usuario</span>
                                <span class="user-role">Rol Usuario</span>
                            </div>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <!-- Dropdown Menu (agregar clase 'show' para mostrar) -->
                        <div class="user-menu">
                            <a href="#" class="user-menu-item">
                                <i class="fas fa-user-edit"></i>
                                <span>Mi Perfil</span>
                            </a>
                            <a href="#" class="user-menu-item">
                                <i class="fas fa-cog"></i>
                                <span>Configuración</span>
                            </a>
                            <div class="user-menu-divider"></div>
                            <a href="#" class="user-menu-item logout">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Cerrar Sesión</span>
                            </a>
                        </div>
                    </div>
                </div>
            

            <!-- Toggle Menú Móvil -->
            <button class="mobile-toggle" type="button">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>
        </div>

        <!-- Menú Móvil (agregar clase 'show' para mostrar) -->
        <div class="mobile-menu">
            <div class="mobile-menu-header">
                <!-- Info usuario en móvil (mostrar solo si está logueado) -->
                <div class="mobile-user-info" style="display: none;">
                    <div class="mobile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="mobile-user-details">
                        <span class="mobile-user-name">Nombre Usuario</span>
                        <span class="mobile-user-role">Rol Usuario</span>
                    </div>
                </div>
            </div>
            <ul class="mobile-menu-items">
                <li>
                    <a href="#" class="mobile-menu-link">
                        <i class="fas fa-home"></i>
                        <span>Inicio</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="mobile-menu-link">
                        <i class="fas fa-store"></i>
                        <span>Tiendas</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="mobile-menu-link">
                        <i class="fas fa-tags"></i>
                        <span>Ofertas</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="mobile-menu-link highlight">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Ingresar</span>
                    </a>
                </li>
                
                <!-- Items adicionales para usuarios logueados (mostrar solo si está logueado) -->
                <li class="mobile-divider" style="display: none;"></li>
                <li style="display: none;">
                    <a href="#" class="mobile-menu-link">
                        <i class="fas fa-user-edit"></i>
                        <span>Mi Perfil</span>
                    </a>
                </li>
                <li style="display: none;">
                    <a href="#" class="mobile-menu-link">
                        <i class="fas fa-cog"></i>
                        <span>Configuración</span>
                    </a>
                </li>
                <li style="display: none;">
                    <a href="#" class="mobile-menu-link logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Cerrar Sesión</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Overlay para cerrar menú móvil (agregar clase 'show' para mostrar) -->
        <div class="mobile-overlay"></div>
    </nav>
</html>
