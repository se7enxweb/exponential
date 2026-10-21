---
title: Workflows für Builder
audience: developer, designer, app-creator, admin
category: extension-development
order: 190
summary: Etwas rund um das Veröffentlichen geschehen lassen - Workflows, Ereignisse, Trigger, Prozesse und der Workflow-Cronjob, für Builder erklärt, mit einem Freigabe-Workflow und den Ereignistypen, die keinen Code brauchen.
keywords: Workflow, Ereignis, Trigger, Freigabe, freigeben, Multiplexer, Bis Datum warten, Workflow-Prozess, Workflow-Cronjob, workflow, event, trigger, approve, approval, wait until date, workflow process, workflow cronjob, content/publish, pre_publish, post_publish
type: help
---
# Workflows für Builder

Teil 2, Kapitel 19. Ein **Workflow** führt Schritte rund um eine Operation des Systems aus, meistens um das
Veröffentlichen: „jeden Artikel vor der Veröffentlichung einem Redakteur zur Freigabe schicken“, „dies an einem Datum
veröffentlichen“. Builder richten Workflows in der Administration ein; Entwickler fügen neue Arten von Schritten hinzu
([Workflow-Ereignistypen](../teil-3-erweiterungsentwickler/workflow-ereignistypen.md)). Die vollständige Anleitung ist
[Workflows](../../../anleitungen/workflows.md); dieses Kapitel ist die Zusammenfassung für Builder.

## Die Bausteine

| Baustein | Was er ist | Wo |
|---|---|---|
| Workflow | eine geordnete Liste von Ereignissen | Setup > Workflows (`/workflow/grouplist`) |
| Ereignis | ein Schritt eines Ereignistyps | die Bearbeitungsseite des Workflows |
| Trigger | verbindet einen Workflow mit einer Operation, davor oder danach (`pre_publish`, `post_publish`) | Setup > Trigger (`/trigger/list`) |
| Prozess | ein noch nicht beendeter Lauf eines Workflows für ein Objekt und eine Version | Setup > Workflow-Prozesse (`/workflow/processlist`) |
| Workflow-Cronjob | setzt wartende Prozesse fort | `cronjobs/workflow.php` |

## Ereignistypen, die keinen Code brauchen

Der Kernel bringt diese mit (`workflow.ini [EventSettings] AvailableEventTypes[]`):

| Typ | Tut |
|---|---|
| Freigeben | hält das Veröffentlichen an, bis ein Freigebender freigibt |
| Bis Datum warten | hält das Veröffentlichen bis zu einem Datum in einem Attribut an |
| Multiplexer | führt einen anderen Workflow nur für bestimmte Sektionen, Klassen oder Benutzer aus |
| Zahlungsgateway | hält eine Bestellung bis zur Zahlung an (Shop) |
| Einfacher Versand | fügt einer Bestellung Versandkosten hinzu (Shop) |
| Finish user register | schließt eine Benutzerregistrierung ab |

Erweiterungen fügen weitere hinzu; `swark` zum Beispiel bringt Ereignistypen in `eventtypes/event/` mit.

## Ausgearbeiteter Plan: Freigabe vor dem Veröffentlichen

1. Setup > Workflows: Legen Sie einen Workflow „Approve articles“ mit einem Ereignis **Freigeben** an; wählen Sie die
   Freigebenden (eine Benutzergruppe) und die Sektionen, Sprachen oder Klassen, für die er gilt.
2. Setup > Trigger: Verbinden Sie den Workflow mit `content`, `publish`, **Vorher**.
3. Stellen Sie sicher, dass der Workflow-Cronjob läuft (`cronjob.ini`, Teil `frequent`):

   ```bash
   php runcronjobs.php -s <siteaccess> --script=workflow.php
   ```

4. Probieren Sie es aus: Ein Redakteur veröffentlicht einen Artikel; er wartet als schwebend; der Freigebende gibt ihn
   in seinem Posteingang unter Zusammenarbeit frei; der nächste Lauf des Workflow-Cronjobs veröffentlicht ihn.

Die Anleitung geht jeden Bildschirm durch: [Workflows](../../../anleitungen/workflows.md).

## Mit einem Multiplexer gezielt auswählen

Ein Trigger führt einen Workflow für **jedes** Veröffentlichen aus. Um nur Artikel in einer Sektion freigeben zu lassen,
machen Sie den ausgelösten Workflow zu einem **Multiplexer**, der „Approve articles“ nur für die Klasse Article und die
von Ihnen gewählte Sektion startet.

## Wenn ein Builder einen Entwickler braucht

| Sie brauchen | Verwenden Sie |
|---|---|
| Einen Schritt, den die Liste nicht hat (ein externes System benachrichtigen, ein Feld setzen, Inhalte verschieben) | einen eigenen Workflow-Ereignistyp ([Mit RAD: ein Workflow-Ereignis](../teil-3-erweiterungsentwickler/tutorials/rad-workflow-ereignis.md)) |
| Etwas beim Veröffentlichen, das nie wartet und nie nachfragt | oft einfacher als Event-Listener ([Ereignisse, Listener und Filter](../teil-3-erweiterungsentwickler/ereignisse-und-filter.md)) |

## Fehlersuche

| Symptom | Prüfung |
|---|---|
| Artikel bleiben ewig schwebend | läuft der Workflow-Cronjob? Setup > Workflow-Prozesse listet wartende Prozesse auf |
| Der Freigebende bekommt nichts | die im Ereignis gewählten Freigebenden; ihr Posteingang unter Zusammenarbeit |
| Der Workflow läuft für alles | der Trigger hat keinen Filter; verwenden Sie einen Multiplexer |

## Verwandte Seiten

- Zurück: [Sektionen, Rollen und Zugriff für Builder](sektionen-rollen-und-zugriff-fuer-builder.md)
- Weiter: [Cronjobs für Builder](cronjobs-fuer-builder.md)
- [Workflows](../../../anleitungen/workflows.md)
- [Trigger und Workflows für Entwickler](../teil-3-erweiterungsentwickler/trigger-und-workflows-fuer-entwickler.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
