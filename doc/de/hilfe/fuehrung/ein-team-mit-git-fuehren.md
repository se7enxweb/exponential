---
title: Ein Team mit Git führen
audience: project-manager, developer
category: leadership
order: 130
summary: Wie ein Projektteam mit Git an einer Exponential-Site arbeitet - Repositorys, Branches, Pull-Requests und Reviews, Commit-Nachrichten, Einstellungen unter Versionsverwaltung, Deployments und warum ein veröffentlichter Tag unveränderlich ist.
keywords: Git, Team, Branch, Branching, Pull-Request, Review, Merge, Tag, Release, Version, unveränderlich, Commit-Nachricht, Hooks, Manifest, Deployment, ManagedByDeployment, Composer, Worktree, git_manager, Arbeitsablauf, git, team, branching, pull request, merge, immutable, commit message, deploy, workflow
type: help
---
# Ein Team mit Git führen

Diese Seite richtet sich an Projektleiter, die ein Team führen, das eine Exponential-Site baut oder pflegt, und an die
Entwickler in diesem Team. Sie macht aus der Git-Praxis des Projekts einen Arbeitsablauf für das Team: wie die
Repositorys aufgebaut sind, wie Arbeit von einem Branch auf die Live-Site gelangt, wer prüft und wer veröffentlicht und
welche Regeln das Team vor Fehlern schützen, die sich nicht rückgängig machen lassen.

Sie müssen keine Git-Befehle eingeben, um diese Seite zu nutzen. Wo ein Befehl wichtig ist, wird er gezeigt, damit Sie
ihn beim Namen nennen können. Das Tutorial für Entwickler ist [Mit Git entwickeln](../mit-git-entwickeln.md).

## Inhalt

1. [Warum Git, und warum Klone](#warum-git-und-warum-klone)
2. [Wie eine Exponential-Site in Git aufgebaut ist](#wie-eine-exponential-site-in-git-aufgebaut-ist)
3. [Ein Branching-Modell für ein Site-Team](#ein-branching-modell-für-ein-site-team)
4. [Pull-Requests und Reviews](#pull-requests-und-reviews)
5. [Commit-Nachrichten](#commit-nachrichten)
6. [Die Prüfungen, die jeder Commit besteht](#die-prüfungen-die-jeder-commit-besteht)
7. [Einstellungen und Geheimnisse](#einstellungen-und-geheimnisse)
8. [Vom Branch zur Live-Site](#vom-branch-zur-live-site)
9. [Tags sind unveränderlich](#tags-sind-unveränderlich)
10. [Dem Upstream folgen](#dem-upstream-folgen)
11. [Rollen im Git-Arbeitsablauf](#rollen-im-git-arbeitsablauf)
12. [Checkliste für die Einrichtung des Teams](#checkliste-für-die-einrichtung-des-teams)

## Warum Git, und warum Klone

„Sie bauen Websites mit Exponential? Der empfohlene Weg ist, den Kernel und jede se7enxweb-Erweiterung als Git-Klone
mit ihrer vollständigen Historie zu beziehen“ (README, zitiert unter [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#git-zuerst)).

Was Sie als Führungskraft von dieser Entscheidung haben, in den Worten von [Mit Git entwickeln](../mit-git-entwickeln.md#warum-git-klone-und-keine-archive):

| Frage, die Sie stellen werden | Mit Release-Archiven | Mit Git-Klonen |
|---|---|---|
| Was haben wir geändert? | `composer status -v` nennt geänderte Dateien | `git status` und `git diff` zeigen jede Änderung, Zeile für Zeile |
| Warum steht diese Zeile hier? | Nicht möglich | `git log` und `git blame` zeigen, wer sie wann und warum geändert hat |
| Welche Änderung hat es kaputt gemacht? | Nicht möglich | `git bisect` findet sie in wenigen Schritten |
| Können wir eine Korrektur nutzen, bevor sie veröffentlicht ist? | Dateien von Hand kopieren | `git cherry-pick` |
| Sind unsere eigenen Änderungen beim nächsten Update sicher? | Beim nächsten Update verloren | Auf unserem eigenen Branch erhalten; Composer weigert sich, sie zu überschreiben |
| Können wir eine Korrektur zurückschicken? | Nicht möglich | Einen Branch in einen Fork pushen und einen Pull-Request öffnen |

## Wie eine Exponential-Site in Git aufgebaut ist

Eine Installation besteht aus **mehreren Repositorys**, nicht aus einem:

- **Der Kernel**, [se7enxweb/exponential](https://github.com/se7enxweb/exponential), im Wurzelverzeichnis der Installation.
- **Jede Erweiterung**, in `extension/<name>`, mit eigenem Repository und eigener Historie. Auf `main` klont
  `composer install` 67 Repositorys ([Mit Git entwickeln](../mit-git-entwickeln.md#eine-neue-site)).
- **Ihre eigene Arbeit**: Ihre eigenen Erweiterungen und Designs sowie die Einstellungen Ihrer Site.

`git status` im Wurzelverzeichnis der Installation listet die Erweiterungen als nicht verfolgte Ordner auf, weil sie
eigene Repositorys sind; das ist so gewollt.

Entscheiden Sie früh, und halten Sie im Projektauftrag fest, wo Ihr **eigener** Code liegt:

| Option | Passt zu | Worauf zu achten ist |
|---|---|---|
| Ein Branch des Kernels (`my-site`), der die Ordner Ihrer eigenen Erweiterung und Ihres Designs enthält | Ein kleines Team, eine Site | Ihr Branch erhält auch jeden Merge aus dem Upstream; halten Sie Ihre Commits getrennt von den Merges |
| Ihre eigene Erweiterung in einem eigenen Repository, installiert per Composer | Mehrere Sites oder Code, den Sie wiederverwenden | Veröffentlichen Sie sie mit Versionen wie jede Erweiterung ([Erweiterungen, „Release it“](../../anleitungen/extensions.md#7-die-extension-veröffentlichen)) |
| Beides: Site-Einstellungen auf dem Kernel-Branch, wiederverwendbarer Code in Erweiterungen | Die meisten Agenturprojekte | Dokumentieren Sie im Übergabedokument, welches Repository was enthält |

## Ein Branching-Modell für ein Site-Team

Das Projekt selbst arbeitet auf `main` und erstellt daraus Release-Tags ([Den Code beziehen](../../installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches)).
Ein Site-Team ergänzt darüber eine Ebene:

| Branch | Enthält | Wer darauf schreibt |
|---|---|---|
| `my-site` (benennen Sie ihn nach Ihrer Site) | Den ausgerollten Stand, auf Basis eines Release-Tags oder von `main` | Nur durch das Mergen geprüfter Pull-Requests |
| `feature/<topic>`, `fix/<topic>` | Jeweils ein Arbeitspaket | Der Entwickler, der es bearbeitet |
| Upstream-Branches und -Tags | `main` des Projekts und seine Tags | Niemand in Ihrem Team; Sie mergen aus ihnen |

Der erste Schritt jeder neuen Site steht in [Mit Git entwickeln](../mit-git-entwickeln.md#eine-neue-site):
`git switch -c my-site`, auf Basis von `main` oder eines Release-Tags wie `v6.0.14`.

> **Tipp**
> Bauen Sie für die Produktion auf einem **Release-Tag** auf. „Sie können einen Tag festlegen und sich darauf
> verlassen, jedes Mal dieselben Dateien zu erhalten; deshalb ist eine festgelegte Version die richtige Wahl für die
> Produktion“ ([Den Code beziehen](../../installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches)).
> Wählen Sie `main` nur, wenn Sie etwas brauchen, das noch in keinem Tag enthalten ist, und halten Sie diese
> Entscheidung schriftlich fest.

## Pull-Requests und Reviews

Jede Änderung gelangt über einen Pull-Request nach `my-site`, den eine zweite Person gelesen hat. Das ist der
günstigste Schutz gegen das Risiko „Wissen in nur einem Kopf“ ([Risikomanagement](risikomanagement.md)).

Was ein Reviewer mindestens prüft:

- Die Änderung tut, was ihr Ticket sagt, und das Ticket sagt, wie sie getestet wurde.
- Die Betreffzeilen der Commits folgen dem Format (siehe unten).
- Keine Geheimnisse, Kundendaten oder Hostnamen in Code, Einstellungen, Logs oder Screenshots.
- Templates eines Administrationsdesigns folgen der
  [Checkliste für Code-Reviews aus „Die Administration gestalten“](../../../en/us/bc/6.0/admin4-styling.md#code-review-checklist).
- Änderungen an Einstellungen stehen in Dateien unter Versionsverwaltung und werden nicht auf dem Server vorgenommen.

Die eigene Praxis des Projekts, die es wert ist, übernommen zu werden (aus [CI](../../../en/us/bc/6.0/ci.md)): Ein Pull-Request
wird **so getestet, als wäre er in `main` gemergt**; ein Branch, der grün war, kann also rot werden, wenn sich `main`
weiterbewegt. Die Lösung ist, `main` in den Branch zu mergen und erneut zu pushen.

## Commit-Nachrichten

Die Regel des Projekts (`CONTRIBUTING.md`, [Qualitätsprüfungen](../../../en/us/features/6.0/quality-checks.md)): Jeder
Commit-Betreff beginnt mit dem, was die Änderung tut.

| Präfix | Verwendung |
|---|---|
| `Added:` | Eine neue Funktion, Datei, Klasse oder Fähigkeit |
| `Updated:` | Geändertes oder verbessertes Verhalten |
| `Fixed:` | Eine Fehlerkorrektur |
| `Removed:` | Etwas wurde gelöscht |
| `Renamed:` | Etwas wurde umbenannt |

Beispiel aus CONTRIBUTING.md: `Fixed: The redirect keeps the query string on the own host`.

Warum das eine Führungskraft interessieren sollte: Aus solchen Betreffzeilen lässt sich ein Changelog zusammenstellen,
und wer die Historie liest, versteht jede Änderung, ohne sie öffnen zu müssen. Übernehmen Sie dieselbe Regel für Ihre
eigenen Repositorys.

## Die Prüfungen, die jeder Commit besteht

Ein einmaliges `make hooks` in einem Kernel-Klon setzt den Hook-Pfad von Git auf `.githooks`. Ab dann gilt
([Qualitätsprüfungen](../../../en/us/features/6.0/quality-checks.md)):

| Wann | Prüfung |
|---|---|
| `git commit` | PHP-Syntax der vorgemerkten Dateien (auch mit PHP 8.0, wenn es installiert ist), JavaScript-Syntax, das Dateimanifest, der Codestil der Änderung |
| `git commit` | Das Format des Betreffs |
| `git push` | PHPStan gegen seine Baseline |

**Das Dateimanifest** `share/filelist.md5` enthält die Prüfsumme jeder Datei der Distribution. Die Upgrade-Prüfung der
Administration vergleicht die installierten Dateien damit; eine veraltete Zeile erscheint daher auf jeder Site als
„geänderte“ Datei. Jeder Commit, der eine Datei hinzufügt, ändert oder entfernt, aktualisiert ihre Zeile
(`make manifest-fix`).

`make check` führt Lint, Manifest, phpcs und phpstan so aus wie die CI. Ihre eigenen Repositorys können die Idee
übernehmen, auch wenn nicht die Werkzeuge: ein einziger Befehl, den jeder Entwickler vor dem Pushen ausführt und den
auch die CI ausführt.

## Einstellungen und Geheimnisse

Zwei Regeln aus der Dokumentation, die es wert sind, zur Teamrichtlinie zu werden:

1. **Pushen Sie niemals die Geheimnisse.** „Die Dateien in `settings/override/` und `settings/siteaccess/` enthalten
   Datenbankpasswörter: Pushen Sie sie nie in ein öffentliches Repository“ ([Mit Git entwickeln](../mit-git-entwickeln.md#der-kernel)).
   Vereinbaren Sie, wo die echten Werte liegen (ein privates Repository, das Deployment-Werkzeug, nur der Server), und
   halten Sie es im Übergabedokument fest.
2. **Die Konfiguration kommt aus der Versionsverwaltung.** Mit `[ConfigurationSettings] ManagedByDeployment=enabled`
   für einen Siteaccess ändert die Administration dessen Konfiguration auf dem Server nicht: Der Einstellungseditor,
   die Editoren für Toolbar und Menüs, der Template-Editor, der Setup-Assistent, die Aktivierung von Erweiterungen und
   die Installation von Paketen sind gesperrt, und `eZINI::save()` verweigert das Speichern
   ([ManagedByDeployment](../../../en/us/features/6.0/managed-by-deployment.md)). Ohne diese Einstellung schreiben diese
   Editoren Dateien in das Release-Verzeichnis, „wo die Versionsverwaltung sie nicht sieht und das nächste Deployment
   sie verwirft“.

Ein Team, das aus Git ausrollt, sollte `ManagedByDeployment` für seine Produktions-Siteaccesses einschalten.
Vereinbaren Sie vorher mit Redakteuren und Administratoren, welche Änderungen nun über einen Pull-Request laufen.

## Vom Branch zur Live-Site

Ein typischer Weg für eine Änderung:

1. Der Entwickler legt einen Branch an, ändert, committet, pusht und öffnet einen Pull-Request.
2. Der Reviewer liest und genehmigt; die Prüfungen sind bestanden.
3. Der Pull-Request wird in `my-site` gemergt.
4. Der Administrator rollt den gemergten Commit auf Staging aus, danach auf die Produktion.

Für Schritt 4 bietet das Produkt einen einzigen Befehl: `exp:velocity deploy`. Er führt die Autoloads der
Erweiterungen, den INI-Cache, die Caches für Templates, Overrides, Übersetzungen und Designs, das Neuladen von PHP-FPM,
den Neustart von Velocity und danach die Caches für Inhalte, HTTP und Template-Blöcke sowie den Antwort-Cache von
Velocity aus, gibt für jeden Schritt PASS, FAIL oder SKIP aus und hält beim ersten Fehler an
([Deployment, Abschnitt 7](../../anleitungen/bereitstellung.md#7-eine-php-änderung-mit-einem-befehl-ausliefern)). `--dry-run` zeigt, was er
tun würde. Die Reihenfolge ist wichtig: Ein Cache gerenderter Seiten, der geleert wird, bevor PHP den neuen Code
ausführt, füllt sich wieder mit Seiten des alten Codes ([11.6](../../installation/11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand)).

**Ohne Shell.** Die Erweiterung [git_manager](../../../en/us/features/6.0/extensions/git_manager.md) bietet ein Git-Dashboard
in der Administration (sehen, wo die Installation im Verhältnis zu ihrem Branch steht, Branches wechseln, pullen,
pushen) und einen Backup-Manager. Verwenden Sie sie, wo niemand im Team Shell-Zugang hat, und erstellen Sie vor jeder
Änderung ein Backup.

> **Warnung**
> Halten Sie andere Arbeitskopien des Codes (zum Beispiel Git-Worktree-Ordner) **außerhalb** des Wurzelverzeichnisses
> der Installation, oder tragen Sie sie in `.autoloadignore` ein. Andernfalls kann das Neuerzeugen der
> Kernel-Klassenzuordnung Kernel-Klassen auf diese Kopien abbilden ([11.6 Schritt 1](../../installation/11-aktualisieren.md#schritt-1-den-neuen-code-an-seinen-platz-bringen)).

## Tags sind unveränderlich

Das ist die eine Regel, die ein Team nie brechen darf, und das Projekt nennt sie an mehreren Stellen:

- „Ein veröffentlichter Tag wird nie verändert: Eine Korrektur für ein Problem in `v6.0.14` oder einem früheren Tag
  erreicht Sie als nächstes Release, nicht als korrigierte Kopie des alten“ ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)).
- „Ist eine veröffentlichte Version fehlerhaft, verschieben oder löschen Sie ihren Tag nicht. Veröffentlichen Sie die
  nächste Version mit der Korrektur und schreiben Sie in deren Notes, was an der vorherigen falsch war. Packagist
  speichert, was es gelesen hat, daher erzeugt ein verschobener Tag zwei verschiedene Pakete unter einer
  Versionsnummer“ ([Erweiterungen, „Release it“](../../anleitungen/extensions.md#7-die-extension-veröffentlichen)).

Was das für Ihr Team bedeutet, wenn Sie eigene Erweiterungen veröffentlichen:

| Regel | Warum |
|---|---|
| Die Nummer anhand dessen wählen, was veröffentlicht ist: `git fetch --tags`, dann `git tag -l 'v*' --sort=version:refname` | Gedächtnis und Arbeitsverzeichnis sind nicht verlässlich; eine Nummer kann bereits vergeben sein |
| Nach Version sortieren, nie nach Namen | Nach Namen steht `0.1.10` vor `0.1.8` |
| Nach `.9` kommt `.10` | Nur die letzte Stelle zählt hoch, sofern Sie nichts anderes entscheiden |
| Die Nummer in `ezinfo.php` und `extension.xml` (und `composer.json`) eintragen, committen und **dann** diesen Commit taggen | Ein zuerst erstellter Tag trüge die alte Nummer |
| Prüfen, bevor Sie den Tag pushen | Ein Tag ist leicht richtig zu setzen und unmöglich zu korrigieren |
| Auf ein fehlerhaftes Release folgt das nächste Release | Nie ein verschobener Tag |

Machen Sie **eine Person** für das Taggen jedes Repositorys verantwortlich und geben Sie ihr die Checkliste aus
[Erweiterungen, „Release it“](../../anleitungen/extensions.md#7-die-extension-veröffentlichen).

## Dem Upstream folgen

Der Kernel auf Ihrem Branch zieht auf ein neues Release um, indem Sie dessen Tag mergen ([Mit Git entwickeln](../mit-git-entwickeln.md#auf-dem-neuesten-stand-bleiben)):

```bash
git fetch origin --tags
git merge v6.0.14
composer install
```

Erweiterungen ziehen einzeln um, nachdem ihre Release-Notes gelesen wurden: `composer update se7enxweb/<package>`.
Führen Sie auf einer Live-Site nie ein einfaches `composer update` aus, das alle Pakete auf einmal umzieht
([Den Code beziehen](../../installation/03-den-code-beschaffen.md#35-extensions-sind-composer-pakete)).

Wenn Composer sich weigert, ein Paket zu aktualisieren, schützt es Ihre Arbeit: nicht committete Änderungen oder
Commits, die es nur auf einem Rechner gibt. Die Tabelle in [Mit Git entwickeln, „Wenn Composer sich weigert“](../mit-git-entwickeln.md#wenn-composer-sich-weigert)
sagt, was zu tun ist. Die Lehre für Führungskräfte: **Keine Arbeit darf nur auf dem Rechner eines einzelnen
Entwicklers liegen.** Pushen Sie die Branches.

Den Code umzuziehen ist die Hälfte eines Upgrades; die Datenbank ist die andere Hälfte. Siehe
[Release- und Upgrade-Planung](release-und-upgrade-planung.md).

## Rollen im Git-Arbeitsablauf

| Tätigkeit | Entwickler | Reviewer | Release-Verantwortlicher | Administrator | Projektleiter |
|---|---|---|---|---|---|
| Branch anlegen und committen | Führt aus | | | | |
| Pull-Request öffnen | Führt aus | | | | Verknüpft ihn mit dem Ticket |
| Review | | Führt aus | | | Sorgt dafür, dass es stattfindet |
| In `my-site` mergen | | Führt aus, nach Genehmigung | | | |
| Eine eigene Erweiterung taggen | | | Führt aus, mit der Checkliste | | Genehmigt die Version |
| Auf Staging und Produktion ausrollen | | | | Führt aus | Plant ein |
| Ein Upstream-Release mergen | Führt aus, auf einem Branch | Prüft | | Rollt aus | Plant das Upgrade |
| Eine Korrektur an den Upstream schicken | Führt aus | Prüft | | | Verfolgt sie |

## Checkliste für die Einrichtung des Teams

```text
Git setup for <project name>

[ ] Every developer installs with Git clones (preferred-install for se7enxweb/* set to source)
[ ] The site branch is named: <branch>, based on <tag or main>; the decision is written down
[ ] Where own code lives is decided: <kernel branch / own extension repositories>
[ ] Repositories for own code created: <list>
[ ] Branch protection: no direct pushes to the site branch; review required
[ ] Commit subject format agreed (Added: / Updated: / Fixed: / Removed: / Renamed:)
[ ] Kernel clones: make hooks run by every developer
[ ] Secrets: settings/override and settings/siteaccess never pushed to a public repository; real values kept in <place>
[ ] ManagedByDeployment decided per siteaccess: <list>
[ ] Deploy command and order agreed (exp:velocity deploy, or the manual steps)
[ ] Release owner per repository named: <names of roles>
[ ] Tagging checklist from "Release it" copied into the repository
[ ] Upstream fixes go as pull requests; local patches listed in the handover document
```

## Verwandte Seiten

- [Mit Git entwickeln](../mit-git-entwickeln.md): das Schritt-für-Schritt-Tutorial für Entwickler
- [Den Code beziehen](../../installation/03-den-code-beschaffen.md), [Erweiterungen](../../anleitungen/extensions.md)
- [Qualitätsprüfungen](../../../en/us/features/6.0/quality-checks.md), [Kontinuierliche Integration](../../../en/us/bc/6.0/ci.md)
- [Deployment](../../anleitungen/bereitstellung.md), [ManagedByDeployment](../../../en/us/features/6.0/managed-by-deployment.md)
- [Mit der Community arbeiten und etwas zurückgeben](mit-der-community-arbeiten.md), [Qualitätsschranken und Abnahme](qualitaetsschranken-und-abnahme.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
