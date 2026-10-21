---
title: pdf.ini [Header]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Header] in pdf.ini: Page, TopMargin, BottomMargin, LeftMargin, RightMargin, LineThicknes, LineMargin, PageOffset"
keywords: Einstellung, pdf.ini, Header, INI, setting, Page, TopMargin, BottomMargin, LeftMargin, RightMargin, LineThicknes, LineMargin, PageOffset
type: generated
generated: exp:help:examples
---
# pdf.ini [Header]

Der Block `[Header]` von `settings/pdf.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/pdf.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/pdf.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/pdf.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/pdf.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Page

Standard: `all`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[Header]
Page=all
*/ ?>
```

## TopMargin

Standard: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[Header]
TopMargin=20
*/ ?>
```

## BottomMargin

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[Header]
BottomMargin=5
*/ ?>
```

## LeftMargin

Standard: `80`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[Header]
LeftMargin=80
*/ ?>
```

## RightMargin

Standard: `80`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[Header]
RightMargin=80
*/ ?>
```

## LineThicknes

Standard: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[Header]
LineThicknes=1
*/ ?>
```

## LineMargin

Standard: `80`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[Header]
LineMargin=80
*/ ?>
```

## PageOffset

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/pdf.ini.append.php
[Header]
PageOffset=0
*/ ?>
```

## Siehe auch

- [Alle Blöcke von pdf.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/pdf.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
