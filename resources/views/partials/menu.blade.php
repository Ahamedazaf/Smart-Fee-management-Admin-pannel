<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">

        <!-- Sidebar Menu -->
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <!-- Dashboard -->
                <li class="{{ Route::is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Payments -->
                @if(Qs::userIsAdministrative())
                <li class="submenu {{ in_array(Route::currentRouteName(), [
                        'payments.index','payments.create','payments.edit',
                        'payments.manage','payments.show','payments.invoice','payments.receipts'
                    ]) ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa-solid fa-credit-card"></i>
                        <span>Payments</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        @if(Qs::userIsTeamAccount())
                        <li>
                            <a href="{{ route('payments.create') }}"
                                class="{{ Route::is('payments.create') ? 'active' : '' }}">
                                <i class="fa-solid fa-user-plus me-1"></i> Create Payment
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('payments.index') }}"
                                class="{{ in_array(Route::currentRouteName(), ['payments.index','payments.edit','payments.show']) ? 'active' : '' }}">
                                <i class="fa-solid fa-clipboard-list me-1"></i> Manage Payments
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('payments.manage') }}"
                                class="{{ in_array(Route::currentRouteName(), ['payments.manage','payments.invoice','payments.receipts']) ? 'active' : '' }}">
                                <i class="fa-solid fa-file-invoice-dollar me-1"></i> Student Payments
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                <!-- Students -->
                @if(Qs::userIsTeamSAT())
                <li class="submenu {{ in_array(Route::currentRouteName(), [
                        'students.create','students.studentinfo','students.list',
                        'students.edit','students.show','students.promotion',
                        'students.promotion_manage','students.graduated'
                    ]) ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa-solid fa-user-graduate"></i>
                        <span>Students</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul>
                        @if(Qs::userIsTeamSA())
                        <li>
                            <a href="{{ route('students.create') }}"
                                class="{{ Route::is('students.create') ? 'active' : '' }}">
                                <i class="fa-solid fa-user-plus me-1"></i> Add Student
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('students.studentinfo') }}"
                                class="{{ Route::is('students.studentinfo') ? 'active' : '' }}">
                                <i class="fa-solid fa-id-card me-1"></i> Student Information
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                <!-- Fees Summary -->
                <li class="{{ Route::is('payments.summary') ? 'active' : '' }}">
                    <a href="{{ route('payments.summary') }}">
                        <i class="fa-solid fa-money-bill-wave"></i>
                        <span>Fees Summary</span>
                    </a>
                </li>

                <!-- Classes -->
                @if(Qs::userIsTeamSA())
                <li class="{{ in_array(Route::currentRouteName(), ['classes.index','classes.edit']) ? 'active' : '' }}">
                    <a href="{{ route('classes.index') }}">
                        <i class="fa-solid fa-chalkboard-user"></i>
                        <span>Classes</span>
                    </a>
                </li>
                @endif

                <!-- User-Specific Menu -->
                @include('pages.' . Qs::getUserType() . '.menu')

                <!-- My Account -->
                <li class="{{ Route::is('my_account') ? 'active' : '' }}">
                    <a href="{{ route('my_account') }}">
                        <i class="fa-solid fa-user"></i>
                        <span>My Account</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- /Sidebar Menu -->
    </div>
</div>