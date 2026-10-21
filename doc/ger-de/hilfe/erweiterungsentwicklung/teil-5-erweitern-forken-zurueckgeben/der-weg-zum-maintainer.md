---
title: Der Weg zum Maintainer
audience: developer, app-creator
category: extension-development
order: 140
summary: Was es Woche für Woche bedeutet, eine Erweiterung zu pflegen - Issues, Pull Requests, Releases, PHP- und Kernel-Updates, Sicherheitsmeldungen, Übergabe - und eine Routine, die den Aufwand bei wenigen Stunden im Monat hält.
keywords: Maintainer, Pflege, Wartung, Issues, Pull Requests, Releases, Sicherheit, Routine, Übergabe, Verantwortung, Community, maintainer, maintenance, issues, pull requests, releases, security, routine, hand-over, responsibility, community
type: help
---
# Der Weg zum Maintainer

Teil 5, Kapitel 14. Eine Erweiterung zu schreiben dauert Tage. Sie zu pflegen dauert Jahre. Ein Maintainer ist die
Person, auf die sich Benutzer verlassen können, dass sie antwortet, korrigiert und veröffentlicht. Dieses Kapitel
beschreibt die Aufgabe, damit Sie sich mit offenen Augen dafür entscheiden können, und gibt eine Routine vor, die sie
überschaubar hält.

## Die Stufen des Weges

Die meisten Maintainer haben nicht geplant, einer zu werden. Der Weg sieht meist so aus:

| Stufe | Sie ... |
|---|---|
| Benutzer | installieren Erweiterungen und melden Probleme ([Gute Issues schreiben](gute-issues-schreiben.md)) |
| Beitragender | schicken Korrekturen als Pull Requests ([Mit Pull Requests zurückgeben](mit-pull-requests-zurueckgeben.md)) |
| Reviewer | probieren die Pull Requests anderer aus und kommentieren sie ([Die Arbeit anderer prüfen](die-arbeit-anderer-pruefen.md)) |
| Co-Maintainer | können mergen, während der ursprüngliche Maintainer noch da ist |
| Maintainer | entscheiden, was hineinkommt, und veröffentlichen |

Jede Stufe ist für sich nützlich. Niemand muss den ganzen Weg gehen.

## Was die Aufgabe umfasst

| Aufgabe | Wie oft | Anmerkungen |
|---|---|---|
| Neue Issues lesen und beantworten | wöchentlich | auch „Danke, bestätigt“ hilft der meldenden Person |
| Pull Requests prüfen und mergen | wöchentlich | kleine schnell; große mit einem Plan |
| Veröffentlichen | wenn es etwas gibt, das sich zu veröffentlichen lohnt | [Teil 7: Ihre Erweiterung veröffentlichen](../teil-7-veroeffentlichen/README.md) |
| PHP-Releases verfolgen | jährlich (PHP hat ein Minor-Release pro Jahr) | die neue Version in die CI aufnehmen, ihre Deprecations beheben |
| Kernel-Releases verfolgen | bei jedem Release | die Verhaltensänderungen lesen; testen |
| Sicherheitsmeldungen bearbeiten | sobald sie kommen, schnell und vertraulich | siehe unten |
| Die Dokumentation aktuell halten | bei jeder Änderung, die Benutzer betrifft | |
| Übergeben, wenn Sie aufhören | einmal | siehe unten |

## Eine Routine für wenige Stunden im Monat

- **Ein fester Termin pro Woche**, etwa eine Stunde am Montag, für Issues und Pull Requests. Benutzer lernen, wann sie
  mit Antworten rechnen können.
- **Labels** für Issues: `bug`, `feature`, `question`, `help wanted`, `good first issue`. Die letzten beiden laden
  Beitragende ein.
- **CI bei jedem Push**
  ([Tests und CI für eine aktualisierte Erweiterung](../teil-4-aktualisieren/tests-und-ci-fuer-eine-aktualisierte-erweiterung.md)),
  damit Sie Ergebnisse prüfen, statt Tests von Hand auszuführen.
- **Kleine Releases.** Veröffentlichen Sie, wenn eine Korrektur übernommen ist; sammeln Sie nicht monatelang.
- **Beobachten Sie das Repository des Kernels** auf Releases (GitHub: **Watch > Custom > Releases**).

## Sicherheitsmeldungen

Jemand meldet vielleicht eine Schwachstelle in Ihrer Erweiterung. Dann:

1. Bedanken Sie sich und bestätigen Sie den Eingang, vertraulich.
2. Besprechen Sie sie nicht in öffentlichen Issues, bis eine Korrektur veröffentlicht ist.
3. Beheben Sie sie, veröffentlichen Sie und sagen Sie in den Release Notes, dass ein Sicherheitsproblem behoben wurde
   und was Benutzer tun müssen.
4. Nennen Sie die meldende Person, wenn sie einverstanden ist.

Legen Sie eine kurze `SECURITY.md` in Ihr Repository, die sagt, wie man vertraulich meldet. GitHub kann für ein
Repository in dessen Einstellungen außerdem die vertrauliche Meldung von Schwachstellen (private vulnerability
reporting) einschalten.

## Nein sagen

Ein Maintainer sagt öfter Nein als Ja: zu Funktionen, die nicht passen, zu Änderungen, die Benutzer beeinträchtigen
würden, zu Code, den niemand pflegen kann. Sagen Sie es freundlich und mit Begründung. Ein klares Nein ist besser als
ein Pull Request, der ein Jahr lang offen bleibt.

## Übergeben

Wenn Sie keine Zeit mehr haben, sagen Sie es in der README („looking for a maintainer“) und in einem angehefteten
Issue. Wenn sich jemand anbietet, nehmen Sie ihn als Co-Maintainer auf, lassen Sie ihn einmal gemeinsam mit Ihnen
veröffentlichen und übergeben Sie dann. Übertragen Sie das Repository und das Packagist-Paket, wenn Sie beide
einverstanden sind. Das ist das freundliche Ende des Weges; die Alternative ist eine verwaiste Erweiterung, die jemand
anderes übernehmen muss ([Eine verwaiste Erweiterung übernehmen](eine-verwaiste-erweiterung-uebernehmen.md)).

## Verwandte Seiten

- Zurück: [Eine verwaiste Erweiterung übernehmen](eine-verwaiste-erweiterung-uebernehmen.md)
- Weiter: [Die Community durch Code wachsen lassen](die-community-durch-code-wachsen-lassen.md)
- [Der Wartungslebenszyklus](../teil-7-veroeffentlichen/der-wartungslebenszyklus.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
