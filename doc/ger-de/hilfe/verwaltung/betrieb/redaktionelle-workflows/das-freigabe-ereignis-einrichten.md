---
title: Das Freigabe-Ereignis einrichten
audience: government, admin
category: public-sector
order: 30
summary: Jedes Feld des Freigabe-Ereignisses (betroffene Sektionen, Sprachen und Versionen, freigebende Benutzer und Gruppen, ausgeschlossene Gruppen) mit der Wahl, die eine öffentliche Stelle für jedes üblicherweise trifft, und der Trigger und der Cronjob, die es zum Laufen bringen.
keywords: Freigabe-Ereignis, Freigabe, Workflow-Ereignis, betroffene Sektionen, betroffene Sprachen, betroffene Versionen, Freigebende, Gruppen, die Content freigeben, ausgeschlossene Benutzergruppen, Trigger, Vor dem Veröffentlichen von Inhalten, workflow.php, Approve event, approval, workflow event, affected sections, affected languages, affected versions, approvers, groups who approve content, excluded user groups, trigger, Before publishing content
type: help
---
# Das Freigabe-Ereignis einrichten

Diese Seite richtet sich an Administratoren, die den Freigabe-Workflow aufbauen. Sie geht die Felder des Ereignisses **Freigeben** so durch, wie
[Workflows, Abschnitt 3](../../../../anleitungen/workflows.md#3-durchgerechnetes-beispiel-freigabe-vor-dem-veröffentlichen) sie beschreibt, und nennt für jedes die
übliche Wahl im öffentlichen Sektor.

## Inhalt
1. [Den Workflow anlegen](#den-workflow-anlegen)
2. [Die Felder](#die-felder)
3. [Den Trigger verbinden](#den-trigger-verbinden)
4. [Den Cronjob prüfen](#den-cronjob-prüfen)
5. [Ausprobieren und festhalten](#ausprobieren-und-festhalten)
6. [Verwandte Seiten](#verwandte-seiten)

## Den Workflow anlegen

1. **Setup > Workflows**. Öffnen Sie eine Workflow-Gruppe (zum Beispiel *Standard*) oder legen Sie eine mit **Neue Workflow Gruppe** an.
2. **Neuer Workflow**; benennen Sie ihn nach seinem Zweck, zum Beispiel „Freigabe vor dem Veröffentlichen - öffentliche Website“.
3. Wählen Sie in der Ereignisliste **Event / Approve** und klicken Sie auf **Ereignis hinzufügen**.

## Die Felder

| Feld | Was es tut | Übliche Wahl |
|---|---|---|
| Betroffene Sektionen | nur Inhalte in diesen Sektionen brauchen eine Freigabe; Inhalte, deren eigene Sektion nicht aufgeführt ist, zählen ebenfalls, wenn das übergeordnete Element einer ihrer Platzierungen in einer aufgeführten Sektion liegt | die Sektionen, die Besucher lesen (Standard und jede eigene öffentliche Sektion); nicht Users oder Media, es sei denn, Mediendateien sind veröffentlichte Inhalte |
| Betroffene Sprachen | nur Versionen, deren Ursprungssprache aufgeführt ist | *Alle Sprachen*, es sei denn, ein Sprachteam hat einen eigenen Workflow ([Mehrsprachige Freigaben](mehrsprachige-freigaben.md)) |
| Betroffene Versionen | *Alle Versionen*, nur *Neues Objekt publizieren* oder nur *Objekt aktualisieren* | *Alle Versionen*: Eine Änderung an einer veröffentlichten Seite ist ebenfalls eine Veröffentlichung |
| Benutzer, die den Inhalt bewilligen | einzelne Freigebende | leer lassen; Gruppen verwenden |
| Gruppen, die Content freigeben | jeder Benutzer in diesen Gruppen ist freigebende Person | eine Gruppe von Freigebenden je Bereich |
| Gruppen ausschließen | Benutzer in diesen Gruppen veröffentlichen ohne Freigabe | die kleinstmögliche Gruppe, dokumentiert ([Notfallumgehung beim Veröffentlichen](notfall-veroeffentlichung-ohne-freigabe.md)) |

Klicken Sie auf **OK**, um den Workflow zu speichern. Das Ereignis lässt Inhalte außerhalb seiner Felder sofort durch, ebenso Inhalte, die von
einer seiner Freigebenden veröffentlicht werden
([Workflows, Abschnitt 3.2](../../../../anleitungen/workflows.md#32-das-ereignis-freigeben-einrichten)).

## Den Trigger verbinden

**Setup > Trigger** listet die Operationen, die einen Workflow starten können. Wählen Sie in der Zeile **Vor dem Veröffentlichen von Inhalten**
(`content/publish`, before) Ihren Workflow und klicken Sie auf **Änderungen anwenden**. *Before* hält das Veröffentlichen zurück, bis der
Workflow fertig ist; *after* würde ihn ausführen, wenn der Inhalt bereits öffentlich ist, was für eine Freigabe nutzlos ist.

Nur ein Workflow je Trigger: Wenn vor dem Veröffentlichen bereits ein anderer Workflow läuft (zum Beispiel ein Benachrichtigungs-Workflow aus
einer Extension), bauen Sie einen Workflow mit einem Ereignis **Multiplexer**, der beide ausführt.

## Den Cronjob prüfen

**Setup > Workflow-Prozesse** zeigt in seiner Statusleiste, ob `workflow.php` eingeplant ist, zum Beispiel „Fortgesetzt vom Workflow-Cronjob
(workflow.php) im Teil Frequent: Alle 5 Minuten“. Steht dort **nicht in der Crontab eingeplant**, installieren Sie die Zeile, die
**Setup > Cronjobs** vorschlägt ([Prüfen, ob Cronjobs laufen](../../../../../eng-us/help/government/tasks/operations/check-that-cronjobs-run.md)).

## Ausprobieren und festhalten

1. Veröffentlichen Sie als Redakteurin oder Redakteur, die oder der nicht freigebend ist, einen Artikel in einer betroffenen Sektion: Er wartet.
2. **Setup > Workflow-Prozesse** zeigt einen Prozess unter **Vor dem Veröffentlichen von Inhalten**.
3. Öffnen Sie als freigebende Person **Mein Konto > Zusammenarbeit**, öffnen Sie die Anfrage, **Freigeben**.
4. Führen Sie den Teil *Frequent* unter **Setup > Cronjobs** aus oder warten Sie: Der Artikel wird veröffentlicht.

Notieren Sie für Ihren Änderungsnachweis den Namen des Workflows, den Trigger, jeden Feldwert und das Datum; die Trigger-Liste und die Seite des
Workflows zeigen sie. Änderungen an Workflows sind Konfigurationsänderungen: Leiten Sie sie durch Ihren Änderungsprozess
([Änderungsmanagement und Änderungsbeiräte](../aktualisierungen-und-aenderungen/aenderungsmanagement-und-aenderungsbeiraete.md)).

## Verwandte Seiten
- [Überblick über Freigabe-Workflows](freigabe-workflows-im-ueberblick.md)
- [Rollenentwurf: Freigebende](../personen-und-berechtigungen/rollenentwurf-freigebende.md)
- [Fehlerbehebung bei Workflows](workflow-fehlerbehebung.md)
- [Einen Freigabe-Workflow für eine Sektion einrichten](../../../../../eng-us/help/government/tasks/people-and-permissions/set-up-an-approval-workflow-for-a-section.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
