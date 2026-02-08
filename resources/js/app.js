import './bootstrap'; // Ini akan load axios + csrf setup
import 'bootstrap-icons/font/bootstrap-icons.css';

// Core JS Libraries
import $ from 'jquery';
window.$ = window.jQuery = $;

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import Swal from 'sweetalert2';
window.Swal = Swal;

$(function() {
    console.log('✅ App Loaded Successfully');
    console.log('✅ Axios available:', typeof axios !== 'undefined');
    console.log('✅ jQuery available:', typeof $ !== 'undefined');
    console.log('✅ Swal available:', typeof Swal !== 'undefined');
});