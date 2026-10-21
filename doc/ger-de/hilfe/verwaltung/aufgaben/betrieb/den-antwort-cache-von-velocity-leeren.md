---
title: Den Antwort-Cache von Velocity leeren
audience: government, admin, hosting
category: public-sector
order: 300
summary: Schritt für Schritt - Velocity nach einer Änderung an Templates, einer Korrektur oder einer Wiederherstellung frisch dargestellte Seiten ausliefern lassen, mit exp:velocity cache clear, zum richtigen Zeitpunkt, und mit den Headern X-Cache und Age prüfen.
keywords: Velocity, Antwort-Cache, Cache leeren, exp:velocity cache clear, Generationsmarke, X-Cache, Age, veraltete Seiten, nach dem Deployment, response cache, cache clear, generation marker, stale pages, after deploy
type: help
---
# Den Antwort-Cache von Velocity leeren

Diese Aufgabe ist für Administratoren von Sites, die von Exponential Velocity ausgeliefert werden. Der Antwort-Cache von
Velocity beantwortet wiederholte Anfragen nach öffentlichen Seiten innerhalb des Servers, noch vor PHP; nach einer
Änderung können diese Antworten veraltet sein. Referenz:
[Antwort-Cache von Velocity](../../../../../eng-us/features/6.0/velocity-response-cache.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Shell-Zugang.
- Wenn die Änderung PHP-Code oder Einstellungen betraf, rollen Sie zuerst aus: Wird der Cache geleert, bevor die Worker
  den neuen Code ausführen, werden alte Seiten erneut gespeichert
  ([Eine PHP-Änderung ausrollen](eine-php-aenderung-ausrollen.md), das diesen Cache als letzte Schritte leert).

## Schritte

```bash
./console exp:velocity cache clear --allow-root-user
php bin/php/cache.php velocity status --allow-root-user
```

Das Leeren berührt die Generationsmarke des Caches: Jeder vorher gespeicherte Eintrag gilt als Fehltreffer und wird bei
seiner nächsten Anfrage ersetzt; jeder Worker und jeder Server, der die Marke teilt, sieht es innerhalb einer Sekunde.
Wärmen Sie dann die wichtigsten Seiten vor ([Die Caches nach einem Deployment vorwärmen](die-caches-nach-einem-deployment-vorwaermen.md)).

## Prüfen, ob es funktioniert hat

```bash
curl -sI https://www.example.gov/ | grep -iE 'x-cache|age'
```

Die erste Anfrage nach dem Leeren ist ein Fehltreffer (dargestellt); die nächste trägt `X-Cache: HIT` und ein kleines
`Age`. Prüfen Sie den geänderten Inhalt in einem privaten Fenster.

## Verwandte Seiten
- [Caches nach einer Änderung an Templates leeren](caches-nach-einer-aenderung-an-templates-leeren.md)
- [Veralteter Cache](../../../../../eng-us/help/government/operations/incidents/stale-cache.md)
- [Antwort-Cache von Velocity und HTTP-Cache](../../../../../eng-us/help/government/operations/availability/velocity-response-cache-and-http-cache.md)
- [Antwort-Cache von Velocity (Hosting)](../../../hosting/leistung/antwort-cache-von-velocity.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
