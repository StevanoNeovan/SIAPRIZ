@component('mail::message')
# Akun Anda Sudah Aktif 🎉

Terima kasih telah memverifikasi email Anda.

Akun **Administrator SIAPRIZ** Anda kini sudah aktif dan siap digunakan.

@component('mail::button', ['url' => route('login')])
Login ke SIAPRIZ
@endcomponent

Jika Anda tidak merasa melakukan pendaftaran ini, silakan abaikan email ini.

Salam hangat,  
**Tim SIAPRIZ**
@endcomponent
