---
title: Der Operator ezini
audience: designer, developer
category: templates
order: 270
summary: Einstellungen in Templates mit ezini und ezini_hasvariable lesen - die Parameter Abschnitt, Einstellung, Datei und Verzeichnis, Arrays aus INI-Listen, die eigene Einstellungsdatei eines Themes und wann Einstellungen in Templates das richtige Werkzeug sind.
keywords: ezini, ezini_hasvariable, Einstellungen in Templates, INI-Datei, Einstellung lesen, Theme-Einstellungen, design.ini, site.ini, eigene INI-Datei, Konfiguration, settings in templates, ini file, read setting, theme settings, own ini file, configuration
type: help
---
# Der Operator ezini

Diese Seite ist für Designer, die in Templates Einstellungen lesen: den Website-Namen, eine Liste von Stylesheets, eine
Option des Themes.

## Inhalt

1. [Der Aufruf](#der-aufruf)
2. [Zuerst prüfen](#zuerst-prüfen)
3. [Listen](#listen)
4. [Eine Einstellungsdatei für Ihr Theme](#eine-einstellungsdatei-für-ihr-theme)
5. [Wann Einstellungen verwenden](#wann-einstellungen-verwenden)

## Der Aufruf

```tpl
{ezini( 'SiteSettings', 'SiteName' )}                         {* site.ini when no file is given *}
{ezini( 'StylesheetSettings', 'CSSFileList', 'design.ini' )}
{ezini( 'NodeSettings', 'RootNode', 'content.ini' )}
```

Parameter (`kernel/common/ezurloperator.php`): Abschnitt, Einstellung, Datei (Standard `site.ini`) und ein optionales
Verzeichnis. Der Wert wird für den aktuellen Siteaccess gelesen, mit allen angewendeten Overrides.

## Zuerst prüfen

Eine nicht vorhandene Einstellung zu lesen, ist eine Template-Warnung. Wenn eine Einstellung optional ist (zum Beispiel eine,
die Ihr Theme hinzufügt und die eine Installation noch nicht hat):

```tpl
{if ezini_hasvariable( 'SiteInfo', 'PrivacyPolicyID', 'menu.ini' )}
    ...
{/if}
```

Die `pagelayout/head/script.tpl` des Designs media tut das, bevor sie ihre Einstellung für die Datenschutzseite liest.

## Listen

Eine INI-Liste (`Setting[]=a`, `Setting[]=b`) kommt als Array zurück und kann also direkt in einen Operator gehen, der
Arrays annimmt:

```tpl
{ezcss_load( array( 'mytheme.css', ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' ) ) )}
{if ezini( 'Header', 'LandingClasses', 'mytheme.ini' )|contains( $node.class_identifier )} ... {/if}
```

## Eine Einstellungsdatei für Ihr Theme

Gestaltungsentscheidungen, die sich je Website unterscheiden können, gehören in eine Einstellungsdatei des Themes, nicht in
Templates. Das Theme starter hat `cjw_jumper.ini` mit `[Header]`, `[Menu]` und `[LayoutSettings]`, das seine Templates mit
`ezini( ..., 'cjw_jumper.ini' )` lesen. Die `settings/mytheme.ini.append.php` Ihres Themes mit Standardwerten, pro
Siteaccess überschrieben, funktioniert genauso ([Einem Theme eine eigene Einstellungsdatei geben](../erweiterungen/eine-eigene-ini-datei.md)).

## Wann Einstellungen verwenden

- Ja: IDs besonderer Inhalte (über die Remote-ID), Listen von Klassen, Schalter an/aus, Dateilisten.
- Nein: Texte (verwenden Sie `i18n`), alles Geheime (Templates dürfen nie Geheimnisse ausgeben), Werte, die sich pro Anfrage
  ändern.

## Verwandte Seiten

- [Operatoren im Überblick](operatoren-ueberblick.md)
- [Der Operator ezhttp](der-ezhttp-operator.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
