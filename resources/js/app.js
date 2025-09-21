import './bootstrap';
import $ from 'jquery';
import 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';

// Jika ingin global
window.$ = window.jQuery = $;

$(document).ready(function() {
    $('#customersTable').DataTable({
        responsive: true,
        autoWidth: false,
        columnDefs: [
            { orderable: false, targets: 5 }
        ]
    });
});

