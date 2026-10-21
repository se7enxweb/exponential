---
title: Override nach Sektion
audience: designer, developer
category: templates
order: 610
summary: Einem Bereich der Website mit Sektionen eigene Templates geben - section_identifier (übertragbar) gegenüber der Sektions-ID, Kombination mit Klassenregeln, ein sektionsspezifisches Stylesheet und Sektionsklassen am body aus dem Pagelayout.
keywords: Override nach Sektion, section_identifier, Match section, Design eines Website-Bereichs, Sektions-Templates, Mitgliederbereich, Intranet-Sektion, Gestaltung nach Sektion, override section, site area design, section templates, members area, intranet section, section styling
type: help
---
# Override nach Sektion

Diese Seite ist für Designer, die einem Bereich der Website (einem Mitgliederbereich, einem Pressebereich, einer Kampagne) ein
eigenes Aussehen geben. Sektionen gruppieren Inhalte quer durch den Baum; Redakteure weisen sie zu.

## Inhalt

1. [Die Regel](#die-regel)
2. [Mit Klassen kombinieren](#mit-klassen-kombinieren)
3. [Nach Sektion gestalten ohne Overrides](#nach-sektion-gestalten-ohne-overrides)

## Die Regel

```ini
[mytheme_full_press_article]
Source=node/view/full.tpl
MatchFile=full/press_article.tpl
Subdir=templates
Match[section_identifier]=press
Match[class_identifier]=article
Priority=10
```

Verwenden Sie `section_identifier` (beim Anlegen der Sektion gesetzt) statt `section` (ihrer ID), die sich zwischen
Installationen unterscheidet.

## Mit Klassen kombinieren

Eine Sektionsregel allein ersetzt das Template für jede Klasse in der Sektion, Ordner und Bilder eingeschlossen. Kombinieren Sie
sie meist mit `class_identifier` und geben Sie ihr eine niedrigere `Priority` als der einfachen Klassenregel, damit sie zuerst
geprüft wird.

## Nach Sektion gestalten ohne Overrides

Oft unterscheiden sich nur Farben oder ein Kopfbereich. Dann ist kein Override nötig: Das Pagelayout liest die Sektion aus
`$module_result.content_info` und setzt eine Klasse:

```tpl
{def $section_class = ''}
{if and( is_array( $module_result.content_info ), is_set( $module_result.content_info.section_identifier ) )}
    {set $section_class = concat( 'section-', $module_result.content_info.section_identifier )}
{/if}
<body class="{$section_class|wash}">
```

und das Stylesheet erledigt den Rest (`.section-press { --site-accent: ... }`). Die Seitendaten des Designs ezwebin fügen
seinem Seiten-Wrapper auf dieselbe Weise eine Klasse `section_id_<n>` hinzu.

## Verwandte Seiten

- [Override-Match-Schlüssel](override-match-schluessel.md)
- [Pagelayout-Variablen](../theme-design/pagelayout-variablen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
