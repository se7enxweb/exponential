---
title: logviewer.ini [SearchSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SearchSettings] in logviewer.ini: MaxBytesPerRequest, TimeLimit, MaxResults, MaxPatternLength, BacktrackLimit, Rotations, MaxGzipBytes, RequestWindow"
keywords: Einstellung, logviewer.ini, SearchSettings, INI, setting, MaxBytesPerRequest, TimeLimit, MaxResults, MaxPatternLength, BacktrackLimit, Rotations, MaxGzipBytes, RequestWindow
type: generated
generated: exp:help:examples
---
# logviewer.ini [SearchSettings]

Der Block `[SearchSettings]` von `settings/logviewer.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/logviewer.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/logviewer.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/logviewer.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/logviewer.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MaxBytesPerRequest

> Die meisten Bytes, die eine Suchanfrage liest; eine Suche über mehr macht mit „Weitere Ergebnisse“ dort weiter, wo sie aufgehört hat.  

Standard: `33554432`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[SearchSettings]
MaxBytesPerRequest=33554432
*/ ?>
```

## TimeLimit

> Wie lange eine Suchanfrage höchstens liest, in Millisekunden (auch das Zeitbudget des regulären Ausdrucks).  

Standard: `2000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[SearchSettings]
TimeLimit=2000
*/ ?>
```

## MaxResults

> Die meisten passenden Einträge, die eine Anfrage zurückgibt.  

Standard: `200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[SearchSettings]
MaxResults=200
*/ ?>
```

## MaxPatternLength

> Der längste Suchtext oder reguläre Ausdruck.  

Standard: `200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[SearchSettings]
MaxPatternLength=200
*/ ?>
```

## BacktrackLimit

> Die Backtracking-Grenze einer Suche mit regulärem Ausdruck (pcre.backtrack\_limit, während sie läuft): Ein Muster, das  
> auf einer Zeile mehr braucht, wird angehalten und als zu aufwendig gemeldet.  

Standard: `100000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[SearchSettings]
BacktrackLimit=100000
*/ ?>
```

## Rotations

> Auch die Rotationen (.1 .. .N und .gz) einer Datei durchsuchen, wenn die Seite danach fragt.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[SearchSettings]
Rotations=enabled
*/ ?>
```

## MaxGzipBytes

> Die meisten Bytes einer komprimierten Rotation, die eine Suche entpackt.  

Standard: `67108864`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[SearchSettings]
MaxGzipBytes=67108864
*/ ?>
```

## RequestWindow

> Die Sekunden vor und nach einem Eintrag, die als dieselbe Anfrage zählen (zugehörige Zeilen).  

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[SearchSettings]
RequestWindow=2
*/ ?>
```

## Siehe auch

- [Alle Blöcke von logviewer.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/logviewer.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
