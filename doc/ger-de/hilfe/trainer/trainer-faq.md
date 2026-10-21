---
title: "Trainer-FAQ"
audience: trainer
category: training
order: 8
summary: Kurze Antworten auf die Fragen, die Trainer am häufigsten stellen, zu Zertifizierung, Lizenz, Versionen, Übungsumgebungen, Konten, E-Mail, Sprachen, Barrierefreiheit und dem Aktualisieren des Materials, jeweils mit der Seite, die es erklärt.
keywords: faq, trainerfragen, zertifizierung, lizenz, version, übungsumgebung, sqlite, konten, e-mail, sprache, barrierefreiheit, aktualisierungen, auf github bearbeiten, trainer questions, certification, license, lab, accounts, accessibility, edit on github
type: help
---
# Trainer-FAQ

Kurze Antworten, jeweils mit einem Link auf die ausführliche Erklärung.

## Zum Material

**Gibt es eine offizielle Exponential-Zertifizierung?**
Das Exponential-Partnerprogramm nennt Zertifizierungen für Partnerteams (Developer, Design, Solution, Sales), aber
deren Lehrplan, Prüfungsformat und Prüfer sind nicht veröffentlicht ([Zertifizierungen](../partner/zertifizierungen.md)).
Die Kurse hier sind nicht diese Zertifizierungen. Prüfungen und Zertifikate, die Sie ausstellen, sind Ihre eigenen;
sagen Sie das auch darauf
([Teilnahmebescheinigungen in Ihrem eigenen Namen](../../../eng-us/help/trainers/train-the-trainer/module-06-assessment-and-follow-up/certificates-of-attendance.md)).

**Darf ich diese Seiten in einem kostenpflichtigen Kurs verwenden?**
Ja. Die Dokumentation ist freie Dokumentation unter der GNU FDL 1.3 oder später; Kopien dürfen kostenlos oder gegen
eine Gebühr weitergegeben werden, zu den Bedingungen der Lizenz ([Freie Dokumentation](../freie-dokumentation.md)).

**Darf ich die Lektionen ändern?**
Ja, und Ihre Fassung unter derselben Lizenz teilen, als geändert gekennzeichnet und mit einem eigenen Titel
([Lektion 1.4](train-the-trainer/modul-01-den-unterricht-vorbereiten/ihr-material-und-die-gfdl.md)).

**Eine Lektion ist für die aktuelle Version falsch. Was nun?**
Korrigieren Sie sie mit **Auf GitHub bearbeiten**, oder melden Sie es auf der Seite **Fehler melden** des Hilfe-Centers
([Lektion 3.5](train-the-trainer/modul-03-einen-kurs-gestalten/diese-kurse-anpassen-und-verbessern.md)).

## Zur Übungsumgebung

**Welche Datenbank für eine Schulungsumgebung?**
SQLite: kein Server, kein Passwort, eine Datei pro Installation; es ist die Voreinstellung von `exp:install`
([Lektion 2.3](train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/eine-installation-pro-teilnehmer.md)).

**Brauchen die Teilnehmenden einen Webserver?**
Nein. `php bin/php/console exp:velocity start` stellt die Website standardmäßig mit dem eingebauten Server von PHP
bereit ([Lektion 2.4](train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/die-websites-der-uebungsumgebung-bereitstellen.md)).

**Welches Release soll der Kurs verwenden?**
Ein festes Release für alle, nämlich das, mit dem Sie geprobt haben
([Lektion 2.2](train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/den-code-fuer-einen-kurs-beschaffen.md)).

**Wie setze ich eine Übungsumgebung zwischen zwei Gruppen zurück?**
Stellen Sie eine Referenzkopie von `var/storage` wieder her, oder installieren Sie nach einem Probelauf mit `--force`
neu ([Lektion 2.6](train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/die-uebungsumgebung-zuruecksetzen.md)).

**Gesperrte Laptops, keine Installationen erlaubt?**
Verwenden Sie eine gemeinsame Schulungs-Website und beschränken Sie die Rolle aller Teilnehmenden auf ihren eigenen
Ordner ([Lektion 2.7](train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/gemeinsamer-server-oder-einer-pro-teilnehmer.md)).

## Zu Konten und Daten

**Kann sich der Kurs ein Administratorkonto teilen?**
Nein. Ein Konto pro Person
([Lektion 2.5](train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/konten-und-rollen-der-teilnehmenden.md)).

**Welche E-Mail-Adressen für Testbenutzer?**
Adressen unter `example.invalid`, die niemals E-Mails empfangen können.

**Darf ich unsere echten Inhalte verwenden, damit es anschaulich wird?**
Keine echten Kunden- oder personenbezogenen Daten, auch nicht vorübergehend. Verwenden Sie erfundene Inhalte
([Lektion 4.2](../../../eng-us/help/trainers/train-the-trainer/module-04-delivering/demonstrating-safely.md)).

**Eine Teilnehmerin oder ein Teilnehmer hat ein Sicherheitsproblem gefunden.**
Es wird vertraulich gemeldet, wie `SECURITY.md` es beschreibt, niemals in einem öffentlichen Issue
([Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)).

## Zum Unterrichten

**Meine Teilnehmenden sprechen eine andere Sprache.**
Stellen Sie die **Sprache der Oberfläche** der Administration um und prüfen Sie ihren Vollständigkeitswert
([Lektion 5.3](../../../eng-us/help/trainers/train-the-trainer/module-05-accessibility-and-inclusion/languages-and-right-to-left-in-class.md)).

**Wie mache ich den Kurs barrierefrei?**
Barrierefreie Handouts, die Tastatur- und Anzeigefunktionen des Designs Admin 4 und Nachteilsausgleiche, die im Voraus
geplant sind ([Modul 5](../../../eng-us/help/trainers/train-the-trainer/module-05-accessibility-and-inclusion/README.md)).

**Wie lange dauert jeder Kurs?**
Siehe den [Kurskatalog](kurskatalog.md); jede Modulseite hat einen Zeitplan.

**Wo fragen die Teilnehmenden nach dem Kurs?**
In den Kanälen der Community: Foren, Chat und Issue-Tracker
([Die Kanäle der Community](../fuersprecher/community-kanaele.md)).

## Verwandte Seiten

- [Trainer: hier beginnen](trainer-hier-beginnen.md)
- [Trainer-Werkzeugkasten](trainer-werkzeugkasten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
