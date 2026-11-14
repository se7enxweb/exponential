---
title: image.ini [AliasSource_pro]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AliasSource_pro] in image.ini: BaseURL, Credential"
keywords: Einstellung, image.ini, AliasSource_pro, INI, setting, BaseURL, Credential
type: generated
generated: exp:help:examples
---
# image.ini [AliasSource_pro]

Der Block `[AliasSource_pro]` von `settings/image.ini`, mit 2 Einstellung(en).

> Ein Block pro Umgebung, die eine Quelle sein kann (SyncFrom=pro liest \[AliasSource\_pro\]); fügen Sie  
> \[AliasSource\_stage\] und so weiter auf dieselbe Weise hinzu.  

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## BaseURL

> Die Basis-URL dieser Installation, zum Beispiel https://www.example.org (http oder https, ohne Benutzernamen oder  
> Passwort darin, ohne Query-String).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSource_pro]
BaseURL=
*/ ?>
```

## Credential

> Der Name eines Geheimnisses (Setup > Geheimnisse oder env:<VARIABLE>), das "user:password" (Basic  
> Authentication) oder "Bearer <token>" enthält; nie der Wert selbst, der nie in eine Einstellung, das  
> Protokoll oder eine Meldung geschrieben wird. Leer: keine Authentifizierung.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSource_pro]
Credential=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
