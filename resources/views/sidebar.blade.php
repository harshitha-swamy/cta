<div class="sidebar-container">
                        <!-- <div class="eshop-section">
                            @php
                                $connection = session('db_connection');
                                $eshopName = $connection === 'mysql2' ? 'E-Shop US' : 'E-Shop Canada';
                            @endphp

                            <span class="form-control-plaintext country-select">
                                {{ $eshopName }}
                            </span>
                        </div> -->
                        <div class="sidebar-menu">
                            <ul>
                                <li class="{{ request()->routeIs('dashboard') ? 'active-menu-item' : '' }}">
                                    <a href="{{ route('dashboard') }}">
                                        <span>Dashboard</span>
                                    </a>
                                </li>

                                <li class="{{ request()->routeIs('task.create*') ? 'active-menu-item' : '' }}">
                                    <a href="{{ route('task.create') }}">
                                        <span>Task Section</span>
                                    </a>
                                </li>

                                <li class="{{ request()->routeIs('catalogue*') ? 'active-menu-item' : '' }}">
                                    <a href="{{ route('catalogue') }}">
                                        <span>CTA Catalogue</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('logout') }}">
                                        <span><i class="bi bi-box-arrow-right"></i>Logout</span>
                                    </a>
                                </li>
                            </ul>

                        </div>
                    </div>


                    