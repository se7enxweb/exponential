---
title: "Theme-Design: Kursüberblick"
audience: trainer, designer, developer
category: training
order: 1
summary: Der Kurs für Designer und Frontend-Entwickler, die Themes für Exponential bauen, 8 Module und 30 Lektionen, etwa 24 Stunden Lektionen, von der Fallback-Kette der Designs über das Pagelayout, Overrides, den ezjscore-Packer, die Gestaltungsregeln von admin4, Barrierefreiheit, Rechts-nach-links und Icons bis zum Paketieren, Lizenzieren und Veröffentlichen einer Theme-Erweiterung.
keywords: theme-design, theme-kurs, designer-schulung, design, fallback-kette, pagelayout, override, ezjscore-packer, admin4, token, dunkelmodus, barrierefreiheit, rechts-nach-links, icons, theme-erweiterung, ezpkg, Composer, lizenz, veröffentlichung, kursüberblick, theme design, fallback chain, accessibility, right to left, theme extension, course overview
type: help
---
# Theme-Design: Kursüberblick

Theme-Design ist der Kurs für alle, die das Aussehen von Exponential verändern. Die Teilnehmenden lesen zuerst, wie
eine Seite ihre Templates findet, bauen dann eine eigene Theme-Erweiterung, `training_theme`, und führen sie bis zum
Ende: ihr Pagelayout und ihre Overrides, Stylesheets und Skripte über den Packer, ein Block in der Administration nach
den Gestaltungsregeln von admin4, Barrierefreiheit und Unterstützung für Rechts-nach-links, Icons, Metadaten, ein
Export als `.ezpkg`, ein Git-Repository mit einer `composer.json`, eine Lizenzaufstellung und eine erste
Veröffentlichung in einem lokalen Repository.

## Für wen der Kurs gedacht ist

- Designer und Frontend-Entwickler, die Themes für mit Exponential gebaute Websites erstellen oder anpassen.
- Entwickler, die Administrationsseiten für Erweiterungen bauen und die Gestaltungsregeln von admin4 einhalten müssen
  (die Module 4 und 6 lassen sich für sie auch einzeln unterrichten).
- Agenturen, die Themes veröffentlichen oder verkaufen wollen (Module 7 und 8).

## Ergebnis

Nach dem Kurs können die Teilnehmenden die Design-Kette jedes Siteaccess lesen und das Template hinter jedem Markup
finden; eine Theme-Erweiterung anlegen und einschalten; ein Pagelayout, Overrides und sichere Cache-Blöcke schreiben;
Stylesheets und Skripte über den ezjscore-Packer ausliefern und ein Theme für eine Content Security Policy bereithalten;
Administrations-Markup aus den gemeinsamen Komponenten und Tokens bauen, das im Dunkelmodus und mit erzwungenen Farben
funktioniert; ein barrierefreies, für Rechts-nach-links taugliches Theme erstellen und testen; Icons mitliefern; und
ein Theme paketieren, lizenzieren und veröffentlichen, ohne je einen veröffentlichten Tag zu verschieben.

## Format und Umfang

| | |
|---|---|
| Lektionen | 30 Lektionen in 8 Modulen |
| Lektionszeit | 24 Stunden 15 Minuten (Präsentation und Übungen) |
| Mit Pausen | etwa 25 Stunden 45 Minuten, wie in den Zeitplänen der Module |
| Format | Schulungsraum mit Projektor oder online mit Bildschirmfreigabe; alle Teilnehmenden an einem eigenen Computer mit einer Shell |
| Gruppengröße | Bis zu etwa 10 Teilnehmende pro Trainer: Die meisten Übungen ändern Dateien, und der Trainer prüft Bildschirme und Quellen |
| Niveau | Einsteiger bis Experten, über die Module ansteigend |

## Voraussetzungen

- Teilnehmende: HTML und CSS; grundlegender Umgang mit einer Shell und einem Texteditor; Git-Grundlagen für Modul 7.
  Die Template-Sprache wird dort eingeführt, wo sie gebraucht wird; der Entwicklerkurs (Entwickler, in diesem Bereich
  der Hilfe) unterrichtet sie vollständig.
- Trainer: Erfahrung mit dem Bau einer Theme-Erweiterung auf der unterrichteten Version und der Kurs Train the Trainer
  oder gleichwertige Erfahrung ([Train the Trainer: Kursüberblick](../train-the-trainer/README.md)).

## Einrichtung der Übungsumgebung

- **Eine Installation pro Teilnehmerin oder Teilnehmer**, denn die Teilnehmenden legen Erweiterungen an, ändern
  Einstellungen und schalten Erweiterungen ein und aus. Installieren Sie jede mit `php bin/php/console exp:install`
  ([7. Die Installation über die Konsole](../../../installation/07-installation-ueber-die-konsole.md)) mit einem Paket mit Demo-Inhalten,
  damit Ordner, Artikel und Bilder vorhanden sind ([Das Seitenpaket für eine Schulung wählen](../train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/das-seitenpaket-fuer-den-kurs-waehlen.md)).
  Wie Sie eine Übungsumgebung bauen, bereitstellen und zurücksetzen:
  [Modul 2: Die Übungsumgebung aufbauen](../train-the-trainer/modul-02-die-uebungsumgebung-aufbauen/README.md).
- Ein Administratorkonto pro Person mit `setup/setup` (für **Setup > RAD**) und `uiswitch/design`, dessen
  E-Mail-Adresse auf `example.invalid` lautet. Nie der gemeinsame Administrator einer echten Website.
- Der Siteaccess der Administration in `admin4` (oder `admin4l`) für die Module 4 bis 6.
- Auf jedem Computer der Teilnehmenden: ein aktueller Chromium-basierter Browser mit Entwicklerwerkzeugen, ein
  axe-basierter Barrierefreiheitsprüfer, Git, `xmllint` und nach Möglichkeit Composer.
- Für Lektion 4.5 ein Entwicklungs-Checkout mit den Entwicklungsabhängigkeiten (`vendor/bin/phpunit`).
- Keine echten E-Mails und keine entfernten Repositories: Im Kurs wird nichts veröffentlicht.

## Module

| Modul | Lektionen | Dauer | Themen |
|---|---|---|---|
| [Modul 1: Designs und die Fallback-Kette](modul-01-designs-und-die-fallback-kette/README.md) | 4 | 3 Std. 10 Min. | Design-Ordner, die Kette eines Siteaccess, Template-Pfadkommentare, Website- und Administrationsdesigns |
| [Modul 2: Templates, Overrides und das Pagelayout](modul-02-templates-overrides-und-das-pagelayout/README.md) | 4 | 4 Std. 5 Min. | Der Assistent für Design-Erweiterungen, das Pagelayout, `override.ini`, Cache-Blöcke |
| [Modul 3: Stylesheets und Skripte](modul-03-stylesheets-und-skripte/README.md) | 4 | 2 Std. 55 Min. | Listen in `design.ini`, der ezjscore-Packer, die Routine zum Leeren, Content Security Policy |
| [Modul 4: Die Administration gestalten](modul-04-die-administration-gestalten/README.md) | 5 | 4 Std. 35 Min. | Die Gestaltungsregeln, gemeinsame Komponenten, Tokens und Dunkelmodus, erzwungene Farben, Review und Tests |
| [Modul 5: Barrierefreiheit und Rechts-nach-links](modul-05-barrierefreiheit-und-rechts-nach-links/README.md) | 4 | 3 Std. 40 Min. | Der Stand des Projekts, ein barrierefreier Rahmen, Testen, Rechts-nach-links |
| [Modul 6: Icons](modul-06-icons/README.md) | 2 | 1 Std. 30 Min. | Icon-Themes in einer Erweiterung, Icons in admin4 |
| [Modul 7: Ein Theme paketieren](modul-07-ein-theme-paketieren/README.md) | 4 | 3 Std. 10 Min. | Metadaten, Aktivierung und Ladereihenfolge, `.ezpkg` und Composer, Git |
| [Modul 8: Lizenzieren und veröffentlichen](modul-08-lizenzieren-und-veroeffentlichen/README.md) | 3 | 2 Std. 40 Min. | Lizenzen, Versionen und dauerhafte Tags, Changelog, Release Notes, Checklisten |

## Lesepfad

Für Trainer, die den Kurs vorbereiten, in dieser Reihenfolge:

1. [Templates und Design: das Aussehen einer Seite ändern](../../../anleitungen/templates-und-design.md): die gesamte
   technische Grundlage in einem Leitfaden.
2. [Designs, Siteaccesses und die Fallback-Kette der Designs](../../../../eng-us/help/creators/website-creators/designs-and-the-fallback-chain.md),
   [Das Pagelayout](../../../../eng-us/help/creators/website-creators/the-pagelayout.md),
   [Template-Overrides für Website-Bauer](../../../../eng-us/help/creators/website-creators/template-overrides.md) und
   [View-Cache und Cache-Blöcke](../../../../eng-us/help/creators/website-creators/view-cache-and-cache-blocks.md).
3. [Stylesheets und Skripte für Ihre Website](../../../../eng-us/help/creators/website-creators/stylesheets-and-scripts.md) und
   [Content Security Policy](../../../../eng-us/features/6.0/content-security-policy.md).
4. [Die Administration gestalten](../../../bc/6.0/admin4-gestaltung.md) und [Das Design admin4](../../../../eng-us/features/6.0/admin4-design.md):
   Lesen Sie das erste vollständig; die Module 4 und 6 bauen darauf auf.
5. [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md),
   [Barrierefreiheit für Website-Bauer](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md) und
   [Rechts-nach-links-Sprachen in der Administration](../../../../eng-us/features/6.0/right-to-left.md).
6. [Icon-Themes in Erweiterungen](../../../../eng-us/features/6.0/icon-themes-in-extensions.md).
7. Die Leitfäden für Designer: [Theme-Erweiterungen: hier beginnen](../../../../eng-us/help/designers/extensions/theme-extensions-start-here.md),
   [Ein Theme verteilen: hier beginnen](../../../../eng-us/help/designers/distribution/distribution-start-here.md) und
   [Der Lebenszyklus eines Themes](../../../../eng-us/help/designers/lifecycle/the-life-cycle-of-a-theme.md).
8. Dann die Module der Reihe nach: zuerst jeweils der Überblick des Moduls (seine Vorbereitungsliste und sein
   Zeitplan), dann seine Lektionen. Bauen Sie `training_theme` einmal selbst auf einer Schulungsinstallation, genau
   wie die Übungen es beschreiben.

## Vorgeschlagene Ablaufpläne

Die Lektionszeiten ergeben zusammen 24 Stunden 15 Minuten; die Ablaufpläne fügen Pausen und eine kurze tägliche
Wiederholung hinzu.

### Vier ganze Tage

| Tag | Vormittag | Nachmittag |
|---|---|---|
| 1 | Modul 1 | Modul 2 |
| 2 | Modul 3 | Modul 4 (Lektionen 4.1 bis 4.3) |
| 3 | Lektionen 4.4 und 4.5, Modul 6 | Modul 5 |
| 4 | Modul 7 | Modul 8, offene Fragen |

Modul 6 kommt am dritten Tag vor Modul 5, weil seine Übungen den Dashboard-Block aus Modul 4 fortsetzen.

### Acht halbe Tage (zum Beispiel einer pro Woche)

| Halber Tag | Module |
|---|---|
| 1 | Modul 1 |
| 2 | Modul 2 |
| 3 | Modul 3 |
| 4 | Modul 4, Lektionen 4.1 bis 4.3 |
| 5 | Lektionen 4.4 und 4.5, Modul 6 |
| 6 | Modul 5 |
| 7 | Modul 7 |
| 8 | Modul 8 |

Zwischen den halben Tagen behalten die Teilnehmenden ihre Installation und `training_theme`; jedes Modul baut auf dem
vorigen auf.

### Zwei Tage nur für Administrationsseiten

Für Entwickler, die Administrationsseiten für Erweiterungen bauen: die Lektionen 1.1, 1.4, 2.1, 3.1, 3.3, das ganze
Modul 4, Lektion 5.4 und Modul 6 (10 Stunden 30 Minuten Lektionen).

## Die Teilnehmenden prüfen

- Das Quiz und das erwartete Ergebnis jeder Lektion prüfen das Verständnis während des Kurses; das Peer-Review aus
  Lektion 4.5 und die Checklisten aus Lektion 8.3 sind praktische Prüfungen.
- Das fertige `training_theme` ist eine naheliegende Abschlussaufgabe: Es sollte die Review-Checkliste aus Lektion 4.5
  und die Checkliste „Die Theme-Erweiterung ist bereit“ aus Lektion 8.3 bestehen.
- Der Kurs [Prüfungen](../pruefungen/README.md) enthält weiteres Material. Alle Prüfungen sind Prüfungen des Trainers
  selbst, keine offizielle Zertifizierung; die Zertifizierungen des Partnerprogramms sind davon getrennt
  ([Zertifizierungen](../../partner/zertifizierungen.md)).

## Verwandte Seiten

- [Trainer: hier beginnen](../trainer-hier-beginnen.md)
- [Kurskatalog](../kurskatalog.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
