<div class="header">


    <div class="header-left">

        <div class="header-left">
            <a href="index.html" class="logo">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo">
            </a>
            <a href="index.html" class="logo logo-small">
                <img src="{{ asset('assets/img/logo-small.png') }}" alt="Logo" width="30" height="30">
            </a>
        </div>

    </div>
    <div class="menu-toggle">
        <a href="javascript:void(0);" id="toggle_btn">
            <i class="fas fa-bars"></i>
        </a>
    </div>
    <div class="top-nav-search">
        <form id="global-search-form" class="d-flex">
            <input type="text" id="global-search-input" class="form-control" placeholder="Search here">
            <button class="btn btn-primary " type="submit">
                {{-- <i class="fas fa-search"></i> --}}
            </button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('global-search-form');
            const input = document.getElementById('global-search-input');

            form.addEventListener('submit', function(e) {
                e.preventDefault(); // prevent page reload

                const query = input.value.trim();

                if (query.length > 0) {
                    //  Redirect to your global search route
                    // Example: /search?query=student
                    window.location.href = '?query=' + encodeURIComponent(query);
                } else {
                    //  Empty search warning
                    Swal.fire({
                        title: 'Empty Search',
                        text: 'Please type something to search.',
                        icon: 'warning',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        });
    </script>

    <a class="mobile_btn" id="mobile_btn">
        <i class="fas fa-bars"></i>
    </a>

    <ul class="nav user-menu">
        <li class="nav-item dropdown noti-dropdown language-drop me-2">
            <a href="#" class="dropdown-toggle nav-link header-nav-list" data-bs-toggle="dropdown">
                <img src="{{ asset('assets/img/icons/header-icon-01.svg') }}" alt="">
            </a>
            <div class="dropdown-menu ">
                <div class="noti-content">
                    <div>
                        <a class="dropdown-item" href="javascript:;"><i class="flag flag-lr me-2"></i>English</a>
                        <a class="dropdown-item" href="javascript:;"><i class="flag flag-bl me-2"></i>Francais</a>
                        <a class="dropdown-item" href="javascript:;"><i class="flag flag-cn me-2"></i>Turkce</a>
                    </div>
                </div>
            </div>
        </li>



        <li class="nav-item zoom-screen me-2">
            <a href="#" class="nav-link header-nav-list win-maximize">
                <img src="{{ asset('assets/img/icons/header-icon-04.svg') }}" alt="">
            </a>
        </li>

        <li class="nav-item dropdown has-arrow new-user-menus">
            <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                <span class="user-img">
                    <img class="rounded-circle" src="{{ asset('assets/img/profiles/avatar-01.jpg') }}" width="31"
                        alt="Soeng Souy">
                    <div class="user-text">
                        <h6>{{ Auth::user()->name }}</h6>
                        <p class="text-muted mb-0">Administrator</p>
                    </div>
                </span>


            </a>
            <div class="dropdown-menu">
                <div class="user-header">
                    <div class="avatar avatar-sm">
                        <img src="{{ asset('assets/img/profiles/avatar-01.jpg') }}" alt="User Image"
                            class="avatar-img rounded-circle">
                    </div>
                    <div class="user-text">
                        <h6>Soeng Souy</h6>
                        <p class="text-muted mb-0">Administrator</p>
                    </div>
                </div>
                <a class="dropdown-item" href="my_account">My Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">Logout</button>
                </form>


            </div>
        </li>

    </ul>
    `
</div>