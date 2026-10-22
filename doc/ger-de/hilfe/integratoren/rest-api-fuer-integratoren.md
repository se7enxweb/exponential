---
title: Die REST-API für Integratoren
audience: integrator, developer
category: integration
order: 50
summary: Was die REST-Inhalts-API von ezprestapi einer Integration bietet - die Routen unter v1 und v2, Listen seitenweise lesen, Inhalte anlegen und entfernen, die zu behandelnden Statuscodes, der Antwort-Cache und die Lücken (kein Update, listAtom) - mit Beispielen in curl und Python.
keywords: REST, API, Integrations-Client, Statuscodes, seitenweise lesen, anlegen, entfernen, ezprestapi, /api/ezp/v1, /api/ezp/v2, content/node, list, offset, limit, ResponseGroups, create, delete, 401, 403, 429, rest.ini, integration client
type: help
---
# Die REST-API für Integratoren

Diese Seite richtet sich an Entwickler von Systemen, die Exponential über HTTP aufrufen. Die REST-Inhalts-API wird von
der Erweiterung `ezprestapi` (von der `composer.json` im Wurzelverzeichnis als `~1.2.6` verlangt) auf dem
REST-Framework des Kernels (`kernel/private/rest/`) bereitgestellt. Die vollständige Referenz ist
[REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md); diese Seite wählt aus, was eine Integration braucht, und
ergänzt Ratschläge für die Client-Seite.

## Inhalt

1. [Was sie kann und was nicht](#was-sie-kann-und-was-nicht)
2. [Die Routen](#die-routen)
3. [Eine große Liste seitenweise lesen](#eine-große-liste-seitenweise-lesen)
4. [Anlegen und entfernen](#anlegen-und-entfernen)
5. [Antworten, die Ihr Client behandeln muss](#antworten-die-ihr-client-behandeln-muss)
6. [Ein kleiner Client in Python](#ein-kleiner-client-in-python)
7. [Einstellungen, die das Verhalten ändern](#einstellungen-die-das-verhalten-ändern)

## Was sie kann und was nicht

| Kann | Kann nicht (Stand `ezprestapi` 1.2.6) |
|---|---|
| Einen Knoten oder ein Objekt mit Metadaten, Orten und Feldern lesen | Inhalte aktualisieren: `POST /content/node/<id>` prüft `content/edit` und antwortet dann mit `501` |
| Die Kinder eines Knotens auflisten, mit Offset, Limit und Sortierung | Nach Text suchen oder nach Feld filtern (verwenden Sie `expservices` oder ein Fetch in einer eigenen Route) |
| Kinder zählen, ein Feld lesen | `listAtom` beantworten (antwortet mit `500`, ein bekanntes Problem; verwenden Sie den RSS-Export der Website) |
| Ein Objekt unterhalb eines Elternknotens anlegen und veröffentlichen (v2) | Sich selbst in OpenAPI beschreiben |
| Ein Objekt mit allen seinen Orten entfernen (v2) | Verschieben, verbergen oder Sektionen ändern |

Lesende Aufrufe antworten unter `v1` und `v2`, schreibende nur unter `v2`. Links in einer v1-Antwort bleiben in v1.

## Die Routen

Alle Pfade liegen unterhalb von `/api/ezp/v1` oder `/api/ezp/v2` (das Präfix `ezpl` ist ein Alias von `ezp`).

| Methode | Pfad | v1 | v2 |
|---|---|---|---|
| `GET` | `/content/node/<id>` | ja | ja |
| `GET` | `/content/node/<id>/list(/offset/<n>)(/limit/<n>)(/sort/<key>(/asc\|desc))` | ja | ja |
| `GET` | `/content/node/<id>/childrenCount` | ja | ja |
| `GET` | `/content/node/<id>/fields`, `/content/node/<id>/field/<identifier>` | ja | ja |
| `GET` | `/content/object/<id>`, `.../fields`, `.../field/<identifier>` | ja | ja |
| `POST` | `/content/node/create` | nein | ja |
| `DELETE` oder `POST` | `/content/node/delete/<id>` | nein | ja |

Abfrageparameter der lesenden Aufrufe: `ResponseGroups=Metadata,Locations,Fields` wählt die Teile der Antwort, und
`OutputFormat=xhtml` ergänzt die gerenderte Ansicht in `renderedOutput`.

## Eine große Liste seitenweise lesen

Fragen Sie zuerst die Anzahl ab und gehen Sie die Liste dann mit fester Seitengröße durch. Sortieren Sie nach einem
stabilen Schlüssel, damit sich die Seiten beim Lesen nicht verschieben.

```bash
export EXP_API_KEY='expk_...'
BASE=https://example.com/api/ezp/v1

curl -s -H "Authorization: Bearer $EXP_API_KEY" "$BASE/content/node/<node id>/childrenCount"
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "$BASE/content/node/<node id>/list/offset/0/limit/50?ResponseGroups=Metadata,Fields"
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "$BASE/content/node/<node id>/list/offset/50/limit/50?ResponseGroups=Metadata,Fields"
```

Aufgelistet werden nur die Kinder eines Knotens. Um einen tiefen Baum zu lesen, gehen Sie ihn Ebene für Ebene durch
oder lassen Sie einen serverseitigen Export den ganzen Teilbaum in einem Lauf schreiben
([Einen Export schreiben](einen-export-oder-ausgehenden-abgleich-schreiben.md)).

## Anlegen und entfernen

```bash
# create and publish a folder below a parent (v2 only)
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/create \
     -d parentNodeID=<parent node id> -d classIdentifier=folder -d languageLocale=eng-US -d name='From the ERP'
# {"message":"Created","objectId":<object id>,"nodeId":<node id>}

# remove it with all its locations and everything below them (v2 only)
curl -s -X DELETE -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/delete/<node id>
```

Die Felder zum Anlegen sind `parentNodeID`, `classIdentifier`, `languageLocale` und dann die Attributbezeichner der
Klasse. Das Anlegen wird als `content/create` dieser Klasse unterhalb dieses Elternknotens in dieser Sprache geprüft,
und der Workflow für das Veröffentlichen läuft wie in der Administration. Das Backend für das Anlegen ist
`nxc_powercontent`.

> **Hinweis**
> Das Anlegen per REST hat kein Feld `remote_id` (der Controller liest `parentNodeID`, `classIdentifier` und
> `languageLocale` und behandelt jedes andere Feld als Attribut). Lesende Aufrufe liefern die Remote-IDs aber: Die
> Metadaten einer Antwort tragen `objectRemoteId`, bei einem Knoten `nodeRemoteId`. Eine Integration, die ihre Objekte
> wiederfinden muss, sollte die zurückgegebene `objectId` bei sich speichern oder Inhalte aus PHP anlegen, wo
> `remote_id` unterstützt wird ([Idempotente Importe](idempotente-importe-ueber-die-remote-id.md)).

## Antworten, die Ihr Client behandeln muss

| Status | Bedeutung | Reaktion des Clients |
|---|---|---|
| `200`, `201` | Erledigt | Weitermachen |
| `400` | Ein Feld eines Schreibvorgangs fehlt oder nennt eine unbekannte Klasse oder Sprache | Die Anfrage korrigieren; nicht unverändert wiederholen |
| `401` `invalid_token` oder `expired_token` | Keine, unbekannte, widerrufene oder abgelaufene Zugangsdaten; oder ein Proxy hat den Header verworfen | Das Token erneuern oder Alarm geben; nicht in einer Schleife wiederholen |
| `403` `access_denied` | Der Benutzer darf das mit diesem Knoten nicht tun | Die Rolle korrigieren, nicht den Client |
| `403` `insufficient_scope` | Dem Schlüssel fehlt der Scope der Route, oder sein Besitzer hat die Richtlinie verloren | Dem Schlüssel den Scope geben oder einen anderen Schlüssel verwenden |
| `404` | Unbekannter Knoten oder unbekanntes Objekt | In einem Abgleich als "verschwunden" behandeln |
| `405` | Die Route nimmt diese Methode nicht an (ein Schreibvorgang an v1) | v2 verwenden |
| `429` `rate_limited` mit `Retry-After` | Über `RateLimitPerMinute` des Schlüssels | Die angegebenen Sekunden warten, dann wiederholen |
| `500`, `502`, `503` | Server oder Wartung (`503` mit `Retry-After` im Wartungsmodus) | Mit Backoff wiederholen, dann Alarm geben |

## Ein kleiner Client in Python

Nur mit der Standardbibliothek:

```python
import json, os, time, urllib.error, urllib.request

BASE = 'https://example.com/api/ezp/v1'
KEY = os.environ['EXP_API_KEY']

def get(path):
    for attempt in range(5):
        request = urllib.request.Request(BASE + path, headers={'Authorization': 'Bearer ' + KEY})
        try:
            with urllib.request.urlopen(request, timeout=30) as answer:
                return json.load(answer)
        except urllib.error.HTTPError as error:
            if error.code in (429, 502, 503):
                time.sleep(int(error.headers.get('Retry-After', 2 ** attempt)))
                continue
            raise
    raise RuntimeError('gave up on ' + path)

print(get('/content/node/2')['metadata']['objectName'])
```

## Einstellungen, die das Verhalten ändern

`settings/rest.ini`, überschrieben in `settings/override/rest.ini.append.php`:

| Einstellung | Standard | Wirkung für eine Integration |
|---|---|---|
| `[System] ApiPrefix` | `/api` | Der Pfad, unterhalb dessen die API antwortet |
| `[Authentication] RequireAuthentication` | `enabled` | `disabled` führt jede Anfrage als `DefaultUserID` oder anonym aus; Richtlinien gelten weiterhin |
| `[Authentication] AuthenticationStyle` | `ezpRestOauthAuthenticationStyle` | OAuth oder `ezpRestBasicAuthStyle`; API-Schlüssel funktionieren mit beiden |
| `[Authentication] RequireHTTPS` | `disabled` | Reines HTTP ablehnen |
| `[OAuthSettings] TokenTTL` | `3600` | Lebensdauer eines OAuth-Tokens |
| `[CacheSettings] ApplicationCache` | | Der Antwort-Cache: eine Antwort je Benutzer, nur `GET` und `HEAD` |

Leeren Sie nach einer Änderung den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`), laden Sie
PHP-FPM neu und starten Sie Velocity neu, wenn es die Website ausliefert.

## Verwandte Seiten

- [REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md) und [Persönliche API-Schlüssel](../../anleitungen/api-schluessel.md)
- [ezprestapi](../../funktionen/6.0/erweiterungen/ezprestapi.md), [nxc_powercontent](../../funktionen/6.0/erweiterungen/nxc_powercontent.md)
- [Die REST-API für App-Ersteller](../ersteller/anwendungsentwickler/rest-api.md)
- [Zugangsdaten für eine Integration wählen](zugangsdaten-fuer-eine-integration-waehlen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
