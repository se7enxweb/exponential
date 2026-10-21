# Die Geschichte von Exponential, Dezember 2023 bis heute

Dies ist die Geschichte jeder Änderung an Exponential und an den Repositorys um es herum seit Beginn der se7enxweb-Ära
im Dezember 2023, so geschrieben, dass Sie daraus lernen können: was eine Änderung bringt, wie Sie sie nutzen und was
Sie beim Upgrade prüfen sollten. Sie hat zwei Hälften. Die **Chronik** erzählt die Geschichte Monat für Monat. Das
**Änderungsverzeichnis** ist die vollständige, maschinell erzeugte Aufzeichnung jedes Commits. Zwischen beiden fehlt
nichts, und [die Seite zur Abdeckung](abdeckung.md) beweist es.

## Kurz gesagt

- Lesen Sie **eine Monatsseite**, um zu erfahren, was in diesem Monat möglich wurde; jede beginnt mit einer kurzen Zusammenfassung und endet mit verwandten Seiten.
- Jede Änderung nennt ihren Commit, zum Beispiel `2ddbc4c624`. Öffnen Sie ihn mit `git show 2ddbc4c624`.
- Das **Änderungsverzeichnis** führt alle Änderungen eines Repositorys mit Datum, Art, Größe und Release-Tag auf: [Änderungsverzeichnis](aenderungsverzeichnis/README.md).
- Die Seite zur **Abdeckung** zeigt je Repository und Monat, dass jede Änderung erklärt, als klein aufgeführt oder als ohne Nutzen für Anwender gekennzeichnet ist: [Abdeckung](abdeckung.md).

## Die Geschichte bisher

Lesen Sie diese Zeilen zuerst, wenn Sie neu sind; jede verweist auf den Monat, der sie ausführlich erzählt.

- **Dezember 2023: Die Codebasis wechselt den Besitzer.** Die erste Änderung von außen wurde am 11. Dezember zusammengeführt, der Setup-Assistent lief nun unter PHP 8, und Update-Scripts ab 5.4 wurden veröffentlicht. [Dezember 2023](2023/2023-12.md)
- **Januar 2024: 6.0.0 ist stabil, und SQLite kommt.** Eine Datenbank, die keinen Server braucht, mit Treiber, Schema-Handler und Demonstrationsdatenbank. [Januar 2024, erste Hälfte](2024/2024-01a.md)
- **August bis November 2024: admin3.** Ein vollständiges, responsives Design für die Administration, danach auf echten Geräten getestet und korrigiert. [August 2024](2024/2024-08.md), [November 2024](2024/2024-11.md)
- **April 2025: PHP 8.4 kommt, PHP 7 geht.** Websites mit PHP 7.4 bleiben auf 6.0.7. [April 2025](2025/2025-04.md)
- **August 2025: Das Produkt heißt Exponential.** Bezeichner, Pfade und Tabellen blieben unverändert. [August 2025](2025/2025-08.md)
- **Dezember 2025: PHP 8.5 wird unterstützt.** [Dezember 2025](2025/2025-12.md)
- **Juni 2026: MongoDB 8 als Datenbank, und 6.0.14.** [Juni 2026, erste Hälfte](2026/2026-06a.md)
- **Juli 2026: Die Kommandozeile wird erwachsen.** `ezpm` in der Konsole, der Kickstarter, ein Werkzeug zum Zurücksetzen von Passwörtern, ein vertrauenswürdiger Template-Editor. [Juli 2026](2026/2026-07.md)
- **August 2026: ehrliches Template-Debugging, sechs Sicherheits-Patches, Tests bei jeder Änderung.** [August 2026](2026/2026-08.md)
- **September 2026: große Listen, die funktionieren, Velocity, eine Installation mit einem Befehl und eine Härtungsrunde.** [Erste Hälfte](2026/2026-09a.md), [zweite Hälfte](2026/2026-09b.md)
- **Oktober 2026: jQuery 4 und admin4, ein Prüfprotokoll, `exp:ini`, eine Debug-Leiste, Inhaltsjobs und TinyMCE 8.** [Oktober 2026](2026/2026-10.md)

## So lesen Sie die Chronik

1. Wählen Sie einen Monat aus der Tabelle unten. Eine Monatsseite beginnt mit **Kurz gesagt**: drei bis fünf Zeilen, die Sie in einer halben Minute lesen.
2. Der Hauptteil ist nach Tag oder nach Thema gegliedert. Jeder Absatz nennt, was sich geändert hat, warum es wichtig ist und was Sie jetzt tun können, was vorher nicht ging. Commit-Hashes stehen in Backticks.
3. **Kleinere Änderungen** führt die Änderungen auf, die keine eigene Geschichte brauchen, jede mit ihrem Commit.
4. **Verwandte Seiten** verlinkt die Funktionsseiten (wie Sie es nutzen), die Spezifikationen (jede Einstellung und Klasse), die Hinweise zu Verhaltensänderungen (was Sie beim Upgrade prüfen) und das Changelog des Releases.
5. Ein Monat mit zwei Seiten (`a` und `b`) wurde nach Tagen geteilt, weil er so viel enthielt.

Eine Änderung, die nicht in der Chronik steht, steht im Änderungsverzeichnis. Eine Änderung ohne Nutzen für Anwender, etwa eine Finanzierungsdatei, ist auf der Seite zur Abdeckung als solche gekennzeichnet, mit Begründung.

## So lesen Sie das Änderungsverzeichnis

Öffnen Sie [das Änderungsverzeichnis](aenderungsverzeichnis/README.md), wählen Sie ein Repository und dann einen Monat. Jede Zeile nennt:

| Spalte | Bedeutung |
|---|---|
| Datum und Commit | Wann die Änderung gemacht wurde, und ihr Hash (Kurzform) |
| Art | Hinzugefügt, Aktualisiert, Entfernt, Umbenannt, Korrigiert, Merge oder Sonstiges, aus der Commit-Nachricht abgeleitet |
| Änderung | Die Betreffzeile des Commits, so wie sie im Repository steht |
| Dateien und +/- | Wie viele Dateien, wie viele Zeilen hinzugefügt und entfernt |
| Release | Das Release-Tag, das die Änderung zuerst enthielt, wenn es eines gibt |

Um zu sehen, welches Release in dieser Installation eine Änderung enthält, listen Sie die Tags in Versionsreihenfolge auf:

```bash
git tag -l 'v6.0.*' --sort=version:refname | tail -3
```

Erwartet: die neuesten Tags, endend mit `v6.0.14`. Die Linie 6.0.15 ist in Entwicklung und hat noch kein Tag; ihre
Hinweise stehen im [Changelog von 6.0.15](../../changelogs/6.0/6.0.15.md).

## Die Chronik, Monat für Monat

| Monat | Schlagzeile | Außerdem |
|---|---|---|
| [Dezember 2023](2023/2023-12.md) | Die erste Änderung von außen am Fork wurde am 11. Dezember 2023 zusammengeführt (Upstream-Pull-Request #40). | [Plattform](oekosystem/monate/2023-12.md) [Erweiterungen](erweiterungen/monate/2023-12.md) |
| [Januar 2024, erste Hälfte](2024/2024-01a.md) | 6.0.0 wurde am 1. Januar 2024 stabil. | [Plattform](oekosystem/monate/2024-01.md) [Erweiterungen](erweiterungen/monate/2024-01.md) |
| [Januar 2024, zweite Hälfte](2024/2024-01b.md) | 6.0.1 entstand am 29. Januar 2024. | [Plattform](oekosystem/monate/2024-01.md) [Erweiterungen](erweiterungen/monate/2024-01.md) |
| [Februar 2024](2024/2024-02.md) | 6.0.2 wurde am 29. Februar 2024 veröffentlicht. | [Plattform](oekosystem/monate/2024-02.md) [Erweiterungen](erweiterungen/monate/2024-02.md) |
| [März 2024](2024/2024-03.md) | Die Wurzel des Inhaltsbaums lässt sich anzeigen. | [Plattform](oekosystem/monate/2024-03.md) [Erweiterungen](erweiterungen/monate/2024-03.md) |
| [April 2024](2024/2024-04.md) | 6.0.3 wurde am 2. April 2024 veröffentlicht. | [Plattform](oekosystem/monate/2024-04.md) [Erweiterungen](erweiterungen/monate/2024-04.md) |
| [Mai 2024](2024/2024-05.md) | Keine Änderung an der Hauptinstallation im Mai 2024; die Arbeit fand in den Plattform-Repositorys statt. | [Plattform](oekosystem/monate/2024-05.md) |
| [Juni 2024](2024/2024-06.md) | Ein ruhiger Monat: Zwei Änderungen an `composer.json` hielten die Paketliste der Distribution aktuell. | [Plattform](oekosystem/monate/2024-06.md) |
| [Juli 2024](2024/2024-07.md) | Keine Änderung an der Hauptinstallation im Juli 2024; das Site-Bundle und die Administrationsoberfläche kamen im Upstream voran. | [Plattform](oekosystem/monate/2024-07.md) [Erweiterungen](erweiterungen/monate/2024-07.md) |
| [August 2024](2024/2024-08.md) | Der August 2024 ist der Monat von admin3, einem vollständigen responsiven Design für die Administration (22. August). | [Plattform](oekosystem/monate/2024-08.md) [Erweiterungen](erweiterungen/monate/2024-08.md) |
| [September 2024](2024/2024-09.md) | 6.0.4 wurde am 5. September 2024 veröffentlicht. | [Plattform](oekosystem/monate/2024-09.md) [Erweiterungen](erweiterungen/monate/2024-09.md) |
| [Oktober 2024](2024/2024-10.md) | 6.0.5 wurde am 1. Oktober 2024 veröffentlicht. | [Plattform](oekosystem/monate/2024-10.md) [Erweiterungen](erweiterungen/monate/2024-10.md) |
| [November 2024](2024/2024-11.md) | Ein zweiter Durchgang über admin3 nach Tests auf echten Geräten. | [Plattform](oekosystem/monate/2024-11.md) [Erweiterungen](erweiterungen/monate/2024-11.md) |
| [Dezember 2024](2024/2024-12.md) | 6.0.6 wurde am 17. Dezember 2024 veröffentlicht. | [Plattform](oekosystem/monate/2024-12.md) |
| [Januar 2025](2025/2025-01.md) | 6.0.7 war in Entwicklung. | [Plattform](oekosystem/monate/2025-01.md) [Erweiterungen](erweiterungen/monate/2025-01.md) |
| [Februar 2025](2025/2025-02.md) | 6.0.7 wurde am 1. Februar 2025 veröffentlicht. | [Plattform](oekosystem/monate/2025-02.md) |
| [März 2025](2025/2025-03.md) | Keine Änderung an der Hauptinstallation im März 2025. | [Plattform](oekosystem/monate/2025-03.md) |
| [April 2025](2025/2025-04.md) | Die Unterstützung für PHP 8.4 beginnt, die für PHP 7 endet. | [Plattform](oekosystem/monate/2025-04.md) |
| [Mai 2025](2025/2025-05.md) | 6.0.8 wurde veröffentlicht; die Entwicklung von 6.0.9 begann. | [Plattform](oekosystem/monate/2025-05.md) |
| [Juni 2025](2025/2025-06.md) | 6.0.9 wurde am 10. Juni 2025 veröffentlicht. | [Plattform](oekosystem/monate/2025-06.md) |
| [Juli 2025](2025/2025-07.md) | Lizenz und Urheberschaft wurden mit `COPYRIGHT.md` und `LICENSE.md` ausdrücklich festgehalten (GNU GPL v2 oder später). | [Plattform](oekosystem/monate/2025-07.md) |
| [August 2025](2025/2025-08.md) | Das Produkt wurde am 12. August 2025 in Exponential umbenannt. | [Plattform](oekosystem/monate/2025-08.md) [Erweiterungen](erweiterungen/monate/2025-08.md) |
| [September 2025](2025/2025-09.md) | Aufräumen für PHP 8.1 und 8.2 und Korrekturen für Benachrichtigungs-Mails und den Cronjob der Aufrufzähler. | [Plattform](oekosystem/monate/2025-09.md) [Erweiterungen](erweiterungen/monate/2025-09.md) |
| [Oktober 2025](2025/2025-10.md) | Keine Änderung an der Hauptinstallation im Oktober 2025. | [Plattform](oekosystem/monate/2025-10.md) |
| [November 2025](2025/2025-11.md) | Keine Änderung an der Hauptinstallation im November 2025. | [Plattform](oekosystem/monate/2025-11.md) |
| [Dezember 2025](2025/2025-12.md) | PHP 8.5 wird unterstützt: Zwei Klassen erhielten die Nullable-Typen, die es verlangt, und die Zeta Components wurden angehoben. | [Plattform](oekosystem/monate/2025-12.md) [Erweiterungen](erweiterungen/monate/2025-12.md) |
| [Januar 2026](2026/2026-01.md) | 6.0.12 war in Entwicklung. | [Plattform](oekosystem/monate/2026-01.md) |
| [Februar 2026](2026/2026-02.md) | 6.0.12 wurde am 10. Februar 2026 veröffentlicht. | [Plattform](oekosystem/monate/2026-02.md) |
| [März 2026](2026/2026-03.md) | Die Test-Werkzeugkette zog auf PHPUnit 13 um. | [Plattform](oekosystem/monate/2026-03.md) [Erweiterungen](erweiterungen/monate/2026-03.md) |
| [April 2026](2026/2026-04.md) | 6.0.13 wurde am 18. April 2026 veröffentlicht. | [Plattform](oekosystem/monate/2026-04.md) [Erweiterungen](erweiterungen/monate/2026-04.md) |
| [Mai 2026](2026/2026-05.md) | Ein ruhiger Monat: eine Typkorrektur in PHP für die Diff-Engine der Inhalte und eine neu erzeugte Dateiliste des Releases. | [Plattform](oekosystem/monate/2026-05.md) |
| [Juni 2026, erste Hälfte](2026/2026-06a.md) | MongoDB 8 kam am 1. Juni 2026 als Datenbank hinzu. | [Plattform](oekosystem/monate/2026-06.md) [Erweiterungen](erweiterungen/monate/2026-06.md) |
| [Juni 2026, zweite Hälfte](2026/2026-06b.md) | 6.0.15 war in Entwicklung. | [Plattform](oekosystem/monate/2026-06.md) [Erweiterungen](erweiterungen/monate/2026-06.md) |
| [Juli 2026](2026/2026-07.md) | Die Kommandozeile wurde erwachsen: `ezpm` kam in die Konsole, ein Installer mit einem Befehl und der Kickstarter erschienen, dazu ein Werkzeug zum Zurücksetzen von Passwörtern. | [Plattform](oekosystem/monate/2026-07.md) [Erweiterungen](erweiterungen/monate/2026-07.md) [Velocity](velocity/2026-07.md) |
| [August 2026](2026/2026-08.md) | Template-Debugging sagt die Wahrheit: Pfadkommentare nennen das Template, das wirklich lief, und die Erweiterungsliste sortiert und lädt herunter. | [Erweiterungen](erweiterungen/monate/2026-08.md) [Velocity](velocity/2026-08.md) |
| [September 2026, erste Hälfte](2026/2026-09a.md) | Lange Listen in der Administrationsoberfläche werden seitenweise angezeigt, sind sortierbar und in der Größe einstellbar, sodass Installationen mit Tausenden von Rollen oder Orten wieder funktionieren. | [Plattform](oekosystem/monate/2026-09.md) [Erweiterungen](erweiterungen/monate/2026-09.md) [Velocity](velocity/2026-09a.md) |
| [September 2026, zweite Hälfte](2026/2026-09b.md) | Velocity, ein Webserver mit dauerhaft laufenden Workern für Exponential, kam am 22. September und wurde innerhalb von zwei Tagen der empfohlene Weg zu hoher Geschwindigkeit. | [Plattform](oekosystem/monate/2026-09.md) [Erweiterungen](erweiterungen/monate/2026-09.md) [Velocity](velocity/2026-09b.md) |
| [Oktober 2026](2026/2026-10.md) | 354 Änderungen in zwei Tagen (1. und 2. Oktober), kein Release-Tag; sie gehören zur Linie 6.0.15. | [Erweiterungen](erweiterungen/monate/2026-10.md) [Velocity](velocity/2026-10.md) |

## Die anderen Chroniken

| Teil | Umfasst | Beginnen Sie hier |
|---|---|---|
| Plattform-Ökosystem | Die Symfony-basierten Repositorys rund um Exponential: Administrationsoberfläche, Layouts, Starter, Bridges, Forks | [Übersicht des Ökosystems](oekosystem.md) |
| Erweiterungen | Die Legacy-Erweiterungen, Themes und Pakete, eine Chronik je Repository | [Erweiterungen](erweiterungen/README.md) |
| Velocity | Die Webserver-Engine, vom ersten Commit im Juli 2026 an | [Velocity-Chronik](velocity/README.md), [Installer und Pakete](velocity/installer-und-pakete.md) |
| Release Notes | Eine Seite je Release und je Erweiterung | [6.0.15](../../changelogs/6.0/6.0.15.md), [Erweiterungen](../../changelogs/extensions/README.md) |

## Mit der Geschichte lernen

- **Sie wollen eine Funktion nutzen:** Folgen Sie dem Link von ihrem Monat zur Funktionsseite, oder beginnen Sie bei den [Anleitungen](../anleitungen/README.md).
- **Sie machen ein Upgrade:** Lesen Sie das [Changelog von 6.0.15](../../changelogs/6.0/6.0.15.md), dann die dort verlinkten Hinweise zu Verhaltensänderungen, die neuesten zuletzt.
- **Sie stoßen auf ein Wort, das Sie nicht kennen:** [Glossar](../../eng-us/glossary.md).
- **Sie suchen einen einzelnen Commit:** Durchsuchen Sie das Änderungsverzeichnis des Repositorys, oder führen Sie in der Installation `git log --oneline -S"text"` aus.

## Verwandte Seiten

- [Anleitungen: der Lernpfad](../anleitungen/README.md)
- [Glossar](../../eng-us/glossary.md)
- [Abdeckung: jede Änderung nachgewiesen](abdeckung.md)
- [Änderungsverzeichnis aller Repositorys](aenderungsverzeichnis/README.md)
