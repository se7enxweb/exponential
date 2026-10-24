---
title: "API-Design: REST, ezjscore und Headless"
audience: software-engineer, developer
category: software-engineering
order: 800
summary: Die Schnittstellen, die Exponential anderen Systemen und Frontends bietet - die REST-Inhalts-API von ezprestapi in v1 und v2, OAuth 2.0 und persönliche API-Schlüssel, die ezjscore-Aufrufschnittstelle und expservices, ausgehende Audit-Webhooks, RSS und Atom sowie SOAP - mit ihrem Stand, ihren Lücken und Hinweisen für den Entwurf von Headless- und Hybridsystemen und eigenen Endpunkten.
keywords: API, API-Design, REST, ezprestapi, v1, v2, OAuth, API-Schlüssel, ezjscore, Serverfunktionen, expservices, JSON, Webhooks, Headless, entkoppelt, OpenAPI, SOAP, Softwareingenieur, API design, API keys, server functions, headless, decoupled
type: help
---
# API-Design: REST, ezjscore und Headless

Diese Seite richtet sich an Ingenieure, die Exponential mit anderen Systemen verbinden, ein Frontend darauf bauen oder
eigene Endpunkte hinzufügen. Der Stand jedes Protokolls, mit Belegen und Lücken, steht in
[Standards für APIs und Protokolle](../../standards/apis-und-protokolle.md).

## Inhalt

1. [Die Schnittstellen](#die-schnittstellen)
2. [Die REST-Inhalts-API](#die-rest-inhalts-api)
3. [Die ezjscore-Aufrufschnittstelle](#die-ezjscore-aufrufschnittstelle)
4. [Ausgehende Ereignisse](#ausgehende-ereignisse)
5. [Headless und hybrid](#headless-und-hybrid)
6. [Einen eigenen Endpunkt entwerfen](#einen-eigenen-endpunkt-entwerfen)
7. [Bekannte Lücken](#bekannte-lücken)

## Die Schnittstellen

| Schnittstelle | Für | Stand |
|---|---|---|
| REST-Inhalts-API (`ezprestapi`) | Lesen und Schreiben von Inhalten aus anderen Systemen und Apps | entspricht teilweise der HTTP-Semantik |
| OAuth-2.0-Token-Endpunkt | Anwendungen, die für einen Benutzer handeln | teilweise erfüllt (kein PKCE, kein `state` auf der Serverseite) |
| Persönliche API-Schlüssel | Skripte, die als ein Benutzer handeln, mit Geltungsbereichen | Exponential 6.0.15 |
| `ezjscore`-Aufrufschnittstelle | JSON-Aufrufe von PHP aus JavaScript und Apps | erfüllt (JSON) |
| `expservices` | benannte entfernte Dienste auf der Aufrufschnittstelle | siehe [Entfernte Dienste und Apps](../../anleitungen/entfernte-dienste-und-apps.md) |
| Audit-Webhook | Zustellung von Audit-Datensätzen an ein anderes System, HMAC-signiert | erfüllt seinen eigenen Vertrag |
| RSS und Atom | Feeds (Atom nur beim Export) | [RSS-Feeds](../../anleitungen/rss-feeds.md) |
| SOAP | Legacy-Integrationen; standardmäßig aus | teilweise erfüllt |

## Die REST-Inhalts-API

- Eine Erweiterung, `ezprestapi`, bedient `/api/ezp/v1/...` und `/api/ezp/v2/...`: Lesen in beiden Versionen, Schreiben
  (Erstellen, Aktualisieren, Löschen) nur in v2. `ezprestapiprovider` ist abgekündigt.
- Authentifizierung: ein OAuth-2.0-Token von `POST /api/auth/v1/oauth/token` oder ein persönlicher API-Schlüssel als
  `Authorization: Bearer expk_...`.
- **Jedes Lesen und Schreiben prüft die Richtlinien des Benutzers** (`content/read`, `content/create`, `content/edit`,
  `content/remove`), was auch immer die Anfrage authentifiziert hat.
- Ein API-Schlüssel über seinem Ratenlimit erhält `429 Too Many Requests` mit `Retry-After`.
- Einstellungen: `settings/rest.ini`.

Anleitung mit curl-Beispielen: [REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md). Für Autoren:
[REST-API, für Anwendungsentwickler](../ersteller/anwendungsentwickler/rest-api.md).

## Die ezjscore-Aufrufschnittstelle

`/ezjscore/call/<class>::<method>` ruft eine registrierte Serverfunktion auf und antwortet mit JSON. Serverfunktionen sind
PHP-Klassen, die in `ezjscore.ini` registriert und durch Rollenrichtlinien geschützt sind. Verwenden Sie sie für das
JavaScript Ihrer eigenen Seiten und für kleine App-Aufrufe
([JSON-Endpunkte mit ezjscore](../ersteller/anwendungsentwickler/json-endpunkte-mit-ezjscore.md),
[Backend-Dienste von ezjscore](../../bc/6.0/backend-dienste-ueber-ezjscore.md),
[Referenz der ezjscore-Serverfunktionen](../erweiterungsentwicklung/teil-8-referenz/ezjscore-serverfunktionen-referenz.md)).

## Ausgehende Ereignisse

Nur das Audit-Log sendet Webhooks: Stapel von Datensätzen als JSON mit Zeitstempel und HMAC-SHA-256-Signatur, über einen
Spool mit Wiederholungen zugestellt, mindestens einmal und nach Ereignis-ID dedupliziert (`settings/audit.ini
[AuditSink_webhook]`, aus, bis eine URL gesetzt ist). Inhaltsereignisse (Veröffentlichen, Entfernen) haben keinen
allgemeinen ausgehenden Webhook; um darauf zu reagieren, verwenden Sie ein Workflow-Ereignis oder einen Listener in einer
Erweiterung ([Events, Erweiterungspunkte und Plugins](events-erweiterungspunkte-und-plugins.md)).

## Headless und hybrid

Sie behalten Inhaltsklassen, Bearbeitung, Versionen, Übersetzungen, Workflows und Berechtigungen; das Frontend ersetzt
Seiten-Templates, Menüs und Routing. Planen Sie ein:

- **Lesen** über die REST-API, mit den Berechtigungen des authentifizierten Benutzers;
- **Rich Text und Bilder**: gerendertes XHTML und Bildvarianten, mit ihren URLs ausgeliefert;
- **Vorschau** von Entwürfen für Redakteure;
- **Caching**: die eigenen Cache-Einstellungen der API und ein HTTP-Cache davor für öffentliche Lesezugriffe;
- **hybrid**: Templates für einige Seiten, die API für Apps, auf denselben Inhalten.

Details: [Headless Exponential](../ersteller/anwendungsentwickler/headless-exponential.md).

## Einen eigenen Endpunkt entwerfen

- Wählen Sie den Ort: eine REST-Route in einer Erweiterung für Ressourcen, eine ezjscore-Serverfunktion für kleine Aufrufe
  aus Ihren eigenen Seiten, eine Modul-View für HTML.
- Entscheiden Sie Berechtigungen mit Richtlinienfunktionen, nie mit Benutzer-IDs; prüfen Sie sie bei jedem Aufruf.
- Nehmen Sie Zustandsänderungen nur per POST (oder PUT, PATCH, DELETE) an, mit einem Formular-Token für Browser-Aufrufe oder
  einem Token oder Schlüssel für Maschinen.
- Geben Sie JSON mit `json_encode()` zurück; geben Sie Fehlern einen Statuscode und ein stabiles maschinenlesbares Feld.
- Halten Sie die Form stabil: Ein entferntes oder umbenanntes Feld ist ein Bruch, der Upgrade-Hinweise braucht
  ([Richtlinie zur Rückwärtskompatibilität](richtlinie-zur-rueckwaertskompatibilitaet.md)).
- Versionieren Sie den Pfad, wenn sich die Form ändern muss, wie `ezprestapi` es mit v1 und v2 tut.
- Protokollieren und auditieren Sie Schreibzugriffe; protokollieren Sie nie Tokens oder Schlüssel.

## Bekannte Lücken

Laut Standard: keine OpenAPI-Beschreibung der REST-API des Kernels (Velocity veröffentlicht eine nur für seine eigenen
Handler), kein JSON:API, keine bedingten Anfragen bei Schreibzugriffen gefunden, keine allgemeinen Inhalts-Webhooks, kein
Endpunkt für eingehende Webhooks, und dem OAuth-Server fehlt PKCE.

## Verwandte Seiten

- [OAuth und API-Schlüssel](../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md)
- [Entfernte Dienste mit expservices](../ersteller/anwendungsentwickler/entfernte-dienste-expservices.md)
- [AJAX mit ezjscore-Serverfunktionen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/ajax-mit-ezjscore-serverfunktionen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
