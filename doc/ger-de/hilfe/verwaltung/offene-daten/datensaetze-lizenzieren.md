---
title: Datensätze lizenzieren
audience: government, project-manager, editor
category: public-sector
order: 60
summary: Für Menschen, die entscheiden, unter welchen Bedingungen die Datensätze einer öffentlichen Stelle veröffentlicht werden - was eine offene Lizenz erlauben muss, wie Sie die Lizenz in Exponential auf jeder Datensatzseite festhalten und was Sie vor der Wahl prüfen sollten.
keywords: Lizenz, offene Lizenz, Daten lizenzieren, Open Definition, Weiterverwendung, Namensnennung, Richtlinie über offene Daten, Datenlizenz Deutschland, Lizenz des Datensatzes, Auswahlfeld, Nutzungsbedingungen, license, open license, licensing data, re-use, attribution, Open Data Directive, dataset license, selection field, terms of use
type: help
---
# Datensätze lizenzieren

Diese Seite richtet sich an Führungskräfte und Redaktionen, die angeben müssen, unter welchen Bedingungen jeder
Datensatz weiterverwendet werden darf. Sie erklärt, was eine Lizenz offen macht, wie Sie die Lizenz auf einer
Datensatzseite in Exponential festhalten und welche Fragen Sie vor der Wahl mit Ihrer Rechtsberatung klären sollten.
Sie empfiehlt keine bestimmte Lizenz: Diese Wahl gehört Ihrer Organisation.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Was eine offene Lizenz erlauben muss](#was-eine-offene-lizenz-erlauben-muss)
2. [Fragen, die Sie vor der Wahl klären sollten](#fragen-die-sie-vor-der-wahl-klären-sollten)
3. [Die Lizenz in Exponential festhalten](#die-lizenz-in-exponential-festhalten)
4. [Die Lizenz Menschen und Maschinen zeigen](#die-lizenz-menschen-und-maschinen-zeigen)
5. [Daten, die nicht offen sein können](#daten-die-nicht-offen-sein-können)

## Was eine offene Lizenz erlauben muss

Die [Open Definition 2.1](https://opendefinition.org/od/2.1/en/) listet auf, was eine Lizenz erlauben muss, um „offen“
zu sein: Nutzung, Weitergabe, Veränderung, Trennung von Teilen, Zusammenstellung mit anderen Werken, keine
Diskriminierung von Personen, Gruppen oder Einsatzbereichen und die Weitergabe derselben Rechte an jeden, der das Werk
erhält. Sie erlaubt einige wenige Bedingungen, etwa die Pflicht zur Namensnennung oder dass veränderte Fassungen unter
denselben Bedingungen bleiben.

In der EU behandelt die [Richtlinie (EU) 2019/1024](https://eur-lex.europa.eu/eli/dir/2019/1024/oj) Lizenzen für die
Weiterverwendung von Dokumenten des öffentlichen Sektors. Ihr nationales Recht und Ihre Richtlinien können die Lizenzen
nennen, die öffentliche Stellen verwenden sollen.

## Fragen, die Sie vor der Wahl klären sollten

| Frage | Warum sie zählt |
|---|---|
| Hat die Stelle alle Rechte an den Daten? | Daten, die von Dritten gekauft oder mit ihnen erstellt wurden, können deren Bedingungen unterliegen |
| Schreibt eine nationale Richtlinie eine Lizenz vor? | Viele Rechtsordnungen nennen eine Standardlizenz für öffentliche Daten |
| Ist eine Namensnennung gewünscht? | Entscheidet zwischen Lizenzen mit und ohne Bedingung zur Namensnennung |
| Enthalten die Daten personenbezogene Daten? | Personenbezogene Daten werden in der Regel nicht als offene Daten veröffentlicht; anonymisieren oder aggregieren Sie zuerst (siehe „Datenschutzprüfung vor dem Veröffentlichen von Daten“) |
| Gibt es Gründe der Sicherheit, geschäftliche Gründe oder Vertraulichkeit gegenüber Dritten, die eine Zurückhaltung erfordern? | Manche Datensätze oder Spalten müssen unveröffentlicht bleiben |
| Wer zeichnet ab? | Benennen Sie die Rolle, die die Lizenz jedes Datensatzes genehmigt |

## Die Lizenz in Exponential festhalten

Geben Sie der Datensatzklasse ein Pflichtfeld **Lizenz** vom Datentyp Auswahl (`ezselection`), mit einer Option pro
Lizenz, die Ihre Stelle genehmigt hat. Eine Auswahl hält den Wortlaut auf jeder Seite identisch und ermöglicht es,
Datensätze nach Lizenz aufzulisten.

1. **Setup > Klassen**, Gruppe **Content**, die Klasse `dataset`, **Bearbeiten**.
2. Wählen Sie unter **Attribut hinzufügen** die **Auswahl**, klicken Sie auf **Hinzufügen**, benennen Sie sie
   `License`, Bezeichner `license`, kreuzen Sie **Erforderlich** an und entfernen Sie das Häkchen bei **Übersetzbar**
   (die Lizenz ist in jeder Sprache dieselbe).
3. Fügen Sie eine Option pro Lizenz hinzu, mit ihrem offiziellen Kurznamen, zum Beispiel Name und Version so, wie der
   Herausgeber der Lizenz sie veröffentlicht.
4. Klicken Sie auf **OK**.

Wenn Sie den Lizenztext oder seine Adresse auf der Seite brauchen, fügen Sie ein zweites Feld vom Datentyp URL
(`ezurl`) namens `license_url` hinzu, oder halten Sie eine Seite pro Lizenz vor und verknüpfen Sie die Datensätze
mit einer Objektbeziehung damit.

Die Schritte im Klasseneditor finden Sie unter
[Inhaltsklassen für Websites](../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md).

## Die Lizenz Menschen und Maschinen zeigen

- **Auf der Seite**: Zeigen Sie den Namen der Lizenz und einen Link zu ihrem Text in der Nähe der Download-Links,
  nicht in der Fußzeile.
- **In Dateien**: Wo das Format es erlaubt, fügen Sie Lizenz und Quelle in einer Kopfzeile oder einer eigenen Datei
  `README` in der Distribution hinzu.
- **In Feeds**: Ein RSS-Export kann ein Attribut der Beschreibung oder Kategorie jedes Eintrags zuordnen
  ([RSS-Feeds](../../../anleitungen/rss-feeds.md#6-einen-export-oder-einen-import-bearbeiten)); ein Katalog, der `dct:license` in DCAT
  braucht, muss von Ihrem eigenen Template geschrieben werden (siehe „DCAT und nationale Portale“).
- **In der API**: Die REST-API liefert jedes Feld des Objekts, das Lizenzfeld eingeschlossen
  ([REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)).

## Daten, die nicht offen sein können

Nicht jeder Datensatz darf offen sein. Typische Gründe: personenbezogene Daten, Sicherheit, Rechte Dritter oder
Gesetze, die die Weiterverwendung einschränken. Sie können trotzdem eine Beschreibung eines solchen Datensatzes mit der
Lizenzoption „Nicht zur Weiterverwendung verfügbar“ veröffentlichen, damit die Menschen wissen, dass er existiert und
wen sie fragen können.

## Verwandte Seiten

- [Eine Datensatzklasse entwerfen](eine-datensatzklasse-entwerfen.md)
- [Datentypen wählen](../../../../eng-us/help/creators/website-creators/choosing-datatypes.md)
- [Lizenzen für Käufer erklärt](../../geschaeft/lizenzen-fuer-kaeufer.md): die Lizenz der Software, was eine andere Frage ist
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
