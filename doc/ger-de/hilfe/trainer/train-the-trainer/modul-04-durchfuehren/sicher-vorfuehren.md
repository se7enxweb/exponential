---
title: "Lektion 4.2: Sicher vorführen"
audience: trainer
category: training
order: 42
summary: Führen Sie Exponential auf einem Projektor vor, ohne etwas preiszugeben: eine saubere Demo-Installation, erfundene Inhalte, keine Passwörter oder personenbezogenen Daten auf dem Bildschirm, ein vorbereiteter Rechner und ein Plan für den Fall, dass die Vorführung scheitert. 40 Minuten.
keywords: vorführung, demo, live-vorführung, projektor, sicherheit, datenschutz, passwörter, personenbezogene daten, demo-installation, erfundene inhalte, notfallplan, live demonstration, projector, safety, privacy, passwords, personal data, demo installation, backup plan
type: help
---
# Lektion 4.2: Sicher vorführen

Ein Trainer führt den ganzen Tag vor. Jede Vorführung ist eine Gelegenheit, auf einem Projektor ein Passwort, einen
Kundennamen oder eine private Nachricht preiszugeben oder versehentlich eine echte Website zu verändern. Diese Lektion
wendet die Regeln der Fürsprecher für sichere Live-Demos auf den Kursraum an.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Train the Trainer](../README.md) |
| Modul | [Modul 4: Einen Kurs durchführen](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Trainer |
| Voraussetzungen | [Lektion 2.3](../modul-02-die-uebungsumgebung-aufbauen/eine-installation-pro-teilnehmer.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- aufzählen, was niemals auf dem Bildschirm sein darf;
- eine Demo-Installation nur mit erfundenen Inhalten vorbereiten;
- den Rechner vorbereiten (Benachrichtigungen aus, ein Browserprofil, keine sichtbaren gespeicherten Passwörter);
- ruhig reagieren, wenn eine Vorführung schiefgeht.

## Hinweise für Trainer

### So präsentieren Sie es

1. Gehen Sie [Sicherheit bei Live-Demos](../../../fuersprecher/sicherheit-bei-live-demos.md) durch (10 Minuten): was niemals auf
   dem Bildschirm sein darf; eine saubere Demo-Installation vorbereiten; den Rechner vorbereiten; während der
   Vorführung; wenn etwas durchsickert; wenn die Vorführung schiefgeht.
2. Ergänzen Sie den Blick des Kursraums (5 Minuten): Führen Sie auf Ihrer eigenen Schulungsinstallation vor, niemals
   auf der Live-Website eines Kunden oder Ihrer Organisation und niemals mit dem gemeinsamen Administratorkonto einer
   echten Website.

### Häufige Fragen

**Darf ich unser echtes Intranet zeigen, damit es anschaulich wird?** Nur wenn keine personenbezogenen oder
vertraulichen Daten erscheinen können, und nur lesend. Eine Kopie mit erfundenen Inhalten ist besser.

**Was, wenn jemand eine echte Kunden-Website sehen möchte?** Verweisen Sie auf öffentliche Demos und auf Ihre eigene
Installation.

### Stolperfallen

- **Die Zusammenfassung der Installation.** Sie gibt das Administratorpasswort aus; projizieren Sie dieses Terminal
  niemals.
- **Autovervollständigung des Browsers.** Sie zeigt die Logins anderer Konten. Verwenden Sie ein eigenes
  Browserprofil.
- **Debug-Ausgabe.** Sie kann Pfade und Einstellungen zeigen; schalten Sie sie nur ein, wenn Sie sie unterrichten.

## Praktische Übung

1. Lesen Sie [Sicherheit bei Live-Demos](../../../fuersprecher/sicherheit-bei-live-demos.md) vollständig.
2. Bereiten Sie Ihren Vorführbrowser vor: ein neues Browserprofil ohne gespeicherte Logins und ohne Erweiterungen, die
   Sie nicht brauchen.
3. Öffnen Sie Ihre Schulungsinstallation und prüfen Sie, dass ihre Inhalte erfunden sind (Demo-Inhalte oder Ihre
   eigenen ausgedachten Elemente).
4. Prüfen Sie die Benutzerliste auf echte E-Mail-Adressen; ersetzen Sie sie durch Adressen unter `example.invalid`.
5. Schalten Sie Desktop-Benachrichtigungen für die Dauer einer Vorführung aus.
6. Führen Sie einer Partnerin oder einem Partner drei Minuten lang vor: einen Artikel öffnen, bearbeiten, einen
   Entwurf speichern, ihn verwerfen.
7. Die Partnerin oder der Partner notiert alles, was auf dem Bildschirm nicht hätte zu sehen sein sollen.

## Erwartetes Ergebnis

- Ein Browserprofil für Vorführungen und eine Installation nur mit erfundenen Inhalten.
- Eine dreiminütige Vorführung, während der die Partnerin oder der Partner nichts Privates gesehen hat.

## Quiz

1. Nennen Sie drei Dinge, die während einer Vorführung niemals auf dem Bildschirm sein dürfen.
2. Warum sollte das Terminal mit der Zusammenfassung der Installation nicht projiziert werden?
3. Wo sollten Sie vorführen: auf Ihrer Schulungsinstallation oder auf einer Live-Website?
4. Was tun Sie, wenn ein Passwort auf dem Bildschirm erscheint?

<details>
<summary>Antworten</summary>

1. Drei beliebige von: Passwörter, Schlüssel, personenbezogene Daten, Kundennamen, private Nachrichten, echte
   Benutzerlisten ([Sicherheit bei Live-Demos](../../../fuersprecher/sicherheit-bei-live-demos.md)).
2. Es zeigt das erzeugte Administratorpasswort.
3. Auf Ihrer eigenen Schulungsinstallation.
4. Folgen Sie „Wenn etwas durchsickert“: Ändern Sie es sofort nach der Vorführung (oder in einer Pause) und behandeln
   Sie es als offengelegt.

</details>

## Weiterführende Literatur

- [Sicherheit bei Live-Demos](../../../fuersprecher/sicherheit-bei-live-demos.md)
- [Vorführungen als Fürsprecher](../../../fuersprecher/demos-zeigen.md)
- [Demo-Skripte](../../../geschaeft/demo-drehbuecher.md): Vorführungen von 5, 15 und 45 Minuten, Klick für Klick.

**Demomodus für gemeinsame Anmeldungen.** Wenn sich die Teilnehmenden eine veröffentlichte Administratoranmeldung
teilen, betreiben Sie die Installation im Demomodus (`EXP_DEMO_MODE=1`): Das Passwort kann während der Sitzung nicht
geändert oder zurückgesetzt werden. Andere Änderungen macht er nicht rückgängig; installieren Sie danach neu.
[Demomodus-FAQ](../../../demomodus-faq.md).

## Verwandte Seiten

- [Modul 4: Einen Kurs durchführen](README.md)
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
