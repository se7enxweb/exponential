---
title: Twig-Funktionen für den Alltag
audience: dxp-developer
category: dxp-development
order: 21
summary: Die Twig-Funktionen und -Filter, die der Core der DXP für Inhalts-Templates registriert - Felder und Inhalte ausgeben, Feldwerte lesen, Bildvariationen, Routen und Pfade, der aktuelle Benutzer - gelesen aus den Twig-Erweiterungen des installierten Core, mit kurzen Beispielen.
keywords: Twig-Funktionen, ibexa_render_field, ibexa_field_value, ibexa_content_name, ibexa_image_alias, ibexa_path, ibexa_url, ibexa_render, ibexa_render_content, ibexa_render_location, ibexa_current_user, ibexa_field_is_empty, Templates, Twig functions, templates
type: help
---
# Twig-Funktionen für den Alltag

Diese Seite richtet sich an Entwickler, die auf der DXP Inhalts-Templates schreiben. Die Namen wurden aus den
Twig-Erweiterungen des installierten Core (`src/lib/MVC/Symfony/Templating/Twig/Extension/` in
`se7enxweb/exponential-platform-dxp-core`) und aus `ibexa/twig-components` gelesen. Für die Argumente jeder Funktion
führen Sie in Ihrem Projekt `php bin/console debug:twig` aus.

## Inhalt

1. [Felder](#felder)
2. [Inhalte und Platzierungen](#inhalte-und-platzierungen)
3. [Bilder](#bilder)
4. [Links und Routen](#links-und-routen)
5. [Benutzer und Voreinstellungen](#benutzer-und-voreinstellungen)
6. [Komponenten](#komponenten)
7. [Ein Full-View-Template](#ein-full-view-template)

## Felder

| Funktion | Gibt zurück |
|---|---|
| `ibexa_render_field(content, 'identifier')` | das Feld, mit seinem Feld-Template ausgegeben |
| `ibexa_field_value(content, 'identifier')` | das Wertobjekt des Feldes |
| `ibexa_field(content, 'identifier')` | das Feld |
| `ibexa_has_field(content, 'identifier')` | ob der Inhalt dieses Feld hat |
| `ibexa_field_is_empty(content, 'identifier')` | ob das Feld leer ist |
| `ibexa_field_name(content, 'identifier')`, `ibexa_field_description(...)` | den übersetzten Namen oder die Beschreibung der Felddefinition |
| `ibexa_field_group_name(...)` | den übersetzten Namen einer Feldgruppe |
| `ibexa_render_field_definition_settings(...)` | die Einstellungen einer Felddefinition, ausgegeben |

## Inhalte und Platzierungen

| Funktion | Gibt zurück |
|---|---|
| `ibexa_content_name(content)` | den übersetzten Namen eines Inhaltselements |
| `ibexa_render(value)` | gibt ein Inhaltselement oder eine Platzierung in einer Ansicht aus (mit `viewType`) |
| `ibexa_render_content(content)`, `ibexa_render_location(location)` | dasselbe für jeweils eine Art |

## Bilder

| Funktion | Gibt zurück |
|---|---|
| `ibexa_image_alias(field, versionInfo, 'variation')` | die Variation eines Bildfeldes |
| `ibexa_content_field_identifier_first_filled_image(content)` | den Bezeichner des ersten Bildfeldes mit Wert |
| `ibexa_content_field_identifier_image_asset(...)` | den Bezeichner eines Bild-Asset-Feldes |
| `ibexa_file_size` (Filter) | eine lesbare Dateigröße |

## Links und Routen

| Funktion | Gibt zurück |
|---|---|
| `ibexa_path(location)` | die relative URL einer Platzierung (ihren URL-Alias) |
| `ibexa_url(location)` | die absolute URL |
| `ibexa_route(...)` | eine Routenreferenz, die Sie vor dem Erzeugen der URL ändern können |

## Benutzer und Voreinstellungen

| Funktion | Gibt zurück |
|---|---|
| `ibexa_current_user()` | den angemeldeten Benutzer |
| `ibexa_is_current_user(user)` | ob ein Benutzer der angemeldete ist |
| `ibexa_get_user_preference_value(...)`, `ibexa_has_user_preference(...)` | eine Benutzervoreinstellung |

## Komponenten

`ibexa_twig_component(...)` und `ibexa_twig_component_group(...)` geben Twig-Komponenten aus, die bei
`ibexa/twig-components` registriert sind ([Twig-Komponenten](twig-komponenten.md)).

## Ein Full-View-Template

```twig
{% extends '@ibexadesign/pagelayout.html.twig' %}

{% block content %}
    <article class="exp-article">
        <h1>{{ ibexa_content_name(content) }}</h1>

        {% if not ibexa_field_is_empty(content, 'image') %}
            {{ ibexa_render_field(content, 'image', { parameters: { alias: 'large' } }) }}
        {% endif %}

        {{ ibexa_render_field(content, 'body') }}

        <p><a href="{{ ibexa_path(location) }}">{{ 'Permalink'|trans }}</a></p>
    </article>
{% endblock %}
```

Die Variablen `content` und `location` übergibt der Core an Templates für Inhaltsansichten. Prüfen Sie die
Parameternamen von `ibexa_render_field` mit `debug:twig`, bevor Sie sich darauf verlassen.

## Verwandte Seiten

- [Twig-Templates und die Design-Engine](twig-templates-und-die-design-engine.md)
- [Bildvariationen](bildvariationen.md)
- [Die mitgelieferten Feldtypen](die-mitgelieferten-feldtypen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
