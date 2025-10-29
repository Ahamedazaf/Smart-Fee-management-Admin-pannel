<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>@yield('title', 'Home Page')</title>
    <link rel="shortcut icon" href="assets/img/favicon.png">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.inc_top')

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/icons/flags/flags.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <style>
        .drag-handle {
            cursor: grab;
        }
    </style>
</head>

<body>
    <div class="page-content">
        <div class="content-wrapper">
            <!-- jQuery first -->
            <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>

            <div class="main-wrapper">

























                <!-- Header & Sidebar -->
                @include('partials.header')
                @include('partials.menu')

                <div class="page-wrapper">
                    <!-- Main Content -->
                    @yield('content')

                    <!-- Session Alerts -->
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                        @if (session('success'))
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: "{{ session('success') }}",
                                timer: 3000,
                                showConfirmButton: false
                            });
                        @endif
                        @if (session('error'))
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: "{{ session('error') }}",
                                timer: 3000,
                                showConfirmButton: false
                            });
                        @endif
                        @if (session('warning'))
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: "{{ session('warning') }}",
                                timer: 3000,
                                showConfirmButton: false
                            });
                        @endif
                        @if (session('info'))
                            Swal.fire({
                                icon: 'info',
                                title: 'Info',
                                text: "{{ session('info') }}",
                                timer: 3000,
                                showConfirmButton: false
                            });
                        @endif
                    </script>

                    <!-- Footer -->
                    @include('partials.inc_bottom')

                    <!-- Stack for page-specific scripts -->
                    @stack('scripts')


                </div>
            </div>

            <!-- JS Scripts (Proper Order) -->
            <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
            <script src="{{ asset('assets/js/feather.min.js') }}"></script>
            <script src="{{ asset('assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
            <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
            <script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>
            <script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
            <script src="{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
            <script src="{{ asset('assets/plugins/apexchart/chart-data.js') }}"></script>
             <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script> 
            <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
            <script src="{{ asset('assets/js/script.js') }}"></script>

            <!-- Custom DataTable Initialization -->
            <script>
                $(document).ready(function() {
                    if ($.fn.DataTable.isDataTable('.datatable')) {
                        $('.datatable').DataTable().destroy();
                    }
                    var table = $('.datatable').DataTable({
                        ordering: true,
                        pageLength: 10
                    });
                    $('#customSearchBox').on('keyup', function() {
                        table.search(this.value).draw();
                    });
                });
            </script>

        </div>
    </div>
</body>

</html>
