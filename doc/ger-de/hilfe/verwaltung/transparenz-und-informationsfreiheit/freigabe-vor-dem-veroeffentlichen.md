---
title: Freigabe vor dem Veröffentlichen
audience: government, admin, editor
category: public-sector
order: 100
summary: Wie Sie in Exponential jede Transparenzveröffentlichung auf die Freigabe durch eine zweite Person warten lassen - das Workflow-Ereignis Freigeben, der Trigger, der Cronjob und wie Freigebende und Autoren ausstehende Einträge sehen.
keywords: Freigabe, freigeben, Workflow, Trigger, ausstehend, Vier-Augen-Prinzip, zweite Prüfung, Zusammenarbeit, Workflow-Cronjob, Vor dem Veröffentlichen von Inhalten, Abzeichnung, approval, approve, pending, four eyes, second check, collaboration, workflow cronjob, Before publishing content, sign-off
type: help
---
# Freigabe vor dem Veröffentlichen

Diese Seite richtet sich an Administratoren, die für Transparenzveröffentlichungen ein Vier-Augen-Prinzip einrichten,
und an die Redaktionen und Freigebenden, die damit arbeiten. Exponential hält eine Version als *ausstehend* zurück,
bis eine freigebende Person entscheidet, mit dem Workflow-Ereignis *Freigeben*. Die vollständige Referenz ist
[Workflows](../../../anleitungen/workflows.md).

## Inhalt

1. [Wie es funktioniert](#wie-es-funktioniert)
2. [Einrichten](#einrichten)
3. [Für Autoren](#für-autoren)
4. [Für Freigebende](#für-freigebende)
5. [Was aufgezeichnet wird](#was-aufgezeichnet-wird)
6. [Probleme](#probleme)

## Wie es funktioniert

1. Eine Redakteurin oder ein Redakteur klickt auf **Zur Veröffentlichung schicken**.
2. Der Trigger *Vor dem Veröffentlichen von Inhalten* startet den Freigabe-Workflow.
3. Das Ereignis *Freigeben* legt für die Freigebenden einen Eintrag unter Zusammenarbeit an; die Version ist ausstehend.
4. Eine freigebende Person klickt auf **Freigeben** oder **Ablehnen**.
5. Der Workflow-Cronjob läuft erneut; eine freigegebene Version wird veröffentlicht, eine abgelehnte geht als Entwurf
   an den Autor zurück.

## Einrichten

1. **Setup > Workflows**: Öffnen Sie eine Workflow-Gruppe, **Neuer Workflow**, benennen Sie ihn („Freigabe
   Transparenz“).
2. Wählen Sie **Event / Approve** und klicken Sie auf **Ereignis hinzufügen**. Stellen Sie ein:
   - **Betroffene Sektionen**: die Transparenzsektion;
   - **Betroffene Versionen**: *Alle Versionen* (neue und geänderte Einträge);
   - **Gruppen, die Content freigeben**: die Benutzergruppe der Freigebenden;
   - **Excluded user groups** (ausgenommene Benutzergruppen): keine, es sei denn, eine Gruppe darf direkt
     veröffentlichen.
3. **OK**.
4. **Setup > Trigger**: Zeile **Vor dem Veröffentlichen von Inhalten**, wählen Sie den Workflow, **Änderungen
   anwenden**. Ein Workflow pro Trigger; um ihn mit einem vorhandenen zu kombinieren, verwenden Sie ein Ereignis
   **Multiplexer**.
5. Prüfen Sie, ob die Cronjob-Gruppe `frequent` läuft: **Setup > Workflow-Prozesse** zeigt in seiner Statusleiste, ob
   der Workflow-Cronjob eingeplant ist. Ohne ihn werden freigegebene Inhalte nie veröffentlicht.

Eigene Inhalte einer freigebenden Person werden ohne Freigabe durchgelassen; benennen Sie deshalb mindestens zwei
Freigebende.

## Für Autoren

- **Meine Warteliste** auf dem Dashboard listet Ihre Versionen auf, die auf eine Freigabe warten, und was sie aufhält
  ([Entwürfe und ausstehende Einträge](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)).
- Eine abgelehnte Version kommt als Entwurf zurück; bearbeiten Sie sie und senden Sie sie erneut.

## Für Freigebende

- **Zusammenarbeit** auf dem Dashboard (`/collaboration/view/summary`) listet die Anfragen auf.
- Öffnen Sie die Anfrage, lesen Sie die Version (Vorschau), fügen Sie einen Kommentar hinzu, wenn Ihr Ablauf das
  vorsieht, und klicken Sie auf **Freigeben** oder **Ablehnen**.
- Eine Version, die auf Ihre Freigabe wartet, wird nur aufgeführt, wenn Sie sie lesen dürfen (`content/versionread`).

## Was aufgezeichnet wird

Die Versionsgeschichte behält, wer die Version erstellt hat; der Audit-Trail zeichnet das Veröffentlichungsereignis im
Kanal `content` auf ([Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)). Bedenken Sie, dass die Freigabeentscheidung
selbst im Eintrag unter Zusammenarbeit liegt; prüfen Sie in der Audit-Konsole Ihrer Installation, welche Ereignisse
sie zeigt, bevor Sie sich auf sie als Nachweis der Freigabe verlassen.

## Probleme

| Problem | Lösung |
|---|---|
| Freigegebene Einträge werden nicht veröffentlicht | Der Workflow-Cronjob läuft nicht; planen Sie den Teil mit `workflow.php` ein |
| Nichts wartet auf eine Freigabe | Der Inhalt liegt in einer anderen Sektion als unter **Betroffene Sektionen**, oder der Autor ist in einer ausgenommenen Gruppe oder selbst Freigebender |
| Ein Prozess muss gestoppt werden | **Setup > Workflow-Prozesse**, **Abbrechen...**: Die ausstehende Version wird wieder zum Entwurf; es werden keine Inhalte gelöscht |

## Verwandte Seiten

- [Workflows](../../../anleitungen/workflows.md)
- [Entwürfe und ausstehende Einträge](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)
- [Wer Datensätze veröffentlichen darf](../offene-daten/wer-datensaetze-veroeffentlichen-darf.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
