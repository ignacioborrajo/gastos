<?php ?>

<div id="m_ver_menu" class="m-aside-menu  m-aside-menu--skin-dark m-aside-menu--submenu-skin-dark " data-menu-vertical="true" data-menu-scrollable="false" data-menu-dropdown-timeout="500">
    <ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow ">
        <!--
        <li class="m-menu__item  m-menu__item--submenu" aria-haspopup="true"  data-menu-submenu-toggle="hover">
            <a  href="cabeceras-y-seo.php" class="m-menu__link m-menu__toggle">
                <i class="m-menu__link-icon flaticon-layers"></i>
                <span class="m-menu__link-text">
                    Cabeceras y SEO
                </span>
            </a>
        </li>
        -->

        <li class="m-menu__item  m-menu__item--submenu <?= (strpos($_SERVER['PHP_SELF'], 'dashboard') !== false) ?>" aria-haspopup="true" data-menu-submenu-toggle="hover">
            <a href="dashboard.php" class="m-menu__link m-menu__toggle">
                <i class="m-menu__link-icon flaticon-layers"></i>
                <span class="m-menu__link-text">
                    Resumen
                </span>
            </a>
        </li>

        <li class="m-menu__item  m-menu__item--submenu <?= (strpos($_SERVER['PHP_SELF'], 'bancos') !== false) ?>" aria-haspopup="true" data-menu-submenu-toggle="hover">
            <a href="bancos.php" class="m-menu__link m-menu__toggle">
                <i class="m-menu__link-icon flaticon-piggy-bank"></i>
                <span class="m-menu__link-text">
                    Cuentas Bancarias
                </span>
            </a>
        </li>

        <li class="m-menu__section">
            <h4 class="m-menu__section-text">
                GASTOS
            </h4>
            <i class="m-menu__section-icon flaticon-more-v3"></i>
        </li>
        <li class="m-menu__item  m-menu__item--submenu <?= (strpos($_SERVER['PHP_SELF'], 'resumen_gastos_comunes') !== false || strpos($_SERVER['PHP_SELF'], '/todos_gastos.php') !== false  ? 'm-menu__item--open m-menu__item--expanded' : '') ?>" aria-haspopup="true" data-menu-submenu-toggle="hover">
            <a href="#" class="m-menu__link m-menu__toggle">
                <i class="m-menu__link-icon flaticon-users"></i>
                <span class="m-menu__link-text">
                    Gastos Comunes
                </span>
                <i class="m-menu__ver-arrow la la-angle-right"></i>
            </a>
            <div class="m-menu__submenu">
                <span class="m-menu__arrow"></span>
                <ul class="m-menu__subnav">
                    <li class="m-menu__item  m-menu__item--parent" aria-haspopup="true">
                        <a href="#" class="m-menu__link ">
                            <span class="m-menu__link-text">
                                Gastos Comunes
                            </span>
                        </a>
                    </li>
                    <li class="m-menu__item <?= (strpos($_SERVER['PHP_SELF'], 'resumen_gastos_comunes') !== false ? 'm-menu__item--active' : '') ?>" aria-haspopup="true">
                        <a href="resumen_gastos_comunes.php" class="m-menu__link ">
                            <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                <span></span>
                            </i>
                            <span class="m-menu__link-text">
                                Resumen
                            </span>
                        </a>
                    </li>
                    <li class="m-menu__item <?= (strpos($_SERVER['PHP_SELF'], 'viajes_comunes') !== false ? 'm-menu__item--active' : '') ?>" aria-haspopup="true">
                        <a href="viajes_comunes.php" class="m-menu__link ">
                            <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                <span></span>
                            </i>
                            <span class="m-menu__link-text">
                                Viajes
                            </span>
                        </a>
                    </li>
                    <li class="m-menu__item <?= (strpos($_SERVER['PHP_SELF'], '/todos_gastos.php') !== false ? 'm-menu__item--active' : '') ?>" aria-haspopup="true">
                        <a href="todos_gastos.php" class="m-menu__link ">
                            <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                <span></span>
                            </i>
                            <span class="m-menu__link-text">
                                Todos los gastos
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="m-menu__item  m-menu__item--submenu <?= (strpos($_SERVER['PHP_SELF'], '/gastos.php') !== false || strpos($_SERVER['PHP_SELF'], 'todos_gastos_privados') !== false  ? 'm-menu__item--open m-menu__item--expanded' : '') ?>" aria-haspopup="true" data-menu-submenu-toggle="hover">
            <a href="#" class="m-menu__link m-menu__toggle">
                <i class="m-menu__link-icon flaticon-user"></i>
                <span class="m-menu__link-text">
                    Gastos Privados
                </span>
                <i class="m-menu__ver-arrow la la-angle-right"></i>
            </a>
            <div class="m-menu__submenu">
                <span class="m-menu__arrow"></span>
                <ul class="m-menu__subnav">
                    <li class="m-menu__item  m-menu__item--parent" aria-haspopup="true">
                        <a href="#" class="m-menu__link ">
                            <span class="m-menu__link-text">
                                Gastos Privados
                            </span>
                        </a>
                    </li>
                    <li class="m-menu__item <?= (strpos($_SERVER['PHP_SELF'], '/gastos.php') !== false ? 'm-menu__item--active' : '') ?>" aria-haspopup="true">
                        <a href="gastos.php" class="m-menu__link ">
                            <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                <span></span>
                            </i>
                            <span class="m-menu__link-text">
                                Gastos Privados
                            </span>
                        </a>
                    </li>
                    <li class="m-menu__item <?= (strpos($_SERVER['PHP_SELF'], 'todos_gastos_privados') !== false ? 'm-menu__item--active' : '') ?>" aria-haspopup="true">
                        <a href="todos_gastos_privados.php" class="m-menu__link ">
                            <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                <span></span>
                            </i>
                            <span class="m-menu__link-text">
                                Todos los Gastos
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="m-menu__section">
            <h4 class="m-menu__section-text">
                INGRESOS
            </h4>
            <i class="m-menu__section-icon flaticon-more-v3"></i>
        </li>
        <li class="m-menu__item  m-menu__item--submenu <?= (strpos($_SERVER['PHP_SELF'], 'ingresos.php') !== false || strpos($_SERVER['PHP_SELF'], 'todos_ingresos') !== false  ? 'm-menu__item--open m-menu__item--expanded' : '') ?>" aria-haspopup="true" data-menu-submenu-toggle="hover">
            <a href="#" class="m-menu__link m-menu__toggle">
                <i class="m-menu__link-icon flaticon-user"></i>
                <span class="m-menu__link-text">
                    Ingresos
                </span>
                <i class="m-menu__ver-arrow la la-angle-right"></i>
            </a>
            <div class="m-menu__submenu">
                <span class="m-menu__arrow"></span>
                <ul class="m-menu__subnav">
                    <li class="m-menu__item  m-menu__item--parent" aria-haspopup="true">
                        <a href="#" class="m-menu__link ">
                            <span class="m-menu__link-text">
                                Ingresos
                            </span>
                        </a>
                    </li>
                    <li class="m-menu__item <?= (strpos($_SERVER['PHP_SELF'], 'ingresos.php') !== false ? 'm-menu__item--active' : '') ?>" aria-haspopup="true">
                        <a href="ingresos.php" class="m-menu__link ">
                            <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                <span></span>
                            </i>
                            <span class="m-menu__link-text">
                                Ingresos
                            </span>
                        </a>
                    </li>
                    <li class="m-menu__item <?= (strpos($_SERVER['PHP_SELF'], 'todos_ingresos') !== false ? 'm-menu__item--active' : '') ?>" aria-haspopup="true">
                        <a href="todos_ingresos.php" class="m-menu__link ">
                            <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                <span></span>
                            </i>
                            <span class="m-menu__link-text">
                                Todos los Ingresos
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        <li class="m-menu__section">
            <h4 class="m-menu__section-text">
                IMPORTAR
            </h4>
            <i class="m-menu__section-icon flaticon-more-v3"></i>
        </li>
        <li class="m-menu__item  m-menu__item--submenu <?= (strpos($_SERVER['PHP_SELF'], 'importar.php') !== false || strpos($_SERVER['PHP_SELF'], 'desimportar.php') !== false  ? 'm-menu__item--open m-menu__item--expanded' : '') ?>" aria-haspopup="true" data-menu-submenu-toggle="hover">
            <a href="#" class="m-menu__link m-menu__toggle">
                <i class="m-menu__link-icon flaticon-folder-4"></i>
                <span class="m-menu__link-text">
                    Importar
                </span>
                <i class="m-menu__ver-arrow la la-angle-right"></i>
            </a>
            <div class="m-menu__submenu">
                <span class="m-menu__arrow"></span>
                <ul class="m-menu__subnav">
                    <li class="m-menu__item  m-menu__item--parent" aria-haspopup="true">
                        <a href="#" class="m-menu__link ">
                            <span class="m-menu__link-text">
                                Importar
                            </span>
                        </a>
                    </li>
                    <li class="m-menu__item <?= (strpos($_SERVER['PHP_SELF'], 'importar.php') !== false ? 'm-menu__item--active' : '') ?>" aria-haspopup="true">
                        <a href="importar.php" class="m-menu__link ">
                            <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                <span></span>
                            </i>
                            <span class="m-menu__link-text">
                                Importar
                            </span>
                        </a>
                    </li>
                    <li class="m-menu__item <?= (strpos($_SERVER['PHP_SELF'], 'desimportar.php') !== false ? 'm-menu__item--active' : '') ?>" aria-haspopup="true">
                        <a href="desimportar.php" class="m-menu__link ">
                            <i class="m-menu__link-bullet m-menu__link-bullet--dot">
                                <span></span>
                            </i>
                            <span class="m-menu__link-text">
                                Histórico Importaciones
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</div>