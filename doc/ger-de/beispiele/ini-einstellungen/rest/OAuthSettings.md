---
title: rest.ini [OAuthSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [OAuthSettings] in rest.ini: TokenTTL, AuthorizeRequiresPolicy"
keywords: Einstellung, rest.ini, OAuthSettings, INI, setting, TokenTTL, AuthorizeRequiresPolicy
type: generated
generated: exp:help:examples
---
# rest.ini [OAuthSettings]

Der Block `[OAuthSettings]` von `settings/rest.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/rest.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rest.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rest.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rest.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TokenTTL

> OAuth token/authcode time to live, in seconds  
> Default is one hour  

Standard: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
TokenTTL=3600
*/ ?>
```

## AuthorizeRequiresPolicy

> enabled or disabled. Enabled (the default): a signed in user may authorize an OAuth client at oauth/authorize only  
> with the policy oauth/authorize (optionally limited by siteaccess); without it the client gets error=access\_denied.  
> disabled: every signed in user may authorize clients, as before 6.0.15. Use it only for a short time during an  
> upgrade, until "php bin/php/addoauthauthorizepolicy.php" has granted the policy (doc/eng-us/bc/6.0/oauth-authorize-policy.md).  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rest.ini.append.php
[OAuthSettings]
AuthorizeRequiresPolicy=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von rest.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rest.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
