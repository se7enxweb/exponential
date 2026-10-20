---
title: Qualitätsschranken und Abnahme
audience: project-manager, developer
category: leadership
order: 100
summary: Die Prüfungen, die Exponential bereits bei jeder Änderung ausführt (CI-Workflows, Testsuiten, Design- und Barrierefreiheitstests, CSP-Berichte), und wie Sie darauf die Qualitätsschranken und die Abnahme Ihres eigenen Projekts aufbauen.
keywords: Qualität, Qualitätsschranke, Abnahme, CI, kontinuierliche Integration, GitHub Actions, PHPUnit, PHPStan, Codestil, Testsuite, Barrierefreiheit, WCAG, Kontrast, axe, CSP, Content Security Policy, Definition of Done, Freigabe, Abnahmetest, quality, gate, acceptance, continuous integration, code style, test suite, accessibility, a11y, contrast, definition of done, sign-off, UAT
type: help
---
# Qualitätsschranken und Abnahme

Diese Seite richtet sich an Projektleiter, die sagen müssen: „Das ist gut genug für die Auslieferung“, und an die
Entwickler, die ihnen die Belege dafür liefern. Sie erklärt, welche Prüfungen das Exponential-Projekt selbst bei jeder
Änderung ausführt, welche davon Ihr eigenes Projekt übernehmen kann und wie Sie daraus Qualitätsschranken und ein
Abnahmeverfahren mit einer klaren Freigabe zusammensetzen.

Eine **Qualitätsschranke** ist ein Punkt im Projekt, an dem die Arbeit nur weitergeht, wenn benannte Prüfungen
bestanden sind. Die **Abnahme** ist die letzte Schranke: Die Personen, die die Site nutzen werden, bestätigen, dass sie
leistet, was vereinbart wurde.

## Inhalt

1. [Was das Projekt bei jeder Änderung prüft](#was-das-projekt-bei-jeder-änderung-prüft)
2. [Die Testsuiten](#die-testsuiten)
3. [Prüfungen der Barrierefreiheit](#prüfungen-der-barrierefreiheit)
4. [Sicherheitsprüfungen: die Content Security Policy und ihre Berichte](#sicherheitsprüfungen-die-content-security-policy-und-ihre-berichte)
5. [Prüfungen an einer laufenden Site](#prüfungen-an-einer-laufenden-site)
6. [Die Schranken Ihres Projekts entwerfen](#die-schranken-ihres-projekts-entwerfen)
7. [Abnahme: Verfahren und Checkliste](#abnahme-verfahren-und-checkliste)
8. [Eine rote Prüfung lesen](#eine-rote-prüfung-lesen)

## Was das Projekt bei jeder Änderung prüft

Das Kernel-Repository `se7enxweb/exponential` führt vier GitHub-Actions-Workflows aus `.github/workflows/` aus. Den
Überblick über sie gibt [Kontinuierliche Integration](../../../en/us/bc/6.0/ci.md); die Prüfungen beschreibt
[Qualitätsprüfungen](../../../en/us/features/6.0/quality-checks.md).

| Workflow | Läuft bei | Was er prüft | Blockiert eine Änderung |
|---|---|---|---|
| **Quality** (`quality.yml`) | Jedem Push auf `main`, jedem Pull-Request dorthin und von Hand | PHP-Syntax geänderter Dateien (PHP 8.3), JavaScript-Syntax geänderter Skripte (Node.js 22), das Dateimanifest `share/filelist.md5`, Commit-Nachrichten, Codestil geänderter Dateien, PHPStan, die Tests der Codestil-Sniffs (PHP 8.4) sowie PHP-8.0-Syntax und -Start | Ja. Der Codestil ist eine Warnung; das Manifest ist bei einem Pull-Request eine Warnung und auf `main` blockierend |
| **PHPUnit** (`phpunit.yml`) | Jedem Push auf `main` und jedem Pull-Request, ohne Pfadfilter | Die Testsuite auf PHP 8.1 bis 8.5 ohne Datenbank und Datenbanktests auf MySQL 8.0 und PostgreSQL 15 | Ja |
| **Performance** (`performance.yml`) | Einer Änderung an PHP, Templates, `settings/`, der deutschen Übersetzung oder `tests/benchmark/` | Ein Mikro-Benchmark auf PHP 8.5 gegen `tests/benchmark/baseline.json` | Nein, nur eine Warnung |
| **CI health** (`ci-health.yml`) | Montags 06:17 UTC und von Hand | Ein Bericht über Erfolgsquoten, die am häufigsten fehlschlagenden Schritte und Tests sowie instabile Läufe | Nein, er berichtet nur |

Was Sie als Führungskraft wissen sollten:

- **Ein Pull-Request wird so getestet, als wäre er in `main` gemergt.** Ein Branch, der grün war, kann rot werden,
  wenn sich `main` weiterbewegt ([CI](../../../en/us/bc/6.0/ci.md)).
- **Kein Pfadfilter für PHPUnit.** Die Tests lesen auch Templates, Stylesheets, Skripte, Übersetzungen, Einstellungen
  und Dokumentation, daher startet eine Änderung an jeder dieser Dateien die Suite ([Qualitätsprüfungen](../../../en/us/features/6.0/quality-checks.md#pull-requests)).
- **Mitwirkende werden nicht durch den Stil der Commit-Nachricht blockiert.** Ein Betreff außerhalb des Formats
  `Added:` / `Updated:` / `Fixed:` / `Removed:` / `Renamed:` ist bei Mitwirkenden eine Warnung; ein Fehler ist er nur
  für die eigenen Autoren des Projekts.
- **Dieselben Prüfungen laufen lokal.** `make hooks` installiert Git-Hooks, die Syntax, Manifest, Codestil und
  Commit-Nachricht bei `git commit` prüfen und PHPStan bei `git push`; `make check` führt Lint, Manifest, phpcs und
  phpstan so aus wie die CI ([Qualitätsprüfungen](../../../en/us/features/6.0/quality-checks.md)).

### Die Ausgangswerte der CI-Gesundheit

Das Projekt hat am 17.10.2026 seine eigene CI für den Zeitraum vom 03.10.2026 bis zum 17.10.2026 gemessen, vor den
Reparaturen jener Woche: 1.540 abgeschlossene Läufe, 61,7 % davon erfolgreich ([CI, „The baseline“](../../../en/us/bc/6.0/ci.md)).
Die Seite empfiehlt, spätere Berichte mit diesen Zahlen zu vergleichen. Den Bericht selbst erzeugt
`python3 .github/scripts/ci_health_report.py --days 14`. Verwenden Sie denselben Bericht für Ihre eigenen
Repositorys, wenn sie auf GitHub Actions laufen: Er ist eine Möglichkeit, die Frage „Wie zuverlässig ist unsere
Pipeline?“ mit einer Zahl zu beantworten.

## Die Testsuiten

`phpunit.xml` gruppiert die Tests in Suiten. Die Namen in der Datei lauten:

| Suite | Deckt ab |
|---|---|
| `security` | sicherheitsbezogene Kernel-Tests |
| `kernel-classes`, `kernel-content`, `kernel-datatypes`, `kernel-search`, `kernel-private` | den Kernel |
| `kernel-contentfinder`, `kernel-contentcheck`, `kernel-logviewer`, `kernel-requestlog` | die Inhaltssuche, die Inhaltsprüfungen, den Log-Viewer und die Request-Logs |
| `lib` | die Bibliotheken in `lib/` |
| `mongodb`, `mongodb-live` | die MongoDB-Schicht; der Live-Teil benötigt einen Server |
| `cjw_newsletter`, `explayouts_admin`, `ezjscore`, `ezmultiupload`, `expservices`, `ezoe`, `maps`, `admin-extensions` | Erweiterungen |
| `quality` | die eigenen Codestil-Sniffs des Projekts |
| `design` | die Designs der Administration: Gestaltungsregeln, Barrierefreiheit, Tastatur, CSP |

Fakten aus [Qualitätsprüfungen](../../../en/us/features/6.0/quality-checks.md#run-only-the-tests-of-a-change):

- Der vollständige Lauf umfasst „mehr als 10.000 Tests“ und dauert auf einem ausgelasteten Rechner lange.
- Für eine alltägliche Änderung genügt es, die Testdateien auszuführen, die die berührten Dateien abdecken; man findet
  sie über `tests/test-map.json`, die Klassennamen und die Pfade. Der vollständige Lauf bleibt die Prüfung vor einem
  Release.
- Tests, die eine laufende Datenbank oder einen Webserver benötigen, lesen deren Adresse aus Umgebungsvariablen und
  werden ohne sie übersprungen; ein einfaches `vendor/bin/phpunit` läuft daher auch auf einem Rechner ohne diese
  Server erfolgreich durch.

Für Ihr Projekt: Ihre eigenen Erweiterungen können im selben Stil eigene Tests mitbringen. Fragen Sie Ihre
Entwickler, welche Suite welche Ihrer Funktionen abdeckt, und nehmen Sie diese Liste in die Definition der Schranke auf.

## Prüfungen der Barrierefreiheit

Das Design admin4 folgt WCAG-Regeln, die von Tests geprüft werden ([Sprachen und Barrierefreiheit](../wer-ist-die-exponential-foundation.md#sprachen-und-barrierefreiheit)).
Diese Tests liegen in `tests/tests/design/` und laufen bei jeder Änderung in der Suite `design`:

| Test | Was er prüft | Quelle |
|---|---|---|
| `AdminTokenContrastTest` | Kontrast der Farb-Tokens nach der WCAG-2-Formel, hell und dunkel: Text 4,5:1, Fokusring und Feldränder 3:1 | die Testdatei |
| `AdminLandmarksTest` | Sprunglink, Landmarks, genau ein Hauptbereich (main landmark) | [Design admin4](../../../en/us/features/6.0/admin4-design.md) |
| `AdminKeyboardTest` | Das Markup und die Hooks, auf die die Tastaturbedienung angewiesen ist | [Design admin4](../../../en/us/features/6.0/admin4-design.md) |
| `AdminFormLabelsTest`, `AdminFormErrorsTest` | Jedes Formularfeld hat einen Namen; Fehler werden angesagt | [Die Administration gestalten](../../../en/us/bc/6.0/admin4-styling.md#code-review-checklist) |
| `StandardContentEditAccessibleNamesTest` | Jedes Bedienelement des öffentlichen Bearbeitungsformulars hat einen zugänglichen Namen | die Testdatei |
| `AdminStylingRulesTest` | Kein Inline-CSS in Templates der Administration; Tokens mit Werten für den dunklen Modus | [Die Administration gestalten](../../../en/us/bc/6.0/admin4-styling.md) |

**axe.** Das Changelog verzeichnet manuelle Prüfungen mit der Barrierefreiheits-Engine axe, zum Beispiel „axe meldet
keinen schwerwiegenden Befund im Seitenrahmen auf 10 klassischen Seiten bei 1440 px und bei 960 px mit 200 % Zoom,
auf beiden Webservern“ ([Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)). axe gehört nicht zu den
CI-Workflows. Wenn Ihre Abnahme ein automatisiertes Audit Ihrer eigenen Seiten erfordert, planen Sie es als eigene
Aufgabe mit eigenem Werkzeug und eigenem Verantwortlichen.

Die [Checkliste für Code-Reviews](../../../en/us/bc/6.0/admin4-styling.md#code-review-checklist) für Templates der
Administration verlangt eine Prüfung „im hellen und dunklen Modus bei 1440 px und bei 960 px mit Skalierung 2: keine
helle Karte auf der dunklen Seite, Textkontrast mindestens 4,5:1, kein horizontaler Überlauf, keine Konsolenfehler“.
Sie ist ein gutes Vorbild für Ihre eigene Frontend-Schranke.

Sprachen mit Schreibrichtung von rechts nach links gehören für viele Zielgruppen zur Barrierefreiheit: siehe
[Von rechts nach links](../../../en/us/features/6.0/right-to-left.md) und
[Checklisten zu Barrierefreiheit, Sicherheit und Compliance](checklisten-barrierefreiheit-sicherheit-und-compliance.md).

## Sicherheitsprüfungen: die Content Security Policy und ihre Berichte

Exponential sendet eine Content Security Policy aus `site.ini [ContentSecurityPolicy]`; standardmäßig ist sie in der
Administration im Modus „nur berichten“ (report-only) und auf den Sites ausgeschaltet
([Content Security Policy](../../../en/us/features/6.0/content-security-policy.md)). Der Kernel sammelt die Berichte des
Browsers selbst: Die View `csp/report` schreibt je Verstoß eine Zeile in `var/log/csp.log`, die der Log-Viewer als
**Berichte der Content Security Policy** auflistet ([Berichte](../../../en/us/features/6.0/content-security-policy.md#reports)).

So erhalten Sie eine messbare Schranke, bevor Sie eine Richtlinie auf `enforce` umstellen:

1. Lassen Sie die Richtlinie im Modus `report-only` laufen, während die Redakteure für einen vereinbarten Zeitraum
   normal arbeiten.
2. Lesen Sie die Fehlerübersicht des Logs `csp` (**Setup > Logdateien > Fehlerübersicht**), gruppiert nach Direktive
   und blockierter Quelle ([Log-Viewer](../../../en/us/features/6.0/log-viewer.md#error-digest)).
3. Die Schranke ist bestanden, wenn kein unerklärter Verstoß mehr übrig ist. Folgen Sie dann „Schrittweise auf
   enforce umstellen“ auf der Seite [Content Security Policy](../../../en/us/features/6.0/content-security-policy.md).

Der Test `AdminContentSecurityPolicyTest` in der Suite `design` hält die Templates der Administration mit einer
strengen Richtlinie verträglich.

## Prüfungen an einer laufenden Site

Diese Prüfungen brauchen keinen Entwickler und können ab der Schranke „Staging bereit“ Teil jeder Schranke sein.

| Prüfung | Wo | Bestanden, wenn | Quelle |
|---|---|---|---|
| Gesundheitsprüfungen | **Setup > Systeminformationen** | Kein Fehlschlag; Warnungen erklärt | [Systeminformationen](../../anleitungen/systeminformationen.md#3-zustandsprüfungen) |
| Dateikonsistenz | **Setup > Upgrade-Prüfung** | Nur absichtlich geänderte Dateien werden aufgeführt | [Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md) |
| Datenbankkonsistenz | **Setup > Upgrade-Prüfung** | Kein Unterschied oder nur Hinweise zur Datenbank-Engine | ebenda |
| Defekte Relationen, fehlende Dateien | **Setup**, Inhaltsprüfungen | Nichts Unerklärtes | [Inhaltsprüfungen](../../../en/us/features/6.0/content-checks.md) |
| Fehler | **Setup > Logdateien > Fehlerübersicht** | Keine neue Gruppe, die Sie nicht erklären können | [Log-Viewer](../../../en/us/features/6.0/log-viewer.md#error-digest) |
| Audit-Kette | Tab **Audit** oder `./console exp:audit verify` | Jeder Kanal intakt | [Audit-Protokoll](../../../en/us/features/6.0/audit-trail.md) |
| Cronjobs | **Setup > Cronjobs**, Letzte Läufe | Läufe sind OK; Probleme erklärt | [Cronjobs](../../anleitungen/cronjobs.md#10-letzte-ausführungen-und-wie-probleme-gezählt-werden) |
| Performance | `./console exp:benchmark --baseline=<file>` | Exit-Status 0, keine Verschlechterung | [Benchmarking](../../anleitungen/benchmarking.md#9-läufe-vergleichen-baselines-und-regressionen) |
| Sicherheitshärtung | Die Checkliste der Hilfeseite | Jede Zeile abgehakt oder bewusst ausgenommen | [Sicherheitshärtung](../sicherheitshaertung.md#checkliste) |

## Die Schranken Ihres Projekts entwerfen

Ein einfaches Modell mit vier Schranken. Passen Sie Namen und Prüfungen an; behalten Sie die Regel bei, dass eine
Schranke benannte Prüfungen hat und einen benannten Verantwortlichen, der „bestanden“ sagt.

| Schranke | Wann | Prüfungen | Verantwortlich |
|---|---|---|---|
| G1: Änderung bereit | Jeder Pull-Request Ihres eigenen Codes | Ihre CI grün (oder die Prüfungen des Projekts lokal mit `make check` ausgeführt); Review erledigt; Tests der Änderung bestanden | Leitender Entwickler |
| G2: Staging bereit | Bevor Redakteure den Build sehen | G1 für jede Änderung; Site-Prüfungen aus der Tabelle oben auf Staging; Inhaltsmodell entspricht dem vereinbarten Modell | Leitender Entwickler und Administrator |
| G3: Abnahme | Vor dem Livegang | Abnahme-Checkliste unten unterzeichnet; Schranken für Barrierefreiheit und CSP bestanden; Performance-Ausgangswerte gespeichert | Projektleiter mit dem Product Owner |
| G4: Live | Nach dem Livegang | Livegang-Checkliste abgearbeitet; Prüfungen der ersten Woche aus [10.15](../../installation/10-nach-der-installation.md#1015-eine-checkliste-für-die-erste-woche) erledigt | Administrator |

> **Tipp**
> Schreiben Sie die Definition jeder Schranke gleich zu Beginn in den Projektauftrag ([Projektvorlagen](projektvorlagen.md)).
> Eine Schranke, die erst kurz vor dem Termin definiert wird, ist eine Verhandlung, keine Schranke.

### Definition of Done für eine Änderung

```text
Definition of done: <project name>
[ ] The change does what its ticket says; the ticket names how it was tested
[ ] php -l passes for every touched PHP file
[ ] Commit subject starts with Added: / Updated: / Fixed: / Removed: / Renamed:
[ ] Tests that cover the change pass; new behavior has a test, or the ticket says why not
[ ] Template changes: no inline CSS; checked in light and dark mode at 1440 px and 960 px at 200 %
[ ] Settings changes are in version control, not written on the server
[ ] Documentation updated where users or administrators will notice the change
[ ] Reviewed by a second person
```

## Abnahme: Verfahren und Checkliste

1. **Vereinbaren Sie die Szenarien früh.** Jede Anforderung aus dem Projektauftrag erhält mindestens ein Szenario:
   „Als Redakteur lege ich einen Artikel in zwei Sprachen an und veröffentliche ihn.“
2. **Bereiten Sie die Abnahme-Site vor.** Eine Kopie mit realistischen Inhalten, den echten Rollen und den echten
   Siteaccesses. Geben Sie jedem Tester ein Konto mit der Rolle, die er auf der Live-Site haben wird, niemals ein
   gemeinsam genutztes Administratorkonto.
3. **Spielen Sie die Szenarien durch.** Die Tester notieren „bestanden“ oder „nicht bestanden“ mit der Adresse der
   Seite und, bei einer Fehlerseite, der unten angezeigten Fehlerreferenz ([Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)).
4. **Bewerten Sie.** Jeder Fehlschlag wird zu „vor dem Livegang beheben“, „nach dem Livegang beheben“ oder „kein
   Mangel“. Ein Fehler in Exponential selbst wird an das Projekt gemeldet ([Mit der Community arbeiten](mit-der-community-arbeiten.md)).
5. **Geben Sie frei.** Der Product Owner unterzeichnet die Checkliste; der Projektleiter legt sie zum
   Übergabedokument.

```text
Acceptance checklist: <project name>, build <version or commit>, date <date>
Testers: <roles>          Site: <acceptance copy address>

Content and editing
[ ] Every content class of the model can be created, edited, previewed and published by its role
[ ] Versions: a previous version can be viewed and restored
[ ] Translations: every agreed language can be added and published
[ ] Media: images and files upload; alt text can be entered
[ ] Layouts (if used): a layout can be edited, previewed and published

Roles and access
[ ] Each role sees only what it should; a forbidden page shows the access message, not an error
[ ] No tester needed the Administrator role for their daily tasks

Site
[ ] Every siteaccess answers; language switch works; right-to-left languages read right to left
[ ] Search finds new content
[ ] Forms and mail reach their recipients

Quality
[ ] System information shows no failure
[ ] Error digest shows nothing unexplained after the test period
[ ] CSP reports reviewed
[ ] Accessibility checks of your project passed
[ ] Performance baseline saved

Sign-off: <product owner role>   Result: accepted / accepted with listed defects / rejected
```

## Eine rote Prüfung lesen

Wenn ein Workflow des Projekts rot ist, erklärt [CI](../../../en/us/bc/6.0/ci.md) den Entwicklern, wie sie den
fehlgeschlagenen Schritt finden (`gh run view <id> --log-failed`) und denselben Befehl auf ihrem Rechner ausführen. Was
Sie als Führungskraft wissen sollten:

- **Ein Pull-Request, der mit `main` in Konflikt steht, zeigt überhaupt keine Prüfungen.** Die Lösung ist, `main` in
  den Branch zu mergen, nicht etwas erneut auszuführen.
- **Ein abgebrochener Lauf bei einem Pull-Request ist kein Fehlschlag**: Ein neuerer Push hat ihn ersetzt.
- **Der erste Pull-Request neuer Mitwirkender wartet auf die Genehmigung eines Maintainers**, bevor irgendein
  Workflow läuft.
- **Performance ist eine Warnung.** Ein langsamerer Benchmark blockiert keine Änderung; er verlangt nur einen
  genaueren Blick.

## Verwandte Seiten

- [Kontinuierliche Integration](../../../en/us/bc/6.0/ci.md) und [Qualitätsprüfungen](../../../en/us/features/6.0/quality-checks.md)
- [Die Administration gestalten](../../../en/us/bc/6.0/admin4-styling.md), [Content Security Policy](../../../en/us/features/6.0/content-security-policy.md)
- [Benchmarking](../../anleitungen/benchmarking.md), [Systeminformationen](../../anleitungen/systeminformationen.md)
- [Release- und Upgrade-Planung](release-und-upgrade-planung.md), [Risikomanagement](risikomanagement.md)
- [Checklisten zu Barrierefreiheit, Sicherheit und Compliance](checklisten-barrierefreiheit-sicherheit-und-compliance.md)
- [Projektvorlagen](projektvorlagen.md), [Ein Team mit Git führen](ein-team-mit-git-fuehren.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
