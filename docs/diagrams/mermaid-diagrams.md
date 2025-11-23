# Diagram Arsitektur PT Eshokita

## Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    USER {
        int id_user PK
        string nama
        string username
        string password_hash
        enum role "super-admin|admin|produksi|distributor"
    }

    RUTE {
        string kode_rute PK
        string nama_wilayah
    }

    CUSTOMER {
        int id_customer PK
        string nama
        string alamat
        string kode_rute FK
        json pricing_json "harga khusus per produk"
    }

    PRODUCT_CATEGORY {
        int id_category PK
        string name
        string description
    }

    PRODUCT {
        int id_product PK
        string sku
        string name
        string unit
        decimal price
        boolean active
        int id_category FK
    }

    STOCK {
        int id_product PK, FK
        int qty
        datetime updated_at
    }

    STOCK_TRANSACTION {
        int id_tx PK
        int id_product FK
        int qty_change
        enum type "in|out|adjust"
        string ref_type "surat_jalan|manual"
        int ref_id
        string note
        datetime created_at
    }

    SURAT_JALAN {
        int id_surat_jalan PK
        date tanggal
        string supir
        string kenek
        string plat_kendaraan
        json muatan_json "besar|kecil|serut → SKU"
    }

    PENGIRIMAN {
        int id_pengiriman PK
        int id_customer FK
        date tanggal
        string no_bon
        json pemesanan_json "array{sku,qty,price}"
        enum tipe_pembayaran
        string status
    }

    SHIPMENT_TRACKING {
        int id_tracking PK
        int id_pengiriman FK
        string status
        string location
        string note
        datetime created_at
    }

    INVOICE {
        int id_invoice PK
        int id_pengiriman FK
        string invoice_no
        date issue_date
        date due_date
        decimal amount
        enum status "unpaid|partial|paid"
    }

    PAYMENT {
        int id_payment PK
        int id_invoice FK
        datetime paid_at
        enum method "cash|transfer|other"
        decimal amount
        string note
    }

    CUSTOMER ||--o{ PENGIRIMAN : places
    RUTE ||--o{ CUSTOMER : belongs_to
    PRODUCT_CATEGORY ||--o{ PRODUCT : categorizes
    PRODUCT ||--|| STOCK : has
    PRODUCT ||--o{ STOCK_TRANSACTION : affects
    PENGIRIMAN ||--o{ SHIPMENT_TRACKING : tracks
    PENGIRIMAN ||--|| INVOICE : generates
    INVOICE ||--o{ PAYMENT : receives
```

## Flowchart Proses Utama

```mermaid
flowchart TD
    A[Start] --> B[Input Master Data]
    B --> C[Adjustment Stok Awal]
    
    C --> D[Buat Surat Jalan]
    D --> E[Auto Stock OUT by Muatan]
    
    E --> F[Buat Pengiriman]
    F --> G[Auto Create Invoice]
    
    G --> H{Update Status}
    H -->|Tracking| I[Update Lokasi/Status]
    I --> H
    
    H -->|Payment| J[Catat Pembayaran]
    J --> K[Update Invoice Status]
    K -->|Belum Lunas| H
    K -->|Lunas| L[End]
```

## Usecase Diagram

```mermaid
---
title: Use Case PT Eshokita
---
flowchart TB
    subgraph Roles
        sa[Super Admin]
        ad[Admin]
        pr[Produksi]
        ds[Distributor]
    end

    subgraph "Master Data"
        m1[Kelola User]
        m2[Kelola Produk & Kategori]
        m3[Kelola Customer & Rute]
    end

    subgraph "Operasional"
        o1[Kelola Surat Jalan]
        o2[Adjustment Stok]
        o3[Catat Pengiriman]
        o4[Update Tracking]
    end

    subgraph "Keuangan"
        f1[Kelola Invoice]
        f2[Catat Pembayaran]
        f3[Lihat Laporan]
    end

    sa ---- m1 & m2 & m3 & o1 & o2 & o3 & o4 & f1 & f2 & f3
    ad ---- m2 & m3 & o1 & o2 & o3 & o4 & f1 & f2 & f3
    pr ---- o1 & o2
    ds ---- o3 & o4
```

## Sequence Diagram: Proses Pengiriman

```mermaid
sequenceDiagram
    actor A as Admin
    participant SJ as SuratJalan
    participant P as Pengiriman
    participant S as Stock
    participant I as Invoice
    participant T as Tracking
    
    A->>SJ: Create Surat Jalan + Muatan
    SJ->>S: Auto Stock OUT (by SKU)
    
    A->>P: Create Pengiriman
    P->>I: Auto Create Invoice (unpaid)
    
    loop Tracking Updates
        A->>T: Update Status/Location
        T-->>P: Link to Pengiriman
    end
    
    A->>I: Record Payment
    I->>I: Update Status (unpaid→partial→paid)
```

## State Diagram: Status Invoice

```mermaid
stateDiagram-v2
    [*] --> Unpaid: Create Invoice
    Unpaid --> Partial: Partial Payment
    Partial --> Paid: Full Payment
    Unpaid --> Paid: Full Payment
    Paid --> [*]
```

## Component Diagram

```mermaid
---
title: Component Architecture
---
flowchart TD
    subgraph Frontend
        V[Views]
        C[Controllers]
        H[Helpers]
    end

    subgraph Models
        M1[UserModel]
        M2[CustomerModel]
        M3[RuteModel]
        M4[ProductModel]
        M5[StockModel]
        M6[SuratJalanModel]
        M7[PengirimanModel]
        M8[InvoiceModel]
    end

    subgraph Database
        D1[MySQL Tables]
    end

    V <--> C
    C --> H
    C <--> Models
    Models <--> D1
```