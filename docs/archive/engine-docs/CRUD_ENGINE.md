# MasjidCMS — Generic CRUD Engine Foundation

Dokumen ini menjelaskan rancangan, alur eksekusi, dan kait lifecycle (**Lifecycle & Validation Hooks**) dari **Generic CRUD Engine** di layer `app/Core/CRUD` sesuai **SOFTWARE_ARCHITECTURE.md (v1.1)** dan **CORE_FRAMEWORK.md**.

---

## 1. Architecture Overview

Generic CRUD Engine dirancang agar seluruh domain bisnis di MasjidCMS dapat berbagi fungsi CRUD standar tanpa melakukan pengulangan kode (*DRY - Don't Repeat Yourself*), sembari mempertahankan fleksibilitas kustomisasi melalui **Hooks System**.

```
+-------------------------------------------------------------------------------+
| Client Request (HTTP Controller)                                             |
+-------------------------------------------------------------------------------+
                                       │
                                       ▼
+-------------------------------------------------------------------------------+
| Domain Service (Extend App\Core\CRUD\CrudService)                             |
|  - validateCreate() / validateUpdate()                                        |
|  - beforeCreate() / beforeUpdate()                                            |
|  - afterCreate()  / afterUpdate()                                             |
+-------------------------------------------------------------------------------+
                                       │
                                       ▼ (Depends ONLY on Contract)
+-------------------------------------------------------------------------------+
| App\Core\Contracts\CrudRepositoryInterface                                   |
+-------------------------------------------------------------------------------+
                                       │
                                       ▼
+-------------------------------------------------------------------------------+
| App\Core\Repositories\BaseRepository (Implements CrudRepositoryInterface)     |
+-------------------------------------------------------------------------------+
                                       │
                                       ▼
+-------------------------------------------------------------------------------+
| Database                                                                      |
+-------------------------------------------------------------------------------+
```

---

## 2. CRUD Execution Lifecycle & Pipeline

Setiap metode manipulasi data di `CrudService` mengeksekusi pipeline urut sebagai berikut:

### Create Lifecycle Pipeline:
1. **validateCreate($data)**: Pengecekan aturan validasi awal.
2. **beforeCreate(&$data)**: Modifikasi data sebelum dikirim ke DB (misal: auto-generate UUID, slug, atau hashing).
3. **Repository::create($data)**: Eksekusi simpan ke database.
4. **afterCreate($entity)**: Pemicu event pasca-simpan (misal: kirim notifikasi, log audit).
5. **Return Result**.

### Update Lifecycle Pipeline:
1. **exists($id)**: Memastikan keberadaan data (throw `NotFoundException` jika tidak ada).
2. **validateUpdate($id, $data)**: Pengecekan validasi pembaruan.
3. **beforeUpdate($id, &$data)**: Modifikasi data sebelum update.
4. **Repository::update($id, $data)**: Eksekusi update ke database.
5. **afterUpdate($updatedEntity)**: Pemicu event pasca-update.
6. **Return Updated Entity**.

---

## 3. Hook Method Reference

| Category | Hook Method | Parameter | Purpose |
|---|---|---|---|
| **Validation** | `validateCreate()` | `array $data` | Pengecekan aturan sebelum insert. |
| **Validation** | `validateUpdate()` | `int/string $id, array $data` | Pengecekan aturan sebelum update. |
| **Validation** | `validateDelete()` | `int/string $id` | Pengecekan aturan sebelum delete. |
| **Lifecycle** | `beforeCreate()` | `array &$data` | Manipulasi data sebelum insert. |
| **Lifecycle** | `afterCreate()` | `mixed $entity` | Trigger event pasca-insert. |
| **Lifecycle** | `beforeUpdate()` | `int/string $id, array &$data` | Manipulasi data sebelum update. |
| **Lifecycle** | `afterUpdate()` | `mixed $entity` | Trigger event pasca-update. |
| **Lifecycle** | `beforeDelete()` | `int/string $id` | Pre-processing hapus data. |
| **Lifecycle** | `afterDelete()` | `int/string $id` | Trigger event pasca-hapus. |
| **Lifecycle** | `beforeRestore()` | `int/string $id` | Pre-processing restore data. |
| **Lifecycle** | `afterRestore()` | `int/string $id` | Trigger event pasca-restore. |

---

## 4. Class & Interface Diagram

```
App\Core\Contracts\CrudRepositoryInterface (Interface)
              ▲
              │ (Implements)
App\Core\Repositories\BaseRepository (Abstract Repository)

App\Core\Services\BaseService (Abstract Service)
              ▲
              │ (Extends)
App\Core\CRUD\CrudService (Abstract Generic CRUD Engine)
              │
              └─► Depends on CrudRepositoryInterface
```
