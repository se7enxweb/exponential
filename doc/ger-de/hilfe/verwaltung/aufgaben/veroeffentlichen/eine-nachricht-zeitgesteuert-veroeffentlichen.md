---
title: Eine Nachricht zeitgesteuert veröffentlichen
audience: government, editor, admin
category: public-sector
order: 80
summary: Für Redakteure und den Administrator, der es einrichtet - eine Nachricht oder Pressemitteilung zu einer festen Zeit veröffentlichen, mit dem Workflow-Ereignis Bis Datum warten von Exponential und dem Workflow-Cronjob, was der Redakteur tut, wie man es prüft und die ehrlichen Grenzen.
keywords: zeitgesteuert, geplante Veröffentlichung, Sperrfrist, später veröffentlichen, Bis Datum warten, Wait until date, Workflow, Auslöser, Workflow-Cronjob, ausstehend, Veröffentlichungsdatum, Freigabezeit, schedule, scheduled publishing, embargo, publish later, trigger, workflow cronjob, pending, publish date, release time
type: help
---
# Eine Nachricht zeitgesteuert veröffentlichen

Diese Seite ist für Redakteure, bei denen ein Element zu einer festen Zeit erscheinen muss (eine Pressemitteilung mit
Sperrfrist, eine Nachricht zu einem Start), und für den Administrator, der das einrichtet. Exponential plant die
Veröffentlichung mit dem Workflow-Ereignis *Bis Datum warten*: Beim Veröffentlichen hält es die Version zurück, bis ein
Datumsattribut des Objekts erreicht ist; danach veröffentlicht der Workflow-Cronjob sie
([Workflows](../../../../anleitungen/workflows.md), `kernel/classes/workflowtypes/event/ezwaituntildate/`).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte für den Redakteur](#schritte-für-den-redakteur)
3. [Einmalige Einrichtung für den Administrator](#einmalige-einrichtung-für-den-administrator)
4. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Ihre Klasse hat ein Attribut vom Typ Datum und Uhrzeit für den Veröffentlichungszeitpunkt (zum Beispiel
  **Release date and time**), und der Administrator hat es mit einem Ereignis *Bis Datum warten* verbunden
  (Einrichtung unten).
- Der häufige Teil der Cronjobs läuft oft genug (typisch ist alle paar Minuten).

## Schritte für den Redakteur

1. Legen Sie das Element wie üblich an.
2. Setzen Sie **Release date and time** auf den Zeitpunkt, zu dem es erscheinen soll.
3. **Zur Veröffentlichung schicken**. Die Version ist noch nicht veröffentlicht: Sie ist *ausstehend*.
4. Sie finden sie unter **Meine ausstehenden Elemente** (`/content/pendinglist`): Der Workflow hält sie zurück.

Eine ausstehende Version lässt sich nicht bearbeiten. Um die Zeit zu ändern, bitten Sie den Administrator, den
wartenden Prozess unter **Setup > Workflow Prozesse** abzubrechen (**Abbrechen...** macht die Version wieder zu einem
Entwurf); bearbeiten Sie sie dann und schicken Sie sie erneut.

## Einmalige Einrichtung für den Administrator

1. **Setup > Workflows**, eine Workflow-Gruppe, **Neuer Workflow**; benennen Sie ihn, wählen Sie
   **Ereignis / Bis Datum warten**, **Ereignis hinzufügen**.
2. Im Ereignis unter **Class/attribute combinations**: Wählen Sie die Klasse und ihr Attribut für das
   Veröffentlichungsdatum; **Eigenschaften aktualisieren**; **OK**.
3. **Setup > Auslöser**: Wählen Sie in der Zeile für das Veröffentlichen von Inhalten, vorher, den Workflow;
   **Änderungen anwenden**. Ein Workflow je Auslöser: Wenn dort schon ein Freigabe-Workflow steht, legen Sie beide
   Ereignisse in einen Workflow oder verwenden Sie einen *Multiplexer*.
4. Prüfen Sie **Setup > Workflow Prozesse**: Die Statusleiste nennt den Teil der Cronjobs, der wartende Prozesse
   fortsetzt, und sagt, ob die Crontab ihn ausführt.

## Das Ergebnis prüfen

- [ ] Direkt nach dem Senden ist das Element für abgemeldete Besucher nicht sichtbar.
- [ ] **Setup > Workflow Prozesse** listet dafür einen wartenden Prozess.
- [ ] Nach dem Veröffentlichungszeitpunkt und dem nächsten Lauf des häufigen Cronjobs ist das Element veröffentlicht
  und sichtbar.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Das Datumsattribut ist leer oder liegt in der Vergangenheit | das Ereignis lässt die Version sofort durch |
| Die exakte Sekunde erwarten | es erscheint beim ersten Lauf des Cronjobs nach der Zeit |
| Der Cronjob läuft nicht | das Element wartet ewig; prüfen Sie die Statusleiste |
| Ein Text mit Sperrfrist liegt bereits in der Datenbank | beschränken Sie, wer ausstehende Versionen lesen darf (`content/versionread`) |
| Auf Ihrer Site ist keine Zeitsteuerung eingerichtet | die ehrliche Alternative: Bereiten Sie das Element als Entwurf vor (**Entwurf speichern und beenden**) und veröffentlichen Sie es zur Zeit von Hand |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Eine Pressemitteilung veröffentlichen](eine-pressemitteilung-veroeffentlichen.md)
- [Pressemitteilungen: Sperrfrist](../../aufbauen/nachrichten-und-warnmeldungen/pressemitteilungen.md#sperrfrist)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
