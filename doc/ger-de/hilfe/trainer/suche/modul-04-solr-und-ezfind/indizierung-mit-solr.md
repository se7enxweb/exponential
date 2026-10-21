---
title: "Lektion 4.3: Indizierung mit Solr"
audience: trainer, admin, hosting
category: training
order: 43
summary: Die Teilnehmenden bauen den Solr-Index mit updatesearchindexsolr.php der Erweiterung auf, erklären Commits, die Cronjob-Teile für Optimierung und Teilbäume und warum Verschiebungen und Sichtbarkeitsänderungen Solr erreichen, und halten den Index mit den Inhalten im Gleichschritt. 30 Minuten.
keywords: suchkurs, ezfind, Solr, updatesearchindexsolr.php, --clean, --clean-all, --conc, commit, CommitWithin, OptimizeOnCommit, ezfoptimizeindex, ezfindexsubtree, index_moved_node, festschreiben, search course
type: help
---
# Lektion 4.3: Indizierung mit Solr

Mit ezfind liegt der Index in Solr statt in der Datenbank. Das Veröffentlichen indiziert weiterhin, die verzögerte
Indizierung funktioniert weiterhin, aber es gibt neue Teile: ein eigenes Skript zum Neuindizieren, Commits, die
Änderungen sichtbar machen, Cronjob-Teile für die Optimierung und für Teilbäume und Vorgänge wie Verschiebungen, die
jetzt Indexarbeit brauchen. Diese Lektion baut den Index auf und erklärt die Teile.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Modul | [Modul 4: Solr und ezfind](README.md) |
| Dauer | 30 Minuten (10 Präsentation, 20 Übung) |
| Niveau | Experten |
| Zielgruppe | Website-Administratoren und Hosting-Mitarbeitende |
| Voraussetzungen | [Lektion 4.2](exp-solr-und-die-solr-einstellungen.md); ezfind aktiv und Solr antwortet |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- den Solr-Index mit `extension/ezfind/bin/php/updatesearchindexsolr.php` aufbauen und zwischen `--clean` und
  `--clean-all` wählen;
- erklären, was ein Commit ist, und die Einstellungen in `[IndexOptions]` nennen, die Commits und Optimierung steuern;
- die Cronjob-Teile nennen, die die Erweiterung hinzufügt (`ezfoptimizeindex`, `ezfindexsubtree` und ihr Skript in
  `frequent`);
- erklären, warum Verschiebungen, neue Orte, Verbergen und Anzeigen Solr erreichen, während die eingebaute
  Suchmaschine nichts braucht.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [10.8 Suchindizierung und Bildverarbeitung](../../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung),
[Suche mit ezfind und Solr](../../../../../eng-us/help/creators/website-creators/search-with-ezfind-and-solr.md#indexing) und
`ezfind.ini [IndexOptions]` und `cronjob.ini.append.php` der Erweiterung.

1. **Das Skript zum Neuindizieren** (3 Minuten). Die Hinweise der Erweiterung indizieren neu mit
   `php extension/ezfind/bin/php/updatesearchindexsolr.php -s <admin siteaccess>`. Seine Optionen: `--clean` entfernt
   zuerst die Suchdaten der aktuellen Installation; `--clean-all` entfernt die Daten aller Installationen in diesem
   Solr (gefährlich bei einem gemeinsamen Solr); `--conc` legt die Anzahl paralleler Prozesse fest (2, wenn nicht
   angegeben); `--commit-within` legt die Commit-Verzögerung in Sekunden fest.
2. **Commits** (3 Minuten). Solr zeigt neue Dokumente erst nach einem Commit. `[IndexOptions]` steuert das:
   `DisableDirectCommits=false` (sofort festschreiben), `CommitWithin` (Millisekunden, `0` ignoriert es),
   `EnableSoftCommits`, `OptimizeOnCommit=disabled` (stattdessen per Cron optimieren). Die Erweiterung liefert auch
   `bin/php/ezsolrcommit.php` und `bin/php/ezsolroptimize.php` mit.
3. **Pfade im Index** (4 Minuten). Solr speichert Pfade, deshalb stellt ein Verschieben bei verzögerter Indizierung
   `index_moved_node` und `index_object` in die Warteschlange; das Hinzufügen eines Ortes oder ein Tausch stellt
   `index_object` ein; Verbergen und Anzeigen werden sofort indiziert und stellen die eigene Teilbaumaktion der
   Erweiterung ein. Die Erweiterung fügt `ezfindexsubtree.php` dem Teil `frequent` und einem eigenen Teil hinzu sowie
   `ezfoptimizeindex.php` im Teil `ezfoptimizeindex`.

### Häufige Fragen

**Wird der Datenbankindex noch verwendet?** Nein. Mit `SearchEngine=ezsolr` gehen Suchen an Solr. Die eingebauten
Tabellen bleiben, wie sie sind.

**Welches Skript verwende ich nach einem Import?** Mit ezfind das `updatesearchindexsolr.php` der Erweiterung; der
Hosting-Leitfaden sagt, dass mit den eigenen Werkzeugen der Erweiterung neu indiziert wird.

**Wie oft sollten wir optimieren?** Die Einstellungen der Erweiterung schlagen einen Cronjob „typischerweise jeden Tag
oder jede Woche“ vor und merken an, dass es eher wenig ausmacht, gar nicht zu optimieren.

### Stolperfallen

- **`--clean-all` bei einem gemeinsamen Solr.** Es entfernt die Daten aller Installationen. Vermitteln Sie `--clean`
  und sagen Sie nur, dass es `--clean-all` gibt.
- **Kein Cron für den Teil frequent.** Sichtbarkeits- und Teilbaumänderungen warten dann. Prüfen Sie die Crontab wie
  in [Lektion 2.2](../modul-02-indizierung/verzoegerte-indizierung-und-der-indexcontent-cronjob.md).
- **Aus Gewohnheit das Kernel-Skript ausführen.** Es arbeitet über die konfigurierte Suchmaschine, aber das Skript der
  Erweiterung ist das für Solr dokumentierte und bietet parallele Prozesse.

## Praktische Übung

Verwenden Sie Ihre Schulungsinstallation aus den Lektionen 4.1 und 4.2, mit laufendem Solr. `<admin siteaccess>` ist
Ihr Admin-Siteaccess. Fügen Sie `--allow-root-user` nur hinzu, wenn Sie als root arbeiten.

1. Suchen Sie in der Administration nach einem Wort aus den Beispielinhalten. Notieren Sie das Ergebnis (der Index ist
   noch leer).
2. Bauen Sie den Solr-Index auf:

   ```bash
   php extension/ezfind/bin/php/updatesearchindexsolr.php -s <admin siteaccess> --clean
   ```

3. Lesen Sie die Ausgabe: die Anzahl der Prozesse und den Fortschritt.
4. Suchen Sie erneut nach demselben Wort.
5. Listen Sie die Cronjob-Teile auf und suchen Sie die Skripte der Erweiterung:

   ```bash
   php runcronjobs.php --list
   ```

6. Geben Sie die Commit-Einstellungen aus:

   ```bash
   php bin/php/console exp:ini get ezfind.ini/IndexOptions/DisableDirectCommits
   php bin/php/console exp:ini get ezfind.ini/IndexOptions/OptimizeOnCommit
   ```

7. Veröffentlichen Sie einen neuen Artikel mit dem Wort `persimmon`, suchen Sie danach, verbergen Sie ihn dann und
   durchsuchen Sie die öffentliche Website als Besucher.
8. Führen Sie den Optimierungsteil einmal aus: `php runcronjobs.php -s <admin siteaccess> ezfoptimizeindex`.

## Erwartetes Ergebnis

- Schritt 1 findet nichts oder fast nichts; Schritt 2 endet ohne Fehler, und Schritt 4 findet die Inhalte.
- Schritt 3 nennt die Anzahl der Prozesse (2, sofern `--conc` nicht angegeben wurde).
- Schritt 5 listet `ezfindexsubtree.php` in `frequent` und in seinem eigenen Teil sowie `ezfoptimizeindex.php` in
  `ezfoptimizeindex` auf.
- Schritt 6 gibt `false` und `disabled` aus.
- Schritt 7 findet den neuen Artikel sofort; nach dem Verbergen findet der Besucher ihn nicht.
- Der Trainer prüft Schritt 4 auf jedem Rechner.

## Quiz

1. Welche Option von `updatesearchindexsolr.php` entfernt nur die Daten der aktuellen Installation?
   A. `--clean` B. `--clean-all` C. `--conc` D. `--commit-within`
2. Was macht ein neu indiziertes Dokument in den Ergebnissen von Solr sichtbar?
3. Warum stellt ein Verschieben für Solr Arbeit in die Warteschlange, für die eingebaute Suchmaschine aber nicht?
4. Welches Cronjob-Skript optimiert den Solr-Index, und in welchem Teil?
5. Nennen Sie zwei Einstellungen aus `[IndexOptions]` und was sie bewirken.

<details>
<summary>Antworten</summary>

1. **A.** `--clean-all` entfernt die Daten aller Installationen.
2. Ein Commit (direkt, innerhalb von `CommitWithin`, ein Soft-Commit oder ein Commit per Cron).
3. Solr speichert Pfade in seinem Index; der Index der eingebauten Suchmaschine enthält keine
   ([10.8](../../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung)).
4. `ezfoptimizeindex.php`, im Teil `ezfoptimizeindex`.
5. Zwei beliebige aus: `OptimizeOnCommit`, `DisableDirectCommits`, `CommitWithin`, `DisableDeleteCommits`,
   `EnableSolrAttributeStorage`, `EnableSoftCommits`, mit ihrer Bedeutung aus der `ezfind.ini` der Erweiterung.

</details>

## Weiterführende Informationen

- [10.8 Suchindizierung und Bildverarbeitung](../../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung):
  was jeder Vorgang für eine Suchmaschine mit Pfaden in die Warteschlange stellt.
- [Suche mit ezfind und Solr](../../../../../eng-us/help/creators/website-creators/search-with-ezfind-and-solr.md#indexing): Indizierung
  für Website-Ersteller.
- [Cronjobs: ausführen, planen und verfolgen](../../../../anleitungen/cronjobs.md): von Erweiterungen hinzugefügte Teile.

## Verwandte Seiten

- [Modul 4: Solr und ezfind](README.md)
- [Suche: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
