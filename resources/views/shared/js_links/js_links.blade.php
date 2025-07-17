<!-- jQuery -->
<script src="{{ asset('public/template/jquery/js/jquery.min.js') }}"></script>
{{-- <script src="{{ asset('public/template/jquery/js/jquery.min.js') }}"></script> --}} <!-- For deployment -->

<!-- Bootstrap 5 -->
<script src="{{ asset('public/template/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('public/template/bootstrap/js/popper.min.js') }}"></script>
{{-- <script src="{{ asset('public/template/bootstrap/js/bootstrap.min.js') }}"></script> --}} <!-- For deployment -->
{{-- <script src="{{ asset('public/template/bootstrap/js/popper.min.js') }}"></script> --}} <!-- For deployment -->

<!-- AdminLTE -->
<script src="{{ asset('public/template/adminlte/js/adminlte.min.js') }}"></script>
{{-- <script src="{{ asset('public/template/adminlte/js/adminlte.min.js') }}"></script>--}} <!-- For deployment -->

<!-- DataTables -->
<script src="{{ asset('public/template/datatables/js/datatables.min.js') }}"></script>
{{-- <script src="{{ asset('/template/datatables/js/dataTables.bootstrap5.min.js') }}"></script> --}}
{{-- <script src="{{ asset('public/template/datatables/js/datatables.min.js') }}"></script>--}} <!-- For deployment -->
{{-- <script src="{{ asset('public/template/datatables/js/dataTables.bootstrap5.min.js') }}"></script> --}} <!-- For deployment -->

<!-- Select2 -->
<script src="{{ asset('public/template/select2/js/select2.min.js') }}"></script>
{{-- <script src="{{ asset('public/template/select2/js/select2.min.js') }}"></script>--}} <!-- For deployment -->

<!-- Toastr -->
<script src="{{ asset('public/template/toastr/js/toastr.min.js') }}"></script>
{{-- <script src="{{ asset('public/template/toastr/js/toastr.min.js') }}"></script>--}} <!-- For deployment -->

<!-- Lightbox -->
<script src="{{ asset('public/template/lightbox/bs5-lightbox.js') }}"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/bs5-lightbox@1.8.1/dist/index.bundle.min.js"></script> --}}

<!-- MommentJS -->
<script src="{{ asset('public/template/momentjs/moment.min.js') }}"></script>

<!-- Bootstrap Datepicker -->
<script src="{{ asset('public/template/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

<!-- Bootstrap Datetimepicker -->
<script src="{{ asset('public/template/datetimepicker/js/datetimepicker.js') }}"></script>

<!-- Custom JS -->
<script>
    // import Lightbox from 'bs5-lightbox';

    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "3000",
        "timeOut": "3000",
        "extendedTimeOut": "3000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "iconClass":  "toast-custom"
    };
</script>

<script src="{{ asset('public/js/main/Common.js') }}"></script>
<script src="{{ asset('public/js/main/User.js') }}"></script>
<script src="{{ asset('public/js/main/Routes.js') }}"></script>
<script src="{{ asset('public/js/main/ShuttleProvider.js') }}"></script>
<script src="{{ asset('public/js/main/PickupTime.js') }}"></script>
<script src="{{ asset('public/js/main/SystemOneHRIS.js') }}"></script>
<script src="{{ asset('public/js/main/Masterlist.js') }}"></script>
<script src="{{ asset('public/js/main/CutoffTime.js') }}"></script>