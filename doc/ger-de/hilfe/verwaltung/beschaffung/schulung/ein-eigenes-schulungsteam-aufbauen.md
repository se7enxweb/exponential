---
title: Ein eigenes Schulungsteam aufbauen
audience: government, trainer, project-manager
category: public-sector
order: 70
summary: Wie eine öffentliche Stelle mit dem Kurs Train the Trainer eigene Trainerinnen und Trainer für Exponential ausbildet, ein Schulungs-Lab aufbaut und erhält, das freie Kursmaterial anpasst und aktuell hält, damit die Schulung nach dem Ende des Lieferantenvertrags weitergeht.
keywords: interne Trainer, Train the Trainer, Schulungsteam, Lab, Kursmaterial, GFDL, Wissenstransfer, Nachhaltigkeit, öffentlicher Sektor, in-house trainer, train the trainer, training team, lab, course material, knowledge transfer, sustainability, public sector
type: help
---
# Ein eigenes Schulungsteam aufbauen

Diese Seite richtet sich an die Schulungskoordination einer öffentlichen Stelle, die Wissen über Exponential in der
Organisation halten möchte. Eine interne Trainerin oder ein interner Trainer kann neue Beschäftigte jederzeit einarbeiten
und geht nicht, wenn ein Vertrag endet. Die Kurse und ihr Inhalt stammen aus [Train the Trainer](../../../trainer/train-the-trainer/README.md) und
[Trainer: hier beginnen](../../../trainer/trainer-hier-beginnen.md).

## Inhalt

1. [Wann es sich lohnt](#wann-es-sich-lohnt)
2. [Die Trainerinnen und Trainer auswählen](#die-trainerinnen-und-trainer-auswählen)
3. [Der Ausbildungsweg für Trainerinnen und Trainer](#der-ausbildungsweg-für-trainerinnen-und-trainer)
4. [Das Lab](#das-lab)
5. [Das Material anpassen und pflegen](#das-material-anpassen-und-pflegen)
6. [Schritte](#schritte)

## Wann es sich lohnt

| Situation | Interne Trainerinnen und Trainer |
|---|---|
| Viele Redaktionsmitglieder, über Abteilungen verteilt | lohnt sich: Die Einarbeitung ist laufend |
| Regelmäßige Personalfluktuation | lohnt sich |
| Ein kleines Team weniger Redaktionsmitglieder | eine Mentorin oder ein Mentor kann reichen |
| Mehrere Lieferanten über die Jahre | lohnt sich: Das Wissen geht nicht mit dem Lieferanten |

## Die Trainerinnen und Trainer auswählen

Wählen Sie Personen, die das System bereits jede Woche nutzen: eine leitende Redakteurin oder einen leitenden
Redakteur für Redaktionskurse, eine Person aus der Administration der Website für den Administrationskurs. Sie brauchen
Kenntnisse im Browser und im Terminal für Train the Trainer ([Kurskatalog](../../../trainer/kurskatalog.md)) und
Zeit: Trainerinnen und Trainer bereiten vor und proben, sie unterrichten nicht nur.

## Der Ausbildungsweg für Trainerinnen und Trainer

| Schritt | Kurs | Länge |
|---|---|---|
| 1 | [Train the Trainer](../../../trainer/train-the-trainer/README.md): Vorbereitung, Aufbau des Labs, Gestaltung eines Kurses, Durchführung, Barrierefreiheit und Inklusion, Prüfung und Nachbereitung | 34 Lektionen, etwa 21 Stunden |
| 2 | der Fachkurs, den sie unterrichten werden, als Teilnehmende besucht (zum Beispiel [Editors 101](../../../trainer/redakteure-101/README.md)) | siehe Katalog |
| 3 | diesen Kurs der Gruppe zurück unterrichten, wie es der Katalog für neue Trainerinnen und Trainer vorschlägt | |
| 4 | [Prüfungen und praktische Übungen](../../../trainer/pruefungen/README.md), wenn Sie Lernende prüfen | etwa 6 Stunden |

## Das Lab

Eine Schulungsinstallation ist von der Live-Website getrennt. Die [Lab-Kurzkarte](../../../trainer/kurzkarte-fuer-die-uebungsumgebung.md)
führt jeden Befehl auf, um eine zu bauen, auszuliefern, zu prüfen, zurückzusetzen und zu entfernen. Der schnellste
Einstieg ist das Konsolen-Installationsprogramm auf SQLite, das keinen Datenbankserver braucht:

```bash
./console exp:install
```

([Exponential mit einem Befehl installieren](../../../../../eng-us/features/6.0/install-in-one-command.md)). Das generierte
Administratorpasswort wird einmal ausgegeben und nach `var/log/initial-admin-password` geschrieben; ändern Sie es nach
der ersten Anmeldung.

Bauen Sie das Lab auf derselben Version von Exponential wie Ihre Live-Website und setzen Sie es zwischen den Kursen
zurück. Verwenden Sie für Schulungen nie Kopien der Live-Datenbank mit echten personenbezogenen Daten; verwenden Sie
Testinhalte.

## Das Material anpassen und pflegen

- Die Kurse sind freie Dokumentation unter der GNU FDL 1.3 oder neuer. Sie dürfen sie für Ihre Organisation anpassen,
  wenn Sie die Hinweise erhalten und das Ergebnis genauso lizenzieren ([Freie Dokumentation](../../../freie-dokumentation.md)).
- Ergänzen Sie die Übungen um Ihr eigenes Inhaltsmodell, Ihre Sektionsnamen und Ihre redaktionellen Regeln.
- Proben Sie nach jedem Upgrade der Live-Website die Übungen erneut und aktualisieren Sie Screenshots oder Schritte, die
  sich geändert haben ([Übungen schreiben, die funktionieren](../../../trainer/train-the-trainer/modul-03-einen-kurs-gestalten/uebungen-schreiben-die-funktionieren.md)).
- Verwenden Sie die wiederverwendbaren Checklisten und Vorlagen im [Werkzeugkasten für Trainer](../../../trainer/trainer-werkzeugkasten.md).
- Verbesserungen, die allen helfen, können als Beiträge zur Dokumentation an das Projekt zurückgehen
  ([Dokumentation und Übersetzungen beitragen](../../../partner/dokumentation-und-uebersetzungen-beitragen.md)).

## Schritte

1. Benennen Sie eine oder zwei Trainerinnen oder Trainer und vereinbaren Sie ihre Zeit mit ihren Vorgesetzten.
2. Schicken Sie sie durch Train the Trainer, von einem Partner durchgeführt oder im eigenen Tempo.
3. Bauen Sie das Lab und eine Routine zum Zurücksetzen auf.
4. Passen Sie Editors 101 an Ihre Website an; proben Sie den Kurs.
5. Führen Sie einen Pilotkurs mit einigen wohlwollenden Teilnehmenden durch; sammeln Sie Rückmeldungen
   ([Rückmeldungen sammeln](../../../trainer/train-the-trainer/modul-06-pruefung-und-nachbereitung/feedback-sammeln.md)).
6. Nehmen Sie den Kurs in die Routine der Einarbeitung auf (siehe „Einarbeitung neuer Beschäftigter“ in diesem Ordner).
7. Überprüfen Sie das Material nach jedem Upgrade und einmal im Jahr.

## Verwandte Seiten

- [Schulung in der Beschaffung](README.md)
- [Train the Trainer: Kursübersicht](../../../trainer/train-the-trainer/README.md)
- [Trainer: hier beginnen](../../../trainer/trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
