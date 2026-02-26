# Analisis Kode Program - Louwes Care (Shoe Care Service)

## 1. Overview Proyek

Proyek ini adalah aplikasi manajemen layanan sepatu (shoe care service) berbasis Laravel yang bernama **Louwes Care**. Aplikasi ini mengelola pesanan pelanggan dengan fitur:

- **Input Order**: Penerimaan pesanan sepatu dengan berbagai treatment
- **Member System**: Sistem keanggotaan dengan point rewards
- **Manajemen Pesanan**: Tracking status pesanan (Proses → Selesai → Diambil)
- **Invoice**: Generasi invoice dan cetak nota (80mm thermal printer)
- **WhatsApp Integration**: Pengiriman notifikasi ke pelanggan
- **Dashboard Owner**: Laporan dan pengaturan

---

## 2. Struktur Database & Relasi

### 2.1 Model Utama

| Model | Tabel | Relasi |
|-------|-------|--------|
| `Customer` | `customers` | hasMany `Order`, hasOne `Member` |
| `Member` | `members` | belongsTo `Customer`, hasMany `PointHistory` |
| `Order` | `orders` | belongsTo `Customer`, hasMany `OrderDetail` |
| `OrderDetail` | `order_details` | belongsTo `Order` |
| `Treatment` | `treatments` | - |
| `Karyawan` | `karyawans` | - |
| `PointHistory` | `point_histories` | belongsTo `Member`, belongsTo `Order` |
| `Setting` | `settings` | - |

### 2.2 Schema Kunci

**Customers:**
```
php
- id, nama, no_hp (unique), tipe, alamat, sumber_info, timestamps
```

**Members:**
```
php
- id, customer_id (FK), level (Silver/Gold/Platinum), poin, total_transaksi, timestamps
```

**Orders:**
```
php
- id, no_invoice (unique), customer_id (FK), tgl_masuk, total_harga,
- klaim, paid_amount, metode_pembayaran, status_pembayaran,
- status_order, tipe_customer, catatan, kasir, kasir_keluar,
- wa_sent_1, wa_sent_2, timestamps
```

**OrderDetails:**
```
php
- id, order_id (FK), nama_barang, layanan, harga,
- estimasi_keluar, catatan, status, timestamps
```

**Treatments:**
```
php
- id, kategori, nama_treatment, harga, timestamps
```

---

## 3. Analisis Controller

### 3.1 OrderController (`app/Http/Controllers/OrderController.php`)

**Metode:**

| Metode | Fungsi |
|--------|--------|
| `index()` | Daftar pesanan dengan filter (search, tanggal, kategori, treatment, komplain) |
| `show($id)` | Tampilkan detail pesanan untuk edit |
| `update()` | Update pesanan termasuk logika klaim point |
| `store()` | Simpan pesanan baru dengan full logic |
| `check()` | Cek customer dari form cek-customer.blade.php |
| `checkCustomer()` | AJAX cek customer live di input-order |
| `toggleWa()` | Kirim notifikasi WhatsApp (invoice/pengambilan) |
| `deleteItems()` | Hapus item pesanan tertentu |

**Logika Kunci:**

1. **Klaim Reward (Multiple):**
   - Support multiple klaim (bisa klaim Diskon + Parfum sekaligus)
   - Setiap 8 poin bisa klaim 1 unit
   - Validasi poin cukup sebelum proses
   - Poin dikurangi dari member, dan dicatat di PointHistory

2. **Auto Status Order:**
   - Jika ada item berstatus 'Proses' → status_order = 'Proses'
   - Jika semua item 'Selesai' → status_order = 'Selesai'
   - Jika semua item 'Diambil' → status_order = 'Diambil'

3. **Pemberian Poin:**
   - 1 poin per Rp 50.000 transaksi
   - Poin bertambah setelah order disimpan

4. **WhatsApp Integration:**
   - Type 1: WA Invoice (hanya jika semua item 'Proses')
   - Type 2: WA Pengambilan (status update)
   - Format nomor: 08xx → 628xx

### 3.2 MemberController (`app/Http/Controllers/MemberController.php`)

**Metode:**

| Metode | Fungsi |
|--------|--------|
| `store()` | Daftar member baru dari modal popup |
| `claimPoints()` | Klaim reward dari halaman detail pesanan |

**Logika:**
- Validasi manual dengan `Validator` (tidak auto-redirect)
- Jika HP sudah terdaftar sebagai member → error
- Jika validasi gagal → JSON error 422
- Jika berhasil → JSON success dengan data member

### 3.3 OrderDetailController (`app/Http/Controllers/OrderDetailController.php`)

**Metode:**
| Metode | Fungsi |
|--------|--------|
| `updateStatus()` | Update status item, auto-update status Order utama |

---

## 4. Analisis View

### 4.1 Input Order (`resources/views/input-order.blade.php`)

**Fitur:**
- Live customer check via AJAX
- Dynamic item + treatment rows (add/remove)
- Kategori → Treatment dropdown (filter berdasarkan kategori)
- Modal klaim reward (8 poin = 1 klaim)
- Modal pembayaran (Tunai/Transfer/QRIS, Lunas/DP)
- Live hitung total, diskon, kembalian
- Invoice popup dengan print function (80mm thermal)

**JavaScript Logic:**
- `cekCustomer()` - AJAX live check
- `calculateGlobalTotal()` - Hitung total semua item
- `filterTreatments()` - Filter dropdown berdasarkan kategori
- `submitOrder()` - AJAX submit ke backend
- `populateInvoice()` - Render invoice dari response
- `printInvoice()` - Print ke thermal printer via iframe

### 4.2 Detail Pesanan (`resources/views/pesanan/show.blade.php`)

**Fitur:**
- Edit semua field pesanan via AJAX
- Modal klaim reward (edit klaim yang sudah ada)
- Modal pembayaran pelunasan
- Delete selected items
- Auto-update status order
- Invoice popup dan print

**Alpine.js:**
- State management untuk klaim, pembayaran
- Computed properties untuk hitungan diskon/poin

---

## 5. Sistem Point & Reward

### 5.1 Alur Point

```
Transaksi Selesai
       ↓
Hitung Poin: floor(total_harga / 50000)
       ↓
Tambah poin ke member + Record PointHistory (type: earn)
       ↓
Member bisa klaim reward
       ↓
8 poin = 1 Klaim (Diskon Rp XXXX / Free Parfum)
       ↓
Kurangi poin + Record PointHistory (type: redeem)
```

### 5.2 Logika Klaim di OrderController

```php
// Di method store() dan update()
$totalPointsNeeded = ($qtyDiskon + $qtyParfum) * 8;

if ($customer->member && $customer->member->poin >= $totalPointsNeeded) {
    // Proses klaim...
} else {
    // Error: Poin tidak cukup
}
```

---

## 6. Keamanan & Best Practices

### 6.1 Yang Sudah Baik

✅ **CSRF Protection** - Semua form menggunakan `@csrf`  
✅ **Mass Assignment Protection** - Model menggunakan `$fillable`/`$guarded`  
✅ **SQL Injection Prevention** - Menggunakan Eloquent ORM  
✅ **XSS Prevention** - Blade template auto-escapes output  
✅ **Validation** - Server-side validation dengan Validator  
✅ **Transaction** - DB transaction untuk operasi terkait  
✅ **Error Handling** - Try-catch dengan rollback  

### 6.2 Yang Perlu Perbaikan

⚠️ **Route Security** - Beberapa route bisa ditambahkan middleware role check  
⚠️ **Input Sanitization** - Validasi tambahan untuk input number  
⚠️ **Rate Limiting** - Belum ada implementasi rate limiting untuk AJAX calls  
⚠️ **Logging** - Activity logging untuk audit trail  

---

## 7. Potensi Bug & Perbaikan

### 7.1 Bug yang Perlu Dicek

1. **Race Condition pada Poin**
   - Jika user klaim reward dari 2 tab browser bersamaan
   - **Solusi**: Gunakan database locking atau optimistic locking

2. **Duplicate Invoice Number**
   - Jika 2 order dibuat dalam 1 detik yang sama
   - **Solusi**: Gunakan UUID atau lock table saat generate invoice

3. **Expired Session pada AJAX**
   - Jika session expired, AJAX bisa gagal silent
   - **Solusi**: Tambahin global AJAX error handler untuk redirect ke login

4. **NullPointer di View**
   - `$order->customer->member` bisa null
   - **Solusi**: Gunakan optional() helper atau null coalescing

### 7.2 Kode yang Bisa Dioptimasi

1. **Duplicate Code di View**
   - Invoice popup code duplikasi di input-order dan show.blade
   - **Solusi**: Extract jadi Blade Component

2. **Large Query di index()**
   - Semua detail order di-load sekaligus untuk pagination
   - **Solusi**: Lazy loading atau separate query

3. **Frontend Logic di Controller**
   - Beberapa logic bisa dipindahkan ke Service class

---

## 8. Ringkasan Arsitektur

```
┌─────────────────────────────────────────────────────────────┐
│                    ROUTER (web.php)                         │
│  - Route grouping dengan auth middleware                    │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                 CONTROLLER LAYER                            │
│  - OrderController (CRUD Order, Klaim, WA)                │
│  - MemberController (Member, Klaim Point)                  │
│  - OrderDetailController (Status Item)                     │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                   MODEL LAYER                               │
│  - Eloquent Relationships                                   │
│  - Business Logic di static methods (Setting::getDiskon)    │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                   VIEW LAYER                                │
│  - Blade Template + Tailwind CSS                           │
│  - Alpine.js untuk interactivity                           │
│  - jQuery untuk AJAX                                       │
└─────────────────────────────────────────────────────────────┘
```

---

## 9. Kesimpulan

Aplikasi ini sudah memiliki struktur yang cukup solid dengan:

✅ Arsitektur MVC yang jelas  
✅ Sistem member & point yang lengkap  
✅ Integrasi WhatsApp yang useful  
✅ Invoice printing support untuk thermal printer  
✅ UI yang interaktif dengan modal-popup  

Namun ada beberapa area yang bisa dikembangkan:
- Unit testing
- API endpoints (untuk mobile app)
- Performance optimization
- Enhanced security

---

*Generated: Analisis komprehensif kode program Louwes Care*

---

## 10. Analisis Sistem Login & Autentikasi

### 10.1 Struktur Route Login

**File:** `routes/auth.php`

```
guest middleware
├── GET /login      → Tampilkan form login
├── POST /login     → Proses autentikasi
├── GET /register   → Tampilkan form registrasi
├── POST /register  → Proses registrasi user baru
├── GET /forgot-password → Lupa password
├── POST /forgot-password → Kirim link reset
├── GET /reset-password/{token} → Form reset password
└── POST /reset-password → Proses reset password

auth middleware
├── GET /verify-email → Prompt verifikasi email
├── GET /verify-email/{id}/{hash} → Verifikasi email (signed + throttle)
├── POST /verification-notification → Kirim ulang email verifikasi
├── GET /confirm-password → Konfirmasi password sebelum action sensitif
├── POST /confirm-password → Proses konfirmasi password
├── PUT /password → Update password
└── POST /logout → Logout
```

### 10.2 Controller Autentikasi

**AuthenticatedSessionController** (`app/Http/Controllers/Auth/AuthenticatedSessionController.php`)

| Metode | Fungsi |
|--------|--------|
| `create()` | Tampilkan view login |
| `store()` | Proses login + redirect berdasarkan role |
| `destroy()` | Logout + invalidate session |

**Logika Redirect Setelah Login:**
```
php
if ($user->role === 'owner') {
    return redirect()->intended(route('owner.dashboard'));
}
// Admin/Kasir
return redirect()->intended(route('dashboard'));
```

### 10.3 Login Request Validation

**File:** `app/Http/Requests/Auth/LoginRequest.php`

**Validasi:**
- `email`: required, string, email
- `password`: required, string

**Fitur Keamanan:**
1. **Rate Limiting**: Maksimal 5 percobaan gagal
   - Jika lebih dari 5x gagal, harus tunggu beberapa saat
   - Pesan error: "Too many login attempts"

2. **Throttle Key**: `strtolower(email)|ip_address`
   - Mencegah brute force dari IP berbeda

3. **Session Regeneration**: Setelah login berhasil, session di-regenerate untuk mencegah session fixation

### 10.4 User Model & Role

**File:** `app/Models/User.php`

```
php
protected $fillable = [
    'name',
    'email',
    'password',
    'role', // 'admin' atau 'owner'
];

protected $hidden = [
    'password',
    'remember_token',
];
```

**Role User:**
| Role | Deskripsi | Redirect |
|------|-----------|----------|
| `owner` | Owner toko | `/owner/dashboard` |
| `admin` | Admin/Kasir | `/dashboard` (cek-customer) |

**Default Role:** `admin` (saat registrasi)

### 10.5 View Login

**File:** `resources/views/auth/login.blade.php`

**Fitur:**
- Input email & password
- Checkbox "Ingat saya" (remember token)
- Link "Lupa password?"
- Validasi error display
- Session status display

### 10.6 Keamanan yang Sudah Baik

✅ **Rate Limiting** - Maksimal 5 percobaan gagal  
✅ **Password Hashing** - Menggunakan Laravel Hash (bcrypt)  
✅ **Session Regeneration** - Mencegah session fixation  
✅ **CSRF Protection** - Semua form menggunakan @csrf  
✅ **Email Verification** - Laravel Breeze/Veryfi email  
✅ **Signed URLs** - Verifikasi email menggunakan signed route  
✅ **Role-based Redirect** - Owner vs Admin/Kasir  

### 10.7 Potensi Perbaikan Login

1. **Two-Factor Authentication (2FA)**
   - Belum ada implementasi 2FA
   - Bisa ditambahkan dengan Laravel Fortify

2. **Login Audit Trail**
   - Tidak ada logging percobaan login
   - Cocok ditambahkan untuk security audit

3. **Captcha**
   - Belum ada captcha untuk login
   - Bisa ditambahkan Google Recaptcha

4. **Password Requirements**
   - Validasi password strength belum ada
   - Bisa ditambahkan: min 8 karakter, kombinasi huruf/angka

5. **Account Lockout**
   - Jika terlalu banyak percobaan gagal, bisa lock account sementara
   - Saat ini hanya rate limiting

---

## 11. Ringkasan Keseluruhan

### Kekuatan Aplikasi:
1. ✅ Arsitektur MVC yang bersih
2. ✅ Sistem member & point yang lengkap
3. ✅ Integrasi WhatsApp yang baik
4. ✅ Invoice printing support
5. ✅ UI interaktif dengan modal
6. ✅ Rate limiting untuk login
7. ✅ Role-based access control

### Area Perbaikan:
1. ⚠️ Tambahkan 2FA
2. ⚠️ Tambahkan audit trail login
3. ⚠️ Tambahkan validasi password strength
4. ⚠️ Unit testing
