---
title: "Agenda 2.3: Der Tag für Entwickler"
audience: trainer, developer
category: training
order: 23
summary: Ein eintägiger Workshop für PHP-Entwickler, die neu bei Exponential sind, eine Git-Installation, das Inhaltsmodell, eine Inhaltsklasse, Templates und ein Override, die Konsole und eine erste Erweiterung mit den RAD-Werkzeugen. 6 Stunden Einheiten.
keywords: ein tag, entwickler, workshop-agenda, git, inhaltsklasse, templates, override, konsole, RAD-werkzeuge, erweiterung, one day, developers, workshop agenda, content class, console, RAD tools, extension
type: help
---
# Agenda 2.3: Der Tag für Entwickler

Entwickler lernen am schnellsten, indem sie bauen. Dieser Tag führt von einer Git-Installation zu einer ersten
Erweiterung und berührt die Teile von Exponential, denen ein Entwickler in der ersten Woche eines Projekts begegnet: das
Inhaltsmodell, Templates, Overrides, die Konsole und Erweiterungen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Workshops und Bootcamps](../README.md) |
| Modul | [Modul 2: Eintägige Agenden](README.md) |
| Dauer | 6 Stunden Einheiten (9:00 bis 16:30 mit Pausen und Mittagspause) |
| Niveau | Fortgeschrittene |
| Zielgruppe | PHP-Entwickler, 4 bis 12 Teilnehmende |
| Voraussetzungen | PHP, Git und Composer auf jedem Laptop; ein GitHub-Konto für den optionalen letzten Block |

## Lernziele

Am Ende des Tages können die Teilnehmenden:

- eine Installation aus Git-Klonen auf einem eigenen Branch einrichten;
- eine Inhaltsklasse definieren und damit Inhalte anlegen;
- herausfinden, welches Template eine Seite zeichnet, und es mit einem Override ändern, das sie rückgängig machen
  können;
- die Konsolenbefehle auflisten und eine erste Erweiterung anlegen, aktivieren und sehen.

## Die Agenda

| Zeit | Block | Verwendet | Kontrollpunkt |
|---|---|---|---|
| 9:00 | Begrüßung; Git-Installation (60 Min.) | [Mit Git entwickeln](../../../mit-git-entwickeln.md), [Den Code beschaffen](../../../../installation/03-den-code-beschaffen.md), [Eine Installation pro Person](../../train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/eine-installation-pro-teilnehmer.md) | Website aus einem Klon auf dem Branch `training` ausgeliefert |
| 10:00 | Das Inhaltsmodell (30 Min.) | [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md), [Die Kernkonzepte unterrichten](../../die-kernkonzepte-unterrichten.md) | Objekt, Knoten, Version erklärt |
| 10:30 | Pause (15 Min.) | | |
| 10:45 | Eine Inhaltsklasse „Event“ (60 Min.) | [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md) | Ein Event angelegt |
| 11:45 | Mittagspause (45 Min.) | | |
| 12:30 | Templates, Debug und ein Override (80 Min.) | [Templates und Design](../../../../anleitungen/templates-und-design.md) | Override sichtbar, dann entfernt |
| 13:50 | Pause (10 Min.) | | |
| 14:00 | Die Konsole (30 Min.) | [Die Installation über die Konsole, 7.1](../../../../installation/07-installation-ueber-die-konsole.md#71-die-konsole-im-überblick) | `php bin/php/console list` gelesen |
| 14:30 | Eine erste Erweiterung mit den RAD-Werkzeugen (60 Min.) | [RAD-Werkzeuge](../../../../funktionen/6.0/rad-werkzeuge.md), [Erweiterungen](../../../../anleitungen/extensions.md) | Erweiterung unter **Setup > RAD** gezählt |
| 15:30 | Optional: eine Korrektur der Dokumentation als Pull Request; Abschluss (60 Min.) | [Mit Git entwickeln](../../../mit-git-entwickeln.md) | Ein Link zu einem Pull Request oder ein vorbereiteter Commit |
| 16:30 | Ende | | |

Streichpunkte, in dieser Reihenfolge: der Block zum Pull Request; der Block zur Konsole (in den Block zur Erweiterung
einbauen).

## Hinweise für Trainer

- **Der Klon ist groß** (die ganze Historie). Bereiten Sie Klone in langsamen Netzwerken vorab vor.
- **Overrides**: Verwenden Sie zuerst den Klickpfad des Template-Editors und zeigen Sie dann die Datei, die er
  geschrieben hat (`settings/siteaccess/<siteaccess>/override.ini.append.php`).
- **Erweiterungen**: Führen Sie nach dem Aktivieren `php bin/php/ezpgenerateautoloads.php -e` aus und leeren Sie die
  Caches, wie es die Seite zu den RAD-Werkzeugen sagt.
- **Nach dem Tag** verweisen Sie auf den vollständigen Kurs Entwickler und das Buch [Entwicklung von Erweiterungen](../../../erweiterungsentwicklung/README.md).

## Praktische Übung: das Abschlussprojekt des Tages

1. Legen Sie eine Inhaltsklasse „Event“ mit einem Titel, einem Datum und einem Text an.
2. Legen Sie zwei Events an.
3. Erstellen Sie ein Override, sodass die vollständige Ansicht eines Events eine zusätzliche Zeile zeigt, prüfen Sie es
   und entfernen Sie das Override dann wieder.
4. Erzeugen Sie mit einem RAD-Assistenten eine Erweiterung, aktivieren Sie sie, erzeugen Sie die Autoloads neu, leeren
   Sie die Caches und sehen Sie sie in der Übersicht der Erweiterungspunkte.

## Erwartetes Ergebnis

- Zwei Events existieren; das Override wurde angezeigt und mit einem Klick entfernt.
- Die neue Erweiterung ist unter **Setup > RAD > Übersicht der Erweiterungspunkte** aufgeführt.

## Quiz (am Ende des Tages)

1. Welcher Befehl listet jeden Konsolenbefehl auf?
2. Wohin schreibt der Template-Editor eine Override-Regel?
3. Was müssen Sie nach dem Aktivieren einer neuen Erweiterung mit Klassen ausführen?
4. Warum arbeitet man im Klon auf einem eigenen Branch?

<details>
<summary>Antworten</summary>

1. `php bin/php/console list`.
2. `settings/siteaccess/<siteaccess>/override.ini.append.php` ([Templates und Design](../../../../anleitungen/templates-und-design.md)).
3. `php bin/php/ezpgenerateautoloads.php -e`, dann die Caches leeren ([RAD-Werkzeuge](../../../../funktionen/6.0/rad-werkzeuge.md)).
4. Updates überschreiben nie Ihre eigenen Commits, und Sie können Änderungen als Pull Requests zurückschicken.

</details>

## Weiterführende Informationen

- [Entwicklung von Erweiterungen](../../../erweiterungsentwicklung/README.md)
- [Mit Git entwickeln](../../../mit-git-entwickeln.md)
- [Templates und Design](../../../../anleitungen/templates-und-design.md)

## Verwandte Seiten

- [Modul 2: Eintägige Agenden](README.md)
- [Workshops und Bootcamps: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
