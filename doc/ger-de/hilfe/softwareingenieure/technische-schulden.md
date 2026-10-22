---
title: Mit technischen Schulden umgehen
audience: software-engineer, developer
category: software-engineering
order: 560
summary: Wie technische Schulden in Exponential gefunden, festgehalten und abgebaut werden - die Abschnitte zu bekannten Lücken in den Standards, die PHPStan-Baseline, Prüfungen, die nur an neuen Problemen scheitern, Abkündigungen ohne Entfernungsdatum, Code als einfache Dateien neben klassenbasiertem Code und wie Sie Schulden in kleinen, prüfbaren Schritten abbauen, ohne Installationen zu brechen.
keywords: technische Schulden, Legacy-Code, bekannte Lücken, Baseline, PHPStan, Abkündigung, Refactoring, Modernisierung, klassenbasierte Views, Kompatibilität, schrittweise Verbesserung, Softwareingenieur, technical debt, legacy code, known gaps, deprecation, refactoring, modernization, class-based views, incremental improvement
type: help
---
# Mit technischen Schulden umgehen

Diese Seite richtet sich an Ingenieure, die in einer Codebasis mit langer Geschichte arbeiten: Die Codebasis wurde ab 1999
entwickelt, zuerst unter dem Namen eZ Publish, einem Produkt, das sein Hersteller nach fast 18 Jahren eingestellt hat, und
Exponential führt sie fort. Schulden sind zu erwarten. Wichtig ist, dass sie sichtbar sind, nicht wachsen und abgebaut
werden, ohne Installationen zu brechen.

## Inhalt

1. [Wo Schulden festgehalten werden](#wo-schulden-festgehalten-werden)
2. [Schulden, die nicht wachsen können](#schulden-die-nicht-wachsen-können)
3. [Festgehaltene Beispiele](#festgehaltene-beispiele)
4. [Sie abbauen](#sie-abbauen)
5. [Was Sie nicht tun sollten](#was-sie-nicht-tun-sollten)

## Wo Schulden festgehalten werden

| Aufzeichnung | Was sie enthält |
|---|---|
| Abschnitte **Known gaps** der Standards in `doc/eng-us/standards/` | was das Projekt noch nicht erfüllt, mit Belegen ([Kernel-Architektur, bekannte Ausnahmen und Schulden](../../standards/architektur/kernel-architektur.md#bekannte-ausnahmen-und-technische-schulden)) |
| `phpstan-baseline.neon` | die PHPStan-Befunde, die der Code hatte, als die Prüfung eingeführt wurde |
| `@deprecated`-Tags in `kernel/` und `lib/` | APIs mit einem Ersatz, die weiter funktionieren |
| Upgrade-Hinweise in `doc/eng-us/bc/6.0/` | Änderungen, die eine Handlung brauchten, und ihre Gründe |
| Der Issue-Tracker | einzelne Probleme |

## Schulden, die nicht wachsen können

Die Prüfungen des Projekts sind so gebaut, dass alte Probleme geduldet und neue abgelehnt werden:

- PHPStan scheitert nur an Befunden, die in der Baseline fehlen.
- Der Codestil scheitert nur, wenn eine Änderung einer Datei Verstöße hinzufügt.
- Die Designtests der Administration haben eine leere Freigabeliste; ein Eintrag darf nicht hinzugefügt werden, um eine
  Änderung durchzulassen.

Eine Änderung darf ein altes Problem bestehen lassen; sie darf keines hinzufügen. Siehe
[Statische Analyse und Codestil](statische-analyse-und-codestil.md).

## Festgehaltene Beispiele

Aus den Seiten der Standards:

- `lib/` hängt in mehreren Dateien von Kernel-Klassen ab, ist also "tiefer als der Kernel", nicht unabhängig.
- Einige View-Klassen lesen `$_POST` oder `$_GET` noch direkt.
- `ezpEvent` ist als private API dokumentiert, dennoch verlassen sich Erweiterungen darauf.
- Views, Cronjob-Teile und Befehle als einfache Dateien laufen noch neben der klassenbasierten Form.
- INI-Schalter mischen `enabled`/`disabled` und `true`/`false`.
- Datenbankaktualisierungen werden von Hand angewendet; es gibt keinen Migrations-Runner.
- Einige Testdateien gehören zu keiner Suite und laufen nie.
- Aus der alten Codebasis geerbte `@deprecated`-APIs haben kein Entfernungsdatum.

## Sie abbauen

1. **Klein und getrennt.** Erhöhen Sie die PHPStan-Stufe oder verkleinern Sie die Baseline in kleinen, getrennten
   Änderungen; ein Refactoring je Pull-Request, nie mit einer Korrektur gemischt.
2. **Einstiegspunkte behalten.** Als Code in 6.0.15 in Klassen umzog, behielt jede View, jedes Skript und jeder
   Cronjob-Teil seinen Pfad als einzeiligen Aufruf, sodass sich Includes, Cron-Einträge und die Ausgabe von `--help` nicht
   änderten. Machen Sie es genauso.
3. **Abkündigen, nicht entfernen.** Kennzeichnen Sie eine ersetzte API mit `@deprecated` und ihrem Ersatz und halten Sie
   sie funktionsfähig; kündigen Sie eine Entfernung im Changelog und in einem Upgrade-Hinweis an
   ([Richtlinie zur Rückwärtskompatibilität](richtlinie-zur-rueckwaertskompatibilitaet.md)).
4. **Testen, bevor Sie verschieben.** Fügen Sie einen Test hinzu, der das aktuelle Verhalten festhält, und ändern Sie dann
   den Code.
5. **Die Aufzeichnung aktualisieren.** Schließt sich eine bekannte Lücke, entfernen Sie sie in derselben Änderung aus der
   Liste des Standards.

## Was Sie nicht tun sollten

- Erzeugen Sie die Baseline nicht neu, um einen neuen Befund durchzulassen.
- Benennen Sie vorhandene Bezeichner, Tabellen oder Einstellungen `eZ*` nicht um, um sie zu modernisieren.
- Formatieren Sie in einer funktionalen Änderung keine ganzen Dateien neu: Prüfer können die Änderung nicht sehen.
- Fügen Sie vorhandenen Kernel-Signaturen keine Typdeklarationen hinzu: Erweiterungen, die sie überschreiben, brechen.

## Verwandte Seiten

- [Kernel, lib und Erweiterungen: die Schichtung](kernel-lib-und-erweiterungen.md)
- [Warum die meisten Änderungen klein sind](../erweiterungsentwicklung/teil-4-aktualisieren/warum-die-meisten-aenderungen-klein-sind.md)
- [Eine alte Erweiterung inventarisieren](../erweiterungsentwicklung/teil-4-aktualisieren/bestandsaufnahme-einer-alten-erweiterung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
