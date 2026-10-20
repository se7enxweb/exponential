---
title: "Zum Projekt beitragen: alle Wege, Exponential mitzugestalten"
audience: user, editor, admin, developer, designer, partner, supporter
category: project
order: 60
summary: Wie Sie sich an der Entwicklung von Exponential beteiligen, nach der FAQ „Support Exponential!“ und den Community-Seiten von exponential.earth sowie der CONTRIBUTING.md des Kernels - Code über Pull-Requests, die Lizenz Ihrer Beiträge, Tests und Fehlermeldungen, Übersetzungen und das Exponential Translation Project, Dokumentation, Antworten in der Community, Erweiterungen, Composer-Pakete und Site-Pakete - jeweils mit dem ersten Schritt.
keywords: beitragen, Beitrag, Mitwirkende, Pull-Request, Fork, Branch, Verhaltenskodex, Commit-Nachricht, Tests, Manifest, Fehlermeldung, Testen, Übersetzung, Übersetzungsprojekt, Dokumentation, Foren, Erweiterungen, Site-Pakete, Lizenz, contribute, contributing, contributor, pull request, fork, branch, GitHub, CONTRIBUTING.md, code of conduct, commit message, tests, manifest, bug report, testing, translation, Exponential Translation Project, documentation, GFDL, eZpedia, forums, extensions, composer.json, Packagist, site packages, license, GPL
featured: true
type: help
---
# Zum Projekt beitragen: alle Wege, Exponential mitzugestalten

„Unabhängig von Ihrer Erfahrung“, so die FAQ [Support Exponential!](https://exponential.earth/faq/support) der
Website, können Sie zu Exponential beitragen. Die Community-Seite fügt hinzu, dass das Projekt Pull-Requests aus der
Community inzwischen ausdrücklich begrüßt. Diese Seite listet die Einstiegswege auf, von denen, die gar keinen Code
brauchen, bis zu Änderungen am Kernel, jeweils mit dem ersten Schritt.

## Wählen Sie Ihren Einstieg

| Sie können | Beginnen Sie hier | Sie brauchen |
|---|---|---|
| Die Frage eines anderen beantworten | [Foren von Share Exponential](https://share.exponential.earth/forums), [Telegram](https://t.me/exponentialcms) | Erfahrung mit Exponential |
| Einen Fehler gut melden | [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#einen-fehler-in-exponential-melden) | Ein kostenloses GitHub-Konto |
| Ein Release oder einen Pull-Request testen | [Eine Änderung vor dem Release testen](#änderungen-vor-dem-release-testen) | Eine Testinstallation |
| Übersetzen | [Übersetzungen](#übersetzungen) | Ihre Muttersprache |
| Dokumentation schreiben oder korrigieren | [Dokumentation](#dokumentation) | Klares Schreiben |
| Einen Fehler beheben oder eine Funktion hinzufügen | [Code](#code-pull-requests-an-den-kernel-und-die-erweiterungen) | PHP, Git |
| Eine Erweiterung oder ein Design teilen | [Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md) | Eine Erweiterung, die auch andere nutzen könnten |
| Einer Erweiterung eine `composer.json` hinzufügen | [Composer-Pakete](#composer-pakete-und-site-pakete) | Grundkenntnisse in Composer |
| Die Arbeit finanzieren | [Geben und spenden](geben-und-spenden.md) | |

## Code: Pull-Requests an den Kernel und die Erweiterungen

Die Website beschreibt den Weg: ein GitHub-Konto anlegen, das Repository forken, die Korrektur in einem Branch
vornehmen und einen Pull-Request an das Repository von 7x schicken, wo er geprüft und nach dem Merge für alle
veröffentlicht wird. Im Einzelnen:

1. **Lesen Sie die Regeln.** `CONTRIBUTING.md` im Hauptverzeichnis der Installation und den Verhaltenskodex, auf den
   sie verweist.
2. **Arbeiten Sie in einem Klon.** Forken Sie [se7enxweb/exponential](https://github.com/se7enxweb/exponential)
   (oder das Repository der Erweiterung), klonen Sie Ihren Fork und legen Sie einen Branch für eine einzelne Änderung
   an. [Mit Git entwickeln](../mit-git-entwickeln.md) zeigt, wie Sie aus einer Installation Klone machen, in denen Sie
   arbeiten können.
3. **Halten Sie die Änderung klein und fokussiert.** Ein Pull-Request pro Änderung. Folgen Sie dem Codestil der
   umliegenden Dateien; die Website verweist außerdem auf die
   [Exponential-Codestandards](https://share.exponential.earth/learn/exponential-code-standards).
4. **Schreiben Sie die Commit-Nachricht so, wie das Projekt es tut.** Der Betreff beginnt damit, was die Änderung
   tut: `Added:`, `Updated:`, `Fixed:`, `Removed:` oder `Renamed:`, zum Beispiel
   `Fixed: The redirect keeps the query string`.
5. **Halten Sie die Prüfungen grün.** Führen Sie in Ihrem Klon einmal `make hooks` aus; die Hooks prüfen dann bei
   jedem Commit die Commit-Nachricht, die PHP-Syntax, den Codestil und das Dateimanifest (`share/filelist.md5`, zu
   korrigieren mit `make manifest-fix`). Führen Sie die Tests aus und aktualisieren Sie die Dokumentation, wenn Ihre
   Änderung das erfordert.
6. **Eröffnen Sie den Pull-Request** und beschreiben Sie, was Sie geändert haben, warum und wie Sie es getestet
   haben. Nennen Sie das Issue, das er behebt (`Fixes #123`).

Die Continuous Integration des Projekts läuft bei jedem Pull-Request ([CI](../../../eng-us/bc/6.0/ci.md)). Das Projekt mergt
jeden Pull-Request und behält die Commits der Mitwirkenden so, wie sie geschrieben wurden; was zu korrigieren ist,
wird darauf aufbauend korrigiert
([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#mitwirkende-und-pull-requests)).

### Die Lizenz Ihres Beitrags

Tragen Sie zu denselben Bedingungen bei wie alle anderen: unter der GNU GPL Version 2 oder später. Die FAQ der
Website bittet darum, damit Beiträge mit dem übrigen Code vereinbar bleiben und das Projekt als Ganzes auf spätere
Versionen der GPL wechseln kann; `CONTRIBUTING.md` sagt dasselbe. Das Urheberrecht an Ihrem Code behalten Sie. Wenn
Sie einen Beitrag nicht so lizenzieren können, reichen Sie ihn nicht ein
([Fragen zur Lizenz](lizenz-faq.md#darf-ich-code-ausschließlich-unter-gpl-version-3-oder-ausschließlich-unter-gpl-version-2-beitragen)).

## Änderungen vor dem Release testen

Jede Änderung erreicht `main` auf GitHub lange bevor sie in einem Release steckt. `main` auf einer Testinstallation
zu betreiben und zu melden, was nicht mehr funktioniert, ist einer der nützlichsten Beiträge überhaupt.
[Mit Git aktualisieren](../mit-git-aktualisieren.md) zeigt, wie Sie `main` folgen und wie Sie zu einem Release
zurückkehren. Melden Sie, was Sie finden, im [Issue-Tracker](https://github.com/se7enxweb/exponential/issues), mit
dem Commit, den Sie getestet haben.

## Übersetzungen

Das [Exponential Translation Project](https://exponential.earth/about/etp) (ETP) der Website hat die
Übersetzungsarbeit für die Exponential-Familie wieder aufgenommen: zuerst eine Bestandsaufnahme der vorhandenen
Übersetzungen und ihrer Vollständigkeit, dann neue Sprachen, mit dem Ziel, dass jede gebräuchliche Sprache eine
fertige Übersetzung hat.

In diesem Repository sind die Übersetzungen die Dateien `share/translations/<locale>/translation.ts`; den Stand jeder
Sprache und welche vollständig sind, finden Sie unter [Sprachen](../../../eng-us/languages.md). Wie Sie übersetzen und testen:
[Übersetzungen beitragen](../unterstuetzer/uebersetzungen-beitragen.md).

## Dokumentation

Die Dokumentation, die Sie gerade lesen, ist der Ordner `doc/` des Kernels, unter der GNU Free Documentation
License; eine Korrektur ist ein Pull-Request wie jeder andere ([Freie Dokumentation](../freie-dokumentation.md)). Das
Wiki der Community, [eZpedia](https://ezpedia.exponential.earth/), steht allen offen, die sich anmelden. Auch die
Roadmap bittet um Hilfe bei den Dokumentations-Websites ([Die Roadmap](roadmap.md#die-community-websites)). Mehr:
[Dokumentation beitragen](../unterstuetzer/dokumentation-beitragen.md).

## Composer-Pakete und Site-Pakete

Die offizielle Roadmap sucht Freiwillige für zwei Aufgaben ([Die Roadmap](roadmap.md#zwei-aufrufe-an-freiwillige)):

- **Composer-Pakete**: Vielen älteren Repositories von Erweiterungen fehlen noch eine `composer.json` und ein Eintrag
  auf Packagist. Sie hinzuzufügen ist pro Erweiterung ein kleiner, klar umrissener Pull-Request
  ([Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md#4-composerjson-schreiben)).
- **Site-Pakete**: Das Projekt möchte Site-Pakete aus der Community in der Liste des Setup-Assistenten
  ([Ihr Site-Paket beitragen](../../../eng-us/help/site-packages/contribute-your-site-package.md)).

## In der Community helfen

Beantworten Sie Fragen in den Foren und Chats, schreiben Sie einen Artikel auf Share Exponential, melden Sie einen
defekten Link auf einer Website des Projekts im [Projekt-Tracker](https://github.com/se7enxweb/exponential-community/issues)
oder erzählen Sie anderen von Exponential. Das kostet kein Geld und hält die Community lebendig. Weitere Ideen:
[In der Community helfen](../unterstuetzer/in-der-community-helfen.md) und der
[Bereich für Fürsprecher](../../../eng-us/help/advocates/advocate-toolkit.md).

## Verwandte Seiten

- [Erweiterungen zurückgeben](../erweiterungen-zurueckgeben.md)
- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Wege, Exponential zu unterstützen](../unterstuetzer/wege-exponential-zu-unterstuetzen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt
  und verbessert werden.
