# Identity Providers (System Domain)

## Overview
Folder `app/Domains/System/Providers` memuat implementasi penyedia identitas (Identity Provider) yang mengimplementasikan `App\Core\Contracts\Auth\IdentityProviderInterface`.

## Provider Architecture & Isolation
`AuthenticationService` **tidak pernah** bergantung pada implementasi provider tertentu. Pemilihan provider diatur melalui konfigurasi `AuthConfig::$default_provider` dan diinstansiasi oleh `IdentityProviderFactory`.

---

## Identity Provider Roadmap

| Provider Class | Protocol / Source | Status |
|---|---|---|
| **DatabaseIdentityProvider** | Local Database (`users` table) | **ACTIVE (Phase 1.3)** |
| **LDAPIdentityProvider** | Active Directory / OpenLDAP | *PLANNED (Future)* |
| **OAuthIdentityProvider** | OAuth 2.0 Provider | *PLANNED (Future)* |
| **SAMLIdentityProvider** | SAML 2.0 Enterprise SSO | *PLANNED (Future)* |
| **OIDCIdentityProvider** | OpenID Connect | *PLANNED (Future)* |
| **AzureIdentityProvider** | Microsoft Azure AD / Entra ID | *PLANNED (Future)* |
| **GoogleIdentityProvider** | Google Workspace Identity | *PLANNED (Future)* |
