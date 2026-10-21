---
title: "Lektion 4.1: Die erste Stunde gestalten"
audience: trainer
category: training
order: 41
summary: Eröffnen Sie einen Kurs so, dass in der ersten Stunde alle angemeldet, sicher und orientiert sind: Vorstellungsrunde, Prüfung der Übungsumgebung, die erste Anmeldung, das erzeugte Passwort ändern und das Hilfe-Center finden. 60 Minuten.
keywords: erste stunde, kurseröffnung, begrüßung, prüfung der übungsumgebung, erste anmeldung, passwort ändern, initial-admin-password, hilfe-center, orientierung, first hour, course opening, welcome, lab check, first sign-in, change password, help center, orientation
type: help
---
# Lektion 4.1: Die erste Stunde gestalten

Die erste Stunde entscheidet, ob ein Kurs reibungslos läuft. An ihrem Ende sollten alle Teilnehmenden an einer
funktionierenden Installation mit einem eigenen Passwort angemeldet sein und wissen, wo die Hilfe ist. Diese Lektion
ist ein Drehbuch für diese Stunde, das Trainer üben, indem sie es füreinander durchführen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Train the Trainer](../README.md) |
| Modul | [Modul 4: Einen Kurs durchführen](README.md) |
| Dauer | 60 Minuten (15 Präsentation, 45 Übung: Alle Teilnehmenden führen eine kurze Eröffnung durch) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Trainer |
| Voraussetzungen | [Modul 2](../modul-02-die-uebungsumgebung-aufbauen/README.md); [Lektion 1.5](../modul-01-den-unterricht-vorbereiten/namen-und-geschichte-im-unterricht.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Eröffnung durchführen, die Vorstellungsrunde, Ziele, die Prüfung der Übungsumgebung und die erste Anmeldung
  abdeckt;
- alle Teilnehmenden dazu bringen, ein erzeugtes Administratorpasswort zu ändern und die Passwortdatei zu löschen;
- die Schaltfläche „?“ und das Hilfe-Center als ersten Ort zum Nachschauen zeigen;
- jemanden erkennen, dessen Übungsumgebung nicht funktioniert, und diese Person an einen Helfer weiterleiten.

## Hinweise für Trainer

### So präsentieren Sie es

Zeigen Sie das Drehbuch auf einer Folie und lassen Sie die Teilnehmenden dann Teile davon füreinander durchführen
(15 Minuten Präsentation):

| Minute | Was passiert |
|---|---|
| 0-10 | Begrüßung, die zweiminütige Eröffnung (Lektion 1.5), was der Kurs behandelt, Pausen, wie man fragt |
| 10-25 | Prüfung der Übungsumgebung: Alle Teilnehmenden öffnen die Administration ihrer Installation (oder die gemeinsame Website) |
| 25-35 | Erste Anmeldung; das Passwort ändern: Benutzermenü, **Passwort ändern**; `var/log/initial-admin-password` löschen |
| 35-45 | Die Schaltfläche „?“, das Hilfe-Center, die Suche; „hier zuerst nachschauen“ |
| 45-60 | Erste kleine Aufgabe (zum Beispiel **Inhaltsstruktur** öffnen und einen Artikel finden), Fragen |

### Häufige Fragen

**Was, wenn eine Übungsumgebung nicht startet?** Ein Helfer übernimmt; die Person arbeitet mit ihrem Nachbarn
zusammen, bis es funktioniert. Halten Sie den Raum nicht an.

**Warum das Passwort in einer Übungsumgebung ändern?** Es ist die erste Gewohnheit eines sicheren Administrators, und
die Zusammenfassung von exp:install sagt selbst, man solle sich anmelden, es ändern und dann die Datei löschen.

### Stolperfallen

- **Mit den Inhalten beginnen, bevor die Übungsumgebung funktioniert.** Alles danach hängt davon ab.
- **Das erzeugte Passwort auf einem Projektor.** Die Teilnehmenden lesen ihr eigenes in ihrem eigenen Terminal.
- **Die Hilfe überspringen.** Wer das „?“ kennt, stellt später weniger Fragen.

## Praktische Übung

Arbeiten Sie in Dreiergruppen: Trainer, Teilnehmer, Beobachter. Wechseln Sie die Rollen.

1. Der „Trainer“ hält die zweiminütige Eröffnung (Lektion 1.5).
2. Der „Teilnehmer“ öffnet die Administration seiner Schulungsinstallation und meldet sich als `admin` mit dem Passwort
   aus der Zusammenfassung der Installation oder aus `var/log/initial-admin-password` an.
3. Der Teilnehmer öffnet das Benutzermenü (seinen Namen in der Kopfzeile) und wählt **Passwort ändern**. Er füllt
   **Aktuelles Passwort**, **Neues Passwort** und **Neues Passwort bestätigen** aus (oder verwendet
   **Sicheres Passwort erzeugen**) und klickt dann auf **Passwort ändern**.
4. Im Terminal löscht der Teilnehmer die Passwortdatei: `rm var/log/initial-admin-password`.
5. Der Trainer bittet den Teilnehmer, das Hilfe-Center über die Schaltfläche „?“ zu öffnen und nach `veröffentlichen`
   zu suchen.
6. Der Beobachter misst die Zeit jedes Teils und notiert einen Punkt zur Verbesserung.

## Erwartetes Ergebnis

- Alle in der Gruppe sind mit einem eigenen Passwort angemeldet; die Passwortdatei ist gelöscht.
- Alle haben das Hilfe-Center und ein Suchergebnis für `veröffentlichen` gefunden.
- Die Notizen des Beobachters nennen pro Eröffnung eine Verbesserung; die Gesamtzeit der Gruppe liegt unter
  60 Minuten.

## Quiz

1. Was müssen alle Teilnehmenden am Ende der ersten Stunde getan haben?
2. Wohin wird das erzeugte Administratorpasswort geschrieben, und was geschieht mit dieser Datei?
3. Wo ist **Passwort ändern** in der Administration?
4. Die Übungsumgebung einer Teilnehmerin startet nicht. Was tun Sie?

<details>
<summary>Antworten</summary>

1. An einer funktionierenden Installation mit einem eigenen Passwort angemeldet sein und wissen, wo die Hilfe ist.
2. `var/log/initial-admin-password`; löschen Sie sie nach der ersten Anmeldung und der Passwortänderung
   ([Die Installation über die Konsole, 7.5](../../../../installation/07-installation-ueber-die-konsole.md#75-das-passwort-des-administrators)).
3. Im Benutzermenü (dem Namen des Benutzers in der Kopfzeile), Eintrag **Passwort ändern**.
4. Geben Sie ihr einen Helfer und lassen Sie sie mit ihrem Nachbarn zusammenarbeiten; halten Sie den Raum in Bewegung.

</details>

## Weiterführende Literatur

- [Anmelden in der Administration](../../../anmelden-in-der-administration.md)
- [Das Administratorpasswort ändern](../../../administratorpasswort-aendern.md)
- [Erste Schritte](../../../erste-schritte.md)

## Verwandte Seiten

- [Modul 4: Einen Kurs durchführen](README.md)
- [Train the Trainer: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
