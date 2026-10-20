---
title: exphelp.ini [SearchSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SearchSettings] in exphelp.ini: ResultLimit, TitleBoost, HeadingBoost, BodyBoost, PathBoost, KeywordBoost, TitleMatchBoost, TypeBoost ..."
keywords: Einstellung, exphelp.ini, SearchSettings, INI, setting, ResultLimit, TitleBoost, HeadingBoost, BodyBoost, PathBoost, KeywordBoost, TitleMatchBoost, TypeBoost, VersionBoost, AudienceBoost, StopWords
type: generated
generated: exp:help:examples
---
# exphelp.ini [SearchSettings]

Der Block `[SearchSettings]` von `settings/exphelp.ini`, mit 11 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ResultLimit

> Ergebnisse pro Seite (die JSON-Ansicht nimmt limit=, höchstens 50)  

Standard: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SearchSettings]
ResultLimit=20
*/ ?>
```

## TitleBoost

> Gewichte eines Treffers im Titel, in einer Überschrift und im Text  

Standard: `8`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SearchSettings]
TitleBoost=8
*/ ?>
```

## HeadingBoost

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SearchSettings]
HeadingBoost=3
*/ ?>
```

## BodyBoost

Standard: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SearchSettings]
BodyBoost=1
*/ ?>
```

## PathBoost

> ... im Pfad der Seite (features/6.0/maintenance-mode.md: "maintenance", "mode") und in den Schlüsselwörtern ihres Front Matter  

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SearchSettings]
PathBoost=2
*/ ?>
```

## KeywordBoost

Standard: `4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SearchSettings]
KeywordBoost=4
*/ ?>
```

## TitleMatchBoost

> Faktor für eine Seite, deren Titel jedes Wort der Anfrage enthält  

Standard: `1.4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SearchSettings]
TitleMatchBoost=1.4
*/ ?>
```

## TypeBoost

> Faktor pro Seitentyp (Typen von expHelpDocumentIndex): Hilfeseiten und Anleitungen stehen über Referenzmaterial mit  
> denselben Wörtern, Changelogs und Historie darunter  
> Die generierten Referenzbeispiele (doc/eng-us/examples/, ./console exp:help:examples): Tausende kurzer Seiten, die  
> Namen von Einstellungen, Operatoren und Ansichten wiederholen; unter den geschriebenen Seiten, die dieselben Wörter erklären  
> der generierte Musterkatalog (doc/eng-us/standards/patterns/) listet Tausende Klassennamen auf: unter den geschriebenen Seiten  

Standard: ein Hash mit 7: `help=1.5`, `guide=1.3`, `specification=0.8`, `changelog=0.45`, `history=0.6`, `generated=0.5`, `catalogue=0.5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SearchSettings]
# ein leeres "TypeBoost[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TypeBoost[]
TypeBoost[help]=1.5
TypeBoost[guide]=1.3
TypeBoost[specification]=0.8
TypeBoost[changelog]=0.45
TypeBoost[history]=0.6
TypeBoost[generated]=0.5
TypeBoost[catalogue]=0.5
*/ ?>
```

## VersionBoost

> Faktor pro Hauptversion für Seiten unter einem Versionsverzeichnis (features/3.8/, bc/4.1/): ältere Releases stehen weiter unten  

Standard: ein Hash mit 3: `3=0.5`, `4=0.6`, `5=0.75`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SearchSettings]
# ein leeres "VersionBoost[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
VersionBoost[]
VersionBoost[3]=0.5
VersionBoost[4]=0.6
VersionBoost[5]=0.75
*/ ?>
```

## AudienceBoost

> Faktor pro Zielgruppe für die Seiten, die für sie sind (der niedrigste, wenn eine Seite mehrere hat); nicht angewendet, solange die  
> Suche auf diese Zielgruppe gefiltert ist. Die Lektionen der Trainer wiederholen die Themen der Seiten für Redakteure und Administratoren,  
> sie stehen also in einer allgemeinen Suche darunter und für sich allein unter dem Filter Trainer.  

Standard: ein Hash mit 1: `trainer=0.6`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SearchSettings]
# ein leeres "AudienceBoost[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AudienceBoost[]
AudienceBoost[trainer]=0.6
*/ ?>
```

## StopWords

> Wörter, die nicht indiziert werden (eine leere Liste behält die eingebaute englische und deutsche Liste)  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SearchSettings]
# ein leeres "StopWords[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
StopWords[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
