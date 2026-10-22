---
title: Git-Arbeitsablauf
audience: software-engineer, developer
category: software-engineering
order: 900
summary: Wie Git für Exponential verwendet wird - main als einzige Entwicklungslinie, Branches und Pull-Requests, die zusammengefasst und neu aufgesetzt werden, solange sie offen sind, Merge-Commits, kein Umschreiben der Geschichte nach einem Merge, Betreffzeilen mit Added, Updated, Fixed, Removed oder Renamed, die Regeln für den Nachrichtentext, die Hooks und die Arbeit mit Git-Klonen des Kernels und der Erweiterungen.
keywords: Git, Arbeitsablauf, Branches, main, Pull-Request, Squash, Rebase, force-with-lease, Merge-Commit, Commit-Nachricht, Added, Updated, Fixed, Removed, Renamed, Hooks, Klone, Softwareingenieur, git workflow, pull request, merge commit, commit message, clones
type: help
---
# Git-Arbeitsablauf

Diese Seite richtet sich an Ingenieure, die in Exponential committen, seine Geschichte prüfen oder Installationen aus
Git-Klonen betreiben. Der Standard ist [Quellcodeverwaltung und Releases](../../standards/versionsverwaltung-und-releases.md);
die praktischen Anleitungen sind [Mit Git entwickeln](../mit-git-entwickeln.md) und [Mit Git aktualisieren](../mit-git-aktualisieren.md).

## Inhalt

1. [Branches und Merges](#branches-und-merges)
2. [Commit-Nachrichten](#commit-nachrichten)
3. [Hooks](#hooks)
4. [Was nie in einen Commit gehört](#was-nie-in-einen-commit-gehört)
5. [Installationen aus Klonen](#installationen-aus-klonen)
6. [Bekannte Lücken](#bekannte-lücken)

## Branches und Merges

- **`main` ist die Entwicklungslinie**, für Composer mit dem Alias `6.0.x-dev`; die CI läuft darauf.
- **Gearbeitet wird auf Branches**, die per Pull-Request nach `main` gelangen.
- **Solange ein Pull-Request offen ist**, fasst sein Autor ihn zu logischen Commits zusammen (meist einem) und setzt ihn auf
  `main` neu auf, wie die Reviews es verlangen:

  ```bash
  git fetch origin
  git rebase -i origin/main
  git push --force-with-lease
  ```

  Erzwingen Sie einen Push nur auf den Branch Ihres eigenen Pull-Requests.
- **Pull-Requests werden mit einem Merge-Commit zusammengeführt.** Nach dem Merge wird die Geschichte von `main` nie
  umgeschrieben; spätere Korrekturen kommen als neue Pull-Requests.

## Commit-Nachrichten

Die Betreffzeile beginnt mit dem, was die Änderung tut:

| Präfix | Für |
|---|---|
| `Added: ` | eine neue Funktion, Datei, Klasse, Einstellung oder Fähigkeit |
| `Updated: ` | eine Änderung an bestehendem Verhalten, Templates, Einstellungen oder Code |
| `Fixed: ` | eine Fehlerkorrektur |
| `Removed: ` | etwas Gelöschtes |
| `Renamed: ` | eine umbenannte Datei, Klasse, Einstellung oder Erweiterung |

Beispiel aus dem Hook: `Fixed: The redirect keeps the query string on the own host`. Die Betreffzeilen `Merge`, `Revert`,
`fixup!`, `squash!` und `amend!` von Git selbst werden akzeptiert.

Der Text sagt warum und was, für einen Leser, der den Zusammenhang nicht kennt. Weitere Regeln, die kein Werkzeug prüft:
Nennen Sie das Produkt Exponential (Bezeichner wie `eZContentObject` bleiben), keine Zuschreibung an Werkzeuge oder
Assistenten, keine privaten oder unveröffentlichten Pfade, und vermischen Sie keine Änderungsarten in einem Commit, wenn
sie sich trennen lassen.

Eine schlechte Betreffzeile eines Projektautors lässt die Prüfung scheitern; die eines Beitragenden erzeugt nur eine
Warnung. Um Ihren Branch streng zu prüfen:

```bash
EXP_COMMIT_MSG_STRICT_ALL=1 sh .githooks/check-commit-range origin/main HEAD
```

## Hooks

```bash
make hooks
```

setzt `core.hooksPath` auf `.githooks`: Der Pre-Commit-Hook prüft die Syntax (auch mit PHP 8.0, wenn vorhanden), das
Dateimanifest und den Codestil der vorgemerkten Dateien; der Commit-Msg-Hook prüft die Betreffzeile; der Pre-Push-Hook
führt PHPStan aus, wenn die Entwicklungswerkzeuge installiert sind. Überspringen Sie sie nur im Notfall (`--no-verify`);
die CI führt dieselben Prüfungen aus ([Statische Analyse und Codestil](statische-analyse-und-codestil.md)).

## Was nie in einen Commit gehört

`.gitignore` schließt vieles davon bereits aus; prüfen Sie trotzdem:

- Zugangsdaten und Maschineneinstellungen: `settings/override/`, `config.env.php`;
- Laufzeitverzeichnisse: `var/cache/`, `var/log/`, `var/storage/`;
- `vendor/`, `composer.lock`, `.devtools/`;
- private Arbeitsnotizen.

Merken Sie Dateien namentlich vor statt mit `git add -A`, damit nichts Unversioniertes hineinrutscht.

## Installationen aus Klonen

Composer kann den Kernel und die Erweiterungen als Git-Klone installieren. Dann sehen Sie, was sich geändert hat, finden,
wann und warum sich eine Zeile geändert hat, suchen eine Regression mit Bisect, nutzen eine Korrektur vor ihrem Release und
bleiben mit einem Merge aktuell ([Mit Git entwickeln](../mit-git-entwickeln.md)). Eine solche Installation zu aktualisieren
ist ein Verfahren in sieben Schritten mit einem Wartungsfenster, auf der Kommandozeile oder aus der Administration
([Mit Git aktualisieren](../mit-git-aktualisieren.md)).

## Bekannte Lücken

Die Regeln für Merge und Push sind Richtlinien: `main` hat keine Branch-Schutzregel, und Commits und Tags werden nicht
signiert.

## Verwandte Seiten

- [Beiträge an das Projekt](upstream-beitragen.md)
- [Commit-Nachrichten](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/commit-nachrichten.md)
- [Ein Team mit Git führen](../fuehrung/ein-team-mit-git-fuehren.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
