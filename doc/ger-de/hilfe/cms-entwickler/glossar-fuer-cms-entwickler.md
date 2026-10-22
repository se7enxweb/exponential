---
title: Glossar für CMS-Entwickler
audience: cms-developer
category: cms-development
order: 49
summary: Für CMS-Entwickler die Begriffe von Exponential an einer Stelle, mit dem Begriff anderer Systeme, wo er abweicht, und der Seite, die jeden erklärt. Das Glossar behandelt Inhalte, Darstellung, Konfiguration, Auslieferung und das Projekt.
keywords: Glossar, Begriffe, Definitionen, Inhaltsklasse, Attribut, Datentyp, Objekt, Knoten, Platzierung, Hauptknoten, Sektion, Objektzustand, Siteaccess, Design, Override, Ansichtsmodus, Pagelayout, Fetch, Operator, Erweiterung, Cronjob, Workflow, Auslöser, Layout, Zone, Block, Paket, Velocity, glossary, terms, definitions, content class, attribute, datatype, node, location, main node, section, object state, view mode, extension, trigger, package
type: help
---
# Glossar für CMS-Entwickler

Diese Seite richtet sich an Entwickler, die in Exponential auf ein Wort stoßen und seine Bedeutung und weiterführende
Lektüre suchen. Wo andere Systeme für dieselbe Idee ein anderes Wort verwenden, steht es in Klammern.

## Inhalt

1. [Inhalte](#inhalte)
2. [Darstellung](#darstellung)
3. [Konfiguration und Code](#konfiguration-und-code)
4. [Auslieferung und Betrieb](#auslieferung-und-betrieb)
5. [Das Projekt](#das-projekt)

## Inhalte

| Begriff | Bedeutung | Lesen |
|---|---|---|
| Inhaltsklasse (content class) | ein Inhaltstyp mit Attributen (Content Type, Post Type, Bundle) | [Inhaltsklassen und Attribute](inhaltsklassen-und-attribute.md) |
| Klassenattribut (class attribute) | ein Feld einer Klasse, von einem Datentyp (Felddefinition) | [Inhaltsklassen und Attribute](inhaltsklassen-und-attribute.md#eigenschaften-der-attribute) |
| Datentyp (datatype) | die Art eines Feldes: Speicherung, Prüfung, Bearbeitung, Ausgabe (Feldtyp) | [Datentypen wählen](datentypen-waehlen.md) |
| Inhaltsobjekt (content object) | ein Element einer Klasse, mit Versionen und Übersetzungen (Eintrag, Beitrag, Content Item) | [Das Inhaltsmodell](das-inhaltsmodell.md) |
| Version | ein gespeicherter Zustand eines Objekts: Entwurf, veröffentlicht, archiviert und andere (Revision) | [Versionen, Entwürfe und Veröffentlichen](versionen-entwuerfe-und-veroeffentlichen.md) |
| Knoten, Platzierung (node, location) | ein Platz eines Objekts im Inhaltsbaum | [Objekte, Knoten und Platzierungen](objekte-knoten-und-platzierungen.md) |
| Hauptknoten (main node) | die Platzierung, die einem Objekt seine kanonische Adresse gibt | [Objekte, Knoten und Platzierungen](objekte-knoten-und-platzierungen.md#der-hauptknoten-und-weitere-platzierungen) |
| Inhaltsbaum (content tree) | der eine Baum aller Knoten; die Inhaltswurzel ist Knoten 2 | [Objekte, Knoten und Platzierungen](objekte-knoten-und-platzierungen.md#der-baum-pfade-tiefe-und-die-festen-spitzen) |
| Remote-ID | ein eigener stabiler Bezeichner für ein Objekt oder eine Klasse | [Das Inhaltsmodell](das-inhaltsmodell.md#bezeichner-die-sie-täglich-verwenden) |
| Relation | ein erfasster Verweis zwischen Objekten: Attribut, Einbettung, Link oder allgemein | [Relationen und Einbettungen](relationen-und-einbettungen.md) |
| XML Block (`ezxmltext`) | Rich Text, als XML gespeichert und von Tag-Templates gezeichnet | [Rich Text](rich-text.md) |
| Eigenes Tag (custom tag) | ein eigenes Rich-Text-Tag | [Rich Text](rich-text.md#eigene-tags-ihre-eigene-struktur) |
| Bildvariante (image alias) | eine benannte Größe und Behandlung von Bildern in `image.ini` (Image Style, Variation) | [Bilder und Bildvarianten](bilder-und-bildvarianten.md) |
| Sektion (section) | eine Kennzeichnung je Objekt, die Rollen und Templates verwenden | [Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md) |
| Objektzustand (object state) | eigene Kennzeichnungen in Gruppen, in Richtlinien verwendbar | [Objektzustände](../../anleitungen/objektzustaende.md) |
| Rolle, Richtlinie, Einschränkung (role, policy, limitation) | ein Satz von Berechtigungen, eine Berechtigung, eine Bedingung daran | [Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md) |
| Informationssammler (information collector) | ein Feld, das Besucher in einem Formular auf der Website ausfüllen | [Formulare und Informationssammlung](formulare-und-informationssammlung.md) |
| Immer verfügbar (always available) | ein Objekt, das in seiner Hauptsprache angezeigt wird, wo keine aufgeführte Sprache vorhanden ist | [Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md#immer-verfügbare-objekte) |

## Darstellung

| Begriff | Bedeutung | Lesen |
|---|---|---|
| Design | ein Satz aus Templates, Styles und Bildern; Designs fallen in einer Kette zurück (Theme) | [Templates und Overrides](templates-und-overrides-fuer-cms-entwickler.md#die-design-kette) |
| Pagelayout | das äußere Template jeder Seite (Layout, Basis-Template) | [Das Pagelayout](../designer/theme-design/das-pagelayout.md) |
| Override-Regel | eine Regel in `override.ini`, die ein Template nach Klasse, Knoten, Sektion und mehr wählt (Template-Vorschlag) | [Templates und Overrides](templates-und-overrides-fuer-cms-entwickler.md#override-regeln) |
| Ansichtsmodus (view mode) | wie Inhalte in einem Zusammenhang gezeigt werden: full, line, embed, eigene (View Mode, Display Mode) | [Ansichtsmodi](ansichtsmodi.md) |
| Fetch-Funktion | eine Template-Funktion, die Inhalte zurückgibt (Abfrage) | [Inhalte in Templates abrufen](inhalte-in-templates-abrufen.md) |
| Template-Operator | eine Funktion, die in einem Template an einen Wert angehängt wird (Filter) | [Template-Operatoren](../template-operatoren/README.md) |
| Cache-Block | ein zwischengespeicherter Teil eines Templates | [Caching für CMS-Entwickler](caching-fuer-cms-entwickler.md#cache-blöcke) |
| Layout, Zone, Block, Regel | Exponential Layouts: ein Seitenplan, seine Bereiche, seine Teile, wo er gilt (Page Builder) | [Layouts für CMS-Entwickler](layouts-fuer-cms-entwickler.md) |

## Konfiguration und Code

| Begriff | Bedeutung | Lesen |
|---|---|---|
| Siteaccess | ein Zugang zu einer Installation: ein Name, Einstellungen und eine Zuordnungsregel | [Siteaccesses und Multisite](siteaccesses-und-multisite.md) |
| INI-Datei, Append-Datei | das Format der Einstellungen; `*.ini.append.php` überschreibt Standards | [Einstellungen und INI](einstellungen-und-ini-fuer-cms-entwickler.md) |
| Erweiterung (extension) | ein Ordner unter `extension/`, der Designs, Einstellungen und Code hinzufügt (Plugin, Modul, Bundle) | [Erweiterungen, die Sie schreiben werden](erweiterungen-die-sie-schreiben-werden.md) |
| Modul, View | eine Gruppe von Seiten unter einem Adresspräfix und eine ihrer Seiten (Route, Controller) | [URL-Aliase und Routing](url-aliase-und-routing.md#eigene-seiten) |
| URL-Alias | die lesbare Adresse eines Knotens (Slug, Pfad-Alias, Permalink) | [URL-Aliase und Routing](url-aliase-und-routing.md) |
| Workflow, Event, Auslöser | eine Liste von Schritten, die vor oder nach einer Operation laufen | [Workflows und Auslöser](workflows-und-ausloeser.md) |
| Event-Listener | ein Callback für ein Event, das der Kernel ankündigt (Hook, Subscriber) | [Workflows und Auslöser](workflows-und-ausloeser.md#event-listener-der-leichtere-weg) |
| Cronjob, Teil | ein geplantes Skript und eine Gruppe davon | [Cronjobs](cronjobs.md) |

## Auslieferung und Betrieb

| Begriff | Bedeutung | Lesen |
|---|---|---|
| Velocity | der eigene PHP-Anwendungsserver von Exponential, die empfohlene Engine | [Eine Website bereitstellen](eine-website-bereitstellen.md) |
| FrankenPHP | der produktionsreife alternative Server | [Eine Website bereitstellen](eine-website-bereitstellen.md#wählen-wie-die-website-ausgeliefert-wird) |
| View-Cache | gespeicherte gerenderte Ansichten von Knoten | [Caching für CMS-Entwickler](caching-fuer-cms-entwickler.md#der-view-cache) |
| Paket (`.ezpkg`) | ein Archiv mit Klassen, Inhalten, Erweiterungen oder einer ganzen Website | [Seitenpakete und Installer](seitenpakete-und-installer.md) |
| Seitenpaket (site package) | ein Paket vom Typ `site`, das eine ganze Website installiert | [Seitenpakete und Installer](seitenpakete-und-installer.md#das-standard-seitenpaket) |
| `exp:install`, Kickstarter, Einrichtungsassistent | die drei Installer | [Seitenpakete und Installer](seitenpakete-und-installer.md#die-drei-installer) |
| `ezprestapi` | die Erweiterung, die die REST-Inhaltsrouten bedient | [Die REST-API](die-rest-api.md) |
| `expservices` | die Funktionen der Administration als HTTP-Dienste | [Headless-Auslieferung](headless-auslieferung.md#expservices) |

## Das Projekt

| Begriff | Bedeutung | Lesen |
|---|---|---|
| Exponential | das Produkt; Exponential 6 ist dieser PHP-Kernel | [Exponential für CMS-Entwickler](exponential-fuer-cms-entwickler.md) |
| 7x | das Unternehmen, das Exponential führt | [Wer ist 7x?](../wer-ist-7x.md) |
| Exponential Foundation | die gemeinnützige Organisation | [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md) |
| Exponential Platform | die Linie auf Basis von Symfony (Nexus und die Plattform-Distributionen) | [Umstieg von eZ Platform oder Ibexa](umstieg-von-ez-platform-oder-ibexa.md) |

Das Glossar für Erweiterungsentwickler enthält weitere Begriffe von der Code-Seite:
[Glossar für Erweiterungsentwickler](../erweiterungsentwicklung/teil-8-referenz/glossar-fuer-erweiterungsentwickler.md);
das Glossar der Designer die Begriffe des Designs: [Design-Glossar](../designer/theme-design/design-glossar.md).

## Verwandte Seiten

- [FAQ für CMS-Entwickler](faq-fuer-cms-entwickler.md)
- [Zwei Wege zum Bauen: Glossar](../zwei-wege-zum-bauen/glossar.md)
- [Glossar zu Seitenpaketen](../seitenpakete/glossar-der-seitenpakete.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
