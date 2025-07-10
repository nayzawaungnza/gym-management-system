<!-- Dashboards -->
<li class="menu-item {{ $activePage == 'dashboard' ? 'active' : '' }}">
    <a href="{{ route('dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-smart-home"></i>
        <div data-i18n="Dashboard">Dashboard</div>
    </a>
</li>

<!-- Members -->
<li class="menu-item {{ $activePage == 'members' ? 'active' : '' }}">
    <a href="{{ route('members.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-user"></i>
        <div data-i18n="Members">Members</div>
    </a>
</li>

<!-- Trainers -->
<li class="menu-item {{ $activePage == 'trainers' ? 'active' : '' }}">
    <a href="{{ route('trainers.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-user-star"></i>
        <div data-i18n="Trainers">Trainers</div>
    </a>
</li>

<!-- Classes -->
<li class="menu-item {{ $activePage == 'classes' ? 'active' : '' }}">
    <a href="{{ route('classes.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-calendar-event"></i>
        <div data-i18n="Classes">Classes</div>
    </a>
</li>

<!-- Payments -->
<li class="menu-item {{ $activePage == 'payments' ? 'active' : '' }}">
    <a href="{{ route('payments.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-credit-card"></i>
        <div data-i18n="Payments">Payments</div>
    </a>
</li>

<!-- Equipment -->
<li class="menu-item {{ $activePage == 'equipment' ? 'active' : '' }}">
    <a href="{{ route('equipment.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-barbell"></i>
        <div data-i18n="Equipment">Equipment</div>
    </a>
</li>

<!-- Attendance -->
<li class="menu-item {{ $activePage == 'attendance' ? 'active' : '' }}">
    <a href="{{ route('attendance.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-clipboard-check"></i>
        <div data-i18n="Attendance">Attendance</div>
    </a>
</li>
{{-- Roles --}}
<li class="menu-item {{ $activePage == 'roles' ? 'active' : '' }}">
    <a href="{{ route('roles.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-shield-lock"></i>
        <div data-i18n="Roles">Roles</div>
    </a>
</li>

<!-- Activity Logs -->
<li class="menu-item {{ $activePage == 'activitylogs' ? 'active' : '' }}">
    <a href="{{ route('activity_logs.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-activity"></i>
        <div data-i18n="Activity Logs">Activity Logs</div>
    </a>
</li>