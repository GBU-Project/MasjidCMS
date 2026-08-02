# MasjidCMS

**MasjidCMS** is an open-source content management and administration platform built for mosques (masjid) and their DKM (Dewan Kemakmuran Masjid) management boards. It combines a public-facing website with a full administrative back office for managing congregation (jamaah) data, mosque programs, finances, and content — all in one place.

## Project Goals

- Give every mosque a professional, easy-to-manage website without needing a developer on staff.
- Provide DKM administrators with the tools to manage congregation data, organizational structure, and day-to-day operations.
- Implement mosque financial management that respects Sharia accounting principles, with clear separation between restricted and unrestricted funds.
- Stay approachable for small teams while remaining solid enough to extend and self-host.

## Main Features

- **Mosque Profile Management** — institutional identity, history, vision & mission, contact details, and prayer time configuration.
- **Jamaah Management** — congregation member records and profiles.
- **Family Management** — household/family grouping linked to congregation members.
- **DKM & Organizational Structure** — management board (pengurus) and work divisions (bidang).
- **Website CMS** — manage the public site's pages and homepage sections without touching code.
- **News & Articles** — publish mosque announcements and articles.
- **Kajian Schedule** — recurring religious study session schedules.
- **Agenda** — mosque events and activity calendar.
- **Gallery** — photo, video, and document media showcase.
- **Media Library** — centralized, reusable file/image management.
- **Financial Management** — double-entry bookkeeping with fund accounting (see below).
- **Prayer Time Configuration** — locally calculated prayer schedules (no external API dependency), configurable per mosque location and calculation method.
- **Role Based Access Control (RBAC)** — granular, role-driven permissions for administrators and staff.
- **Audit Log** — traceability for sensitive administrative actions.
- **Responsive Admin Dashboard** — a consistent, modern back-office UI across desktop and mobile.

### Fund Accounting

MasjidCMS implements **Fund Accounting** by separating restricted funds (Zakat, Qurban, Wakaf) from unrestricted funds (General Cash, Development Fund), while enforcing Sharia accounting business rules **BR-FIN-01 through BR-FIN-04**.

## System Requirements

- PHP 8.2 or higher
- MySQL / MariaDB
- Composer
- A standard web server (Apache/Nginx) or PHP's built-in server for local development

## Installation

MasjidCMS is built on [CodeIgniter 4](https://codeigniter.com/). At a high level:

1. Clone the repository and install dependencies with Composer.
2. Configure your `.env` file (database connection, base URL, etc.).
3. Run database migrations.
4. Open the app in your browser and complete the guided installer, which creates your mosque profile and the first administrator account.

See [`INSTALLATION.md`](INSTALLATION.md) for the full step-by-step guide.

## Project Structure

```
app/            Application code (Controllers, Domains, Views, Config, ...)
public/         Web root, front controller, and public assets
tests/          Automated test suite
docs/           Technical documentation and architecture references
docs/adr/       Architecture Decision Records
docs/archive/   Historical/point-in-time project reports (kept for reference)
```

MasjidCMS's application layer follows a domain-oriented structure (`app/Domains/*`) grouping entities, services, and repositories by business area (e.g. Jamaah, Financial, System), on top of the standard CodeIgniter 4 MVC layout.

## Roadmap

MasjidCMS is under active stabilization. Near-term priorities include broadening test coverage, refining the public site experience, and expanding financial reporting. See [`ROADMAP.md`](ROADMAP.md) for more detail.

## Contribution

Contributions are welcome. Please open an issue to discuss significant changes before submitting a pull request. Keep changes scoped, include tests where practical, and follow the existing project conventions.

## License

Released under the [MIT License](LICENSE).
