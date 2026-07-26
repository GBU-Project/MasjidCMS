# Generic CRUD Engine

## Purpose
Folder `app/Core/CRUD` menyediakan kelas abstrak dasar `CrudService` yang dapat diturunkan oleh seluruh Business Service untuk menghadirkan operasi CRUD standar yang konsisten dan dilengkapi Lifecycle/Validation Hooks.

## Allowed Responsibility
- Menyediakan alur eksekusi CRUD standar (`create`, `update`, `delete`, `restore`, `find`, `findAll`, `paginate`, `exists`, `count`).
- Menyediakan Hook Method (`beforeCreate`, `afterCreate`, `beforeUpdate`, `afterUpdate`, dll) yang dapat di-override oleh kelas turunan.
- Berinteraksi secara decoupled via `App\Core\Contracts\CrudRepositoryInterface`.

## Forbidden Responsibility
- DILARANG mengeksekusi SQL query langsung (harus melalui `CrudRepositoryInterface`).
- DILARANG terikat pada tabel database atau domain bisnis tertentu.
