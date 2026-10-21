---
title: Fehlerbehebung bei Workflows
audience: government, admin, editor
category: public-sector
order: 150
summary: Was zu tun ist, wenn freigegebene Inhalte nicht veröffentlicht werden, eine Freigabe auf eine ausgetretene freigebende Person wartet, „Zur Veröffentlichung schicken“ scheinbar nichts bewirkt oder Prozesse fehlschlagen - mit der Seite Setup > Workflow-Prozesse, den Cronjob-Befehlen und einem sicheren Weg zum Abbrechen.
keywords: Fehlerbehebung bei Workflows, Freigabe hängt, nicht veröffentlicht, ausstehend, Workflow-Prozesse, Prozess abbrechen, Workflow-Cronjob, workflow.php, fehlgeschlagener Prozess, freigebende Person ausgetreten, Zur Veröffentlichung schicken, workflow troubleshooting, approval stuck, not published, pending, workflow processes, cancel process, workflow cronjob, failed process, approver left, Send for publishing
type: help
---
# Fehlerbehebung bei Workflows

Diese Seite richtet sich an Administratoren und Webverantwortliche, wenn der Freigabeprozess nicht weitergeht. Sie verdichtet die Fehlerbehebung
aus [Workflows, Abschnitt 9](../../../../anleitungen/workflows.md#9-fehlerbehebung) auf die Fälle, denen öffentliche Stellen am häufigsten begegnen,
aus der Sicht des öffentlichen Sektors: Informieren Sie die Autorin oder den Autor und die anfragende Stelle, was geschehen ist, und bewahren Sie
den Nachweis auf.

## Inhalt
1. [Erster Blick](#erster-blick)
2. [Freigegeben, aber nicht veröffentlicht](#freigegeben-aber-nicht-veröffentlicht)
3. [Wartet auf eine freigebende Person, die nicht mehr da ist](#wartet-auf-eine-freigebende-person-die-nicht-mehr-da-ist)
4. [Zur Veröffentlichung schicken hat nichts bewirkt](#zur-veröffentlichung-schicken-hat-nichts-bewirkt)
5. [Fehlgeschlagene und festhängende Prozesse](#fehlgeschlagene-und-festhängende-prozesse)
6. [Sicher abbrechen](#sicher-abbrechen)
7. [Verwandte Seiten](#verwandte-seiten)

## Erster Blick

Öffnen Sie **Setup > Workflow-Prozesse** (`/workflow/processlist`) und tippen Sie den Namen des Inhalts in **Prozess suchen** ein. Jede Karte sagt in
einer grauen Zeile, worauf der Prozess wartet, bei Freigaben mit **Freigabeanfrage öffnen**. Die Statusleiste sagt, ob der Workflow-Cronjob
eingeplant ist. Die Übersicht zählt wartende, fehlgeschlagene und angehaltene Prozesse und zeigt den ältesten wartenden. Ist die Liste leer,
sehen Sie unter **Angehalten** und **Alle** nach.

## Freigegeben, aber nicht veröffentlicht

Ursache: Der Workflow-Cronjob (`workflow.php`, Teil frequent) ist seit der Entscheidung nicht gelaufen.

1. Die Statusleiste zeigt **nicht in der Crontab eingeplant**: Installieren Sie die Zeile aus **Setup > Cronjobs**
   ([Prüfen, ob Cronjobs laufen](../../aufgaben/betrieb/pruefen-ob-die-cronjobs-laufen.md)).
2. Führen Sie ihn jetzt aus: **Setup > Cronjobs**, die Karte *Frequent*, **Teil starten**; oder auf der Kommandozeile:

   ```bash
   php runcronjobs.php -s <siteaccess> --script=workflow.php
   ```

3. Laden Sie die Prozessliste neu: Der Prozess ist verschwunden, und der Inhalt ist veröffentlicht.

## Wartet auf eine freigebende Person, die nicht mehr da ist

Eine bereits gestellte Anfrage behält ihre Beteiligten. Wenn niemand darin mehr entscheiden kann:

1. Korrigieren Sie zuerst die Freigebenden: Nehmen Sie Personen in die Gruppe der Freigebenden auf oder nennen Sie im Freigabe-Ereignis eine Gruppe.
2. Brechen Sie die wartenden Prozesse ab (siehe unten); ihre Versionen werden wieder zu Entwürfen.
3. Bitten Sie die Autorinnen und Autoren, sie erneut zur Veröffentlichung zu schicken; die neuen Anfragen gehen an die aktuellen Freigebenden.

## Zur Veröffentlichung schicken hat nichts bewirkt

Ein fehlgeschlagener oder abgebrochener Prozess mit demselben Schlüssel (dasselbe Objekt, dieselbe Version, derselbe Workflow und derselbe Benutzer)
wird beim ersten Versuch entfernt, was die Operation einmal anhält; der zweite Versuch funktioniert. Brechen Sie fehlgeschlagene Prozesse vorher in
der Liste ab, um das zu vermeiden ([Workflows, Abschnitt 4.4](../../../../anleitungen/workflows.md#44-dieselbe-version-erneut-veröffentlichen)).

## Fehlgeschlagene und festhängende Prozesse

| Die Karte zeigt | Tun Sie |
|---|---|
| Fehlgeschlagen | lesen Sie in den technischen Details die Antwort des Ereignisses; brechen Sie den Prozess ab |
| Als laufend markiert, seit Stunden | der Lauf wurde abgebrochen; brechen Sie ab; suchen Sie in `var/log/error.log` nach dem abgestürzten Lauf |
| Auslöser nicht vermerkt | kann nicht fortgesetzt werden; brechen Sie ab und veröffentlichen Sie den Inhalt dann erneut |
| Schritt „Keiner“ | der Workflow wurde gelöscht oder nie erreicht; brechen Sie ab |
| Wartet auf seinen übergeordneten Workflow | ein untergeordneter Prozess eines Multiplexers; brechen Sie die untergeordneten Prozesse zusammen mit dem übergeordneten ab |

## Sicher abbrechen

**Abbrechen...** auf einer Karte, oder markieren Sie mehrere und wählen Sie **Ausgewählte Prozesse abbrechen...**; eine Bestätigung sagt, was
geschehen wird. Nach der Bestätigung wird eine ausstehende Version wieder zum Entwurf, die Freigabeanfrage verschwindet aus dem Posteingang, und der
Prozess wird entfernt. Nichts wird veröffentlicht, und kein Inhalt wird gelöscht; der Weg zurück ist, den Entwurf erneut zu veröffentlichen. Das
Abbrechen macht nicht rückgängig, was frühere Ereignisse getan haben (eine bereits gesendete E-Mail) ([Workflows, Abschnitt 7](../../../../anleitungen/workflows.md#7-prozesse-sicher-abbrechen-und-aufräumen)).

Es gibt keinen Konsolenbefehl, um Prozesse aufzulisten oder abzubrechen; die Seite ist das Werkzeug. Für einen nur lesenden Blick auf die Tabelle
gibt der Leitfaden eine SQL-Abfrage auf `ezworkflow_process` an.

## Verwandte Seiten
- [Überblick über Freigabe-Workflows](freigabe-workflows-im-ueberblick.md)
- [Überwachung der Cronjobs](../ueberwachung/ueberwachung-der-cronjobs.md)
- [Posteingang der Zusammenarbeit](posteingang-der-zusammenarbeit.md)
- [Leitfaden zu Workflows](../../../../anleitungen/workflows.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
