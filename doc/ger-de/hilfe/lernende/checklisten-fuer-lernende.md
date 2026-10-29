---
title: Checklisten für Lernende
audience: student
category: learning
order: 53
summary: Checklisten für Lernende - vor Ihrer ersten Sitzung, eine einsatzbereite Lernwebsite, jede Lernsitzung, vor der Abgabe eines Projekts, vor einem Pull Request, vor einer Präsentation und bevor Sie die Partnerstufe Student kaufen -, jeder Punkt verlinkt auf die Seite, die ihn erklärt.
keywords: checkliste, checklisten, lernende, lernwebsite, sitzung, abgabe, pull request, präsentation, sicherheit, datenschutz, stufe student, checklist, checklists, student, study site, session, hand in, presentation, security, privacy, Student level, learner
type: help
---
# Checklisten für Lernende

Kopieren Sie die Liste, die Sie brauchen, in Ihre Notizen und haken Sie sie ab.

## Inhalt

1. [Vor Ihrer ersten Sitzung](#vor-ihrer-ersten-sitzung)
2. [Eine einsatzbereite Lernwebsite](#eine-einsatzbereite-lernwebsite)
3. [Jede Lernsitzung](#jede-lernsitzung)
4. [Bevor Sie ein Projekt abgeben](#bevor-sie-ein-projekt-abgeben)
5. [Vor einem Pull Request](#vor-einem-pull-request)
6. [Vor einer Präsentation](#vor-einer-präsentation)
7. [Bevor Sie die Stufe Student kaufen](#bevor-sie-die-stufe-student-kaufen)

## Vor Ihrer ersten Sitzung

- [ ] Ich habe einen Lernpfad gewählt ([Einen Lernpfad wählen](einen-lernpfad-waehlen.md)).
- [ ] Ich habe ein Lerntagebuch begonnen ([Ein Lerntagebuch führen](ein-lerntagebuch-fuehren.md)).
- [ ] Ich habe PHP 8.0 oder neuer (PHP 7.4 ohne Support nutzbar) und Composer, oder ich beginne mit der öffentlichen Demo
      ([Ihre erste Website auf drei Wegen](ihre-erste-website-auf-drei-wegen.md)).
- [ ] Ich habe einen Passwortmanager für die Passwörter, die der Installer ausgibt.

## Eine einsatzbereite Lernwebsite

- [ ] `php bin/php/console --version` funktioniert, und das Release steht in meinem Tagebuch.
- [ ] Die Website antwortet unter ihrer Adresse, und ich habe mich an der Administrationsoberfläche angemeldet.
- [ ] Ich habe das Administratorpasswort geändert und `var/log/initial-admin-password` gelöscht
      ([Eine Lernwebsite mit einem Befehl installieren](eine-lernwebsite-mit-einem-befehl-installieren.md#anmelden-und-das-passwort-ändern)).
- [ ] Testkonten verwenden Adressen unter `example.invalid`.
- [ ] Ich weiß, wie ich sie sichere und neu aufbaue ([Die Lernwebsite zurücksetzen und neu aufbauen](die-lernwebsite-zuruecksetzen-und-neu-aufbauen.md)).
- [ ] Sie liegt auf meinem eigenen Rechner oder einer Schulungsmaschine, niemals auf einer Live-Website.

## Jede Lernsitzung

- [ ] Ein Ziel, aufgeschrieben, bevor ich beginne.
- [ ] Einen Abschnitt lesen, durcharbeiten, mit „Erwartet“ vergleichen ([Mit der Hilfe lernen](mit-der-hilfe-lernen.md#der-kreislauf-aus-lesen-tun-und-prüfen)).
- [ ] Wenn etwas abweicht: Log, Cache, Siteaccess ([Logs und Fehler lesen](logs-und-fehler-lesen.md#ein-ablauf-für-jeden-fehler)).
- [ ] Tagebucheintrag geschrieben; Arbeit committet; nichts Geheimes committet.

## Bevor Sie ein Projekt abgeben

- [ ] Jede Aufgabe erledigt, oder der Bericht sagt, warum nicht.
- [ ] Jede Projektprüfung mit einem Nachweis beantwortet ([Die eigene Arbeit bewerten](die-eigene-arbeit-bewerten.md#projektprüfungen)).
- [ ] Keine Kerndatei bearbeitet; nur Overrides, Dateien `.ini.append.php` und Erweiterungen.
- [ ] Jeder in Templates ausgegebene Wert maskiert.
- [ ] Keine Passwörter, keine Schlüssel, kein `settings/override/` und kein `kickstart.ini` im Repository oder auf
      Screenshots.
- [ ] Nur erfundene Inhalte; keine personenbezogenen Daten.
- [ ] Das Release genannt; die verwendeten Seiten angegeben.
- [ ] Selbstbegutachtung einen Tag später durchgeführt ([Die eigene Arbeit bewerten](die-eigene-arbeit-bewerten.md#selbstbegutachtung)).

## Vor einem Pull Request

- [ ] Die Aufgabe ist klein und echt ([Ihr erster Beitrag, Schritt für Schritt](ihr-erster-beitrag-schritt-fuer-schritt.md)).
- [ ] Ich habe den Tracker nach einem vorhandenen Issue durchsucht.
- [ ] Die Arbeit liegt auf einem Branch; die Commit-Betreffzeile beginnt mit `Added:`, `Updated:`, `Fixed:`,
      `Removed:` oder `Renamed:`.
- [ ] Für den Kernel: `make hooks` installiert, `share/filelist.md5` aktuell, CLA unterzeichnet
      ([Die CLA für Lernende](die-cla-fuer-lernende.md)).
- [ ] Die Beschreibung sagt, was, warum und wie ich es getestet habe.
- [ ] Ich bin bereit für Begutachtungsrunden und werde auf Nachfrage per Squash zusammenfassen und rebasen.

## Vor einer Präsentation

- [ ] Die Lernwebsite läuft; eine Sicherung liegt bereit, falls nicht.
- [ ] Kein Passwort und kein Schlüssel auf dem Bildschirm; der Browser zeigt keine gespeicherten Anmeldungen oder
      privaten Tabs.
- [ ] Nur Demodaten.
- [ ] Wenn ich eine öffentliche Demo zeige, weiß ich, dass sich ihre Inhalte ändern oder zurückgesetzt werden können
      ([Die öffentliche Demo ausprobieren](die-oeffentliche-demo-ausprobieren.md)).
- [ ] Ich nenne das Release und verwende die richtigen Namen: Exponential, 7x, die Exponential Foundation
      ([Namen und Geschichte für Lernende](namen-und-geschichte-fuer-lernende.md)).

## Bevor Sie die Stufe Student kaufen

- [ ] Ich habe [Die Partnerstufe Student](die-partnerstufe-student.md) und die aktuellen Programmseiten gelesen.
- [ ] Ich habe sie mit dem verglichen, was frei ist ([Stufe Student oder freie Community: was wählen](stufe-student-oder-freie-community.md)).
- [ ] Ich habe das Partnerteam gefragt, was der „Sandbox-Zugang“ umfasst.
- [ ] Ich verstehe die Reihenfolge: bewerben, prüfen lassen, bezahlen, dann unterzeichnen ([Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md)).
- [ ] Ich weiß, dass sie keine Zertifizierung ist und dass offizielle Zertifizierungen geplant, aber noch nicht
      verfügbar sind ([Zertifizierungen und Zertifikate](zertifizierungen-und-zertifikate.md)).

## Verwandte Seiten

- [FAQ für Lernende](faq-fuer-lernende.md)
- [Lesepfad für Lernende](lesepfad-fuer-lernende.md)
- [Werkzeugkasten für Trainer](../trainer/trainer-werkzeugkasten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
