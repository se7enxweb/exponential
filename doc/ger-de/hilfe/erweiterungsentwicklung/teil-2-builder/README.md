---
title: "Teil 2: Der Builder: Einstellungen, Templates und Overrides"
audience: developer, designer, app-creator
category: extension-development
order: 20
summary: Der zweite Teil des Buchs. Ändern Sie, wie Exponential sich verhält und aussieht, ohne PHP - Einstellungen und ihre Ebenen, Templates, die Design-Kette, Overrides, View-Modi, Fetch, Klassen, Rollen, Workflows und Cronjobs.
keywords: Buch, Teil 2, Builder, Site-Builder, Einstellungen, INI, Templates, Overrides, Design, View-Modi, Fetch, Inhaltsklassen, Rollen, Workflows, Cronjobs, book, part 2, site builder, settings, view modes, content classes, roles
type: help
---
# Teil 2: Der Builder: Einstellungen, Templates und Overrides

Dies ist der zweite Teil von [Erweiterungsentwicklung: das Buch](../README.md). Ein **Builder** ändert, wie sich eine
Website verhält und wie sie aussieht, ohne PHP zu schreiben. Die meisten echten Projekte entstehen fast vollständig mit
den Werkzeugen dieses Teils, und jeder Erweiterungsentwickler und jeder Theme-Entwickler nutzt sie täglich. Dieser Teil
lohnt sich deshalb auch dann, wenn Sie wegen PHP gekommen sind.

Vom ersten Kapitel an arbeiten Sie in einer eigenen Erweiterung: einer **Einstellungserweiterung** für die
Konfiguration Ihrer Website und einer **Design-Erweiterung** für ihre Templates. So liegt jede Änderung in einem Ordner
unter Versionskontrolle, statt über `settings/override/` und die Verzeichnisse des Kernels verstreut zu sein.

## Kapitel

### Einstellungen

| Kapitel | Was Sie lernen |
|---|---|
| [Wie INI-Einstellungen funktionieren](wie-ini-einstellungen-funktionieren.md) | Dateien, Blöcke, Variablen, Arrays, Hashes, Kommentare, der INI-Cache |
| [Einstellungsebenen und die Reihenfolge, in der sie gewinnen](einstellungsebenen-und-ihre-reihenfolge.md) | Standard, Erweiterung, Siteaccess, Override und die Verzeichnisse je Website in Erweiterungen |
| [Siteaccess-Einstellungen](siteaccess-einstellungen.md) | Was ein Siteaccess ist, wie eine Anfrage einen auswählt, was in seine Einstellungen gehört |
| [Einstellungen in einer Erweiterung](erweiterungseinstellungen-fuer-builder.md) | Die Konfiguration einer Website in eine Erweiterung verlagern |
| [Einstellungen mit exp:ini ändern](der-befehl-exp-ini.md) | Jedes Verb des Einstellungsbefehls, mit Probeläufen und Sicherungen |
| [Einstellungen in der Administration](einstellungen-in-der-administration.md) | Setup > Ini Einstellungen: die Override-Kette, Vergleich, sicheres Speichern |
| [.ini.append.php-Dateien und sichere Einstellungen](append-php-dateien-und-sicherheit.md) | Warum der PHP-Rahmen, Geheimnisse, Berechtigungen, was nie committet wird |

### Templates

| Kapitel | Was Sie lernen |
|---|---|
| [Templates für Builder](templates-fuer-builder.md) | Die Template-Sprache: Variablen, Bedingungen, Schleifen, Includes |
| [Die Design-Kette: wo ein Template gefunden wird](die-design-kette.md) | Site-Design, zusätzliche Designs, Design-Erweiterungen, standard |
| [Template-Overrides: die Grundlagen](grundlagen-der-template-overrides.md) | Ein Template nur für bestimmte Inhalte ersetzen |
| [Override-Bedingungen](override-bedingungen.md) | Jeder Match-Schlüssel: Klasse, Knoten, Elternknoten, Sektion, Status, View-Modus, Tiefe und mehr |
| [View-Modi für Builder](view-modi-fuer-builder.md) | full, line, embed und eigene |
| [Inhalte in Templates abrufen](fetch-fuer-builder.md) | `fetch()`: Listen, Bäume, Anzahlen, Filter, Sortierung |
| [Die Template-Operatoren, die Builder am häufigsten nutzen](die-wichtigsten-template-operatoren-fuer-builder.md) | Ein Arbeitssatz an Operatoren mit Beispielen |
| [Templates debuggen](templates-debuggen.md) | Herausfinden, warum ein Template oder ein Override nicht greift |
| [Caching und Templates](caching-und-templates.md) | Der View-Cache, `cache-block` und wann Sie was leeren |

### Der Rest des Werkzeugkastens

| Kapitel | Was Sie lernen |
|---|---|
| [Inhaltsklassen für Builder](inhaltsklassen-fuer-builder.md) | Das Inhaltsmodell entwerfen |
| [Sektionen, Rollen und Zugriff für Builder](sektionen-rollen-und-zugriff-fuer-builder.md) | Wer was sehen und tun darf |
| [Workflows für Builder](workflows-fuer-builder.md) | Freigabe und andere Ereignisse beim Veröffentlichen |
| [Cronjobs für Builder](cronjobs-fuer-builder.md) | Was zeitgesteuert läuft und wie Sie es einrichten |
| [Eine Website bauen, ohne Code zu schreiben](eine-website-ohne-code-bauen.md) | Eine vollständige kleine Website aus Einstellungen, Klassen und Templates |
| [Wenn Einstellungen nicht reichen](wenn-einstellungen-nicht-reichen.md) | Entscheiden, wann Sie PHP schreiben und welchen Erweiterungspunkt Sie nutzen |

## Bevor Sie beginnen

Sie brauchen eine Entwicklungsinstallation ([Eine Entwicklungsinstallation einrichten](../teil-1-vom-benutzer-zum-entwickler/eine-entwicklungsinstallation-einrichten.md))
mit eingeschalteter Debug-Ausgabe, und Sie sollten [Ihre erste Stunde als Entwickler](../teil-1-vom-benutzer-zum-entwickler/ihre-erste-stunde-als-entwickler.md)
durchgearbeitet haben.

## Verwandte Seiten

- Zurück: [Die Community um Hilfe bitten](../teil-1-vom-benutzer-zum-entwickler/die-community-fragen.md)
- Weiter: [Wie INI-Einstellungen funktionieren](wie-ini-einstellungen-funktionieren.md)
- [Erweiterungsentwicklung: das Buch](../README.md)
- [Templates und Design](../../../anleitungen/templates-und-design.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
