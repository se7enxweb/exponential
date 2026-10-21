---
title: "Lektion 3.6: Online- und Hybridunterricht"
audience: trainer
category: training
order: 36
summary: Führen Sie die Kurse online oder mit einem gemischten Raum durch: Übungsumgebungen, die die Teilnehmenden erreichen, Bildschirmfreigabe ohne Datenlecks, Rückfragen statt Rundgang durch den Raum und kürzere Blöcke. 40 Minuten.
keywords: online-schulung, fernunterricht, hybrid, videokonferenz, bildschirmfreigabe, breakout-räume, rückfrage, zugang zur übungsumgebung, ssh-tunnel, online training, remote, video call, screen sharing, breakout rooms, check-in, lab access, ssh tunnel
type: help
---
# Lektion 3.6: Online- und Hybridunterricht

Online-Kurse verlieren das beste Werkzeug des Trainers: durch den Raum zu gehen und zu sehen, wer festhängt. Diese
Lektion ersetzt es durch ausdrückliche Rückfragen und behandelt die technische Seite: wie die Teilnehmenden eine
Übungsumgebung erreichen und wie man einen Bildschirm freigibt, ohne zu zeigen, was nicht zu sehen sein darf.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Train the Trainer](../README.md) |
| Modul | [Modul 3: Einen Kurs gestalten](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Trainer, die online unterrichten |
| Voraussetzungen | [Lektion 2.4](../modul-02-die-uebungsumgebung-aufbauen/die-websites-der-uebungsumgebung-bereitstellen.md), [Lektion 2.7](../modul-02-die-uebungsumgebung-aufbauen/gemeinsamer-server-oder-einer-pro-teilnehmer.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- einen Aufbau der Übungsumgebung für einen Online-Kurs wählen (Installationen auf den Rechnern der Teilnehmenden oder
  eine gemeinsame Website);
- nach jedem wichtigen Übungsschritt eine Rückfrage durchführen;
- einen Bildschirm freigeben, ohne Passwörter, personenbezogene Daten oder andere Fenster preiszugeben;
- Blöcke auf 45 Minuten mit Pausen kürzen.

## Hinweise für Trainer

### So präsentieren Sie es

1. Zugang zur Übungsumgebung (5 Minuten): Eine Installation auf dem Rechner jeder Teilnehmerin und jedes Teilnehmers,
   unter `127.0.0.1` bereitgestellt, ist nur für diese Person erreichbar; das ist gut für die Privatsphäre, und der
   Trainer kann sie nicht sehen. Eine gemeinsame Website braucht einen richtigen Webserver mit HTTPS (siehe
   [Die Website bereitstellen](../../../../installation/08-die-website-ausliefern.md)); die Engine `php` von Velocity ist nur für die
   Entwicklung da und sollte nicht offen erreichbar sein.
2. Rückfragen (5 Minuten): Bitten Sie nach jedem wichtigen Schritt alle, im Chat mit „fertig“ oder „hänge fest“ zu
   reagieren. Gehen Sie nie weiter, solange jemand festhängt, ohne dass ein Helfer zugeteilt ist.
3. Bildschirmfreigabe (5 Minuten): Geben Sie ein Fenster frei, nicht den Desktop; schließen Sie E-Mail und Chat;
   verwenden Sie eine Schulungsinstallation mit erfundenen Inhalten
   ([Sicherheit bei Live-Demos](../../../fuersprecher/sicherheit-bei-live-demos.md)).

### Häufige Fragen

**Aufzeichnung?** Nur mit der Zustimmung aller Aufgezeichneten und ohne die Bildschirme der Teilnehmenden, es sei
denn, sie stimmen zu.

**Breakout-Räume?** Gut für Partnerarbeit. Geben Sie jedem Paar eine schriftliche Aufgabe und eine Zeitgrenze.

### Stolperfallen

- **Erzeugte Passwörter auf dem Bildschirm.** Die Zusammenfassung der Installation zeigt das Administratorpasswort;
  geben Sie dieses Terminal nicht frei.
- **Stille bedeutet nichts.** Bitten Sie um ein ausdrückliches „fertig“.
- **Lange Blöcke.** Online ist die Aufmerksamkeit kürzer; halten Sie Blöcke bei etwa 45 Minuten.

## Praktische Übung

1. Nehmen Sie eine Lektion aus Modul 2 und schreiben Sie ihren Zeitplan für den Online-Unterricht um: Blöcke von
   45 Minuten, Rückfragen nach den wichtigen Schritten.
2. Schreiben Sie die drei Rückfragen auf, die Sie stellen werden (zum Beispiel „Sehen Sie die Zeile ‚Dry-run
   completed‘?“).
3. Bereiten Sie Ihren Bildschirm vor: ein Browserfenster nur mit der Schulungs-Website und dem Hilfe-Center; ein
   Terminal ohne Passwörter in seinem Verlauf.
4. Führen Sie die ersten fünf Minuten der Lektion mit einer Partnerin oder einem Partner über eine Videokonferenz durch
   und geben Sie ein Fenster frei.
5. Fragen Sie diese Person, was sie gesehen hat, das nicht hätte sichtbar sein sollen.

## Erwartetes Ergebnis

- Eine Online-Fassung einer Lektion mit Rückfragen und Blöcken von 45 Minuten.
- Die Partnerin oder der Partner hat kein Passwort, keine personenbezogenen Daten und kein anderes Fenster gesehen.

## Quiz

1. Warum ist eine Website der Übungsumgebung, die unter `127.0.0.1` bereitgestellt wird, nur für die Teilnehmerin oder
   den Teilnehmer erreichbar?
2. Welche Velocity-Engine darf für eine gemeinsame Online-Übungsumgebung nicht ins Internet gestellt werden?
3. Was ersetzt online den Rundgang durch den Raum?
4. Was geben Sie frei: den Desktop oder ein Fenster?

<details>
<summary>Antworten</summary>

1. Sie ist an den lokalen Rechner gebunden und nur von dort erreichbar.
2. Die Engine `php` (der eingebaute Server von PHP), die nur für die Entwicklung da ist.
3. Ausdrückliche Rückfragen („fertig“ oder „hänge fest“) nach wichtigen Schritten.
4. Ein Fenster.

</details>

## Weiterführende Literatur

- [Sicherheit bei Live-Demos](../../../fuersprecher/sicherheit-bei-live-demos.md)
- [Ein Online-Meetup durchführen](../../../fuersprecher/ein-online-treffen-durchfuehren.md)
- [Die Website bereitstellen](../../../../installation/08-die-website-ausliefern.md)

## Verwandte Seiten

- [Modul 3: Einen Kurs gestalten](README.md)
- [Train the Trainer: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
