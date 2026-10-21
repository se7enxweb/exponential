---
title: "Ausschreibungsvorlage: Klausel zur Mehrsprachigkeit"
audience: government, project-manager, partner, editor
category: public-sector
order: 140
summary: Klauseltext für die Lieferung einer Exponential-Website in mehreren Amts- oder Gemeinschaftssprachen - Sprachen und Schriften, Unterstützung von rechts nach links, Sprachadressen, Sprachen der Administration, Übersetzungsablauf, Kodierung und Tests je Sprache - mit Lücken zum Ausfüllen.
keywords: Klausel zur Mehrsprachigkeit, Sprachen, Amtssprachen, Übersetzung, rechts nach links, RTL, Sprachumschalter, Sprache der Administration, UTF-8, Ausschreibungsvorlage, multilingual clause, languages, official languages, translation, right to left, language switcher, administration language, tender template
type: help
---
# Ausschreibungsvorlage: Klausel zur Mehrsprachigkeit

Diese Seite richtet sich an Beschaffungsstellen in öffentlichen Stellen, die in mehr als einer Sprache veröffentlichen.
Die Klausel sorgt dafür, dass der Lieferant jede Sprache liefert und testet, nicht nur einschaltet. Hintergrund und die
dokumentierten Fähigkeiten: [Anforderungen an Sprache und Mehrsprachigkeit](../anforderungen/anforderungen-an-sprache-und-mehrsprachigkeit.md).

**Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung

## Inhalt
1. [Die Klausel](#die-klausel)
2. [Anlage zu den Sprachen](#anlage-zu-den-sprachen)
3. [Was ein auf Exponential beruhendes Angebot zeigen kann](#was-ein-auf-exponential-beruhendes-angebot-zeigen-kann)
4. [Hinweise für die bewertende Person](#hinweise-für-die-bewertende-person)

## Die Klausel

```text
G. LANGUAGES

G.1 Languages. The public website is delivered in the languages listed in the
    language annex, with the scripts and writing directions listed there.

G.2 Structure. Each language is reached by <path prefix / host name>; every page
    links to its available translations; pages declare their language to
    browsers and assistive technology.

G.3 Missing translations. Where a page has no translation in a language, the site
    <shows the page in <fallback language> with a notice / hides it from that
    language>, as set out in <annex>.

G.4 Templates and fixed text. All fixed text in templates, forms, e-mails,
    error pages and the cookie or consent notices is translated into each
    language.

G.5 Right-to-left. For right-to-left languages, layout, navigation, forms and
    the editor display in the correct direction; mixed-direction text (names,
    numbers, addresses) remains readable.

G.6 Administration. The administration is available to staff in <languages>, as
    far as translations of the software exist; the Supplier lists the coverage.

G.7 Workflow. The Supplier configures a translation workflow: <who translates,
    who approves, how missing translations are reported>.

G.8 Encoding. All text is stored and delivered as UTF-8; characters required by
    the languages in the annex are stored without loss. The Supplier names the
    database and its character set.

G.9 Testing. Acceptance tests are run in every language, including search,
    forms and e-mails.
```

## Anlage zu den Sprachen

```text
| Language | Locale code | Script | Direction | Pages | Address | Admin UI needed |
|----------|-------------|--------|-----------|-------|---------|-----------------|
| <name>   | <xxx-YY>    | <...>  | LTR / RTL | all / <subset> | <prefix or host> | yes / no |
```

Die internen Locale-Codes von Exponential haben die Form `ger-DE`, `ara-SA` und ähnlich, und Browser erhalten
BCP-47-Tags ([Standards zur Internationalisierung](../../../../../eng-us/standards/internationalization.md)).

## Was ein auf Exponential beruhendes Angebot zeigen kann

| Klausel | Nachweis |
|---|---|
| G.1, G.7 | [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md), [Sprachen](../../../sprachen.md) |
| G.2 | [Zuordnung von Siteaccesses nach Host und Sprache](../../../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md), [Sprachumschalter und Sprach-URLs](../../../../../eng-us/help/creators/website-creators/language-switcher-and-language-urls.md) |
| G.5 | [Sprachen von rechts nach links](../../../../../eng-us/features/6.0/right-to-left.md), [Websites von rechts nach links](../../../../../eng-us/help/creators/website-creators/right-to-left-sites.md) |
| G.6 | [Sprachen in Exponential](../../../../../eng-us/languages.md) (Übersetzungen der Administration und ihre Vollständigkeit), [UI-Umschalter: Sprachen der Oberfläche](../../../../../eng-us/features/6.0/ui-switch-languages.md) |
| G.8 | Interne Kodierung UTF-8: Erfüllt; unter MySQL und MariaDB verwendet die Verbindung Drei-Byte-`utf8` (Teilweise erfüllt) |
| Lücke bei G.4 | Übersetzungen der Oberfläche haben keine Pluralformen (Nicht erfüllt) |

## Hinweise für die bewertende Person

- Lassen Sie sich während der Vorführung eine Seite von rechts nach links und den Editor in einer Sprache von rechts
  nach links zeigen.
- G.6 hängt von vorhandenen Übersetzungen ab; ein Bieter kann anbieten, eine zu vervollständigen und zurückzugeben
  ([Dokumentation und Übersetzungen beitragen](../../../partner/dokumentation-und-uebersetzungen-beitragen.md)).
- Mehr als 30 Inhaltssprachen brauchen 64-Bit-PHP ([Anforderungen](../../../../installation/02-voraussetzungen.md)).

## Verwandte Seiten
- [Ausschreibungsvorlagen](README.md)
- [Anforderungen an Sprache und Mehrsprachigkeit](../anforderungen/anforderungen-an-sprache-und-mehrsprachigkeit.md)
- [Klausel zum Abnahmetest](klausel-abnahmetest.md)
- [Mehrsprachige Websites](../../../../../eng-us/help/creators/website-creators/multilingual-sites.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
