---
title: Headless-Auslieferung
audience: cms-developer
category: cms-development
order: 30
summary: Für CMS-Entwickler, die Exponential-Inhalte an Apps und Frontends anderer Technologien ausliefern. Die Seite stellt dar, was es gibt, nämlich die REST-Schnittstelle, expservices, Serverfunktionen von ezjscore, eigene JSON-Views und Feeds, und wie Sie wählen. Sie behandelt auch die Muster für Authentifizierung, Caching und gerenderte Ausgabe sowie das, was Exponential 6 nicht bietet.
keywords: Headless, entkoppelt, API, JSON, REST, expservices, ezjscore, Serverfunktionen, mobile App, Single-Page-Anwendung, Frontend, Feeds, JSON Feed, gerenderte Ausgabe, OutputFormat, Tokens, Caching, GraphQL, headless, decoupled, server functions, mobile app, single page application, front end, rendered output
type: help
---
# Headless-Auslieferung

Diese Seite richtet sich an Entwickler, die Inhalte in Exponential verwalten und anderswo zeigen möchten: in einer
mobilen App, einer Single-Page-Anwendung, einem Kiosk, einer anderen Website. Exponential 6 ist ein vollständiges CMS mit
eigenen Templates und kann Inhalte neben seinen Seiten oder statt ihrer als Daten ausliefern. Diese Seite listet die
vorhandenen Wege auf und wie Sie wählen.

## Inhalt

1. [Die Möglichkeiten](#die-möglichkeiten)
2. [Die REST-Schnittstelle](#die-rest-schnittstelle)
3. [expservices](#expservices)
4. [Serverfunktionen von ezjscore](#serverfunktionen-von-ezjscore)
5. [Eine eigene JSON-View](#eine-eigene-json-view)
6. [Feeds](#feeds)
7. [Die Wahl](#die-wahl)
8. [Muster, die funktionieren](#muster-die-funktionieren)
9. [Was es nicht gibt](#was-es-nicht-gibt)

## Die Möglichkeiten

| Weg | Adresse | Authentifizierung | Am besten für |
|---|---|---|---|
| REST-Schnittstelle (`ezprestapi`) | `/api/ezp/v1/...`, `/api/ezp/v2/...` | persönlicher API-Schlüssel, OAuth2-Token | Lesen von Knoten, Unterelementen und Feldern; Anlegen und Entfernen in v2 |
| `expservices` | `/ezjscore/call/exp<domain>::<method>` | Session-Cookie oder ein persönliches Token (`expt_...`) | die Funktionen der Administration: Inhalte, Benutzer, Shop, Medien, Layouts und mehr |
| Eigene Serverfunktionen von ezjscore | `/ezjscore/call/<class>::<function>` | Session und Richtlinien der Website | kleine JSON-Aufrufe für Ihr eigenes Frontend |
| Eine Modul-View, die JSON antwortet | `/<your module>/<view>/...` | Session und Richtlinien der Website | ein eigener Endpunkt mit eigener Form |
| Feeds | RSS-Export und Feed-Ausgabe von `expservices` | öffentlich oder wie die Website es erlaubt | Listen von Inhalten für Leser und Aggregatoren |

## Die REST-Schnittstelle

Die dokumentierte Inhalts-API: Knoten, Unterelemente mit Offset, Limit und Sortierung, Felder, Objekte;
Schreibzugriffe in v2. In v2 fügt `OutputFormat=xhtml` die gerenderte Ausgabe der Ansicht des Knotens hinzu, sodass ein
Client Rich Text als HTML zeigen kann, das die Templates der Website gezeichnet haben. Details, Authentifizierung und
Grenzen: [Die REST-API](die-rest-api.md).

## expservices

Die Erweiterung `expservices`, mitgeliefert im Verzeichnis `extension/` des Kernels, bietet die Funktionen der
Administration als benannte HTTP-Dienste mit einer JSON-Hülle (`{"ok":true,"data":...,"meta":{...}}`), geblätterten
Listen, einem Shell-Client und Python-Beispielen. Ein angemeldeter Benutzer erstellt mit `expsession::tokenCreate` ein
persönliches Token; es wird einmal angezeigt und als `Authorization: Bearer <token>` gesendet
([Entfernte Dienste und Apps](../../anleitungen/entfernte-dienste-und-apps.md),
[Entfernte Dienste: expservices](../../funktionen/6.0/entfernte-dienste-expservices.md)). Ermitteln Sie mit dem
Katalogdienst, was Ihre Installation anbietet, statt aus dem Gedächtnis:

```bash
curl -s -H "Authorization: Bearer $EXP_SERVICES_TOKEN" \
  'https://www.example.com/ezjscore/call/expservices::catalog::system?ContentType=json'
```

## Serverfunktionen von ezjscore

Für ein eigenes Frontend auf derselben Website (ein Suchfeld mit Vorschlägen beim Tippen, eine Schaltfläche "mehr
laden") ist eine Serverfunktion von ezjscore eine statische PHP-Funktion, registriert in `ezjscore.ini`, aufgerufen unter
`/ezjscore/call/...` und mit der Session und den Richtlinien der Website als JSON beantwortet
([Ajax mit Serverfunktionen von ezjscore](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ajax-mit-ezjscore-serverfunktionen.md),
[Eine Serverfunktion von Hand](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/tutorials/von-hand-ajax-serverfunktion.md)).

## Eine eigene JSON-View

Braucht ein Client eine Form, die keine vorhandene Schnittstelle liefert, kann eine Modul-View in Ihrer Erweiterung mit
JSON statt einer Seite antworten (kein Pagelayout, ein JSON-Körper, der richtige Content-Type) und eine
Richtlinienfunktion für den Zugriff deklarieren
([Mit JSON oder einer Datei antworten](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/view-ergebnisse-und-das-pagelayout.md#mit-json-oder-einer-datei-antworten),
[Module und Views](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/module-und-views.md)). Bauen Sie die Daten mit
Fetch-Funktionen oder der Kernel-API, damit Berechtigungen gelten ([Inhalte aus PHP](inhalte-aus-php.md)).

## Feeds

Der RSS-Export des Kernels veröffentlicht einen Teilbaum als Feed (**Setup** > **RSS**), und `expservices` kann für
jeden Teilbaum RSS, Atom oder JSON Feed ausgeben ([RSS-Feeds](../../anleitungen/rss-feeds.md)). Verwenden Sie Feeds für
News-Listen, die andere Systeme nach Zeitplan abholen.

## Die Wahl

| Bedarf | Wählen Sie |
|---|---|
| Eine App liest veröffentlichte Inhalte | REST v1 oder v2 mit einem persönlichen Schlüssel eines Kontos nur mit Leserechten |
| Eine App lässt ihre Benutzer sich anmelden und als sie selbst schreiben | REST mit OAuth2-Tokens |
| Ein Werkzeug automatisiert redaktionelle oder administrative Aufgaben | `expservices` mit einem persönlichen Token |
| Das JavaScript Ihrer eigenen Website braucht ein paar Daten | eine Serverfunktion von ezjscore |
| Ein Partner braucht genau eine bestimmte JSON-Form | eine eigene Modul-View |

## Muster, die funktionieren

- **Ein Konto je Client**, mit einer Rolle, die genau erlaubt, was der Client braucht, und mit Schlüsseln, die ablaufen.
- **Fragen Sie nach Feldern, nicht nach Seiten.** Verwenden Sie `ResponseGroups` und Routen `field/<identifier>`, um
  Antworten klein zu halten.
- **Lassen Sie das Rendern von Rich Text auf dem Server**, wenn der Client wie die Website aussehen soll: v2
  `OutputFormat=xhtml`.
- **Nur HTTPS**: `rest.ini [Authentication] RequireHTTPS=enabled` auf jeder erreichbaren Installation.
- **Cachen Sie im Client** und vermeiden Sie Abfragen im Sekundentakt; Inhaltsänderungen werden veröffentlicht, nicht
  gestreamt.

## Was es nicht gibt

Exponential 6 hat keinen GraphQL-Endpunkt, und die REST-Schnittstelle hat noch keine Route zum Aktualisieren. Projekte,
die REST v2 und GraphQL der Symfony-Plattform brauchen, verwenden die Linie Exponential Platform, die über die Legacy
Bridge neben diesem Kernel laufen kann
([Umstieg von eZ Platform oder Ibexa](umstieg-von-ez-platform-oder-ibexa.md#zwei-ziele-in-exponential),
[Legacy Bridge](../../funktionen/6.0/legacy-bridge.md)).

## Verwandte Seiten

- [Die REST-API](die-rest-api.md)
- [Anwendungsentwickler: hier beginnen](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md)
- [Standards für APIs und Protokolle](../../standards/apis-und-protokolle.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
