<!-- Main Sidebar Container -->
<aside class="main-sidebar bg-lightblue elevation-4">

    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
        <img src="{{asset('icon2.png')}}" 
             alt="AdminLTE Logo" 
             class="brand-image img-circle elevation-3" 
             style="opacity: .8">

        <span class="brand-text font-weight-bold">PT.BSTI</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        @php
            $roleName = Auth::user()->role->name ?? null;
        @endphp

        <!-- Sidebar Menu -->
        <nav class="mt-2 text-capitalize">

            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview"
                role="menu"
                data-accordion="false">

                <li class="nav-header">{{ __("menu") }}</li>

                {{-- ========================================================= --}}
                {{-- DASHBOARD (SEMUA ROLE) --}}
                {{-- ========================================================= --}}
                <li class="nav-item">
                    <a href="{{route('dashboard')}}" class="nav-link text-white">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{ __("dashboard") }}</p>
                    </a>
                </li>



                {{-- ========================================================= --}}
                {{-- MASTER DATA --}}
                {{-- ADMIN + SUPER ADMIN --}}
                {{-- ========================================================= --}}
                @if(in_array($roleName, ['admin','super_admin']))

                <li class="nav-item">
                    <a href="javascript:void(0)" class="nav-link text-white">
                        <i class="nav-icon fas fa-box"></i>

                        <p>
                            {{ __("master of goods") }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a href="{{route('barang.jenis')}}" class="nav-link text-white">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ __("category") }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('barang.satuan')}}" class="nav-link text-white">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ __("unit") }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('barang.merk')}}" class="nav-link text-white">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ __("brand") }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('barang')}}" class="nav-link text-white">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ __("goods") }}</p>
                            </a>
                        </li>

                    </ul>
                </li>



                {{-- Employee --}}
                <li class="nav-item">
                    <a href="{{route('customer')}}" class="nav-link text-white">
                        <i class="nav-icon far fa-user"></i>
                        <p>{{ __("customer") }}</p>
                    </a>
                </li>



                {{-- Supplier --}}
                <li class="nav-item">
                    <a href="{{route('supplier')}}" class="nav-link text-white">
                        <i class="nav-icon fas fa-shipping-fast"></i>
                        <p>{{ __("supplier") }}</p>
                    </a>
                </li>

                @endif



                {{-- ========================================================= --}}
                {{-- TRANSACTION --}}
                {{-- STAFF + ADMIN + SUPER ADMIN --}}
                {{-- ========================================================= --}}
                @if(in_array($roleName, ['staff','admin','super_admin']))

                <li class="nav-item">

                    <a href="javascript:void(0)" class="nav-link text-white">

                        <i class="nav-icon fas fa-exchange-alt"></i>

                        <p>
                            {{ __("transaction") }}
                            <i class="right fas fa-angle-left"></i>
                        </p>

                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a href="{{route('transaksi.masuk')}}" class="nav-link text-white">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ __("incoming transaction") }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('transaksi.keluar')}}" class="nav-link text-white">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ __("outbound transaction") }}</p>
                            </a>
                        </li>

                    </ul>

                </li>

                @endif



                {{-- ========================================================= --}}
                {{-- REPORT --}}
                {{-- SEMUA ROLE --}}
                {{-- ========================================================= --}}
                @if(in_array($roleName, [
                    'staff',
                    'admin',
                    'super_admin',
                    'pimpinan',
                    'sales',
                    'pembelian'
                ]))

                <li class="nav-item">

                    <a href="javascript:void(0)" class="nav-link text-white">

                        <i class="nav-icon fas fa-print"></i>

                        <p>
                            {{ __("report") }}
                            <i class="right fas fa-angle-left"></i>
                        </p>

                    </a>

                    <ul class="nav nav-treeview">

                        <li class="nav-item">
                            <a href="{{route('laporan.masuk')}}" class="nav-link text-white">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ __("incoming goods report") }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('laporan.keluar')}}" class="nav-link text-white">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ __("outgoing goods report") }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('laporan.stok')}}" class="nav-link text-white">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ __("stock report") }}</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{route('laporan.pendapatan')}}" class="nav-link text-white">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ __("income report") }}</p>
                            </a>
                        </li>

                    </ul>

                </li>

                @endif



                <li class="nav-header">{{ __("others") }}</li>



                {{-- ========================================================= --}}
                {{-- SETTING --}}
                {{-- ========================================================= --}}
                <li class="nav-item">

                    <a href="javascript:void(0)" class="nav-link text-white">

                        <i class="nav-icon fas fa-cog"></i>

                        <p>
                            {{ __("setting") }}
                            <i class="right fas fa-angle-left"></i>
                        </p>

                    </a>

                    <ul class="nav nav-treeview">

                        {{-- USERS --}}
                        {{-- PIMPINAN ONLY --}}
                        @if($roleName == 'pimpinan')

                        <li class="nav-item">
                            <a href="{{route('settings.employee')}}" class="nav-link text-white">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ __("users") }}</p>
                            </a>
                        </li>

                        @endif



                        {{-- PROFILE --}}
                        {{-- SEMUA ROLE --}}
                        <li class="nav-item">
                            <a href="{{route('settings.profile')}}" class="nav-link text-white">
                                <i class="fas fa-angle-right"></i>
                                <p>{{ __("profile") }}</p>
                            </a>
                        </li>

                    </ul>

                </li>



                {{-- ========================================================= --}}
                {{-- LOGOUT --}}
                {{-- ========================================================= --}}
                <li class="nav-item">

                    <a href="{{route('login.delete')}}" class="nav-link text-white">

                        <i class="nav-icon fas fa-sign-out-alt"></i>

                        <p>{{ __("messages.logout") }}</p>

                    </a>

                </li>

            </ul>

        </nav>

    </div>

</aside>