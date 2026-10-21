---
title: "Lektion 5.4: Runbooks für eine Website, die nicht erreichbar oder langsam ist"
audience: trainer, hosting, admin
category: training
order: 54
summary: Die Teilnehmenden arbeiten die Runbooks für eine nicht erreichbare und eine langsame Website an einer Schulungsinstallation mit vorbereiteten Fehlern durch, unterscheiden die Fehlerseiten, messen vor dem Abstimmen und schreiben eine Störungsnotiz. 45 Minuten.
keywords: hosting und betrieb, runbook, website nicht erreichbar, langsame website, störung, fehlerseite, ERR-referenz, database cannot be reached, wartung, vendor fehlt, exp:benchmark, baseline, ANALYZE, störungsnotiz, hosting and operations, site down, slow site, incident, error page, incident note
type: help
---
# Lektion 5.4: Runbooks für eine Website, die nicht erreichbar oder langsam ist

Mitarbeitende in Rufbereitschaft müssen nicht alles wissen; sie brauchen eine kurze Reihenfolge von Prüfungen, die die
Ursache schnell findet. Die Hilfe für Hoster hat Runbooks genau dafür. Diese Lektion probt die zwei häufigsten,
„Website nicht erreichbar“ und „langsame Website“, an Schulungsinstallationen mit Fehlern, die der Trainer vorbereitet
hat.

> **Hinweis**
> Bereiten Sie die Fehler nur auf den eigenen Schulungsinstallationen der Teilnehmenden vor. Machen Sie zum Üben nie
> eine gemeinsam genutzte Website oder eine Kunden-Website kaputt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 5: Monitoring und Logs](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Systemadministratoren und Support-Mitarbeitende in Rufbereitschaft |
| Voraussetzungen | [Lektion 5.3](anfrage-und-sicherheitslogs.md); Module 1 bis 4 |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die ersten fünf Minuten des Runbooks für eine nicht erreichbare Website befolgen: was der Besucher sieht, die
  neuesten Fehler, der Rechner;
- die Fehlerseiten unterscheiden und die Ursache jeder nennen;
- entscheiden, ob überhaupt nichts antwortet, und die Engine oder den Webserver und den Pool prüfen;
- eine langsame Website messen, bevor sie etwas ändern, und die Prüfungen der Reihe nach befolgen;
- eine Störungsnotiz mit Beginn, Erkennung, Ursache, Behebung, Ende und Vorbeugung schreiben.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Runbook: Website nicht erreichbar](../../../hosting/runbooks/website-nicht-erreichbar.md),
[Runbook: langsame Website](../../../hosting/runbooks/langsame-website.md) und
[Kapitel 12, Seiten und Fehler zur Laufzeit](../../../../installation/12-fehlerbehebung.md#129-seiten-und-fehler-zur-laufzeit).

1. **Website nicht erreichbar: die ersten fünf Minuten** (4 Minuten). Notieren Sie genau, was die Website zeigt, mit
   dem Status (`curl -sI`), probieren Sie die Administration, lesen Sie die neuesten Fehler
   (`exp:logs --tail=var/log/error.log`), prüfen Sie Festplatte, Speicher und Last. Eine volle Festplatte verursacht
   die meisten Symptome.
2. **Welche Seite?** (4 Minuten). Zeigen Sie die Tabelle: „We'll be right back“ (503) ist die Datenbank; „Down for
   maintenance“ (503) ist der Wartungsmodus; „missing the software libraries“ (503) ist `vendor/`; „Something went
   wrong“ (500) mit `ERR-...` ist ein nicht abgefangener Fehler; eine leere Seite oder ein 500 ohne Eintrag in
   `error.log` bedeutet, dass PHP fehlgeschlagen ist, bevor Exponential protokollieren konnte. Fehler, die direkt nach
   einer Bereitstellung begonnen haben, werden fast immer durch diese Änderung verursacht.
3. **Langsame Website** (5 Minuten). Eine langsame Website ist eine Zahl, kein Gefühl: `curl`-Zeitmessung und
   `exp:benchmark`, verglichen mit einer gespeicherten Baseline. Dann: Betrifft es alle oder eine Seite? Prüfungen der
   Reihe nach: der Rechner, die Caches, die Engine, OPcache, die Datenbank, der Verkehr, der statische Cache. Ein
   Benchmark ist Last; halten Sie ihn auf einer angeschlagenen Website klein.
4. **Nach der Störung** (2 Minuten). Leeren Sie, was die Behebung braucht, nach jedem Neustart den Antwort-Cache;
   prüfen Sie Startseite, Administration, Anmeldung und Veröffentlichung; schreiben Sie die Notiz; informieren Sie den
   Kunden.

### Häufige Fragen

**Wo wird der Fehler gemeldet, wenn Exponential schuld ist?** Das Runbook verlinkt
[Wenn etwas schiefgeht](../../../wenn-etwas-schiefgeht.md).

**Nur Redakteure finden die Website langsam. Warum?** Angemeldete Benutzer umgehen die anonymen Caches; prüfen Sie die
Datenbank und OPcache.

**Die Website ist nur zu bestimmten Zeiten langsam.** Suchen Sie in den Anfrage-Logs und den Cron-Zeiten nach einem
Cronjob, einer Sicherung, einem Import oder einem Crawler.

**Velocity läuft, antwortet aber nicht.** `exp:velocity restart`; wenn ein Worker festhängt, `exp:velocity kill`,
dann `start`.

### Stolperfallen

- **Beheben vor dem Lesen.** Teilnehmende springen gleich zum Neustart. Bestehen Sie zuerst auf Schritt 1 des
  Runbooks; die Seite des Besuchers nennt meist die Ursache.
- **Vorbereitete Fehler, die bestehen bleiben.** Schreiben Sie jeden Fehler auf, den Sie vorbereiten, und machen Sie
  jeden am Ende der Lektion rückgängig.
- **Benchmarks auf einem gemeinsam genutzten Rechner.** Halten Sie `--requests` klein, damit sich die Teilnehmenden
  nicht gegenseitig ausbremsen.

## Praktische Übung

Arbeiten Sie in Paaren. Der Trainer hat auf der Schulungsinstallation jedes Paars einen Fehler vorbereitet, zum
Beispiel einen angelassenen Wartungsmodus, die Datenbankdatei oder die Zugangsdaten unerreichbar gemacht, `vendor/`
umbenannt oder den Inhalts-View-Cache in einem Override ausgeschaltet. Jedes Paar diagnostiziert seine eigene
Installation.

1. Fragen Sie die Startseite mit Headern an und notieren Sie Status und Titel:
   `curl -s http://127.0.0.1:8088/ | grep -i -m1 '<title>'` und `curl -sI http://127.0.0.1:8088/ | head -5`.
2. Fragen Sie die Anmeldeseite der Administration auf dieselbe Weise an.
3. Lesen Sie die neuesten Fehler: `php bin/php/console exp:logs --tail=var/log/error.log --lines=30`.
4. Prüfen Sie den Rechner: `df -h .`, `free -m`, `uptime`.
5. Prüfen Sie die Engine: `php bin/php/console exp:velocity status`.
6. Prüfen Sie den Wartungsmodus: `php bin/php/maintenance.php status`.
7. Ordnen Sie, was Sie gesehen haben, einer Zeile der Tabelle *Welche Seite sieht der Besucher?* im Runbook für eine
   nicht erreichbare Website zu und nennen Sie die Ursache.
8. Beheben Sie es mit der Maßnahme des Runbooks und leeren Sie dann den Antwort-Cache:
   `php bin/php/console exp:velocity cache clear`.
9. Messen Sie die Website: `php bin/php/console exp:benchmark --requests=20`. Notieren Sie Median und p90.
10. Prüfen Sie: Startseite, Anmeldung an der Administration und eine Testveröffentlichung in Ihrem Kursbereich.
11. Schreiben Sie eine Störungsnotiz in fünf Zeilen: Beginn, Erkennung, Ursache, Behebung, Ende und was eine
    Wiederholung verhindert.

## Erwartetes Ergebnis

- Jedes Paar nennt den vorbereiteten Fehler anhand der Seite des Besuchers und der Logs, bevor es etwas ändert.
- Nach Schritt 8 antworten Startseite und Administration mit `200`.
- Schritt 9 gibt Median, p90 und p99 für die angefragten Seiten aus.
- Jedes Paar hat eine Störungsnotiz mit allen fünf Punkten.
- Der Trainer prüft das, indem er jedes Paar fragt, welche Zeile der Tabelle gepasst hat und welcher Befehl es bewiesen
  hat.

## Quiz

1. Ein Besucher sieht „We'll be right back“ mit Status 503. Was ist die wahrscheinlichste Ursache?
   A. Wartungsmodus B. die Datenbank ist nicht erreichbar C. ein Template-Fehler D. ein voller Cache
2. Wo sehen Sie nach, wenn eine Seite leer ist und `error.log` nichts Neues enthält?
3. Was tun Sie zuerst, wenn ein Kunde sagt, die Website sei langsam?
4. Fehler haben direkt nach einer Bereitstellung begonnen. Wo liegt die Ursache am wahrscheinlichsten?
5. Warum wird der Antwort-Cache nach einem Neustart geleert und nicht vorher?

<details>
<summary>Antworten</summary>

1. **B.** Die Seite für eine nicht erreichbare Datenbank; „Down for maintenance“ ist der Wartungsmodus
   ([Runbook: Website nicht erreichbar](../../../hosting/runbooks/website-nicht-erreichbar.md#welche-seite-sieht-der-besucher)).
2. Im Fehlerlog des Webservers oder von PHP-FPM: PHP ist fehlgeschlagen, bevor Exponential protokollieren konnte
   (Syntaxfehler, fehlende Erweiterung, Speicher erschöpft).
3. Messen: `curl`-Zeitmessung und `exp:benchmark`, verglichen mit einer Baseline, falls es eine gibt.
4. In dieser Änderung; unter Velocity braucht eine geänderte Klasse oder Einstellung einen Neustart oder
   `exp:velocity deploy`.
5. Würde er vorher geleert, würden Seiten, die in der Zwischenzeit angefragt werden, vom alten Code dargestellt und
   erneut zwischengespeichert.

</details>

## Weiterführende Informationen

- [Runbook: Website nicht erreichbar](../../../hosting/runbooks/website-nicht-erreichbar.md)
- [Runbook: langsame Website](../../../hosting/runbooks/langsame-website.md)
- [Velocity startet nicht](../../../hosting/runbooks/velocity-startet-nicht.md)
- [Kapitel 12: Fehlerbehebung](../../../../installation/12-fehlerbehebung.md)

## Verwandte Seiten

- [Modul 5: Monitoring und Logs](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
