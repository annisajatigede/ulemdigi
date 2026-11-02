import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

export const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer);
    toast.addEventListener('mouseleave', Swal.resumeTimer);
  }
});

export function notifySuccess(title = 'Berhasil!', text = '') {
  return Toast.fire({
    icon: 'success',
    title: title,
    text: text
  });
}

export function notifyError(title = 'Terjadi Kesalahan', text = '') {
  return Toast.fire({
    icon: 'error',
    title: title,
    text: text
  });
}

export function alertSuccess(title, text) {
  return Swal.fire({
    icon: 'success',
    title: title || 'Berhasil!',
    text: text || '',
    confirmButtonColor: '#e11d48',
    confirmButtonText: 'OK',
    customClass: {
      popup: 'rounded-3xl font-sans',
      confirmButton: 'rounded-xl px-6 py-2.5 font-bold text-sm'
    }
  });
}

export function alertError(title, text) {
  return Swal.fire({
    icon: 'error',
    title: title || 'Gagal!',
    text: text || '',
    confirmButtonColor: '#e11d48',
    confirmButtonText: 'Tutup',
    customClass: {
      popup: 'rounded-3xl font-sans',
      confirmButton: 'rounded-xl px-6 py-2.5 font-bold text-sm'
    }
  });
}

export async function alertConfirm(title, text, confirmButtonText = 'Ya, Hapus') {
  const res = await Swal.fire({
    title: title,
    text: text,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e11d48',
    cancelButtonColor: '#78716c',
    confirmButtonText: confirmButtonText,
    cancelButtonText: 'Batal',
    customClass: {
      popup: 'rounded-3xl font-sans',
      confirmButton: 'rounded-xl px-5 py-2.5 font-bold text-sm',
      cancelButton: 'rounded-xl px-5 py-2.5 font-bold text-sm'
    }
  });
  return res.isConfirmed;
}

export default Swal;
