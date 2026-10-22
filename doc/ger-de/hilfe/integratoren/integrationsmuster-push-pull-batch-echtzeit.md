---
title: "Integrationsmuster: Push und Pull, Batch und Echtzeit"
audience: integrator, developer
category: integration
order: 270
summary: Die grundlegenden Integrationsmuster, angewandt auf Exponential - wer den Austausch startet (Push oder Pull), wie oft (Batch oder nahezu Echtzeit), Delta- und Volläufe, Outbox und Inbox, Polling mit einem Wasserzeichen und welche Exponential-Schnittstelle jedes Muster umsetzt.
keywords: Integrationsmuster, Push, Pull, Batch, Echtzeit, nahezu Echtzeit, Delta, Vollabgleich, Wasserzeichen, Outbox, Inbox, Polling, Webhook, letztendliche Konsistenz, Abgleich, integration patterns, real time, near real time, full sync, watermark, eventual consistency, reconciliation
type: help
---
# Integrationsmuster: Push und Pull, Batch und Echtzeit

Diese Seite ist für Architekten und Entwickler, die Datenflüsse zwischen Exponential und anderen Systemen entwerfen.
Sie benennt die grundlegenden Muster, sagt, wann jedes passt, und ordnet jedem die Schnittstelle zu, die es in
Exponential umsetzt. Die Beispiele gehen von einem Produkt-Feed aus einem ERP und einem Lead-Fluss in ein CRM aus, den
zwei häufigsten Aufgaben.

## Inhalt

1. [Zwei Fragen pro Fluss](#zwei-fragen-pro-fluss)
2. [Die vier Kombinationen](#die-vier-kombinationen)
3. [Deltaläufe und Wasserzeichen](#deltaläufe-und-wasserzeichen)
4. [Outbox und Inbox](#outbox-und-inbox)
5. [Abgleich](#abgleich)
6. [Wahl pro Fluss](#wahl-pro-fluss)

## Zwei Fragen pro Fluss

1. **Wer startet den Austausch?** Bei einem **Push** sendet das System, das die Änderung hat, sie ab. Bei einem
   **Pull** fragt das System, das die Änderung braucht, danach.
2. **Wann?** Bei einem **Batch** werden Änderungen gesammelt und nach Zeitplan gemeinsam übertragen. In **nahezu
   Echtzeit** wird jede Änderung innerhalb von Sekunden oder Minuten nach ihrem Entstehen übertragen.

## Die vier Kombinationen

| | Nach Exponential | Aus Exponential heraus |
|---|---|---|
| **Push, nahezu Echtzeit** | Das andere System ruft für jede Änderung die REST-Route zum Anlegen oder Schreibzugriffe von `expservices` auf | Ein Workflow-Ereignis auf `content / publish / after` zeichnet die Änderung auf; ein häufig laufender Cronjob sendet sie (Outbox). Die Webhook-Senke des Audit-Trails für Ereignis-Feeds |
| **Push, Batch** | Das andere System legt eine Datei ab (SFTP, ein Ordner); ein Cronjob-Teil importiert sie; oder `xrowextract` plant einen Import | Ein Cronjob-Teil oder ein Zeitplan von `xrowextract` schreibt eine Datei und liefert sie an SFTP, S3, WebDAV oder einen HTTP-Endpunkt |
| **Pull, nahezu Echtzeit** | Ein häufig laufender Cronjob-Teil fragt die API des anderen Systems nach Änderungen seit dem letzten Wasserzeichen | Das andere System fragt die REST-API oder `expservices` regelmäßig ab (kürzlich geänderte Objekte) oder einen RSS-Export |
| **Pull, Batch** | Ein nächtlicher Cronjob-Teil lädt den vollständigen Export des anderen Systems herunter | Das andere System lädt einen nächtlichen Export oder einen Feed herunter |

Ein Push aus Exponential braucht Code auf der Seite von Exponential; ein Pull durch das andere System braucht nur
Zugangsdaten. Ein Pull nach Exponential braucht die API des anderen Systems und einen Cronjob-Teil.

## Deltaläufe und Wasserzeichen

Ein **Volllauf** überträgt jedes Mal alles: einfach, langsam, und er findet Löschungen durch ihr Fehlen. Ein
**Deltalauf** überträgt nur, was sich seit dem letzten erfolgreichen Lauf geändert hat, erkannt an einem
**Wasserzeichen**:

| Wasserzeichen | Woher es in Exponential kommt |
|---|---|
| Änderungszeit | `modified` von `eZContentObject`; der Dienst `expobject::recentlyModified` von `expservices`; `xrowextract --changed-since` |
| Eine aufsteigende ID | `ezinfocollection.id` für Formularantworten; die ID Ihrer Outbox-Tabelle |
| Das Änderungstoken des anderen Systems | von Ihrer Integration nach jedem Lauf gespeichert |

Regeln: Speichern Sie das Wasserzeichen erst, nachdem der Lauf erfolgreich war; lassen Sie das Zeitfenster etwas
überlappen (zum Beispiel fünf Minuten), und verlassen Sie sich auf Idempotenz, um Duplikate aufzufangen; führen Sie
ab und zu einen vollständigen Abgleich durch, um zu finden, was Deltas übersehen, etwa Löschungen.

## Outbox und Inbox

| Muster | Was es ist | In Exponential |
|---|---|---|
| **Outbox** | Die Änderung wird in einer Tabelle innerhalb desselben Systems und derselben Transaktion aufgezeichnet; ein getrennter Prozess sendet sie und markiert sie als gesendet | Ein Workflow-Ereignis oder ein Event-Listener schreibt eine Zeile in die Tabelle Ihrer Erweiterung; ein Cronjob-Teil oder ein Worker von `expqueue` sendet sie |
| **Inbox** | Eingehende Nachrichten werden zuerst gespeichert, dann verarbeitet; Duplikate werden an der Nachrichten-ID erkannt | Eine Modulansicht oder eine Serverfunktion speichert die Nutzdaten in Ihrer Tabelle und antwortet sofort; ein Cronjob-Teil verarbeitet sie |

Beide entkoppeln die Anfrage des Redakteurs von der Verfügbarkeit des anderen Systems, und beide machen Wiederholungen
sicher.

## Abgleich

Auch gut gebaute Synchronisationen driften auseinander: eine Nachricht, die verloren ging, während ein System nicht
erreichbar war, ein Datensatz, der von Hand geändert wurde. Planen Sie einen **Abgleich**-Job, der beide Seiten nach
Schlüssel (der `remote_id`) vergleicht und Unterschiede meldet oder behebt:

- [ ] Anzahl der Datensätze pro Klasse auf jeder Seite.
- [ ] Datensätze, die nur auf einer Seite vorhanden sind (außerhalb der Synchronisation angelegt oder entfernt).
- [ ] Eine Prüfsumme oder Änderungszeit pro Datensatz, um geänderte zu finden.
- [ ] Ein Bericht an die Betreiber und eine Entscheidungsregel, wer gewinnt.

## Wahl pro Fluss

| Fluss | Typische Wahl |
|---|---|
| ERP-Produkte in Produktseiten | Push-Batch (Datei) oder Pull-Batch (API) nachts, tagsüber mit einem Delta |
| Preis und Bestand | Pull in nahezu Echtzeit oder eine Serverfunktion, die einen von Ihrem Import gepflegten Cache liest |
| Leads aus Formularen ins CRM | Pull in nahezu Echtzeit durch einen Cronjob auf der Seite von Exponential, der an die CRM-API pusht |
| Veröffentlichungen an ein Such- oder Archivsystem | Outbox, nahezu Echtzeit |
| Redaktionelle Inhalte an eine mobile App | Pull durch die App über REST, auf der Seite der App zwischengespeichert |
| Benutzer aus einem Verzeichnis | Login-Handler (bei Bedarf) plus ein nächtlicher Job zum Entziehen von Zugängen |

## Verwandte Seiten

- [Eine Integrationsschnittstelle wählen](eine-integrationsschnittstelle-waehlen.md)
- [Idempotente Importe mit remote_id als Schlüssel](idempotente-importe-ueber-die-remote-id.md)
- [Workflows und Trigger als Integrations-Hooks](workflows-und-trigger-als-hooks.md)
- [Muster für Hintergrundarbeit](../ersteller/anwendungsentwickler/muster-fuer-hintergrundarbeit.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
