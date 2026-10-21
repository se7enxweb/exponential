---
title: "Bootcamp 4.1: Die Full-Stack-Entwicklerwoche"
audience: trainer, developer
category: training
order: 41
summary: Fünf Tage, die Entwickler darauf vorbereiten, ein Exponential-Projekt zu leiten, Installation und Git, Inhaltsmodellierung, Templates und Designs, Erweiterungen und Module, Betrieb und Velocity, mit einer Website, die über die Woche gebaut und an Tag 5 begutachtet wird.
keywords: fünf tage, bootcamp, entwicklerwoche, full stack, inhaltsmodellierung, templates, designs, erweiterungen, module, velocity, deploy, tests, abschlussprojekt, five days, developer week, content modeling, extensions, modules, capstone
type: help
---
# Bootcamp 4.1: Die Full-Stack-Entwicklerwoche

Eine Woche reicht, um Entwickler von ihrer ersten Installation bis zu einer begutachteten, auslieferbaren Website mit
einer eigenen Erweiterung zu bringen. An jedem Tag kommt das Abschlussprojekt einen Schritt weiter; Tag 5 gehört der
Begutachtung, den Tests und dem Ausrollen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Workshops und Bootcamps](../README.md) |
| Modul | [Modul 4: Fünftägige Bootcamps](README.md) |
| Dauer | 5 Tage mit je 6 Stunden Einheiten |
| Niveau | Fortgeschrittene bis Experten |
| Zielgruppe | PHP-Entwickler, die Projekte leiten oder bauen werden, 4 bis 10 Teilnehmende |
| Voraussetzungen | PHP, Git, Composer; ein GitHub-Konto |

## Lernziele

Am Ende der Woche können die Teilnehmenden:

- eine Installation aus Git-Klonen einrichten, aktualisieren und ausrollen;
- Inhalte für eine echte Aufgabenstellung modellieren und als Klassen umsetzen;
- ein Design mit Templates, Overrides und Stylesheets über den Packer schreiben;
- eine Erweiterung mit einer Modulansicht, Einstellungen und einem Template-Operator schreiben und testen;
- die Website unter Velocity betreiben und eine Änderung sicher ausrollen.

## Der Ablauf

| Tag | Vormittag (3 Std.) | Nachmittag (3 Std.) | Schritt des Abschlussprojekts |
|---|---|---|---|
| 1 | Installation aus Git, Aktualisierungsroutine ([Mit Git entwickeln](../../../mit-git-entwickeln.md), [Mit Git aktualisieren](../../../mit-git-aktualisieren.md)) | Kernkonzepte und Inhaltsmodellierung ([Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md)) | Die Aufgabenstellung in Klassen umgesetzt |
| 2 | Template-Sprache, Debugging ([Templates und Design](../../../../anleitungen/templates-und-design.md)) | Designs, Overrides, Pagelayout ([Overrides im Template-Editor](../../../../../eng-us/features/6.0/template-editor-overrides.md)) | Voll- und Zeilenansichten für jede Klasse |
| 3 | Erweiterungen, RAD-Werkzeuge, Autoloads ([Erweiterungen](../../../../anleitungen/extensions.md), [RAD-Werkzeuge](../../../../../eng-us/features/6.0/rad-tools.md)) | Module und Ansichten ([Module per Erweiterung überschreiben](../../../../../eng-us/features/6.0/extension-module-override.md)) | Die Erweiterung des Projekts mit einer Modulansicht |
| 4 | INI und Siteaccesses ([exp:ini](../../../../../eng-us/features/6.0/exp-ini-command.md), [INI-Overrides für mehrere Websites](../../../../../eng-us/features/6.0/multi-site-ini-overrides.md)), REST ([REST-API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md)) | Betrieb: Caches, Cronjobs, Velocity ([Die Website ausliefern](../../../../installation/08-die-website-ausliefern.md)) | Einstellungen in der Erweiterung; von Velocity ausgeliefert |
| 5 | Tests und Qualität ([Qualitätsprüfungen](../../../../../eng-us/features/6.0/quality-checks.md)); Ausrollen ([Ausrollen](../../../../anleitungen/bereitstellung.md)) | Code-Review der Abschlussprojekte; Präsentationen; nächste Schritte | Begutachtet und ausgerollt |

Pausen: jeden Tag 15 Minuten am Vormittag und am Nachmittag, 45 Minuten Mittagspause.

## Hinweise für Trainer

- **Eine Aufgabenstellung für die ganze Woche**, zum Beispiel ein Verein, eine kleine Konferenz oder eine Bibliothek.
- **Code-Review an Tag 5**: Paare prüfen gegenseitig ihre Erweiterung anhand der
  [Programmierrichtlinien](../../../../../eng-us/standards/coding-standards.md).
- **Lassen Sie den Kernel unangetastet**: Der gesamte Projektcode gehört in die Erweiterung und das Design.

## Praktische Übung: das Abschlussprojekt der Woche

Aufgabe: eine kleine Konferenz-Website mit Vorträgen, Referenten und einem Programm.

1. Klassen: Talk (Titel, Abstract, Uhrzeit, Raum, Relation zum Referenten), Speaker (Name, Biografie, Foto).
2. Ansichten: eine Programmseite, die Vorträge nach Uhrzeit auflistet; eine Referentenseite, die deren Vorträge
   auflistet.
3. Erweiterung `conference` mit den Overrides, dem Design, einer Modulansicht `conference/schedule` und ihren
   Einstellungen.
4. Unter Velocity ausgeliefert; eine Änderung mit `php bin/php/console exp:velocity deploy` ausgerollt.

## Erwartetes Ergebnis

- Die Programm- und die Referentenseite funktionieren aus der Erweiterung heraus, ohne dass außerhalb davon eine Datei
  geändert wurde, abgesehen von der Aktivierung.
- Der Deploy läuft ohne Fehler, und die Änderung ist sichtbar.
- Jedes Team präsentiert und erhält ein Review.

## Quiz (Ende der Woche)

1. Wohin gehört der Code eines Projekts, und warum?
2. Welcher Befehl rollt eine PHP-Änderung unter Velocity in einem Schritt aus?
3. Nennen Sie zwei Stellen, aus denen der Wert einer Einstellung stammen kann.
4. Warum ist ein Relationsattribut für den Referenten eines Vortrags vorzuziehen?

<details>
<summary>Antworten</summary>

1. In die Erweiterungen und das Design des Projekts, damit Aktualisierungen des Kernels ihn nie überschreiben.
2. `php bin/php/console exp:velocity deploy` ([Die Website ausliefern, 8.3.14](../../../../installation/08-die-website-ausliefern.md#8314-eine-änderung-bereitstellen-expvelocity-deploy)).
3. Zum Beispiel die Standardwerte, eine Erweiterung, der Siteaccess, das Override-Verzeichnis ([Glossar: INI-Datei](../../../../../eng-us/glossary.md)).
4. Der Referent ist ein eigener Inhalt; die Relation hält ein Referentenobjekt für viele Vorträge.

</details>

## Weiterführende Informationen

- [Entwicklung von Erweiterungen](../../../../../eng-us/help/extension-development/README.md)
- [Programmierrichtlinien](../../../../../eng-us/standards/coding-standards.md)

## Verwandte Seiten

- [Modul 4: Fünftägige Bootcamps](README.md)
- [Workshops und Bootcamps: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
