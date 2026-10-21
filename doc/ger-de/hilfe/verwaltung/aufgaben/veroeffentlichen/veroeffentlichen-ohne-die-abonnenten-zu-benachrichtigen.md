---
title: Veröffentlichen, ohne die Abonnenten zu benachrichtigen
audience: government, editor, admin
category: public-sector
order: 480
summary: Für Redakteure - eine kleine Korrektur veröffentlichen, ohne Benachrichtigungs-E-Mails an die Menschen zu senden, die dem Ort der Seite folgen, mit Ohne Benachrichtigung veröffentlichen, wo die Site es eingeschaltet hat und Ihre Rolle die Richtlinie hat; und wann Sie es nicht verwenden sollten.
keywords: Ohne Benachrichtigung veröffentlichen, Benachrichtigungen, Abonnenten, kleine Korrektur, Tippfehler, PublishWithoutNotification, content/publish_without_notification, notification.ini, Publish without notification, notifications, subscribers, minor correction, typo
type: help
---
# Veröffentlichen, ohne die Abonnenten zu benachrichtigen

Diese Seite ist für Redakteure, die einen Tippfehler oder Metadaten in einer Seite korrigieren, der Menschen per E-Mail
folgen. Wo die Site es anbietet, veröffentlicht **Ohne Benachrichtigung veröffentlichen** die Version, ohne ein
Benachrichtigungsereignis zu erzeugen, sodass die Folgenden dafür keine E-Mail erhalten
([Ohne Benachrichtigung veröffentlichen](../../../../../eng-us/features/6.0/publish-without-notification.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Wann Sie es nicht verwenden sollten](#wann-sie-es-nicht-verwenden-sollten)
5. [Einrichtung für Administratoren](#einrichtung-für-administratoren)

## Bevor Sie beginnen

- Die Schaltfläche erscheint nur, wenn `notification.ini [NotificationSettings] PublishWithoutNotification=enabled`
  gesetzt ist und Ihre Rolle `content/publish_without_notification` hat. Wenn Sie sie nicht sehen, bietet die Site sie
  Ihnen nicht an.

## Schritte

1. **Bearbeiten** Sie die Seite, nehmen Sie die kleine Änderung vor.
2. Klicken Sie auf **Ohne Benachrichtigung veröffentlichen** (neben **Zur Veröffentlichung schicken**; auch in der
   Versionsvorschau).
3. Die Version wird wie üblich veröffentlicht; Freigaberegeln gelten weiterhin.

## Das Ergebnis prüfen

- [ ] Die Seite zeigt die Änderung.
- [ ] **Setup > Benachrichtigungen** zeigt dafür kein neues Ereignis (fragen Sie das Web-Team, wenn Sie es bestätigen
  müssen).

## Wann Sie es nicht verwenden sollten

| Änderung | Verwenden |
|---|---|
| Ein Tippfehler, Formatierung, eine Korrektur der Metadaten | **Ohne Benachrichtigung veröffentlichen** |
| Ein geändertes Datum, ein Betrag, ein Ort oder eine Regel | **Zur Veröffentlichung schicken**: Die Menschen müssen es wissen |
| Eine Korrektur von etwas, nach dem Menschen gehandelt haben | **Zur Veröffentlichung schicken**, mit einem Korrekturhinweis ([Eine veröffentlichte Bekanntmachung korrigieren und den Verlauf behalten](eine-veroeffentlichte-bekanntmachung-korrigieren-und-den-verlauf-behalten.md)) |

## Einrichtung für Administratoren

1. `notification.ini [NotificationSettings] PublishWithoutNotification=enabled` in `settings/override/` (alle
   Siteaccesses) oder nur im Siteaccess der Administration; leeren Sie den INI-Cache.
2. Fügen Sie die Richtlinienfunktion **content / publish_without_notification** den Rollen der Redakteure hinzu, die
   sie verwenden dürfen (eine Richtlinie `content/*` enthält sie bereits).

Ohne die Richtlinie veröffentlicht eine gesendete Schaltfläche mit Benachrichtigung, und eine Warnung wird
protokolliert.

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Änderungen mit Benachrichtigungen verfolgen](aenderungen-mit-benachrichtigungen-verfolgen.md)
- [Benachrichtigungen und Abonnements](../../aufbauen/nachrichten-und-warnmeldungen/benachrichtigungen-und-abonnements.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
