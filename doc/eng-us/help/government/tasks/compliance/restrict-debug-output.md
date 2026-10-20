---
title: Restrict debug output
audience: government, admin, developer
category: public-sector
order: 350
summary: Step by step - make sure Exponential's debug output is off in production, and when a live problem needs it, switch it on for your own address only, then remove it again and check that nothing is shown to visitors.
keywords: debug output, DebugOutput, DebugByIP, DebugIPList, template debug, information disclosure, production, display_errors, audit system.setting.write, public sector
type: help
---
# Restrict debug output

For administrators and developers. Debug output shows SQL, file paths and template names: never for visitors of a public
site ([Debug output in production](../../security/debug-output-in-production.md)).

## Contents

1. [Check it is off](#check-it-is-off)
2. [Debug for your address only](#debug-for-your-address-only)
3. [Remove it again](#remove-it-again)
4. [Related pages](#related-pages)

## Check it is off

```bash
./console exp:ini get site.ini/DebugSettings/DebugOutput --allow-root-user        # disabled
./console exp:ini get site.ini/TemplateSettings/Debug --allow-root-user           # disabled
./console exp:ini where site.ini/DebugSettings/DebugOutput --allow-root-user      # which files set it
```

Also check PHP: `display_errors=Off` and `expose_php=Off` for PHP-FPM.

## Debug for your address only

1. In `settings/override/site.ini.append.php`:

   ```ini
   [DebugSettings]
   DebugOutput=enabled
   DebugByIP=enabled
   DebugIPList[]
   DebugIPList[]=203.0.113.10
   ```

   Behind a proxy, `DebugByIP` uses the visitor address only from trusted proxies; never list the proxy's own address.
2. Clear the INI cache and reload PHP.
3. Reproduce the problem; consider finding the error by its reference in the log viewer instead
   ([Finding an error by its reference](../../../../guides/finding-an-error-by-its-reference.md)).

## Remove it again

Set `DebugOutput=disabled` (or remove the lines), clear the INI cache, reload. Then from another network:

```bash
curl -s https://www.example.gov/ | grep -c -i 'debug'
```

The audit records each setting write; `/audit/console/(name)/system.setting.write` shows forgotten debug switches.

## Related pages

- [Debug output in production](../../security/debug-output-in-production.md)
- [The Exp Debug bar](../../../../bc/6.0/debug-bar.md)
- [13.4 Debug output and error display](../../../../install/13-security-hardening.md#134-debug-output-and-error-display)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
