---
title: Die REST-API als Datenschnittstelle
audience: government, developer, admin
category: public-sector
order: 110
summary: Für Entwickler und Administratoren, die die REST-API von Exponential (v1 und v2, Erweiterung ezprestapi) als Maschinenschnittstelle zu Datensätzen in Betracht ziehen - was sie als JSON liefert, wie Authentifizierung und Rechte funktionieren und was Sie entscheiden müssen, bevor Sie sie für die Öffentlichkeit öffnen.
keywords: REST-API, JSON, API, ezprestapi, v1, v2, OAuth2, API-Schlüssel, RequireAuthentication, anonym, Datenschnittstelle, DataService, offene Daten, REST API, API key, anonymous, data interface, open data
type: help
---
# Die REST-API als Datenschnittstelle

Diese Seite richtet sich an Entwickler und Administratoren, die möchten, dass Maschinen Datensatzbeschreibungen und
Dateilinks direkt lesen. Exponential stellt aus einer Erweiterung, `ezprestapi`, eine REST-API für Inhalte unter
`/api/ezp/v1/...` und `/api/ezp/v2/...` bereit. Alles Folgende stammt aus
[REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md) und [Persönliche API-Schlüssel](../../../anleitungen/api-schluessel.md).

## Inhalt

1. [Was sie liefert](#was-sie-liefert)
2. [Standardmäßig mit Authentifizierung](#standardmäßig-mit-authentifizierung)
3. [Für anonyme Leser öffnen](#für-anonyme-leser-öffnen)
4. [Aufrufe, die ein Datenclient macht](#aufrufe-die-ein-datenclient-macht)
5. [Was sie nicht ist](#was-sie-nicht-ist)
6. [Bevor Sie sie ankündigen](#bevor-sie-sie-ankündigen)

## Was sie liefert

| Aufruf | Liefert |
|---|---|
| `GET /content/node/<id>` | einen Knoten, mit Metadaten, Platzierungen und auf Anfrage Feldern (`ResponseGroups`) |
| `GET /content/node/<id>/list/offset/<n>/limit/<n>` | die Unterknoten, seitenweise |
| `GET /content/node/<id>/fields` | jedes Feld des Objekts des Knotens |
| `GET /content/node/<id>/field/<identifier>` | ein Feld, zum Beispiel `license` |
| `GET /content/node/<id>/childrenCount` | die Anzahl der Unterknoten |

Lesezugriffe antworten unter v1 und v2; Schreibzugriffe nur unter v2. Die Antworten sind JSON.

Ein mit `list` gelesener Katalogordner liefert die Datensätze; jeder mit `fields` gelesene Datensatz liefert seine
Metadaten; das Dateifeld jeder Distribution liefert die Dateiinformationen.

## Standardmäßig mit Authentifizierung

`rest.ini [Authentication] RequireAuthentication=enabled` ist die Voreinstellung: Jede Inhaltsroute braucht einen
Benutzer, entweder mit einem OAuth2-Token (registriert unter **Setup > oAuth-Verwaltung**) oder mit einem persönlichen
API-Schlüssel, gesendet als `Authorization: Bearer expk_...`. Jeder Lesezugriff prüft die Richtlinie `content/read` des
Benutzers, sodass ein Client nie mehr sieht, als dieser Benutzer darf.

## Für anonyme Leser öffnen

`RequireAuthentication=disabled` lässt jede Anfrage als `DefaultUserID` laufen, oder als anonymer Benutzer, wenn diese
Einstellung leer ist; die Rechteprüfungen gelten weiter, sodass anonyme Clients genau das sehen, was anonyme Besucher
der Website sehen. Bevor Sie das ändern:

1. Stellen Sie sicher, dass die Rolle Anonymous nur öffentliche Sektionen liest
   ([Rechte für Besucher](../../../../eng-us/help/creators/website-creators/visitor-permissions.md)).
2. Entscheiden Sie, ob die Schreibrouten geschlossen bleiben müssen: Sie prüfen weiterhin `content/create`,
   `content/edit` und `content/remove`, die Anonymous nicht haben darf.
3. Planen Sie die Last ein: Eine öffentliche API kann massenhaft aufgerufen werden. Setzen Sie Ratenbegrenzungen davor
   (Webserver oder request-shield, [Sicherheitshärtung](../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)).
4. Setzen Sie `RequireHTTPS` in `rest.ini`, wenn Ihre Installation es erlaubt.
5. Leeren Sie nach der Änderung den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`).

Das ist eine Sicherheitsentscheidung; halten Sie fest, wer sie genehmigt hat.

## Aufrufe, die ein Datenclient macht

```bash
# the datasets of the catalog folder, 50 at a time
curl -s "https://www.example.gov/api/ezp/v2/content/node/<catalog node id>/list/offset/0/limit/50"

# the metadata of one dataset
curl -s "https://www.example.gov/api/ezp/v2/content/node/<dataset node id>/fields"

# only its license
curl -s "https://www.example.gov/api/ezp/v2/content/node/<dataset node id>/field/license"
```

Fügen Sie `-H "Authorization: Bearer expk_..."` hinzu, solange eine Authentifizierung verlangt wird.

## Was sie nicht ist

- **Keine Abfrageschnittstelle über die Daten in Ihren Dateien.** Die API liefert Inhaltsobjekte und ihre Felder; eine
  CSV-Datei ist ein Dateifeld, keine Zeilen, die Sie filtern können.
- **Kein DCAT.** Das JSON folgt der eigenen Struktur der API, keinem Katalogvokabular.
- **Die Atom-Route `listAtom` antwortet mit 500** (bekanntes Problem, Abschnitt 8 der Anleitung); verwenden Sie für
  Feeds **Setup > RSS**.

## Bevor Sie sie ankündigen

- [ ] Entscheiden Sie zwischen authentifiziert und anonym, und dokumentieren Sie, wie Clients einen Schlüssel
  erhalten.
- [ ] Veröffentlichen Sie eine kurze Seite mit der Basisadresse, den Aufrufen oben und der Lizenz der Daten.
- [ ] Beobachten Sie nach dem Start die Anfrageprotokolle und den Audit-Trail
  ([Anfrageprotokolle](../../../../eng-us/features/6.0/request-logs.md), [Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)).

## Verwandte Seiten

- [REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)
- [Persönliche API-Schlüssel](../../../anleitungen/api-schluessel.md)
- [Entfernte Dienste und Apps](../../../anleitungen/entfernte-dienste-und-apps.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
