---
title: Commit-Nachrichten, aus denen das Projekt ein Changelog bauen kann
audience: developer, designer, app-creator
category: extension-development
order: 110
summary: Das Format der Commit-Nachrichten von Exponential - Added, Updated, Fixed, Removed, Renamed - warum es existiert, wie der commit-msg-Hook des Kernels es prüft und wie Sie einen Betreff und einen Text schreiben, die erklären, was sich geändert hat und warum.
keywords: Commit-Nachricht, Added, Updated, Fixed, Removed, Renamed, Changelog, commit-msg-Hook, make hooks, Betreff, Text, git, commit message, subject, body
type: help
---
# Commit-Nachrichten, aus denen das Projekt ein Changelog bauen kann

Teil 5, Kapitel 11. Eine Commit-Nachricht wird einmal geschrieben und oft gelesen: in Reviews, in `git log`, in
`git blame` Jahre später und im Changelog des nächsten Releases. Exponential verwendet ein einfaches Format, das all
das möglich macht. Verwenden Sie es im Kernel (dort wird es geprüft) und in Ihren eigenen Erweiterungen.

## Das Format

Der Betreff beginnt mit einem von fünf Wörtern und einem Doppelpunkt:

| Präfix | Für |
|---|---|
| `Added: ` | etwas Neues: eine Funktion, eine Datei, eine Klasse, eine Einstellung, eine Seite |
| `Updated: ` | geändertes oder verbessertes Verhalten, Templates, Texte, Einstellungen |
| `Fixed: ` | ein behobener Fehler |
| `Removed: ` | etwas Gelöschtes: eine Funktion, eine Datei, eine Abhängigkeit |
| `Renamed: ` | eine umbenannte Datei, Klasse, Einstellung oder View |

Der Rest des Betreffs ist ein Satz, der sagt, was **nach** dem Commit zutrifft:

```text
Fixed: The redirect keeps the query string on the own host
Updated: The context menu entries of expchangeclass carry no inline handlers
Added: German translation of the sign-in and setup texts
```

Das Beispiel für einen `Fixed:`-Betreff ist dasjenige, das `CONTRIBUTING.md` angibt.

## Warum dieses Format

- **Das Changelog schreibt sich von selbst.** Beim Release werden die Commits seit dem letzten Tag nach ihrem ersten
  Wort in die vier oder fünf Gruppen eines Changelogs sortiert ([Changelogs](../teil-7-veroeffentlichen/changelogs.md)).
- **Reviewer wissen, was sie erwartet.** Ein `Fixed:`-Commit sollte keine Funktionen hinzufügen; ein `Renamed:`-Commit
  sollte kein Verhalten ändern.
- **Es hält Commits klein.** Wenn ein Betreff ein „und“ zwischen zwei Präfixen braucht, sind es zwei Commits.

## Der Kernel prüft es

Das Kernel-Repository liefert Git-Hooks in `.githooks/` mit. Führen Sie in Ihrem Klon einmal aus:

```bash
make hooks
```

Von da an lehnt `git commit` einen Betreff ohne eines der fünf Präfixe ab:

```text
commit-msg: the subject must start with 'Added: ', 'Updated: ', 'Fixed: ', 'Removed: ' or 'Renamed: ', e.g.
    Fixed: The redirect keeps the query string on the own host
```

Merge-Commits und die von Git erzeugten Betreffs `Revert`, `fixup!` und `squash!` sind erlaubt. Die CI prüft die
Commits eines Pushs oder Pull Requests mit `.githooks/check-commit-range`
([Qualitätsprüfungen](../../../../eng-us/features/6.0/quality-checks.md), auf Englisch); laut dieser Seite führt der
Commit eines Beitragenden dort nur zu einer Warnung.

Sie können den `commit-msg`-Hook in das Repository Ihrer Erweiterung kopieren: Es ist ein kurzes Shell-Skript.

## Der Text

Nach einer Leerzeile erklärt der Text, was der Betreff nicht kann: warum die Änderung nötig war, was vorher falsch war
und wie sie geprüft wurde. Echte Beispiele aus den Erweiterungen, die dieses Buch anführt:

```text
Updated: Version 1.0.6: installs and runs on PHP 8.0, the oldest PHP Exponential 6 supports

PHP 8.0 is the stock PHP of Red Hat Enterprise Linux 9 and the oldest version Exponential 6 supports. The code of
this extension was checked with PHPCompatibility for PHP 8.0, linted and loaded with PHP 8.0.30, and served by
PHP 8.0 on a full installation.
```

(se7enxweb/expchangeclass, Commit 32196f0.) Der Text nennt den Grund, die Prüfungen und das Ergebnis. Wer ihn in fünf
Jahren liest, weiß, ob er der Änderung trauen kann.

## Gute und schlechte Betreffs

| Schlecht | Besser |
|---|---|
| `fix` | `Fixed: The batch script stops when no object of a batch converts` |
| `Updated stuff` | `Updated: The result boxes use the shared admin notice instead of style attributes` |
| `Fixed: bug #12` | `Fixed: The parameter file shown after a conversion is escaped` (und „Closes #12“ im Text) |
| `Added: tests and fixed the menu and new translations` | drei Commits |
| `WIP` | nicht pushen; oder vor dem Pull Request zusammenführen (squash) |

## Was nicht in eine Commit-Nachricht gehört

- Geheimnisse, Passwörter, personenbezogene Daten: Ein Commit ist öffentlich, sobald er gepusht ist, und lässt sich
  nicht zurücknehmen.
- Private Pfade Ihres Rechners oder Namen interner Server.
- Nennungen von Werkzeugen, mit denen Sie die Änderung geschrieben haben. Der Autor des Commits ist die Person, die
  dafür verantwortlich ist.

## Verwandte Seiten

- Zurück: [Gute Issues schreiben](gute-issues-schreiben.md)
- Weiter: [Die Arbeit anderer prüfen](die-arbeit-anderer-pruefen.md)
- [Changelogs](../teil-7-veroeffentlichen/changelogs.md)
- [Qualitätsprüfungen](../../../../eng-us/features/6.0/quality-checks.md) (auf Englisch)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
