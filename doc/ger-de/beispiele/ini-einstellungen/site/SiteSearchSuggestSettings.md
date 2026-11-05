---
title: site.ini [SiteSearchSuggestSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SiteSearchSuggestSettings] in site.ini: Enabled, MinLength, Limit, ExcludeClasses, IncludeClasses, ThumbnailAlias, Wildcard, PublicCacheTTL ..."
keywords: Einstellung, site.ini, SiteSearchSuggestSettings, INI, setting, Enabled, MinLength, Limit, ExcludeClasses, IncludeClasses, ThumbnailAlias, Wildcard, PublicCacheTTL, RateLimit, RateWindow
type: generated
generated: exp:help:examples
---
# site.ini [SiteSearchSuggestSettings]

Der Block `[SiteSearchSuggestSettings]` von `settings/site.ini`, mit 10 Einstellung(en).

> Vorschläge unter dem Suchfeld im Kopf des Designs einer Site, während der Besucher tippt (das Include  
> design:parts/front\_search\_suggest.tpl im Suchformular des Designs; das Skript javascript/page/search\_suggest.js fragt  
> content/sitesuggest/(text)/<text>, expSearchSuggest::siteSuggest()). Je Siteaccess: Schalten Sie es in der  
> site.ini.append.php des Siteaccess ab. Anleitung: doc/eng-us/help/searching.md („Vorschläge auf der Site“).  
>
> Nur Inhaltsobjekte, die der Benutzer lesen darf, werden vorgeschlagen (der anonyme Benutzer: was öffentlich ist), mit Name, Adresse,  
> Klassenname und einem kleinen Bild; nie Benutzerkonten, Inhaltsklassen, Seiten der Administration oder Einstellungen. Die Ansicht  
> braucht die Richtlinie content/read, wie content/search.  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> true oder false. false: Das Feld ist ein einfaches Suchfeld, content/sitesuggest antwortet mit 404.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
Enabled=true
*/ ?>
```

## MinLength

> Die wenigsten getippten Zeichen, bevor Vorschläge angefragt werden.  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
MinLength=3
*/ ?>
```

## Limit

> Die meisten Vorschläge (0 bis 20).  

Standard: `6`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
Limit=6
*/ ?>
```

## ExcludeClasses

> Klassen, die nie vorgeschlagen werden (neben jeder Klasse mit einem Benutzerkonto-Attribut, die es nie wird).  

Standard: eine Liste mit 2: `user`, `user_group`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
# ein leeres "ExcludeClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExcludeClasses[]
ExcludeClasses[]=user
ExcludeClasses[]=user_group
*/ ?>
```

## IncludeClasses

> Nur diese Klassen werden vorgeschlagen und im Inhaltstyp-Filter der Suchseite angeboten  
> (content/front\_search\_page.tpl); leer: jede Klasse, die oben nicht ausgeschlossen ist. Eine Site, deren Baum Klassen anderer Sites enthält  
> (ein Multisite-Paket), nennt hier ihre eigenen, in der site.ini.append.php des Siteaccess.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
# ein leeres "IncludeClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
IncludeClasses[]
*/ ?>
```

## ThumbnailAlias

> Der Bildalias des kleinen Bildes eines Vorschlags (image.ini), aus dem ersten Bildattribut des Objekts; leer:  
> nur das Klassensymbol.  

Standard: `small`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
ThumbnailAlias=small
*/ ?>
```

## Wildcard

> auto, enabled oder disabled, wie admininterface.ini \[SearchSuggestSettings\] Wildcard.  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
Wildcard=auto
*/ ?>
```

## PublicCacheTTL

> Sekunden, die eine anonyme Antwort aufbewahrt wird (auf dem Server und als Cache-Control: public, max-age), 0 bis 3600. 0: nicht aufbewahrt.  
> Die Antwort an einen angemeldeten Benutzer wird nie aufbewahrt (private, no-store).  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
PublicCacheTTL=60
*/ ?>
```

## RateLimit

> Anfragen einer Adresse innerhalb von RateWindow Sekunden; weitere werden mit 429 und Retry-After beantwortet. 0: keine Grenze.  

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
RateLimit=60
*/ ?>
```

## RateWindow

Standard: `60`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[SiteSearchSuggestSettings]
RateWindow=60
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
