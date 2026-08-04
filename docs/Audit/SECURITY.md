# Security Policy

## Supported Versions

Below are the versions of MasjidCMS currently supported with security updates.

| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | :white_check_mark: |
| < 1.0   | :x:                |

## Verified Controls (as of this audit)

The following controls were reviewed against the `develop` branch source and confirmed present (source review only — not a penetration test):

- **CSRF protection**: enabled globally via `Config/Filters.php`, exempted only for `api/*` and installer routes, with token randomization enabled (`Config/Security.php`).
- **API authentication**: `api/*` routes are CSRF-exempt by CodeIgniter convention, but are independently protected by the `auth` (session authentication) and `rbac:<permission>` filters at the route-group level — see `app/Domains/Financial/Routes/financial.php` for the reference pattern. New API routes must follow the same pattern.
- **Login rate limiting**: implemented via CodeIgniter's `Throttler` service in both `AuthPageController::login()` (browser login) and `App\Domains\System\Controllers\AuthenticationController` (JSON/API login), limiting to 5 attempts per 5 minutes per IP+username.
- **SQL injection**: all raw `->query()` calls found in the codebase use parameterized placeholders (`?`), not string concatenation.
- **Secrets in history**: no `.env` file found committed to the `develop` branch history.

These were unverified claims in an earlier third-party audit and have since been confirmed by direct source inspection; no code changes were required for these items.

## Reporting a Vulnerability

Security is paramount for MasjidCMS as it manages public funds, donations, and jamaah data.

If you discover a security vulnerability within MasjidCMS, please send an email to the security team at `security@masjidcms.org` or open a confidential security report on GitHub. 

### Guidelines
- Please provide detailed steps to reproduce the issue.
- Do not disclose the vulnerability publicly until a fix has been released.
- We will acknowledge receipt of your vulnerability report within 48 hours and strive to release a security patch as quickly as possible.
