---
title: "Die Design-Kette: wo ein Template gefunden wird"
audience: developer, designer, app-creator
category: extension-development
order: 90
summary: Wie design:path/to/template.tpl aufgelöst wird - SiteDesign, AdditionalSiteDesignList, StandardDesign und DesignExtensions in jedem Design, mit dem Kernel-Code und einem Skript, das Ihre Kette ausgibt.
keywords: Design-Kette, Design-Basen, Rückfall, Template-Suche, design chain, design bases, SiteDesign, AdditionalSiteDesignList, StandardDesign, DesignExtensions, design resource, fallback, template lookup, ezdesign
type: help
---
# Die Design-Kette: wo ein Template gefunden wird

Teil 2, Kapitel 9. Fordert ein Template `design:node/view/full.tpl` an, durchsucht der Kernel eine Liste von Ordnern,
die **Design-Kette**, und verwendet die erste Datei, die er findet. Wer die Kette kennt, versteht, warum ein Template
verwendet wird oder nicht. Die Regeln unten sind aus `eZTemplateDesignResource::findDesignBase()` in
`kernel/common/eztemplatedesignresource.php` gelesen.

## Die Regel

1. Nehmen Sie die Liste der **Designs**: `SiteDesign`, dann jeden Eintrag von `AdditionalSiteDesignList[]` der Reihe
   nach, dann `StandardDesign` (alle in `site.ini [DesignSettings]`, meist je Siteaccess gesetzt). Doppelte Einträge
   entfallen.
2. Für **jedes Design** in dieser Reihenfolge:
   1. suchen Sie in `extension/<ext>/design/<design>/` für jede Erweiterung, die in
      `design.ini [ExtensionSettings] DesignExtensions[]` aufgeführt ist, in dieser Reihenfolge,
   2. dann im `design/<design>/` des Kernels.
3. Jeder vorhandene Ordner ist eine **Design-Basis**. Ein Template-Pfad wird in jeder Basis probiert, der erste Treffer
   gewinnt.

Die Reihenfolge der **Designs** zählt also mehr als die der Erweiterungen: Das `design/mysite/` irgendeiner Erweiterung
schlägt jeden Ordner des nächsten Designs, auch den des Kernels.

## Ihre Kette ansehen

Ein kleines Skript gibt sie aus (speichern Sie es in Ihrer Erweiterung, zum Beispiel unter
`extension/mysite_hello/scripts/designbases.php`):

```php
<?php
// Prints the design chain (the folders searched for a design: template), in search order.
// Run: php bin/php/ezexec.php extension/mysite_hello/scripts/designbases.php -s <siteaccess>
$cli = eZCLI::instance();
foreach ( eZTemplateDesignResource::allDesignBases() as $i => $base )
{
    $cli->output( ( $i + 1 ) . '. ' . $base );
}
```

Auf einer Installation, deren öffentlicher Siteaccess `SiteDesign=media` mit `ezwebin`, `standard` und `base` als
zusätzlichen Designs verwendet, sah der Anfang der Ausgabe so aus:

```text
1. extension/sevenx_alpha_settings/design/media
2. extension/sevenx_themes_media/design/media
3. extension/sevenx_authentication_2fa/design/media
4. design/media
5. extension/cjw_newsletter/design/ezwebin
6. extension/ezautosave/design/ezwebin
7. extension/ezwebin/design/ezwebin
8. design/ezwebin
9. extension/expui/design/standard
10. extension/ezjscore/design/standard
```

Jede Erweiterung erscheint unter jedem Design, für das sie einen Ordner hat. Die vollständige Liste ist lang; auf ihren
Anfang kommt es an.

## Das Design Ihrer Erweiterung registrieren

Der Ordner `design/` einer Erweiterung wird nur durchsucht, wenn die Erweiterung in `DesignExtensions[]` steht. Jede
Erweiterung mit Templates liefert dazu in `settings/design.ini.append.php` Folgendes aus:

```php
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mysite_hello

*/ ?>
```

Prüfen Sie:

```bash
./console exp:ini where design.ini/ExtensionSettings/DesignExtensions
```

## Welchen Design-Ordner soll ich verwenden?

| Ihr Template ist für | Legen Sie es ab in |
|---|---|
| Das Aussehen einer Website | `extension/<yours>/design/<that site's design>/templates/` |
| Jede Website, jedes Design (die eigene Seite eines Moduls, die Ansicht eines Datentyps) | `extension/<yours>/design/standard/templates/` (das `StandardDesign`, am Ende jeder Kette) |
| Die Administration | `extension/<yours>/design/admin/templates/` und, für die moderne Administration, `design/admin4/` (siehe [Admin-Seiten im admin4-Stil](../teil-3-erweiterungsentwickler/admin-seiten-im-admin4-stil.md)) |

Liegen die Templates eines Moduls in `standard`, funktionieren sie unter jedem Site-Design; ein Site-Design kann sie
trotzdem ersetzen, indem es denselben Pfad weiter vorn in der Kette hat.

## Zwei Wege, ein Template zu ersetzen

1. **Derselbe Pfad, weiter vorn in der Kette.** Legen Sie `node/view/full.tpl` in Ihr Design: Es ersetzt das
   Standard-Template für **alle** Inhalte. Einfach, aber grob.
2. **Ein Override.** Behalten Sie den Standardpfad und fügen Sie eine Regel hinzu: „für Artikel verwende
   `full/article.tpl`“. Präzise, und das Thema des nächsten Kapitels.

Bevorzugen Sie für Inhalts-Templates Overrides; verwenden Sie die Methode mit demselben Pfad für Templates, die keine
Override-Bedingungen haben (das Pagelayout, die Seite eines Moduls).

## Andere Dateien als Templates

Dieselbe Kette findet Stylesheets, Skripte und Bilder: `{'stylesheets/site.css'|ezdesign}` gibt die Adresse der ersten
`stylesheets/site.css` in der Kette aus, zum Beispiel `"/design/base/stylesheets/site.css"`. Auch der Packer von
ezjscore verwendet sie (siehe [Theme-Assets, der Packer und CSP](../teil-6-theme-entwickler/theme-assets-der-packer-und-csp.md)).

## Caching

`site.ini [DesignSettings] DesignLocationCache=enabled` speichert die Liste der Design-Basen zwischen. Nachdem Sie einen
Design-Ordner hinzugefügt oder `DesignExtensions[]` geändert haben, leeren Sie die Caches:

```bash
php bin/php/ezcache.php --clear-tag=ini
php bin/php/ezcache.php --clear-id=design_base,template-override
php bin/php/ezcache.php --clear-tag=template
```

Eine **neue** Template-Datei braucht einen geleerten Template-Override-Cache (`template-override`), sonst wird die Seite
womöglich mit leerem Inhaltsbereich dargestellt. `--clear-all` deckt alles ab; `php bin/php/ezcache.php --list-ids`
listet jede Cache-ID auf.

## Verwandte Seiten

- Zurück: [Templates für Builder](templates-fuer-builder.md)
- Weiter: [Template-Overrides: die Grundlagen](grundlagen-der-template-overrides.md)
- [Templates und Design](../../../anleitungen/templates-und-design.md)
- [Template-Pfadkommentare](../../../../eng-us/features/6.0/template-path-comments.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
