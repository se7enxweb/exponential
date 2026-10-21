---
title: "Lektion 2.2: Verzögerte Indizierung und der Cronjob indexcontent"
audience: trainer, admin
category: training
order: 22
summary: Die Teilnehmenden schalten auf ihrer Schulungsinstallation die verzögerte Indizierung ein, beobachten, wie veröffentlichte Inhalte in der Warteschlange warten, und indizieren sie, indem sie den globalen Cronjob-Teil ausführen, in der Shell und unter Setup > Cronjobs. 35 Minuten.
keywords: suchkurs, DelayedIndexing, classbased, DelayedIndexingClassList, indexcontent.php, ezpending_actions, index_object, index_moved_node, cronjob, runcronjobs, warteschlange, verzögerte indizierung, search course, delayed indexing, queue
type: help
---
# Lektion 2.2: Verzögerte Indizierung und der Cronjob indexcontent

Ein großes Objekt beim Veröffentlichen zu indizieren kostet Zeit, und der Redakteur wartet darauf. Die verzögerte
Indizierung verlagert diese Arbeit in einen Cronjob: Das Veröffentlichen stellt das Objekt nur in eine Warteschlange,
und das Skript `indexcontent.php` indiziert es später. Der Preis ist, dass die Suche von Cron abhängt. Diese Lektion
zeigt beide Seiten.

> **Hinweis**
> Ändern Sie Einstellungen nur auf Ihrer eigenen Schulungsinstallation. Jeder Schritt dieser Übung wird am Ende
> rückgängig gemacht.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Modul | [Modul 2: Indizierung](README.md) |
| Dauer | 35 Minuten (10 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren und Hosting-Mitarbeitende |
| Voraussetzungen | [Lektion 2.1](indizierung-beim-veroeffentlichen.md); ein Terminal im Wurzelverzeichnis der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die drei Werte von `[SearchSettings] DelayedIndexing` erklären: `disabled`, `enabled` und `classbased` mit
  `DelayedIndexingClassList[]`;
- die Warteschlangentabelle `ezpending_actions` und ihre beiden Aktionen `index_object` und `index_moved_node`
  beschreiben;
- den globalen Cronjob-Teil, der `indexcontent.php` enthält, in der Shell und unter **Setup > Cronjobs** ausführen;
- erklären, warum eine Website mit verzögerter Indizierung eine funktionierende Crontab braucht.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [10.8 Suchindizierung und Bildverarbeitung](../../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung),
[Such-Backends](../../../hosting/grundlagen/such-backends.md#verzögerte-indizierung-und-cron) und
[Cronjobs](../../../../anleitungen/cronjobs.md).

1. **Die Einstellung** (3 Minuten). `site.ini [SearchSettings] DelayedIndexing` steht bei Auslieferung auf
   `disabled`. `enabled` verzögert jede Klasse; `classbased` verzögert nur die in `DelayedIndexingClassList[]`
   aufgeführten Klassen (Klassenbezeichner), zum Beispiel nur eine große Klasse `file` oder `article`.
2. **Die Warteschlange** (3 Minuten). Mit verzögerter Indizierung schreibt das Veröffentlichen eine Zeile in
   `ezpending_actions`: `index_object` für das Objekt oder `index_moved_node` für ein Verschieben, wenn die
   Suchmaschine Pfade speichert (ezfind). Jede Zeile hat ihre Zeit (`created`); der Cronjob nimmt die ältesten zuerst,
   jeweils 50, und entfernt eine Zeile, sobald sie indiziert ist.
3. **Der Cronjob** (4 Minuten). `indexcontent.php` steht in `[CronjobSettings] Scripts[]`, dem Teil, den **Setup >
   Cronjobs** **Global** nennt und den `runcronjobs.php` ausführt, wenn ihm kein Teilname übergeben wird. Zeigen Sie
   die Seite und die Karte des globalen Teils. Betonen Sie die Folge: **Ohne die Crontab erscheinen neue Inhalte nie
   in der Suche.**

### Häufige Fragen

**Wann sollten wir sie verwenden?** Wenn das Veröffentlichen großer Objekte wegen der Indizierung langsam ist, zum
Beispiel bei langen Dokumenten oder vielen Attributen. Die Dokumentation nennt schnelleres Veröffentlichen als Grund.

**Wie lange warten Redakteure, bis sie ihr Element finden?** Bis zum nächsten Lauf des globalen Teils; das hängt vom
Crontab-Zeitplan der Website ab. Nennen Sie den Redakteuren den Zeitplan; die Hilfe sagt, dass neue Elemente auf
solchen Websites „einige Minuten“ brauchen können.

**Kann ich ein einzelnes Objekt aus der Warteschlange von Hand indizieren?** Führen Sie den globalen Teil aus oder nur
das Skript: `php runcronjobs.php -s <siteaccess> --script=indexcontent.php`.

### Stolperfallen

- **Den INI-Cache vergessen.** Leeren Sie nach dem Ändern der Einstellung den INI-Cache, sonst bleibt der alte Wert
  wirksam.
- **Cron als root ausführen.** Von root angelegte Dateien können die Website später beschädigen. Führen Sie Cron als
  Benutzer der Website aus; der Schalter `--allow-root-user` ist nur für Schulungsrechner gedacht, auf denen alle als
  root arbeiten.
- **Sie eingeschaltet lassen.** Die Übung endet damit, sie wieder auszuschalten, damit sich die nächsten Lektionen wie
  ausgeliefert verhalten.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation und ein Terminal in ihrem Wurzelverzeichnis. `<siteaccess>` ist der
Name Ihres öffentlichen Siteaccess (siehe **Setup > Systeminformationen**, Abschnitt **Siteaccesses**). Fügen Sie
`--allow-root-user` nur hinzu, wenn Sie als root arbeiten.

1. Schalten Sie die verzögerte Indizierung für die ganze Installation ein:

   ```bash
   php bin/php/console exp:ini set site.ini/SearchSettings/DelayedIndexing enabled global
   ```

2. Leeren Sie den INI-Cache:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   ```

3. Legen Sie in der Administration einen **Article** mit dem Titel `Queued plum` an und veröffentlichen Sie ihn, mit
   dem Wort `plum` in den Pflichtfeldern.
4. Suchen Sie mit dem Feld **Suchen...** nach `plum`.
5. Prüfen Sie, dass `indexcontent.php` im globalen Teil steht (die ersten Zeilen der Liste), und führen Sie dann nur
   dieses Skript aus:

   ```bash
   php runcronjobs.php --list
   php runcronjobs.php -s <siteaccess> --script=indexcontent.php
   ```

6. Suchen Sie erneut nach `plum`.
7. Veröffentlichen Sie einen zweiten Artikel mit dem Titel `Queued apricot`. Öffnen Sie diesmal **Setup > Cronjobs**,
   wählen Sie Ihre Website, suchen Sie den Teil **Global** und klicken Sie dort auf **Starten**. Beobachten Sie
   **Ausgabe**.
8. Suchen Sie nach `apricot`.
9. Schalten Sie die verzögerte Indizierung wieder aus und leeren Sie den INI-Cache:

   ```bash
   php bin/php/console exp:ini set site.ini/SearchSettings/DelayedIndexing disabled global
   php bin/php/ezcache.php --clear-tag=ini
   ```

10. Veröffentlichen Sie einen dritten Artikel mit dem Wort `quince` und suchen Sie sofort danach.

## Erwartetes Ergebnis

- Schritt 4 findet nichts: Das Objekt wartet in `ezpending_actions`.
- Schritt 5 listet `indexcontent.php` im globalen Teil auf, und der Skriptlauf indiziert das wartende Objekt;
  Schritt 6 findet es.
- Schritt 7 zeigt den Lauf in **Ausgabe**; Schritt 8 findet den zweiten Artikel.
- Schritt 10 findet den Artikel sofort: Die Indizierung beim Veröffentlichen ist zurück.
- Der Trainer prüft `exp:ini get site.ini/SearchSettings/DelayedIndexing` auf zwei Rechnern: Beide geben `disabled`
  aus.

## Quiz

1. Welcher Wert von `DelayedIndexing` verzögert nur einige Klassen, und welche Einstellung listet sie auf?
2. In welcher Tabelle wartet ein verzögertes Objekt?
   A. `ezsearch_word` B. `ezpending_actions` C. `ezcontentobject_version` D. `ezcronjob_queue`
3. Welches Cronjob-Skript indiziert wartende Objekte, und in welchem Teil wird es ausgeliefert?
4. Eine Website mit verzögerter Indizierung meldet, dass neue Artikel nie gefunden werden. Was prüfen Sie zuerst?
5. Welche Aktion stellt ein Verschieben für eine Suchmaschine, die Pfade speichert, in die Warteschlange?

<details>
<summary>Antworten</summary>

1. `classbased`, mit den Klassenbezeichnern in `DelayedIndexingClassList[]`.
2. **B.** ([10.8](../../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung))
3. `indexcontent.php`, in `[CronjobSettings] Scripts[]`, dem globalen (Standard-)Teil.
4. Ob die Crontab den globalen Teil für diese Website ausführt (**Setup > Cronjobs** zeigt Status und letzten Lauf).
5. `index_moved_node` (das Objekt und jedes Objekt unter seinem Hauptknoten werden neu indiziert).

</details>

## Weiterführende Informationen

- [Cronjobs: ausführen, planen und verfolgen](../../../../anleitungen/cronjobs.md): die Seite, die Crontab und die Shell.
- [Such-Backends](../../../hosting/grundlagen/such-backends.md#verzögerte-indizierung-und-cron): die verzögerte Indizierung
  aus Sicht eines Hosters.
- [Cronjobs](../../../hosting/betrieb/cronjobs.md): Cron auf einem Server planen.

## Verwandte Seiten

- [Modul 2: Indizierung](README.md)
- [Suche: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
