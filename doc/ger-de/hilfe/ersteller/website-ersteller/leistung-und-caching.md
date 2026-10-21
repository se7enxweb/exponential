---
title: Leistung und Caching
audience: website-creator, admin
category: website-creation
order: 620
summary: Eine Website schnell machen - die Cache-Ebenen vom Browser bis zur Datenbank, die Hebel nach ihrer Wirkung geordnet, Messen mit exp:benchmark und die Regel, Caches in der richtigen Reihenfolge zu leeren.
keywords: Leistung, Geschwindigkeit, Caching, View-Cache, Cache-Block, Antwort-Cache von Velocity, HTTP-Cache, Abfrage-Cache, Benchmark, Vorwärmen, Website-Ersteller, performance, speed, caching, view cache, cache-block, Velocity response cache, HTTP cache, OPcache, query cache, benchmark, exp:benchmark, warm, website creator
type: help
---
# Leistung und Caching

Diese Seite richtet sich an Website-Ersteller, die schnelle Seiten wollen. Exponential cacht auf vielen Ebenen; die
meisten sind standardmäßig eingeschaltet. Diese Seite zeigt die Ebenen, die Hebel nach ihrer Wirkung geordnet und wie
Sie messen ([10.12 Leistungsoptimierung](../../../installation/10-nach-der-installation.md#1012-performance-tuning)).

![Websites vorladen: die Caches der Websites nach dem Leeren vorwärmen](../../../../eng-us/help/images/creators/preload-sites.webp)

## Inhalt

1. [Die Ebenen](#die-ebenen)
2. [Die Hebel, nach Wirkung geordnet](#die-hebel-nach-wirkung-geordnet)
3. [Messen](#messen)
4. [Templates, die schnell bleiben](#templates-die-schnell-bleiben)
5. [Caches in der richtigen Reihenfolge leeren](#caches-in-der-richtigen-reihenfolge-leeren)

## Die Ebenen

| Ebene | Was sie vorhält | Lesen |
|---|---|---|
| Browser, Proxy, CDN | Seiten und statische Dateien, wenn die Header es erlauben | [HTTP-Caching und CDNs](http-caching-und-cdns.md) |
| Antwort-Cache von Velocity | ganze öffentliche Seiten, beantwortet ohne PHP | [Statischer Cache und Velocity](statischer-cache-und-velocity.md) |
| View-Cache für Inhalte | der gerenderte Hauptbereich jeder Seite | [View-Cache und Cache-Blöcke](view-cache-und-cache-bloecke.md) |
| Template-Block-Cache | Ausgabe von `{cache-block}`: Menüs, Fußbereiche | ebenda |
| Kompilierte Templates | `.tpl` zu PHP kompiliert | |
| OPcache | kompiliertes PHP | [10.12](../../../installation/10-nach-der-installation.md#1012-performance-tuning) |
| SQL-Abfrage-Cache | Abfrageergebnisse, optional | [SQL-Abfrage-Cache](../../../bc/6.0/sql-abfrage-cache.md) |

## Die Hebel, nach Wirkung geordnet

Aus dem Installationshandbuch:

1. **Mit Velocity ausliefern** und mit seinem Antwort-Cache; persistente Worker halten die Anwendung warm.
2. **Den View-Cache für Inhalte und den Template-Block-Cache eingeschaltet lassen**
   (`[ContentSettings] ViewCaching=enabled`, `[TemplateSettings] TemplateCache=enabled`, beide mitgeliefert). Wärmen
   Sie die Website nach einem vollständigen Leeren mit `php bin/php/warm.php --allow-root-user` vor.
3. **OPcache**.
4. **APCu** für die Speicherstufe des Antwort-Caches und den gemeinsamen Abfrage-Cache.
5. **Der SQL-Abfrage-Cache** (`querycache.ini`), auf Ihrer Website gemessen.
6. **Die Datenbank** selbst ([Datenbanken](../../../installation/09-datenbanken.md)).

Lassen Sie den statischen Cache aus (`StaticCache=disabled`, der mitgelieferte Wert); der Antwort-Cache bietet Besuchern
denselben Vorteil, ohne das Veröffentlichen zu verlangsamen.

## Messen

```bash
php bin/php/console exp:benchmark --save=var/benchmark/base.json --allow-root-user
# change something
php bin/php/console exp:benchmark --baseline=var/benchmark/base.json --threshold=15 --allow-root-user
```

Es gibt Median, p90 und p99 pro Seite aus. Vergleichen Sie Läufe auf demselben Rechner, direkt hintereinander
([Benchmarking](../../../anleitungen/benchmarking.md)). Messen Sie auch im Browser: Bildgrößen, Anzahl der Anfragen und
Layoutverschiebungen sind oft wichtiger als die Serverzeit.

## Templates, die schnell bleiben

- Holen Sie nur, was Sie anzeigen: `limit`, `class_filter_array`, `load_data_map`, wo angeboten.
- Kein Fetch eines ganzen Teilbaums im Pagelayout ohne Cache-Block.
- Cache-Blöcke mit genau den Schlüsseln, von denen die Ausgabe abhängt.
- Bilder in der richtigen Alias-Größe, mit `width`, `height` und `loading="lazy"`
  ([Bilder und Bildvarianten](bilder-und-bildvarianten.md)).
- Stylesheets und Skripte über den Packer ([Stylesheets und Skripte](stylesheets-und-skripte.md)).

## Caches in der richtigen Reihenfolge leeren

Eine Seite, die zwischen dem Leeren eines Caches und dem Neuladen von PHP von altem Code gerendert wird, wird erneut
gecacht. Also: INI- und Template-Caches, dann PHP neu laden und Velocity neu starten, und erst danach die Inhalts-,
Template-Block- und Antwort-Caches. Unter Velocity erledigt `exp:velocity deploy` das in dieser Reihenfolge
([Die Reihenfolge nach einer Codeänderung](../../../installation/10-nach-der-installation.md#die-reihenfolge-nach-einer-code-änderung)).

| Sie haben geändert | Leeren |
|---|---|
| eine Einstellung | `--clear-tag=ini` |
| ein Template | `--clear-tag=template` |
| ein neues Template oder eine neue Override-Regel | `--clear-id=template-override,design_base` |
| ein Stylesheet oder Skript | `--clear-id=ezjscore-packer,template-block`, dann den Velocity-Cache |
| Inhalte wirken veraltet | `--clear-tag=content` |

## Verwandte Seiten

- [View-Cache und Cache-Blöcke](view-cache-und-cache-bloecke.md)
- [Caches](../../../anleitungen/caches.md)
- [Profiling und Leistung](../anwendungsentwickler/profiling-und-leistung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
