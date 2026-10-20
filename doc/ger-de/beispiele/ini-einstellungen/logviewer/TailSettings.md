---
title: logviewer.ini [TailSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TailSettings] in logviewer.ini: InitialBytes, MaxBytesPerPoll, PollInterval, MaxPollInterval, MaxLinesKept, WrapLines, ChunkCacheTTL, ChunkCacheMaxBytes"
keywords: Einstellung, logviewer.ini, TailSettings, INI, setting, InitialBytes, MaxBytesPerPoll, PollInterval, MaxPollInterval, MaxLinesKept, WrapLines, ChunkCacheTTL, ChunkCacheMaxBytes
type: generated
generated: exp:help:examples
---
# logviewer.ini [TailSettings]

Der Block `[TailSettings]` von `settings/logviewer.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/logviewer.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/logviewer.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/logviewer.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/logviewer.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## InitialBytes

> Wie viel einer Datei die Seite beim Öffnen zeigt: die letzten InitialBytes Bytes, ab der ersten vollständigen Zeile.  

Standard: `65536`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[TailSettings]
InitialBytes=65536
*/ ?>
```

## MaxBytesPerPoll

> Die meisten Bytes, die eine Abfrage liest (eine Datei, die um mehr gewachsen ist, wird über die nächsten Abfragen eingeholt).  

Standard: `262144`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[TailSettings]
MaxBytesPerPoll=262144
*/ ?>
```

## PollInterval

> Das Abfrageintervall der Seite in Millisekunden, solange Zeilen ankommen.  

Standard: `1000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[TailSettings]
PollInterval=1000
*/ ?>
```

## MaxPollInterval

> Solange nichts ankommt, verdoppelt sich das Intervall nach jeder leeren Abfrage, bis zu MaxPollInterval Millisekunden.  

Standard: `15000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[TailSettings]
MaxPollInterval=15000
*/ ?>
```

## MaxLinesKept

> Die meisten Zeilen, die die Seite behält (ältere Zeilen fallen von der Seite, nie aus der Datei).  

Standard: `5000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[TailSettings]
MaxLinesKept=5000
*/ ?>
```

## WrapLines

> Ob lange Zeilen für einen Betrachter umgebrochen werden, der noch nicht gewählt hat. Der Schalter „Zeilen umbrechen“ der Seite speichert die  
> Wahl jedes Betrachters als Benutzereinstellung admin\_logviewer\_wrap (1 oder 0), für jedes Log und jeden Besuch.  
> enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[TailSettings]
WrapLines=enabled
*/ ?>
```

## ChunkCacheTTL

> Der Server behält den zuletzt gelesenen Abschnitt jeder Datei so viele Sekunden, sodass mehrere Betrachter derselben Datei  
> und eine Abfrage, deren Datei sich nicht geändert hat, beantwortet werden, ohne sie zu öffnen. 0 schaltet den Cache ab.  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[TailSettings]
ChunkCacheTTL=10
*/ ?>
```

## ChunkCacheMaxBytes

> Die meisten Bytes eines Abschnitts, die der Cache behält.  

Standard: `262144`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[TailSettings]
ChunkCacheMaxBytes=262144
*/ ?>
```

## Siehe auch

- [Alle Blöcke von logviewer.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/logviewer.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
