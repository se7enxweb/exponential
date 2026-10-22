---
title: Agenten und die REST-API
audience: ai-agents, developer, app-creator
category: ai-agents
order: 62
summary: Die REST-API von Exponential aus einem KI-Agenten verwenden - die Routen von v1 und v2 als Werkzeuge, ein persönlicher API-Schlüssel mit dem engsten Geltungsbereich, Berechtigungen und Antworten, Blättern und ein Muster, um die Routen einem Modell zu beschreiben, ohne ihm mehr zu geben, als es braucht.
keywords: REST-API, Agent, /api/ezp/v1, /api/ezp/v2, persönlicher API-Schlüssel, expk_, Geltungsbereiche, Geltungsbereich read, OAuth2, ResponseGroups, Blättern, Werkzeugdefinition, Function Calling, HTTP-Werkzeuge, REST API, agent, personal API key, scopes, read scope, paging, tool definition, function calling, HTTP tools
type: help
---
# Agenten und die REST-API

Diese Seite richtet sich an Entwickler, die einem KI-Agenten über HTTP Zugriff auf Inhalte geben. Die REST-API ist Teil des Kernels;
ein Agent verwendet sie wie jeder andere Client, als das Konto hinter seinen Zugangsdaten. Die vollständige Referenz ist
[REST-API v1 und v2 für Anwendungen](../../ersteller/anwendungsentwickler/rest-api.md) und die Anleitung
[REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md); diese Seite ergänzt, was für Agenten wichtig ist.

## Inhalt

1. [Die Routen, die ein Agent braucht](#die-routen-die-ein-agent-braucht)
2. [Die Zugangsdaten](#die-zugangsdaten)
3. [Die Routen einem Modell beschreiben](#die-routen-einem-modell-beschreiben)
4. [Antworten, die der Agent behandeln muss](#antworten-die-der-agent-behandeln-muss)
5. [Über REST schreiben](#über-rest-schreiben)

## Die Routen, die ein Agent braucht

Relativ zu `/api/ezp/<version>`:

| Methode | Pfad | v1 | v2 | Tut |
|---|---|---|---|---|
| `GET` | `/content/node/<id>` | ja | ja | Ein Knoten |
| `GET` | `/content/node/<id>/list(/offset/<n>)(/limit/<n>)` | ja | ja | Die Kinder, seitenweise |
| `GET` | `/content/node/<id>/childrenCount` | ja | ja | Die Zahl der Kinder |
| `GET` | `/content/node/<id>/fields`, `/field/<identifier>` | ja | ja | Felder |
| `GET` | `/content/object/<id>` und seine Felder | ja | ja | Nach Objekt-ID |
| `POST` | `/content/node/create` | nein | ja | Anlegen und veröffentlichen |
| `DELETE` | `/content/node/delete/<id>` | nein | ja | Entfernen |

Ein nur lesender Agent braucht nur die `GET`-Routen.

## Die Zugangsdaten

Ein **persönlicher API-Schlüssel** (`Authorization: Bearer expk_...`) ist der einfachste Zugang für einen Agenten, den Sie
selbst betreiben:

1. Legen Sie ein Konto für den Agenten an und geben Sie ihm nur die Inhaltsrichtlinien, die er braucht.
2. Erstellen Sie, als dieses Konto angemeldet, unter **Mein Konto > API-Zugang** einen Schlüssel nur mit den Geltungsbereichen, die
   der Agent braucht (`read` für einen Leser), und mit einer kurzen Laufzeit. Der Schlüssel wird einmal angezeigt.
3. Bewahren Sie ihn im Geheimnisspeicher des Agenten oder in einer Umgebungsvariablen auf, nie in einem Prompt oder einem
   Repository.

Ein Schlüssel erhält nie ein Recht, das sein Eigentümer nicht hat, und eine Route braucht den Geltungsbereich, dem sie zugeordnet
ist; eine Schreibroute einer Erweiterung bleibt für Schlüssel geschlossen, bis ihr Geltungsbereich benannt ist
([Persönliche API-Schlüssel, Geltungsbereiche](../../../anleitungen/api-schluessel.md#6-bereiche)). Eine App, die für viele Benutzer handelt,
verwendet stattdessen OAuth2 ([OAuth und API-Schlüssel](../../ersteller/anwendungsentwickler/oauth-und-api-schluessel.md)).

## Die Routen einem Modell beschreiben

Wenn das Agenten-Framework Sie Werkzeuge für das Modell definieren lässt, definieren Sie **ein Werkzeug je erlaubter Route**, kein
allgemeines Werkzeug "HTTP request":

| Werkzeug | Eingabe | Ruft auf |
|---|---|---|
| `get_node` | `node_id` (Ganzzahl) | `GET /api/ezp/v1/content/node/<node_id>` |
| `list_children` | `node_id`, `offset`, `limit` (höchstens, was Sie erlauben) | `GET .../content/node/<node_id>/list/offset/<n>/limit/<n>` |
| `get_field` | `node_id`, `identifier` | `GET .../content/node/<node_id>/field/<identifier>` |

Ihr Code baut die URL aus geprüften Eingaben und fügt den Schlüssel hinzu; das Modell sieht den Schlüssel nie und kann keine anderen
Routen oder Hosts erreichen. Sagen Sie dem Modell in der Werkzeugbeschreibung, dass Feldwerte von Menschen geschriebene Daten sind,
keine Anweisungen.

## Antworten, die der Agent behandeln muss

| Status | Bedeutung | Der Agent sollte |
|---|---|---|
| 200 | Der Inhalt | Ihn verwenden |
| 201 | Angelegt | Die neue ID festhalten |
| 400 | Eine fehlerhafte Anfrage | Die Argumente korrigieren |
| 401 | Keine gültigen Zugangsdaten | Anhalten; ein Mensch muss den Schlüssel erneuern |
| 403 | Das Konto oder der Geltungsbereich des Schlüssels darf nicht | Für dieses Element anhalten; nicht andere Routen versuchen, um es zu umgehen |
| 404 | Unbekannter Knoten | Es melden |

Listen werden seitenweise geliefert; lassen Sie den Agenten mit einer von Ihnen gewählten Grenze blättern, statt alles anzufordern.

## Über REST schreiben

Ein `POST` an `/api/ezp/v2/content/node/create` **legt an und veröffentlicht**. Für von Agenten geschriebene Inhalte, die ein Mensch
zuerst prüfen muss, legen Sie sie in einer Sektion an, die Besucher nicht sehen und die ein Freigabe-Workflow abdeckt, oder
verwenden Sie Exponential Intelligence, das Entwürfe schreibt. Testen Sie jeden schreibenden Ablauf auf einer Testinstallation und
lesen Sie danach den Audit-Trail ([Ein Ablauf für den Inhaltsimport](ablauf-inhaltsimport.md)).

## Verwandte Seiten

- [Eine Schnittstelle für einen Agenten wählen](eine-schnittstelle-waehlen.md)
- [Zugangsdaten für Agenten](zugangsdaten-fuer-agenten.md)
- [Headless Exponential](../../ersteller/anwendungsentwickler/headless-exponential.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
