---
title: "Tutorials: echte Erweiterungen Schritt für Schritt bauen"
audience: developer, app-creator, designer
category: extension-development
order: 900
summary: Schritt-für-Schritt-Tutorials, die echte kleine Erweiterungen bauen, zuerst mit den RAD-Assistenten unter Setup > RAD, dann von Hand, und mit einer vollständigen Erweiterung von der Idee bis zum Release enden. Jede gezeigte Datei wurde mit php -l geprüft und ausgeführt.
keywords: Tutorial, Schritt für Schritt, Assistent, von Hand, Template-Operator, Fetch-Funktion, Modul, Richtlinie, Datentyp, Workflow-Ereignis, Cronjob, persistentes Objekt, Übersetzung, Hilfeseiten, Abschlussprojekt, tutorial, step by step, RAD, wizard, by hand, template operator, fetch function, module, policy, datatype, workflow event, cronjob, persistent object, ajax, custom tag, translation, help pages, phpunit, capstone
type: help
---
# Tutorials: echte Erweiterungen Schritt für Schritt bauen

Diese Tutorials gehören zu [Teil 3](../README.md)
des Buchs. Jedes baut eine kleine, funktionierende Erweiterung und zeigt jede Datei vollständig, die Befehle, mit denen
Sie sie einschalten, und was Sie sehen sollten. Die PHP-Dateien wurden mit `php -l` geprüft und, wo es gefahrlos
möglich war, mit geladenem Kernel ausgeführt; die Template-Beispiele wurden von der Template-Engine gerendert.

## Zwei Wege zum selben Ergebnis

| | Mit den RAD-Werkzeugen | Von Hand |
|---|---|---|
| Wo | **Setup > RAD** (`/setup/rad`) in der Administration | Ihr Editor und ein Terminal |
| Tempo | ein funktionierendes Gerüst in Minuten | Minuten für kleine Dinge, länger für große |
| Was Sie lernen | welche Dateien und Einstellungen ein Mechanismus braucht | warum jede Zeile da ist |
| Am besten für | etwas Neues beginnen; prüfen, dass Sie keine Einstellung vergessen haben | kleine Änderungen; Verständnis; Arbeiten ohne die Administration |

Machen Sie für denselben Mechanismus je eines von beiden und vergleichen Sie die Dateien: So lernen Sie den Aufbau am
schnellsten.

## Mit RAD

| Tutorial | Baut |
|---|---|
| [Der Rundgang durch die RAD-Werkzeuge](rundgang-durch-die-rad-werkzeuge.md) | den Katalog, die Übersicht, die Zustandsprüfung und wie jeder Assistent funktioniert |
| [Ein Template-Operator in fünf Minuten](rad-template-operator.md) | Operatoren, Funktionen, Fetch-Funktionen und Fetch-Aliase |
| [Ein Datentyp](rad-datentyp.md) | eine neue Art von Feld mit ihren Templates |
| [Ein Modul mit Views](rad-modul.md) | ein Modul mit Views und Richtlinien oder ein Modul auf Grundlage vorhandener Tabellen |
| [Ein Workflow-Ereignis](rad-workflow-ereignis.md) | einen Workflow-Ereignistyp mit seinen Templates |
| [Eine Design-Erweiterung](rad-design-erweiterung.md) | ein Theme-Gerüst mit Pagelayout, Override und Stylesheets |
| [Eine Erweiterung nur mit Einstellungen](rad-einstellungs-erweiterung.md) | Einstellungen, Listener und Anpassungen von Siteaccesses |
| [Handler und Kernel-Overrides](rad-handler-und-kernel-overrides.md) | einen Handler, der über eine Einstellung benannt wird, und das letzte Mittel |

## Von Hand

| Tutorial | Baut |
|---|---|
| [Die kleinste Erweiterung in fünf Minuten](von-hand-minimale-erweiterung.md) | Metadaten und Einstellungen, sonst nichts |
| [Ein Template-Operator](von-hand-template-operator.md) | `mysite_greet` und `mysite_initials` |
| [Eine Fetch-Funktion](von-hand-fetch-funktion.md) | `fetch( 'hello', 'newest', ... )` |
| [Ein Modul mit einer View](von-hand-modul-mit-view.md) | `/hello/world/<name>` |
| [Ein Modul, das durch eine Richtlinie geschützt ist](von-hand-modul-mit-richtlinie.md) | eine Admin-Seite, die nur manche Rollen öffnen dürfen |
| [Ein Datentyp](von-hand-datentyp.md) | eine Bewertung von 1 bis 5 |
| [Ein Workflow-Ereignis](von-hand-workflow-ereignis.md) | eine Protokollzeile für jede Veröffentlichung |
| [Ein Cronjob-Teil](von-hand-cronjob-teil.md) | eine nächtliche Zusammenfassung |
| [Ihre eigene Tabelle mit eZPersistentObject](von-hand-eigene-tabelle-mit-persistentem-objekt.md) | eine Tabelle mit Grüßen samt ihrer Klasse |
| [Eine ezjscore-Serverfunktion](von-hand-ajax-serverfunktion.md) | einen Ajax-Aufruf, der mit JSON beantwortet wird |
| [Ein Custom Tag für Rich Text](von-hand-custom-tag.md) | ein Tag, das Redakteure in Rich Text verwenden können |
| [Eine Erweiterung übersetzen](von-hand-uebersetzung.md) | deutsche Texte für alles oben Genannte |
| [Hilfeseiten für Ihre Erweiterung](von-hand-hilfeseiten-fuer-ihre-erweiterung.md) | Seiten und Kontexthilfe im Hilfecenter |
| [Ein PHPUnit-Test](von-hand-phpunit-test.md) | Tests, die ohne Datenbank laufen |

## Abschlussprojekt

[Eine vollständige Erweiterung von der Idee bis zum Release](abschlussprojekt-eine-vollstaendige-erweiterung.md) fügt
die Teile zusammen und veröffentlicht das Ergebnis, nach
[Teil 7](../../teil-7-veroeffentlichen/README.md).

## Was Sie brauchen

Eine Entwicklungsinstallation mit eingeschalteter Debug-Ausgabe
([Eine Entwicklungsinstallation einrichten](../../teil-1-vom-benutzer-zum-entwickler/eine-entwicklungsinstallation-einrichten.md))
und für die RAD-Tutorials einen Administrator mit der Richtlinie `setup/setup`. Alle Tutorials verwenden den
Erweiterungsnamen `mysite_hello`; wählen Sie für echte Arbeit Ihr eigenes Präfix.

## Verwandte Seiten

- Zurück: [Ihre Erweiterung dokumentieren, einschließlich Hilfeseiten](../ihre-erweiterung-dokumentieren.md)
- Weiter: [Der Rundgang durch die RAD-Werkzeuge: Setup > RAD](rundgang-durch-die-rad-werkzeuge.md)
- [RAD-Werkzeuge](../../../../funktionen/6.0/rad-werkzeuge.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
