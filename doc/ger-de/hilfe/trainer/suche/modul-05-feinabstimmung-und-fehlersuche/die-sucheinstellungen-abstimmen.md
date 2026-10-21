---
title: "Lektion 5.1: Die Sucheinstellungen abstimmen"
audience: trainer, admin
category: training
order: 51
summary: Die Teilnehmenden ändern die Sucheinstellungen der eingebauten Suchmaschine pro Siteaccess mit exp:ini, schalten Platzhalter mit einer Mindestlänge ein, passen die Stoppwortregel und die Ergebnisgrenzen an und beurteilen jede Änderung nach Wirkung und Kosten. 30 Minuten.
keywords: suchkurs, feinabstimmung, SearchSettings, EnableWildcard, MinCharacterWildcard, StopWordThresholdValue, StopWordThresholdPercent, MaximumSearchLimit, AllowEmptySearch, MaximumSearchTextWords, siteaccess, platzhalter, stoppwörter, search course, tuning, wildcards
type: help
---
# Lektion 5.1: Die Sucheinstellungen abstimmen

Die eingebaute Suchmaschine hat wenige Einstellungen, und jede wägt etwas ab: Platzhalter helfen Besuchern, belasten
aber die Datenbank; die Stoppwortregel entfernt Rauschen, kann aber ein wichtiges Wort verbergen; Grenzen schützen den
Server, schneiden aber lange Suchen ab. Diese Lektion bringt den Teilnehmenden bei, sie bewusst pro Siteaccess zu
ändern und eine Änderung rückgängig zu machen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Modul | [Modul 5: Feinabstimmung und Fehlersuche](README.md) |
| Dauer | 30 Minuten (10 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren |
| Voraussetzungen | [Modul 3](../modul-03-suchen-in-der-praxis/README.md); ein Terminal im Wurzelverzeichnis der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Sucheinstellung für einen Siteaccess mit `exp:ini set ... siteaccess:<name>` setzen und mit `exp:ini where`
  prüfen;
- die Platzhaltersuche mit `EnableWildcard` einschalten und `MinCharacterWildcard` erklären;
- die Stoppwortregel (`StopWordThresholdValue`, `StopWordThresholdPercent`) erklären und anpassen;
- die Grenzen `MaximumSearchLimit`, `MaximumSearchTextWords`, `MaximumSearchTextLength` und `AllowEmptySearch` nennen
  und sagen, warum es jede gibt.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: `settings/site.ini [SearchSettings]` (ihre Kommentare), [Website-Suche](../../../ersteller/website-ersteller/suche-auf-der-website.md#einstellungen-auf-die-es-ankommt)
und [Einstellungen über die Kommandozeile ändern: exp:ini](../../../../funktionen/6.0/exp-ini-befehl.md).

1. **Wo man sie setzt** (2 Minuten). Sucheinstellungen gehören zum Siteaccess, in dem gesucht wird, meist dem
   öffentlichen: `settings/siteaccess/<public siteaccess>/site.ini.append.php`, geschrieben von
   `exp:ini set ... siteaccess:<name>`. Jede Änderung wird nach `var/backup/ini/<timestamp>/` gesichert und im
   Audit-Protokoll aufgezeichnet.
2. **Platzhalter** (3 Minuten). `EnableWildcard=true` lässt Besucher `salad*` eingeben. `MinCharacterWildcard=3` ist
   die Mindestzahl an Zeichen vor dem `*`. Der mitgelieferte Kommentar warnt, dass Platzhalter „die Leistung des
   Systems stark beeinflussen können“. Probieren Sie es auf der Schulungs-Website aus; messen Sie, bevor Sie es auf
   einer großen verwenden (Lektion 5.4).
3. **Stoppwörter** (2 Minuten). Nur auf Websites mit mehr als `StopWordThresholdValue` (100) Objekten: Ein Wort, das in
   mehr als `StopWordThresholdPercent` (60) Prozent der Objekte vorkommt, wird ignoriert. Ein höherer Prozentsatz
   behält mehr Wörter; ein niedrigerer entfernt mehr.
4. **Grenzen** (3 Minuten). `MaximumSearchLimit` (30) begrenzt die Ergebnisse pro Anfrage; `MaximumSearchTextWords`
   (16) und `MaximumSearchTextLength` (1024) schneiden lange Texte ab, weil jedes Wort eine Bedingung der Abfrage ist
   und zu viele eine Suche scheitern lassen; `AllowEmptySearch` bleibt `disabled`, sofern Templates nicht selbst genug
   Einschränkungen hinzufügen.

### Häufige Fragen

**Warum pro Siteaccess und nicht global?** Die Administration und die öffentliche Website haben unterschiedliche
Benutzer und Bedürfnisse; ein Platzhalter, der Besuchern hilft, ist in der Administration vielleicht nicht gewünscht,
oder umgekehrt.

**Kann ich das im Browser ändern?** Die Einstellungsseite der Administration zeigt INI-Einstellungen; dieser Kurs
verwendet `exp:ini`, das zeilenweise schreibt und Kommentare erhält.

**Und ezfind?** Mit aktivem ezfind schaltet dessen eigene `site.ini.append.php` Platzhalter und leere Suche bereits
ein; seine Feinabstimmung steht in `ezfind.ini` (Modul 4).

### Stolperfallen

- **Den INI-Cache vergessen.** Leeren Sie ihn nach jeder Änderung (`ezcache.php --clear-tag=ini`), sonst scheint nichts
  zu passieren.
- **Globale Overrides, die gewinnen.** Ein Wert in `settings/override` schlägt einen Siteaccess-Wert. `exp:ini where`
  zeigt, welche Datei wirksam ist.
- **Experimente zurücklassen.** Beenden Sie die Übung, indem Sie entfernen, was Sie gesetzt haben.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation. `<public siteaccess>` ist ihr öffentlicher Siteaccess. Fügen Sie
`--allow-root-user` nur hinzu, wenn Sie als root arbeiten.

1. Zeigen Sie die Platzhaltereinstellung und jede Datei, die sie setzt:

   ```bash
   php bin/php/console exp:ini where site.ini/SearchSettings/EnableWildcard
   ```

2. Suchen Sie auf der öffentlichen Website als Besucher nach den ersten vier Buchstaben eines Wortes aus den
   Beispielinhalten, gefolgt von `*`, zum Beispiel `heal*`.
3. Schalten Sie Platzhalter für den öffentlichen Siteaccess ein und leeren Sie den INI-Cache:

   ```bash
   php bin/php/console exp:ini set site.ini/SearchSettings/EnableWildcard true siteaccess:<public siteaccess>
   php bin/php/ezcache.php --clear-tag=ini
   ```

4. Wiederholen Sie Schritt 2. Probieren Sie dann zwei Buchstaben und ein `*`, zum Beispiel `he*`.
5. Führen Sie Schritt 1 erneut aus und lesen Sie, welche Datei den Wert jetzt setzt.
6. Sehen Sie sich die Stoppwortregel an: Geben Sie `StopWordThresholdValue` und `StopWordThresholdPercent` mit
   `exp:ini get` aus und schätzen Sie, wie viele Objekte Ihre Schulungs-Website hat (die Beispielinhalte plus Benutzer
   und Medien). Entscheiden Sie, ob die Regel auf Ihrer Website greift.
7. Öffnen Sie `var/backup/ini/` und suchen Sie die Sicherung der Datei, die Sie in Schritt 3 geändert haben.
8. Machen Sie die Änderung rückgängig und leeren Sie den INI-Cache:

   ```bash
   php bin/php/console exp:ini rem site.ini/SearchSettings/EnableWildcard siteaccess:<public siteaccess>
   php bin/php/ezcache.php --clear-tag=ini
   ```

## Erwartetes Ergebnis

- Schritt 2 findet für das unvollständige Wort nichts.
- Schritt 4 findet die Elemente für vier Buchstaben; für zwei Buchstaben findet er nichts, weil
  `MinCharacterWildcard` auf `3` steht.
- Schritt 5 listet die Siteaccess-Datei mit wirksamem `true` auf.
- Schritt 7 zeigt einen datierten Sicherungsordner mit der früheren Version der Datei.
- Nach Schritt 8 zeigt Schritt 1 wieder das mitgelieferte `false` als wirksam.
- Der Trainer prüft Schritt 8 auf jedem Rechner.

## Quiz

1. Welcher Befehl schreibt eine Sucheinstellung nur für den öffentlichen Siteaccess?
2. Platzhalter sind eingeschaltet, `MinCharacterWildcard=3`. Funktioniert `ca*`? Funktioniert `cak*`?
3. Wird die Stoppwortregel auf einer Website mit 80 Objekten angewendet?
   A. ja, immer B. nein, der Schwellenwert von 100 Objekten ist nicht überschritten C. nur mit Platzhaltern D. nur mit
   ezfind
4. Warum ist die Anzahl der Wörter in einem Suchtext begrenzt?
5. Ein Siteaccess-Wert wirkt nicht. Nennen Sie zwei Gründe.

<details>
<summary>Antworten</summary>

1. `php bin/php/console exp:ini set site.ini/SearchSettings/<setting> <value> siteaccess:<public siteaccess>`.
2. `ca*` nicht (zwei Zeichen); `cak*` schon (drei).
3. **B.** Die Regel greift nur oberhalb von `StopWordThresholdValue` Objekten (Kommentar in `settings/site.ini`).
4. Jedes Wort ist eine Bedingung der Abfrage der Suchmaschine; bei zu vielen scheitert die Suche, statt Ergebnisse zu
   liefern.
5. Der INI-Cache wurde nicht geleert; ein Wert in `settings/override` überschreibt ihn (prüfen Sie mit
   `exp:ini where`).

</details>

## Weiterführende Informationen

- [Website-Suche](../../../ersteller/website-ersteller/suche-auf-der-website.md#einstellungen-auf-die-es-ankommt): die Tabelle der
  Einstellungen.
- [Einstellungen über die Kommandozeile ändern: exp:ini](../../../../funktionen/6.0/exp-ini-befehl.md): Geltungsbereiche,
  Sicherungen und Sicherheit.
- [Die Einstellungsseite: sehen, woher jeder INI-Wert kommt](../../../../anleitungen/einstellungsseite.md): Einstellungen in der
  Administration lesen.

## Verwandte Seiten

- [Modul 5: Feinabstimmung und Fehlersuche](README.md)
- [Suche: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
