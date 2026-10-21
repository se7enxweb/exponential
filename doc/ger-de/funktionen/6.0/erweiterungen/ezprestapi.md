# ezprestapi: ein REST-Content-Provider für entfernte Aktualisierungen

Diese Seite richtet sich an Entwickler, die Inhalte aus Skripten und anderen Diensten in Exponential veröffentlichen.
`ezprestapi` ist ein REST-API-Provider (Versionen 1 und 2 der `ezp`-API) zum **entfernten Aktualisieren von Inhalten**:
Andere Systeme listen, lesen, erstellen und löschen Knoten über HTTP mit einem OAuth-Token. Er ist ein registrierter
Provider des REST-Frameworks des Kernels.

Seit 1.2.6 bedient er REST API v1 und v2 als **eine** Extension: eine Aktivierung (`ActiveExtensions[]=ezprestapi`),
eine Provider-Klasse für die Präfixe `ezp` und `ezpl`, eine Routentabelle, eine OAuth2-Einrichtung. Er ersetzt den
veralteten [ezprestapiprovider](ezprestapiprovider.md). Der Leitfaden
[REST API v1 und v2: eine Extension](../../../anleitungen/rest-api-v1-und-v2.md) enthält die Routen, die Authentifizierung,
die Konfiguration und die Migration.

## Wie er eingebunden ist

Die Provider-Klassen werden in `rest.ini` angegeben:

```ini
[ApiProvider]
ProviderClass[ezp]=ezp7xRestApiProvider
ProviderClass[ezpl]=ezp7xRestApiProvider
```

Die Routen werden unterhalb des REST-Präfixes der Installation bedient und tragen eine Version. Lesende Routen
antworten unter `v1` und `v2`, schreibende nur unter `v2`. Mit den Präfixen `ezp` und `ezpl` antworten sie zum Beispiel
unter `/api/ezp/v1/content/node/<id>/list/offset/0/limit/20` und `/api/ezpl/v2/content/node/create`.

`ProviderClass[ezp]` dieser Extension ersetzt die von [ezprestapiprovider](ezprestapiprovider.md), die `v1` bediente.
Vor 1.2.5 fiel damit `v1` weg (jede `/api/ezp/v1/...` antwortete mit 404), obwohl bestehende Clients, darunter eine
mobile App, darüber lasen; seit 1.2.5 bedient dieser Provider die lesenden `v1`-Routen selbst. Dafür ist der Kernel von
Exponential 6.0.15 nötig, dessen versionierte Routen eine Liste von Versionen annehmen; auf einem älteren Kernel sind
die Routen wie bisher nur `v2`. Seit 1.2.6 setzt `ezprestapiprovider` (6.0.4) dieselbe Provider-Klasse, sodass jede
Kombination der beiden Extensions jede Inhalts-URL an `ezp7xRestContentController` leitet.

Das Template für `OutputFormat=xhtml` ist für den Controller dieser Extension gesetzt (1.2.6):

```ini
[ezp7xRestContentController_viewContent_OutputSettings]
Template=rest_pagelayout.tpl
```

Die Aufrufe zum Erstellen und Löschen stützen sich auf [nxc_powercontent](nxc_powercontent.md) (eine Aktualisierung wird
geprüft und antwortet dann mit 501). Die Authentifizierung ist das OAuth2 des REST-Frameworks: Holen Sie ein Token unter
`POST /api/auth/v1/oauth/token` (Grants `authorization_code` und `refresh_token`) und senden Sie es als Parameter
`oauth_token` oder im Header `Authorization: OAuth`.
Ein Benutzer kann auf der Website auch einen [persönlichen API-Schlüssel](../../../anleitungen/api-schluessel.md) anlegen und ihn
als `Authorization: Bearer expk_...` senden; die Routen zum Erstellen, Aktualisieren und Löschen brauchen dann zusätzlich
den Scope `publish`, `edit` oder `remove` des Schlüssels (`rest.ini [ApiKeySettings] RouteScopes[]`, `RouteGuards[]`).

## Berechtigungen

Seit 1.2.5 prüft der Controller vor jedem Aufruf die Richtlinien des aktuellen Benutzers, unabhängig davon, womit die
Anfrage authentifiziert wurde, so wie das Modul content sie prüft: `content/read` für lesende Aufrufe (bei einer Liste
oder Zählung auch für den Elternknoten), `content/create` der Klasse unterhalb von `parentNodeID` in `languageLocale`
(mit jeder Einschränkung), `content/edit` für eine Aktualisierung, `content/remove` für jeden Platz des Objekts und
alles darunter bei einer Entfernung. Eine Ablehnung antwortet mit `403 {"error":"access_denied","error_message":...}`
und schreibt nichts. Vor 1.2.5 fragten die schreibenden Aufrufe nichts ab: Ein OAuth-Token konnte erstellen und
entfernen, wo sein Benutzer kein Recht dazu hatte. Die Prüfung ist `expRestContentPermission` des Kernels
(Exponential 6.0.15); auf einem älteren Kernel lehnt die Extension jeden schreibenden Aufruf mit 403 ab, statt ihn
ungeprüft auszuführen. Die Einzelheiten stehen im Leitfaden:
[Berechtigungen der REST-Schnittstelle](../../../anleitungen/api-schluessel.md#32-berechtigungen-der-rest-schnittstelle).

| Antwort | Wann |
|---|---|
| `201 {"message":"Created","objectId","nodeId"}` | ein erstelltes Objekt wurde veröffentlicht |
| `200 {"message":"Removed","nodeId","objectId"}` | eine Entfernung ist erfolgt |
| `400 invalid_request` | `parentNodeID`, `classIdentifier` oder `languageLocale` fehlt oder nennt eine Klasse oder Sprache, die es nicht gibt |
| `403 access_denied` | der Benutzer darf es nicht |
| `404 not_found` | der Knoten existiert nicht |
| `501 not_implemented` | eine erlaubte Aktualisierung: Das Aktualisieren der Attributwerte ist noch nicht implementiert, nichts ändert sich |
| `401` | kein oder ein ungültiges Token bzw. ein ungültiger Schlüssel (auch bei `POST` oder `DELETE`; vor 6.0.15 antworteten sie mit 405) |

## Routen

Registriert von `ezp7xRestApiProvider` (`classes/rest_provider.php`); alle außer dem Atom-Feed laufen in
`ezp7xRestContentController`, sodass sie alle dessen Prüfungen erhalten:

| Route | Versionen | Zweck |
|---|---|---|
| `GET /content/node/:nodeId` | v1, v2 | Ein Knoten |
| `GET /content/node/:nodeId/list...` (eine Route mit regulärem Ausdruck) | v1, v2 | Kinder auflisten (Offset, Limit, Sortierung) |
| `GET /content/node/:nodeId/fields`, `.../field/:fieldIdentifier`, `.../childrenCount` | v1, v2 | Felder und Zählungen |
| `GET /content/object/:objectId`, `.../fields`, `.../field/:fieldIdentifier` | v1, v2 | Dasselbe über die Objekt-ID |
| `GET /content/node/:nodeId/listAtom` (`ezpRestAtomController`) | v1, v2 | Atom-Feed (antwortet mit 500: [bekanntes Problem](../../../anleitungen/rest-api-v1-und-v2.md#8-bekanntes-problem-listatom)) |
| `POST /content/node/create` | v2 | Inhalt erstellen und veröffentlichen |
| `DELETE` oder `POST /content/node/delete/:nodeId` | v2 | Das Objekt des Knotens entfernen |
| `POST /content/node/:nodeId` | v2 | Aktualisieren (geprüft, dann 501: noch nicht implementiert) |

| Vor 1.2.5 und 6.0.15 | Seitdem |
|---|---|
| `/api/ezp/v1/...`: 404 für jede Route | die lesenden Routen antworten unter v1 |
| `POST /api/ezp/v2/content/node/create` ohne oder mit abgelehntem Token: 405 | 401 oder 403 |
| `DELETE /api/ezp/v2/content/node/delete/<id>`: 405 | wird ausgeführt (auch `POST`) |
| ein erfolgreiches Löschen antwortete mit 500 und "has been removed" | 200 mit den IDs |
| Felder und Objekt-Lesezugriffe liefen in `ezpRestContentController` von ezprestapiprovider | im Controller dieser Extension |

## Aufruf über die Kommandozeile

`bin/php/ezrestcall.php` (1.2.0) ist ein Beispiel-Client auf Basis von Guzzle. Guzzle ist nicht Teil der Distribution:
Führen Sie `composer require guzzlehttp/guzzle` in der Installation aus, die das Skript ausführt. Es spricht HTTPS mit
einem Host:

```bash
# list the children of a node (the script appends /limit/<n> and the token)
php bin/php/ezrestcall.php --host=www.example.com --token=<oauth token> --action=list --uri=/api/ezpl/v2/content/node/2/list
```

| Option | Bedeutung |
|---|---|
| `--host` | Hostname ohne Protokoll |
| `--token` | Ein OAuth-Token |
| `--action` | `list` (Standard), `login` oder `create` |
| `--uri` | Der aufzurufende Pfad |

Die Aktionen `login` und `create` sind Beispiele zum Lesen und Anpassen. `create` sendet einen Artikel an
`/api/ezpl/v2/content/node/create` mit `parentNodeID`, `classIdentifier`, `languageLocale` und den Attributwerten. Die
Werte darin gehören zur eigenen Website des Autors; kopieren Sie das Skript also und ersetzen Sie sie vor der
Verwendung. Bewahren Sie keine echten Zugangsdaten oder Tokens in einem Skript oder im Shell-Verlauf auf gemeinsam
genutzten Rechnern auf.

## Was sich geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 1.2.0 | 29. Oktober 2024 | Erster Import der Abhängigkeiten der Lösung, der Finanzierungsinformationen und des Beispiel-Clients. |
| 1.2.1 bis 1.2.4 | 27. September bis 2. Oktober 2026 | Version, Lizenz und Website; Beschreibung; Header von 7x und Exponential Foundation in den Einstiegsdateien; Befehle und Cronjob-Teile sind Klassen, die die Dateien aufrufen und über die gemeinsamen Hilfsfunktionen starten. |
| 1.2.5 | 6. Oktober 2026 | Jeder lesende und schreibende Aufruf prüft die Richtlinien des aktuellen Benutzers bei jeder Art der Authentifizierung; lesende Routen antworten wieder unter v1; Löschen akzeptiert `DELETE`; Erstellen und Löschen antworten mit 201 und 200 und den IDs. Benötigt Exponential 6.0.15 für die Prüfungen und v1. |
| 1.2.6 | 15. Oktober 2026 | `OutputFormat=xhtml` wird wieder gerendert (`[ezp7xRestContentController_viewContent_OutputSettings] Template=rest_pagelayout.tpl`; es antwortete mit einem leeren `renderedOutput`); Name, Beschreibung und README sagen, dass die Extension REST API v1 und v2 bedient und ezprestapiprovider ersetzt; die Modelltests wurden aus ezprestapiprovider übernommen. |

[ezprestapiprovider](ezprestapiprovider.md) ist veraltet; sein letztes Release, 6.0.4, ist eine Kompatibilitätsschicht,
die Provider und Controller dieser Extension erweitert. Siehe die
[Migration](../../../anleitungen/rest-api-v1-und-v2.md#6-migration-von-ezprestapiprovider).

## Verwandte Seiten

- [REST API v1 und v2: eine Extension](../../../anleitungen/rest-api-v1-und-v2.md)
- [nxc_powercontent](nxc_powercontent.md), [ezprestapiprovider](ezprestapiprovider.md) (veraltet)
- [Entfernte Dienste (expservices)](../entfernte-dienste-expservices.md)
- [Persönliche API-Schlüssel](../../../anleitungen/api-schluessel.md): REST-Anfragen mit einem Schlüssel des Benutzers
- [CLI-, Cronjob- und View-Abstraktionen](../../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)
- [Chronik](../../../geschichte/erweiterungen/ezprestapi.md) und [Versionshinweise](../../../../changelogs/extensions/ezprestapi.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/ezprestapi.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2024-10](../../../geschichte/erweiterungen/monate/2024-10.md), [2026-10](../../../geschichte/erweiterungen/monate/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
