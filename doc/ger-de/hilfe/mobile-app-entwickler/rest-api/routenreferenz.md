---
title: Routenreferenz
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 120
summary: Jede Route des Content-Providers von ezprestapi mit ihrer HTTP-Methode, den Versionen, unter denen sie antwortet, der Controller-Aktion dahinter und dem Scope, den ein persönlicher API-Schlüssel braucht - dazu die Authentifizierungsroute und die Query-Parameter, die die Routen verstehen.
keywords: Routen, Referenz, Routenreferenz, ezp7xRestApiProvider, ezp7xRestContentController, content node, content object, list, childrenCount, fields, field, Anlegen, Löschen, Aktualisieren, listAtom, oauth token, ResponseGroups, Translation, OutputFormat, Scope, routes, reference, create, delete, update, scope
type: help
---
# Routenreferenz

Diese Seite richtet sich an App-Entwickler, die die vollständige Liste der Routen in einer Tabelle möchten. Sie stammt
aus der Routentabelle von `ezp7xRestApiProvider::getRoutes()` in `extension/ezprestapi/classes/rest_provider.php` und
aus `settings/rest.ini`. Alle Inhaltspfade sind relativ zu `/api/ezp/<version>` (oder zum Alias-Präfix
`/api/ezpl/<version>`).

## Inhalt

1. [Inhaltsrouten](#inhaltsrouten)
2. [Die Authentifizierungsroute](#die-authentifizierungsroute)
3. [Query-Parameter](#query-parameter)
4. [Scopes für persönliche API-Schlüssel](#scopes-für-persönliche-api-schlüssel)
5. [Nicht passende Methoden](#nicht-passende-methoden)

## Inhaltsrouten

| Routenname | Methode | Pfad | v1 | v2 | Controller-Aktion |
|---|---|---|---|---|---|
| `ezpNode` | `GET` | `/content/node/<id>` | ja | ja | `viewContent` |
| `ezpNodeUpdate` | `POST` | `/content/node/<id>` | nein | ja | `UpdateContentNode` (antwortet nach der Berechtigungsprüfung mit `501`) |
| `ezpList` | `GET` | `/content/node/<id>/list(/offset/<n>)(/limit/<n>)(/sort/<key>(/asc\|desc))` | ja | ja | `list` |
| `ezpChildrenCount` | `GET` | `/content/node/<id>/childrenCount` | ja | ja | `countChildren` |
| `ezpFieldsByNode` | `GET` | `/content/node/<id>/fields` | ja | ja | `viewFields` |
| `ezpFieldByNode` | `GET` | `/content/node/<id>/field/<identifier>` | ja | ja | `viewField` |
| `ezpObject` | `GET` | `/content/object/<id>` | ja | ja | `viewContent` |
| `ezpFieldsByObject` | `GET` | `/content/object/<id>/fields` | ja | ja | `viewFields` |
| `ezpFieldByObject` | `GET` | `/content/object/<id>/field/<identifier>` | ja | ja | `viewField` |
| `ezpNodeCreate` | `POST` | `/content/node/create` | nein | ja | `CreateContentNode` |
| `ezpNodeDelete` | `POST` oder `DELETE` | `/content/node/delete/<id>` | nein | ja | `DeleteContentNode` |
| `ezpListAtom` | `GET` | `/content/node/<id>/listAtom` | ja | ja | Atom-Feed; antwortet mit `500`, ein [bekanntes Problem](../../../anleitungen/rest-api-v1-und-v2.md#8-bekanntes-problem-listatom) |

Jede Inhaltsaktion läuft in `ezp7xRestContentController`, der bei jedem Aufruf die Richtlinien des Benutzers prüft.

## Die Authentifizierungsroute

| Methode | Pfad | Bewirkt |
|---|---|---|
| `POST` | `/api/auth/v1/oauth/token` | tauscht einen Autorisierungscode oder ein Refresh-Token gegen ein Access-Token |

Das Präfix `auth` gehört zu `ezpRestAuthProvider` des Kernels (`rest.ini [ApiProvider] ProviderClass[auth]`). Die
Autorisierungsseite selbst ist eine normale Seite der Site, `/oauth/authorize`
([OAuth2-Autorisierungscode-Ablauf](../anmeldung/oauth2-autorisierungscode-ablauf.md)).

## Query-Parameter

Der Request-Parser des REST-Frameworks (`kernel/private/rest/classes/request/http_parser.php`) übernimmt drei
Parameter aus dem Query-String:

| Parameter | Verwendet von | Werte |
|---|---|---|
| `ResponseGroups` | Lesezugriffen auf Knoten, Objekte, Felder und Listen | eine kommagetrennte Liste (`Metadata,Fields`) oder wiederholtes `ResponseGroups[]=...` ([Antwortgruppen](antwortgruppen.md)) |
| `Translation` | Lesezugriffen auf Knoten, Objekte und Felder | eine Locale, zum Beispiel `eng-GB` ([Mehrsprachige Inhalte](../inhaltsmodellierung/mehrsprachige-inhalte.md)) |
| `OutputFormat` | Lesezugriffen auf Knoten und Objekte | `xhtml` ([Gerenderte XHTML-Ausgabe](gerenderte-xhtml-ausgabe.md)) |

`Translation` und `OutputFormat` werden in die `links` der Antwort übernommen, sodass sie beim Folgen eines Links
erhalten bleiben.

## Scopes für persönliche API-Schlüssel

Ein persönlicher API-Schlüssel braucht für jede Route einen Scope (`rest.ini [ApiKeySettings]`):

| Route | Scope |
|---|---|
| jede unten nicht aufgeführte Route mit `GET` oder `HEAD` | `read` (`DefaultReadScope`) |
| `ezp7xRestContentController_CreateContentNode` | `publish` |
| `ezp7xRestContentController_UpdateContentNode` | `edit` |
| `ezp7xRestContentController_DeleteContentNode` | `remove` |

Jede andere Route ohne `GET` wird für Schlüssel abgewiesen, bis ihr Scope benannt ist
([Persönliche API-Schlüssel: Scopes](../../../anleitungen/api-schluessel.md#6-bereiche)). OAuth-Tokens haben keine solchen Scopes;
dort entscheiden die Richtlinien des Benutzers.

## Nicht passende Methoden

Seit 6.0.15 beendet eine Route, deren Pfad passt, die aber die Methode nicht annimmt, die Suche nicht mehr: Der Router
probiert die nächsten Routen und antwortet erst dann mit `405` und einem Header `Allow`, wenn keine die Methode
annimmt. Ein abgewiesener Schreibzugriff antwortet mit `401` oder `403`, nie mit `405`
([Persönliche API-Schlüssel, Routen](../../../anleitungen/api-schluessel.md#31-routen-des-providers-ezp)).

## Verwandte Seiten

- [REST-API v1 und v2: die Routen](../../../anleitungen/rest-api-v1-und-v2.md#2-die-routen)
- [Fehler und Statuscodes](fehler-und-statuscodes.md)
- [REST-API v1 und v2 für Anwendungen](../../ersteller/anwendungsentwickler/rest-api.md#routen)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
