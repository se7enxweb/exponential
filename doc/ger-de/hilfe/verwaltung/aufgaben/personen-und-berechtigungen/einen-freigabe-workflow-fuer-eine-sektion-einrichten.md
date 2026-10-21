---
title: Einen Freigabe-Workflow für eine Sektion einrichten
audience: government, admin
category: public-sector
order: 130
summary: Schritt für Schritt - einen Workflow mit einem Ereignis Approve für eine Sektion anlegen, ihn mit Vor dem Veröffentlichen von Inhalten verbinden, sicherstellen, dass der Workflow-Cronjob läuft, und ihn mit jemandem testen, der kein Freigebender ist.
keywords: Freigabe-Workflow, Freigabe einrichten, Ereignis Approve, Sektion, Auslöser, Vor dem Veröffentlichen von Inhalten, Workflow-Cronjob, workflow.php, Setup Workflows, Setup Auslöser, approval workflow, set up approval, Approve event, section, trigger, Before publishing content, workflow cronjob, Setup Triggers
type: help
---
# Einen Freigabe-Workflow für eine Sektion einrichten

Diese Aufgabe ist für Administratoren. Am Ende wartet jede in einer Sektion veröffentlichte Version, bis ein Mitglied
einer Gruppe der Freigebenden sie freigibt. Sie folgt dem durchgearbeiteten Beispiel aus
[Workflows, Abschnitt 3](../../../../anleitungen/workflows.md#3-durchgerechnetes-beispiel-freigabe-vor-dem-veröffentlichen).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Den Workflow anlegen](#den-workflow-anlegen)
3. [Den Auslöser verbinden](#den-auslöser-verbinden)
4. [Sicherstellen, dass der Cronjob läuft](#sicherstellen-dass-der-cronjob-läuft)
5. [Ihn testen](#ihn-testen)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Konto mit den Modulen `workflow` und `trigger` und `setup/managecronjobs` (Administratoren haben sie).
- Die Benutzergruppe der Freigebenden mit der Rolle der Freigebenden ([Einem Freigebenden Rechte geben](einem-freigebenden-rechte-geben.md)).
- Die Sektion existiert und enthält die Inhalte.
- Zum Üben zuerst: `./console exp:collaborationsampledata --dry-run --allow-root-user` zeigt, was die Beispielfreigaben
  anlegen würden; `--remove` nimmt sie wieder weg.

## Den Workflow anlegen

1. **Setup > Workflows** (`/workflow/grouplist`). Öffnen Sie eine Workflow-Gruppe oder legen Sie eine mit
   **Neue Workflow Gruppe** an.
2. **Neuer Workflow**, benennen Sie ihn, zum Beispiel „Approval - <Name der Sektion>“.
3. Wählen Sie in der Liste **Event / Approve** und klicken Sie auf **Ereignis hinzufügen**.
4. Füllen Sie das Ereignis aus:
   - **Betroffene Sektionen**: die Sektion.
   - **Betroffene Sprachen**: *Alle Sprachen*.
   - **Betroffene Versionen**: *Alle Versionen*.
   - **Gruppen, die Content freigeben**: die Gruppe der Freigebenden.
   - **Gruppen ausschließen**: leer lassen, sofern Sie keine dokumentierte Gruppe zum Umgehen haben.
5. Klicken Sie auf **OK**.

## Den Auslöser verbinden

1. **Setup > Auslöser** (`/trigger/list`).
2. Wählen Sie in der Zeile **Vor dem Veröffentlichen von Inhalten** den Workflow und klicken Sie auf
   **Änderungen anwenden**.
3. Wenn dort schon ein anderer Workflow gesetzt ist, ersetzen Sie ihn nicht blind: Verbinden Sie beide mit einem
   Ereignis Multiplexer.

## Sicherstellen, dass der Cronjob läuft

Öffnen Sie **Setup > Workflow Prozesse**. Die Statusleiste muss sagen, dass der Workflow-Cronjob (`workflow.php`)
geplant ist, zum Beispiel im Teil *Frequent* alle 5 Minuten. Wenn sie **nicht in der Crontab eingeplant** sagt,
installieren Sie die Zeile der Crontab, die **Setup > Cronjobs** vorschlägt
([Prüfen, ob die Cronjobs laufen](../betrieb/pruefen-ob-die-cronjobs-laufen.md)).

## Ihn testen

1. Veröffentlichen Sie als Redakteur, der kein Freigebender ist, eine Testseite in der Sektion: Sie geht nicht online;
   **Meine ausstehenden Elemente** zeigt sie wartend.
2. **Setup > Workflow Prozesse** listet einen Prozess unter **Vor dem Veröffentlichen von Inhalten**.
3. Als Freigebender **Mein Konto > Zusammenarbeit**, die Anfrage öffnen, **Freigeben**.
4. Führen Sie jetzt den Teil *Frequent* aus (**Setup > Cronjobs**, **Teil starten**) oder warten Sie: Die Seite wird
   veröffentlicht und der Prozess verschwindet.
5. Entfernen Sie die Testseite.

Änderungsnachweis: Name des Workflows, Einstellungen des Ereignisses, Auslöser, Testergebnis und Datum.

## Verwandte Seiten
- [Das Ereignis Approve einrichten](../../../../../eng-us/help/government/operations/editorial-workflows/setting-up-the-approve-event.md)
- [Überblick über Freigabe-Workflows](../../../../../eng-us/help/government/operations/editorial-workflows/approval-workflows-overview.md)
- [Fehlersuche bei Workflows](../../../../../eng-us/help/government/operations/editorial-workflows/workflow-troubleshooting.md)
- [Anleitung zu den Workflows](../../../../anleitungen/workflows.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
