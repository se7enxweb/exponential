---
title: Kapazitätsplanung
audience: government, hosting, admin, project-manager
category: public-sector
order: 120
summary: Für IT-Teams im öffentlichen Sektor, die Server für eine Exponential-Website dimensionieren. Die Zahlen, die das Repository tatsächlich nennt (Speichergrenzen, Velocity-Worker, OPcache, Festplattenschwellen, gemessener Cache-Durchsatz), was die Last bestimmt, wie Sie Ihre eigene Website mit exp:benchmark messen und wie Sie für Lastspitzen planen.
keywords: Kapazitätsplanung, Dimensionierung, Arbeitsspeicher, CPU, Festplatte, Velocity-Worker, OPcache, APCu, exp:benchmark, exp:system:info, Spitzenlast, Besucheransturm, Beschaffung, Serverspezifikation, capacity planning, sizing, memory, disk, Velocity workers, peak load, traffic surge, procurement, server specification
type: help
---
# Kapazitätsplanung

Diese Seite richtet sich an IT-Teams und Projektleitungen in öffentlichen Stellen, die Server für eine
Exponential-Website spezifizieren müssen, oft für eine Beschaffung oder einen Haushaltsantrag. Sie beantwortet,
welche Zahlen das Repository tatsächlich liefert, was den Verbrauch von Arbeitsspeicher, CPU und Festplatte bestimmt
und wie Sie Ihre eigene Website messen, damit die Spezifikation auf Belegen beruht. Sie nennt keine eigenen Zahlen;
das Installationsbuch sagt dasselbe: „Die Dokumentation nennt wenige feste Zahlen, und dieses Buch erfindet keine
weiteren“ ([Arbeitsspeicher, CPU und Festplatte dimensionieren](../../../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md)).

## Inhalt

1. [Die Zahlen, die das Repository nennt](#die-zahlen-die-das-repository-nennt)
2. [Was die Last bestimmt](#was-die-last-bestimmt)
3. [Messen Sie Ihre eigene Website](#messen-sie-ihre-eigene-website)
4. [Planung für Lastspitzen](#planung-für-lastspitzen)
5. [Eine Serverspezifikation schreiben](#eine-serverspezifikation-schreiben)
6. [Checkliste](#checkliste)

## Die Zahlen, die das Repository nennt

| Element | Wert | Quelle |
|---|---|---|
| PHP `memory_limit`, Untergrenze des Installers | 64M | `settings/setup.ini` |
| PHP `memory_limit`, Zustandsprüfung | unter 128M ein Fehler, unter 256M eine Warnung | [Systeminformationen](../../../../anleitungen/systeminformationen.md) |
| Ein Velocity-Worker | etwa 10 MB privater Arbeitsspeicher (gemessen) | `settings/velocity.ini` |
| Velocity-Pool | `Workers=4` ausgeliefert | `settings/velocity.ini` |
| OPcache | `memory_consumption=256`, `max_accelerated_files=20000` | `velocity.ini [PHPSettings]` |
| Freier Festplattenplatz, Zustandsprüfung | unter 1 GB oder 5 % ein Fehler; unter 5 GB oder 10 % eine Warnung | [Systeminformationen](../../../../anleitungen/systeminformationen.md) |
| SQLite `VACUUM` | etwa die doppelte Größe der Datenbank an freiem Festplattenplatz | [9.2](../../../../installation/09-datenbanken.md#analyze-vacuum-und-die-wal-datei) |
| Gecachte Seiten unter Velocity | etwa 0,3 ms CPU je Seite; etwa 3.500 pro Sekunde auf einer Maschine mit 12 Kernen (gemessen) | `settings/velocity.ini` |

Alles aus [Arbeitsspeicher, CPU und Festplatte dimensionieren](../../../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md),
wo auch die MongoDB-Zahlen und die Grenzen der Log-Rotation stehen. Gemessene Zahlen stammen von den eigenen Maschinen
des Projekts; Ihre werden abweichen.

## Was die Last bestimmt

- **Gecachte Seiten kosten wenig.** Anonyme Besucher werden vom Antwort-Cache oder vom HTTP-Cache ohne PHP bedient
  ([Velocity-Antwort-Cache und HTTP-Cache](velocity-antwortcache-und-http-cache.md)).
- **Gerenderte Seiten** (erste Aufrufe nach einer Leerung, angemeldete Redakteurinnen und Redakteure, Suchen,
  Formulare) sind der Ort, an dem die CPU verbraucht wird.
- **PHP-Prozesse** nutzen in Spitzen bis zu ihrem `memory_limit`; Importe, Paketinstallationen und Bildbearbeitung
  brauchen am meisten.
- **Festplatte** wächst mit den Uploads in `var/storage`, was meist der Hauptfaktor ist; Caches und Logs werden durch
  die Rotation begrenzt und können geleert werden.
- **Cronjobs und Bildvarianten** erzeugen zusätzliche Lastschübe.

## Messen Sie Ihre eigene Website

```bash
php bin/php/console exp:benchmark --save=var/benchmark/base.json --allow-root-user
php bin/php/console exp:system:info --json --allow-root-user
du -sh var/storage var/cache var/log
```

`exp:benchmark` ruft die Startseite, Menüseiten, die Suchseite und die Anmeldeseite der Administration ab und gibt
Median, p90 und p99 aus; standardmäßig geht es schonend vor ([Benchmarking](../../../../anleitungen/benchmarking.md)).
Messen Sie dieselbe Anfrage vor und nach einer Änderung; „ein einzelner Seitenaufruf beweist nichts“
([Bereitstellen](../../../../anleitungen/bereitstellung.md)). Führen Sie die Messung an einer Testkopie mit Produktionsinhalten
aus, nicht an der Live-Website zu einer belebten Zeit.

## Planung für Lastspitzen

Websites des öffentlichen Sektors haben vorhersehbare Spitzen (Fristen, Wahlergebnisse, Schulanmeldungen) und
unvorhersehbare (Unwetter, Notlagen). Für beide gilt:

- Stellen Sie sicher, dass die Seiten, die aufgerufen werden, im Cache liegen und warm sind
  ([Cache-Aufwärmen nach Änderungen](caches-nach-aenderungen-vorwaermen.md));
- vermeiden Sie vollständige Cache-Leerungen und Bereitstellungen direkt vor einer bekannten Spitze;
- wissen Sie, wie Sie die `Workers` von Velocity erhöhen und neu starten, und messen Sie nach jeder Änderung erneut;
- erwägen Sie für Notlagen eine schlanke, weitgehend statische Informationsseite ([Statischer Cache](statischer-cache.md)).

## Eine Serverspezifikation schreiben

Schreiben Sie für eine Beschaffung oder einen Haushaltsantrag die Spezifikation auf Grundlage von Messungen und
kennzeichnen Sie den Rest:

| Element | Wie Sie es angeben |
|---|---|
| CPU-Kerne und Arbeitsspeicher | gemessen an einer Testkopie, plus eine von Ihnen gewählte Reserve; kennzeichnen Sie die Reserve als „Beispielannahme“, bis sie gemessen ist |
| Festplatte für `var/storage` | die heutige Größe (`du -sh`) und das erwartete Wachstum aus Ihren Inhaltsplänen |
| Datenbankgröße | aus **Setup > Systeminformationen**, Karte „Datenbank“ |
| PHP-Worker | die `Workers` von Velocity oder die Größe des PHP-FPM-Pools, die Sie getestet haben |
| Datensicherungen | Platz außerhalb der Maschine für die Generationen, die Sie festgelegt haben |

## Checkliste

- [ ] Produktionspools haben ein `memory_limit` von 256M oder mehr.
- [ ] Die `Workers` von Velocity sind ausdrücklich gesetzt und getestet.
- [ ] Die Festplattenüberwachung verwendet die Schwellenwerte der Systeminformationen.
- [ ] Ein Benchmark einer Testkopie ist als Referenz gespeichert.
- [ ] Die Serverspezifikation trennt gemessene Zahlen von Annahmen.

## Verwandte Seiten

- [Verfügbarkeit](README.md): Übersicht dieses Ordners
- [Velocity-Worker und -Pools](../../../hosting/server/velocity-worker-und-pools.md)
- [Benchmarking einer gehosteten Website](../../../hosting/leistung/benchmark-einer-gehosteten-website.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
