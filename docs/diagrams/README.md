# Panduan Pembacaan Diagram

## 1. Entity Relationship Diagram (ERD)
- Menggambarkan struktur database dan hubungan antar tabel
- Notasi:
  - PK = Primary Key
  - FK = Foreign Key
  - ||--o{ = One-to-Many relation
  - ||--|| = One-to-One relation

## 2. Flowchart Proses Utama
- Menunjukkan alur kerja dari awal hingga akhir
- Kotak = proses/aktivitas
- Diamond = decision point
- Panah = arah alur

## 3. Use Case Diagram
- Menunjukkan siapa bisa melakukan apa dalam sistem
- Roles = peran pengguna
- Subgraph = pengelompokan fungsionalitas
- Garis = hubungan akses

## 4. Sequence Diagram
- Menunjukkan interaksi antar komponen sistem secara berurutan
- Actor = pengguna
- Participant = komponen sistem
- Panah = request/response
- Loop = proses berulang

## 5. State Diagram
- Menunjukkan perubahan status suatu entitas
- [*] = start/end state
- --> = transisi
- Text di atas panah = trigger perubahan

## 6. Component Diagram
- Menunjukkan arsitektur aplikasi secara high-level
- Subgraph = pengelompokan komponen
- <--> = komunikasi dua arah
- --> = komunikasi satu arah

## Cara Membaca File
1. Buka file mermaid-diagrams.md di editor yang mendukung Mermaid (e.g., VS Code dengan extension Mermaid)
2. Tiap diagram akan ter-render otomatis
3. Alternatif: copy kode diagram ke https://mermaid.live untuk melihat visualisasi

## Keterangan Khusus
- JSON fields dijelaskan dalam quotes ("...")
- Enum values dalam quotes ("a|b|c")
- Foreign key relations ditandai dengan _FK di nama kolom
- Subgraph digunakan untuk logical grouping