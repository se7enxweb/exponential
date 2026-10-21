---
title: "Die Teile des Design-Assistenten, einer nach dem anderen"
audience: designer, developer
category: theme-extensions
order: 250
summary: Referenz aller Teile, die der Design-Erweiterungs-Assistent schreiben kann - die Dateien, die jeder erzeugt, ob er standardmäßig an ist und was Sie am Ergebnis ändern.
keywords: Teile des Design-Assistenten, Pagelayout, Seitenteile, Stylesheets, Druck-Stylesheet, JavaScript, Bilder, Template-Overrides, Siteaccess-Einstellungen, Template-Operatoren, ezinfo, extension.xml, composer.json, README, gitignore, LICENSE, design wizard parts, page parts, print stylesheet, template overrides, siteaccess settings
type: help
---
# Die Teile des Design-Assistenten, einer nach dem anderen

Diese Seite ist eine Referenz für die Liste **Was hineinkommt** des [Design-Erweiterungs-Assistenten](der-design-erweiterungs-assistent.md).
`<name>` ist der eingegebene Name der Erweiterung; der Design-Ordner hat denselben Namen. Die Angaben unten stammen aus dem
Code des Assistenten (`kernel/setup/expdesignextensionwizard.php`).

## Design-Teile

| Teil | Standard | Schreibt | Hinweise |
|---|---|---|---|
| **Seitenlayout** | an | `design/<name>/templates/pagelayout.tpl` | Der Rahmen, in dem jede Seite gezeichnet wird; setzt `<html lang>` aus der Locale des Siteaccess |
| **Seitenteile** | an | `page_head.tpl`, `page_header.tpl`, `page_footer.tpl` in `design/<name>/templates/` | Damit jeder einzeln überschrieben werden kann |
| **Stylesheets** | an | `design/<name>/stylesheets/site.css` und `FrontendCSSFileList[]=site.css` in `settings/design.ini.append.php` | Die erzeugte `page_head.tpl` gibt die Liste mit `ezcss_load` aus |
| **Druck-Stylesheet** | aus | `design/<name>/stylesheets/print.css` | Von `page_head.tpl` mit `media="print"` geladen |
| **JavaScript** | an | `design/<name>/javascript/site.js` und `FrontendJavaScriptList[]=site.js` | Die erzeugte `pagelayout.tpl` gibt die Liste vor `</body>` mit `ezscript_load` aus |
| **Bildverzeichnis** | an | `design/<name>/images/.gitkeep` | Wo `ezimage` sucht |
| **Template-Overrides** | an | `settings/override.ini.append.php` mit einer Regel und `design/<name>/override/templates/full/article.tpl` | Die Beispielregel ersetzt `node/view/full.tpl` für die Klasse `article` |

### Drei Änderungen, die der erzeugte Kopf und das Layout brauchen

Ab Exponential 6.0.15 schreibt der Assistent einen funktionierenden Titel, gibt die Stylesheets und Skripte aus und
schreibt einen einfachen Zeichensatz, sodass ein von ihm erzeugtes Theme keine dieser Änderungen braucht. Ein Theme, das
ein älterer Assistent erzeugt hat, braucht drei Änderungen von Hand, sonst haben seine Seiten einen fehlerhaften Titel
und laden keine Stylesheets oder Skripte:

1. **Der Titel.** `page_head.tpl` baut `<title>` mit `implode_key`, das kein registrierter Template-Operator ist. Bauen
   Sie ihn stattdessen mit einer Schleife über den Pfad:

   ```
   <title>{foreach $module_result.path|reverse as $item}{$item.text|wash}{delimiter} / {/delimiter}{/foreach} - {ezini( 'SiteSettings', 'SiteName' )|wash}</title>
   ```

2. **Die Dateien ausgeben.** Die erzeugten Templates rufen `ezcss_require` und `ezscript_require` auf, die Dateien nur
   sammeln; nichts gibt sie aus. Fügen Sie `{ezcss_load()}` am Ende von `page_head.tpl` und `{ezscript_load()}` direkt
   vor `</body>` in `pagelayout.tpl` hinzu ([Die Operatoren ezcss und ezscript](../theme-design/die-operatoren-ezcss-und-ezscript.md)).
3. **Der Zeichensatz.** Ersetzen Sie die erzeugte Zeile `<meta charset=...>`, die `utf-8` mit `extract_left( 0 )` aus
   der Locale baut und nur zufällig funktioniert, durch ein einfaches `<meta charset="utf-8" />`.

Prüfen Sie das Ergebnis: Öffnen Sie eine Seite des Siteaccess, sehen Sie sich ihren Quelltext an und suchen Sie nach dem
Seitentitel, einem `<link rel="stylesheet">` für Ihr Stylesheet und einem `<script>`-Element für jQuery.

`settings/design.ini` kennzeichnet `CSSFileList[]` und `JavaScriptList[]` als veraltet zugunsten von
`FrontendCSSFileList[]` und `FrontendJavaScriptList[]`. Ab 6.0.15 schreibt der Assistent die neueren Listen, und die
erzeugten `page_head.tpl` und `pagelayout.tpl` laden beide, wie die mitgelieferten Designs. Ein Theme, das ein älterer
Assistent erzeugt hat, hat `CSSFileList[]=site.css` und `JavaScriptList[]=site.js`: Benennen Sie die beiden Zeilen in
`FrontendCSSFileList[]` und `FrontendJavaScriptList[]` um und stellen Sie sicher, dass Kopf und Layout die
Frontend-Listen laden ([Stylesheets laden](../../../../eng-us/help/designers/theme-design/loading-stylesheets.md),
[JavaScript laden](../../../../eng-us/help/designers/theme-design/loading-javascript.md)).

## Einstellungsteile

| Teil | Standard | Schreibt |
|---|---|---|
| **Siteaccess-Einstellungen** | aus | `settings/siteaccess/<siteaccess>/site.ini.append.php` mit `SiteDesign=<name>` und `AdditionalSiteDesignList[]=<base design>` |
| **Template-Operatoren** | aus | `autoloads/<name>operators.php` (Klasse `<name>Operators`, Beispieloperator `<name>_excerpt`), `autoloads/eztemplateautoload.php`, das ihn registriert, und `settings/site.ini.append.php` mit `ExtensionAutoloadPath[]=<name>` |

Ab 6.0.15 schreibt der Teil für Template-Operatoren auch `autoloads/eztemplateautoload.php`, die Datei, die die
Template-Engine liest, um Operatoren zu registrieren. Einem Theme, das ein älterer Assistent erzeugt hat, fehlt sie, und
sein Operator wird nie registriert: Fügen Sie die Datei selbst hinzu ([Template-Operatoren in einem Theme](template-operatoren-in-einem-theme.md)).

## Metadaten- und Repository-Teile

| Teil | Standard | Schreibt |
|---|---|---|
| **ezinfo.php** | an | `ezinfo.php` ([ezinfo.php](ezinfo-php.md)) |
| **extension.xml** | an | `extension.xml` ([extension.xml](extension-xml.md)) |
| **composer.json** | an | `composer.json` mit `name` = `<vendor>/<name with dashes>`, `type` = `ezpublish-legacy-extension`, `license`, `require.php` = `>=7.4`, `extra.ezpublish-legacy-extension-name` und `extra.installer-name` = `<name>` |
| **README.md** | an | Was es ist, wie man es einschaltet, was enthalten ist |
| **.gitignore** | an | Hält Editor-Reste und Build-Ausgaben aus dem Repository heraus |
| **LICENSE** | an | Der Text der gewählten Lizenz |

Erhöhen Sie `require.php` in `composer.json` auf die PHP-Versionen, die Sie wirklich testen. Ab 6.0.15 schreibt der
Assistent `extra.ezpublish-legacy-extension-name`, den Schlüssel, den der Installer liest, sodass sich das Paket nach
`extension/<name>` installiert. Eine `composer.json` von einem älteren Assistenten hat nur `installer-name`, das der
Installer nicht liest: Fügen Sie `"ezpublish-legacy-extension-name": "<name>"` unter `extra` hinzu
([Der Composer-Installer und der Ordnername](composer-installer-und-erweiterungsname.md)).

## Verwandte Seiten

- [Der Design-Erweiterungs-Assistent](der-design-erweiterungs-assistent.md)
- [Die Ordnerstruktur einer Theme-Erweiterung](ordnerstruktur-einer-theme-erweiterung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
