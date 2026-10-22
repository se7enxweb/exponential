---
title: Eine Integrationsschnittstelle wählen
audience: integrator, developer
category: integration
order: 30
summary: Eine Entscheidungshilfe, um für jeden Datenfluss die richtige Schnittstelle zu wählen - wo der Code läuft, wie viele Daten fließen, wie schnell sie ankommen müssen und welche Schnittstellen passen - mit durchgespielten Entscheidungen für häufige Aufgaben.
keywords: Auswahl, Entscheidung, Integrationsschnittstelle, REST, PHP-API, Cronjob, Workflow, Event-Listener, expservices, RSS, Pakete, xrowextract, Volumen, Latenz, Abwägungen, choosing, decision, integration surface, volume, latency, trade-offs
type: help
---
# Eine Integrationsschnittstelle wählen

Diese Seite ist für Lösungsarchitekten und Integrationsentwickler, die einen Datenfluss bauen müssen und dafür
mehrere Wege haben. Exponential bietet HTTP-APIs, serverseitiges PHP, Feeds, Dateien und Hooks; die meisten Flüsse
lassen sich auf mehr als eine Art bauen. Vier Fragen grenzen die Wahl schnell ein.

## Inhalt

1. [Vier Fragen](#vier-fragen)
2. [Die Entscheidungstabelle](#die-entscheidungstabelle)
3. [Durchgespielte Entscheidungen](#durchgespielte-entscheidungen)
4. [Schnittstellen kombinieren](#schnittstellen-kombinieren)
5. [Warnsignale](#warnsignale)

## Vier Fragen

1. **Wo kann Ihr Code laufen?** Auf dem Exponential-Server (als Erweiterung, CLI-Skript oder Cronjob) oder nur auf
   einem anderen Rechner? Code auf dem Server kann die PHP-Inhalts-API nutzen, die vollständigste Schnittstelle; Code
   anderswo muss HTTP, Feeds oder Dateien verwenden.
2. **Wie viele Daten fließen, und wie oft?** Eine Handvoll Datensätze pro Stunde ist über HTTP in Ordnung. Zehntausende
   Datensätze pro Nacht gehören in einen serverseitigen Stapel mit Fortschritt und Probelauf.
3. **Wie schnell muss eine Änderung ankommen?** „Innerhalb von Sekunden nach dem Veröffentlichen" spricht für ein
   Workflow-Ereignis oder einen Event-Listener; „bis morgen früh" für einen Cronjob.
4. **Wer startet den Austausch?** Exponential schiebt zum anderen System, das andere System holt aus Exponential,
   oder das andere System schiebt hinein ([Push und Pull, Stapel und Echtzeit](integrationsmuster-push-pull-batch-echtzeit.md)).

## Die Entscheidungstabelle

| Ihre Lage | Erste Wahl | Ebenfalls möglich | Vermeiden |
|---|---|---|---|
| Viele Datensätze nach Zeitplan importieren, Code darf auf dem Server laufen | Cronjob-Teil oder CLI-Skript mit `eZContentFunctions` und `remote_id` | Zeitgesteuerter Import mit `xrowextract` | Viele einzelne REST-Anlagevorgänge |
| Ein anderes System legt ab und zu ein paar Elemente an und kann keinen Code auf dem Server ausführen | REST `POST /api/ezp/v2/content/node/create` mit einem API-Schlüssel | Schreibdienste von `expservices` | Direktes Schreiben in Datenbanktabellen |
| Eine App liest Inhalte | REST-Lesezugriffe (`/api/ezp/v1/...` oder `/v2/...`) | Lesezugriffe über `expservices`, RSS- oder Atom-Feeds | HTML-Seiten auslesen (Scraping) |
| Ein anderes System muss sofort wissen, wenn etwas veröffentlicht wird | Ein Workflow-Ereignis auf `content / publish / after` | Audit-Webhook-Senke mit eingeschaltetem `content.object.publish` | Jede Minute die REST-API nach allem abfragen |
| Ein anderes System muss eine Veröffentlichung freigeben oder ablehnen | Ein Workflow-Ereignis auf `content / publish / before`, das angenommen oder abgelehnt zurückgibt oder an den Cronjob abgibt | Das Ereignis *Approve* für eine Freigabe durch Menschen | Nach dem Veröffentlichen prüfen |
| Formularantworten gehen an ein CRM | Ein Cronjob, der neue Sammlungen liest und sie sendet | `bccie`-Exporte, der CSV-Export | Aus `collect.ini` an das Postfach des CRM mailen und E-Mails parsen |
| Mitarbeiter melden sich mit dem Unternehmensverzeichnis an | `LoginHandler[]=LDAP` | Ein eigener Login-Handler | Passwörter in Exponential kopieren |
| Zwei Exponential-Installationen teilen Inhalte | `syndication` | RSS-Export und -Import; Pakete | Datenbankkopien |
| Eine Website oder einen Teilbaum zwischen Installationen verschieben | Pakete (`ezpm.php`) | Inhaltspakete von `xrowextract` | Datenbank-Dumps einzelner Tabellen |
| Ein Tabellen-Hin-und-Her für Redakteure | `xrowextract` | `ezcsvimport.php`, `ezcsvexport.php` | Handgeschriebenes SQL |
| E-Mails über die API eines Anbieters versenden | Eine eigene Mail-Transportklasse (`TransportAlias[]`) | SMTP zum Anbieter | Den Anbieter aus Templates aufrufen |

## Durchgespielte Entscheidungen

### Produktdaten aus einem PIM, 20 000 Produkte, nächtlich

Das PIM exportiert nachts eine Datei. Code darf auf dem Server laufen. Die Entscheidung: ein Cronjob-Teil Ihrer
Erweiterung, der die Datei liest, jedes Produkt mit `eZContentObject::fetchByRemoteID()` findet, es mit
`eZContentFunctions::createAndPublishObject()` anlegt oder nur dann mit `updateAndPublishObject()` aktualisiert, wenn
sich ein Feld geändert hat, und eine Zusammenfassung protokolliert. Veröffentlichen ohne Benachrichtigung
(`$notify = false`) bewahrt Abonnenten vor 20 000 E-Mails. Siehe [Ein Importskript schreiben](ein-importskript-schreiben.md).

### Eine mobile App zeigt Nachrichten und lässt Reporter Entwürfe einstellen

Die App läuft auf Smartphones; auf dem Server läuft nichts. Lesezugriffe nutzen die REST-API; Reporter melden sich über
eine OAuth-Anwendung an, sodass die App als der jeweilige Reporter handelt. Das Anlegen von Inhalten nutzt die
v2-Anlageroute, die mit den eigenen `content/create`-Rechten des Reporters veröffentlicht. Siehe
[OAuth-Anwendungen](oauth-anwendungen-fuer-integrationen.md).

### Suchindex in einem externen Dienst, innerhalb einer Minute aktualisiert

Ein Workflow-Ereignis auf `content / publish / after` stellt die Objekt-ID in eine Warteschlange; ein häufig
laufender Cronjob schickt die eingestellten IDs an den Suchdienst. Das Ereignis bleibt schnell, und ein Ausfall des
Suchdienstes blockiert keine Redakteure. Siehe [Workflows und Trigger als Hooks](workflows-und-trigger-als-hooks.md).

## Schnittstellen kombinieren

Die meisten echten Integrationen kombinieren zwei Schnittstellen: einen **Hook**, der eine Änderung günstig bemerkt,
und einen **Stapel**, der die schwere Arbeit erledigt und wiederholen kann. Typische Paare:

| Hook | Stapel |
|---|---|
| Ein Workflow-Ereignis beim Veröffentlichen schreibt eine ID in Ihre eigene Tabelle | Ein Cronjob-Teil sendet ausstehende IDs und markiert sie als erledigt |
| Ein Event-Listener für `content/cache` notiert geleerte Knoten | Ein Cronjob leert sie beim CDN |
| Ein Audit-Webhook liefert `content.node.remove.*` | Das empfangende System entfernt seine Kopie |

## Warnsignale

- **Direkt in die Datenbanktabellen von Exponential schreiben.** Das umgeht Versionen, den Veröffentlichungsvorgang,
  Workflows, Caches, den Suchindex und das Audit-Protokoll. Verwenden Sie immer die Funktionen des Kernels
  ([Inhalte aus PHP anlegen](../erweiterungsentwicklung/teil-8-referenz/inhaltsfunktionen-aus-php.md)).
- **Importe als Administrator ausführen.** Verwenden Sie ein Dienstkonto mit den Rechten, die der Import braucht
  ([Dienstkonten und Rollen](dienstkonten-rollen-und-richtlinien.md)).
- **Langsame entfernte Aufrufe innerhalb der Veröffentlichungsanfrage.** Redakteure warten, und ein Timeout auf der
  anderen Seite bricht das Veröffentlichen ab. Geben Sie die Arbeit an einen Cronjob ab.
- **Sich installationsübergreifend auf Objekt- oder Knoten-IDs verlassen.** Sie unterscheiden sich zwischen
  Installationen; verwenden Sie `remote_id`.

## Verwandte Seiten

- [Integrationsschnittstellen auf einen Blick](integrationsschnittstellen-auf-einen-blick.md)
- [Push und Pull, Stapel und Echtzeit](integrationsmuster-push-pull-batch-echtzeit.md)
- [Zugangsdaten für eine Integration wählen](zugangsdaten-fuer-eine-integration-waehlen.md)
- [Muster für Hintergrundarbeit](../ersteller/anwendungsentwickler/muster-fuer-hintergrundarbeit.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
