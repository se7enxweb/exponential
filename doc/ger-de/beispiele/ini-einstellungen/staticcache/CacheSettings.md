---
title: staticcache.ini [CacheSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CacheSettings] in staticcache.ini: HostName, SourceProtocol, StaticStorageDir, MaxCacheDepth, CachedURLArray, CachedSiteAccesses, AlwaysUpdateArray, CronjobCacheClear ..."
keywords: Einstellung, staticcache.ini, CacheSettings, INI, setting, HostName, SourceProtocol, StaticStorageDir, MaxCacheDepth, CachedURLArray, CachedSiteAccesses, AlwaysUpdateArray, CronjobCacheClear, AppendGeneratedTime
type: generated
generated: exp:help:examples
---
# staticcache.ini [CacheSettings]

Der Block `[CacheSettings]` von `settings/staticcache.ini`, mit 9 Einstellung(en).

> Exponential-Konfigurationsdatei.  
>
> HINWEIS: Es wird nicht empfohlen, diese Datei direkt zu bearbeiten; legen Sie stattdessen  
>       eine Datei in override an, um die Werte zu setzen,  
>       die Ihre Website braucht. Legen Sie entweder  
>       eine Datei settings/override/staticcache.ini.append oder  
>       settings/override/staticcache.ini.append.php an, für mehr Sicherheit  
>       im Modus ohne virtuellen Host (die .php-Datei ist eventuell schon vorhanden  
>       und kann dafür verwendet werden).  

Bearbeiten Sie `settings/staticcache.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/staticcache.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/staticcache.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/staticcache.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## HostName

> Der Host, von dem beim Erzeugen des Caches Seiten geholt werden.  
>
> Lassen Sie dies leer. Es ist seit 4.4 veraltet, und leer ist der richtige  
> Wert: Jeder Siteaccess wird dann von seiner eigenen  
> site.ini.\[SiteSettings\].SiteURL geholt, was eine Multisite-Installation  
> braucht. Jahrelang wurde "localhost" ausgeliefert, was jeden Abruf an das schickte,  
> was der Webserver für diesen Namen antwortet, meist den Platzhalter des Hosting-  
> Panels, nie diese Website, und der Lauf schlug still fehl.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
HostName=
*/ ?>
```

## SourceProtocol

> Das Protokoll, mit dem Seiten geholt werden, http oder https.  
>
> http genügt bei einer Website, die auf https weiterleitet, weil der Abruf  
> Weiterleitungen folgt. Setzen Sie https, wo einfaches http glatt abgelehnt statt  
> weitergeleitet wird.  

Standard: `http`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
SourceProtocol=http
*/ ?>
```

## StaticStorageDir

> Wo die erzeugten Seiten geschrieben werden, relativ zum var-Verzeichnis des  
> Siteaccess, außer der Pfad ist absolut oder beginnt schon mit var/.  
>
> Der Standard ergibt daher var/<site>/static, neben  
> var/<site>/cache und var/<site>/storage. Erzeugte Seiten sind Cache und  
> gehören zu dessen Rest, nicht in das Wurzelverzeichnis der Installation.  

Standard: `static`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
StaticStorageDir=static
*/ ?>
```

## MaxCacheDepth

> URLs mit mehr Segmenten werden nie gecacht und nie aktualisiert,  
> wenn ein Redakteur veröffentlicht. Der alte Standard von 3 erlaubte zwei Pfadsegmente,  
> was den Großteil eines echten Inhaltsbaums ausschließt.  

Standard: `12`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
MaxCacheDepth=12
*/ ?>
```

## CachedURLArray

> Eine Liste der zu cachenden URLs. Mit dem Platzhalter \* schließen Sie einen ganzen  
> Teilbaum ein. Ein bloßes /\* cacht die ganze Website, begrenzt durch MaxCacheDepth.  

Standard: eine Liste mit 2: `/`, `/*`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
# ein leeres "CachedURLArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CachedURLArray[]
CachedURLArray[]=/
CachedURLArray[]=/*
*/ ?>
```

## CachedSiteAccesses

> Eine Liste der Siteaccesses, für die statische Inhalte erzeugt werden.  
>
> Wenn leer, werden die Siteaccesses verwendet, die diese Installation bedient: jeder  
> Eintrag aus site.ini.\[SiteAccessSettings\].RelatedSiteAccessList außer denen,  
> die eine Anmeldung erfordern, deren Seiten pro Benutzer sind und nicht geteilt werden dürfen.  
> Nennen Sie hier Siteaccesses, um das zu überschreiben.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
# ein leeres "CachedSiteAccesses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CachedSiteAccesses[]
*/ ?>
```

## AlwaysUpdateArray

> Eine Liste von Orten, die bei jeder Veröffentlichung eines Objekts aktualisiert werden. Sie  
> können hier KEINE Platzhalter verwenden.  

Standard: eine Liste mit 1: `/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
# ein leeres "AlwaysUpdateArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AlwaysUpdateArray[]
AlwaysUpdateArray[]=/
*/ ?>
```

## CronjobCacheClear

> Die Erzeugung des Caches auf den Cronjob verschieben.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
CronjobCacheClear=disabled
*/ ?>
```

## AppendGeneratedTime

> Steuert, ob der Kommentar <!-- Generated: YYY-MM-DD HH:MM:SS -->  
> an die erzeugte Datei des statischen Caches angehängt wird.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/staticcache.ini.append.php
[CacheSettings]
AppendGeneratedTime=true
*/ ?>
```

## Siehe auch

- [Alle Blöcke von staticcache.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/staticcache.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
