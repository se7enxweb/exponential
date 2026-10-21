---
title: "Ihre Design-Erweiterung planen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1011
summary: "Eine Erweiterung für alles, was zur Site gehört: Templates, Stylesheets, Einstellungen, Übersetzungen, benannt, versioniert und an der richtigen Stelle aktiviert."
keywords: Design-Erweiterung, mysite, Versionsverwaltung, Reihenfolge der Aktivierung, Planung, design extension, version control, activation order, planning
type: help
---
# Ihre Design-Erweiterung planen

Änderungen an der Site landen nie im Theme oder im Kernel.

*Lebenszyklus: Planen, Beispiel 12 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Mit Templates](#mit-templates)
2. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
3. [Prüfen](#prüfen)

## Mit Templates

1. Geben Sie ihr einen Namen (diese Seiten verwenden `mysite`), eine `settings/design.ini.append.php` mit `DesignExtensions[]=mysite` und den Design-Ordner der Site (`design/media/`).
2. Planen Sie, sie an die erste Stelle in `ActiveExtensions[]` zu setzen: Dann haben ihre Templates Vorrang vor denen des Themes (für Blocktypen spielt die Position seit explayouts 1.4.42 keine Rolle).
3. Bewahren Sie sie in einem eigenen git-Repository auf.

## Welchen Weg Sie wählen

Auch eine Site, die überwiegend im Layout-Editor gebaut wird, braucht diese Erweiterung für ihre wenigen Templates und Styles.

## Prüfen

- `./console exp:ini get design.ini/ExtensionSettings/DesignExtensions -s site` listet sie nach dem Theme.

## Verwandte Seiten

- [Ihre eigene Design-Erweiterung](../../eigene-design-erweiterung.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
