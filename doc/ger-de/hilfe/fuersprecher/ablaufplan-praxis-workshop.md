---
title: Ablaufplan für einen Praxis-Workshop
audience: advocate
category: advocacy
order: 43
summary: Ablaufpläne für einen 90-minütigen und einen halbtägigen Praxis-Workshop zu Exponential - installieren, bearbeiten und veröffentlichen, eine Seite mit Layouts bauen, eine Inhaltsklasse definieren, ein Template überschreiben, eine Erweiterung mit den RAD-Werkzeugen schreiben, eine Korrektur senden - mit Voraussetzungen, Übungen, Kontrollpunkten und Hinweisen für die Moderation.
keywords: Workshop, Praxis, Schulung, Tutorial, Installation, Inhaltsklasse, Template-Override, Erweiterung, RAD-Werkzeuge, Layouts, Pull Request, Übungen, Moderation, Teilnehmende, workshop, hands-on, training, install, content class, template override, extension, RAD tools, exercises, facilitator, participants
type: help
---
# Ablaufplan für einen Praxis-Workshop

Diese Seite ist für Fürsprecher, die möchten, dass Menschen mit Exponential **etwas tun**, nicht nur zuschauen. Sie
bietet zwei Formate, eine 90-minütige Einführung und einen halbtägigen Workshop für Website-Ersteller, mit Übungen, die
jeweils mit etwas enden, das die Teilnehmenden prüfen können. Jede Übung verlinkt die Dokumentationsseite, der sie folgt,
damit die Teilnehmenden allein weitermachen können.

## Inhalt

1. [Vor dem Workshop](#vor-dem-workshop)
2. [Format A: Einführung in 90 Minuten](#format-a-einführung-in-90-minuten)
3. [Format B: halbtägiger Workshop für Website-Ersteller](#format-b-halbtägiger-workshop-für-website-ersteller)
4. [Hinweise für die Moderation](#hinweise-für-die-moderation)
5. [Handzettel](#handzettel)

## Vor dem Workshop

**Die Teilnehmenden bringen** einen Laptop mit PHP 8.1 oder später, Composer und Git mit sowie ein GitHub-Konto für die
letzte Übung. Prüfen Sie die PHP-Anforderung unter [Betriebssystemunterstützung](../betriebssystemunterstuetzung.md#was-das-betriebssystem-bereitstellen-muss).

**Sie bereiten vor**:

- einen getesteten Installationsweg für Linux, macOS und Windows (oder ein gemeinsames Container-Image, das Sie selbst
  gebaut haben);
- eine Rückfallebene: einige Ersatz-Laptops oder eine vorinstallierte Kopie pro Person auf einem Rechner, den Sie kontrollieren;
- den Handzettel unten, gedruckt oder als Link;
- eine helfende Person für je acht bis zehn Teilnehmende.

> **Warnung**
> Jede teilnehmende Person installiert ihre eigene Kopie. Lassen Sie eine Gruppe nie ein Administratorkonto oder eine
> Installation mit echten Daten gemeinsam nutzen ([Sicherheit bei Live-Demos](sicherheit-bei-live-demos.md)).

## Format A: Einführung in 90 Minuten

| Zeit | Übung | Kontrollpunkt | Folgt |
|---|---|---|---|
| 0:00 | Begrüßung, was Exponential ist (5 Min.) | | [Die Geschichte in zwei Minuten](die-geschichte-in-zwei-minuten.md) |
| 0:05 | Mit einem Befehl auf SQLite installieren | Die Administration öffnet sich; Passwort geändert | [Installation mit einem Befehl](../../../eng-us/features/6.0/install-in-one-command.md) |
| 0:25 | Rundgang durch die Administration | Alle haben die Hilfe-Schaltfläche „?“ gefunden | [Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md) |
| 0:35 | Einen Artikel erstellen, bearbeiten, in der Vorschau ansehen und veröffentlichen; Versionen vergleichen | Zwei Versionen sichtbar unter **Versionen verwalten** | [Inhalte erstellen und bearbeiten](../inhalte-bearbeiten.md), [Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md) |
| 0:55 | Eine Sprache hinzufügen und den Artikel übersetzen | Der Artikel hat zwei Sprachkennzeichen | [Sprachen](../sprachen.md) |
| 1:10 | Eine Seite im Layouts-Editor bauen | Ein Block hinzugefügt und veröffentlicht | [Der Layouts-Editor](../layout-editor.md) |
| 1:25 | Wie es weitergeht; Community-Kanäle | Alle haben die Links | [Die Community-Kanäle](community-kanaele.md) |

## Format B: halbtägiger Workshop für Website-Ersteller

Die ersten drei Übungen aus Format A, dann:

| Zeit | Übung | Kontrollpunkt | Folgt |
|---|---|---|---|
| 0:40 | Eine Inhaltsklasse „Event“ mit Titel, Datum und Text definieren | Ein Event lässt sich anlegen | [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| 1:10 | Herausfinden, welches Template eine Seite zeichnet; mit einem Override, das sich rückgängig machen lässt, etwas ändern | Die Seite zeigt die Änderung | [Templates und Design](../../anleitungen/templates-und-design.md) |
| 1:50 | Pause | | |
| 2:00 | Eine erste Erweiterung mit den RAD-Werkzeugen schreiben, aktivieren, in der Übersicht sehen | Die Erweiterung erscheint unter **Setup > RAD** | [RAD-Werkzeuge](../../../eng-us/features/6.0/rad-tools.md), [Erweiterungen, Abschnitt 6](../../anleitungen/extensions.md#6-eine-eigene-extension-bauen) |
| 2:50 | Als Git-Klone installieren; eine kleine Korrektur an der Dokumentation machen; einen Pull Request an den eigenen Fork oder das Projekt eröffnen | Ein Link zum Pull Request | [Mit Git entwickeln](../mit-git-entwickeln.md), [Die Hilfe verbessern](die-hilfe-verbessern.md) |
| 3:30 | Abschluss: was die Teilnehmenden gebaut haben; nächste Schritte | | [Die Stufen der Fürsprache](die-stufen-der-fuersprache.md) |

Die Übung mit dem Pull Request ist freiwillig und nur mit dem eigenen GitHub-Konto jeder teilnehmenden Person. Bitten
Sie Teilnehmende nicht, „zur Übung“ Pull Requests ohne echte Änderung an das Projekt zu eröffnen; eine echte, kleine
Korrektur (ein Tippfehler, ein klarerer Satz) ist ein besserer erster Beitrag.

## Hinweise für die Moderation

- **Beginnen Sie früh mit der Installation.** Der Installationsschritt dauert am längsten, wenn einem Laptop eine
  PHP-Erweiterung fehlt; Helfende gehen durch den Raum. [Einen Rechner in zwei Minuten prüfen](../betriebssystemunterstuetzung.md#einen-rechner-in-zwei-minuten-prüfen) hilft.
- **Verwenden Sie erfundene Inhalte.** „Jährlicher Fahrradtag“, nicht die Veranstaltung eines echten Kunden.
- **Bilden Sie Paare.** Schnellere Teilnehmende helfen langsameren; das baut Community auf.
- **Schreiben Sie Fragen auf.** Beantworten Sie sie später mit Quellen; veröffentlichen Sie die Antworten danach im Forum.
- **Barrierefreiheit.** Große Schriften am Projektor, Material, das mit Screenreadern lesbar ist, stündlich Pausen
  ([Barrierefreie Folien und Grafiken](barrierefreie-folien-und-grafiken.md)).
- **Sammeln Sie Rückmeldungen** mit drei Fragen: Was hat funktioniert, was war schwer, wie geht es weiter. Keine
  personenbezogenen Daten nötig.

## Handzettel

```text
EXPONENTIAL WORKSHOP                                               <date>, <place>

Install            ./console exp:install --dry-run   then   ./console exp:install
First sign-in      change the generated password; delete var/log/initial-admin-password
Help               the "?" in the header of every page; Help center: /exphelp/dashboard
Exercises          (list with the help page of each)
Build              Setup > Classes   |   Templates and design guide   |   Setup > RAD
Contribute         Develop with Git  |   Edit on GitHub on every help page
Ask                share.exponential.earth/forums   |   t.me/exponentialcms
Report a bug       Help > Report an error   (never passwords or personal data)
Security problems  privately, as SECURITY.md describes
```

## Verwandte Seiten

- [Einen Beitrags-Sprint vorschlagen](einen-beitrags-sprint-vorschlagen.md)
- [Ein Tutorial schreiben, das funktioniert](ein-tutorial-schreiben.md)
- [Checklisten für Veranstaltungen](checklisten-fuer-veranstaltungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
