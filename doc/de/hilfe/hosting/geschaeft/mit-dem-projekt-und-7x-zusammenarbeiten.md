---
title: Mit dem Projekt und 7x zusammenarbeiten
audience: hosting
category: hosting
order: 80
summary: Wie ein Hosting-Anbieter im Alltag mit dem Exponential-Projekt zusammenarbeitet - Releases verfolgen, Fehler und Sicherheitsprobleme melden, Korrekturen an das Projekt beitragen, sponsern und bei Bedarf Dienste von 7x einkaufen.
keywords: Projekt, 7x, Exponential Foundation, Releases, GitHub, Packagist, Issues, Sicherheitsmeldung, SECURITY.md, beitragen, Pull Request, Sponsor, Support-Vertrag, Upstream, project, releases, issues, security report, contribute, pull request, sponsor, support contract, upstream
type: help
---
# Mit dem Projekt und 7x zusammenarbeiten

Diese Seite ist für Systemadministratoren und Führungskräfte bei einem Hosting-Anbieter. Sie beantwortet: wie Sie von
neuen Releases erfahren, wo Sie Probleme melden, wie Sie Korrekturen zurückgeben und wie Sie bezahlte Hilfe bekommen, wenn
Ihr eigenes Team nicht ausreicht.

## Inhalt

1. [Releases verfolgen](#releases-verfolgen)
2. [Fehler melden](#fehler-melden)
3. [Sicherheitsprobleme melden](#sicherheitsprobleme-melden)
4. [Korrekturen an das Projekt beitragen](#korrekturen-an-das-projekt-beitragen)
5. [Sponsern](#sponsern)
6. [Dienste einkaufen](#dienste-einkaufen)

## Releases verfolgen

| Wo | Was Sie sehen |
|---|---|
| Das Kernel-Repository, `github.com/se7enxweb/exponential` | Tags, Release Notes, Issues, Pull Requests |
| Packagist, `se7enxweb/exponential` | die Composer-Versionen |
| Die Changelogs in `doc/changelogs/` | was jedes Release geändert hat, auch in dieser Hilfe |
| Das Repository jeder Erweiterung unter `github.com/se7enxweb` | ihre eigenen Releases und ihr Changelog |

Regeln, die für einen Hoster zählen, aus `SECURITY.md` und den Release-Regeln:

- **Eine veröffentlichte Version ändert sich nie.** Ein veröffentlichter Tag wird nie geändert: Eine Korrektur für ein
  Problem in `v6.0.14` oder einem früheren Tag erreicht Sie als nächstes Release, nicht als korrigierte Kopie des alten
  (`SECURITY.md`). Sie können Versionen gefahrlos festschreiben.
- **Nur die Linie 6.0 wird gepflegt.** 5.x, 4.x, 3.x und älter werden nicht unterstützt (`SECURITY.md`); Kunden auf
  diesen Versionen brauchen eine Migration ([Kunden migrieren](../migrationen/README.md)).
- **Versionsnummern zählen in der letzten Stelle weiter.** Nach `.9` kommt `.10`; sortieren Sie Versionen mit
  `git tag -l --sort=version:refname` oder `sort -V`, nicht nach Namen
  ([Wer ist die Exponential Foundation?](../../../../en/us/help/who-is-the-exponential-foundation.md#releases-and-version-numbers)).

```bash
# list the newest kernel tags in version order
git ls-remote --tags https://github.com/se7enxweb/exponential.git | awk '{print $2}' | sort -V | tail -5
```

## Fehler melden

Folgen Sie [Wenn etwas schiefgeht](../../../../en/us/help/when-something-goes-wrong.md): Dort steht, welcher Tracker passt (Software,
Projekt oder Community) und was eine gute Meldung enthält. Als Hoster geben Sie an:

- die Version und die PHP-Version;
- die Engine (Velocity, FrankenPHP, Apache oder nginx mit PHP-FPM) und die Datenbank;
- die Schritte, um es auf einer sauberen Installation nachzustellen, wenn Sie können;
- Log-Zeilen, aus denen Geheimnisse und personenbezogene Daten entfernt sind.

## Sicherheitsprobleme melden

Vertraulich, nie in einem öffentlichen Issue. `SECURITY.md` im Repository nennt die Adresse und sagt, dass Sie eine erste
Antwort innerhalb eines Werktags erwarten können. Geben Sie die Version, die PHP-Version, die Engine und die Datenbank an,
was ein Angreifer tun kann und die Schritte zum Nachstellen, und lassen Sie Passwörter, Geheimnisse und personenbezogene
Daten weg. Details: [Sicherheitsupdates und Offenlegung](../sicherheit/sicherheitsupdates-und-offenlegung.md).

## Korrekturen an das Projekt beitragen

Eine Korrektur, die Sie nur auf Ihren Servern behalten, müssen Sie bei jedem Update erneut anwenden. Eine Korrektur, die im
Projekt gemergt wird, pflegt das Projekt. Die Praxis des Projekts: Jeder Pull Request wird gemergt, und die Commits der
Mitwirkenden bleiben erhalten
([Mitwirkende und Pull Requests](../../../../en/us/help/who-is-the-exponential-foundation.md#contributors-and-pull-requests)).

1. Forken Sie das Repository und legen Sie einen Branch an ([Mit Git entwickeln](../../../../en/us/help/develop-with-git.md)).
2. Nehmen Sie die Korrektur vor, wo möglich mit einem Test.
3. Öffnen Sie einen Pull Request, der das Problem erklärt und wie Sie die Korrektur geprüft haben.
4. Code wird unter der GPL v2 oder später beigetragen (`CONTRIBUTING.md`).

> **Tipp**
> Erfahrung aus dem Hosting ist auch wertvolle Dokumentation. Eine Korrektur an einer dieser Hosting-Seiten ist ein Pull
> Request wie jeder andere; jede Hilfeseite hat einen Link **Auf GitHub bearbeiten**.

## Sponsern

Sowohl 7x als auch die Foundation nehmen Sponsoring an; die Links stehen auf
[Wie Sie unterstützen oder mitmachen](../../../../en/us/help/who-is-the-exponential-foundation.md#how-to-support-or-join). Ein Hoster, der
mit Exponential Geld verdient, hat ein direktes Interesse an einem gut finanzierten Projekt: Neue Funktionen, Dokumentation,
Erweiterungen und Sicherheitsarbeit halten seine Kunden auf der Plattform.

## Dienste einkaufen

7x verkauft Support- und Wartungsverträge für bestehende Websites, Migrationen, Entwicklung und Schulungen; Preise und
Bedingungen stehen auf der Website von 7x, nicht hier ([Wer ist 7x?](../../../../en/us/help/who-is-7x.md#services-and-hosting)). Andere
Unternehmen bieten vielleicht dieselben Dienste an. Typische Gründe für einen Hoster, Hilfe einzukaufen:

- die Migration eines Kunden von einer alten Hauptversion;
- ein Leistungsproblem, das Sie nicht nachstellen können;
- eine eigene Erweiterung, die Ihr Team nicht pflegen kann.

## Verwandte Seiten

- [Partnerprogramme für Hoster](partnerprogramme-fuer-hoster.md)
- [Sicherheitsupdates und Offenlegung](../sicherheit/sicherheitsupdates-und-offenlegung.md)
- [Kunden-Websites aktualisieren](../betrieb/kunden-websites-aktualisieren.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
