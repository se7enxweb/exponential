---
title: Einen Fehler als Community-Mitglied melden
audience: community, user, admin, editor, developer
category: community
order: 23
summary: Wie Sie einen Fehler in Exponential so melden, dass er behoben wird - den richtigen Tracker wählen, zuerst suchen, die Teile einer guten Fehlerbeschreibung, eine sichere Zusammenfassung der Installation und was nach der Meldung geschieht.
keywords: Fehler, Bug, Issue, Tracker, GitHub-Issues, Fehler melden, exphelp/report, Systeminformationen, exp:info, nachstellen, Velocity-Issues, Community-Issues, bug, issue, tracker, Report an error, system information, reproduce
type: help
---
# Einen Fehler als Community-Mitglied melden

Eine gute Fehlerbeschreibung ist einer der wertvollsten Beiträge überhaupt: Sie macht aus dem Problem einer Person
eine Korrektur für alle. Diese Seite ist die Sicht des Community-Mitglieds; die vollständige Anleitung steht unter
[Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md).

## Inhalt

1. [Ist es ein Fehler?](#ist-es-ein-fehler)
2. [Der richtige Tracker](#der-richtige-tracker)
3. [Die Teile einer guten Fehlerbeschreibung](#die-teile-einer-guten-fehlerbeschreibung)
4. [Was nie in ein Issue gehört](#was-nie-in-ein-issue-gehört)
5. [Nach der Meldung](#nach-der-meldung)

## Ist es ein Fehler?

| Es ist so | Dann |
|---|---|
| Die Software macht etwas falsch, und Sie können es erneut auslösen | Ein Fehler: melden Sie ihn |
| Sie wissen nicht, wie etwas geht | Eine Frage: stellen Sie sie in den Foren ([Eine gute Frage stellen](../beitreten/eine-gute-frage-stellen.md)) |
| Jemand könnte sehen oder ändern, was er nicht sehen oder ändern sollte | Ein Sicherheitsproblem: vertraulich an security@se7enx.com (`SECURITY.md`) |
| Eine Idee für das Projekt, die Website oder die Community | Der Issue-Tracker der Community |
| Es funktioniert wie vorgesehen, aber nicht so, wie Sie es möchten | Vielleicht eine **negative Funktion**: ein gewolltes Verhalten, kein Fehler. Lesen Sie [Negative Funktionen](../../projekt/unterstuetzung-erhalten.md#negative-funktionen), bevor Sie es melden |

> **Warnung:** Manches Verhalten, das falsch aussieht, ist Absicht oder auslegungsfähig. Eine Änderung an einer solchen
> negativen Funktion kann jede Website betreffen, die davon abhängt; ein Issue oder Pull Request dazu braucht daher
> eine klare Begründung und kann mehrere Review-Runden durchlaufen, bevor er angenommen wird. Siehe
> [Negative Funktionen](../../projekt/unterstuetzung-erhalten.md#negative-funktionen).

## Der richtige Tracker

| Tracker | Für |
|---|---|
| [se7enxweb/exponential](https://github.com/se7enxweb/exponential/issues) | Den Kernel, die Administration, die mitgelieferten Erweiterungen, die Dokumentation |
| [se7enxweb/exponential-velocity](https://github.com/se7enxweb/exponential-velocity/issues) | Den Anwendungsserver Velocity |
| Das eigene Repository der Erweiterung | Eine Erweiterung mit eigenem Repository; die Seite **Fehler melden** listet sie auf |
| [se7enxweb/exponential-community](https://github.com/se7enxweb/exponential-community/issues) | Das Projekt, die Website, das Ökosystem, alles, was kein Softwarefehler ist |

Quelle: [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md). Mehr zur Auswahl:
[Welcher Tracker für welche Meldung](../../fuersprecher/welcher-tracker-fuer-welche-meldung.md).

## Die Teile einer guten Fehlerbeschreibung

- **Ein Titel, der das Problem benennt**, nicht „Es funktioniert nicht“.
- **Die Schritte**, nummeriert, von einem bekannten Ausgangspunkt aus.
- **Erwartetes und tatsächliches Ergebnis**, mit der genauen Meldung.
- **Die Umgebung**: Version, PHP-Version, Engine (Velocity, Apache oder nginx mit PHP-FPM, FrankenPHP), Datenbank,
  Design. **Setup** > **Systeminformationen** > **Bericht als Text kopieren** oder `./console exp:info --plain --no-logo`
  liefern eine Zusammenfassung ohne Passwörter, Schlüssel und Serverpfade.
- **Logzeilen** aus dem betreffenden Zeitraum, bereinigt um alles Private.
- **Ein Bildschirmfoto**, wenn es hilft, ohne sichtbare private Daten.

## Was nie in ein Issue gehört

Passwörter, Schlüssel, Session-Cookies, Datenbankadressen oder die personenbezogenen Daten von Kunden oder Nutzern
([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)).

## Nach der Meldung

- Beantworten Sie Fragen der Maintainer; ein Issue, das sich nicht nachstellen lässt, wird oft geschlossen.
- Wenn Sie einen Workaround finden, ergänzen Sie ihn im Issue.
- Wenn Sie können, senden Sie die Korrektur: „Ist der Code als Git-Klone installiert, ist eine Korrektur nur einen
  Branch in Ihrem Fork und einen Pull Request entfernt“ ([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)).
  Nennen Sie das Issue (`Fixes #123`) im Pull Request.
- Wenn ein Release den Fehler behebt, bestätigen Sie das im Issue.

## Verwandte Seiten

- [Als Community-Mitglied mitwirken](README.md)
- [Fehler gut melden](../../fuersprecher/fehler-gut-melden.md)
- [Testen und Fehler melden](../../unterstuetzer/testen-und-fehler-melden.md)
- [Probleme melden](../../kunden/gesund-halten/probleme-melden.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
