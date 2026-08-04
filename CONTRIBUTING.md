# Contributing to MasjidCMS

Thank you for considering a contribution. MasjidCMS manages mosque finances and congregation data for real communities, so we ask contributors to follow a few extra guardrails beyond typical OSS etiquette.

## Before you start

- **Open an issue first** for anything beyond a trivial fix (typo, small bug), so the change can be discussed before you invest time.
- **Do not change architecture, domain boundaries, or workflows speculatively.** MasjidCMS follows a domain-oriented structure (`app/Domains/*`); if your change would move code across domain boundaries or restructure a controller, explain why in the issue first.

## Setting up

Follow [`INSTALLATION.md`](INSTALLATION.md) for local environment setup (PHP 8.2+, Composer, MySQL/MariaDB).

## Running tests

```bash
composer install
composer test        # runs phpunit
```

All pull requests are automatically run against the test suite via GitHub Actions (see `.github/workflows/ci.yml`). PRs that fail CI will not be merged.

- Add or update tests for any behavior change.
- The `Financial` domain (fund accounting, double-entry posting) is the highest-risk area in the codebase — changes here require test coverage of the affected business rule(s) (see `docs/FINANCIAL_DATA_MODEL.md`) and should be reviewed with extra care.

## Coding conventions

- Follow the existing domain structure: Entities, Services, Repositories, DTOs, Policies under `app/Domains/<Domain>`.
- Keep controllers thin — orchestration and business logic belong in domain Services, not Controllers. If a controller is growing to handle multiple resources, split it by resource rather than adding more actions to one class.
- New API endpoints under `api/*` are CSRF-exempt by framework convention; they **must** be protected by the `auth` filter (and `rbac:<permission>` where applicable) at the route level — see `app/Domains/Financial/Routes/financial.php` for the pattern.

## Security

Do not open a public issue for security vulnerabilities — see [`SECURITY.md`](SECURITY.md) for the responsible-disclosure process.

## Pull request checklist

- [ ] Linked to an existing issue (or explains why one wasn't needed)
- [ ] Tests added/updated and passing locally (`composer test`)
- [ ] No unrelated architectural or workflow changes bundled in
- [ ] Documentation updated if the change affects architecture, security, or installation (see `docs/`, `README.md`, `INSTALLATION.md`)
