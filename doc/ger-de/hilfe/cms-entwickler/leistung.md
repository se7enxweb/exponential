---
title: Leistung
audience: cms-developer
category: cms-development
order: 42
summary: Für CMS-Entwickler, wie Sie eine Exponential-Website schnell machen und schnell halten. Die Seite behandelt das Messen mit exp:benchmark, was Inhaltsmodell und Templates entscheiden, Regeln für Fetches, Caching je Ebene, die Serverseite (Velocity, OPcache, Antwort-Cache), Bilder und das Gewicht des Frontends, das Vorwärmen von Caches und eine kurze Checkliste.
keywords: Leistung, Geschwindigkeit, schnelle Website, Benchmark, exp:benchmark, Messen, Fetch-Leistung, View-Cache, cache-block, Velocity, OPcache, Antwort-Cache, HTTP-Cache, SQL-Abfrage-Cache, Bilder, Bildvarianten, Packer, Vorladen, Caches vorwärmen, langsame Website, performance, speed, fast site, benchmark, measuring, fetch performance, view cache, response cache, image aliases, preload, warm caches, slow site
type: help
---
# Leistung

Diese Seite richtet sich an Entwickler, die dafür verantwortlich sind, wie schnell eine Website ist. Der größte Teil der
Geschwindigkeit einer Exponential-Website wird von drei Dingen bestimmt, die Sie steuern: wie viel jede Seite abruft,
wie viel davon zwischengespeichert werden kann und wie die Website ausgeliefert wird. Messen Sie zuerst, ändern Sie eine
Sache, messen Sie erneut.

## Inhalt

1. [Messen](#messen)
2. [Inhaltsmodell und Templates](#inhaltsmodell-und-templates)
3. [Fetches](#fetches)
4. [Caching](#caching)
5. [Der Server](#der-server)
6. [Bilder und das Frontend](#bilder-und-das-frontend)
7. [Caches vorwärmen](#caches-vorwärmen)
8. [Eine kurze Checkliste](#eine-kurze-checkliste)
9. [Wenn es in der Produktion langsam ist](#wenn-es-in-der-produktion-langsam-ist)

## Messen

```bash
./console exp:benchmark --allow-root-user
./console exp:cache status --allow-root-user
curl -s -o /dev/null -w '%{time_total}s\n' https://www.example.com/
```

`exp:benchmark` ruft die Startseite, Menüseiten, die Suche und die Anmeldung der Administration viele Male auf und gibt
Median-, p90- und p99-Zeiten aus; es kann Läufe speichern und mit einer Ausgangsbasis vergleichen und misst auch Teile
einer Seite innerhalb des Kernels ([Benchmarking von Exponential](../../anleitungen/benchmarking.md)). Die Debug-Ausgabe
listet auf einem Entwicklungsrechner die SQL-Abfragen und die Zeit einer Seite. Ein einzelner Seitenaufruf beweist
nichts: Die erste Anfrage füllt die Caches.

## Inhaltsmodell und Templates

- Eine **Listenseite**, die fünfzig Elemente zeichnet, jedes mit einzeln abgerufenen Relationen, erzeugt Hunderte von
  Abfragen. Modellieren Sie so, dass Listenansichten nur die eigenen Attribute des Elements brauchen; verwenden Sie
  `load_data_map`, wenn Sie Attribute vieler Knoten lesen.
- **Fetches im Pagelayout** laufen bei jeder Anfrage und sind nicht im View-Cache: Schließen Sie Menüs und Fußzeilen in
  Cache-Blöcke ein.
- **Nicht cachebare Teile** (der Name des Besuchers, der Warenkorb) sollten klein sein und aus dem View-Cache
  herausgehalten werden ([Caching für CMS-Entwickler](caching-fuer-cms-entwickler.md#besucherbezogene-ausgaben)).

## Fetches

Übergeben Sie immer `limit`; filtern Sie im Fetch nach Klasse; zählen Sie mit `list_count`; vermeiden Sie Fetches in
Schleifen; beschränken Sie Attributfilter auf großen Bäumen auf das, was Sie gemessen haben
([Fetch-Leistung](../designer/vorlagen/fetch-leistung.md),
[Inhalte in Templates abrufen](inhalte-in-templates-abrufen.md#regeln-für-schnelle-fetches)). Code in Ihren Erweiterungen
folgt denselben Regeln ([Leistung in Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/leistung-in-erweiterungen.md)).

## Caching

| Ebene | Was zu tun ist |
|---|---|
| View-Cache | eingeschaltet lassen (`ViewCaching=enabled`); `full` zwischengespeichert lassen; `cache_ttl` nur wo nötig setzen |
| Cache-Blöcke | um alles im Pagelayout, was für viele Besucher gleich ist, mit korrekten Schlüsseln |
| SQL-Abfrage-Cache | standardmäßig an; `./console exp:cache querycache status` |
| HTTP-Cache, Antwort-Cache von Velocity | ganze Seiten für anonyme Besucher; prüfen Sie mit `exp:cache status` |

Jede Ebene: [Caching-Ebenen](../hosting/leistung/cache-schichten.md).

## Der Server

1. **Velocity** mit der Engine `qbix` hält PHP-Worker zwischen Anfragen geladen und hat einen Antwort-Cache; es ist die
   empfohlene Engine ([Velocity, der Server mit persistenten Workern](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md)).
2. **OPcache** für PHP-FPM einschalten; Velocity übergibt eigene Optionen für den Opcode-Cache
   ([OPcache](../hosting/leistung/opcache.md), [Velocity und der Opcode-Cache](../../funktionen/6.0/velocity-opcode-cache-und-profil.md)).
3. **Worker**: `velocity.ini [ServerSettings] Workers`, mit der Zahl der CPUs erhöht und nach jeder Änderung gemessen.
4. **Die Datenbank**: eine Engine, die für die Inhalte bemessen ist; siehe
   [Speicher, CPU und Festplatte bemessen](../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md).

Die Reihenfolge dieser Schritte mit Befehlen: [Schnell machen](../../anleitungen/bereitstellung.md#8-machen-sie-sie-schnell).

## Bilder und das Frontend

- Legen Sie Bildvarianten für die Größen fest, die Sie zeigen; geben Sie `width` und `height` aus; laden Sie Bilder
  unterhalb des sichtbaren Bereichs verzögert ([Bilder und Bildvarianten](bilder-und-bildvarianten.md),
  [Bildleistung](../designer/theme-design/bildleistung.md)).
- Laden Sie Stylesheets und Skripte über den Packer, der sie bündelt und zwischenspeichert
  ([Der Packer](../designer/theme-design/der-packer.md), [Theme-Leistung](../designer/theme-design/theme-leistung.md)).
- Messen Sie Layoutverschiebung und größtes Element auf echten Seiten
  ([CLS und LCP messen](../designer/theme-design/cls-und-lcp-messen.md)).

## Caches vorwärmen

Nach einer Bereitstellung oder dem Leeren eines Cache bezahlen die ersten Besucher für den Aufbau der Caches.
`exp:velocity deploy` ruft Seiten zum Vorwärmen auf, und der Preloader ruft die Seiten einer Website auf, wie es ein
anonymer Besucher täte (**Setup** > **Preload**, `php bin/php/preload.php --apply`), und listet dabei defekte Links auf
([Caches vorladen](../../anleitungen/caches-vorwaermen.md)).

## Eine kurze Checkliste

- [ ] Vor und nach jeder Änderung mit demselben Befehl gemessen.
- [ ] Keine Seite stellt mehr Abfragen, als ihr Inhalt erklärt; die Debug-Ausgabe wurde auf den schwersten Seiten
      geprüft.
- [ ] Jeder Listen-Fetch hat ein Limit; keine Fetches in Schleifen.
- [ ] Menüs und Fußzeilen des Pagelayouts liegen in Cache-Blöcken mit den richtigen Schlüsseln.
- [ ] Der View-Cache ist an; besucherbezogene Ausgaben liegen außerhalb.
- [ ] Ausgeliefert von Velocity (oder einem anderen abgestimmten Server) mit OPcache.
- [ ] Bildvarianten für jede gezeigte Größe; Assets über den Packer.

## Wenn es in der Produktion langsam ist

Folgen Sie dem Runbook [Langsame Website](../hosting/runbooks/langsame-website.md): Prüfen Sie die Last, die Cache-Ebenen,
die Anfrage-Logs und die langsamsten Seiten, bevor Sie etwas ändern ([Anfrage-Logs](../hosting/betrieb/anfrage-logs.md)).

## Verwandte Seiten

- [Checkliste: Leistung](../designer/theme-design/checkliste-leistung.md)
- [Benchmarking einer gehosteten Website](../hosting/leistung/benchmark-einer-gehosteten-website.md)
- [Eine Website bereitstellen](eine-website-bereitstellen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
