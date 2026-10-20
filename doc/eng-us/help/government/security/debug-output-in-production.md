---
title: Debug output in production
audience: government, admin, developer
category: public-sector
order: 350
summary: For administrators of public sites - why debug output must stay off in production, Exponential's debug settings and their safe defaults, how to debug a live problem for your own address only and remove it again, and PHP's own error display.
keywords: debug output, DebugOutput, DebugByIP, DebugIPList, DebugByUser, template debug, display_errors, expose_php, error.log, debug bar, information disclosure, production, TrustedProxies, public sector
type: help
---
# Debug output in production

This page is for administrators and developers. Debug reports show SQL, file paths, template names and timings: useful
while developing, a map for an attacker on a public site ([13.4 Debug output and error display](../../../install/13-security-hardening.md#134-debug-output-and-error-display)).

## Contents

1. [The safe defaults](#the-safe-defaults)
2. [Debugging a live problem](#debugging-a-live-problem)
3. [PHP itself](#php-itself)
4. [Checking](#checking)
5. [Related pages](#related-pages)

## The safe defaults

`settings/site.ini [DebugSettings]` as shipped:

| Setting | Shipped | Meaning |
|---|---|---|
| `DebugOutput` | `disabled` | no debug report on pages |
| `AlwaysLog[]` | `error` | errors still written to the error log |
| `ScriptDebugOutput` | `disabled` | command-line scripts |
| `DebugByIP` | `disabled` | when enabled: only `DebugIPList[]` addresses get debug |
| `DebugByUser` | `disabled` | when enabled: only `DebugUserIDList[]` users |
| `DisplayDebugWarnings` | `disabled` | |

and `[TemplateSettings] Debug=disabled` (no template names in the HTML).

## Debugging a live problem

Only when you must, only for your own address, and remove it again the same day:

```ini
# settings/override/site.ini.append.php
[DebugSettings]
DebugOutput=enabled
DebugByIP=enabled
DebugIPList[]
DebugIPList[]=203.0.113.10
```

- Entries may be single addresses or CIDR ranges, IPv4 and IPv6; the debug bar documents labels and expiry dates on
  entries ([The Exp Debug bar](../../../bc/6.0/debug-bar.md)).
- `DebugByIP` trusts the address Exponential determines; behind a proxy it comes from a forwarded header only when the
  proxy is in `TrustedProxies[]`. A range wider than your proxies, or an entry for the proxy's own address, gives debug
  output to everyone.
- The debug bar's settings tab can change these values and needs `setup/setup`.
- Setting writes are recorded in the audit (`system.setting.write`), so a forgotten debug switch can be found:
  `/audit/console/(name)/system.setting.write`.

A safer alternative for many problems: find the error by its reference in the log viewer
([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)).

## PHP itself

In the `php.ini` of PHP-FPM: `display_errors=Off`, `log_errors=On`, `expose_php=Off`. Velocity ships
`display_errors=Off` and `log_errors=On` for FrankenPHP; for the qbix engine add them to
`settings/override/velocity.ini.append.php` `[PHPSettings] IniOptions[]` if your CLI `php.ini` displays errors.

## Checking

```bash
./console exp:ini get site.ini/DebugSettings/DebugOutput --allow-root-user
curl -s https://www.example.gov/ | grep -i -c 'debug'          # from an address not in the list
curl -sI https://www.example.gov/ | grep -i x-powered-by        # nothing with expose_php=Off
```

Add `DebugOutput=disabled` to your go-live checklist and your monthly review.

## Related pages

- [13.4 Debug output and error display](../../../install/13-security-hardening.md#134-debug-output-and-error-display)
- [Security and audit, 4](../../../guides/security-and-audit.md#4-debug-output-for-your-address-only-5-minutes)
- [Proxies and trusted headers](proxies-and-trusted-headers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
