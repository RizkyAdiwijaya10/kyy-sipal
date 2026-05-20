// public/assets/js/custom.js

// ========================================
// SWEETALERT CONFIGURATION
// ========================================

// Konfigurasi SweetAlert dengan Bootstrap buttons
const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: "btn btn-danger ms-2",
        cancelButton: "btn btn-primary"
    },
    buttonsStyling: false
});

// ========================================
// DELETE CONFIRMATION FUNCTION
// ========================================

/**
 * Konfirmasi hapus data
 * @param {string} formId - ID dari form yang akan di-submit
 * @param {string} customText - Teks konfirmasi kustom (opsional)
 */
function confirmDelete(formId, customText = 'Data ini akan dihapus permanen!') {
    swalWithBootstrapButtons.fire({
        title: "Apakah Anda yakin?",
        text: customText,
        icon: "warning",
        showCancelButton: true,
        cancelButtonText: "Batal",
        confirmButtonText: "Hapus",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            document.getElementById(formId).submit();
        }
    });
}

// ========================================
// LOGOUT CONFIRMATION
// ========================================

/**
 * Konfirmasi logout
 */
function confirmLogout() {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda akan keluar dari sistem!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Keluar!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Sedang keluar...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            document.getElementById('logout-form').submit();
        }
    });
}

// ========================================
// FORM SUBMIT LOADING SPINNER
// ========================================

/**
 * Menampilkan loading spinner saat form di-submit
 */
function showFormLoadingSpinner() {
    Swal.fire({
        title: 'Memproses...',
        html: `
            <div style="display:flex; flex-direction:column; align-items:center; gap:15px;">
                <div class="dot-spinner">
                    <div class="dot-spinner__dot"></div>
                    <div class="dot-spinner__dot"></div>
                    <div class="dot-spinner__dot"></div>
                    <div class="dot-spinner__dot"></div>
                    <div class="dot-spinner__dot"></div>
                    <div class="dot-spinner__dot"></div>
                    <div class="dot-spinner__dot"></div>
                    <div class="dot-spinner__dot"></div>
                </div>
                <span>Mohon tunggu...</span>
            </div>
        `,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
    });
}

// ========================================
// DOT SPINNER CSS (Inject ke head jika belum ada)
// ========================================

function injectDotSpinnerStyles() {
    if (!document.querySelector('#dot-spinner-styles')) {
        const style = document.createElement('style');
        style.id = 'dot-spinner-styles';
        style.textContent = `
            .dot-spinner {
                --uib-size: 40px;
                --uib-speed: 0.9s;
                --uib-color: #4e73df;
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
                height: var(--uib-size);
                width: var(--uib-size);
            }
            .dot-spinner__dot {
                position: absolute;
                top: 0;
                left: 0;
                display: flex;
                align-items: center;
                justify-content: flex-start;
                height: 100%;
                width: 100%;
            }
            .dot-spinner__dot::before {
                content: '';
                height: 20%;
                width: 20%;
                border-radius: 50%;
                background-color: var(--uib-color);
                transform: scale(0);
                opacity: 0.5;
                animation: pulse calc(var(--uib-speed) * 1.111) ease-in-out infinite;
            }
            .dot-spinner__dot:nth-child(2) { transform: rotate(45deg); }
            .dot-spinner__dot:nth-child(2)::before { animation-delay: calc(var(--uib-speed) * -0.875); }
            .dot-spinner__dot:nth-child(3) { transform: rotate(90deg); }
            .dot-spinner__dot:nth-child(3)::before { animation-delay: calc(var(--uib-speed) * -0.75); }
            .dot-spinner__dot:nth-child(4) { transform: rotate(135deg); }
            .dot-spinner__dot:nth-child(4)::before { animation-delay: calc(var(--uib-speed) * -0.625); }
            .dot-spinner__dot:nth-child(5) { transform: rotate(180deg); }
            .dot-spinner__dot:nth-child(5)::before { animation-delay: calc(var(--uib-speed) * -0.5); }
            .dot-spinner__dot:nth-child(6) { transform: rotate(225deg); }
            .dot-spinner__dot:nth-child(6)::before { animation-delay: calc(var(--uib-speed) * -0.375); }
            .dot-spinner__dot:nth-child(7) { transform: rotate(270deg); }
            .dot-spinner__dot:nth-child(7)::before { animation-delay: calc(var(--uib-speed) * -0.25); }
            .dot-spinner__dot:nth-child(8) { transform: rotate(315deg); }
            .dot-spinner__dot:nth-child(8)::before { animation-delay: calc(var(--uib-speed) * -0.125); }
            @keyframes pulse {
                0%, 100% { transform: scale(0); opacity: 0.5; }
                50% { transform: scale(1); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }
}

// ========================================
// INITIALIZE ALL
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    // Inject dot spinner styles
    injectDotSpinnerStyles();
    
    // Inisialisasi tombol logout
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', confirmLogout);
    }
    
    // Form submit loading spinner (skip delete forms)
    document.querySelectorAll('form').forEach(form => {
        // Skip delete forms
        if (form.id && form.id.startsWith('delete-')) return;
        
        // Skip logout form
        if (form.id === 'logout-form') return;
        
        form.addEventListener('submit', function() {
            showFormLoadingSpinner();
        });
    });
});