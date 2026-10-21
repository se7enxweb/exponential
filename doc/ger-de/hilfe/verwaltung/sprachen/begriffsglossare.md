---
title: Begriffsglossare
audience: government, editor, website-creator
category: public-sector
order: 240
summary: Wie Sie mit Exponential ein öffentliches Glossar und eine interne Terminologieliste in mehreren Sprachen führen - eine Begriffsklasse mit einer Übersetzung pro Sprache, eine Glossarseite, Begriffe aus Inhalten verlinken, Schlagworte mit Synonymen für die Suche und einheitliche Übersetzungen.
keywords: Glossar, Terminologie, Begriffe, Definitionen, mehrsprachiges Glossar, Begriffsklasse, eztags Synonyme, Übersetzer, Einheitlichkeit, einfache Sprache, glossary, terminology, terms, definitions, multilingual glossary, term class, eztags synonyms, translators, consistency, plain language
type: help
---
# Begriffsglossare

Diese Seite richtet sich an Redakteure, Übersetzer und Websiteersteller. Ein Glossar hilft Lesern, amtliche Begriffe zu
verstehen, und hilft Übersetzern, jedes Mal dasselbe Wort zu verwenden. In Exponential ist ein Glossar Inhalt: ein
Objekt pro Begriff, in jede Sprache übersetzt wie jede Seite.

## Inhalt

1. [Eine Begriffsklasse](#eine-begriffsklasse)
2. [Die öffentliche Glossarseite](#die-öffentliche-glossarseite)
3. [Begriffe aus Inhalten verlinken](#begriffe-aus-inhalten-verlinken)
4. [Synonyme und Suche](#synonyme-und-suche)
5. [Eine interne Terminologieliste für Übersetzer](#eine-interne-terminologieliste-für-übersetzer)

## Eine Begriffsklasse

Legen Sie unter **Setup > Klassen** eine Klasse `term` an
([Inhaltsklassen für Websites](../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md)):

| Attribut | Datentyp | Übersetzbar |
|---|---|---|
| Begriff | Textzeile | ja |
| Definition | Formatierter Text | ja |
| Auch bekannt als | Textzeile | ja |
| Quelle der Definition (Gesetz, Richtlinie) | URL oder Textzeile | nein |
| Verwandte Begriffe | Objekt Relations Liste | nein |

Objekt Namensschema `<term>`. Jede Übersetzung eines Begriffsobjekts ist der Begriff in dieser Sprache, sodass das
Glossar der französischen Website französische Begriffe zeigt.

## Die öffentliche Glossarseite

Ein Ordner „Glossar“ mit den Begriffen als Unterknoten, nach Namen sortiert. Bei vielen Begriffen ein Ordner pro
Buchstabe oder ein Template, das nach dem Anfangsbuchstaben gruppiert. Die Suche der Website findet Begriffe, wenn
„Begriff“ und „Definition“ **Durchsuchbar** sind.

## Begriffe aus Inhalten verlinken

Verlinken Sie die erste Verwendung eines Begriffs auf einer Seite mit dem Link des Editors auf ein Objekt mit seinem
Glossareintrag; der Link folgt dem Begriff, wenn er verschoben wird, und zeigt auf jeder Sprachwebsite auf die richtige
Sprachfassung.

## Synonyme und Suche

Mit `eztags` kann ein Schlagwortbaum von Begriffen Synonyme tragen, sodass ein unter einem anderen Wort gefundenes
Schlagwort zur selben Schlagwortseite führt ([Verschlagworten mit eztags](../../../../eng-us/help/creators/website-creators/tagging-with-eztags.md)).
Mit Solr sorgt eine Synonymdatei in der Solr-Konfiguration dafür, dass die Suche Wörter als gleichwertig behandelt;
die mitgelieferte Datei enthält nur Testzuordnungen, Sie schreiben also Ihre eigene
([Suche mit ezfind und Solr](../../../../eng-us/help/creators/website-creators/search-with-ezfind-and-solr.md)).

## Eine interne Terminologieliste für Übersetzer

Führen Sie ein zweites Glossar in einer Sektion, die nur Mitarbeitende lesen können, mit: dem Begriff in jeder
Sprache, den unerwünschten Varianten, Hinweisen für Übersetzer und dem Datum der Vereinbarung. Exportieren Sie es für
Übersetzungsagenturen mit `xrowextract` als CSV, eine Zeile pro Übersetzung
([xrowextract](../../../../eng-us/features/6.0/extensions/xrowextract.md)).

## Verwandte Seiten

- [Einfache Sprache](einfache-sprache.md)
- [Übersetzungsablauf und Freigabe](uebersetzungsablauf-und-freigabe.md)
- [Beziehungen und Wiederverwendung](../../../../eng-us/help/creators/website-creators/relations-and-reuse.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
