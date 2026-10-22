---
title: Mit der Hilfe lernen
audience: student
category: learning
order: 2
summary: Lerngewohnheiten, die mit der Exponential-Hilfe funktionieren - wie die Hilfe aufgebaut ist, der Kreislauf aus Lesen, Tun und Prüfen, eine eigene Lerninstallation, Notizen und ein Lerntagebuch, Wiederholen mit den Quizzen der Trainer und was zu tun ist, wenn eine Seite und die Software nicht übereinstimmen.
keywords: lernen, lerngewohnheiten, hilfe-center, lesen, üben, notizen, wiederholen, quiz, verteilte wiederholung, wie man lernt, selbststudium, study, study habits, learning, help center, reading, practice, notes, review, spaced review, how to learn, self study
type: help
---
# Mit der Hilfe lernen

Diese Seite ist für alle, die Exponential selbstständig oder begleitend zu einem Kurs lernen: Studierende,
Auszubildende, Quereinsteiger und neue Teammitglieder. Sie erklärt, wie die Hilfe aufgebaut ist, und gibt Ihnen eine
Lernmethode, die Lesen in Können verwandelt.

## Inhalt

1. [Wie die Hilfe aufgebaut ist](#wie-die-hilfe-aufgebaut-ist)
2. [Der Kreislauf aus Lesen, Tun und Prüfen](#der-kreislauf-aus-lesen-tun-und-prüfen)
3. [Auf einer eigenen Installation lernen](#auf-einer-eigenen-installation-lernen)
4. [Notizen machen, die Sie wieder verwenden](#notizen-machen-die-sie-wieder-verwenden)
5. [Mit Quizzen wiederholen](#mit-quizzen-wiederholen)
6. [Wenn eine Seite und die Software nicht übereinstimmen](#wenn-eine-seite-und-die-software-nicht-übereinstimmen)
7. [Ein Wochenrhythmus](#ein-wochenrhythmus)

## Wie die Hilfe aufgebaut ist

Die Hilfe ist eine Sammlung von Markdown-Seiten im Ordner `doc/` des Kernels, angezeigt in der
Administrationsoberfläche unter **Hilfe**. Ihr Dashboard listet jede Seite nach Zielgruppe und Thema, und ihre Suche
findet jede Seite ([Erste Schritte](../erste-schritte.md#hilfe-bekommen)).

| Art der Seite | Was sie Ihnen gibt | Beispiel |
|---|---|---|
| Startseiten | Eine Übersicht über die Seiten einer Zielgruppe | [Trainer: hier beginnen](../trainer/trainer-hier-beginnen.md) |
| Aufgabenseiten | Schritte für eine Aufgabe, mit dem, was Sie sehen sollten | [Der Layouts-Editor](../layout-editor.md) |
| Seiten „Wie mache ich …“ | Eine kurze Antwort pro Frage | [Handbuch](../handbuch/README.md) |
| Anleitungen | Längere Schritt-für-Schritt-Anleitungen für Administratoren und Entwickler | [Anleitungen](../../anleitungen/README.md) |
| Kurse | Lektionen mit Lernzielen, Übungen und Quizzen, für Trainer geschrieben | [Kurskatalog](../trainer/kurskatalog.md) |
| Referenz | Einstellungen, Template-Sprache, Glossar | [Template-Sprache](../template-sprache/README.md), [Glossar](../../glossar.md) |

Die Seiten dieses Ordners führen Sie in der richtigen Reihenfolge zu den richtigen Seiten. Sie müssen die Hilfe nicht
von vorn bis hinten lesen.

## Der Kreislauf aus Lesen, Tun und Prüfen

Die meisten Aufgabenseiten und Anleitungen zeigen einen Befehl oder einen Klickpfad und sagen dann, was Sie sehen
sollten („Erwartet: …“). Nutzen Sie das als Lernkreislauf:

1. **Lesen** Sie einen Abschnitt, nicht eine ganze Seite.
2. **Tun** Sie es auf Ihrer Lerninstallation, und tippen Sie die Befehle selbst, statt sie einzufügen.
3. **Prüfen** Sie das Ergebnis anhand der Seite. Wenn es übereinstimmt, schreiben Sie eine Zeile in Ihr Tagebuch.
   Wenn nicht, finden Sie heraus, warum, bevor Sie weitermachen: Dort lernen Sie am meisten.

Ein Beispiel aus [der Installationsanleitung](../../anleitungen/erste-schritte.md#2-die-dateien-beschaffen): Nach
`php bin/php/console --version` erwartet die Anleitung als erste Zeile `console (Exponential) 6.0.15stable` oder die
Version, die Sie installiert haben. Wenn Sie etwas anderes sehen, haben Sie bereits eine Frage, die sich zu stellen
lohnt.

## Auf einer eigenen Installation lernen

Über ein Content-Management-System zu lesen, ohne eines zu haben, ist wie über das Schwimmen zu lesen. Installieren
Sie eine Website, die nur Ihnen gehört, auf der Sie Dinge kaputt machen und neu anfangen können:

- [Ihre erste Website auf drei Wegen](ihre-erste-website-auf-drei-wegen.md) vergleicht die öffentliche Demo, die Installation
  mit einem Befehl und den Setup-Assistenten.
- [Die Lernwebsite zurücksetzen und neu aufbauen](die-lernwebsite-zuruecksetzen-und-neu-aufbauen.md) zeigt, wie Sie neu
  anfangen.

Üben Sie niemals auf einer Live-Website und niemals auf einer Website mit Daten anderer Menschen.

## Notizen machen, die Sie wieder verwenden

- Führen Sie ein **Lerntagebuch** ([Ein Lerntagebuch führen](ein-lerntagebuch-fuehren.md)): das Datum, was Sie getan
  haben, was Sie erwartet haben, was passiert ist und welche Seite Sie verwendet haben.
- Schreiben Sie die **Befehle, die funktioniert haben**, auf einen persönlichen Spickzettel, mit einer Zeile dazu, was
  jeder bewirkt.
- Sammeln Sie **Wörter**, die Sie nicht kannten, und schlagen Sie sie im [Glossar für Lernende](glossar-fuer-lernende.md)
  nach.
- Notieren Sie das **Release**, mit dem Sie lernen (`php bin/php/console --version`). Seiten beschreiben ein Release;
  Ihre Notizen sollten das auch tun.

Schreiben Sie niemals Passwörter, Schlüssel oder Tokens in Notizen, die Sie teilen oder committen.

## Mit Quizzen wiederholen

Die Lektionen der Trainer enden mit einem Quiz und seinen Antworten ([Trainer: hier beginnen](../trainer/trainer-hier-beginnen.md)).
Sie dürfen sie selbst verwenden:

1. Machen Sie die praktische Übung der Lektion auf Ihrer Lerninstallation.
2. Beantworten Sie einen Tag später das Quiz, ohne nachzusehen.
3. Prüfen Sie die Antworten, die die Hilfeseiten nennen, und lesen Sie erneut, was Sie nicht wussten.
4. Beantworten Sie es eine Woche später noch einmal.

Kurze, verteilte Wiederholungen bewirken mehr als eine lange Sitzung. Der [Kurskatalog](../trainer/kurskatalog.md)
zeigt, welcher Kurs welches Thema behandelt.

## Wenn eine Seite und die Software nicht übereinstimmen

Die Seiten sind für ein Release geschrieben und an echten Installationen geprüft, aber Software ändert sich. Wenn ein
Schritt nicht funktioniert:

1. Vergleichen Sie Ihr Release mit dem, das die Seite nennt.
2. Prüfen Sie, ob Sie im richtigen Siteaccess sind und mit der richtigen Rolle angemeldet; Menüs zeigen nur, was Ihre
   Rolle erlaubt ([Erste Schritte](../../anleitungen/erste-schritte.md#5-erste-anmeldung)).
3. Lesen Sie `var/log/error.log` ([Logs und Fehler lesen](logs-und-fehler-lesen.md)).
4. Wenn die Seite falsch ist, sagen Sie es dem Projekt: Jede Seite hat **Auf GitHub bearbeiten**
   ([Die Hilfe als Lernende verbessern](die-hilfe-als-lernende-verbessern.md)).

## Ein Wochenrhythmus

| Tag | Tätigkeit | Zeit |
|---|---|---|
| 1 | Einen neuen Abschnitt Ihres Lernpfads lesen und durcharbeiten | 60 Minuten |
| 2 | Ihn aus Ihren Notizen wiederholen, ohne die Seite | 30 Minuten |
| 3 | An Ihrem aktuellen Lernprojekt arbeiten | 60 bis 90 Minuten |
| 4 | Quiz zur Lektion der letzten Woche wiederholen | 15 Minuten |
| 5 | Eine Seite außerhalb Ihres Pfads lesen (eine Anleitung, eine Referenzseite) | 30 Minuten |
| Wochenende | Freiwillig: eine Frage in der Community beantworten oder einen Tippfehler in der Hilfe korrigieren | nach Belieben |

Passen Sie die Zeiten an; behalten Sie die Reihenfolge bei. Tun kommt vor Weiterlesen.

## Verwandte Seiten

- [Einen Lernpfad wählen](einen-lernpfad-waehlen.md)
- [Ein Lerntagebuch führen](ein-lerntagebuch-fuehren.md)
- [Lernprojekte: Überblick](lernprojekte-ueberblick.md)
- [Lernende: hier beginnen](lernende-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
