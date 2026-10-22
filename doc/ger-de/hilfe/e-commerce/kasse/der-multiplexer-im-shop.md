---
title: Der Multiplexer im Shop
audience: ecommerce, website-creator, app-creator
category: e-commerce
order: 46
summary: Das Workflow-Ereignis Multiplexer an Shop-Triggern in Exponential verwenden - was es abgleicht (Sektionen, Klassen, ausgeschlossene Benutzergruppen, Sprachen), was es an jedem Shop-Trigger sieht, praktische Anwendungen wie das Überspringen der Zahlung für Mitarbeitende und seine Grenzen.
keywords: Multiplexer, ezmultiplexer, eZMultiplexerType, bedingter Workflow, Unter-Workflow, Benutzergruppen, Sektionen, Klassen, Shop-Trigger, Bestellungen von Mitarbeitenden, addtobasket, checkout, multiplexer, conditional workflow, sub-workflow, user groups, sections, classes, shop triggers, staff orders
type: help
---
# Der Multiplexer im Shop

Diese Seite ist für Entwickler und Administratoren, die einen Shop-Workflow nur in bestimmten Fällen ausführen möchten.
Das Ereignis **Multiplexer** (`ezmultiplexer`, `kernel/classes/workflowtypes/event/ezmultiplexer/ezmultiplexertype.php`)
führt einen anderen Workflow aus, wenn seine Bedingungen zutreffen. Es wurde für das Veröffentlichen entworfen; an
Shop-Triggern sieht es daher weniger, als Sie vielleicht erwarten. Diese Seite sagt genau, was funktioniert.

## Inhalt

1. [Was der Multiplexer prüft](#was-der-multiplexer-prüft)
2. [Was er an Shop-Triggern sieht](#was-er-an-shop-triggern-sieht)
3. [Anwendungen, die funktionieren](#anwendungen-die-funktionieren)
4. [Wie der Unter-Workflow läuft](#wie-der-unter-workflow-läuft)
5. [Grenzen](#grenzen)

## Was der Multiplexer prüft

| Bedingung (im Ereignis gespeichert) | Trifft zu, wenn |
|---|---|
| Sektionen | Die Sektion des Inhaltsobjekts in der Liste steht oder „Beliebig“ gewählt ist |
| Klassen | Die Klasse des Objekts in der Liste steht oder „Beliebig“ gewählt ist |
| Benutzergruppen | Der Benutzer in **keiner** der aufgeführten Gruppen ist: Diese Liste schließt aus |
| Sprachen | Die Ausgangssprache der Version in der Auswahl ist (standardmäßig alle) |
| Versionsoption | Nur die erste Version, alle außer der ersten oder alle |
| Auszuführender Workflow | Der Workflow, der gestartet wird, wenn alles zutrifft |

Treffen alle Bedingungen zu, läuft der gewählte Workflow als Kindprozess. Andernfalls nimmt der Multiplexer an, und der
übergeordnete Workflow läuft weiter.

## Was er an Shop-Triggern sieht

Der Multiplexer sucht in den Prozessparametern nach einer `object_id` (oder `node_id`), um ein Inhaltsobjekt zu
finden:

| Trigger | Parameter | Objekt gefunden? | Was abgeglichen werden kann |
|---|---|---|---|
| `addtobasket` | `object_id`, `option_list`, `quantity`, `basket_id` | Ja, das Produkt | Sektion, Klasse, ausgeschlossene Benutzergruppen |
| `updatebasket` | `item_count_list`, `item_id_list` | Nein | Nur ausgeschlossene Benutzergruppen, mit Sektionen und Klassen auf „Beliebig“ |
| `confirmorder` | `order_id` | Nein | Ebenso |
| `checkout` | `order_id` | Nein | Ebenso |

Ohne Objekt trifft eine Bedingung für Sektion oder Klasse, die nicht „Beliebig“ ist, nie zu, und der Unter-Workflow
läuft nie. Setzen Sie an diesen Triggern Sektionen und Klassen auf „Beliebig“ und verwenden Sie die Liste der
Benutzergruppen.

## Anwendungen, die funktionieren

| Ziel | Trigger | Einstellungen des Multiplexers | Unter-Workflow |
|---|---|---|---|
| Aufgegebene Bestellungen in ein anderes System exportieren, außer Testbestellungen von Mitarbeitenden | `checkout` nachher | Sektionen und Klassen „Beliebig“; Gruppe „Shop staff“ ausschließen | Ein eigenes Export-Ereignis |
| Eine Prüfung nur, wenn Produkte einer Klasse hinzugefügt werden | `addtobasket` vorher | Klasse „Gift card“, Sektionen „Beliebig“ | Ein eigenes Ereignis, das den Posten prüft |
| Eine Prüfung nur für Produkte in einer Sektion | `addtobasket` vorher | Sektion „Restricted“, Klassen „Beliebig“ | Ebenso |

> **Hinweis**
> Ein Zahlungs-Gateway im Unter-Workflow eines Multiplexers ist eine andere Sache: Das Zahlungsobjekt des Gateways
> gehört zum Kindprozess, und der Rückruf des Anbieters setzt diesen Prozess fort. Wir haben diese Kombination im
> Repository weder dokumentiert noch getestet gefunden. Lassen Sie das Zahlungsereignis direkt im Kassen-Workflow, und
> wenn Sie um die Zahlung herum verzweigen müssen, testen Sie den vollständigen Weg einschließlich des Rückrufs im
> Testmodus des Anbieters.

## Wie der Unter-Workflow läuft

Der Kindprozess erhält die Parameter des übergeordneten Prozesses und dazu seine eigene Workflow-ID. Sein Ergebnis wird
nach oben weitergereicht: Eine Weiterleitung (ein Zahlungsanbieter) oder ein Template (Gateway-Auswahl) wird an den
Kunden weitergegeben, als hätte der übergeordnete Prozess sie erzeugt; eine Zurückstellung an Cron lässt den
übergeordneten Prozess warten; „erledigt“ bedeutet angenommen, „abgebrochen“ und „fehlgeschlagen“ bedeuten abgebrochen
und abgelehnt.

## Grenzen

- Ein Workflow pro Multiplexer-Ereignis. Für mehrere Fälle legen Sie mehrere Multiplexer-Ereignisse in den
  übergeordneten Workflow.
- Die Liste der Benutzergruppen schließt aus; es gibt keine Liste „nur diese Gruppen“.
- Keine Bedingung für Bestellsumme, Land oder Zahlungsart. Dafür schreiben Sie ein eigenes Ereignis
  ([Einen Workflow-Ereignistyp schreiben](../../ersteller/anwendungsentwickler/einen-workflow-ereignistyp-schreiben.md)).

## Verwandte Seiten

- [Kasse und Workflows](README.md)
- [Workflows an Shop-Triggern](workflows-an-shop-triggern.md)
- [Workflows](../../../anleitungen/workflows.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
