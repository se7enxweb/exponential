---
title: "Multisite und Seitenpakete: Kursübersicht"
audience: trainer, admin, developer
category: training
order: 1
summary: Der Kurs für Administratoren und Entwickler, die mehrere Websites auf einer Installation betreiben und Websites aus Paketen aufbauen. Sechs Module und 26 Lektionen, etwa 22 Stunden Lektionen, von Siteaccesses und der Zuordnung von Anfragen über Einstellungen, Logs und Caches pro Website, websiteübergreifende Links, Pakete, exp:install und den Kickstarter bis zu einem Abschlussprojekt.
keywords: multisite und seitenpakete, multisite, siteaccess, host-zuordnung, sprachweiterleitung, website-erweiterung, logs pro website, caches pro website, seitenpaket, paket-assistent, kickstarter, kursübersicht, multisite and site packages, host matching, MatchOrder, language redirect, site extension, exp:ini, ezurlmultisite, site package, ezpm, package wizard, exp:install, course overview
type: help
---
# Multisite und Seitenpakete: Kursübersicht

Dieser Kurs zeigt, wie eine Exponential-Installation mehrere Websites ausliefert und wie Websites aus Paketen
aufgebaut und über die Kommandozeile installiert werden. Die Teilnehmenden beginnen mit einer Schulungsinstallation mit
einer einzigen Website, bauen sie Schritt für Schritt zu einer Multisite mit einer zweiten Marke und einer zweiten
Sprache aus, halten die Einstellungen, Logs und Caches jeder Website getrennt, lernen dann die Paketwerkzeuge und die
Installer kennen und bauen zum Schluss selbstständig eine Multisite in einem leeren Verzeichnis auf.

## Für wen er gedacht ist

- Administratoren, die mehrere Websites, Marken oder Sprachen auf einer Installation betreiben oder betreiben werden.
- Entwickler, die Multisites, Website-Erweiterungen oder Pakete bauen und Installationen automatisieren.
- Hoster und Agenturen, die Websites aus Seitenpaketen mit `exp:install` oder dem Kickstarter bereitstellen.

## Ergebnis

Nach dem Kurs können die Teilnehmenden eine Multisite planen; Websites und Sprach-Siteaccesses hinzufügen; Anfragen
nach Pfad, Host, Host und Pfad sowie Port zuordnen; Besucher zu ihrer Sprache weiterleiten; die Einstellungen einer
Website in einer Website-Erweiterung halten, mit eigenen Logs und eigenem INI-Cache; Links zwischen Websites zum
Funktionieren bringen; Teams auf ihre Website beschränken; Pakete in der Administration und mit `ezpm` ansehen,
erstellen, exportieren, vergleichen, importieren und installieren; und Websites mit `exp:install` und dem Kickstarter
installieren, neu installieren und neu erzeugen.

## Form und Dauer

| | |
|---|---|
| Lektionen | 26 Lektionen in 6 Modulen |
| Lektionszeit | 21 Stunden 55 Minuten (Präsentation und Übungen) |
| Mit Pausen | etwa 23 Stunden 45 Minuten, wie in den Zeitplänen der Module |
| Form | Präsenz mit Beamer oder online mit Bildschirmfreigabe; jede Person an einem eigenen Rechner mit einer Shell |
| Gruppengröße | Bis zu etwa 10 Teilnehmende pro Trainer: Die Übungen ändern Einstellungen, und der Trainer muss jedes Terminal ansehen können |
| Niveau | Fortgeschrittene bis Experten |

## Voraussetzungen

- Teilnehmende: sicher im Terminal (Verzeichnisse, ein Texteditor, `curl`); haben die Administration von Exponential
  als Redakteur oder Administrator benutzt. Der Kurs Website-Administration oder vergleichbare Erfahrung ist eine gute
  Grundlage.
- Trainer: hat jede Übung auf der Version geprobt, die er unterrichtet, und hat den Kurs Train the Trainer besucht oder
  vergleichbare Erfahrung ([Train the Trainer: Kursübersicht](../../../../eng-us/help/trainers/train-the-trainer/README.md)).

## Übungsumgebung

- **Eine Installation pro Person**, niemals eine gemeinsame: Die Module 2 bis 4 ändern die globalen
  Zuordnungseinstellungen. Installieren Sie jede mit `php bin/php/console exp:install` (Standard-Seitenpaket, SQLite,
  URL-Zuordnung), wie in [Lektion 2.3 von Train the Trainer](../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/one-install-per-learner.md),
  und liefern Sie sie aus wie in [Lektion 2.4](../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/serving-the-lab-sites.md).
- Shell-Zugang als der Benutzer, der die Website ausliefert, `curl` und `sqlite3` (Lektion 6.4).
- Drei weitere frische Code-Verzeichnisse pro Person für Modul 6
  ([Lektion 2.2 von Train the Trainer](../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/getting-the-code-for-a-class.md)).
- Netzwerkzugang zum Paketindex oder die Seitenpakete bereits importiert für eine Offline-Installation.
- Ein Administratorkonto pro Person auf der eigenen Installation; Testbenutzer mit Adressen unter `example.invalid`;
  keine echten E-Mails.

## Module

| Modul | Lektionen | Dauer | Themen |
|---|---|---|---|
| [Modul 1: Siteaccesses für mehrere Websites](modul-01-siteaccesses-fuer-mehrere-websites/README.md) | 4 | 3 Std. 35 Min. | Siteaccesses, Ladereihenfolge, Planung, eine zweite Website, ein Sprach-Siteaccess |
| [Modul 2: Anfragen Siteaccesses zuordnen](modul-02-anfragen-zuordnen/README.md) | 4 | 3 Std. 45 Min. | Pfad, Host, Host und Pfad, Port, die Weiterleitung zur Sprache |
| [Modul 3: Einstellungen, Logs und Caches pro Website](modul-03-einstellungen-logs-und-caches-pro-website/README.md) | 4 | 3 Std. 35 Min. | Website-Erweiterungen, Overrides pro Website, Logs und INI-Cache pro Website |
| [Modul 4: Links zwischen Websites](modul-04-links-zwischen-websites/README.md) | 3 | 2 Std. 25 Min. | Websiteübergreifende Links mit `ezurlmultisite`, eine Administration für mehrere Websites |
| [Modul 5: Seitenpakete](modul-05-seitenpakete/README.md) | 6 | 5 Std. 20 Min. | Seitenpakete, die Paketseiten, `ezpm`, der Assistent, Vergleichen und Importieren, Hochladen und Installieren |
| [Modul 6: Websites mit dem Kickstarter und exp:install installieren](modul-06-websites-installieren/README.md) | 5 | 5 Std. 5 Min. | Installationsmethoden, `exp:install`, der Kickstarter, Neuinstallation, das Abschlussprojekt |

## Lesepfad

Für Trainer, die den Kurs vorbereiten, in dieser Reihenfolge:

1. [10.2 Siteaccesses und Website-Adressen](../../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site),
   [Multisite auf einer Installation](../../hosting/bereitstellung/mehrere-websites-in-einer-installation.md) und
   [Mehrere Websites in einer Installation einrichten](../../../../eng-us/help/creators/website-creators/multisite-setup.md).
2. [Siteaccess nach Host-Präfix und die Weiterleitung zur Sprache](../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md)
   und die Kommentare von `settings/site.ini [SiteAccessSettings]`.
3. [Einstellungen über die Kommandozeile ändern: exp:ini](../../../../eng-us/bc/6.0/console-exp-ini.md),
   [Einstellungen pro Website in Erweiterungen](../../../../eng-us/features/6.0/multi-site-ini-overrides.md) und
   [Logs, INI-Cache und Ablaufzeitstempel pro Website](../../../../eng-us/features/6.0/multi-site-log-and-cache-paths.md).
4. [Links auf Inhalte einer anderen Website](../../../../eng-us/features/6.0/multisite-cross-site-links.md) und
   [Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md).
5. [4.3 Seitenpakete](../../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird),
   [Pakete](../../../anleitungen/pakete.md), [ezpm](../../../../eng-us/features/6.0/ezpm-package-manager-cli.md),
   [Der Paket-Assistent](../../../../eng-us/features/6.0/package-wizard.md),
   [Paketlizenzen und Semantic Versioning](../../../../eng-us/features/6.0/package-licenses-and-versions.md),
   [Vergleichen und Importieren](../../../../eng-us/features/6.0/package-compare-and-import.md) und
   [Stapelverarbeitung im Installer](../../../../eng-us/features/6.0/package-installer-batching.md).
6. [4. Eine Installationsmethode wählen](../../../installation/04-installationsweg-waehlen.md),
   [6. Der Kickstarter](../../../installation/06-kickstarter.md) und [7. Die Installation über die Konsole](../../../installation/07-installation-ueber-die-konsole.md).
7. Als Hintergrund: [Seitenpakete für Hoster](../../hosting/bereitstellung/seitenpakete-fuer-hoster.md) und
   [Seitenpakete und die mitgelieferten Themes](../../../../eng-us/help/creators/website-creators/site-packages-and-shipped-themes.md).
8. Dann die Module der Reihe nach: zuerst die Übersicht jedes Moduls (seine Vorbereitungsliste und sein Zeitplan), dann
   seine Lektionen. Proben Sie jede Übung auf einer frischen Installation der Übungsumgebung; die Module bauen auf den
   Ergebnissen der vorherigen auf.

## Vorgeschlagene Zeitpläne

Die Lektionszeiten ergeben zusammen 21 Stunden 55 Minuten; die Zeitpläne fügen Pausen und eine kurze tägliche
Wiederholung hinzu.

### Vier Tage

| Tag | Vormittag | Nachmittag |
|---|---|---|
| 1 | Modul 1 | Modul 2 |
| 2 | Modul 3 | Modul 4 |
| 3 | Modul 5, Lektionen 5.1 bis 5.3 | Modul 5, Lektionen 5.4 bis 5.6 |
| 4 | Modul 6, Lektionen 6.1 bis 6.4 | Lektion 6.5 (Abschlussprojekt), offene Fragen |

### Zwei getrennte zweitägige Kurse

- **Multisite** (Module 1 bis 4, etwa 13 Stunden 20 Minuten mit Pausen): für Teams, die ihre Installation bereits
  haben.
- **Pakete und Installer** (Module 5 und 6, etwa 10 Stunden 25 Minuten mit Pausen): für Hoster und Entwickler. Vor
  Lektion 5.3 legt jede Person auf ihrer Installation einen Ordner *Site B* mit einem Unterordner *News* an, weil die
  Lektionen 5.3 und 5.5 diese Inhalte verpacken und vergleichen; das Abschlussprojekt (6.5) braucht dann die Module 1
  bis 3 als Lektüre, oder Sie ersetzen es durch die Lektionen 6.2 bis 6.4, wiederholt auf einem Datenbankserver.

## Teilnehmende beurteilen

- Das Quiz und das erwartete Ergebnis jeder Lektion prüfen das Verständnis während des Kurses.
- [Lektion 6.5](modul-06-websites-installieren/abschlussprojekt-eine-multisite-von-grund-auf.md) hat eine Checkliste mit acht Punkten
  für eine abschließende praktische Prüfung.
- Der [Kurs Prüfungen](../../../../eng-us/help/trainers/assessments/README.md) enthält weiteres Material für technische Kurse.
- All das ist die eigene Prüfung des Trainers, keine offizielle Zertifizierung; die Zertifizierungen des
  Partnerprogramms sind davon getrennt ([Zertifizierungen](../../partner/zertifizierungen.md)).

## Verwandte Seiten

- [Trainer: hier beginnen](../../../../eng-us/help/trainers/trainers-start-here.md)
- [Kurskatalog](../../../../eng-us/help/trainers/course-catalogue.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
