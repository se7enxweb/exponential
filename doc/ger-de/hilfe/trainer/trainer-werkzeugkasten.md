---
title: "Trainer-Werkzeugkasten"
audience: trainer
category: training
order: 7
summary: Die Checklisten und Vorlagen, die ein Trainer für jeden Kurs wiederverwendet, vorher, am Kurstag und danach, ein Datenblatt der Übungsumgebung, eine Vorbereitungsnachricht an die Teilnehmenden, ein Handout-Gerüst, eine Fragentafel, ein Feedbackzettel und eine Nachfassnachricht.
keywords: werkzeugkasten, checklisten, vorlagen, vor dem kurs, kurstag, nach dem kurs, datenblatt, handout, feedbackzettel, nachfassen, trainer-ressourcen, toolkit, checklists, templates, feedback slip, follow-up
type: help
---
# Trainer-Werkzeugkasten

Jeder Kurs braucht dieselbe kleine Sammlung von Dokumenten. Diese Seite fasst sie als Checklisten und Vorlagen zum
Kopieren zusammen. Jede stammt aus einer Lektion von [Train the Trainer](train-the-trainer/README.md), die die Gründe
erklärt.

## Checkliste: zwei Wochen vorher

- [ ] Kurs und Module für die Gruppe gewählt ([Lektion 1.3](train-the-trainer/modul-01-den-unterricht-vorbereiten/ihre-teilnehmenden-kennen.md))
- [ ] Umfrage vor dem Kurs verschickt, einschließlich der Frage nach Vorkehrungen ([Lektion 5.4](train-the-trainer/modul-05-barrierefreiheit-und-inklusion/nachteilsausgleich-und-inklusive-praxis.md))
- [ ] Release gewählt; Aufbau der Übungsumgebung gewählt: eine Installation pro Teilnehmer oder eine gemeinsame Website ([Lektion 2.7](train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/gemeinsamer-server-oder-einer-pro-teilnehmer.md))
- [ ] Jede Übung mit diesem Release geprobt ([Lektion 3.4](train-the-trainer/modul-03-einen-kurs-gestalten/uebungen-schreiben-die-funktionieren.md))
- [ ] Rechneranforderungen an die Teilnehmenden geschickt ([Lektion 2.1](train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/anforderungen-an-einen-schulungsrechner.md))
- [ ] Handouts barrierefrei und korrekt lizenziert ([Lektion 5.1](train-the-trainer/modul-05-barrierefreiheit-und-inklusion/barrierefreie-kursmaterialien.md), [Lektion 1.4](train-the-trainer/modul-01-den-unterricht-vorbereiten/ihr-material-und-die-gfdl.md))

## Checkliste: am Tag vorher

- [ ] Übungsumgebungen installiert oder die Installation auf jedem Rechnertyp getestet
- [ ] Konten der Teilnehmenden mit Adressen unter `example.invalid` angelegt ([Lektion 2.5](train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/konten-und-rollen-der-teilnehmenden.md))
- [ ] Browserprofil für die Vorführung ohne gespeicherte Anmeldungen; Benachrichtigungen aus ([Lektion 4.2](train-the-trainer/modul-04-durchfuehren/sicher-vorfuehren.md))
- [ ] Kurzkarte der Übungsumgebung für die Helfer gedruckt ([Kurzkarte für die Übungsumgebung](kurzkarte-fuer-die-uebungsumgebung.md))
- [ ] Fragentafel bereit

## Checkliste: am Kurstag

- [ ] Erste Stunde: Alle sind angemeldet, haben ein eigenes Passwort und haben das Hilfe-Center gefunden ([Lektion 4.1](train-the-trainer/modul-04-durchfuehren/die-erste-stunde-gestalten.md))
- [ ] Mindestens jede Stunde eine Pause; Puffer nach Installationen ([Lektion 3.3](train-the-trainer/modul-03-einen-kurs-gestalten/zeitplanung-und-tempo.md))
- [ ] Erwartete Ergebnisse auf jedem Bildschirm geprüft ([Lektion 6.1](train-the-trainer/modul-06-pruefung-und-nachbereitung/verstaendnis-im-kurs-pruefen.md))
- [ ] Feedbackzettel am Ende eingesammelt ([Lektion 6.3](train-the-trainer/modul-06-pruefung-und-nachbereitung/feedback-sammeln.md))

## Checkliste: nach dem Kurs

- [ ] Nachfassnachricht mit belegten Antworten verschickt ([Lektion 6.5](train-the-trainer/modul-06-pruefung-und-nachbereitung/nach-dem-kurs.md))
- [ ] Server gestoppt, Konten der Teilnehmenden entfernt, Verzeichnisse der Übungsumgebung gelöscht
- [ ] Umfrageantworten zu Vorkehrungen gelöscht
- [ ] Eine Kursänderung und eine Korrektur oder Meldung für die Hilfe aus dem Feedback

## Vorlage: Datenblatt der Übungsumgebung

```text
COURSE: <name>                          DATES: <dates>               TRAINER: <name>
Release:            v6.0.<n>            (git describe --tags)
Site package:       sevenx_multisite | sevenx_multisite_clean
Lab design:         one installation per learner | shared site <address>
Install command:    php bin/php/console exp:install --apply --url=http://127.0.0.1:8087 ...
Serve command:      php bin/php/console exp:velocity start
Learner accounts:   learner01 .. learnerNN @example.invalid, group <group>, role <role>
Reset routine:      <golden copy | reinstall>, takes <minutes>
Helpers:            <names>, one per 8 to 10 learners
```

## Vorlage: Vorbereitungsnachricht an die Teilnehmenden

```text
Subject: Preparing for "<course>" on <date>

Hello,

To start on time, please check before the course:
  1. PHP 8.0 or later (PHP 7.4 usable without support):   php -v
  2. These PHP extensions are listed by php -m: intl, xsl, mbstring, iconv, dom, zlib, sqlite3, gd
  3. Composer and Git:   composer --version ; git --version
If anything is missing, reply to this message and we will help, or use a prepared machine.

Do you need any arrangements to take part (materials in another format, breaks, captions)?
You do not need to say why.

The course follows the Exponential help. You can look at it in advance in any installation:
the "?" in the header, then "Open the Help center".
```

## Vorlage: Handout-Gerüst

```text
<COURSE NAME>: <MODULE NAME>                                          <date>, <place>

Today you will be able to:      <the module's objectives>
Exercises:                      <lesson> -> <help page title>
Commands:                       <only those this module uses>
Help:                           the "?" in the header; Help center: /exphelp/dashboard
Ask:                            <your contact>; share.exponential.earth/forums
Report a bug:                   Help > Report an error (never passwords or personal data)

Based on the Exponential documentation, free documentation under the GNU FDL 1.3 or later.
<your changes and your name, if you adapted pages>
```

## Vorlage: Fragentafel

| # | Frage (nur Vorname) | Zurückgestellt um | Antwort mit Quelle | Verschickt |
|---|---|---|---|---|
| 1 | | | | |

## Vorlage: Feedbackzettel

```text
1. What worked well today?
2. What was hard?
3. What would you like to learn next?
(No name needed.)
```

## Verwandte Seiten

- [Trainer: hier beginnen](trainer-hier-beginnen.md)
- [Kurzkarte für die Übungsumgebung](kurzkarte-fuer-die-uebungsumgebung.md)
- [Train the Trainer: Kursüberblick](train-the-trainer/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->

## Urheberrecht und Copyleft

Seit dem 29. Oktober 2026 halten die Exponential Foundation und 7x das Copyright an Exponential gemeinsam, die
Foundation zuerst genannt: „Exponential Copyright © 1998-2026 Exponential Foundation and 7x“, in Dateien
„Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.“ Wer es hält und warum, was die
GPL Ihnen erlaubt, welche Hinweise Dritter bleiben und wie der Hinweis in neue Dateien und Erweiterungen kommt:
[Urheberrecht und Copyleft](../urheberrecht-und-copyleft.md).
