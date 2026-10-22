---
title: "Ihre erste Website auf drei Wegen"
audience: student
category: learning
order: 9
summary: Die drei Wege, auf denen Lernende zu einer ersten Exponential-Website kommen - die öffentliche Demo, die Installation mit einem Befehl auf dem eigenen Rechner und der Setup-Assistent im Browser -, dazu Git-Klone für angehende Entwickler, mit dem, was jeder Weg braucht, was Sie damit tun können und was nicht, und welchen Sie für welchen Lernpfad wählen.
keywords: erste website, installieren, demo, exp:install, setup-assistent, kickstarter, git clone, sqlite, lerninstallation, wählen, vergleichen, einsteiger, first site, install, setup wizard, study installation, choose, compare, beginner
type: help
---
# Ihre erste Website auf drei Wegen

Jeder Lernpfad in diesem Ordner beginnt mit einer Website, die Sie nutzen können. Diese Seite vergleicht die Wege, zu
einer zu kommen, damit Sie in fünf Minuten den richtigen wählen. Alle sind dokumentiert in
[Erste Schritte: Exponential installieren](../../anleitungen/erste-schritte.md) und
[Exponential 6.0 installieren](../../INSTALL.md).

## Inhalt

1. [Die Möglichkeiten auf einen Blick](#die-möglichkeiten-auf-einen-blick)
2. [Welcher Weg für Ihren Pfad](#welcher-weg-für-ihren-pfad)
3. [Was Ihnen jede Installation gibt](#was-ihnen-jede-installation-gibt)
4. [Was Sie auf Ihrem Rechner brauchen](#was-sie-auf-ihrem-rechner-brauchen)
5. [Bevor Sie beginnen](#bevor-sie-beginnen)

## Die Möglichkeiten auf einen Blick

| Weg | Braucht | Gut für | Grenzen | Seite |
|---|---|---|---|---|
| **Die öffentliche Demo** | Einen Browser | Einen ersten Blick auf die Website und die Administrationsoberfläche | Mit allen geteilt, regelmäßig zurückgesetzt, Administratorpasswörter können nicht geändert werden; kein Terminal | [Die öffentliche Demo ausprobieren](die-oeffentliche-demo-ausprobieren.md) |
| **Ein Befehl** (`exp:install`) | PHP 8.0 oder neuer, Composer | Jeden Lernpfad; schnell neu aufzubauen | Läuft auf Ihrem Rechner, Sie richten es also selbst ein | [Eine Lernwebsite mit einem Befehl installieren](eine-lernwebsite-mit-einem-befehl-installieren.md) |
| **Der Setup-Assistent** | PHP, Composer, einen Browser | Lernende, die lieber klicken als Befehle eingeben | Langsamer zu wiederholen als ein Befehl | [Mit dem Setup-Assistenten installieren](mit-dem-setup-assistenten-installieren.md) |
| **Git-Klone** | Das oben Genannte, dazu Git | Den Entwicklerpfad und das Beitragen | Ein größerer Download (der Kernel hat seine vollständige Historie) | [Aus Git-Klonen zum Lernen installieren](aus-git-klonen-zum-lernen-installieren.md) |

Ein vierter Installer, der **Kickstarter**, liest seine Antworten aus einer Datei `kickstart.ini`. Er ist dafür
gemacht, dieselbe Installation auf mehreren Servern zu wiederholen ([Erste Schritte](../../anleitungen/erste-schritte.md#33-aus-einer-datei-wiederholbare-installationen));
Lernende brauchen ihn selten.

## Welcher Weg für Ihren Pfad

| Pfad | Erste Wahl | Warum |
|---|---|---|
| Redakteur | Am ersten Tag die Demo, dann ein Befehl | Die Demo zeigt Ihnen alles; auf Ihrer eigenen Website arbeiten Sie, ohne dass andere etwas ändern |
| Administrator | Ein Befehl | Sie werden neu installieren, Einstellungen ändern und Logs lesen |
| Entwickler | Git-Klone | Sie werden die Historie lesen, Code ändern und Korrekturen zurückschicken |
| Designer | Ein Befehl oder Git-Klone | Sie werden Templates ändern und eine Design-Erweiterung anlegen |

## Was Ihnen jede Installation gibt

Nach [Exponential 6.0 installieren](../../INSTALL.md#1-was-sie-erhalten-und-der-schnellstart):

- den Exponential-Kernel und die Erweiterungen, die in `composer.json` aufgeführt sind;
- ein **Site-Paket** mit seinen Inhalten und seinem Design (die Voreinstellung ist `sevenx_multisite`; siehe
  [Site-Pakete für Lernende](site-pakete-fuer-lernende.md));
- drei **Siteaccesses**, immer `site` (Besucher), `admin` (Administratoren) und `editor` (Redakteure: die
  Administrationsoberfläche nur für Inhalte) genannt.

Mit `exp:install` in der Voreinstellung ist die Datenbank **SQLite** in einer Datei, Sie brauchen also keinen
Datenbankserver ([Erste Schritte](../../anleitungen/erste-schritte.md#1-was-sie-brauchen)).

## Was Sie auf Ihrem Rechner brauchen

| Bedarf | Einzelheiten | Quelle |
|---|---|---|
| PHP | 8.0 bis 8.5; prüfen Sie mit `php -v` | [Anforderungen](../../INSTALL.md#2-voraussetzungen) |
| PHP-Einstellungen | `memory_limit` mindestens `64M`, `date.timezone` gesetzt | [Anforderungen](../../INSTALL.md#2-voraussetzungen) |
| Composer | Version 2 | [Erste Schritte](../../anleitungen/erste-schritte.md#1-was-sie-brauchen) |
| Git | 2.28 oder neuer, nur für Git-Klone | [Mit Git entwickeln](../mit-git-entwickeln.md#was-sie-brauchen) |
| Ein Datenbankserver | Für den Anfang keiner: SQLite ist die Voreinstellung | [Datenbankunterstützung](../datenbankunterstuetzung.md) |
| Ein Webserver | Für den Anfang keiner: Exponential kann den eingebauten Server von PHP für Sie starten | [Erste Schritte](../../anleitungen/erste-schritte.md#4-die-site-ansehen) |

Auf einer laufenden Installation listet `php bin/php/console exp:velocity ext check` die PHP-Erweiterungen auf, die die
Installation benötigt, und markiert, was fehlt ([Anforderungen](../../INSTALL.md#2-voraussetzungen)).

## Bevor Sie beginnen

- **Verwenden Sie Ihren eigenen Rechner oder eine Schulungsmaschine**, die Sie verändern dürfen. Installieren Sie
  nicht ohne Erlaubnis auf einem Server Ihrer Schule oder Ihres Arbeitgebers.
- **Bewahren Sie das Administratorpasswort auf**, das der Installer ausgibt. Es wird einmal angezeigt.
- **Verwenden Sie erfundene Daten.** Testkonten bekommen Adressen unter `example.invalid`, sodass keine Übung E-Mails
  an eine echte Person sendet (dieselbe Regel, die Trainer einhalten, [Trainer: hier beginnen](../trainer/trainer-hier-beginnen.md#die-regeln-die-jeder-trainer-einhält)).
- **Notieren Sie Ihr Release** (`php bin/php/console --version`) in Ihrem [Lerntagebuch](ein-lerntagebuch-fuehren.md).

## Verwandte Seiten

- [Die Lernwebsite zurücksetzen und neu aufbauen](die-lernwebsite-zuruecksetzen-und-neu-aufbauen.md)
- [Ihre erste Stunde in der Administrationsoberfläche](ihre-erste-stunde-in-der-administration.md)
- [Exponential 6.0 installieren](../../INSTALL.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
