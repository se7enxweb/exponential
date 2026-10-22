---
title: URLs und Weiterleitungen beim Umzug
audience: adopter, developer, editor, project-manager
category: adoption
order: 45
summary: Wie Anwender Links und Suchplatzierungen erhalten, wenn sie eine Website nach Exponential umziehen - URL-Aliase, alte Adressen neuen zuordnen, Weiterleitungen, Sprachen, 404-Meldungen nach dem Start prüfen und die Dokumentation zu jedem Punkt.
keywords: URLs, Weiterleitungen, URL-Aliase, SEO, 404, alte Adressen, Zuordnung, Sprachen, Migration, Suchplatzierungen, redirects, URL aliases, old addresses, mapping, languages, migration, search rankings
type: help
---
# URLs und Weiterleitungen beim Umzug

Alte Adressen leben in Lesezeichen, auf anderen Websites und in Suchmaschinen. Eine Migration, die sie bricht, verliert
Besucher.

## Inhalt

1. [Wie Exponential Adressen bildet](#wie-exponential-adressen-bildet)
2. [Alt auf neu zuordnen](#alt-auf-neu-zuordnen)
3. [Nach dem Start](#nach-dem-start)

## Wie Exponential Adressen bildet

Exponential gibt Inhalten lesbare Adressen (URL-Aliase) aus ihrem Ort im Inhaltsbaum und ihrem Namen, pro Sprache.
Verschobene oder umbenannte Inhalte funktionieren über ihre Aliase weiter. Die Sicht der Betreiber:
[URLs, Weiterleitungen und verschobene Seiten](../../kunden/alltag/urls-weiterleitungen-und-verschobene-seiten.md). Der
Neuaufbau der Aliase ist einer der gemessenen Schritte einer Migration
([Zu Exponential wechseln](../../unternehmen/auswahl/zu-exponential-wechseln.md#was-den-aufwand-bestimmt)).

## Alt auf neu zuordnen

1. Exportieren Sie die Liste der alten Adressen, mit Verkehrszahlen, falls Sie welche haben.
2. Entscheiden Sie für jede: dieselbe Adresse, eine neue Adresse oder abgeschafft.
3. Legen Sie Weiterleitungen für jede geänderte oder abgeschaffte Adresse an, die Verkehr oder Links hat.
4. Testen Sie die Liste vor dem Start auf Staging.

Weiterleitungsregeln können auch im Webserver liegen; die Hosting-Seiten behandeln jeden Server
([Webserver und Engines](../../oekosystem/php-stack/webserver-und-engines.md)).

## Nach dem Start

- Beobachten Sie in den ersten Wochen täglich die Fehler "nicht gefunden" ([Logs für Betreiber](../../kunden/gesund-halten/logs-fuer-betreiber.md)).
- Ergänzen Sie Weiterleitungen für Adressen, die noch Verkehr bekommen.
- Prüfen Sie Suche und Sitemaps ([SEO-Prüfungen](../../kunden/alltag/seo-pruefungen.md)).

## Verwandte Seiten

- [Umziehen](README.md)
- [Checkliste zur Inhaltsmigration](checkliste-inhaltsmigration.md)
- [Sichere Weiterleitungen](../../../funktionen/6.0/sichere-weiterleitungen.md)
- [Monatliche SEO-Checkliste](../../kunden/alltag/monatliche-seo-checkliste.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
