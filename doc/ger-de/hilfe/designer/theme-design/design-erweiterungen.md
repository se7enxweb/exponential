---
title: Design-Erweiterungen
audience: designer, developer
category: theme-design
order: 40
summary: Wie eine Erweiterung ein Design trägt: die Einstellung DesignExtensions[], der Ordner, auf den sie zeigt, die Erweiterung für alle oder einige Siteaccesses aktivieren und was eine Theme-Erweiterung sonst noch mitliefert.
keywords: Design-Erweiterung, Theme-Erweiterung, DesignExtensions, design.ini, ActiveExtensions, ActiveAccessExtensions, Design-Ordner einer Erweiterung, Theme-Paket, sevenx_themes_media, cjw_themes_jumper, design extension, theme extension, extension design folder, theme package
type: help
---
# Design-Erweiterungen

Diese Seite ist für Designer, deren Design in einer Erweiterung leben soll (der übliche Weg) statt im Ordner `design/` des Kernels.
Sie beantwortet: was eine Erweiterung zu einer Design-Erweiterung macht, wie sie eingeschaltet wird und was die Theme-Erweiterungen
dieser Installation neben ihren Templates mitliefern.

## Inhalt

1. [Warum ein Design in eine Erweiterung gehört](#warum-ein-design-in-eine-erweiterung-gehört)
2. [Die eine Einstellung: DesignExtensions](#die-eine-einstellung-designextensions)
3. [Die Erweiterung aktivieren](#die-erweiterung-aktivieren)
4. [Was eine Theme-Erweiterung meist mitliefert](#was-eine-theme-erweiterung-meist-mitliefert)
5. [Die Theme-Erweiterungen dieser Installation](#die-theme-erweiterungen-dieser-installation)
6. [Eine Erweiterung, mehrere Designs](#eine-erweiterung-mehrere-designs)
7. [Prüfungen nach dem Aktivieren](#prüfungen-nach-dem-aktivieren)

## Warum ein Design in eine Erweiterung gehört

- **Upgrades berühren es nie.** Der Ordner `design/` des Kernels gehört zum Produkt; ein Upgrade ersetzt ihn.
- **Es reist als eine Einheit.** Templates, Stylesheets, Einstellungen und Übersetzungen gehören in einem Ordner zusammen, einem
  Git-Repository, einem Composer-Paket.
- **Es lässt sich ein- und ausschalten.** Die Erweiterung zu aktivieren oder zu deaktivieren, fügt das Design hinzu oder entfernt es,
  ohne Dateien zu verschieben.

## Die eine Einstellung: DesignExtensions

Die Designs einer Erweiterung werden nur gefunden, wenn die Erweiterung in `design.ini [ExtensionSettings] DesignExtensions[]`
aufgeführt ist. Die Erweiterung trägt sich selbst ein, in ihrer eigenen `settings/design.ini.append.php`. Die Datei des Themes
starter, `extension/cjw_themes_jumper/settings/design.ini.append.php`, ist das ganze Beispiel:

```ini
<?php /* #?ini charset="utf-8"?
[ExtensionSettings]
# The design "starter" (design/starter/) of this extension
DesignExtensions[]=cjw_themes_jumper
*/ ?>
```

Mit dieser Zeile wird jeder Ordner `extension/cjw_themes_jumper/design/<name>/` zu einer Design-Basis für das Design `<name>`. Der
Kernel baut die Liste in `eZTemplateDesignResource::designExtensions()` (`kernel/common/eztemplatedesignresource.php`).

> **Hinweis**
> Die Form `.append.php` mit der Hülle `<?php /* ... */ ?>` ist der übliche Weg, Einstellungen von Erweiterungen zu schreiben: Die
> Hülle verhindert, dass die Datei als Text angezeigt wird, falls ein Webserver sie je ausliefern sollte.

## Die Erweiterung aktivieren

Eine Erweiterung wird nur gelesen, wenn sie aktiv ist. Zwei Listen in `site.ini [ExtensionSettings]` entscheiden das:

| Einstellung | Wirkung |
|---|---|
| `ActiveExtensions[]` | aktiv für jeden Siteaccess, gelesen, bevor der Siteaccess gewählt wird |
| `ActiveAccessExtensions[]` | aktiv nur für die Siteaccesses, deren eigene `site.ini.append.php` sie aufführt, gelesen, nachdem der Siteaccess gewählt wurde |

Ein Theme, das nur eine Website verwendet, gehört in `ActiveAccessExtensions[]` dieses Siteaccess, damit die anderen Websites seine
Ordner nie durchsuchen. Aktivierungszeilen kommen in `settings/override/site.ini.append.php` der Installation oder in die
`site.ini.append.php` eines Siteaccess, die ein Administrator bearbeitet.

Erzeugen Sie nach dem Aktivieren einer neuen Erweiterung das Autoload-Array neu, wenn die Erweiterung PHP-Klassen hat, und leeren Sie
die Caches:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

Auf dieser Installation führt `./console exp:velocity deploy` diese Schritte aus und lädt PHP neu.

## Was eine Theme-Erweiterung meist mitliefert

| Pfad in der Erweiterung | Zweck |
|---|---|
| `design/<design>/templates/`, `override/templates/`, `stylesheets/`, `javascript/`, `images/`, `fonts/` | das Design |
| `settings/design.ini.append.php` | `DesignExtensions[]`, Listen der Stylesheets und Skripte |
| `settings/siteaccess/<siteaccess>/site.ini.append.php` | die Design-Zeilen für einen Siteaccess mit bekanntem Namen |
| `settings/siteaccess/<siteaccess>/override.ini.append.php` | die Override-Regeln des Designs |
| `settings/image.ini.append.php`, `content.ini.append.php` | Bildaliase und View-Einstellungen, auf die sich die Templates verlassen |
| `translations/<locale>/translation.ts` | die eigenen Texte des Designs in anderen Sprachen |
| `autoloads/eztemplateautoload.php`, `classes/` | eigene Template-Operatoren, falls vorhanden |
| `ezinfo.php`, `extension.xml`, `composer.json`, `LICENSE` | Name, Version, Lizenz und Paketierung |

Die Metadatendateien, die Paketierung und die Schritte zur Veröffentlichung behandeln die Seiten zu Theme-Erweiterungen in dieser
Hilfe ([Theme-Erweiterungen: hier beginnen](../erweiterungen/theme-erweiterungen-hier-beginnen.md)).

## Die Theme-Erweiterungen dieser Installation

| Erweiterung | Designs, die sie trägt | Hinweise |
|---|---|---|
| `sevenx_themes_media` | `media` | die öffentlichen Siteaccesses `site` und `bold`; Overrides pro Siteaccess in `settings/siteaccess/site/` und `bold/` |
| `sevenx_themes_simple` | `simple` | führt ihr CSS und ihre Skripte in `CSSFileList[]` / `JavaScriptList[]` auf |
| `cjw_themes_jumper` | `starter`, `standard` | die Siteaccesses `starter`, Einstellungen unter `settings/siteaccess/cjw_starter/` |
| `sevenx_themes_super` | `sevenx_themes_super` | ein kleines Design mit `site.css` und `site.js` |
| `ezwebin` | `ezwebin` | das klassische Design website interface, von `site` und `bold` als Rückfall verwendet |

Jede ist auf einer eigenen Seite beschrieben: [Das Theme media](../../../../eng-us/help/designers/theme-design/media-theme-tour.md), [Das Theme simple](../../../../eng-us/help/designers/theme-design/simple-theme-tour.md),
[Das Theme starter](../../../../eng-us/help/designers/theme-design/starter-theme-tour.md), [Das Theme super](../../../../eng-us/help/designers/theme-design/super-theme-tour.md),
[Das Design ezwebin als Basis](ezwebin-als-grundlage.md).

## Eine Erweiterung, mehrere Designs

Eine Design-Erweiterung kann beliebig viele Designs tragen: `cjw_themes_jumper` hat `design/starter` für ihr eigenes Aussehen und
`design/standard` für Templates, die jeder Siteaccess finden soll, dessen Kaskade `standard` erreicht. Gehen Sie mit einem Ordner
`standard` in einer Erweiterung vorsichtig um: Ihn durchsucht **jeder** Siteaccess, der die Erweiterung aktiv hat und auf `standard`
zurückfällt, die Administration eingeschlossen.

## Prüfungen nach dem Aktivieren

- [ ] Die Erweiterung ist aktiv: Die Administration führt sie unter Setup > Extensions auf (oder fragen Sie Ihren Administrator).
- [ ] Ein Template des Designs erscheint: Schalten Sie `ShowTemplatePathComments` auf einem Test-Siteaccess ein und sehen Sie sich den
      Seitenquelltext an ([Ein Design debuggen](ein-design-debuggen.md)).
- [ ] Ein Stylesheet des Designs ist im gepackten CSS: Der Netzwerkbereich des Browsers zeigt eine gepackte Datei aus
      `var/<site>/cache/public/stylesheets/`, die Ihre Regeln enthält.
- [ ] Kein anderer Siteaccess hat sein Aussehen geändert: Öffnen Sie die Administration und die anderen öffentlichen Websites einmal.

## Verwandte Seiten

- [Die Design-Kaskade](../../../../eng-us/help/designers/theme-design/the-design-cascade.md)
- [Der Aufbau des Design-Verzeichnisses](aufbau-des-design-verzeichnisses.md)
- [Tutorial: eine Theme-Erweiterung anlegen](../../../../eng-us/help/designers/theme-design/tutorial-create-a-theme-extension.md)
- [Ladereihenfolge der Erweiterungen](../../../../eng-us/features/6.0/extension-loading-order.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
