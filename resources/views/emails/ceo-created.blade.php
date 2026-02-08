<!DOCTYPE html>
<html>
<body style="font-family:Arial;background:#f4f6f8;padding:30px">

<div style="max-width:600px;margin:auto;background:white;padding:30px;border-radius:10px">
    <h2 style="color:#2563eb">Akun CEO SIAPRIZ Berhasil Dibuat</h2>

    <p>Halo,</p>

    <p>Akun CEO untuk perusahaan Anda telah dibuat.</p>

    <div style="background:#f1f5f9;padding:15px;border-radius:6px">
        <p><strong>Email:</strong> {{ $ceo->email }}</p>
        <p><strong>Password:</strong> {{ $password }}</p>
        <p><strong>Secret Code untuk ubah password:</strong> SIAPRIZ-CEO-2026 </p>
    </div>

    <p>Silakan login dan segera ubah password Anda.</p>

    <a href="{{ url('/login') }}"
       style="display:inline-block;margin-top:20px;background:#2563eb;color:white;
       padding:12px 20px;border-radius:6px;text-decoration:none">
        Login ke SIAPRIZ
    </a>

    <p style="margin-top:30px;font-size:12px;color:#64748b">
        © {{ date('Y') }} SIAPRIZ
    </p>
</div>

</body>
</html>
