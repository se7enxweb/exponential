---
title: exphelp.ini [ManualSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ManualSettings] in exphelp.ini: Manuals, Languages, TranslatedTitles, License, BuildOnInstall, JobBatchSize, JobPause, BatchSize"
keywords: Einstellung, exphelp.ini, ManualSettings, INI, setting, Manuals, Languages, TranslatedTitles, License, BuildOnInstall, JobBatchSize, JobPause, BatchSize
type: generated
generated: exp:help:examples
---
# exphelp.ini [ManualSettings]

Der Block `[ManualSettings]` von `settings/exphelp.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Manuals

> Die Handbücher, die aus der Markdown-Hilfe von ./console exp:help:content build-manuals gebaut werden (doc/eng-us/features/6.0/help-content.md,  
> "Manuals from the Markdown help"). Das Markdown bleibt die Quelle: jeder Lauf bringt die Handbücher, Kapitel und Themen  
> wieder in Übereinstimmung mit den Seiten, und eine nur in der Administration gemachte Änderung wird vom nächsten Lauf überschrieben. Objekte, die der Befehl nicht  
> erzeugt hat (remote\_id nicht help-gen-...), werden nie angefasst.  
> Eine Zeile pro Handbuch, in der Reihenfolge der Handbücher: <key>;<source>\[;<title>\]  
>   key     a-z, 0-9 und innere Bindestriche; die remote\_id des Handbuchs ist help-gen-manual-<key>  
>   source  ein Ordner unterhalb von doc/eng-us/: seine Seiten werden Themen, jeder Unterordner ein Kapitel (und deren Unterordner Unterkapitel);  
>           <folder>/\*.md: nur die Seiten direkt im Ordner, keine Kapitel  
>   title   Standard: der Titel der README.md des Ordners, sonst seine Startseite ("Adopters: start here" ergibt  
>           "Adopters"), sonst der Name des Ordners  
> Den Umfang zu ändern heißt, diese Liste zu bearbeiten. Eine entfernte Zeile entfernt ihr Handbuch, ihre Kapitel und Themen beim nächsten Lauf.  

Standard: eine Liste mit 53: `getting-started;help/*.md;Getting started`, `manual;help/manual`, `book-learning;help/book-learning`, `book-building-a-site;help/book-building-a-site`, `extension-development;help/extension-development`, `adopters;help/adopters`, `advocates;help/advocates`, `ai-agents;help/ai-agents`, `build-two-ways;help/build-two-ways`, `bundlers;help/bundlers`, `business;help/business`, `businesses;help/businesses` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ManualSettings]
# ein leeres "Manuals[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Manuals[]
Manuals[]=getting-started;help/*.md;Getting started
Manuals[]=manual;help/manual
Manuals[]=book-learning;help/book-learning
Manuals[]=book-building-a-site;help/book-building-a-site
Manuals[]=extension-development;help/extension-development
Manuals[]=adopters;help/adopters
Manuals[]=advocates;help/advocates
Manuals[]=ai-agents;help/ai-agents
Manuals[]=build-two-ways;help/build-two-ways
Manuals[]=bundlers;help/bundlers
Manuals[]=business;help/business
Manuals[]=businesses;help/businesses
Manuals[]=cms-developers;help/cms-developers
Manuals[]=community;help/community
Manuals[]=creators;help/creators
Manuals[]=customers;help/customers
Manuals[]=database-administrators;help/database-administrators
Manuals[]=designers;help/designers
Manuals[]=dxp-developers;help/dxp-developers
Manuals[]=e-commerce;help/e-commerce
# ... 33 weitere in der ausgelieferten Datei
*/ ?>
```

## Languages

> Die Inhaltssprachen, in die die Handbücher übersetzt werden, aus den Seiten der Hilfe in dieser Sprache (doc/ger-de/ für  
> ger-DE). Eine Sprache, die die Installation nicht hat, wird ausgelassen und gemeldet, nie angelegt.  

Standard: eine Liste mit 1: `ger-DE`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ManualSettings]
# ein leeres "Languages[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Languages[]
Languages[]=ger-DE
*/ ?>
```

## TranslatedTitles

> Der Titel eines Handbuchs, dessen Titel Manuals\[\] angibt, in einer Übersetzung: <key>;<locale>;<title> (die anderen übernehmen den  
> Titel der übersetzten README oder Startseite)  

Standard: eine Liste mit 2: `getting-started;ger-DE;Erste Schritte`, `contributor-notes;ger-DE;Hinweise für Mitwirkende`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ManualSettings]
# ein leeres "TranslatedTitles[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TranslatedTitles[]
TranslatedTitles[]=getting-started;ger-DE;Erste Schritte
TranslatedTitles[]=contributor-notes;ger-DE;Hinweise für Mitwirkende
*/ ?>
```

## License

> Die Lizenz der Handbücher (die Lizenz der Dokumentation, doc/eng-us/LICENSE-GFDL-1.3.txt)  

Standard: `GNU Free Documentation License 1.3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ManualSettings]
License=GNU Free Documentation License 1.3
*/ ?>
```

## BuildOnInstall

> enabled: der Kickstarter und ./console exp:install reihen nach einer Installation den Build der Handbücher ein  
> ("exp:help:content build-manuals --queue" im Admin-Siteaccess): der Inhalts-Job helpmanuals installiert die Hilfe als  
> Inhalt und baut die Handbücher im Hintergrund, in etwa 20 bis 30 Minuten, während die Website schon in Gebrauch ist; die  
> Installation wartet nicht darauf. disabled: dem Administrator überlassen  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ManualSettings]
BuildOnInstall=enabled
*/ ?>
```

## JobBatchSize

> Der Hintergrund-Job (Inhalts-Job helpmanuals): Objekte pro Batch, jeder Batch eine eigene Transaktion, und die Pause  
> nach jedem Batch in Millisekunden, damit die eigenen Schreibvorgänge der Website nie lange warten  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ManualSettings]
JobBatchSize=10
*/ ?>
```

## JobPause

Standard: `200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ManualSettings]
JobPause=200
*/ ?>
```

## BatchSize

> Pro Datenbanktransaktion angelegte und aktualisierte Objekte: mehr ist schneller, ein fehlgeschlagenes Objekt macht den ganzen Batch rückgängig  

Standard: `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ManualSettings]
BatchSize=50
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
