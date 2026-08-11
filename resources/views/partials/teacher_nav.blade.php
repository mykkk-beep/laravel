        <div class="collapse navbar-collapse" id="teacherNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item d-flex align-items-center">
                    <a class="nav-link me-2" href="{{ url('/teacher') }}">Dashboard</a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Quick Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ url('/teacher/classes/create') }}">Create Class</a></li>
                            <li><a class="dropdown-item" href="{{ url('/teacher/attendance/create') }}">Take Attendance</a></li>
                            <li><a class="dropdown-item" href="{{ url('/teacher/attendance/scan') }}">Scan QR</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/teacher/classes') }}">Classes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/teacher/students') }}">Students</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/teacher/records') }}">Records</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/teacher/recommendations') }}">Recommendation</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-1" href="{{ url('/teacher') }}">
                        Messages
                        @if(($pendingParentMessageCount ?? 0) > 0)
                            <span class="badge rounded-pill bg-danger">{{ $pendingParentMessageCount }}</span>
                        @endif
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/teacher/settings') }}">Settings</a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
