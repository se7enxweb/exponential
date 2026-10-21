---
title: Twig functions you use daily
audience: dxp-developer
category: dxp-development
order: 21
summary: The Twig functions and filters the DXP core registers for content templates - rendering fields and content, reading field values, image variations, routes and paths, the current user - read from the installed core's Twig extensions, with short examples.
keywords: Twig functions, ibexa_render_field, ibexa_field_value, ibexa_content_name, ibexa_image_alias, ibexa_path, ibexa_url, ibexa_render, ibexa_render_content, ibexa_render_location, ibexa_current_user, ibexa_field_is_empty, templates
type: help
---
# Twig functions you use daily

This page is for developers who write content templates on the DXP. The names were read from the Twig extensions
of the installed core (`src/lib/MVC/Symfony/Templating/Twig/Extension/` in `se7enxweb/exponential-platform-dxp-core`)
and from `ibexa/twig-components`. For the arguments of each, run `php bin/console debug:twig` in your project.

## Contents

1. [Fields](#fields)
2. [Content and locations](#content-and-locations)
3. [Images](#images)
4. [Links and routes](#links-and-routes)
5. [Users and preferences](#users-and-preferences)
6. [Components](#components)
7. [A full view template](#a-full-view-template)

## Fields

| Function | Returns |
|---|---|
| `ibexa_render_field(content, 'identifier')` | The field rendered with its field template |
| `ibexa_field_value(content, 'identifier')` | The field's value object |
| `ibexa_field(content, 'identifier')` | The field |
| `ibexa_has_field(content, 'identifier')` | Whether the content has that field |
| `ibexa_field_is_empty(content, 'identifier')` | Whether the field is empty |
| `ibexa_field_name(content, 'identifier')`, `ibexa_field_description(...)` | The translated name or description of the field definition |
| `ibexa_field_group_name(...)` | The translated name of a field group |
| `ibexa_render_field_definition_settings(...)` | The settings of a field definition, rendered |

## Content and locations

| Function | Returns |
|---|---|
| `ibexa_content_name(content)` | The translated name of a content item |
| `ibexa_render(value)` | Renders a content item or location in a view (with `viewType`) |
| `ibexa_render_content(content)`, `ibexa_render_location(location)` | The same for one kind |

## Images

| Function | Returns |
|---|---|
| `ibexa_image_alias(field, versionInfo, 'variation')` | The variation of an image field |
| `ibexa_content_field_identifier_first_filled_image(content)` | The identifier of the first image field with a value |
| `ibexa_content_field_identifier_image_asset(...)` | The identifier of an image asset field |
| `ibexa_file_size` (filter) | A readable file size |

## Links and routes

| Function | Returns |
|---|---|
| `ibexa_path(location)` | The relative URL of a location (its URL alias) |
| `ibexa_url(location)` | The absolute URL |
| `ibexa_route(...)` | A route reference you can change before generating the URL |

## Users and preferences

| Function | Returns |
|---|---|
| `ibexa_current_user()` | The signed-in user |
| `ibexa_is_current_user(user)` | Whether a user is the signed-in user |
| `ibexa_get_user_preference_value(...)`, `ibexa_has_user_preference(...)` | A user preference |

## Components

`ibexa_twig_component(...)` and `ibexa_twig_component_group(...)` render Twig components registered with
`ibexa/twig-components` ([Twig components](twig-components.md)).

## A full view template

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

The variables `content` and `location` are passed to content view templates by the core. Check the parameter names
of `ibexa_render_field` with `debug:twig` before you rely on them.

## Related pages

- [Twig templates and the design engine](twig-templates-and-the-design-engine.md)
- [Image variations](image-variations.md)
- [The field types it ships](the-field-types-it-ships.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
