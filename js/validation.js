/**
 * JavaScript Validation
 * Sistem Informasi Klinik - Milestone 1
 */

// Form validation untuk register
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');

    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const username = document.getElementById('username').value;

            let errors = [];

            // Validasi username
            if (username.length < 4) {
                errors.push('Username minimal 4 karakter');
            }

            if (!/^[a-zA-Z0-9_]+$/.test(username)) {
                errors.push('Username hanya boleh berisi huruf, angka, dan underscore');
            }

            // Validasi password
            if (password.length < 8) {
                errors.push('Password minimal 8 karakter');
            }

            if (!/[A-Z]/.test(password)) {
                errors.push('Password harus mengandung minimal 1 huruf besar');
            }

            if (!/[a-z]/.test(password)) {
                errors.push('Password harus mengandung minimal 1 huruf kecil');
            }

            if (!/[0-9]/.test(password)) {
                errors.push('Password harus mengandung minimal 1 angka');
            }

            // Validasi konfirmasi password
            if (password !== confirmPassword) {
                errors.push('Password dan konfirmasi password tidak cocok');
            }

            // Jika ada error, tampilkan dan prevent submit
            if (errors.length > 0) {
                e.preventDefault();
                alert('Error:\n' + errors.join('\n'));
                return false;
            }

            return true;
        });

        // Real-time password strength indicator
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;

                if (password.length >= 8) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[a-z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;

                // Bisa ditambahkan visual indicator di sini
                // console.log('Password strength:', strength);
            });
        }
    }
});

// Form validation umum
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = 'red';
            isValid = false;
        } else {
            field.style.borderColor = '';
        }
    });

    if (!isValid) {
        alert('Mohon lengkapi semua field yang wajib diisi');
    }

    return isValid;
}

// Format number as currency (Indonesia Rupiah)
function formatRupiah(angka) {
    const number = parseInt(angka) || 0;
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
}

// NIK validation (16 digits)
function validateNIK(nik) {
    return /^\d{16}$/.test(nik);
}

// Phone number validation (Indonesia format)
function validatePhone(phone) {
    // Format: 08xxx atau 62xxx atau +62xxx
    return /^(\+62|62|0)[0-9]{9,12}$/.test(phone.replace(/[\s-]/g, ''));
}

// Date validation (tidak boleh tanggal masa depan untuk tanggal lahir)
function validateBirthDate(date) {
    const birthDate = new Date(date);
    const today = new Date();
    return birthDate <= today;
}

// Auto-format input
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format phone number
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            // Remove non-numeric characters
            let value = this.value.replace(/\D/g, '');

            // Limit to reasonable phone number length
            if (value.length > 13) {
                value = value.substr(0, 13);
            }

            this.value = value;
        });

        input.addEventListener('blur', function() {
            if (this.value && !validatePhone(this.value)) {
                alert('Format nomor telepon tidak valid. Gunakan format: 08xxxxxxxxxx');
                this.focus();
            }
        });
    });

    // Validate NIK
    const nikInputs = document.querySelectorAll('input[name="nik"]');
    nikInputs.forEach(input => {
        input.addEventListener('input', function() {
            // Only allow numbers
            this.value = this.value.replace(/\D/g, '');

            // Limit to 16 characters
            if (this.value.length > 16) {
                this.value = this.value.substr(0, 16);
            }
        });

        input.addEventListener('blur', function() {
            if (this.value && !validateNIK(this.value)) {
                alert('NIK harus 16 digit angka');
                this.focus();
            }
        });
    });

    // Validate birth date
    const birthDateInputs = document.querySelectorAll('input[name="tanggal_lahir"]');
    birthDateInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && !validateBirthDate(this.value)) {
                alert('Tanggal lahir tidak valid');
                this.value = '';
                this.focus();
            }
        });
    });

    // Auto-calculate age from birth date
    birthDateInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value) {
                const birthDate = new Date(this.value);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();

                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }

                console.log('Usia pasien:', age, 'tahun');
            }
        });
    });
});

// Confirm before leaving page with unsaved changes
let formChanged = false;

document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            input.addEventListener('change', function() {
                formChanged = true;
            });
        });

        form.addEventListener('submit', function() {
            formChanged = false;
        });
    });
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
        return e.returnValue;
    }
});

// Search/filter functionality (untuk implementasi future)
function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toUpperCase();
    const table = document.getElementById(tableId);
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        let found = false;
        const td = tr[i].getElementsByTagName('td');

        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                const txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }

        tr[i].style.display = found ? '' : 'none';
    }
}

// Print function
function printPage() {
    window.print();
}

// Export to CSV (untuk implementasi future)
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tr');
    let csv = [];

    for (let i = 0; i < rows.length; i++) {
        const row = [];
        const cols = rows[i].querySelectorAll('td, th');

        for (let j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }

        csv.push(row.join(','));
    }

    const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
    const downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
