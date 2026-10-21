---
title: Knoten-Templates nach Klasse
audience: designer, developer
category: templates
order: 560
summary: Die Templates eines Designs pro Inhaltsklasse und Ansichtsmodus ordnen - das Muster der Override-Regeln, eine Ordnerstruktur nach Ansichtsmodus, ein Benennungsschema, eine Checkliste der Paare aus Klasse und Modus, die eine Website braucht, und wie die Designs media und starter es tun.
keywords: Klassen-Templates, Template pro Klasse, Override pro Klasse, class_identifier, full/article.tpl, line/article.tpl, Ordner nach Ansichtsmodus, Template-Organisation, Benennung von Overrides, class templates, template per class, override per class, view mode folders, template organization, override naming
type: help
---
# Knoten-Templates nach Klasse

Diese Seite ist für Designer, die die View-Templates einer ganzen Website ordnen. Das übliche Muster: eine Override-Regel
und ein Template für jedes Paar aus **Inhaltsklasse** und **Ansichtsmodus**, das ein eigenes Aussehen braucht.

## Inhalt

1. [Das Muster](#das-muster)
2. [Ordnerstruktur und Namen](#ordnerstruktur-und-namen)
3. [Welche Paare eine Website braucht](#welche-paare-eine-website-braucht)
4. [Wie die mitgelieferten Designs es tun](#wie-die-mitgelieferten-designs-es-tun)

## Das Muster

```ini
[mytheme_full_article]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article

[mytheme_line_article]
Source=node/view/line.tpl
MatchFile=line/article.tpl
Subdir=templates
Match[class_identifier]=article
```

## Ordnerstruktur und Namen

```
design/mytheme/override/templates/
├── full/article.tpl      full/folder.tpl      full/event.tpl
├── line/article.tpl      line/event.tpl
├── card/article.tpl
└── embed/image.tpl       embed/video.tpl
```

- Ordner = Ansichtsmodus; Datei = Klassenbezeichner.
- Namen der Abschnitte: `<theme>_<mode>_<class>`; eindeutig über alle Einstellungsdateien.
- Eine Regel pro Paar; kein riesiges switch in einem allgemeinen Template ([Switch und case](switch-und-case.md)).

## Welche Paare eine Website braucht

| Klasse | full | line | card | embed |
|---|---|---|---|---|
| article | ja | ja | ja | |
| folder | ja | ja | | |
| image | | | | ja |
| ... | | | | |

Füllen Sie eine solche Tabelle aus dem Inhaltsmodell der Website; jedes „ja“ ist eine Regel und ein Template. Klassen ohne
Regel verwenden das Standard-`node/view/<mode>.tpl`, also machen Sie die Standards ordentlich.

## Wie die mitgelieferten Designs es tun

- Das Design media hält seine Regeln in `settings/siteaccess/site/override.ini.append.php` (und `bold/`) seiner Erweiterung,
  mit Templates in `override/templates/full/`, `line/`, `embed/`, `content/` und mehr.
- Das Design starter hält sie in `settings/siteaccess/cjw_starter/override.ini.append.php`; jedes `full/<class>.tpl` bindet
  `content/full/<class>.tpl` ein, sodass Layout-Blöcke Teile desselben Inhalts zeichnen können.
- Das Design super zeigt den minimalen Fall: eine Regel, `full/article.tpl`.

## Verwandte Seiten

- [Override-Grundlagen](override-grundlagen.md)
- [Ansichtsmodi](ansichtsmodi.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
