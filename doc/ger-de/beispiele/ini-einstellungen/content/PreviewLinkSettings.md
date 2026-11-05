---
title: content.ini [PreviewLinkSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PreviewLinkSettings] in content.ini: Enabled, DefaultLifetime, MaxLifetime, MaxActivePerVersion"
keywords: Einstellung, content.ini, PreviewLinkSettings, INI, setting, Enabled, DefaultLifetime, MaxLifetime, MaxActivePerVersion
type: generated
generated: exp:help:examples
---
# content.ini [PreviewLinkSettings]

Der Block `[PreviewLinkSettings]` von `settings/content.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Links zum Teilen der Vorschau eines Entwurfs (content/sharedpreview/<token>, expContentPreviewLink): Ein  
> Prüfer ohne Konto sieht diese eine Version über einen signierten, ablaufenden und widerrufbaren Link.  
> Der Signaturschlüssel ist das Geheimnis content.previewlink.key des Secret-Stores (mit dem ersten  
> Link angelegt; es auf Setup > Geheimnisse zu entfernen, beendet jeden Link). Anleitung: doc/eng-us/features/6.0/preview-links.md  
> true oder false  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PreviewLinkSettings]
Enabled=true
*/ ?>
```

## DefaultLifetime

> Sekunden, die ein neuer Link funktioniert, wenn sein Ersteller nichts wählt (ein Tag)  

Standard: `86400`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PreviewLinkSettings]
DefaultLifetime=86400
*/ ?>
```

## MaxLifetime

> Die längste Zeit, die ein Link funktionieren darf, in Sekunden (sieben Tage); mindestens 300  

Standard: `604800`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PreviewLinkSettings]
MaxLifetime=604800
*/ ?>
```

## MaxActivePerVersion

> Links, die für eine Version gleichzeitig funktionieren  

Standard: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[PreviewLinkSettings]
MaxActivePerVersion=20
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
