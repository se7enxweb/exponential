---
title: Mythen und Richtigstellungen
audience: press, advocate
category: press-media
order: 176
summary: Zehn Mythen über Exponential, die in Foren, Kommentaren und älteren Artikeln kursieren - dass es tot sei, dass es Ibexa gehöre, dass es altes PHP sei, dass die KI veröffentliche, dass es unsicher sei, weil es offen ist, und mehr - jeweils mit dem, was die Quellen tatsächlich zeigen.
keywords: Mythen, Missverständnisse, Richtigstellungen, ist es tot, lebt es noch, Ibexa, altes PHP, KI, Sicherheit, Open Source, kostenlos, Legacy, Faktencheck, myths, misconceptions, corrections, is it dead, still alive, old PHP, AI, security, open source, free, legacy, fact check
type: help
---
# Mythen und Richtigstellungen

Diese Seite ist für Journalisten, die in Kommentaren, Foren oder älteren Artikeln auf Behauptungen über Exponential
stoßen. Jeder Mythos wird mit dem beantwortet, was die Quellen zeigen. Die Fürsprecher beantworten dieselben Zweifel im
Gespräch auf ihren Seiten zu Einwänden, zum Beispiel [„Lebt es noch?“](../fuersprecher/einwand-lebt-es-noch.md).

## Inhalt

1. [Die zehn Mythen](#die-zehn-mythen)
2. [Warum es die Mythen gibt](#warum-es-die-mythen-gibt)
3. [Diese Seite verwenden](#diese-seite-verwenden)

## Die zehn Mythen

### 1. „Es ist tot; eZ Publish endete 2021.“

Die Produktlinie eZ Publish endete, die Codebasis nicht. 7x veröffentlichte 6.0.0 am 1. Januar 2024 und vierzehn
weitere Releases bis zum 7. Juni 2026; 6.0.15 ist in Entwicklung ([Die 6.0.x-Releases auf einen Blick](die-6-0-x-releases-auf-einen-blick.md)).
Die README: „Exponential has made it beyond it's end of life in 2021 and survived“.

### 2. „Es ist ein Produkt von Ibexa.“

„This project is not associated with the original eZ Publish software or its original developer, eZ Systems or Ibexa“
(README; [Nicht verbunden mit eZ Systems oder Ibexa](nicht-verbunden-mit-ez-systems-oder-ibexa.md)).

### 3. „Es läuft nur auf altem PHP.“

Es unterstützt PHP 8.0 bis 8.5; Websites mit PHP 7.4 bleiben auf 6.0.7 ([Thema: aktuelles PHP](thema-aktuelles-php.md)).

### 4. „Die KI schreibt und veröffentlicht Ihre Website.“

Exponential Intelligence schreibt Entwürfe, als der angemeldete Redakteur mit dessen Rechten, und veröffentlicht nie
von sich aus ([Thema: ein Assistent, der nur Entwürfe schreibt](thema-ein-assistent-der-nur-entwuerfe-schreibt.md)).

### 5. „Open Source bedeutet unsicher.“

Das Projekt hat ein vertrauliches Meldeverfahren mit einer ersten Antwort innerhalb eines Werktags, Korrekturen im
nächsten Release, eine Content Security Policy, einen Audit-Trail und einen Leitfaden zur Härtung
([Thema: Sicherheit](thema-sicherheit.md)). Keine Software ist absolut „sicher“; überprüfen lässt sich das Verfahren.

### 6. „Kostenlos bedeutet ungepflegt.“

Die Entwicklung wird von 7x geleitet und durch dessen Beiträge und Sponsoren finanziert; das Repository zeigt die Arbeit
Commit für Commit ([Zwei Organisationen, ein Projekt](zwei-organisationen-ein-projekt.md),
[Die Geschichtsseiten als Quelle](die-geschichtsseiten-als-quelle.md)).

### 7. „Es braucht einen großen Datenbankserver.“

SQLite ist die Voreinstellung und braucht keinen Server; PostgreSQL, MongoDB, MariaDB, Oracle und MySQL werden
unterstützt ([Thema: SQLite als Voreinstellung](thema-sqlite-als-standard.md)).

### 8. „Legacy bedeutet aufgegeben.“

„Legacy“ bezeichnet Exponential 6, wenn es neben einer Symfony-Plattform läuft; Exponential 6 selbst ist die gepflegte
Produktlinie ([Die Produktfamilie für Reporter](die-produktfamilie-fuer-reporter.md)).

### 9. „Es ist ein neues CMS von 2025.“

Die Codebasis wird seit 1999 entwickelt; der Name stammt vom August 2025 ([Zeitleiste für Reporter](zeitleiste-fuer-reporter.md)).

### 10. „7x gehört es, und 7x kann es schließen.“

7x und die Exponential Foundation halten das Urheberrecht gemeinsam, und der Code steht unter der GPL, die es jedem
erlaubt, ihn zu behalten und weiterzuführen ([Lizenzen für Reporter erklärt](lizenzen-fuer-reporter-erklaert.md)).

## Warum es die Mythen gibt

- **Alte Artikel**: Berichte aus den Jahren von eZ Publish beschreiben ein Produkt und eine Unternehmenslage, die
  beendet sind.
- **Die Umbenennung**: Leser, die den alten Namen kannten, verbinden ihn nicht immer mit Exponential.
- **Ähnliche Namen**: eZ Platform, Ibexa DXP und Exponential Platform sind verschiedene Produkte verschiedener
  Eigentümer.
- **Ängste vor KI**: Allgemeine Sorgen über KI werden auf eine Funktion übertragen, die dafür entworfen wurde, nur
  Entwürfe zu schreiben.

## Diese Seite verwenden

- Beantworten Sie einen Mythos mit der Quelle, nicht mit Adjektiven.
- Wenn Sie einen Mythos in einem Artikel richtigstellen, verlinken Sie die Primärquelle, nicht nur diese Seite.
- Wenn Sie einen Mythos finden, der hier nicht steht, sagen Sie es dem Projekt im
  [Issue-Tracker der Community](https://github.com/se7enxweb/exponential-community/issues).

## Verwandte Seiten

- [Häufige Fehler in der Berichterstattung](haeufige-fehler-in-berichten.md)
- [Einwände als Fürsprecher beantworten](../fuersprecher/einwaende-beantworten.md)
- [FAQ für Reporter](faq-fuer-reporter.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
