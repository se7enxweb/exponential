---
title: site.ini [SignInSecondFactorSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SignInSecondFactorSettings] in site.ini: Handlers, CodeHeader"
keywords: Einstellung, site.ini, SignInSecondFactorSettings, INI, setting, Handlers, CodeHeader
type: generated
generated: exp:help:examples
---
# site.ini [SignInSecondFactorSettings]

Der Block `[SignInSecondFactorSettings]` von `settings/site.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Handlers

> The second sign-in step on the paths that check a password without the web login form:  
> the REST interface with basic authentication, WebDAV, SOAP and the expservices login.  
> A two-step sign-in extension (sevenx\_authentication\_2fa) adds its class here; it then  
> decides whether a password alone is enough for an account, and which app passwords  
> (made by the user on the extension's setup page) sign in instead. Without a class  
> nothing changes. Console commands are never asked.  
> Guide: doc/eng-us/features/6.0/second-factor-on-other-sign-in-paths.md  
>
> Classes implementing expSignInSecondFactorHandler (the extension's settings add theirs).  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SignInSecondFactorSettings]
# ein leeres "Handlers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Handlers[]
*/ ?>
```

## CodeHeader

> The request header that carries the code of an authenticator app, where the extension  
> accepts one (the expservices login also reads the POST field otp).  

Standard: `X-Exp-OTP`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SignInSecondFactorSettings]
CodeHeader=X-Exp-OTP
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
