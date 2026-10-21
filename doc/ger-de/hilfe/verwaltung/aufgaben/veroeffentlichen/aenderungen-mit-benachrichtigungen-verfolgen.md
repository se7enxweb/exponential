---
title: Änderungen mit Benachrichtigungen verfolgen
audience: government, editor, user
category: public-sector
order: 490
summary: Für Redakteure und Kontoinhaber - einem Ordner oder einer Seite mit Benachrichtigen folgen, damit Sie eine E-Mail erhalten, wenn darunter etwas veröffentlicht wird, unter Meine Benachrichtigungseinstellungen zwischen sofort und einer Zusammenfassung wählen und das Folgen beenden.
keywords: Benachrichtigungen, Benachrichtigen, folgen, Meine Benachrichtigungseinstellungen, Digest, Zusammenfassung, täglich, wöchentlich, E-Mail, Abonnements, Einträge hinzufügen, Ausgewählte entfernen, notification/use, notifications, Notify me, follow, My notification settings, digest, daily, weekly, subscriptions, Add items, Remove selected
type: help
---
# Änderungen mit Benachrichtigungen verfolgen

Diese Seite ist für Redakteure, die wissen möchten, wann Kollegen in einem Bereich veröffentlichen (Freigebende,
Teamleitungen), und für Kontoinhaber der öffentlichen Site, die Nachrichten oder Bekanntmachungen folgen. Exponential
schickt Ihnen eine E-Mail, wenn unter einem Element, dem Sie folgen, etwas veröffentlicht wird
([Benachrichtigungen: das Handbuch für Benutzer](../../../../../eng-us/features/6.0/notifications.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Ihr Konto hat die Richtlinie `notification/use` (wenn der Menüeintrag fehlt, fragen Sie Ihren Administrator).
- Die E-Mails gehen an die E-Mail-Adresse Ihres Kontos.

## Schritte

**Einem Element folgen:**

1. Öffnen Sie den Ordner oder die Seite in der Inhaltsstruktur; wählen Sie in ihrem Menü **Benachrichtigen**.
2. Klicken Sie auf der Seite „Über Änderungen benachrichtigen“ auf **Benachrichtigen**. (Das bloße Öffnen der Adresse
   ändert nichts.)

Oder: **Meine Benachrichtigungseinstellungen** (`/notification/settings`), **Einträge hinzufügen**, Inhalte im Browser
auswählen, bestätigen.

**Wählen, wie die E-Mails kommen:**

1. **Meine Benachrichtigungseinstellungen**.
2. Markieren Sie **Alle Nachrichten gesammelt in einem Digest erhalten**, wählen Sie **Täglich um** eine Uhrzeit,
   **Einmal pro Woche, am** einen Tag oder **Einmal pro Monat, am Tag**; klicken Sie auf
   **Digest-Einstellungen speichern**. Wählen Sie es ab, um E-Mails sofort zu erhalten.

**Das Folgen beenden:** Markieren Sie unter **Meine Benachrichtigungseinstellungen** die Einträge und klicken Sie auf
**Ausgewählte entfernen**, dann **Entfernen**; oder öffnen Sie die Seite **Benachrichtigen** des Elements erneut und
wählen Sie **Benachrichtigungen für diesen Eintrag beenden**.

## Das Ergebnis prüfen

- [ ] **Meine Benachrichtigungseinstellungen** listet das Element („Einträge, denen ich folge“).
- [ ] Nachdem jemand darunter veröffentlicht hat und der Cronjob für Benachrichtigungen gelaufen ist, erhalten Sie
  eine E-Mail (oder den Digest zu seiner Zeit).

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| E-Mails innerhalb von Sekunden erwarten | die E-Mails gehen beim nächsten Lauf der Benachrichtigungen raus oder mit dem Digest |
| Der ganzen Site folgen | zu viele E-Mails; folgen Sie den Ordnern, auf die es ankommt |
| Einträge, deren Inhalt gelöscht wurde | markiert mit „Inhalt existiert nicht mehr“; entfernen Sie sie |
| Überhaupt keine E-Mail | der Cronjob für Benachrichtigungen läuft vielleicht nicht; informieren Sie das Web-Team |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Veröffentlichen, ohne die Abonnenten zu benachrichtigen](veroeffentlichen-ohne-die-abonnenten-zu-benachrichtigen.md)
- [Ihr Konto und die Anmeldung](../../../konten-und-anmeldung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
