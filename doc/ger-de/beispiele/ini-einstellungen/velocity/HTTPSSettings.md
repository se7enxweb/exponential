---
title: velocity.ini [HTTPSSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [HTTPSSettings] in velocity.ini: Enabled, Certificate, Key, HSTSMaxAge, HSTSIncludeSubDomains, HSTSPreload"
keywords: Einstellung, velocity.ini, HTTPSSettings, INI, setting, Enabled, Certificate, Key, HSTSMaxAge, HSTSIncludeSubDomains, HSTSPreload
type: generated
generated: exp:help:examples
---
# velocity.ini [HTTPSSettings]

Der Block `[HTTPSSettings]` von `settings/velocity.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/velocity.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/velocity.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/velocity.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/velocity.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> manual verwendet das Zertifikat und den Schlüssel unten. Lassen Sie Enabled=false, um nur  
> einfaches HTTP auszuliefern.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[HTTPSSettings]
Enabled=false
*/ ?>
```

## Certificate

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[HTTPSSettings]
Certificate=
*/ ?>
```

## Key

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[HTTPSSettings]
Key=
*/ ?>
```

## HSTSMaxAge

> Strict-Transport-Security bei jeder über HTTPS gesendeten Antwort: statische Dateien,  
> die eigenen Seiten des Servers und die Antworten der Skripte, sofern eine Antwort nicht  
> schon einen trägt. Nie über einfaches HTTP. In Sekunden; 0 sendet keinen.  
>
> 300 ist das, was die Frontends Apache und nginx dieser Installation senden. Ein  
> Browser, der ihn gesehen hat, verweigert einfaches HTTP für diesen Hostnamen, auf jedem  
> Port, so lange, und tut das auch weiter, nachdem die Einstellung gesenkt wurde: Erhöhen Sie  
> ihn (ein Jahr sind 31536000) erst, wenn jeder Name der Website dauerhaft über  
> HTTPS ausgeliefert wird.  

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[HTTPSSettings]
HSTSMaxAge=300
*/ ?>
```

## HSTSIncludeSubDomains

> Auch für jede Subdomain des Hosts (enabled oder disabled).  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[HTTPSSettings]
HSTSIncludeSubDomains=disabled
*/ ?>
```

## HSTSPreload

> Darum bitten, in die Preload-Listen der Browser aufgenommen zu werden (enabled oder disabled). Braucht  
> HSTSIncludeSubDomains und ein max-age von mindestens einem Jahr, um akzeptiert zu werden.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/velocity.ini.append.php
[HTTPSSettings]
HSTSPreload=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von velocity.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/velocity.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
