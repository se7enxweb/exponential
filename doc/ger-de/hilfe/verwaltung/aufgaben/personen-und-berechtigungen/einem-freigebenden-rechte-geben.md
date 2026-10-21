---
title: Einem Freigebenden Rechte geben
audience: government, admin
category: public-sector
order: 30
summary: Schritt für Schritt - eine Kollegin oder einen Kollegen zum Freigebenden von Inhalten vor der Veröffentlichung machen, indem Sie sie in die im Ereignis Approve genannte Gruppe der Freigebenden aufnehmen und sicherstellen, dass die Rolle der Gruppe den Eingang der Zusammenarbeit und ausstehende Versionen öffnet.
keywords: Freigebender, Freigaberechte geben, Gruppe der Freigebenden, Ereignis Approve, Zusammenarbeit, versionread, Abzeichnung, Prüfer, Workflow, approver, give approval rights, approvers group, Approve event, collaboration, sign-off, reviewer, workflow
type: help
---
# Einem Freigebenden Rechte geben

Diese Aufgabe ist für Administratoren. Am Ende erhält eine Kollegin oder ein Kollege Freigabeanfragen für einen Bereich
und kann sie freigeben oder ablehnen. Hintergrund:
[Rollengestaltung: Freigebende](../../../../../eng-us/help/government/operations/people-and-permissions/role-design-approvers.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Wenn die Gruppe der Freigebenden existiert](#wenn-die-gruppe-der-freigebenden-existiert)
3. [Wenn sie noch nicht existiert](#wenn-sie-noch-nicht-existiert)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Fallstricke](#fallstricke)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Freigabe-Workflow ist unter **Setup > Auslöser** mit **Vor dem Veröffentlichen von Inhalten** verbunden
  ([Einen Freigabe-Workflow für eine Sektion einrichten](einen-freigabe-workflow-fuer-eine-sektion-einrichten.md)).
- Sie können Benutzerkonten bearbeiten (um einen Ort in einer Gruppe hinzuzufügen) und, für eine neue Gruppe, Rollen und
  Workflows.
- Der Antrag, die Person zum Freigebenden zu machen, ist nach Ihren Regeln genehmigt.

## Wenn die Gruppe der Freigebenden existiert

1. Öffnen Sie das Benutzerkonto der Kollegin oder des Kollegen im Baum **Benutzer**.
2. Fügen Sie einen Ort in der Gruppe der Freigebenden hinzu (Reiter **Orte**, **Orte hinzufügen**, die Gruppe wählen).
   Das Konto behält seine anderen Gruppen.
3. Die Rollen der Gruppe gelten sofort.

## Wenn sie noch nicht existiert

1. Legen Sie unter Benutzer eine Benutzergruppe an, zum Beispiel „Approvers - Department of Example“.
2. **Benutzer > Rollen und Richtlinien > Neue Rolle** „Approver“ mit diesen Richtlinien:

   | Modul | Funktion | Einschränkungen |
   |---|---|---|
   | collaboration | (**Zugriff auf alle Funktionen gewähren**) | keine |
   | content | read | die Sektion oder der Teilbaum des Bereichs |
   | content | versionread, diff | dieselbe |
   | content | dashboard, pendinglist | keine |

   Das Modul collaboration definiert keine eigenen Funktionen, also gilt die Richtlinie für das ganze Modul; ohne sie
   antwortet der Eingang „Die angeforderte Seite konnte nicht angezeigt werden“.
3. **Speichern**, dann der Gruppe die Rolle **Zuweisen**.
4. **Setup > Workflows**, öffnen Sie den Freigabe-Workflow, bearbeiten Sie sein Ereignis **Approve** und fügen Sie die
   Gruppe unter **Gruppen, die Content freigeben** hinzu. **OK**.
5. Nehmen Sie die Kollegin oder den Kollegen wie oben in die Gruppe auf.

## Prüfen, ob es funktioniert hat

1. Veröffentlichen Sie als Redakteur (nicht als Freigebender) eine Testseite im betroffenen Bereich: Sie wartet.
2. Öffnen Sie als die Kollegin oder der Kollege **Mein Konto > Zusammenarbeit** (`/collaboration/view/summary`): Die
   Anfrage ist da. **Meine ausstehenden Elemente** listet sie unter „Wartet auf Ihre Freigabe“.
3. **Ablehnen** Sie den Test mit einem Kommentar; nach dem nächsten Lauf des Workflow-Cronjobs ist die Testversion
   wieder ein Entwurf.

## Fallstricke

- Die eigenen Inhalte eines Freigebenden lässt das Ereignis Approve durch; wenn Ihre Regeln das verbieten, siehe
  [Vier-Augen-Prinzip beim Veröffentlichen](../../../../../eng-us/help/government/operations/editorial-workflows/two-person-rule-for-publishing.md).
- Anfragen, die vor dem Beitritt der Kollegin oder des Kollegen gestellt wurden, behalten ihre alten Teilnehmer.
- Ohne `content/versionread` für den Bereich werden wartende Versionen für den Freigebenden nicht gelistet.

## Verwandte Seiten
- [Eingang der Zusammenarbeit](../../../../../eng-us/help/government/operations/editorial-workflows/collaboration-inbox.md)
- [Das Ereignis Approve einrichten](../../../../../eng-us/help/government/operations/editorial-workflows/setting-up-the-approve-event.md)
- [Eine Rollenzuweisung entfernen](eine-rollenzuweisung-entfernen.md)
- [Anleitung zu den Workflows](../../../../anleitungen/workflows.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
