---
title: Die Leistungsseite und einfache Sprache
audience: government, website-creator, editor, project-manager
category: public-sector
order: 30
summary: Eine feste Reihenfolge für jede Leistungsseite, das Template, das die Klasse Service in dieser Reihenfolge zeigt und leere Teile versteckt, und Schreibregeln für einfache, barrierefreie Leistungstexte, denen Redakteure folgen können.
keywords: Leistungsseite, Seiten-Template, vollständige Ansicht, einfache Sprache, Schreibregeln, Überschriften, barrierefreie Inhalte, Template-Override, leere Felder, service page, page template, full view, plain language, writing rules, headings, accessible content, template override, attribute_view_gui, empty fields
type: help
---
# Die Leistungsseite und einfache Sprache

Diese Seite richtet sich an Site-Ersteller, die das Template der Leistungsseite bauen, und an Redakteure und
Projektleiter, die Leistungsinhalte schreiben. Sie legt eine Reihenfolge für jede Leistungsseite fest, zeigt, wie das
Template ihr folgt, und gibt die Schreibregeln. Die Klasse ist die aus [Leistungen modellieren](leistungen-modellieren.md).

## Inhalt

1. [Eine Reihenfolge für jede Seite](#eine-reihenfolge-für-jede-seite)
2. [Das Template](#das-template)
3. [Schreibregeln](#schreibregeln)
4. [Barrierefreiheit von Leistungsseiten](#barrierefreiheit-von-leistungsseiten)
5. [Eine Checkliste zur Durchsicht für Redakteure](#eine-checkliste-zur-durchsicht-für-redakteure)

## Eine Reihenfolge für jede Seite

| Reihenfolge | Teil (Attribut) | Angezeigte Überschrift |
|---|---|---|
| 1 | Name der Leistung | der Seitentitel (H1) |
| 2 | In Kürze | keine Überschrift; der erste Absatz |
| 3 | Online beantragen | ein Link im Stil einer Schaltfläche „Online beantragen“ weit oben, wenn gefüllt |
| 4 | Wer sie nutzen kann | „Wer diese Leistung nutzen kann“ |
| 5 | Was Sie brauchen | „Was Sie brauchen“ |
| 6 | Was sie kostet | „Kosten“ |
| 7 | Wie Sie beantragen | „So beantragen Sie“ |
| 8 | Wie lange es dauert | „Wie lange es dauert“ |
| 9 | Wie es weitergeht | „Wie es weitergeht“ |
| 10 | Kontakt | „Kontakt“, aus dem verknüpften Ansprechpartner |
| 11 | Zugehörige Dokumente | „Dokumente“, mit Dateityp und Größe |
| 12 | Nächste Überprüfung | Besuchern nicht gezeigt, oder als „Seite geprüft am“, wenn Ihre Richtlinie es zeigt |

## Das Template

Ein Override der vollständigen Ansicht für die Klasse, in Ihrer Design-Erweiterung, über den Bezeichner der Klasse
zugeordnet ([Template-Overrides](../../../../../eng-us/help/creators/website-creators/template-overrides.md)):

```ini
# extension/<your design>/settings/override.ini.append.php
[full_service]
Source=node/view/full.tpl
MatchFile=full/service.tpl
Subdir=templates
Match[class_identifier]=service
```

Geben Sie in `full/service.tpl` jeden Teil nur aus, wenn er Inhalt hat, unter seiner Überschrift:

```text
<h1>{$node.name|wash}</h1>
<p class="lead">{$node.data_map.summary.content|wash}</p>
{if $node.data_map.online_link.has_content}
  <p><a class="button" href={$node.data_map.online_link.content|ezurl}>{'Apply online'|i18n( 'design/<your design>' )}</a></p>
{/if}
{if $node.data_map.eligibility.has_content}
  <h2>{'Who can use this service'|i18n( 'design/<your design>' )}</h2>
  {attribute_view_gui attribute=$node.data_map.eligibility}
{/if}
```

und so weiter für jeden Teil. Überschriften im Template statt im Text der Redakteure halten die Struktur jeder Seite
gleich und die Überschriftenebenen korrekt.

## Schreibregeln

| Regel | Beispiel für die Änderung |
|---|---|
| Beginnen Sie mit dem, was die Person tun kann | „Sie können ... beantragen“ statt „Die Abteilung verwaltet ...“ |
| Ein Gedanke je Satz; kurze Sätze | lange Sätze bei „und“, „welche“ teilen |
| Verwenden Sie die Wörter, die Menschen verwenden | „Mülltonne“, wie Ihre Einwohner es sagen, der amtliche Begriff in Klammern |
| Sprechen Sie die Lesenden mit „Sie“ an | „Sie brauchen ...“ |
| Schritte als nummerierte Listen | in So beantragen Sie |
| Vermeiden Sie Abkürzungen; erklären Sie die nötigen einmal | |
| Daten und Beträge ausgeschrieben | „15. März“, „25 Euro im Jahr“ |

Hinweise einer Regierung zur einfachen Sprache: [plainlanguage.gov](https://www.plainlanguage.gov/).

## Barrierefreiheit von Leistungsseiten

| Punkt | Praxis |
|---|---|
| Überschriften | aus dem Template, in Reihenfolge (H1, dann H2) |
| Links | Text, der sagt, wohin er führt; „<Leistung> online beantragen“ statt „hier klicken“ |
| Dokumente | eine HTML-Erklärung auf der Seite; die Datei als Ergänzung |
| Sprache | `lang` vom Siteaccess gesetzt; fremdsprachige Wörter bei Bedarf im Editor ausgezeichnet |

Der Stand der Barrierefreiheit der Administration selbst und die Lücken stehen in
[Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md); das öffentliche Template müssen Sie selbst
testen ([Barrierefreiheit testen](../../../../../eng-us/help/creators/website-creators/testing-accessibility.md)).

## Eine Checkliste zur Durchsicht für Redakteure

- [ ] **In Kürze** beantwortet in zwei oder drei Sätzen „Was ist das, und ist es für mich?“.
- [ ] Jeder nötige Schritt steht nummeriert in **Wie Sie beantragen**.
- [ ] Kosten und Bearbeitungszeit sind aktuell.
- [ ] Der Online-Link öffnet das richtige Formular.
- [ ] Der Kontakt ist der richtige Schalter.
- [ ] **Nächste Überprüfung** ist auf ein Datum in der Zukunft gesetzt.

## Verwandte Seiten

- [Einen Leistungskatalog aufbauen](README.md)
- [Leistungen modellieren](leistungen-modellieren.md)
- [Barrierefreiheit für Site-Ersteller](../../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
