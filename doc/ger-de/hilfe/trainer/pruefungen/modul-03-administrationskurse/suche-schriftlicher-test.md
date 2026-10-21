---
title: "Lektion 3.3: Suche, schriftlicher Test"
audience: trainer, admin, developer
category: training
order: 33
summary: Ein schriftlicher Test mit zwölf Fragen für das Ende des Kurses Suche, zur Schnellsuche und erweiterten Suche, zum Inhaltsfinder, zur eingebauten Suchmaschine und ihren Grenzen, zu verzögerter Indizierung und Cron, zum Neuaufbau des Index, zu Solr über eZ Find und zu Suchstatistiken, mit einem Lösungsschlüssel, der die Dokumentation zitiert. 25 Minuten für die Teilnehmenden.
keywords: prüfungen und praktische übungen, suche, schriftlicher test, schnellsuche, erweiterte suche, inhaltsfinder, eZSearchEngine, verzögerte indizierung, indexcontent, updatesearchindex, Solr, eZ Find, LogSearchStats, assessments and practical exercises, search, written test, quick search, advanced search, content finder, delayed indexing
type: help
---
# Lektion 3.3: Suche, schriftlicher Test

Dieser Test schließt den Kurs Suche ab. Er deckt beide Seiten ab: was Redakteure und Besucher mit der Suche tun und was
Administratoren ausführen, damit die Suche neue Inhalte weiterhin findet.

> **Hinweis**
> Dies ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung. Die Fragen 1 bis 5 eignen sich für
> Redakteure; die Fragen 6 bis 12 für Administratoren und Entwickler.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Prüfungen und praktische Übungen](../README.md) |
| Modul | [Modul 3: Die Administrationskurse prüfen](README.md) |
| Dauer | 40 Minuten (5 Einweisung, 25 Test, 10 Besprechung der Antworten) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Teilnehmende am Ende des Kurses Suche |
| Voraussetzungen | Der Kurs Suche; [Lektion 1.2](../modul-01-pruefungen-gestalten/schriftliche-tests-schreiben.md) für den Trainer |

## Lernziele

Wer diesen Test besteht, hat gezeigt, dass er Folgendes kann:

- eine Suche in der Administration eingrenzen und ein Element über Nummer oder Adresse öffnen;
- erklären, warum ein vorhandenes Element nicht gefunden wird;
- erklären, wo der eingebaute Index liegt und was die verzögerte Indizierung ändert;
- den Befehl nennen, der den Index neu aufbaut, und wann er auszuführen ist;
- beschreiben, was Solr über eZ Find hinzufügt und was es braucht.

## Hinweise für Trainer

### So führen Sie ihn durch

1. Teilen Sie die Fragen ohne den Schlüssel aus; 25 Minuten.
2. Gehen Sie die Antworten mit [Suchen](../../../suchen.md) und
   [Such-Backends](../../../hosting/grundlagen/such-backends.md) auf dem Projektor durch.

### Häufige Fragen

**Wir unterrichten nur die eingebaute Suche.** Streichen Sie die Fragen 11 und 12 und passen Sie die Bestehensgrenze
entsprechend an.

**Ist `LogSearchStats` wirklich eine Frage des Datenschutzes?** Die Statistik speichert jede Phrase, die ein Besucher
eingegeben hat, und diese kann personenbezogene Daten enthalten; deshalb steht Frage 10 im Test.

### Stolperfallen

- **Platzhalter.** Teilnehmende, die an andere Systeme gewöhnt sind, erwarten, dass `*` funktioniert. Bei der
  eingebauten Suchmaschine ist `EnableWildcard` im Auslieferungszustand ausgeschaltet.

## Der Test

1. Welche Taste setzt den Fokus in das Suchfeld der Administration?
2. Wie suchen Sie nur unterhalb des Orts, den Sie gerade ansehen?
3. Welches Feld der erweiterten Suche beschränkt die Ergebnisse auf einen Inhaltstyp, etwa *Article*?
4. Sie kennen die Adresse eines Elements auf der öffentlichen Website. Wo geben Sie sie ein, um das Element in der
   Administration zu öffnen?
5. Eine Redakteurin findet ein Element nicht, das sicher existiert. Nennen Sie drei mögliche Gründe.
6. Wo hält die eingebaute Suchmaschine ihren Index?
7. Was ändert sich, wenn `[SearchSettings] DelayedIndexing` auf `enabled` steht?
8. Auf einer Website mit verzögerter Indizierung läuft Cron nicht mehr. Was bemerken die Redakteure?
9. Nach einem Datenbankimport sind die Suchergebnisse falsch. Welcher Befehl baut den Index neu auf, und was bewirkt
   `--clean`?
10. Warum ist `LogSearchStats` im Auslieferungszustand ausgeschaltet?
11. Was braucht Solr über eZ Find, was die eingebaute Suche nicht braucht?
12. Welcher Befehl sagt Ihnen, ob ein konfiguriertes Solr antwortet?

<details>
<summary>Antworten</summary>

1. `/`, wenn der Fokus nicht in einem anderen Feld steht ([Suchen](../../../suchen.md#die-schnellsuche)).
2. Setzen Sie im **Suchbereich** **Wo** auf **Aktueller Ort**
   ([Suchen](../../../suchen.md#die-suche-eingrenzen)).
3. **Klasse** ([Suchen](../../../suchen.md#die-erweiterte-suche)).
4. Im **Inhaltsfinder** unter dem Baum, dann **Finden**
   ([Suchen](../../../suchen.md#ein-objekt-über-seine-nummer-oder-adresse-öffnen)).
5. Drei von: Die Rolle verbirgt es, es liegt im Papierkorb, es ist nur ein Entwurf, der Index ist noch nicht
   nachgezogen ([Suchen](../../../suchen.md#tipps)).
6. In der Datenbank der Website ([Such-Backends](../../../hosting/grundlagen/such-backends.md#die-zwei-optionen)).
7. Veröffentlichen stellt das Objekt nur in `ezpending_actions` in die Warteschlange; `indexcontent.php` im
   Standard-Cronjob-Teil indiziert es später
   ([Such-Backends](../../../hosting/grundlagen/such-backends.md#verzögerte-indizierung-und-cron)).
8. Neue Inhalte erscheinen nie in der Suche
   ([Such-Backends](../../../hosting/grundlagen/such-backends.md#verzögerte-indizierung-und-cron)).
9. `php bin/php/updatesearchindex.php --clean --allow-root-user`; `--clean` entfernt zuerst den alten Index
   ([Such-Backends](../../../hosting/grundlagen/such-backends.md#den-index-neu-aufbauen)).
10. Die Statistik speichert jede Phrase, die ein Besucher eingegeben hat, und diese kann personenbezogene Daten
    enthalten ([Such-Backends](../../../hosting/grundlagen/such-backends.md#die-eingebaute-suche)).
11. Einen Solr-Server, lokal oder entfernt, die Erweiterung `ezfind` und eingeschaltete `[SolrSettings]` in
    `solr.ini` ([Such-Backends](../../../hosting/grundlagen/such-backends.md#solr-über-ez-find)).
12. `php bin/php/console exp:solr status` (oder `ping`)
    ([Such-Backends](../../../hosting/grundlagen/such-backends.md#der-befehl-expsolr)).

</details>

## Bewertung

- Ein Punkt pro Frage; Frage 5 braucht für den Punkt drei Gründe.
- Bestanden: 8 von 12 Punkten oder mehr, mit Frage 8 richtig.
- Nur Redakteure (Fragen 1 bis 5): bestanden mit 4 von 5.

## Weiterführende Literatur

- [Suchen](../../../suchen.md): die Seite der Redakteure.
- [Such-Backends](../../../hosting/grundlagen/such-backends.md): Suchmaschinen, Indizierung und Solr.
- [10.8 Suchindizierung und Bildverarbeitung](../../../../installation/10-nach-der-installation.md#108-suchindex-und-bildverarbeitung).

## Verwandte Seiten

- [Lektion 3.4: Suche, praktische Aufgabe](suche-praktische-aufgabe.md)
- [Modul 3: Die Administrationskurse prüfen](README.md)
- [Prüfungen und praktische Übungen: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
