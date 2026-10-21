---
title: Theme-Assets, der Packer und CSP
audience: designer, developer
category: extension-development
order: 60
summary: Die Stylesheets, Skripte, Bilder und Schriften eines Themes so laden, wie Exponential es erwartet - Frontend-Listen, ezcss_load und ezscript_load, der Packer von ezjscore, ezimage -, damit sie schnell sind und eine CSP erfüllen.
keywords: Assets, Stylesheets, Skripte, Bilder, Schriften, Packer, Content Security Policy, Entwicklungsmodus, assets, scripts, images, fonts, ezcss_load, ezscript_load, ezscript_require, ezimage, packer, ezjscore, FrontendCSSFileList, FrontendJavaScriptList, CSP, DevelopmentMode
type: help
---
# Theme-Assets, der Packer und CSP

Teil 6, Kapitel 6. Das Aussehen eines Themes steckt in seinen Assets: Stylesheets, Skripten, Bildern und Schriften.
Dieses Kapitel zeigt, wie Sie jede Art so laden, dass der Packer von ezjscore sie zu wenigen Anfragen bündelt, Browser
sie zwischenspeichern und eine Content Security Policy mit `'self'` sie durchlässt.

## Stylesheets und Skripte: Listen und Ladefunktionen

Registrieren Sie Dateien in der `design.ini.append.php` des Themes, mit ihrem Namen relativ zum Ordner `stylesheets/`
oder `javascript/` des Designs:

```ini
[StylesheetSettings]
FrontendCSSFileList[]=mytheme.css
FrontendCSSFileList[]=mytheme-print.css

[JavaScriptSettings]
FrontendJavaScriptList[]=mytheme.js
```

Das Pagelayout lädt die Listen mit den Template-Funktionen von ezjscore:

```text
{ezcss_load( ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' ) )}
...
{ezscript_load( array( 'ezjsc::jquery', ezini( 'JavaScriptSettings', 'FrontendJavaScriptList', 'design.ini' ) ) )}
```

| Funktion | Tut |
|---|---|
| `ezcss_load( files )` | gibt einen `<link>` für die angegebenen Dateien aus, plus alle mit `ezcss_require` gesammelten |
| `ezscript_load( files )` | gibt die `<script>`-Tags für die angegebenen Dateien aus, plus alle mit `ezscript_require` gesammelten |
| `ezcss_require( files )`, `ezscript_require( files )` | sammeln Dateien aus beliebigen Templates, die später der `_load`-Aufruf ausgibt |
| `ezscript( files )` | gibt die Script-Tags genau an dieser Stelle aus (im Seiteninhalt verwendet) |

`ezjsc::jquery` ist ein Packer-Schlüssel: ezjscore löst ihn zu seiner Datei für jQuery 4 auf.

Templates weiter unten in der Seite, die ein eigenes Skript brauchen, rufen `ezscript_require` auf, solange der
`_load`-Aufruf später in der Seite kommt (am Ende von `<body>`, wie oben). Im Seiteninhalt nach diesem Punkt verwenden
Sie `ezscript`.

## Der Packer

Der Packer von ezjscore fasst die aufgeführten Dateien zu einer Datei je Typ zusammen, minimiert sie optional und
schreibt das Ergebnis in den Cache-Ordner der Website (`var/<site>/cache/public/`). Der Name des Bündels enthält einen
Hash und die jüngste Änderungszeit seiner Dateien, sodass eine geänderte Datei einen neuen Namen ergibt und Browser sie
neu holen.

| Situation | Was geschieht |
|---|---|
| `site.ini [TemplateSettings] DevelopmentMode=enabled` | Dateien werden einzeln und ungepackt ausgeliefert: gut für die Fehlersuche |
| `DevelopmentMode=disabled` (der Standard) | Dateien werden gebündelt und gepackt |
| `ezjscore.ini [eZJSCore] Packer=` | überschreibt das Obige, wenn gesetzt (`disabled`, `enabled` oder eine Stufe von 0 bis 3) |

Nachdem Sie ein Asset auf einer Website mit eingeschaltetem Packen geändert haben, leeren Sie die Caches, die die
gepackten Links enthalten, und laden Sie den Browser hart neu:

```bash
php bin/php/ezcache.php --clear-id=template,template-block
```

Fügen Sie Asset-Links nie `?v=` oder andere Versionsparameter hinzu: Die Namen des Packers ändern sich bereits.

## Bilder

Verweisen Sie über die Design-Kette auf Bilder, damit ein Theme, das auf Ihrem aufbaut, sie ersetzen kann:

```text
<img src={'logo.svg'|ezimage} alt="{ezini( 'SiteSettings', 'SiteName' )|wash}" width="160" height="40">
```

`ezimage` findet `images/logo.svg` im ersten Design, das die Datei hat, und gibt die Adresse in Anführungszeichen aus.
In Stylesheets verweisen Sie relativ zum Stylesheet auf Bilder (`url( ../images/background.webp )`); der Packer schreibt
die Pfade gebündelter Stylesheets um.

Geben Sie jedem Inhaltsbild `width` und `height` (oder ein Seitenverhältnis in CSS), damit die Seite beim Laden nicht
springt.

## Schriften

Liefern Sie Schriftdateien im Theme aus (zum Beispiel in `design/mytheme/fonts/`) und laden Sie sie in Ihrem Stylesheet
mit `@font-face` und einer relativen URL. Schriften von einem Dienst Dritter zu laden schickt die Adresse jedes
Besuchers an diesen Dienst und verlangt dessen Host in der Richtlinie; lokale Dateien brauchen beides nicht. Prüfen Sie
zuerst die Lizenz der Schrift: Die meisten offenen Schriften verwenden die SIL Open Font License, die das Mitliefern
erlaubt ([Ein Theme verteilen](ein-theme-verteilen.md)).

## Die Content Security Policy

Eine Richtlinie mit `'self'` für Skripte und Styles erlaubt genau das, was das Obige erzeugt: Dateien Ihrer eigenen
Website. Sie verweigert:

| Verweigert | Stattdessen |
|---|---|
| `<script>`-Blöcke in Templates | eine Datei in `javascript/`, Werte in `data-*`-Attributen |
| `onclick=` und andere Ereignisattribute | Listener in Ihrer Datei |
| `style=""`-Attribute und `<style>`-Blöcke | Klassen in Ihrem Stylesheet |
| Skripte, Styles und Schriften von anderen Hosts | lokale Dateien oder der Host, in die Richtlinie für diesen Siteaccess aufgenommen |

Die öffentlichen Siteaccesses des Kernels haben die Richtlinie standardmäßig ausgeschaltet; Administratoren können sie
einschalten ([Content Security Policy](../../../funktionen/6.0/content-security-policy.md)). Ein Theme, das bereits darunter
funktioniert, ist ein Hindernis weniger. Die Umstellungen werden in Teil 4 erklärt:
[Inline-Skripte](../teil-4-aktualisieren/csp-inline-skripte-auslagern.md),
[Ereignisattribute](../teil-4-aktualisieren/csp-event-handler-zu-data-attributen.md),
[Inline-CSS](../teil-4-aktualisieren/inline-css-in-stylesheets.md).

Eine kleine Ausnahme, die Sie brauchen könnten: ein Skript, das laufen muss, bevor die Seite gezeichnet wird (um ein
gespeichertes Farbschema anzuwenden, siehe das nächste Kapitel). Machen Sie daraus eine winzige Datei, die als normales
Skript im `<head>` geladen wird, kein Inline-Skript.

## Verwandte Seiten

- Zurück: [Themes und Siteaccesses](themes-und-siteaccesses.md)
- Weiter: [Dunkler Modus und Design-Tokens in Themes](dunkler-modus-und-tokens-in-themes.md)
- [CSS und JavaScript in einer Erweiterung](../teil-3-erweiterungsentwickler/css-und-javascript-in-einer-erweiterung.md)
- [ezjscore](../../../funktionen/6.0/erweiterungen/ezjscore.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
