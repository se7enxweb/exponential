---
title: Pressemitteilungen
audience: government, website-creator, project-manager, admin
category: public-sector
order: 70
summary: Wie eine Pressestelle Pressemitteilungen auf einer Exponential-Site veröffentlicht - eine Klasse für Pressemitteilungen, Freigabe und ihre Ausnahmen, eine Sperrfrist mit dem Workflow-Ereignis Bis Datum warten, Pressekontakte als Rollen, ein Feed für Medien und Korrekturen.
keywords: Pressemitteilung, Pressestelle, Medien, Sperrfrist, Bis Datum warten, Workflow, Freigabe, Pressekontakt, RSS für Medien, Korrekturen, Newsroom, press release, press office, media, embargo, Wait until date, workflow, approval, press contact, RSS for media, corrections, newsroom
type: help
---
# Pressemitteilungen

Diese Seite richtet sich an Site-Ersteller, Projektleiter und Administratoren, die das Veröffentlichen für eine
Pressestelle einrichten. Pressemitteilungen brauchen Tempo, manchmal eine Sperrfrist und sorgfältige Korrekturen. Die
genannten Funktionen sind in [Workflows](../../../../anleitungen/workflows.md), [RSS-Feeds](../../../../anleitungen/rss-feeds.md)
und [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md) dokumentiert.

## Inhalt

1. [Eine Klasse für Pressemitteilungen](#eine-klasse-für-pressemitteilungen)
2. [Freigabe](#freigabe)
3. [Sperrfrist](#sperrfrist)
4. [Der Newsroom](#der-newsroom)
5. [Korrekturen](#korrekturen)

## Eine Klasse für Pressemitteilungen

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Titel | `title` | Textzeile | |
| Datum und Uhrzeit der Freigabe | `release_date` | Datum und Zeit | das Ende der Sperrfrist und das angezeigte Datum |
| Vorspann | `intro` | XML Block | der erste Absatz |
| Text | `body` | XML Block | |
| Hinweise für Redaktionen | `notes` | XML Block | Hintergrund für Journalisten |
| Pressekontakt | `press_contact` | XML Block | Durchwahl und Postfach der Pressestelle, nicht die einer Privatperson |
| Anhänge | `attachments` | Objektbeziehungen | Fotos (mit Alternativtext und Bildnachweis) und Dokumente |
| Themen | `tags` | Tags | |

Die mitgelieferte Klasse Article funktioniert ebenfalls, wenn Sie Sperrfristdatum und Hinweise nicht brauchen
([Nachrichtenartikel](nachrichtenartikel.md)).

## Freigabe

| Regel | Einstellung |
|---|---|
| Mitteilungen werden vor dem Veröffentlichen freigegeben | ein Ereignis *Approve* mit **Betroffene Sektionen** gleich der Pressesektion, Freigebende die Leitung der Kommunikation |
| Die Pressestelle veröffentlicht dringende Erklärungen sofort | die Gruppe der Pressestelle unter **Gruppen ausschließen**, wenn Ihre Regeln es erlauben |
| Freigaben werden vom Workflow-Cronjob durchgelassen | prüfen Sie, dass **Setup > Workflow Prozesse** ihn als geplant zeigt |

## Sperrfrist

Das mitgelieferte Workflow-Ereignis **Bis Datum warten** hält eine Version zurück, bis ein Datumsattribut des Objekts
erreicht ist; der Workflow-Cronjob lässt sie dann durch:

1. **Setup > Workflows**, eine Gruppe, **Neuer Workflow**; fügen Sie **Event / Wait until date** hinzu.
2. Wählen Sie im Ereignis die Klasse Pressemitteilung und das Attribut Datum und Uhrzeit der Freigabe
   (**Klasse/Attribut Kombination**); **OK**.
3. Verbinden Sie ihn unter **Setup > Auslöser** mit **Vor dem Veröffentlichen von Inhalten**. Nur ein Workflow je
   Auslöser: Ist bereits ein Freigabe-Workflow verbunden, fügen Sie beide Ereignisse einem Workflow hinzu (zuerst
   freigeben, dann warten) oder verwenden Sie einen *Multiplexer*.
4. Testen Sie: Veröffentlichen Sie eine Mitteilung mit einer Zeit zehn Minuten in der Zukunft; sie bleibt ausstehend;
   nach dieser Zeit und dem nächsten Lauf des Workflow-Cronjobs (der Teil frequent) ist sie veröffentlicht.

Ohne gesetztes Datum oder mit einem vergangenen Datum lässt das Ereignis die Version sofort durch. Die Mitteilung
erscheint beim ersten Cronjob-Lauf nach dem Zeitpunkt, nicht auf die Sekunde genau; planen Sie den Teil frequent oft
genug ein. Ein Text unter Sperrfrist liegt vor der Freigabe in der Datenbank: Beschränken Sie, wer ausstehende
Versionen lesen darf (`content/versionread`).

## Der Newsroom

| Teil | Wie |
|---|---|
| Ein Presseordner mit den Mitteilungen, die neuesten zuerst | nach Veröffentlichungsdatum sortieren |
| Ein RSS-Export für Medien | [RSS-Feeds](rss-feeds.md) |
| Medien können per E-Mail folgen | Benachrichtigungen für Kontoinhaber ([Benachrichtigungen und Abonnements](benachrichtigungen-und-abonnements.md)) |
| Pressebilder | Bildobjekte mit Bildnachweis und Alternativtext, in einer Galerie |
| Kontakt | der Ansprechpartner der Pressestelle, keine persönlichen Nummern |

## Korrekturen

1. **Bearbeiten** Sie die Mitteilung, korrigieren Sie, fügen Sie am Ende „Korrigiert am <Datum>: <was>“ hinzu.
2. Veröffentlichen Sie. Der frühere Text bleibt in der Versionshistorie; **Vergleichen** zeigt die Änderung.
3. Für eine Korrektur, von der Medien wissen müssen, veröffentlichen Sie normal, damit Folgende benachrichtigt werden;
   für einen Tippfehler **Ohne Benachrichtigung veröffentlichen**, wo eingeschaltet
   ([Ohne Benachrichtigung veröffentlichen](../../../../../eng-us/features/6.0/publish-without-notification.md)).

Entfernen Sie nie eine veröffentlichte Pressemitteilung, um einen Fehler zu verbergen; korrigieren Sie ihn offen, es sei
denn, sie muss aus rechtlichen Gründen zurückgezogen werden.

## Verwandte Seiten

- [Nachrichten und Warnmeldungen](README.md)
- [Nachrichtenartikel](nachrichtenartikel.md)
- [Entwürfe und ausstehende Elemente](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
