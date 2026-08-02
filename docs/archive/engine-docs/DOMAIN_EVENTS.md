# MasjidCMS — Domain Event Engine Foundation

Dokumen ini mendokumentasikan arsitektur, siklus kerja, dan spesifikasi **Domain Event Engine** di layer `app/Core/Events` sesuai dengan **SOFTWARE_ARCHITECTURE.md (v1.1)** dan **CORE_FRAMEWORK.md**.

---

## 1. Vision & Architecture Purpose

Domain Event Engine berfungsi sebagai bus komunikasi bebas antar-domain (*Loosely Coupled Event-Driven Architecture*). Komponen ini menjamin bahwa saat sebuah aksi bisnis terjadi di Domain A (misal: `Masjid` dibuat, `Donasi` diterima, `User` login), Domain A tidak perlu memanggil secara langsung Service dari Domain B, C, atau D.

Sebagai gantinya:
Domain A mempublikasikan (`dispatch`) **DomainEvent**, dan domain lain yang berminat mendengarkan (`listen`) event tersebut secara independen.

```
+-----------------------------------------------------------------------------------+
| Domain Service (Producer)                                                         |
|   e.g. CrudService::create() -> afterCreate()                                     |
+-----------------------------------------------------------------------------------+
                                         │
                                         ▼ (dispatch)
+-----------------------------------------------------------------------------------+
| EventDispatcher (App\Core\Events\EventDispatcher)                                 |
+-----------------------------------------------------------------------------------+
                                         │
                 ┌───────────────────────┼───────────────────────┐
                 ▼                       ▼                       ▼
      [ Activity Log Listener ]  [ Notification Listener ]  [ Analytics Listener ]
```

---

## 2. Component Structure

| Component Interface / Class | Full Namespace | Role / Purpose |
|---|---|---|
| **DomainEventInterface** | `App\Core\Contracts\Events\DomainEventInterface` | Contract standar payload event (`eventName`, `occurredAt`, `payload`). |
| **AbstractDomainEvent** | `App\Core\Events\AbstractDomainEvent` | Base class penampung timestamp & payload. |
| **EventDispatcherInterface** | `App\Core\Contracts\Events\EventDispatcherInterface` | Contract manajerial dispatcher (`dispatch`, `listen`, `forget`, `hasListener`). |
| **EventDispatcher** | `App\Core\Events\EventDispatcher` | Implementasi In-Memory Synchronous Event Dispatcher. |
| **EventListenerInterface** | `App\Core\Contracts\Events\EventListenerInterface` | Contract listener penangan event (`handle`). |
| **EntityCreatedEvent** | `App\Core\Events\EntityCreatedEvent` | Generic event pasca-insert data (`{entity}.created`). |
| **EntityUpdatedEvent** | `App\Core\Events\EntityUpdatedEvent` | Generic event pasca-update data (`{entity}.updated`). |
| **EntityDeletedEvent** | `App\Core\Events\EntityDeletedEvent` | Generic event pasca-delete data (`{entity}.deleted`). |

---

## 3. Integration with CrudService

`CrudService` pada Generic CRUD Engine secara otomatis terintegrasi dengan `EventDispatcher`. Pada hook `afterCreate()`, `afterUpdate()`, dan `afterDelete()`, `CrudService` secara default mempublikasikan event generic:

```
CrudService::create() ──► afterCreate() ──► EventDispatcher::dispatch(new EntityCreatedEvent)
CrudService::update() ──► afterUpdate() ──► EventDispatcher::dispatch(new EntityUpdatedEvent)
CrudService::delete() ──► afterDelete() ──► EventDispatcher::dispatch(new EntityDeletedEvent)
```

---

## 4. Future Queue & Async Architecture Roadmap

Pada fase `Phase 2.2`, `EventDispatcher` mengeksekusi listener secara **Synchronous In-Memory** di dalam siklus HTTP request yang sama. 

Di masa mendatang (Future Architecture Roadmap):
- `EventDispatcher` dapat di-upgrade atau dipasangkan dengan **Queue Driver** (Database Queue, Redis, RabbitMQ, Kafka).
- Event yang bersifat berat (misal: pengiriman Email massal, push notification, sync search engine) akan dipindahkan ke pengolahan **Background Job / Queue Worker** tanpa mengubah kode penayang event di Domain Service.
