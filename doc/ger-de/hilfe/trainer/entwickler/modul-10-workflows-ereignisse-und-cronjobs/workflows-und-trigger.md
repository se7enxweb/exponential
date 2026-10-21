---
title: "Lektion 10.1: Workflows und Trigger für Entwickler"
audience: trainer, developer
category: training
order: 101
summary: Die Teilnehmenden verbinden mit einem Trigger einen Workflow mit der Operation Veröffentlichen, verfolgen einen Workflow-Prozess durch seine Zustände und lesen, wie der Kernel eine Operation zurückhält und der Workflow-Cronjob sie fortsetzt. 45 Minuten.
keywords: entwickler, workflow, ereignis, trigger, content/publish before, workflow-prozess, memento, workflow-cronjob, workflow.php, frequent-teil, Approve, Multiplexer, Setup Workflows, Setup Trigger, Workflow-Prozesse, developers course, event, workflow process, workflow cronjob, frequent part, Setup Triggers, Workflow processes
type: help
---
# Lektion 10.1: Workflows und Trigger für Entwickler

Mit Workflows läuft Code rund um Kernel-Operationen: bevor Inhalte veröffentlicht werden, nachdem sich ein Benutzer
registriert hat, bevor eine Bestellung bestätigt wird. Bevor die Teilnehmenden einen eigenen Ereignistyp schreiben
(Lektion 10.2), brauchen sie das Modell: Workflows, Ereignisse, Trigger und die Prozesse, die warten. Diese Lektion
baut in der Administration einen kleinen Workflow und verfolgt ihn durch den Kernel.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 10: Workflows, Ereignisse und Cronjobs](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Modul 9](../modul-09-die-konsole-und-persistente-objekte/README.md); ein Administratorkonto auf der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Workflow, Ereignis, Trigger, Workflow-Prozess und Memento erklären;
- einen Workflow anlegen und ihn mit **Setup** > **Trigger** mit `content/publish` before verbinden;
- den Unterschied zwischen einem *Before*- und einem *After*-Trigger erklären;
- beschreiben, wie der Workflow-Cronjob (`workflow.php` im Teil *frequent*) einen wartenden Prozess fortsetzt;
- wartende Prozesse unter **Setup** > **Workflow-Prozesse** finden.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Workflows: Freigaben, Trigger und die Prozesse, die warten](../../../../anleitungen/workflows.md).

1. **Das Modell** (5 Minuten). Zeigen Sie die Tabelle aus Abschnitt 1 des Leitfadens: Ein Workflow ist eine Liste von
   Ereignissen; ein Trigger verbindet einen Workflow mit einer Operation (`content/publish`, davor oder danach); ein
   Prozess ist ein Durchlauf für ein Objekt und eine Version; das Memento ist das, was der Kernel von der
   zurückgehaltenen Operation gespeichert hat.
2. **Der Ablauf** (5 Minuten). **Zur Veröffentlichung schicken** startet `content/publish`; der Before-Trigger startet
   einen Prozess; ein Ereignis, das nicht fertig werden kann, antwortet „führe mich später noch einmal aus“, und die
   Version wartet als ausstehend; der Workflow-Cronjob führt wartende Prozesse erneut aus und schließt die Operation ab.
3. **Davor oder danach** (2 Minuten). *Before* hält die Operation zurück, bis der Workflow fertig ist; *After* läuft,
   sobald die Operation geschehen ist, für Benachrichtigungen und Folgearbeiten.
4. **Ein Workflow pro Trigger** (2 Minuten). Mehrere Schritte gehören in einen Workflow; das Ereignis *Multiplexer*
   führt je nach Sektion oder Klasse andere Workflows aus.
5. **Der Cronjob** (3 Minuten). Ohne laufendes `workflow.php` werden freigegebene Inhalte nie veröffentlicht. Die
   Prozessliste zeigt, ob er geplant ist.

### Häufige Fragen

**Welche Operationen haben Trigger?** **Setup** > **Trigger** listet jede Operation, die einen Workflow starten kann,
in Worten, mit dem Modul und der Art der Verbindung.

**Kann ein Workflow nur für eine Klasse laufen?** Das Ereignis *Multiplexer* wählt einen Workflow nach Sektion, Klasse
oder Benutzer; ein eigenes Ereignis kann die Klasse selbst prüfen (Lektion 10.2).

**Wo sieht die Redaktion die wartende Version?** Als ausstehend, unter **Meine Warteliste**.

### Stolperfallen

- **Ein Before-Publish-Workflow auf einer gemeinsamen Schulungsinstallation.** Das Veröffentlichen aller
  Teilnehmenden läuft dann durch ihn. Verwenden Sie getrennte Installationen oder entfernen Sie den Trigger am Ende.
- **Kein Workflow-Cronjob.** Prozesse warten für immer; prüfen Sie die Statusleiste der Prozessliste.
- **Prozesse abbrechen.** **Abbrechen...** macht aus einer ausstehenden Version wieder einen Entwurf; es löscht keine
  Inhalte, aber sagen Sie es der Redakteurin oder dem Redakteur.

## Praktische Übung

Auf der Schulungsinstallation, als Administrator an der Administration angemeldet.

1. Öffnen Sie **Setup** > **Workflows** und öffnen Sie eine Workflow-Gruppe (oder legen Sie eine mit **Neue Workflow
   Gruppe** an).
2. Klicken Sie auf **Neuer Workflow** und nennen Sie ihn `Course: wait until date`.
3. Wählen Sie in der Ereignisliste **Event / Wait until date** und klicken Sie auf **Ereignis hinzufügen**; klicken Sie
   auf **OK**, um den Workflow ohne weitere Einstellungen zu speichern.
4. Öffnen Sie **Setup** > **Trigger**. Suchen Sie die Zeile für das Veröffentlichen von Inhalten, davor
   (`content/publish`), und notieren Sie, welchen Workflow sie jetzt ausführt. Wählen Sie Ihren Workflow und wenden
   Sie die Änderung an.
5. Veröffentlichen Sie einen Artikel in Ihrem Kursordner. Notieren Sie, ob er sofort veröffentlicht wird.
6. Öffnen Sie **Setup** > **Workflow-Prozesse**, suchen Sie nach einem Prozess und lesen Sie die Statusleiste zum
   Workflow-Cronjob.
7. Führen Sie den Workflow-Cronjob einmal aus der Shell aus: `php runcronjobs.php -s <sa> --script=workflow.php`.
8. Setzen Sie den Trigger auf den Workflow zurück, den er vor Schritt 4 ausgeführt hat (oder auf keinen), und wenden
   Sie die Änderung an.
9. Lesen Sie den Code, der einen Prozessschritt beendet: `grep -n "STATUS_" kernel/classes/ezworkflowtype.php | head -12`.

## Erwartetes Ergebnis

- Schritt 5: Der Artikel wird sofort veröffentlicht, weil das Ereignis *Wait until date* ohne gesetztes
  Datumsattribut ihn durchlässt; mit konfigurierten Daten würde er warten.
- Schritt 6: Eine leere Prozessliste ist normal, wenn nichts wartet; die Statusleiste nennt den Teil frequent.
- Schritt 7 führt `workflow.php` aus und endet ohne Fehler.
- Schritt 9 listet die Statuskonstanten wie `STATUS_ACCEPTED`, `STATUS_REJECTED` und `STATUS_DEFERRED_TO_CRON`.
- Der Trainer prüft, dass alle Teilnehmenden den Trigger zurückgesetzt haben.

## Quiz

1. Was verbindet einen Workflow mit einer Operation?
2. Ein Workflow muss entscheiden, bevor Inhalte sichtbar sind. Welche Art der Verbindung?
   A. after B. before C. beide D. keine, einen Cronjob verwenden
3. Was ist ein Memento?
4. Was geschieht mit freigegebenen Inhalten, wenn der Workflow-Cronjob nicht läuft?
5. Wo sehen Sie Prozesse, die warten?

<details>
<summary>Antworten</summary>

1. Ein Trigger.
2. **B.** Before: Die Operation wird zurückgehalten, bis der Workflow fertig ist.
3. Das, was der Kernel von der zurückgehaltenen Operation gespeichert hat (Modul, Operation, Trigger und Parameter),
   damit er sie später ausführen kann.
4. Sie werden nie veröffentlicht; die Prozesse warten.
5. **Setup** > **Workflow-Prozesse** (`/workflow/processlist`).

</details>

## Weiterführende Literatur

- [Workflows: Freigaben, Trigger und die Prozesse, die warten](../../../../anleitungen/workflows.md).
- [Cronjobs: ausführen, planen und verfolgen](../../../../anleitungen/cronjobs.md).
- [Meine Entwürfe und meine ausstehenden Elemente](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md).

## Verwandte Seiten

- [Modul 10: Workflows, Ereignisse und Cronjobs](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
