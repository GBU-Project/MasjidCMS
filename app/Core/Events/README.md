# Domain Event Engine

## Purpose
Folder `app/Core/Events` memuat komponen Domain Event Engine (`DomainEventInterface`, `AbstractDomainEvent`, `EventDispatcher`, `EntityCreatedEvent`, `EntityUpdatedEvent`, `EntityDeletedEvent`) yang memungkinkan komunikasi antar-domain berbasis Event Driven Architecture yang terisolasi (*loosely coupled*).

## Allowed Responsibility
- Menyediakan kontrak `DomainEventInterface`, `EventDispatcherInterface`, `EventListenerInterface`.
- Menyediakan instansi `EventDispatcher` in-memory.
- Menyediakan generic event (`EntityCreatedEvent`, `EntityUpdatedEvent`, `EntityDeletedEvent`).

## Forbidden Responsibility
- DILARANG mengeksekusi panggilan asynchronous / message broker external (Queue/Redis/RabbitMQ/Kafka) di level fondasi in-memory ini.
- DILARANG mengandung logika bisnis spesifik domain tertentu.
