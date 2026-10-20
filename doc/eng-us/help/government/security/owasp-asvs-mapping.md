---
title: Mapping OWASP ASVS to Exponential
audience: government, developer, admin
category: public-sector
order: 40
summary: For public bodies whose security requirements reference the OWASP Application Security Verification Standard - the ASVS 4.0.3 chapters the project maps controls to, the status Unverified, the one known level 1 gap (password length), and how to plan your own verification.
keywords: OWASP ASVS, application security verification standard, 4.0.3, level 1, level 2, V2 authentication, V3 session management, V4 access control, V5 validation, V7 logging, V12 files, V13 API, V14 configuration, verification, security requirements
type: reference
---
# Mapping OWASP ASVS to Exponential

This page is for security architects and developers in public bodies, and for suppliers answering ASVS-based
requirements. The OWASP Application Security Verification Standard (<https://owasp.org/www-project-application-security-verification-standard/>)
is a catalog of testable requirements in three levels. The project has made **no** requirement-by-requirement
assessment: its status is **Unverified** ([Security standards: OWASP ASVS 4.0.3](../../../standards/security.md#owasp-asvs-403)).
The project uses edition 4.0.3, as most current questionnaires do; OWASP has since published 5.0.

## Contents

1. [Controls present, by chapter](#controls-present-by-chapter)
2. [The level 1 gap you can close](#the-level-1-gap-you-can-close)
3. [Planning a verification](#planning-a-verification)
4. [Related pages](#related-pages)

## Controls present, by chapter

| ASVS chapter | Present in Exponential | Operator's note |
|---|---|---|
| V2 Authentication | `password_hash()` storage (V2.4); minimum length 10 (V2.1.1 asks 12); weak password check; optional TOTP two-step sign-in (V2.8) | raise `MinPasswordLength`; require two-step sign-in |
| V3 Session management | `HttpOnly`, `SameSite=Lax`, `Secure` over HTTPS (V3.4); new session id at sign-in (V3.2.1); sessions ended on password change (V3.3.3) | `CookieSecure=true`; shorter `SessionTimeout` if your policy asks |
| V4 Access control | deny by default through policies; module views need a policy unless listed in `[RoleSettings] PolicyOmitList[]` | review `PolicyOmitList[]` additions by extensions |
| V5 Validation, sanitization and encoding | `wash` operator; image uploads checked by content against `AllowedMIMETypes[]`; SVG sanitizer | review templates of your design for `|wash` |
| V7 Error handling and logging | audit without passwords or tokens; log viewer masks e-mail and IP addresses and secrets | keep `DebugOutput=disabled` |
| V12 Files and resources | no PHP execution below the root by the shipped rewrite rules; downloads through `content/download` | keep the rewrite rules current after upgrades |
| V13 API | REST tokens from `random_bytes()` | limit OAuth clients and API keys |
| V14 Configuration | security headers, CSP, debug restricted, file manifest | enforce CSP on the administration |

## The level 1 gap you can close

ASVS 4.0.3 V2.1.1 asks for passwords of at least 12 characters; Exponential ships `MinPasswordLength=10`. Set it in
`settings/override/site.ini.append.php`:

```ini
[UserSettings]
MinPasswordLength=12
```

Raising it locks nobody out: existing passwords keep working and the rule applies when a password is next set
([13.6 Passwords and sign-in](../../../install/13-security-hardening.md#136-passwords-and-sign-in)). Note that the
setting counts bytes. Many organizations choose 15, following NIST SP 800-63B-4 as the installation guide explains.

This closes one known gap; it does not make the installation ASVS level 1 compliant, because nobody has verified the
other requirements.

## Planning a verification

1. Choose the level: level 1 for all public sites, level 2 for sites with personal accounts or sensitive data, as
   your security policy says.
2. Scope it: your Exponential version, activated extensions, your own extensions and templates, the hosting stack.
3. Use the project's evidence (settings, tests in `tests/tests/kernel/classes/security/`, the hardening notes) as
   input, and test what is yours.
4. Commission a tester for what needs one (see "Penetration tests" in this section).
5. Record the result per requirement; keep the report with your security documentation.

## Related pages

- [Security standards](../../../standards/security.md)
- [Security hardening of 6.0.13](../../../bc/6.0/hardening.md)
- [Security defaults of September 2026](../../../specifications/6.0/security-defaults-2026-09.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
