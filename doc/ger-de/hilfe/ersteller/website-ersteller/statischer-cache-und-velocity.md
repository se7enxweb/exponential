---
title: Statischer Cache und Velocity
audience: website-creator, admin
category: website-creation
order: 650
summary: Warum der statische Cache ausgeschaltet bleibt und der Antwort-Cache von Velocity die Aufgabe besser erledigt - den Antwort-Cache einschalten, was er zwischenspeichert, ihn nach Änderungen leeren und Treffer prüfen.
keywords: statischer Cache, Antwort-Cache von Velocity, anonyme Seiten, Cache leeren, Website-Ersteller, static cache, StaticCache, Velocity response cache, exp:velocity cache, anonymous pages, ETag, 304, stale-while-revalidate, cache clear, website creator
type: help
---
# Statischer Cache und Velocity

Diese Seite richtet sich an Website-Ersteller, die öffentliche Seiten so schnell wie möglich ausliefern wollen. Zwei
Funktionen haben diese Aufgabe übernommen: der alte **statische Cache**, der HTML-Dateien auf die Festplatte schrieb,
und der **Antwort-Cache von Velocity**, der Seiten im Serverprozess hält. Verwenden Sie den zweiten.

## Inhalt

1. [Lassen Sie den statischen Cache ausgeschaltet](#lassen-sie-den-statischen-cache-ausgeschaltet)
2. [Der Antwort-Cache von Velocity](#der-antwort-cache-von-velocity)
3. [Einschalten](#einschalten)
4. [Nach einer Änderung](#nach-einer-änderung)
5. [Prüfen](#prüfen)

## Lassen Sie den statischen Cache ausgeschaltet

`[ContentSettings] StaticCache=disabled` ist der ausgelieferte Wert, und der Installer schreibt ihn. Mit eingeschaltetem
statischem Cache erzeugte jedes Veröffentlichen jede betroffene Seite in jedem öffentlichen Siteaccess per HTTP innerhalb
der Anfrage des Redakteurs neu, sodass ein Veröffentlichen Sekunden dauerte
([10.12](../../../installation/10-nach-der-installation.md#1012-performance-tuning),
[Standardwerte des statischen Caches](../../../bc/6.0/standardwerte-des-statischen-caches.md)).

## Der Antwort-Cache von Velocity

Er hält gerenderte öffentliche Seiten vor und beantwortet wiederholte Anfragen im Serverprozess, bevor ein Worker
ausgewählt wird. Er verhält sich wie ein kleiner Reverse Proxy: bedingte Anfragen (`304`), `ETag`, `Age`,
stale-while-revalidate, Negativ-Caching, komprimierte Speicherung und Invalidierung über eine berührte Datei
([Antwort-Cache von Velocity](../../../../eng-us/features/6.0/velocity-response-cache.md)). Er greift nur, wenn Velocity die
Website ausliefert, und nur für anonyme Anfragen.

## Einschalten

Der Cache ist ausgeschaltet, bis eine Einstellung `enabled: true` sagt (seit Engine-Release 0.0.4.39). Schalten Sie ihn
in einer Site-Datei oder im Modul `cache` der Velocity-Konfiguration ein (`qbixctl enmod cache`), mit einem
Cache-Verzeichnis, und starten Sie neu; die Funktionsseite enthält die genauen Einstellungen und die Reihenfolge, in der
Konfigurationsdateien Vorrang haben.

## Nach einer Änderung

```bash
php bin/php/console exp:velocity cache clear --allow-root-user
```

nach Änderungen an Templates, Stylesheets oder Einstellungen und nachdem der Inhalts-Cache geleert wurde.
`exp:velocity deploy` leert ihn als letzten Schritt. Das Leeren braucht keinen Neustart.

## Prüfen

```bash
php bin/php/console exp:velocity cache stats --allow-root-user
curl -sI https://www.example.com/ | grep -i -E "age|etag"
```

Eine zweite anonyme Anfrage nach derselben Seite sollte einen Header `Age` zeigen.

## Verwandte Seiten

- [Leistung und Caching](leistung-und-caching.md)
- [Mit Velocity ausliefern](../anwendungsentwickler/mit-velocity-ausliefern.md)
- [Velocity-Webserver](../../../../eng-us/features/6.0/velocity-web-server.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
