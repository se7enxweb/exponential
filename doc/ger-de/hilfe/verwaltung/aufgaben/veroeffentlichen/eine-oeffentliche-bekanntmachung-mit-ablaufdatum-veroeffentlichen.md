---
title: Eine öffentliche Bekanntmachung mit Ablaufdatum veröffentlichen
audience: government, editor, admin
category: public-sector
order: 40
summary: Für Redakteure und die Administratoren, die es einrichten - eine öffentliche Bekanntmachung veröffentlichen, die nach ihrem Enddatum von selbst verschwindet, mit einem Attribut für das Versteckdatum und dem Cronjob hide.php, und die Alternativen kennen, wenn das automatische Verstecken nicht eingerichtet ist.
keywords: öffentliche Bekanntmachung, amtliche Bekanntmachung, Ablaufdatum, Versteckdatum, hide.php, HideSettings, unpublish.php, Cronjob, automatisches Verstecken, Bekanntmachungsfrist, public notice, statutory notice, expiry date, hide date, cronjob, automatic hiding, notice period
type: help
---
# Eine öffentliche Bekanntmachung mit Ablaufdatum veröffentlichen

Diese Seite ist für Redakteure, die Bekanntmachungen veröffentlichen, die für einen festen Zeitraum gelten (ein
Bauantrag, eine Straßensperrung, eine amtliche Bekanntmachung), und für den Administrator, der das automatische
Verstecken einmal einrichtet. Exponential versteckt Inhalte nach einem Datum mit dem Cronjob `hide.php`; er braucht
ein Datumsattribut an der Klasse und eine Einstellung ([Cronjobs](../../../../anleitungen/cronjobs.md),
[Archiv und Ablauf](../../aufbauen/nachrichten-und-warnmeldungen/archiv-und-ablauf.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte für den Redakteur](#schritte-für-den-redakteur)
3. [Einmalige Einrichtung für den Administrator](#einmalige-einrichtung-für-den-administrator)
4. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Ihre Klasse für Bekanntmachungen hat ein Attribut vom Typ Datum und Uhrzeit für das Ende, zum Beispiel
  **Hide after** (`hide_date`), wie in
  [Inhaltsmodell für Kommunen](../../ebenen/kommunen/inhaltsmodell.md#hinzuzufügende-klasse-öffentliche-bekanntmachung).
- Das automatische Verstecken ist für den Ordner der Bekanntmachungen eingerichtet (fragen Sie das Web-Team;
  Einrichtung unten).
- Sie kennen die Bekanntmachungsfrist, die Ihre Regeln verlangen.

## Schritte für den Redakteur

1. Öffnen Sie den Ordner der Bekanntmachungen, **Neu erstellen**, wählen Sie die Klasse für Bekanntmachungen.
2. Füllen Sie Titel, Art, Text und Dokumente aus.
3. Setzen Sie **Hide after** auf das Ende der Bekanntmachungsfrist (Datum und Uhrzeit).
4. **Vorschau**, dann **Zur Veröffentlichung schicken**.

## Einmalige Einrichtung für den Administrator

```ini
# settings/override/content.ini.append.php
[HideSettings]
RootNodeList[]=<node id of the Notices folder>
HideDateAttributeList[public_notice]=hide_date
```

Leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`). `hide.php` steht im globalen Teil der Cronjobs
(`[CronjobSettings] Scripts[]`); prüfen Sie, ob die Crontab ihn ausführt (**Setup > Cronjobs**). Testen Sie einmal:
`php runcronjobs.php -s <siteaccess> --script=hide.php`.

## Das Ergebnis prüfen

- [ ] Die Bekanntmachung ist jetzt für abgemeldete Besucher sichtbar.
- [ ] Nach der Endzeit und dem nächsten Lauf des Cronjobs ist sie versteckt: Besucher sehen sie nicht mehr; in der
  Administration wird sie als versteckt angezeigt und kann wieder eingeblendet werden.
- [ ] Die Bekanntmachung existiert für die Akten weiterhin.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Kein Enddatum ausgefüllt | die Bekanntmachung bleibt für immer; machen Sie das Attribut zum Pflichtfeld, wenn jede Bekanntmachung endet |
| Der Cronjob läuft nicht | nichts wird versteckt; prüfen Sie die Seite der Cronjobs nach Änderungen am Server |
| Erwarten, dass die Bekanntmachung auf die Minute verschwindet | sie wird beim ersten Lauf nach der Zeit versteckt |
| Stattdessen `unpublish.php` verwenden | es verschiebt Objekte mit einem Attribut `unpublish_date` in den Papierkorb; das Verstecken behält den Vorgang |
| Ohne automatisches Verstecken | verstecken Sie sie am Enddatum von Hand ([Eine Seite vorübergehend verstecken](eine-seite-voruebergehend-verstecken.md)); tragen Sie eine Erinnerung in Ihren Kalender ein |

Objektzustände und Sektionen beenden Inhalte nicht von sich aus nach Datum; nur die Cronjobs handeln nach Daten.

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Eine dringende Warnmeldung auf der Startseite veröffentlichen](eine-dringende-warnmeldung-auf-der-startseite-veroeffentlichen.md)
- [Archiv und Ablauf](../../aufbauen/nachrichten-und-warnmeldungen/archiv-und-ablauf.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
