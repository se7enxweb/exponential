---
title: "Modul 7: Erweiterungen"
audience: trainer, developer
category: training
order: 70
summary: Das siebte Modul des Kurses Entwickler. Die Teilnehmenden erfassen den Aufbau einer Erweiterung, geben ihrer eigenen Metadaten und eine Lizenz, erzeugen Autoloads neu und aktivieren Erweiterungen, deklarieren Abhängigkeiten für die Ladereihenfolge und schreiben einen Template-Operator. 3 Stunden 55 Minuten.
keywords: entwickler, modul 7, erweiterung, ezinfo.php, extension.xml, lizenz, autoloads, ezpgenerateautoloads, ActiveExtensions, Setup Extensions, ladereihenfolge, abhängigkeiten, template-operator, eztemplateautoload, developers course, module 7, extensions, license, loading order, dependencies, template operator
type: help
---
# Modul 7: Erweiterungen

Seit Modul 1 haben die Teilnehmenden `mysite_hello` Stück für Stück aufgebaut. Dieses Modul macht daraus eine
richtige Erweiterung: eine Übersicht über ihren Inhalt, ihre Identität und Lizenz, die Autoloads und die
Aktivierung, die ihren Code erreichbar machen, ihren Platz in der Ladereihenfolge und als letztes Stück einen
Template-Operator.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Dauer | 3 Stunden 55 Minuten (mit einer Pause von 10 Minuten) |
| Lektionen | 5 |
| Niveau | Fortgeschrittene bis Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 7.1: Der Aufbau einer Erweiterung](der-aufbau-einer-erweiterung.md) | 40 Minuten | Jede Datei von `mysite_hello` ihrem Erweiterungspunkt und ihrer Einstellung zuordnen |
| [Lektion 7.2: extension.xml, ezinfo.php und eine Lizenz](extension-xml-und-ezinfo.md) | 45 Minuten | Die Metadaten schreiben, eine Lizenz hinzufügen, die Seite **Über** prüfen |
| [Lektion 7.3: Autoloads und Aktivierung](autoloads-und-aktivierung.md) | 45 Minuten | `ezpgenerateautoloads.php -e`, **Setup** > **Extensions**, Klasse nicht gefunden |
| [Lektion 7.4: Ladereihenfolge und Abhängigkeiten](ladereihenfolge-und-abhaengigkeiten.md) | 40 Minuten | Die Reihenfolge lesen, `<uses>` deklarieren, die tatsächliche Ladeposition sehen |
| [Lektion 7.5: Ein Template-Operator](ein-template-operator.md) | 55 Minuten | `mysite_initials` schreiben, registrieren und verwenden |

## Dieses Modul vorbereiten

- Alle Teilnehmenden haben `mysite_hello` mit den Stücken aus den Modulen 3 bis 6 in einem eigenen Git-Repository
  committet.
- `xmllint` ist auf den Rechnern der Teilnehmenden installiert (Lektion 7.2); prüfen Sie es mit `xmllint --version`.
- Ein Administratorkonto für die Seite **Extensions** und die Seite **Über**.
- Proben Sie Schritt 3 von Lektion 7.3 (die absichtlich nicht gefundene Klasse), damit Sie die Debug-Ausgabe am
  Projektor zeigen können.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 7.1 | 40 |
| Lektion 7.2 | 45 |
| Lektion 7.3 | 45 |
| Pause | 10 |
| Lektion 7.4 | 40 |
| Lektion 7.5 | 55 |
| **Gesamt** | **235** |

## Verwandte Seiten

- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
