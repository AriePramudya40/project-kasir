import axios from 'axios';
import Swal from 'sweetalert2';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Swal = Swal;

// --- LOGIKA ALERT OTOMATIS (GLOBAL) ---
document.addEventListener('DOMContentLoaded', () => {
    const flashData = document.getElementById('flash-data');
    
    if (flashData) {
        // 1. Cek Pesan Login Berhasil (Welcome)
        const loginSuccess = flashData.getAttribute('data-login-success');
        if (loginSuccess) {
            Swal.fire({
                title: 'Login Berhasil!',
                text: loginSuccess,
                icon: 'success',
                timer: 3000,
                showConfirmButton: false,
                backdrop: `rgba(0,0,0,0.4)`
            });
        }

        // 2. Cek Pesan Sukses Biasa
        const success = flashData.getAttribute('data-success');
        if (success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: success,
                showConfirmButton: false,
                timer: 2000
            });
        }

        // 3. Cek Pesan Error
        const error = flashData.getAttribute('data-error');
        if (error) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: error,
                confirmButtonColor: '#9A1B1F'
            });
        }
    }
});