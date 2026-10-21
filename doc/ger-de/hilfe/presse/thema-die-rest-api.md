---
title: "Thema: die REST-API"
audience: press
category: press-media
order: 78
summary: Ein Themenbeitrag über die REST-Inhalts-API von Exponential - eine Erweiterung für die Versionen 1 und 2, Lesen in beiden Versionen und Schreiben in v2, Rechteprüfung bei jedem Aufruf, OAuth2-Token und persönliche API-Schlüssel - für Berichte über Headless- und Integrationseinsatz, mit Quellen und den Aussagen, die Sie vermeiden sollten.
keywords: Thema, REST-API, Headless, API, Integration, OAuth2, API-Schlüssel, v1, v2, ezprestapi, mobile Apps, Entwickler, story, REST API, headless, integration, API keys, mobile apps, developers
type: help
---
# Thema: die REST-API

Diese Seite ist für technische Journalisten, die über Headless-Content-Management und Integrationen berichten.
Exponential stellt seine Inhalte über eine REST-API bereit, und die Linie 6.0.15 hat die beiden API-Versionen in einer
Erweiterung zusammengeführt.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
3. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
4. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)

## Das Thema in einem Absatz

Das README nennt „A headless CRUD REST API“ unter den Hauptfunktionen. In Exponential 6.0.15 kommen die Versionen 1 und
2 der REST-Inhalts-API aus einer Erweiterung, `ezprestapi`: Lesezugriffe antworten in v1 und v2, Schreibzugriffe
(Anlegen, Ändern, Löschen) nur in v2, und „every read and write checks the user's policies ... whatever the request was
authenticated with“. Clients authentifizieren sich mit einem OAuth2-Token oder einem persönlichen API-Schlüssel
([REST API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md)). REST v2 mit Schreibunterstützung war die Hauptnachricht von
6.0.9 in einer Pressemitteilung von 7x aus dem Jahr 2025
([Frühere Pressemitteilungen und Berichte](fruehere-pressemitteilungen-und-berichte.md)).

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Lesen unter `/api/ezp/v1/...` und `/api/ezp/v2/...`; Schreiben nur in v2 | [REST API v1 und v2, Kurz gesagt](../../anleitungen/rest-api-v1-und-v2.md#kurz-gesagt) |
| Rechteprüfung bei jedem Aufruf (`content/read`, `content/create`, `content/edit`, `content/remove`) | ebenda |
| OAuth2-Token oder persönliche API-Schlüssel | ebenda; [Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md) |
| Die alte Erweiterung `ezprestapiprovider` ist veraltet; Clients merken keinen Unterschied | ebenda |
| Die Aussagen der Anleitung wurden am 15. Oktober 2026 auf dem Vorführserver geprüft | [REST API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md) |

## Was Sie nicht behaupten sollten

| Sagen Sie nicht | Sagen Sie stattdessen |
|---|---|
| „GraphQL in Exponential 6“ | GraphQL gehört zur Symfony-Linie (Exponential Platform DXP) ([Thema: Exponential Platform DXP](thema-exponential-platform-dxp.md)) |
| „Jeder kann über die API schreiben“ | Schreiben erfordert Authentifizierung und die Rechte des Benutzers |
| „Die API ist neu“ | Die API gibt es seit Jahren; 6.0.15 vereinheitlicht die Versionen und ergänzt Prüfungen |

## Blickwinkel und Ideen für Überschriften

- „Headless mit Rechten: Jeder API-Aufruf prüft die Rechte des Benutzers.“
- „Eine API-Erweiterung, zwei Versionen, kein Bruch.“

## Verwandte Seiten

- [Thema: Inhalte bearbeiten](thema-inhalte-bearbeiten.md)
- [Thema: Sicherheit](thema-sicherheit.md)
- [Gesprächspunkte für Entwickler](../fuersprecher/gespraechspunkte-fuer-entwickler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
