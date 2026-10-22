---
title: Entfernte Dienste mit expservices
audience: integrator, developer
category: integration
order: 80
summary: Die Erweiterung expservices als Integrations-API verwenden - die JSON-Hülle, der Katalog, Tokens, Lesen und Schreiben, ein idempotentes Upsert per Remote-ID über HTTP, Inhaltsjobs und eine eigene Dienstdomäne hinzufügen.
keywords: expservices, entfernte Dienste, Katalog, Token, Hülle, Upsert, Integrations-API, Inhaltsjobs, remote services, ezjscore call, expobject, getByRemoteId, create, update, expnode, expsearch, expcontentjob, catalog, expt token, X-Exp-Token, envelope, upsert, integration API
type: help
---
# Entfernte Dienste mit expservices

Diese Seite richtet sich an Integratoren, die über HTTP mehr brauchen, als die REST-Inhalts-API bietet: Objekte
aktualisieren, ein Objekt über seine Remote-ID finden, suchen, Inhaltsjobs ausführen, Benutzer oder das Audit lesen.
Die Erweiterung `expservices` macht die Funktionen der Administration zu benannten Diensten (rund 1100), die die
Aufrufschnittstelle von `ezjscore` unter `/ezjscore/call/exp<domain>::<method>` als JSON beantwortet. Das Tutorial mit
den mitgelieferten Clients ist [Entfernte Dienste und Apps](../../anleitungen/entfernte-dienste-und-apps.md); die Referenz für
Entwickler ist [expservices](../erweiterungsentwicklung/teil-8-referenz/expservices.md).

## Inhalt

1. [Warum Integratoren es verwenden](#warum-integratoren-es-verwenden)
2. [Die Hülle und die Regeln](#die-hülle-und-die-regeln)
3. [Den passenden Dienst finden](#den-passenden-dienst-finden)
4. [Ein Upsert per Remote-ID über HTTP](#ein-upsert-per-remote-id-über-http)
5. [Große Operationen als Inhaltsjobs](#große-operationen-als-inhaltsjobs)
6. [Ihre eigene Dienstdomäne](#ihre-eigene-dienstdomäne)
7. [Einstellungen](#einstellungen)

## Warum Integratoren es verwenden

| Bedarf | REST-API (`ezprestapi`) | `expservices` |
|---|---|---|
| Einen Knoten, seine Kinder, Felder lesen | ja | ja (`expnode::get`, `expnode::children`, `expobject::dataMap`) |
| Anlegen und veröffentlichen | ja (ohne `remote_id`) | ja, mit `remote_id` (`expobject::create`) |
| Ein Objekt mit einer neuen veröffentlichten Version aktualisieren | nein (`501`) | ja (`expobject::update`) |
| Ein Objekt über die Remote-ID finden | nein | ja (`expobject::getByRemoteId`) |
| Suchen | nein | ja (`expsearch::search`, `count`, `facets`) |
| Verschieben, Verbergen, Kopieren, Status, Sektion auf einem Teilbaum | nein | ja, als Inhaltsjobs (`expcontentjob::*`) |
| Benutzer, Rollen, Caches, Cronjobs, Pakete, Audit | nein | ja, jeweils hinter ihrer Richtlinie |

## Die Hülle und die Regeln

Jede Antwort hat innerhalb der `ezjscore`-Hülle eine Form:

```text
{"error_text":"","content":{"ok":true,"data":...,"meta":{...}}}
{"error_text":"","content":{"ok":false,"error":{"code":403,"message":"..."}}}
```

Listen sind seitenweise; `meta` trägt `total`, `offset`, `limit`, `count` und `has_more` (`expservices.ini [Paging]`
`DefaultLimit=25`, `MaxLimit=200`). Die Fehlercodes ähneln HTTP: 400 Argumente, 401 anmelden, 403 verweigert,
404 nicht gefunden, 409 Konflikt, 422 ungültig.

| Regel | Einzelheit |
|---|---|
| Argumente | Teile nach der Methode, mit `::` verbunden (`expnode::children::2::name::asc::5::0`); Felder zum Schreiben als `POST` |
| Zugriff | Jeder Dienst deklariert `public`, `user` oder eine Richtlinie `[module, function]`; dieselben Richtlinien, die die Administration prüft |
| Schreibvorgänge | `POST`; mit einer Sitzung auch das Formular-Token (Feld `ezxform_token` und Header `X-CSRF-Token`); mit einem `expt_...`-Token kein Formular-Token |
| Audit | Jeder Schreibvorgang wird als `service.<domain>.<method>` aufgezeichnet (`expservices.ini [Writes] Audit=enabled`) |
| Zugangsdaten | Sitzungs-Cookie oder ein Token aus `expsession::tokenCreate`, gesendet als `Authorization: Bearer` oder `X-Exp-Token` |

## Den passenden Dienst finden

```bash
export EXPSERVICES_URL=https://example.com
cd extension/expservices/bin
./expservices-client.sh catalog object | grep -E '"method"|"summary"'
./expservices-client.sh catalog search
```

Jeder Katalogeintrag nennt die Zusammenfassung, den Zugriff, das Kennzeichen `write`, die Argumente und ein Beispiel.
In der Administration steht derselbe Katalog unter **Setup > RAD** (`/setup/rad`).

## Ein Upsert per Remote-ID über HTTP

Wenn das aufrufende System keinen Code auf dem Exponential-Server ausführen kann, aber genau ein Objekt je externem
Datensatz führen muss, verwenden Sie die Remote-ID. Der mitgelieferte Python-Client
(`extension/expservices/bin/expservices_client.py`, nur Standardbibliothek) übernimmt Hülle und Token:

```python
import json, os, sys
sys.path.insert(0, "extension/expservices/bin")
from expservices_client import Client, ServiceError

c = Client("https://example.com", api_token=os.environ["EXPSERVICES_TOKEN"])

def upsert(remote_id, parent_node_id, class_identifier, attributes, language="eng-US"):
    try:
        found = c.call("expobject::getByRemoteId", remote_id)
    except ServiceError as e:
        if e.code != 404:
            raise
        found = None
    if found is None:
        return c.post("expobject::create", {
            "parent_node_id": parent_node_id, "class": class_identifier,
            "attributes": json.dumps(attributes), "language": language, "remote_id": remote_id})
    return c.post("expobject::update", {"attributes": json.dumps(attributes), "language": language},
                  args=(found["id"],))

upsert("erp-product-10442", 2, "folder", {"name": "Product 10442"})
```

`expobject::create` lehnt eine bereits verwendete Remote-ID mit `409` ab, ein zweiter Lauf legt also kein Duplikat an.
Lassen Sie nicht zwei Schreiber gleichzeitig dieselben Datensätze bearbeiten: Prüfung und Anlegen sind zwei Schritte
(unter MySQL und PostgreSQL stoppt ein eindeutiger Index auf `ezcontentobject.remote_id` das zweite Einfügen; andere
Engines behandelt diese Seite nicht). Über `expobject::setRemoteId` gesetzte Remote-IDs dürfen Buchstaben, Ziffern und
`_ . : -` enthalten, bis zu 100 Zeichen; verwenden Sie dasselbe Alphabet für IDs, die Sie an `create` übergeben.

> **Hinweis**
> Jedes `update` veröffentlicht eine neue Version. Vergleichen Sie die eingehenden Werte zuerst mit
> `expobject::dataMap` und überspringen Sie unveränderte Datensätze, sonst füllt sich die Historie des Objekts mit
> identischen Versionen ([Idempotente Importe](idempotente-importe-ueber-die-remote-id.md)).

## Große Operationen als Inhaltsjobs

Das Verschieben, Verbergen, Kopieren oder Entfernen eines großen Teilbaums läuft als Inhaltsjob in Stapeln. Über
`expservices`:

```bash
./expservices-client.sh post expcontentjob::hide node_id=<node id>
./expservices-client.sh call expcontentjob::progress <job id>
```

`expcontentjob::estimate` sagt vorab, wie viele Knoten eine Operation berührt und ob sie sofort oder als Job laufen
würde ([Inhaltsjobs](../../bc/6.0/inhaltsjobs.md)).

## Ihre eigene Dienstdomäne

Eine Integration, die anderen Systemen eigene Funktionen anbietet (zum Beispiel "den Lagerbestand eines Produkts aus
dem ERP"), kann eine Domäne hinzufügen: eine Klasse, die `expServiceBase` mit ihren `$services`-Deklarationen
erweitert, registriert in `settings/ezjscore.ini.append.php` Ihrer Erweiterung als `[ezjscServer_<domain>]` mit
`Class=<your class>`. Sie erhält dann die Hülle, die Seitenaufteilung, die Zugriffsprüfungen, das Token, das Audit und
den Katalogeintrag gratis ([Einen Dienst schreiben](../erweiterungsentwicklung/teil-8-referenz/expservices.md#einen-dienst-schreiben)).

## Einstellungen

| Datei | Block und Schlüssel | Standard | Bedeutung |
|---|---|---|---|
| `expservices.ini` | `[Services] Enabled` | `enabled` | `disabled`: Jeder Dienst antwortet mit 403 |
| `expservices.ini` | `[Paging] DefaultLimit`, `MaxLimit` | `25`, `200` | Seitengrößen |
| `expservices.ini` | `[Writes] RequireToken` | `enabled` | Das Formular-Token bei Schreibvorgängen mit Sitzung; nur für Tests ausschalten |
| `expservices.ini` | `[Writes] Audit` | `enabled` | Jeden Schreibvorgang aufzeichnen |

## Verwandte Seiten

- [Entfernte Dienste und Apps](../../anleitungen/entfernte-dienste-und-apps.md)
- [Backend-Dienste über ezjscore](../../bc/6.0/backend-dienste-ueber-ezjscore.md)
- [Entfernte Dienste für App-Ersteller](../ersteller/anwendungsentwickler/entfernte-dienste-expservices.md)
- [ezjscore-Serverfunktionen für Integrationen](ezjscore-serverfunktionen-fuer-integrationen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
