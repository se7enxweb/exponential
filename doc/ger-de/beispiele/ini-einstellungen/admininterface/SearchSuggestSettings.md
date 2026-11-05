---
title: admininterface.ini [SearchSuggestSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SearchSuggestSettings] in admininterface.ini: Enabled, MinLength, Groups, Limits, PagesPerDocument, PageHitClasses, Wildcard, AdminPagesCacheTTL ..."
keywords: Einstellung, admininterface.ini, SearchSuggestSettings, INI, setting, Enabled, MinLength, Groups, Limits, PagesPerDocument, PageHitClasses, Wildcard, AdminPagesCacheTTL, AdminMenuSkipParts
type: generated
generated: exp:help:examples
---
# admininterface.ini [SearchSuggestSettings]

Der Block `[SearchSuggestSettings]` von `settings/admininterface.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/admininterface.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/admininterface.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/admininterface.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/admininterface.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> Vorschläge unter dem Suchfeld im Kopf der Administration (Designs admin und admin4), während der Benutzer tippt:  
> Inhalte, PDF-Seiten, Benutzer, Inhaltsklassen und die eigenen Seiten der Administration, in Gruppen, jeweils nur so weit,  
> wie der Benutzer sie sehen darf, und „Alle Ergebnisse anzeigen“ führt zur vollständigen Suche (content/search). Das Skript  
> (javascript/page/search\_suggest.js) fragt content/suggest/(text)/<text> danach (expSearchSuggest).  
> Anleitung: doc/eng-us/help/searching.md („Vorschläge beim Tippen“).  
>
> Die Ansicht content/suggest braucht die Richtlinie content/read (wie content/search): Ein Benutzer ohne sie bekommt gar keine  
> Vorschläge, nicht einmal die Seiten der Administration, und das Feld ist für ihn ein einfaches Suchfeld.  
> Die Gruppen pages und die Grenzen unten begrenzen auch die Last: eine Anfrage pro Tipppause (250 ms), dazu höchstens  
> Limits\[pages\] Anfragen für die Seiten von Dokumenten.  
>
> true oder false. false: Das Feld ist ein einfaches Suchfeld, content/suggest antwortet mit 404.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
Enabled=true
*/ ?>
```

## MinLength

> Die wenigsten getippten Zeichen, bevor Vorschläge angefragt werden.  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
MinLength=3
*/ ?>
```

## Groups

> Die Gruppen, in der Reihenfolge, in der sie gezeigt werden. Lassen Sie eine weg, um sie abzuschalten:  
>   content  die Inhaltssuche (eZ Find, wenn es die Suchmaschine ist, sonst die Suchmaschine des Kernels), mit den  
>            Leserechten des Benutzers, die die Suche selbst anwendet  
>   pages    die PDF-Seiten, die die Wörter enthalten: nur mit eZ Find und aktiver Erweiterung ezfindpdfdeepsearch;  
>            ohne sie wird ein Dokument als Ganzes vorgeschlagen, in der Gruppe content  
>   users    Benutzerkonten nach Login, E-Mail-Adresse oder Name, nur die, die der Benutzer lesen darf  
>   classes  Inhaltsklassen nach Name oder Bezeichner, nur für einen Benutzer, der das Modul class öffnen darf  
>   admin    die oberen Reiter und die Links ihrer linken Menüs, wie das Design sie für den Benutzer zeichnet („Setup › Cache“)  

Standard: eine Liste mit 5: `content`, `pages`, `users`, `classes`, `admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
# ein leeres "Groups[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Groups[]
Groups[]=content
Groups[]=pages
Groups[]=users
Groups[]=classes
Groups[]=admin
*/ ?>
```

## Limits

> Die meisten Vorschläge pro Gruppe (0 bis 20; 0 schaltet die Gruppe ab).  

Standard: ein Hash mit 5: `content=6`, `pages=3`, `users=3`, `classes=3`, `admin=4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
# ein leeres "Limits[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Limits[]
Limits[content]=6
Limits[pages]=3
Limits[users]=3
Limits[classes]=3
Limits[admin]=4
*/ ?>
```

## PagesPerDocument

> Die Seiten eines Dokuments, höchstens (Gruppe pages).  

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
PagesPerDocument=2
*/ ?>
```

## PageHitClasses

> Die Klassen, deren Objekte Dokumente mit Seiten sind (Gruppe pages): die Dateiklasse der Binärdateien.  

Standard: eine Liste mit 1: `file`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
# ein leeres "PageHitClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PageHitClasses[]
PageHitClasses[]=file
*/ ?>
```

## Wildcard

> Ein „\*“ nach dem zuletzt getippten Wort, damit ein Wort schon beim Tippen gefunden wird („Oste“ findet „Ostern“):  
>   auto      nur wenn site.ini \[SearchSettings\] EnableWildcard=true (beide Suchmaschinen beachten es nur dann),  
>             und nur für ein Wort mit MinCharacterWildcard oder mehr Zeichen  
>   enabled   immer (für ein Wort mit MinCharacterWildcard oder mehr Zeichen)  
>   disabled  nie: Nur ganze Wörter werden gefunden  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
Wildcard=auto
*/ ?>
```

## AdminPagesCacheTTL

> Die Seiten der Administration für einen Benutzer werden einmal aus den Menüs ermittelt und so viele Sekunden aufbewahrt, je Benutzer  
> und Rechten (Cache-Eintrag searchsuggest, geleert mit den Template-Caches). 0: bei jeder Anfrage ermittelt.  

Standard: `300`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
AdminPagesCacheTTL=300
*/ ?>
```

## AdminMenuSkipParts

> Navigationsteile, deren linkes Menü keine Liste von Seiten enthält (ein Baum): Nur ihr Reiter wird vorgeschlagen.  

Standard: eine Liste mit 2: `ezcontentnavigationpart`, `ezmedianavigationpart`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[SearchSuggestSettings]
# ein leeres "AdminMenuSkipParts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AdminMenuSkipParts[]
AdminMenuSkipParts[]=ezcontentnavigationpart
AdminMenuSkipParts[]=ezmedianavigationpart
*/ ?>
```

## Siehe auch

- [Alle Blöcke von admininterface.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/admininterface.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
