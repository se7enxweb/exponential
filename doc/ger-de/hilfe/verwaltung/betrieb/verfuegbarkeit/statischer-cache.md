---
title: Der statische Cache
audience: government, hosting, admin
category: public-sector
order: 70
summary: Für IT-Teams im öffentlichen Sektor, die gefragt werden, ob sie den statischen Cache von Exponential einschalten sollen. Was er ist, warum er ausgeschaltet ausgeliefert wird und das Installationsbuch rät, ihn ausgeschaltet zu lassen, in welchen seltenen Fällen er noch passt (zum Beispiel eine Notfall-Informationswebsite) und wie Sie ihn erzeugen, ausliefern und entfernen.
keywords: statischer Cache, StaticCache, makestaticcache.php, statische Seiten, var/site/static, Kosten beim Veröffentlichen, Notfall-Website, Rückfallebene, Setup > Cache, staticcache_cleanup, Cache-Status, static cache, static pages, publish cost, emergency site, fallback, cache status
type: help
---
# Der statische Cache

Diese Seite richtet sich an IT-Teams in öffentlichen Stellen, die gebeten werden, „den statischen Cache
einzuschalten“, oft weil eine ältere Anleitung ihn empfohlen hat. Sie beantwortet, was der statische Cache ist, warum
er ausgeschaltet ausgeliefert wird, wann er für eine öffentliche Stelle noch sinnvoll sein kann und wie Sie ihn
erzeugen, ausliefern und entfernen. Die Quellen sind
[Generator für den statischen Cache](../../../../funktionen/6.0/generator-fuer-den-statischen-cache.md) und die Hosting-Seite
[Statischer Cache](../../../hosting/leistung/statischer-cache.md).

## Inhalt

1. [Was er ist](#was-er-ist)
2. [Warum er ausgeschaltet ausgeliefert wird](#warum-er-ausgeschaltet-ausgeliefert-wird)
3. [Wann er noch passen kann](#wann-er-noch-passen-kann)
4. [Erzeugen und prüfen](#erzeugen-und-prüfen)
5. [Ausliefern und ausschalten](#ausliefern-und-ausschalten)
6. [Checkliste](#checkliste)

## Was er ist

Der statische Cache verwandelt öffentliche Seiten in einfache Dateien unter `var/<site>/static`, die der Webserver
sendet, ohne Exponential zu starten. Die Rewrite-Regeln antworten nur bei einem einfachen `GET` oder `HEAD` ohne
Query-String und ohne Sitzung aus einer Datei; Formulare, Suchen und angemeldete Benutzer erhalten immer die
Live-Seite.

## Warum er ausgeschaltet ausgeliefert wird

`site.ini [ContentSettings] StaticCache=disabled` ist der ausgelieferte Wert, und der Installer schreibt ihn so. Das
Installationsbuch sagt: „Lassen Sie den statischen Cache ausgeschaltet.“ Bei eingeschaltetem Cache erzeugte jede
Veröffentlichung die betroffenen Seiten auf jedem öffentlichen Siteaccess über HTTP innerhalb der Anfrage der
Redakteurin oder des Redakteurs neu, sodass das Veröffentlichen langsam wurde; der Antwort-Cache von Velocity bietet
Besuchern denselben Vorteil ohne diese Kosten
([10.12 Leistungsoptimierung](../../../../installation/10-nach-der-installation.md#1012-performance-tuning)).

| | Statischer Cache | Velocity-Antwort-Cache | Rollenbewusster HTTP-Cache |
|---|---|---|---|
| Ausgeliefert | ausgeschaltet | eingeschaltet unter Velocity | ausgeschaltet |
| Ausgeliefert durch | den Webserver, aus einer Datei | den Serverprozess von Velocity | einen frühen Ausstieg oder Velocity |
| Angemeldete Besucher | nie | nie | ja, je Berechtigungskontext |
| Kosten beim Veröffentlichen | Seiten werden in der Anfrage der Redaktion aufgefrischt | keine; Einträge laufen ab | Löschungen, kein erneutes Abrufen |

Aus [Statischer Cache](../../../hosting/leistung/statischer-cache.md); die anderen beiden stehen unter
[Velocity-Antwort-Cache und HTTP-Cache](velocity-antwortcache-und-http-cache.md).

## Wann er noch passen kann

Die Funktionsseite beschreibt ihn als geeignet für „Websites mit überwiegend öffentlichen, sich selten ändernden
Seiten“. Für eine öffentliche Stelle kann das bedeuten:

- eine kleine Informationswebsite, die von Apache oder nginx ausgeliefert wird, wo Velocity und der HTTP-Cache nicht
  verfügbar sind;
- eine Notfall- oder Krisen-Informationswebsite, die bei einem plötzlichen Ansturm weiter antworten muss und bei der
  einige Sekunden zusätzliche Zeit beim Veröffentlichen akzeptabel sind.

Stimmen Sie die Kosten beim Veröffentlichen zuerst mit der Redaktion ab. Mit `[CacheSettings] CronjobCacheClear=enabled`
übernimmt der Cronjob-Teil `staticcache_cleanup` das Leeren statt der Veröffentlichung.

## Erzeugen und prüfen

Sehen Sie sich zuerst an, was er tun würde:

```bash
php bin/php/cache.php static status --allow-root-user
```

Erwartet: eine Liste der Websites, die erzeugt werden können, und eine Zeile wie `the static cache is not enabled in site.ini
[ContentSettings] StaticCache; 3 sites can be generated`. Nur Siteaccesses, die keine Anmeldung brauchen, werden
angeboten. Erzeugen Sie eine Website, begrenzt, damit Sie die Arbeit verfolgen können:

```bash
php bin/php/makestaticcache.php --site=site --max-pages=500 --max-depth=6 --allow-root-user
```

Dasselbe gibt es in der Administration unter **Setup > Cache-Verwaltung**, Abschnitt *Statischer Inhalts-Cache*, mit
einer Schaltfläche *Anhalten* ([Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md)).

## Ausliefern und ausschalten

Damit der Webserver aus den Dateien antwortet, setzen Sie `StaticCache=enabled` in
`settings/override/site.ini.append.php`, Block `[ContentSettings]`, leeren den INI-Cache und prüfen, dass die
Webserver-Regeln Ihrer Installation aktiv sind:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Um zurückzukehren, setzen Sie den Wert auf `disabled`, leeren den INI-Cache erneut und entfernen die erzeugten
Dateien:

```bash
php bin/php/cache.php static clear --allow-root-user
```

Aus Dateien ausgelieferte Seiten umgehen die Anwendung und stehen daher nicht in den Anfrage-Logs von Exponential.
Denken Sie daran bei Statistiken und Untersuchungen.

## Checkliste

- [ ] Der Standard (ausgeschaltet) bleibt erhalten, sofern kein dokumentierter Grund vorliegt.
- [ ] Wenn eingeschaltet: Die Redaktion akzeptiert die Kosten beim Veröffentlichen, oder `CronjobCacheClear` ist
  eingeschaltet.
- [ ] `cache.php static status` zeigt die erwarteten Websites und Seitenzahlen.
- [ ] Das Verfahren zum Ausschalten und zum Leeren der Dateien steht in Ihrem Runbook.

## Verwandte Seiten

- [Verfügbarkeit](README.md): Übersicht dieses Ordners
- [Cache-Aufwärmen nach Änderungen](caches-nach-aenderungen-vorwaermen.md)
- [Cache-Ebenen](../../../hosting/leistung/cache-schichten.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
