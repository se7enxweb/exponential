---
title: GitHub für Fürsprecher
audience: advocate
category: advocacy
order: 73
summary: Wie Fürsprecher mit und ohne Entwicklerhintergrund GitHub für Exponential nutzen - Releases verfolgen, Issues lesen und kommentieren, reagieren statt „+1“, bei der Sichtung helfen, Pull Requests für Dokumentation, Prüfungen und sicherer Umgang mit Geheimnissen und personenbezogenen Daten.
keywords: GitHub, se7enxweb, Issues, Pull Requests, Releases, beobachten, Benachrichtigungen, Kommentare, Reaktionen, Sichtung, Prüfung, Fork, Auf GitHub bearbeiten, Konto, issues, pull requests, releases, watch, notifications, comments, reactions, triage, review, fork, Edit on GitHub, account
type: help
---
# GitHub für Fürsprecher

Diese Seite ist für Fürsprecher, die neu bei GitHub sind, und für Entwickler, die es als Fürsprecher nutzen möchten,
nicht nur zum Programmieren. Exponential lebt auf GitHub unter dem Konto [se7enxweb](https://github.com/se7enxweb): der
Kernel, die Erweiterungen, Velocity und der Community-Tracker. Für Issues und Pull Requests brauchen Sie ein kostenloses
GitHub-Konto ([Wenn etwas schiefgeht](../../../en/us/help/when-something-goes-wrong.md#filing-a-bug-in-exponential)).

## Inhalt

1. [Die Repositorys, die Sie nutzen werden](#die-repositorys-die-sie-nutzen-werden)
2. [Verfolgen, was passiert](#verfolgen-was-passiert)
3. [Issues: lesen, kommentieren, reagieren](#issues-lesen-kommentieren-reagieren)
4. [Pull Requests](#pull-requests)
5. [Helfen ohne Code](#helfen-ohne-code)
6. [Sicherheit auf GitHub](#sicherheit-auf-github)

## Die Repositorys, die Sie nutzen werden

| Repository | Verwendung |
|---|---|
| [se7enxweb/exponential](https://github.com/se7enxweb/exponential) | Der Kernel, die Administration, die mitgelieferten Erweiterungen und die Dokumentation (diese Hilfe liegt in `doc/`) |
| [se7enxweb/exponential-velocity](https://github.com/se7enxweb/exponential-velocity) | Der Velocity-Server |
| [se7enxweb/exponential-community](https://github.com/se7enxweb/exponential-community) | Das Projekt, die Website, das Ökosystem, Ideen |
| Das eigene Repository jeder Erweiterung | Ihr Code und ihre Issues; die Seite **Fehler melden** der Hilfe listet sie auf |

## Verfolgen, was passiert

- **Watch > Custom > Releases** bei einem Repository, um nur über neue Releases benachrichtigt zu werden.
- Die [Seite der Releases](https://github.com/se7enxweb/exponential/releases) listet die veröffentlichten Versionen auf.
- Die Liste der Commits auf `main` zeigt die tägliche Arbeit; die [Geschichte](../../../en/us/history/README.md) erklärt sie Monat
  für Monat.
- Schalten Sie Benachrichtigungen nicht für alles ein, es sei denn, Sie möchten Hunderte E-Mails.

## Issues: lesen, kommentieren, reagieren

| Tun Sie | Tun Sie nicht |
|---|---|
| Suchen, bevor Sie ein neues Issue eröffnen | Ein Duplikat eröffnen |
| Einem bestehenden Issue **neue Informationen** hinzufügen: Ihre Version, eine Nachstellung | „+1“ oder „bei mir auch“ kommentieren (nutzen Sie stattdessen eine Reaktion) |
| Mit einem Daumen-hoch zeigen, dass das Issue Sie betrifft | Immer wieder „Gibt es Neuigkeiten?“ fragen |
| Konkret und freundlich sein | Eine Korrektur oder einen Termin fordern |
| Verwandte Issues und Forendiskussionen verlinken | Logs mit personenbezogenen Daten oder Geheimnissen einfügen |

Wie Sie ein gutes Issue schreiben: [Fehler gut melden](fehler-gut-melden.md). Welches Repository:
[Welcher Tracker für welche Meldung](welcher-tracker-fuer-welche-meldung.md).

## Pull Requests

- **Dokumentation**: **Auf GitHub bearbeiten** auf jeder Hilfeseite öffnet die Datei; GitHub forkt das Repository für
  Sie ([Die Hilfe verbessern](die-hilfe-verbessern.md)).
- **Code**: forken, Branch anlegen, committen, Pull Request ([Mit Git entwickeln](../../../en/us/help/develop-with-git.md#contribute-a-change-with-a-pull-request)).
- **Commit-Nachrichten** beginnen mit `Added:`, `Updated:`, `Fixed:`, `Removed:` oder `Renamed:`
  ([CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md)).
- **Jeder Pull Request wird übernommen**, und seine Commits bleiben erhalten; was korrigiert werden muss, wird in Commits
  obendrauf korrigiert ([Wie das Projekt wächst](../../../en/us/help/who-is-the-exponential-foundation.md#how-the-project-grows)).

## Helfen ohne Code

| Hilfe | Wie |
|---|---|
| Einen Fehler bestätigen | Ihn auf Ihrer Installation nachstellen und mit Ihrer Umgebung kommentieren ([Issues nachstellen und bestätigen](issues-nachstellen-und-bestaetigen.md)) |
| Ein Issue verbessern | Schritte, ein kleineres Beispiel, die genaue Meldung ergänzen |
| Einen Pull Request testen | Die Änderung auf einer Testinstallation ausprobieren und berichten, was Sie gesehen haben |
| Dokumentation prüfen | Einen Pull Request zur Dokumentation auf Klarheit und Namen lesen |
| Übersetzen | Pull Requests zu den Übersetzungsdateien ([Bei Übersetzungen helfen](bei-uebersetzungen-helfen.md)) |
| Erstmals Beitragende willkommen heißen | Ein freundlicher Kommentar zu ihrem ersten Pull Request |

## Sicherheit auf GitHub

- Alles in einem öffentlichen Repository ist für immer öffentlich, auch Text, der später wegbearbeitet wurde, in der Historie.
- Stellen Sie nie Passwörter, Schlüssel, Cookies, Datenbankadressen oder personenbezogene Daten in Issues, Kommentare oder Code.
- Sicherheitsprobleme gehören nie in ein öffentliches Issue: Nutzen Sie den vertraulichen Weg aus `SECURITY.md`
  ([Sicherheitsmeldungen bleiben vertraulich](sicherheitsmeldungen-bleiben-vertraulich.md)).
- Schalten Sie für Ihr GitHub-Konto die Zwei-Faktor-Authentifizierung ein.

## Verwandte Seiten

- [Welcher Tracker für welche Meldung](welcher-tracker-fuer-welche-meldung.md)
- [Einen ersten Beitrag begleiten](einen-ersten-beitrag-begleiten.md)
- [Mit Git entwickeln](../../../en/us/help/develop-with-git.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
