---
title: "Newsletter mit CJW Newsletter: Kursüberblick"
audience: trainer, editor, admin
category: training
order: 1
summary: Der Kurs für alle, die Newsletter mit der Erweiterung cjw_newsletter schreiben, versenden und betreiben - 6 Module und 20 Lektionen, 16 Stunden Lektionen, auf einer Schulungsinstallation, die jede Mail in eine Datei schreibt und nur Testadressen unter example.invalid verwendet.
keywords: newsletter mit cjw newsletter, newsletter-kurs, cjw_newsletter, newsletter-schulung, abonnenten, double opt-in, versand, cronjobs, bounces, transporte, skins, kursüberblick, newsletters with cjw newsletter, newsletter course, newsletter training, subscribers, sending, transports, course overview
type: help
---
# Newsletter mit CJW Newsletter: Kursüberblick

Dieser Kurs vermittelt das Newsletter-System von Exponential, die Erweiterung `cjw_newsletter`, vom ersten Blick auf
das Dashboard bis zu einem eigenen Skin. Die Teilnehmenden legen eine Liste an, gewinnen erfundene Abonnenten über das
echte Double-Opt-in, schreiben und testen Ausgaben, versenden sie, arbeiten die Warteschlange von Hand ab und planen
den Umstieg auf echten Mailversand für ihre eigene Website.

Eine Regel gilt von der ersten bis zur letzten Lektion: **Es wird nie etwas an eine echte Person gesendet**. Die
Schulungsinstallation schreibt jede Mail in eine Datei, und jede Abonnenten-, Absender- und Testadresse endet auf
`@example.invalid`.

## Für wen er gedacht ist

- Redakteure, die Newsletter schreiben und versenden werden (Module 1 bis 4 und Lektion 6.1).
- Administratoren, die den Newsletter einrichten und betreiben: Cron, Bounces, Transporte, Zustellbarkeit (alle
  Module; Modul 5 gehört ihnen).
- Designer und Entwickler, die dem Newsletter sein Aussehen geben (Modul 6, nach einem Blick auf die Module 2 und 4).

## Ergebnis

Nach dem Kurs können die Teilnehmenden Newsletter-Systeme und Listen mit einer soliden Konfiguration anlegen,
Ausgaben mit Artikeln schreiben, das Anmeldeformular und das Double-Opt-in betreiben, Abonnenten mit dokumentierter
Einwilligung verwalten, importieren und exportieren, Adressen abmelden und auf die Sperrliste setzen, Ausgaben in der
Vorschau ansehen und testen, sie versenden und die Warteschlange verfolgen, einen Versand abbrechen, das Archiv und
die Statistiken lesen, Bounces behandeln, den Umstieg von Dateien auf echten Versand planen, Versandraten und
Testgruppen nutzen und einen Skin schreiben.

## Form und Dauer

| | |
|---|---|
| Lektionen | 20 Lektionen in 6 Modulen |
| Lektionszeit | 16 Stunden (Präsentation und Übungen) |
| Mit Pausen | etwa 17 Stunden, wie in den Zeitplänen der Module |
| Form | Schulungsraum mit Beamer oder online mit Bildschirmfreigabe; alle Teilnehmenden an ihrem eigenen Computer |
| Gruppengröße | Bis zu etwa 10 Teilnehmende pro Trainer; bei den Versandübungen muss der Trainer den Postausgang im Blick behalten |
| Niveau | Einsteiger bis Experten (die späteren Module für Administratoren und Designer) |

## Voraussetzungen

- Teilnehmende: grundlegendes Bearbeiten von Inhalten, wie in [Redakteure 101](../../../../eng-us/help/trainers/editors-101/README.md) vermittelt
  (ein Element anlegen, bearbeiten und veröffentlichen). Administratoren: eine Shell auf einer Schulungsinstallation.
  Designer: die Template-Sprache.
- Trainer: hat den ganzen Kurs einmal auf der eigenen Schulungsinstallation durchgespielt, einschließlich jeder
  Versandübung, und kennt das Material von Train the Trainer
  ([Train the Trainer: Kursüberblick](../../../../eng-us/help/trainers/train-the-trainer/README.md)).

## Übungsumgebung

- Eine Schulungsinstallation, zum Beispiel installiert mit `php bin/php/console exp:install`
  ([7. Die Installation über die Konsole](../../../installation/07-installation-ueber-die-konsole.md)), mit aktiver Erweiterung
  `cjw_newsletter`, angelegten Tabellen, dem Newsletter-Baum mit seiner Sektion und dem Anmeldeformular, das im
  öffentlichen Siteaccess erreichbar ist. Wie man eine Übungsumgebung aufbaut und zurücksetzt:
  [Modul 2: Die Übungsumgebung aufbauen](../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/README.md) von Train the
  Trainer.
- **Jede Mail wird in Dateien geschrieben**: `site.ini [MailSettings] Transport=file`, und in
  `cjw_newsletter.ini [NewsletterMailSettings]` stehen die Methoden `TransportMethodCronjob`, `TransportMethodPreview`
  und `TransportMethodDirectly` alle auf `file`, geprüft in der Einstellungsansicht. Der mitgelieferte Wert von
  `TransportMethodPreview` ist `sendmail`; [Lektion 1.2](modul-01-erste-schritte/eine-sichere-schulungsinstallation-vorbereiten.md)
  ändert ihn.
- **Nur erfundene Personen**: Abonnenten, Absender, Testempfänger und Testgruppen unter `example.invalid`. Niemals eine
  Kopie einer echten Abonnentenliste.
- Ein Schulungskonto für die Administration pro Person; Shell-Zugang als Benutzer des Webservers für Administratoren
  und Designer.
- Während des Kurses kein Cron für die Newsletter-Teile auf einer gemeinsam genutzten Installation: Der Trainer startet
  die Warteschlange.

## Module

| Modul | Lektionen | Dauer | Themen |
|---|---|---|---|
| [Modul 1: Erste Schritte](modul-01-erste-schritte/README.md) | 3 | 2 Std. | Die Bestandteile, eine Schulungsinstallation, die nicht senden kann, das Dashboard |
| [Modul 2: Systeme, Listen und Ausgaben](modul-02-systeme-listen-und-ausgaben/README.md) | 4 | 3 Std. 30 Min. | Newsletter-Wurzel und Systeme, Listenkonfiguration, Ausgaben und Artikel, wiederkehrende Versände, Pools, Freigabe |
| [Modul 3: Abonnenten](modul-03-abonnenten/README.md) | 4 | 3 Std. 15 Min. | Anmeldeformular und Double-Opt-in, Benutzer und Abonnements, CSV-Import und -Export, Einwilligung, Abmelden, Sperrliste |
| [Modul 4: Versand](modul-04-versenden/README.md) | 4 | 3 Std. 15 Min. | Vorschauen und Testversand, Versand, Cronjobs und Konsole, Abbrechen, Archiv, Statistiken |
| [Modul 5: Bounces und Transporte](modul-05-bounces-und-transporte/README.md) | 3 | 2 Std. 15 Min. | Mailkonten und Bounces, Livegang, Versandraten, Testgruppen, Unterdrückung |
| [Modul 6: Templates und Skins](modul-06-templates-und-skins/README.md) | 2 | 1 Std. 45 Min. | Skins, HTML- und Textteil, Platzhalter, einen Skin schreiben |

## Lesepfad

Für einen Trainer, der den Kurs vorbereitet, in dieser Reihenfolge:

1. [cjw_newsletter: Newsletter in der Administration](../../../../eng-us/features/6.0/extensions/cjw_newsletter.md): die
   Bestandteile, die Probe ohne Mail, die Einstellungen und was sich in den Releases von Exponential 6 geändert hat.
2. [Newsletter mit cjw_newsletter](../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md),
   [Newsletter-Listen und Abonnements](../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md) und
   [Newsletter sicher versenden](../../../../eng-us/help/creators/website-creators/sending-newsletters.md): die drei kurzen Seiten, denen
   der Kurs folgt.
3. [10.4 Mail](../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand) und
   [10.5 Benachrichtigungen und Newsletter](../../../installation/10-nach-der-installation.md#105-benachrichtigungen-und-newsletter):
   die Transporte und die Cron-Teile.
4. [Spezifikation: die Erweiterung cjw_newsletter](../../../spezifikationen/6.0/cjw_newsletter.md): jede Ansicht, jeder
   Befehl, jeder Cronjob-Teil und jedes Dashboard-Problem.
5. [E-Mail-Einstellungen: einrichten und betreiben](../../../anleitungen/mail-einstellungen-administrator.md) und
   [Cronjobs](../../../anleitungen/cronjobs.md).
6. Die eigenen Anleitungen der Erweiterung für 4.2 (Zustellbarkeit, Redaktion, Rendering, Statistiken, Import und
   Export), verlinkt von der Funktionsseite.
7. Dann die Module der Reihe nach: zuerst die Übersicht jedes Moduls (seine Vorbereitungsliste und sein Zeitplan),
   dann seine Lektionen. Proben Sie jede Übung auf Ihrer Schulungsinstallation und sehen Sie nach jeder Übung in den
   Postausgang.

## Vorgeschlagene Zeitpläne

Die Lektionszeiten ergeben zusammen 16 Stunden; die Zeitpläne fügen Pausen hinzu.

### Zweieinhalb Tage, der ganze Kurs

| Tag | Vormittag | Nachmittag |
|---|---|---|
| 1 | Modul 1, Lektionen 2.1 und 2.2 | Lektionen 2.3 und 2.4, Lektionen 3.1 und 3.2 |
| 2 | Lektionen 3.3 und 3.4, Lektionen 4.1 und 4.2 | Lektionen 4.3 und 4.4, Lektion 5.1 |
| 3 | Lektionen 5.2 und 5.3, Modul 6 | |

### Anderthalb Tage für Redakteure

Modul 1 (Lektion 1.2 als Vorführung durch den Trainer), Modul 2, Modul 3 ohne Lektion 3.3, die Lektionen 4.1, 4.2
und 4.4 sowie Lektion 6.1: etwa 11 Stunden 15 Minuten Lektionen. Administratoren kommen an einem späteren halben Tag
für die Lektionen 3.3 und 4.3 und Modul 5 dazu.

### Ein halber Tag für Administratoren, die die redaktionelle Seite schon kennen

Lektionen 1.2, 4.3, 5.1, 5.2 und 5.3: 4 Stunden Lektionen.

## Teilnehmende beurteilen

- Das Quiz und das erwartete Ergebnis jeder Lektion prüfen das Verständnis während des Kurses.
- Eine gute praktische Abschlussprüfung: Jede Person durchläuft allein den ganzen Zyklus auf einer frischen
  Schulungsliste (Liste, zwei Abonnenten über das Double-Opt-in, eine Ausgabe, ein Test, ein Versand, ein Abbruch) und
  zeigt den Postausgang.
- Der Prüfungskurs (Prüfungen und praktische Übungen, in diesem Bereich der Hilfe) enthält einen schriftlichen Test
  und eine praktische Aufgabe für diesen Kurs. Alle Prüfungen sind eigenes Material des Trainers, keine offizielle
  Zertifizierung ([Zertifizierungen](../../partner/zertifizierungen.md) sagt, was das Partnerprogramm veröffentlicht).

## Verwandte Seiten

- [Trainer: hier beginnen](../../../../eng-us/help/trainers/trainers-start-here.md)
- [Kurskatalog](../../../../eng-us/help/trainers/course-catalogue.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
