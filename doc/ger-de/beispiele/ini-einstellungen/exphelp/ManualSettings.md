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

> The manuals built from the Markdown help by ./console exp:help:content build-manuals (doc/eng-us/features/6.0/help-content.md,  
> "Manuals from the Markdown help"). The Markdown stays the source: every run makes the manuals, chapters and topics  
> match the pages again, and an edit made only in the admin is overwritten by the next run. Objects the command did not  
> make (remote\_id not help-gen-...) are never touched.  
> One line per manual, in the order of the manuals: <key>;<source>\[;<title>\]  
>   key     a-z, 0-9 and inner hyphens; the manual's remote\_id is help-gen-manual-<key>  
>   source  a folder below doc/eng-us/: its pages become topics, each subfolder a chapter (and theirs sub-chapters);  
>           <folder>/\*.md: only the pages directly in the folder, no chapters  
>   title   default: the title of the folder's README.md, else its start page ("Adopters: start here" gives  
>           "Adopters"), else the folder's name  
> Changing the scope is an edit of this list. A line taken out removes its manual, chapters and topics at the next run.  

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

> The content languages the manuals are translated into, from the help's pages in that language (doc/ger-de/ for  
> ger-DE). A language the installation does not have is left out and reported, never created.  

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

> The title of a manual whose title Manuals\[\] gives, in a translation: <key>;<locale>;<title> (the others take the  
> title of the translated README or start page)  

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

> The license of the manuals (the documentation's license, doc/eng-us/LICENSE-GFDL-1.3.txt)  

Standard: `GNU Free Documentation License 1.3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ManualSettings]
License=GNU Free Documentation License 1.3
*/ ?>
```

## BuildOnInstall

> enabled: the kickstarter and ./console exp:install queue the build of the manuals after an installation  
> ("exp:help:content build-manuals --queue" on the admin siteaccess): the content job helpmanuals installs the help as  
> content and builds the manuals in the background, in about 20 to 30 minutes, while the site is already in use; the  
> installation does not wait for it. disabled: leave it to the administrator  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ManualSettings]
BuildOnInstall=enabled
*/ ?>
```

## JobBatchSize

> The background job (content job helpmanuals): objects per batch, each batch a transaction of its own, and the pause  
> after each batch in milliseconds, so the site's own writes never wait long  

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

> Objects created and updated per database transaction: more is faster, a failed object rolls back the whole batch  

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
