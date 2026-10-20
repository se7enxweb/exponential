---
title: "Runbook: langsame Website"
audience: hosting, admin
category: hosting
order: 30
summary: Was zu tun ist, wenn eine Exponential-Website langsam ist - zuerst mit exp:benchmark messen, kalt von warm trennen, die Caches, OPcache, Worker oder PHP-FPM-Kinder, die Datenbank und die Anfrage-Logs prüfen, dann die Hebel in der Reihenfolge ihrer Wirkung anwenden.
keywords: Runbook, langsame Website, Leistung, Latenz, Benchmark, exp:benchmark, Cache, View-Cache, Antwort-Cache, OPcache, PHP-FPM, Worker, Datenbank, SQLite ANALYZE, vorwärmen, statischer Cache, runbook, slow site, performance, latency, view cache, response cache, workers, database, warm, static cache
type: help
---
# Runbook: langsame Website

Für die Bereitschaft und Administratoren, wenn ein Kunde meldet, dass seine Exponential-Website langsam ist. Die Hebel und ihre Reihenfolge
stammen aus [10.12 Abstimmung der Leistung](../../../../en/us/install/10-after-installing.md#1012-performance-tuning).

## Inhalt

1. [Zuerst messen](#zuerst-messen)
2. [Betrifft es alle, oder eine Seite?](#betrifft-es-alle-oder-eine-seite)
3. [Prüfungen in Reihenfolge](#prüfungen-in-reihenfolge)
4. [Abhilfen in der Reihenfolge ihrer Wirkung](#abhilfen-in-der-reihenfolge-ihrer-wirkung)
5. [Nach dem Vorfall](#nach-dem-vorfall)

## Zuerst messen

Eine langsame Website ist eine Zahl, kein Gefühl. Messen Sie die Startseite und die Seiten, die der Kunde genannt hat:

```bash
curl -s -o /dev/null -w '%{http_code} %{time_total}\n' https://www.example.com/
php bin/php/console exp:benchmark --allow-root-user
```

`exp:benchmark` fragt die Startseite, die ersten Menüseiten, die Suchseite und die Anmeldeseite der Administration an, jeweils 100-mal nach
5 Anfragen zum Vorwärmen, 4 gleichzeitig, und gibt Median, p90 und p99 aus. Wenn Sie eine Basislinie gespeichert haben, als die Website
gesund war, vergleichen Sie damit:

```bash
php bin/php/console exp:benchmark --baseline=var/benchmark/base.json --threshold=15 --allow-root-user
```

> **Warnung**
> Ein Benchmark ist Last. Auf einer Website, die bereits zu kämpfen hat, führen Sie einen kleinen aus (`--requests=20`) oder testen von
> einer Staging-Kopie. Details: [Benchmark einer gehosteten Website](../leistung/benchmark-einer-gehosteten-website.md).

## Betrifft es alle, oder eine Seite?

| Muster | Wahrscheinliche Ursache |
|---|---|
| Jede Seite langsam, auch zwischengespeicherte | die Maschine: CPU, Arbeitsspeicher, Swap, Festplatte; oder die Engine hat keinen freien Worker |
| Nur die erste Anfrage nach einer Änderung ist langsam | Caches werden nach einem Leeren oder Ausrollen neu aufgebaut: normal, wärmen Sie sie vor |
| Nur angemeldete Benutzer (Redakteure) sind langsam | sie umgehen die anonymen Caches; prüfen Sie die Datenbank und OPcache |
| Eine Seite langsam | das Template oder der Fetch dieser Seite; prüfen Sie ihre Log-Zeilen |
| Langsam zu bestimmten Zeiten | ein Cronjob, ein Backup, ein Import oder ein Crawler; prüfen Sie die Anfrage-Logs und die Cron-Zeiten |

## Prüfungen in Reihenfolge

1. **Die Maschine**: `uptime`, `free -m`, `df -h`, `iostat`, falls verfügbar. Swap-Nutzung oder eine volle Festplatte erklären alles andere
   ([Festplatte voll](festplatte-voll.md)).
2. **Die Caches**: `php bin/php/cache.php status --json --allow-root-user` sollte mit `PASS` enden. Prüfen Sie, dass
   `[ContentSettings] ViewCaching=enabled` und `[TemplateSettings] TemplateCache=enabled` (beide mitgeliefert) nicht in einer Überschreibung
   ausgeschaltet wurden.
3. **Die Engine**:
   - Velocity: `php bin/php/console exp:velocity status --allow-root-user` (Worker wie konfiguriert); `exp:velocity cache stats` für den
     Antwort-Cache.
   - PHP-FPM: Sind alle Kinder beschäftigt? Siehe [Abstimmung von PHP-FPM](../leistung/abstimmung-von-php-fpm.md).
4. **OPcache**: `php bin/php/cache.php opcache status --allow-root-user`. Betreiben Sie eine Exponential-Website nie mit
   `opcache.validate_timestamps=0`; unter Velocity zählen die mitgelieferten Einstellungen ([OPcache](../leistung/opcache.md)).
5. **Die Datenbank**: Auf SQLite hat der Query-Planer nach einer Installation oder einem großen Import keine Statistiken: Führen Sie einmal
   `sqlite3 var/storage/sqlite3/exponential.db ANALYZE` aus ([12.10](../../../../en/us/install/12-troubleshooting.md#1210-databases)). Auf MongoDB
   prüfen Sie, dass die Indizes aus `bin/mongodb/create_indexes.js` existieren.
6. **Verkehr**: `security.log` und `404.log` zeigen Häufungen von Abtastungen und Anfragen „nicht gefunden“; ein Crawler oder ein Angriff
   kann wie Langsamkeit aussehen ([Anfrage-Logs](../betrieb/anfrage-logs.md)).
7. **Der statische Cache**: Er sollte aus sein (`[ContentSettings] StaticCache=disabled`, der mitgelieferte Wert). Wenn er an ist, erzeugt
   jede Veröffentlichung betroffene Seiten innerhalb der Anfrage des Redakteurs neu ([Statischer Cache](../leistung/statischer-cache.md)).

## Abhilfen in der Reihenfolge ihrer Wirkung

Aus 10.12:

1. **Mit Velocity und seinem Antwort-Cache ausliefern**; persistente Worker halten die Anwendung warm.
2. **Den Content-View-Cache und den Template-Block-Cache anlassen**, und die Website nach einem Leeren vorwärmen:
   `php bin/php/warm.php --allow-root-user`.
3. **OPcache**, konfiguriert, wie [OPcache](../leistung/opcache.md) es beschreibt.
4. **APCu** für die Speicherstufe des Antwort-Caches von Velocity und den SQL-Abfrage-Cache im Modus `shared`.
5. **Der SQL-Abfrage-Cache**, auf der Website gemessen.
6. **Die Abstimmung der Datenbank** aus [Kapitel 9](../../../../en/us/install/09-databases.md).

Ändern Sie einen Hebel nach dem anderen und messen Sie erneut mit der Basislinie.

## Nach dem Vorfall

- [ ] Speichern Sie eine neue Basislinie, wenn die Website wieder gesund ist:
      `php bin/php/console exp:benchmark --save=var/benchmark/base.json --allow-root-user`.
- [ ] Halten Sie Ursache und Abhilfe im Datensatz der Website fest.
- [ ] Wenn die Website ihrem Tarif entwachsen ist, sagen Sie es dem Kunden mit den Messungen ([Hosting-Tarife gestalten](../geschaeft/hosting-tarife-gestalten.md)).

## Verwandte Seiten

- [Cache-Schichten](../leistung/cache-schichten.md)
- [Caches leeren und vorwärmen](../leistung/caches-leeren-und-vorwaermen.md)
- [Benchmark von Exponential](../../../../en/us/guides/benchmarking.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
