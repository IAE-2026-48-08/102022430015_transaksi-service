# Transaction Service — IAE Tugas 2

Mini-Service untuk manajemen transaksi dan cicilan pinjaman nasabah.  
Dibangun dengan **Laravel 11**, **Docker**, **MySQL**, **Swagger**, dan **GraphQL (Lighthouse)**.

---

## 🚀 Cara Menjalankan

### 1. Clone & masuk ke folder
```bash
git clone <repo-url>
cd transaction-service
```

### 2. Salin dan konfigurasi `.env`
```bash
cp .env.example .env
```
Edit file `.env`, ubah nilai `IAE_API_KEY` dengan NIM kamu:
```
IAE_API_KEY=10XXXXXXXXX
```

### 3. Jalankan dengan Docker
```bash
docker-compose up -d --build
```

### 4. Setup aplikasi (jalankan di dalam container)
```bash
docker exec -it transaction-service-app bash

php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan l5-swagger:generate
exit
```

### 5. Akses aplikasi
| Fitur | URL |
|---|---|
| REST API | http://localhost:8080/api/v1/transactions |
| Swagger UI | http://localhost:8080/api/documentation |
| GraphQL Playground | http://localhost:8080/graphql-playground |

---

## 📡 Endpoint REST API

Semua endpoint wajib menyertakan header:
```
X-IAE-KEY: [NIM_KAMU]
```

| Method | Endpoint | Fungsi |
|---|---|---|
| GET | `/api/v1/transactions` | Ambil daftar semua transaksi |
| GET | `/api/v1/transactions/repayment/{account_id}` | Cek riwayat cicilan nasabah |
| GET | `/api/v1/transactions/account/{account_id}` | Ambil transaksi milik nasabah |
| POST | `/api/v1/transactions/repayment` | Eksekusi pembayaran cicilan |

### Contoh Request POST `/api/v1/transactions/repayment`
```json
{
    "account_id": "ACC-001",
    "repayment_amount": 1500000,
    "installment_number": 3
}
```

---

## 🔷 GraphQL

Akses playground: http://localhost:8080/graphql-playground

### Contoh Query
```graphql
# Semua transaksi
query {
    transactions {
        id
        account_id
        type
        amount
        description
        transaction_date
    }
}

# Transaksi by account
query {
    transactionsByAccount(account_id: "ACC-001") {
        id
        type
        amount
        description
        transaction_date
    }
}

# Cicilan by account
query {
    repaymentsByAccount(account_id: "ACC-001") {
        id
        installment_number
        status
        repayment_amount
        due_date
        paid_at
    }
}
```

---

## 📦 Tech Stack
- Laravel 11
- PHP 8.2
- MySQL 8.0
- Nginx (Alpine)
- Docker & Docker Compose
- L5-Swagger (OpenAPI 3.0)
- Lighthouse PHP (GraphQL)

---

## 📋 Standard Integration Contract Compliance
- ✅ Protokol: HTTP/1.1
- ✅ Format: JSON + UTF-8
- ✅ Response wrapper: `status`, `message`, `data`, `meta`
- ✅ Security: `X-IAE-KEY` header authentication
- ✅ Minimal 3 endpoint fungsional (4 endpoint tersedia)
- ✅ Swagger/OpenAPI documentation
- ✅ GraphQL dengan Lighthouse
