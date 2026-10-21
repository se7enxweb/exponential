---
title: "Lektion 3.4: Suchstatistiken"
audience: trainer, admin
category: training
order: 34
summary: Die Teilnehmenden schalten die Suchstatistiken für den öffentlichen Siteaccess ihrer Schulungsinstallation ein, lesen Setup > Suchstatistiken, machen aus Suchbegriffen ohne Treffer Inhaltsarbeit und wägen die personenbezogenen Daten ab, die die Suchbegriffe enthalten können. 30 Minuten.
keywords: suchkurs, suchstatistiken, LogSearchStats, search/stats, ohne treffer, suchbegriff a bis z, wenigste treffer, jetzt suchen, statistik zurücksetzen, personenbezogene daten, search course, search statistics, found nothing, phrase a to z, fewest results, search now, reset statistics, personal data
type: help
---
# Lektion 3.4: Suchstatistiken

Was Besucher in das Suchfeld eingeben, ist die ehrlichste Rückmeldung, die eine Website bekommt. Suchstatistiken
zeichnen jeden Suchbegriff auf, wie oft danach gesucht wurde und wie viele Ergebnisse er gefunden hat. Sie sind
standardmäßig ausgeschaltet, weil Suchbegriffe personenbezogene Daten enthalten können. Diese Lektion vermittelt, wie
man sie bewusst einschaltet, liest und darauf reagiert.

> **Hinweis**
> Suchbegriffe sind das, was Besucher eingegeben haben. Auf einer echten Website können sie Namen oder andere
> personenbezogene Daten enthalten. Verwenden Sie im Kurs nur Ihre eigenen Testbegriffe, und zeigen Sie nie die
> Statistiken einer Live-Website am Beamer.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Suche](../README.md) |
| Modul | [Modul 3: Suchen in der Praxis](README.md) |
| Dauer | 30 Minuten (10 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren, Inhaltsverantwortliche |
| Voraussetzungen | [Lektion 3.3](website-suche-fuer-besucher.md); ein Terminal im Wurzelverzeichnis der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- `[SearchSettings] LogSearchStats` für einen Siteaccess einschalten und erklären, warum es standardmäßig
  ausgeschaltet ist;
- **Setup > Suchstatistiken** lesen: die Übersichtszahlen, **Einen Suchbegriff finden**, die Liste der Suchbegriffe
  ohne Treffer, die Sortierungen **Suchbegriff A bis Z** und **Wenigste Treffer**;
- einen Suchbegriff mit **Jetzt suchen** erneut ausführen und entscheiden, welche Inhalte oder Wörter fehlen;
- die Statistiken mit **Statistik zurücksetzen** zurücksetzen, wenn sie nicht mehr gebraucht werden.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Links, URL-Aliase, Platzhalter und Suchstatistiken](../../../../anleitungen/urls-und-aliase.md#5-suchstatistiken)
und [Website-Suche](../../../../../eng-us/help/creators/website-creators/site-search.md#search-statistics).

1. **Einschalten** (3 Minuten). Eine Suche zählt nur, wenn der Siteaccess, in dem der Besucher sucht,
   `LogSearchStats=enabled` hat, meist gesetzt in `settings/siteaccess/<public siteaccess>/site.ini.append.php`. Die
   Seite nennt, welche Siteaccesses Suchen aufzeichnen, oder warnt, dass keiner es tut.
2. **Lesen** (4 Minuten). Die Übersicht zählt verschiedene Suchbegriffe, alle Suchen, Suchbegriffe ohne Treffer (ein
   Link listet sie auf) und ihren Anteil. Die Tabelle **Suchbegriffe** zeigt jeden Suchbegriff mit der Anzahl seiner
   Suchen, einem Balken im Vergleich zum meistgesuchten Begriff der Seite und der durchschnittlichen Anzahl der
   Ergebnisse. **Einen Suchbegriff finden** durchsucht die Suchbegriffe; die Sortierungen sind **Am häufigsten
   gesucht** (Standard), **Suchbegriff A bis Z** und **Wenigste Treffer**.
3. **Handeln** (3 Minuten). Ein oft gesuchter Begriff ohne Treffer deutet auf fehlende Inhalte hin oder auf Wörter,
   die die Inhalte nicht verwenden: Fügen Sie die Inhalte hinzu oder die Wörter (ein Synonym im Text oder in den
   Schlüsselwörtern). **Jetzt suchen** führt den Suchbegriff in der Suche der Administration aus, um zu sehen, was er
   heute findet. **Statistik zurücksetzen** fragt an Ort und Stelle nach und entfernt dann alle Suchbegriffe
   endgültig.

### Häufige Fragen

**Ist es rechtlich zulässig, das einzuschalten?** Die Dokumentation sagt, dass Suchbegriffe personenbezogene Daten
enthalten können, und überlässt die Entscheidung dem Website-Eigentümer. Dieser Kurs gibt keine Rechtsberatung; der
Eigentümer muss sich selbst beraten lassen.

**Warum erscheint die Admin-Suche nicht in den Statistiken?** Aufgezeichnet werden nur Suchen in Siteaccesses mit
eingeschaltetem `LogSearchStats`; schalten Sie es nur für den öffentlichen Siteaccess ein.

**Wer darf die Seite sehen?** Sie ist eine View des Moduls `search` (`search/stats`); geben Sie den Zugang nur Personen,
die sehen dürfen, was Besucher eingegeben haben.

### Stolperfallen

- **Global einschalten.** Die Teilnehmenden setzen es in `settings/override` und zeichnen auch ihre eigenen
  Admin-Suchen auf. Setzen Sie es pro öffentlichem Siteaccess.
- **Echte Suchbegriffe zeigen.** Führen Sie es nie auf einer Live-Website vor; Suchbegriffe können personenbezogene
  Daten enthalten.
- **Das Zurücksetzen vergessen.** Statistiken wachsen endlos. Vereinbaren Sie, wann sie zurückgesetzt werden.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation. `<public siteaccess>` ist der Name ihres öffentlichen Siteaccess.

1. Öffnen Sie **Setup > Suchstatistiken**. Lesen Sie, was die Seite darüber sagt, welche Siteaccesses Suchen
   aufzeichnen.
2. Schalten Sie die Aufzeichnung nur für den öffentlichen Siteaccess ein und leeren Sie den INI-Cache:

   ```bash
   php bin/php/console exp:ini set site.ini/SearchSettings/LogSearchStats enabled siteaccess:<public siteaccess>
   php bin/php/ezcache.php --clear-tag=ini
   ```

3. Durchsuchen Sie die öffentliche Website in einem privaten Browserfenster fünfmal: zweimal nach einem vorhandenen
   Wort, nach einem nicht vorhandenen Wort (zum Beispiel `zzqqxx`) und nach einer falschen Schreibung eines
   vorhandenen Wortes.
4. Laden Sie **Setup > Suchstatistiken** neu. Lesen Sie die Übersichtszahlen.
5. Klicken Sie auf den Link für Suchbegriffe ohne Treffer. Probieren Sie dann die Sortierungen **Suchbegriff A bis Z**
   und **Wenigste Treffer** aus.
6. Geben Sie einen Teil eines Suchbegriffs in **Einen Suchbegriff finden** ein und suchen Sie.
7. Klicken Sie neben dem falsch geschriebenen Suchbegriff auf **Jetzt suchen**. Entscheiden Sie, was Sie auf der
   Website ändern würden.
8. Klicken Sie auf **Statistik zurücksetzen**, lesen Sie die Bestätigung und bestätigen Sie (das sind Ihre eigenen
   Schulungsdaten).
9. Schalten Sie die Aufzeichnung wieder aus und leeren Sie den INI-Cache:

   ```bash
   php bin/php/console exp:ini set site.ini/SearchSettings/LogSearchStats disabled siteaccess:<public siteaccess>
   php bin/php/ezcache.php --clear-tag=ini
   ```

## Erwartetes Ergebnis

- Schritt 1 warnt (auf einer frischen Installation), dass kein Siteaccess Suchen aufzeichnet; nach Schritt 2 nennt er
  den öffentlichen Siteaccess.
- Schritt 4 zählt die Suchbegriffe und Suchen aus Schritt 3; die Suchbegriffe ohne Treffer enthalten `zzqqxx` und die
  falsche Schreibung.
- Schritt 7 öffnet die Suche der Administration mit dem Suchbegriff.
- Nach Schritt 8 ist die Tabelle leer; nach Schritt 9 gibt `exp:ini get` für den Siteaccess `disabled` aus.
- Der Trainer fragt jede Person nach einer Inhaltsmaßnahme aus Schritt 7.

![Suchstatistiken: wonach Besucher gesucht haben und wie viele Ergebnisse sie bekamen](../../../../../eng-us/help/images/trainers/se-stats.webp)

## Quiz

1. Warum ist `LogSearchStats` standardmäßig ausgeschaltet?
2. Wo sollte es eingeschaltet werden, und warum dort?
   A. `settings/override` für alle Siteaccesses B. nur im öffentlichen Siteaccess C. nur im Admin-Siteaccess
   D. in `solr.ini`
3. Ein Suchbegriff wird 40-mal gesucht und findet nichts. Nennen Sie zwei Dinge, die Sie tun könnten.
4. Was bewirkt **Jetzt suchen**?
5. Richtig oder falsch: **Statistik zurücksetzen** lässt sich rückgängig machen.

<details>
<summary>Antworten</summary>

1. Die Statistiken speichern jeden Suchbegriff, den Besucher eingegeben haben, und der kann personenbezogene Daten
   enthalten (Kommentar in `settings/site.ini`).
2. **B.** Nur die Suchen in diesem Siteaccess wollen Sie messen; Admin-Suchen würden die Zahlen verfälschen.
3. Die fehlenden Inhalte hinzufügen oder die Wörter der Besucher zu vorhandenen Inhalten hinzufügen (ein Synonym im
   Text oder in den Schlüsselwörtern)
   ([Links, URL-Aliase, Platzhalter und Suchstatistiken](../../../../anleitungen/urls-und-aliase.md#5-suchstatistiken)).
4. Es führt den Suchbegriff in der Suche der Administration aus, um zu sehen, was er heute findet.
5. **Falsch.** Es entfernt nach einer Bestätigung alle Suchbegriffe endgültig.

</details>

## Weiterführende Informationen

- [Links, URL-Aliase, Platzhalter und Suchstatistiken](../../../../anleitungen/urls-und-aliase.md#5-suchstatistiken):
  die Seite im Detail.
- [Website-Suche](../../../../../eng-us/help/creators/website-creators/site-search.md#search-statistics): Statistiken für
  Website-Ersteller.
- [Aufbewahrung von Logs und Daten](../../../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md): aufgezeichnete Daten nur so
  lange aufbewahren wie nötig.

## Verwandte Seiten

- [Modul 3: Suchen in der Praxis](README.md)
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
