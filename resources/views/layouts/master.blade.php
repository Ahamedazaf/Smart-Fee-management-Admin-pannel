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
            {{-- <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
            <script src="{{ asset('assets/plugins/datatables/datatables.min.js') }}"></script> --}}
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





            <style>
                /* =========================================================
   🌈 DataTable Export Buttons - Modern Gradient Style
   Author: Rootmaster
   Version: Final v3.0
   Compatible with: Bootstrap 5 + DataTables 1.13+
   ========================================================= */

                /* ----- Layout Wrapper ----- */
                .dt-buttons {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 10px;
                    margin-bottom: 12px;
                    align-items: center;

                    justify-content: center !important;
                    align-items: center !important;
                }

                /* ----- Base Button Style ----- */
                .dt-button {
                    border: none !important;
                    border-radius: 8px !important;
                    padding: 8px 16px !important;
                    font-size: 0.92rem !important;
                    font-weight: 600 !important;
                    color: #fff !important;
                    text-transform: capitalize;
                    letter-spacing: 0.3px;
                    transition: all 0.3s ease;
                    display: inline-flex !important;
                    align-items: center;
                    justify-content: center;
                    gap: 8px;
                    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
                    cursor: pointer;
                }

                /* ----- Gradient Themes ----- */
                .dt-button.buttons-copy {
                    background: linear-gradient(45deg, #007bff, #00c6ff) !important;
                }

                .dt-button.buttons-excel {
                    background: linear-gradient(45deg, #28a745, #66bb6a) !important;
                }

                .dt-button.buttons-csv {
                    background: linear-gradient(45deg, #17a2b8, #00bcd4) !important;
                }

                .dt-button.buttons-pdf {
                    background: linear-gradient(45deg, #dc3545, #ff4b5c) !important;
                }

                .dt-button.buttons-print {
                    background: linear-gradient(45deg, #6c757d, #9ea7ad) !important;
                }

                /* ----- Hover Animation ----- */
                .dt-button:hover {
                    transform: translateY(-3px);
                    filter: brightness(1.1);
                    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2);
                    opacity: 0.95;
                }

                /* ----- Active State (Click Press) ----- */
                .dt-button:active {
                    transform: translateY(1px);
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
                }

                /* ----- Focus Outline Disable ----- */
                .dt-button:focus {
                    outline: none !important;
                    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
                }

                /* ----- Dark Mode Friendly (auto-detect) ----- */
                @media (prefers-color-scheme: dark) {
                    .dt-button {
                        box-shadow: 0 3px 8px rgba(255, 255, 255, 0.1);
                    }

                    .dt-button:hover {
                        box-shadow: 0 6px 14px rgba(255, 255, 255, 0.15);
                    }
                }

                /* ----- Mobile Responsive ----- */
                @media (max-width: 576px) {
                    .dt-buttons {
                        flex-direction: column;
                        align-items: stretch;
                        gap: 8px;
                    }

                    .dt-button {
                        width: 100%;
                        justify-content: center;
                        padding: 10px !important;
                    }
                }










                /* =============== DataTable Filter =============== */

                /* Wrap alignment */
                .dataTables_filter {
                    display: flex !important;
                    flex-wrap: wrap !important;
                    align-items: center !important;
                    justify-content: flex-end !important;
                    gap: 6px !important;
                    margin-bottom: 10px !important;
                    width: 100% !important;
                }

                /* Label text (Filter:) */
                .dataTables_filter label {
                    display: flex !important;
                    align-items: center !important;
                    gap: 8px !important;
                    width: 100% !important;
                }

                /* Search input styling */
                .dataTables_filter input[type="search"] {
                    flex: 1 1 300px !important;
                    max-width: 300px !important;
                    border-radius: 8px !important;
                    border: 1px solid #ccc !important;
                    padding: 8px 12px !important;
                    font-size: 0.9rem !important;
                    transition: all 0.2s ease-in-out;
                }

                /* Hover and focus animation */
                .dataTables_filter input[type="search"]:focus {
                    border-color: #007bff !important;
                    box-shadow: 0 0 5px rgba(0, 123, 255, 0.3) !important;
                }

                /* ====== Mobile Responsive ====== */
                @media (max-width: 768px) {
                    .dataTables_filter {
                        flex-direction: column !important;
                        align-items: stretch !important;
                        justify-content: center !important;
                    }

                    .dataTables_filter label {
                        width: 100% !important;
                        justify-content: center !important;
                    }

                    .dataTables_filter input[type="search"] {
                        width: 100% !important;
                        max-width: 100% !important;
                    }
                }
            </style>
        </div>
    </div>
</body>

</html>
