---
title: Statischer Cache
audience: hosting, admin
category: hosting
order: 100
summary: Der statische Cache für Hoster - Seiten, die als einfache Dateien erzeugt und vom Webserver ohne PHP ausgeliefert werden, warum er ausgeschaltet ausgeliefert wird und das Installationsbuch sagt, ihn auszulassen, wann ein Kunde ihn trotzdem wollen könnte, wie Sie ihn auf Apache erzeugen und ausliefern, und die Einstellungen.
keywords: statischer Cache, StaticCache, staticcache.ini, makestaticcache.php, .htaccess_root_static, staticcache_offload.conf, EZ_VAR_SITE, CachedURLArray, MaxCacheDepth, CachedSiteAccesses, FetchConcurrency, staticcache_cleanup, Kosten beim Veröffentlichen, static cache, publish cost
type: help
---
# Statischer Cache

Diese Seite ist für Systemadministratoren bei Hosting-Anbietern, die nach dem statischen Cache gefragt werden oder ihn in alten
Hosting-Anleitungen erwähnt sehen. Sie beantwortet: was er ist, warum er ausgeschaltet ausgeliefert wird, wann er noch sinnvoll ist und
wie Sie ihn erzeugen und ausliefern, wenn ein Kunde ihn braucht. Die Quellen sind
[Generator für den statischen Cache](../../../funktionen/6.0/generator-fuer-den-statischen-cache.md),
[Standardwerte des statischen Caches](../../../bc/6.0/standardwerte-des-statischen-caches.md) und
[Kapitel 10, Abschnitt 10.12](../../../installation/10-nach-der-installation.md#1012-performance-tuning).

## Inhalt

1. [Was er ist](#was-er-ist)
2. [Warum er ausgeschaltet ausgeliefert wird](#warum-er-ausgeschaltet-ausgeliefert-wird)
3. [Wann ein Kunde ihn trotzdem wollen könnte](#wann-ein-kunde-ihn-trotzdem-wollen-könnte)
4. [Ihn erzeugen](#ihn-erzeugen)
5. [Ihn vom Webserver ausliefern](#ihn-vom-webserver-ausliefern)
6. [Einstellungen](#einstellungen)
7. [Checkliste](#checkliste)

## Was er ist

Der statische Cache verwandelt öffentliche Seiten in einfache Dateien unter `var/<site>/static`, die der Webserver sendet, ohne
Exponential überhaupt zu starten. Rewrite-Regeln beantworten eine Anfrage aus der Datei, wenn sie ein einfaches `GET` oder `HEAD` ist,
keinen Query-String hat und keine Sitzung trägt; Formulare, Suchen und alle Angemeldeten bekommen immer die Live-Seite.

## Warum er ausgeschaltet ausgeliefert wird

`site.ini [ContentSettings] StaticCache=disabled` ist der mitgelieferte Wert, und der Installer schreibt ihn so. Das Installationsbuch
ist direkt: **Lassen Sie den statischen Cache aus.** Mit eingeschaltetem statischem Cache erzeugte jede Veröffentlichung jede betroffene
Seite auf jedem öffentlichen Siteaccess über HTTP innerhalb der Anfrage des Redakteurs neu, sodass eine Veröffentlichung, die nichts
änderte, Sekunden statt eines Bruchteils davon dauerte. Der Antwort-Cache von Velocity gibt Besuchern denselben Nutzen ohne diese Kosten
([Kapitel 10, Abschnitt 10.12](../../../installation/10-nach-der-installation.md#1012-performance-tuning)).

Die Arbeit an 6.0 hat das Auffrischen günstiger gemacht (jede Seite einmal abgerufen, mehrere gleichzeitig: eine Veröffentlichung ohne
Änderung, die 24 Seiten in 4975 ms auffrischte, frischt jetzt 14 Seiten in etwa 0,5 s auf), aber die Kosten fallen weiterhin in der
Anfrage des Redakteurs an
([Generator für den statischen Cache](../../../funktionen/6.0/generator-fuer-den-statischen-cache.md#erneuern-beim-veröffentlichen-jede-einmal-parallel-01102026)).

| | Statischer Cache | Antwort-Cache von Velocity | Rollenbewusster HTTP-Cache |
|---|---|---|---|
| Ausgeliefert | aus | an unter Velocity | aus |
| Ausgeliefert von | dem Webserver, aus einer Datei | dem Elternprozess von Velocity | einem frühen Ausstieg oder Velocity, vor dem Kernel |
| Angemeldete Besucher | nie | nie | ja, pro Berechtigungskontext |
| Kosten beim Veröffentlichen | Seiten werden über HTTP in der Anfrage des Redakteurs aufgefrischt | keine; Einträge laufen ab | Leeren, kein erneuter Abruf |

## Wann ein Kunde ihn trotzdem wollen könnte

Wo weder Velocity noch der HTTP-Cache verfügbar ist und eine Website fast nur aus öffentlichen Seiten besteht, die sich selten ändern
(die eigene Beschreibung der Seite zur Funktion: Websites mit überwiegend öffentlichen, sich selten ändernden Seiten). Vereinbaren Sie die
Kosten beim Veröffentlichen zuerst mit dem Kunden. Mit `[CacheSettings] CronjobCacheClear=enabled` übernimmt der Cronjob-Teil
`staticcache_cleanup` das Leeren statt der Veröffentlichung.

## Ihn erzeugen

Aus **Setup > Cache > Statischer Inhalts-Cache**: Wählen Sie eine öffentliche Website, eine Seitengrenze (2500, 1 bis 20000) und eine
Linktiefe (12, 0 bis 30), drücken Sie *Neu erstellen* und sehen Sie zu, wie jede Seite gespeichert wird. Von der Kommandozeile:

```bash
php bin/php/makestaticcache.php --help --allow-root-user
php bin/php/makestaticcache.php --site=site --allow-root-user
php bin/php/makestaticcache.php --site=site --max-pages=500 --max-depth=6 --allow-root-user
```

Der Generator durchläuft die Website von ihrer eigenen Wurzel aus und folgt nur den Links, die er schreibt, sodass er genau die Adressen
besucht, die ein Besucher anfragen kann. Ein Lauf leert zuerst die gespeicherten Seiten der Website, es sei denn, `--keep` ist angegeben.
Führen Sie ihn als Benutzer der Website aus, damit die Dateien für den Webserver beschreibbar bleiben.

## Ihn vom Webserver ausliefern

| Einrichtung | Was Sie nutzen |
|---|---|
| Apache mit `.htaccess` | kopieren Sie `.htaccess_root_static` statt `.htaccess_root` nach `.htaccess`; es ist `.htaccess_root` plus die Regeln zur Auslagerung |
| Virtueller Host für Apache | die Regeln aus [doc/eng-us/examples/staticcache_offload.conf](../../../../eng-us/examples/staticcache_offload.conf) im Abschnitt `<Directory>` des vhost der **öffentlichen** Website |
| andere Server | bilden Sie dieselben Bedingungen nach (Methode, leerer Query-String, kein Sitzungscookie, Datei existiert) |

Prüfen Sie die Zeile `RewriteRule ^ - [E=EZ_VAR_SITE:site]`: Der Wert ist `site.ini [FileSettings] VarDir` ohne das führende `var/`;
eine Standardinstallation (`var/site`) braucht keine Änderung.

> **Warnung**
> Fügen Sie die Regeln zur Auslagerung nie dem vhost der Administration hinzu: Dort wird nichts zwischengespeichert, und die Regeln
> dürfen nie die Gelegenheit bekommen, die Anfrage eines Redakteurs aus einer Datei zu beantworten, die für einen anonymen Besucher
> erzeugt wurde (`doc/eng-us/examples/staticcache_offload.conf`).

## Einstellungen

`settings/staticcache.ini`, `[CacheSettings]` (überschrieben in `settings/override/staticcache.ini.append.php`):

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `HostName` | leer | veraltet; leer lassen, damit jeder Siteaccess von seiner eigenen `SiteURL` abgerufen wird |
| `SourceProtocol` | `http` | `http` oder `https` für den Abruf |
| `StaticStorageDir` | `static` | wird zu `var/<site>/static`; ein absoluter Pfad wird unverändert genutzt |
| `MaxCacheDepth` | `12` | tiefere URLs werden weder zwischengespeichert noch beim Veröffentlichen aufgefrischt |
| `CachedURLArray[]` | `/`, `/*` | zwischenzuspeichernde URLs |
| `CachedSiteAccesses[]` | leer | leer bedeutet jeder öffentliche Siteaccess |
| `AlwaysUpdateArray[]` | `/` | URLs, die bei jeder Veröffentlichung aufgefrischt werden |
| `CronjobCacheClear` | `disabled` | `enabled` lässt den Cronjob-Teil `staticcache_cleanup` statt der Veröffentlichung leeren |
| `FetchConcurrency` | `8` (im Code) | gleichzeitig abgerufene Seiten beim Auffrischen |

Installationen, die von älteren Releases aktualisiert wurden, haben vielleicht Überschreibungen, die sich auf die alten Standardwerte
verließen; siehe [Standardwerte des statischen Caches](../../../bc/6.0/standardwerte-des-statischen-caches.md).

## Checkliste

- [ ] `StaticCache=disabled` in jedem Tarif, es sei denn, ein Kunde hat darum gebeten und die Kosten beim Veröffentlichen akzeptiert.
- [ ] Wenn genutzt: Regeln zur Auslagerung nur auf dem öffentlichen vhost; `EZ_VAR_SITE` passt zu `VarDir`.
- [ ] Erzeugung als Benutzer der Website ausgeführt; die gespeicherten Seiten mit einem einfachen `curl` geprüft.
- [ ] Anfragen angemeldeter Benutzer und Anfragen mit Query-String erreichen weiterhin PHP.

## Verwandte Seiten

- [Cache-Schichten](cache-schichten.md)
- [Antwort-Cache von Velocity](antwort-cache-von-velocity.md)
- [Apache mit PHP-FPM](../server/apache-mit-php-fpm.md)
- [Generator für den statischen Cache](../../../funktionen/6.0/generator-fuer-den-statischen-cache.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
