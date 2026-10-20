---
title: "Einwand: PHP und moderne Technik"
audience: advocate
category: advocacy
order: 32
summary: Wie Sie „PHP ist alt“, „es braucht ein altes PHP“, „die Oberfläche wirkt veraltet“ oder „wir brauchen Headless“ mit belegten Fakten beantworten - Unterstützung für PHP 8, der aktuelle Stack, admin4, Velocity, die REST-API und die Symfony-Distributionen.
keywords: Einwand, PHP, alte Technik, Legacy, modern, PHP 8, PHP 8.5, Oberfläche, veraltet, admin4, Dunkelmodus, Headless, REST, GraphQL, Symfony, jQuery 4, TinyMCE 8, objection, old technology, legacy, modern, interface, dated, dark mode, headless
type: help
---
# Einwand: PHP und moderne Technik

Diese Seite ist für Fürsprecher, die hören: „PHP ist alt“, „Das ist Legacy-Software“, „Die Administration sieht aus wie
2010“ oder „Wir brauchen Headless“. Streiten Sie nicht über Sprachen oder Geschmack. Beschreiben Sie mit Quellen,
worauf Exponential heute läuft und was es bietet, und lassen Sie die Menschen urteilen.

## Inhalt

1. [Die Varianten des Einwands](#die-varianten-des-einwands)
2. [Die Fakten](#die-fakten)
3. [Das Wort „Legacy“](#das-wort-legacy)
4. [Antwortvorlagen](#antwortvorlagen)
5. [Sagen Sie nicht](#sagen-sie-nicht)

## Die Varianten des Einwands

| Sie sagen | Die eigentliche Frage |
|---|---|
| „PHP ist alt.“ | „Ist die Sprache ein Risiko?“ |
| „Es braucht ein altes PHP.“ | „Läuft es auf unseren aktuellen Servern?“ |
| „Die Oberfläche wirkt veraltet.“ | „Werden unsere Redakteure sie mögen?“ |
| „Das ist Legacy-Software.“ | „Wird es gepflegt?“ (siehe [Lebt es noch?](einwand-lebt-es-noch.md)) |
| „Wir brauchen Headless / eine API.“ | „Können andere Systeme die Inhalte lesen?“ |

## Die Fakten

| Tatsache | Quelle |
|---|---|
| PHP 8.0 ist die älteste unterstützte Version; 8.1 bis 8.5 werden unterstützt; `composer.json` erlaubt bis 8.8 | [PHP-Versionen](../wer-ist-die-exponential-foundation.md#php-versionen) |
| Das Projekt zielt auf „PHP 8.2, 8.3, 8.4, 8.5, 8.6 und darüber hinaus PHP 9“ | ebenda |
| Die CI führt die Tests auf PHP 8.1 bis 8.5 aus und prüft, dass 8.0 noch startet | ebenda |
| Die Unterstützung für PHP 7.4 endete mit 6.0.7 | ebenda |
| Das Design admin4, mit hellem und dunklem Modus, Tastaturunterstützung und Tests der Barrierefreiheit | [Das Design admin4](../../../en/us/features/6.0/admin4-design.md) |
| jQuery 4 und TinyMCE 8 in der Linie 6.0.15 | [Mit Einwänden umgehen, PHP-Versionen und moderne Technik](../geschaeft/einwandbehandlung.md#php-versionen-und-moderne-technik) |
| AdminUI als alternative Administration | [AdminUI](../../../en/us/features/6.0/exp-adminui.md) |
| Eine REST-API, Versionen 1 und 2 | [REST-API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md) |
| Exponential Velocity, ein PHP-Webserver mit HTTP/2, HTTPS und dauerhaften Workern | [Velocity](../../../en/us/features/6.0/velocity-persistent-worker-server.md) |
| Für Symfony mit GraphQL: Exponential Platform DXP | [Welches Produkt passt zu Ihnen](../wer-ist-die-exponential-foundation.md#welches-produkt-zu-ihnen-passt) |
| Eine Content Security Policy und keine Inline-Skripte in neuen Templates der Administration | [Content Security Policy](../../../en/us/features/6.0/content-security-policy.md) |

## Das Wort „Legacy“

„Legacy“ hat in der Produktfamilie eine genaue Bedeutung: „der Kernel von Exponential 6, wenn er neben einer
Symfony-Plattform läuft“ ([Glossar](../wer-ist-die-exponential-foundation.md#glossar)). Es bedeutet nicht „alt“ oder
„ungepflegt“. Wenn jemand es locker verwendet, erklären Sie die Bedeutung einmal, freundlich, und machen Sie weiter.
Verwenden Sie es selbst nicht im Sinn von „alt“ ([Marken- und Namensregeln](../geschaeft/marken-und-namensregeln.md#die-produktfamilie)).

## Antwortvorlagen

```text
PHP IS OLD
Exponential runs on current PHP: 8.0 to 8.5 are supported, its tests run on 8.1 to 8.5 on every push,
and composer.json already allows later 8.x versions.
Source: (link to the support commitment)

DATED INTERFACE
Have a look at the admin4 design: light and dark mode, keyboard navigation, accessibility tests in CI.
There is also AdminUI as an alternative. Screenshots are better on your own install: one command,
./console exp:install, gives you a site on SQLite in minutes.

HEADLESS
There's a REST API (v1 and v2) for apps and other systems. If you want a Symfony stack with GraphQL,
the family has Exponential Platform DXP. (links)
```

## Sagen Sie nicht

- „PHP ist besser als Sprache X.“ Sprachkriege helfen niemandem.
- „Es ist das modernste CMS.“ Keine Quelle stützt einen Superlativ.
- „Es kann GraphQL.“ Exponential 6 bietet REST; GraphQL gehört zu den Symfony-Distributionen
  ([Exponential Platform DXP](../wer-ist-die-exponential-foundation.md#exponential-platform-dxp)).
- „Es wird PHP 9 unterstützen.“ Die Quellen nennen ein Ziel, kein Versprechen.

## Verwandte Seiten

- [Gesprächspunkte für Entwickler](gespraechspunkte-fuer-entwickler.md)
- [Die Produktfamilie kennen](die-produktfamilie-kennen.md)
- [Einwände als Fürsprecher beantworten](einwaende-beantworten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
