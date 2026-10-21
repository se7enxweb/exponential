---
title: "Ein Projekt messen: Kennzahlen (KPIs) aus den eigenen Logs und dem Audit-Protokoll des Produkts"
audience: project-manager, admin
category: leadership
order: 150
summary: Kennzahlen, die Exponential selbst erzeugt (Audit-Protokoll, Request-Logs, Fehlerübersicht, Zustand der Logs, Cronjobs, Inhaltsprüfungen, Upgrade-Prüfung, Benchmarks, CI), wo jede angezeigt wird und wie Sie sie erheben.
keywords: Kennzahl, Kennzahlen, Messwerte, messen, Bericht, Dashboard, Audit, Audit-Protokoll, Diagramme, Export, CSV, Logs, Fehlerübersicht, 404, Cronjobs, Inhaltsprüfungen, defekte Beziehungen, Upgrade-Prüfung, Benchmark, Statusbericht, KPI, metrics, measure, report, audit trail, charts, error digest, security.log, CSP reports, content checks, broken relations, upgrade check, benchmark, CI health, status report
type: help
---
# Ein Projekt messen: Kennzahlen (KPIs) aus den eigenen Logs und dem Audit-Protokoll des Produkts

Diese Seite ist für Projektleiter, die über den Zustand und den Fortschritt einer Site berichten, und für die
Administratoren, die die Zahlen erheben. Sie führt nur Werte auf, die **Exponential selbst erzeugt**: Zu jedem wird die
Seite in der Administration oder der Befehl genannt, der ihn anzeigt, sodass jeder mit demselben Zugriff die Zahlen in
Ihrem Statusbericht überprüfen kann.

Die Seite schlägt keine Zielwerte vor. Ein guter Wert für Ihre Site hängt von Ihrer Site ab; die erste Messung ist Ihre
Ausgangsbasis, und berichtet wird der Verlauf über die Zeit.

## Inhalt

1. [Grundsätze](#grundsätze)
2. [Wer die Zahlen sehen darf](#wer-die-zahlen-sehen-darf)
3. [Redaktionelle Aktivität: das Audit-Protokoll](#redaktionelle-aktivität-das-audit-protokoll)
4. [Sicherheit und Zugriff](#sicherheit-und-zugriff)
5. [Zuverlässigkeit: Fehler und fehlende Seiten](#zuverlässigkeit-fehler-und-fehlende-seiten)
6. [Betrieb: Cronjobs, Logs und Systemzustand](#betrieb-cronjobs-logs-und-systemzustand)
7. [Inhaltsqualität](#inhaltsqualität)
8. [Aktualität: Versionen und Dateikonsistenz](#aktualität-versionen-und-dateikonsistenz)
9. [Performance](#performance)
10. [Entwicklung: CI und das Repository](#entwicklung-ci-und-das-repository)
11. [Was das Produkt nicht misst](#was-das-produkt-nicht-misst)
12. [Eine Vorlage für den monatlichen Kennzahlenbericht](#eine-vorlage-für-den-monatlichen-kennzahlenbericht)

## Grundsätze

- **Messen Sie, was das Produkt aufzeichnet.** Jede Kennzahl unten stammt von einer Seite oder einem Befehl von
  Exponential. Wo Sie etwas anderes brauchen (Besucheranalyse, Zufriedenheit), sagen Sie, woher es stammt.
- **Gleicher Zeitraum, gleiche Quelle.** Vergleichen Sie Gleiches mit Gleichem: dieselbe Anzahl Tage, von derselben
  Seite abgelesen.
- **Datenschutz zuerst.** Die Logs und das Audit-Protokoll maskieren Client-Adressen, hashen Sitzungen und entfernen Werte
  von Abfrageparametern ([Request-Logs, „Privacy“](../../funktionen/6.0/anfrageprotokolle.md#datenschutz),
  [Das Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md)). Berichten Sie zusammengefasste Werte, nicht einzelne
  Personen. Eine Liste der „aktivsten Akteure“ ist für den Administrator, nicht für eine Präsentationsfolie.
- **Eine Zahl ist ein Hinweis.** Die Seite zu den Cronjobs sagt das über ihre eigene Zahl der Probleme: „Sie ist ein
  Hinweis, kein Urteil“ ([Cronjobs](../../anleitungen/cronjobs.md#10-letzte-ausführungen-und-wie-probleme-gezählt-werden)). Lesen Sie die
  Zeilen hinter einem Wert, bevor Sie danach handeln.

## Wer die Zahlen sehen darf

| Quelle | Benötigte Richtlinie | Quellseite |
|---|---|---|
| Tab Audit: Dashboard, Konsole, Diagramme, Alarme, Export | `audit/read` (wahlweise nach Kanal eingeschränkt); in einer neuen Installation hat sie nur Administrator | [Das Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md) |
| Audit-Archive und -Einstellungen, „Jetzt prüfen“ | `audit/manage` | ebenda |
| Log-Viewer, Fehlerübersicht, Zustand der Logs | `setup/logs` | [Log-Viewer](../../funktionen/6.0/log-betrachter.md) |
| Systeminformationen | `setup/system_info` | [Systeminformationen](../../anleitungen/systeminformationen.md) |
| Upgrade-Prüfung | `setup/setup` | [Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md) |
| Inhaltsprüfungen | Richtlinien des Moduls `contentcheck` | [Inhaltsprüfungen](../../funktionen/6.0/inhaltspruefungen.md) |

Eine Berichtsrolle für einen Projektleiter kann `audit/read`, eingeschränkt auf die benötigten Kanäle, enthalten, ohne
das Recht, irgendetwas zu ändern. Legen Sie sie an, wie [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md)
es beschreibt.

## Redaktionelle Aktivität: das Audit-Protokoll

Das Audit-Protokoll ist ab Werk eingeschaltet und zeichnet Inhaltsänderungen, Anmeldungen, geschriebene Einstellungen,
geleerte Caches, Cronjob-Läufe und Bestellungen in den Kanälen `content`, `access`, `system`, `commerce` und, wenn
eingeschaltet, `read` auf ([Das Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md)). Es braucht die Cronjob-Gruppe
`frequent`, deren Teil `audit` die Ereignisse indiziert, die die Diagramme und die Konsole lesen.

| Kennzahl | Wo | Wie |
|---|---|---|
| Ereignisse pro Tag, pro Kanal | **Audit > Diagramme** (`audit/charts`) | „Events per day per channel“, standardmäßig die letzten 14 Tage, bis zu 366 (`/(days)/<n>`), jedes Diagramm mit seinen Zahlen in einer Tabelle |
| Heute und die letzten 7 Tage, pro Kanal | **Audit > Dashboard** | Die Karte *Heute und 7 Tage* |
| In einem Zeitraum veröffentlichte Objekte | **Audit > Konsole**, gefiltert auf den Namen `content.object.publish` und einen Zeitraum; **Export** sagt, wie viele Einträge der Filter umfasst | Oder `./console exp:audit search --name=content.object.publish --from=<time> --to=<time>` |
| In einem Zeitraum entfernte Inhalte | Konsole oder Suche mit Namen `content.node.*` und `content.object.*` (zum Beispiel `content.node.remove`) | Das Entfernen eines Teilbaums ist ein übergeordnetes Ereignis mit einem untergeordneten pro Knoten |
| Aktivste Redakteure und am häufigsten geänderte Objekte | **Audit > Dashboard**, Karte *Aktivität* (heute); **Diagramme**, „top actors“ und „top event names“ | Für den Administrator; berichten Sie Zahlen, keine Namen |
| Massenbearbeitung | Inhaltsjobs sind übergeordnete Ereignisse von allem, was ihre Stapel getan haben | [Inhaltsjobs](../../funktionen/6.0/inhaltsauftraege.md) |

**Die Zahlen erheben.** **Audit > Export** schreibt den aktuellen Filter der Konsole als CSV, JSON Lines oder JSON;
**Jetzt herunterladen** liefert ihn direkt aus, **Im Hintergrund exportieren** startet für einen großen Filter einen
Inhaltsjob. In einer Shell tut `./console exp:audit export` dasselbe ([Audit-Referenz](../../bc/6.0/audit.md)). Eine
monatliche CSV-Datei, die bei den Berichten des Projekts abgelegt wird, ergibt eine Reihe, die Sie in jeder
Tabellenkalkulation als Diagramm darstellen können.

**Rückstau bei Freigaben.** Versionen, die in einem Workflow warten, meist auf eine Freigabe, sind für jeden
Freigebenden unter **Meine Warteliste** (`/content/pendinglist`) aufgeführt
([Meine Entwürfe und meine Warteliste](../../anleitungen/entwuerfe-und-ausstehende-objekte.md)) sowie unter **Setup > Workflow-Prozesse**
([Workflows](../../anleitungen/workflows.md)). Die Zahl der wartenden Prozesse und das Alter des ältesten sind ein
brauchbares Maß dafür, ob die Freigaben Schritt halten.

## Sicherheit und Zugriff

| Kennzahl | Wo | Anmerkungen |
|---|---|---|
| Fehlgeschlagene Anmeldungen, nach Adresse und nach Anmeldename | **Audit > Dashboard**, Karte *Sicherheit* | Unbekannte Konten werden nur gehasht aufgezeichnet |
| Anmeldungen im Vergleich zu fehlgeschlagenen Anmeldungen, pro Tag | **Audit > Diagramme** | |
| Ablehnungen und Fehlschläge pro Tag | **Audit > Diagramme** | Einschließlich `access.permission.refused`: eine eingetippte URL, die ein Benutzer nicht öffnen darf |
| Rollenzuweisungen | **Audit > Dashboard**, Karte *Sicherheit*: die letzten Rollenzuweisungen | Jede sollte zu einer Anfrage passen, die Sie kennen |
| Ausgelöste Alarme | **Audit > Alarme** (`audit/alerts`): Regel, Anzahl, Zeitfenster | Regeln für Brute-Force-Angriffe, vergebene Administratorrollen, außerhalb der Arbeitszeit geschriebene Einstellungen, Massenlöschungen, abgeschaltetes Audit, unterbrochene Kette |
| Audit-Kette intakt | **Audit > Dashboard**, *Zustand*; `./console exp:audit verify` (Exit-Status 1, wenn unterbrochen) | Berichten Sie „intakt am <Datum>“ |
| Sondierungen und Angriffe, nach Art | **Setup > Logdateien > Fehlerübersicht**, `security.log` gruppiert nach `<category>/<rule>` (zum Beispiel `probe/wp-login`) | Der Klassifizierer klassifiziert nur; nichts wird blockiert ([Request-Logs](../../funktionen/6.0/anfrageprotokolle.md)) |
| Verstöße gegen die Content Security Policy | Fehlerübersicht des Logs `csp`, gruppiert nach Direktive und blockierter Quelle | Die Grundlage, um eine Richtlinie auf Erzwingen umzustellen ([Content Security Policy](../../funktionen/6.0/content-security-policy.md#berichte)) |

> **Hinweis**
> Ein Anstieg in `security.log` sagt meist mehr über das Internet als über Ihre Site aus. Die Hilfe rät, sich die
> Fehlerübersicht und `security.log` wöchentlich anzusehen: „Ein plötzlicher Anstieg einer Regel ist einen Blick wert“
> ([Sicherheitshärtung](../sicherheitshaertung.md#beobachten-logs-der-request-klassifizierer-und-das-audit-protokoll)).

## Zuverlässigkeit: Fehler und fehlende Seiten

| Kennzahl | Wo | Wie gezählt wird |
|---|---|---|
| Unterschiedliche Fehler und Warnungen der letzten 24 Stunden | **Setup > Logdateien > Fehlerübersicht** | Meldungen maskiert, normalisiert, gruppiert und gezählt, die häufigsten zuerst; ein anderer Zeitraum mit `/(hours)/N`, 1 bis 168 ([Log-Viewer](../../funktionen/6.0/log-betrachter.md#fehlerübersicht)) |
| Neue Fehlergruppen seit dem letzten Bericht | Fehlerübersicht mit **Als bekannt markieren** für die bereits erklärten Gruppen | Bekannte Gruppen werden für den Betrachter ausgeblendet |
| Fehlende Seiten (404 und 410), nach Grund | Fehlerübersicht von `404.log`, gruppiert nach seinem Feld `reason=` (`module:...`, `node:#`, `view:...`) | „Warum Seiten fehlen, nicht eine Liste jeder fehlenden Adresse“ ([Request-Logs](../../funktionen/6.0/anfrageprotokolle.md#der-log-viewer-die-rotation-und-die-fehlerübersicht)) |
| Von Redakteuren gemeldete Fehler | Die von Redakteuren weitergegebenen Fehlerreferenzen (`ERR-...`) | [Einen Fehler anhand seiner Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md) |

Nach einer Migration sind die 404-Gruppen nach Grund ein direktes Maß dafür, wie viele alte Adressen noch eine
Weiterleitung oder einen URL-Alias brauchen ([URLs und Aliase](../../anleitungen/urls-und-aliase.md)).

## Betrieb: Cronjobs, Logs und Systemzustand

| Kennzahl | Wo | Wie gezählt wird |
|---|---|---|
| Fehlschläge und Warnungen der Zustandsprüfungen | **Setup > Systeminformationen**, *Zustandsprüfungen* | Fehler, Warnung, Hinweis oder OK pro Prüfung; `./console exp:system:info --json` endet mit Exit-Status 1, wenn eine Prüfung fehlschlägt ([Systeminformationen](../../anleitungen/systeminformationen.md#3-zustandsprüfungen)) |
| Cronjob-Läufe und ihr Ergebnis | **Setup > Cronjobs**, *Letzte Läufe*: die letzten 20 von der Seite gestarteten Läufe, mit Dauer und *OK* oder *n Probleme* | Probleme sind Zeilen mit *error*, *fatal*, *exception*, *failed*, *failure* oder *warning* ([Cronjobs](../../anleitungen/cronjobs.md#10-letzte-ausführungen-und-wie-probleme-gezählt-werden)) |
| Jeder Cronjob-Lauf, auch aus der Crontab | Audit, `system.cronjob.run` und `system.cronjob.fail` | Anzahl pro Tag in der Konsole |
| Aktualität der Cronjobs | Die Cronjob-Prüfung der Systeminformationen (ein letzter Lauf vor mehr als zwei Tagen wird markiert); das Audit-Dashboard warnt, wenn sein Teil eine Stunde lang nicht gelaufen ist | |
| Speicherplatz der Logs im Verhältnis zum Budget | **Setup > Logdateien > Zustand der Logs**: Speicherplatz im Verhältnis zu `[HealthSettings] DiskBudget` (Voreinstellung 256 MB; Warnung ab 80 %) | Außerdem die am schnellsten wachsenden Logs und Logs über ihrer Größengrenze ([Zustand der Logs](../../funktionen/6.0/log-betrachter.md#zustand-der-logs)) |
| Größe von `var/` und der Caches | **Setup > Systeminformationen > Größen messen** | Zehn Minuten lang gespeichert; **Erneut messen** misst neu |
| Cache-Status | `php bin/php/cache.php status` endet mit `PASS` | [Eine Site betreiben](../../anleitungen/betrieb-einer-website.md#10-eine-wöchentliche-checkliste) |

Die wöchentliche Checkliste aus [Eine Site betreiben](../../anleitungen/betrieb-einer-website.md#10-eine-wöchentliche-checkliste) ist ein
fertiger Satz von Ja/Nein-Kennzahlen: Caches PASS, jede Audit-Kette intakt, nichts Unerklärtes in `error.log`,
Cron-Einträge vorhanden, ein Backup aus den letzten sieben Tagen, das mindestens einmal wiederhergestellt wurde.

## Inhaltsqualität

Aus [Inhaltsprüfungen](../../funktionen/6.0/inhaltspruefungen.md), im Setup-Menü:

| Kennzahl | Wo | Was gezählt wird |
|---|---|---|
| Defekte Beziehungen | `/contentcheck/brokenrelations/<node id>` | Veröffentlichte Inhalte, die auf etwas Gelöschtes, im Papierkorb Liegendes, Verborgenes oder nie Veröffentlichtes verweisen |
| Nicht referenzierte Assets | `/contentcheck/unreferenced/<node id>` | Veröffentlichte Bilder, Dateien und Medien unterhalb eines Knotens, die nichts verwendet |
| Verwaiste Dateien und ihre Größe | `/contentcheck/storage/<node id>` | Dateien in `var/<site>/storage`, die kein Inhalt verwendet, mit Summen |
| Fehlende Dateien | Fehlende Dateien in `var/<site>/storage` | Bilder, Dateien und Medien, deren Datei fehlt, sowie fehlende oder veraltete Bildvarianten |

Derselbe Bericht in einer Shell: `./console exp:content-check` (nur Bericht). Starten Sie jede Prüfung jedes Mal am
selben Knoten, damit die Zahlen vergleichbar sind.

## Aktualität: Versionen und Dateikonsistenz

| Kennzahl | Wo | Anmerkungen |
|---|---|---|
| Laufende Version im Vergleich zum neuesten Release | **Setup > Systeminformationen** im Vergleich zum neuesten Tag (`git tag -l 'v*' --sort=version:refname`) | Sicherheitskorrekturen erreichen eine Site nur über das nächste Release ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) |
| Dateien, die vom Release abweichen | **Setup > Upgrade-Prüfung > Überprüfe Dateikonsistenz**: *Geändert*, *Fehlt*, *Nicht lesbar* | Absichtlich geänderte Dateien sind zu erwarten; eine wachsende Liste nicht ([Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md)) |
| Abweichungen der Datenbank vom Schema | **Überprüfe Datenbankkonsistenz** | Nach Tabelle gruppiert |
| Versionen der Erweiterungen | **Setup > Extensions** | Jede Erweiterung gibt ihre eigene Version an |
| Mitgeführte lokale Patches | Ihre eigene Aufzeichnung (Übergabedokument) | Siehe [Mit der Community arbeiten](mit-der-community-arbeiten.md#beiträge-in-ein-projekt-einplanen) |

## Performance

`./console exp:benchmark` ruft eine Reihe von Seiten mehrfach ab und gibt pro Seite Median, p90, p99, Minimum, Maximum
und Standardabweichung aus; `--save` speichert einen Lauf, und `--baseline` vergleicht damit und endet bei einer
Verschlechterung mit Exit-Status 1 ([Benchmarking](../../anleitungen/benchmarking.md)).

| Kennzahl | Wie |
|---|---|
| Median und p90 der Antwortzeit wichtiger Seiten, aus dem Cache | `exp:benchmark` mit Ihrer Seitenliste |
| Dasselbe für gerenderte Seiten | `--cold` |
| Verschlechterung nach einem Deployment | `--baseline=<file>`; Exit-Status 0 bedeutet keine Verschlechterung |

Die Warnung des Leitfadens gilt für jeden Bericht: „Ein Lauf auf einer ausgelasteten Maschine beweist wenig: Vergleichen
Sie Läufe, die direkt nacheinander gemacht wurden, mit denselben Anzahlen, denselben Seiten und derselben
PHP-Konfiguration“ (Abschnitt 10 enthält eine Checkliste). Berichten Sie die Bedingungen zusammen mit den Zahlen.

## Entwicklung: CI und das Repository

Für Teams, deren Repositorys auf GitHub Actions laufen, funktioniert das Berichtsskript des Projekts auch für sie:
`python3 .github/scripts/ci_health_report.py --days 14` gibt die Erfolgsquote pro Workflow und Ereignis aus, die am
häufigsten fehlschlagenden Schritte und Tests sowie instabile Läufe
([CI, „How healthy CI is“](../../bc/6.0/ci.md#wie-gesund-die-ci-ist-der-bericht)). Das Projekt hat seine eigene Ausgangsbasis
veröffentlicht: 61,7 % von 1.540 abgeschlossenen Läufen waren vom 2026-10-03 bis 2026-10-17 erfolgreich, vor den
Reparaturen jener Woche.

Aus Git selbst, mit denselben Befehlen, die [Wer ist 7x?](../wer-ist-7x.md#7x-in-zahlen) für seine Zahlen verwendet:

| Kennzahl | Befehl |
|---|---|
| Commits in einem Zeitraum | `git rev-list --count --since=<date> HEAD` |
| Gemergte Pull-Requests | `git log --grep="Merge pull request" --oneline` (die Zeilen zählen) |
| Änderungen nach Art | `git log --format=%s` und die Präfixe `Added:`, `Updated:`, `Fixed:`, `Removed:`, `Renamed:` zählen |

## Was das Produkt nicht misst

Seien Sie in Ihren Berichten hierzu ehrlich:

- **Besucherverkehr.** Die hier verwendeten Quellen beschreiben keinen eingebauten Bericht zur Besucheranalyse. Was es
  gibt: Der Audit-Kanal `read` zeichnet stichprobenartig Knotenaufrufe, Suchen und Downloads auf, aber nur, wenn
  Lesezugriffe eingeschaltet sind ([Das Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md)); den Cronjob
  `updateviewcount` (genannt im [Changelog 6.0.11](../../../changelogs/6.0/6.0.11.md)); und die Erweiterung
  [bcwebsitestatistics](../../funktionen/6.0/erweiterungen/bcwebsitestatistics.md), die das Skript eines externen
  Analysedienstes einbindet. Wählen Sie bewusst und mit Blick auf den Datenschutz.
- **Zufriedenheit der Redakteure, eingesparte Zeit, Konversionen.** Messen Sie sie mit eigenen Umfragen oder Werkzeugen,
  und sagen Sie das auch.
- **Verfügbarkeit von außen.** Die Systeminformationen lesen den Server aus, auf dem sie laufen; prüfen Sie auch von
  außen ([10.11 Überwachung und Zustandsprüfungen](../../installation/10-nach-der-installation.md#1011-überwachung-und-zustandsprüfungen)).

## Eine Vorlage für den monatlichen Kennzahlenbericht

```text
KPI REPORT: <site name>, <month>          Prepared by: <role>     Data read on: <date>

Editorial (Audit > Charts / Export, window <dates>)
  Items published:                         <n>      previous month: <n>
  Items removed:                           <n>      previous month: <n>
  Approval backlog (waiting processes):    <n>, oldest <days> days

Security (Audit dashboard and alerts; error digest of security.log and csp)
  Failed sign-ins:                         <n>
  Alerts fired:                            <n>  (<rules>)
  Audit chains intact on:                  <date>   yes / no
  security.log top groups:                 <category/rule: n>, ...
  CSP violation groups:                    <n>

Reliability (Error digest, window 24 h / 168 h)
  Distinct error groups:                   <n>   new since last report: <n>
  404 groups by reason (top 3):            <reason: n>, ...

Operations
  System information failures / warnings:  <n> / <n>
  Cronjob runs with issues (last 20):      <n>
  Log disk use vs budget:                  <MB> of <MB>
  Backup restored on:                      <date>

Content quality (Content checks, start node <node>)
  Broken relations:                        <n>
  Unreferenced assets:                     <n>
  Orphan files:                            <n>, <MB>
  Missing files:                           <n>

Currency
  Running version / newest release:        <version> / <version>
  Upgrade check: modified / missing:       <n> / <n>
  Local patches carried:                   <n>

Performance (exp:benchmark, conditions: <server, counts, cached/cold>)
  Front page median / p90:                 <ms> / <ms>
  Regression against baseline:             yes / no

Comments and actions:
  - <finding> -> <action>, owner <role>, due <date>
```

## Verwandte Seiten

- [Das Audit-Protokoll](../../funktionen/6.0/pruefprotokoll.md) und die [Audit-Referenz](../../bc/6.0/audit.md)
- [Log-Viewer](../../funktionen/6.0/log-betrachter.md), [Request-Logs](../../funktionen/6.0/anfrageprotokolle.md)
- [Cronjobs](../../anleitungen/cronjobs.md), [Systeminformationen](../../anleitungen/systeminformationen.md), [Inhaltsprüfungen](../../funktionen/6.0/inhaltspruefungen.md)
- [Benchmarking](../../anleitungen/benchmarking.md), [Continuous Integration](../../bc/6.0/ci.md)
- [Qualitätsschranken und Abnahme](qualitaetsschranken-und-abnahme.md), [Risikomanagement](risikomanagement.md), [Projektvorlagen](projektvorlagen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
