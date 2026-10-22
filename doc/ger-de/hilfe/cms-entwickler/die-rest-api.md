---
title: Die REST-API
audience: cms-developer
category: cms-development
order: 29
summary: Für CMS-Entwickler, die Apps und andere Systeme anbinden, die REST-Schnittstelle, die Exponential 6 mitliefert. Die Seite behandelt die Erweiterung ezprestapi, die Routen v1 und v2, OAuth2-Tokens und persönliche API-Schlüssel, Berechtigungen, Response Groups, die Einstellungen, das bekannte Problem mit listAtom und das Hinzufügen eigener Routen.
keywords: REST, API, ezprestapi, v1, v2, Routen, content/node, list, fields, create, delete, OAuth2, persönlicher API-Schlüssel, expk_, Bearer, rest.ini, ApiPrefix, RequireAuthentication, ResponseGroups, OutputFormat, listAtom, REST-Provider, routes, personal API key, REST provider
type: help
---
# Die REST-API

Diese Seite richtet sich an Entwickler, die eine mobile App, ein Frontend einer anderen Technologie oder ein anderes
System an eine Exponential-Website anbinden. Exponential 6 liefert eine REST-Schnittstelle zum Lesen von Inhalten und
zum Anlegen und Entfernen mit; diese Seite sagt Ihnen, was sie heute abdeckt, wie Sie sich authentifizieren und wo ihre
Grenzen liegen.

## Inhalt

1. [Was die API bedient](#was-die-api-bedient)
2. [Adressen](#adressen)
3. [Die Routen](#die-routen)
4. [Authentifizierung](#authentifizierung)
5. [Berechtigungen und Antworten](#berechtigungen-und-antworten)
6. [Beispiele](#beispiele)
7. [Einstellungen](#einstellungen)
8. [Grenzen, die Sie kennen sollten](#grenzen-die-sie-kennen-sollten)
9. [Eigene Routen](#eigene-routen)

## Was die API bedient

Das REST-Rahmenwerk liegt im Kernel (`kernel/private/rest/`, `settings/rest.ini`). Die Inhaltsrouten kommen aus der
Erweiterung `ezprestapi` (`se7enxweb/ezprestapi`, von der `composer.json` im Hauptverzeichnis verlangt), deren Provider
`ezp7xRestApiProvider` die Präfixe `ezp` und `ezpl` bedient. Der Authentifizierungs-Provider des Kernels bedient `auth`,
den OAuth2-Token-Endpunkt. Der ältere `ezprestapiprovider` ist veraltet
([REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md#1-was-liefert-was-aus)).

## Adressen

```text
/api/ezp/v1/content/node/2
 |    |   |  |
 |    |   |  path
 |    |   version (v1 or v2)
 |    prefix (provider)
 ApiPrefix (rest.ini [System])
```

v1 antwortet wie immer, für vorhandene Clients; Schreibzugriffe gibt es nur in v2.

## Die Routen

Relativ zu `/api/ezp/<version>`:

| Methode | Pfad | v1 | v2 | Was sie tut |
|---|---|---|---|---|
| `GET` | `/content/node/<id>` | ja | ja | ein Knoten |
| `GET` | `/content/node/<id>/list(/offset/<n>)(/limit/<n>)(/sort/<key>(/asc\|desc))` | ja | ja | die Unterelemente |
| `GET` | `/content/node/<id>/childrenCount` | ja | ja | die Zahl der Unterelemente |
| `GET` | `/content/node/<id>/fields`, `/field/<identifier>` | ja | ja | alle Felder, ein Feld |
| `GET` | `/content/object/<id>` und seine `fields`, `field/<identifier>` | ja | ja | dasselbe über die Objekt-ID |
| `POST` | `/content/node/create` | nein | ja | unter `parentNodeID` anlegen und veröffentlichen |
| `POST` | `/content/node/<id>` | nein | ja | aktualisieren: antwortet mit 501, noch nicht implementiert |
| `DELETE` | `/content/node/delete/<id>` | nein | ja | das Objekt des Knotens mit seinen Platzierungen und seinem Teilbaum entfernen |

Abfrageparameter: `ResponseGroups=Metadata,Locations,Fields` wählt, was eine Antwort enthält; `OutputFormat=xhtml`
fügt in v2 die gerenderte Ausgabe der Ansicht des Knotens hinzu
([Die Routen](../../anleitungen/rest-api-v1-und-v2.md#2-die-routen)).

## Authentifizierung

`rest.ini [Authentication] RequireAuthentication=enabled` ist der Standard: Jede Inhaltsroute braucht einen Benutzer.

| Zugangsdaten | Für | Wie |
|---|---|---|
| **Persönlicher API-Schlüssel** | ein eigenes Skript des Benutzers | der Benutzer erstellt ihn unter **Mein Konto** > **API-Zugang** (braucht die Richtlinie `apikey/create`); gesendet als `Authorization: Bearer expk_...` |
| **OAuth2-Token** | eine Anwendung, die andere Benutzer sich anmelden lässt und für sie handelt | die Anwendung wird unter **Setup** > **oAuth-Verwaltung** registriert; Authorization-Code-Flow; Token-Endpunkt `POST /api/auth/v1/oauth/token` |

Eine Anfrage läuft immer als ein Benutzer und geht nie über dessen Richtlinien hinaus; ein Schlüssel ist zudem auf seine
Scopes beschränkt. Schlüssel werden einmal angezeigt, können ablaufen und widerrufen werden
([Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md), [OAuth2-Tokens](../../anleitungen/rest-api-v1-und-v2.md#31-oauth2-tokens)).

## Berechtigungen und Antworten

Jeder Aufruf prüft `content/read`, `content/create` oder `content/remove` seines Benutzers, wie es die Website tut.
Antworten: `200`, `201` für ein Anlegen, `400` für ein fehlendes oder unbekanntes Feld, `401` ohne gültige
Zugangsdaten, `403 access_denied`, wenn der Benutzer es nicht darf, `404` für einen unbekannten Knoten. Richten Sie
API-Zugriff ein, indem Sie einem eigenen Konto eine Rolle mit genau den Rechten geben, die der Client braucht
([Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md)).

## Beispiele

Halten Sie den Schlüssel in einer Umgebungsvariablen, nie in einer Datei, die Sie committen:

```bash
read -s EXP_API_KEY && export EXP_API_KEY
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://www.example.com/api/ezp/v1/content/node/2
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
  "https://www.example.com/api/ezp/v1/content/node/2/list/offset/0/limit/20?ResponseGroups=Fields"
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://www.example.com/api/ezp/v2/content/node/create \
  -d parentNodeID=<node id> -d classIdentifier=folder -d languageLocale=eng-US -d name='From a script'
```

Ein Anlegen antwortet mit `{"message":"Created","objectId":...,"nodeId":...}`. Mehr unter
[Beispiele mit curl](../../anleitungen/rest-api-v1-und-v2.md#4-beispiele-mit-curl) und in der Lektion
[Die API mit einem persönlichen Schlüssel aufrufen](../trainer/entwickler/modul-11-die-rest-api/die-api-mit-einem-schluessel-aufrufen.md).

## Einstellungen

| Einstellung (`rest.ini`) | Bedeutung |
|---|---|
| `[System] ApiPrefix` | der Pfad, unter dem die REST-Schnittstelle antwortet (`/api`) |
| `[ApiProvider] ProviderClass[<prefix>]` | die Provider-Klasse jedes Präfixes |
| `[Authentication] RequireAuthentication`, `AuthenticationStyle`, `RequireHTTPS` | wer aufrufen darf, wie, und nur über HTTPS |
| `[ApiKeySettings] ApiKeys` | persönliche Schlüssel an oder aus (standardmäßig an) |
| `[OAuthSettings] TokenTTL` | die Lebensdauer eines OAuth2-Tokens |

Lesen Sie sie so, wie die Website sie sieht: `./console exp:ini list rest.ini/Authentication`. Schalten Sie
`RequireHTTPS` für jede Installation ein, die für andere erreichbar ist.

## Grenzen, die Sie kennen sollten

- **Noch keine Route zum Aktualisieren.** `POST /content/node/<id>` antwortet mit 501. Suchen Sie im Katalog von
  `expservices` nach einem Aktualisierungsdienst, legen Sie ein neues Objekt an und entfernen Sie das alte, oder führen
  Sie ein Skript auf dem Server aus ([Inhalte aus PHP](inhalte-aus-php.md)).
- **`listAtom` antwortet mit 500** in v1 und v2: Verwenden Sie die Route `list` oder den RSS-Export der Website
  ([Bekanntes Problem: listAtom](../../anleitungen/rest-api-v1-und-v2.md#8-bekanntes-problem-listatom)).
- **Das ist nicht die REST-API der Symfony-Plattformen.** Clients, die für `/api/ibexa/v2` geschrieben wurden, brauchen
  Änderungen ([Umstieg von eZ Platform oder Ibexa](umstieg-von-ez-platform-oder-ibexa.md)).
- In Exponential 6 gibt es keinen GraphQL-Endpunkt.

## Eigene Routen

Ein REST-Provider in Ihrer Erweiterung fügt Routen unter einem eigenen Präfix hinzu, mit Controllern, die die
Kernel-API verwenden ([Ein eigener REST-Provider](../trainer/entwickler/modul-11-die-rest-api/ein-eigener-rest-provider.md)).
Für Funktionen der Administration und einfache JSON-Aufrufe gibt es außerdem `expservices` und die Serverfunktionen
von ezjscore ([Headless-Auslieferung](headless-auslieferung.md)).

## Verwandte Seiten

- [Entfernte Dienste und Apps](../../anleitungen/entfernte-dienste-und-apps.md)
- [Standards für APIs und Protokolle](../../standards/apis-und-protokolle.md)
- [Modul 11: Die REST-API](../trainer/entwickler/modul-11-die-rest-api/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
