---
title: Umstieg von eZ Platform oder Ibexa
audience: cms-developer
category: cms-development
order: 7
summary: Für Entwickler, die eZ Platform oder Ibexa DXP und OSS kennen. Das Inhaltsmodell, das Sie kennen, ist dasselbe, und diese Seite zeigt, was sich bei Templates, Konfiguration, Erweiterungspunkten und Auslieferung unterscheidet. Sie behandelt auch die beiden Ziele in Exponential, die Symfony-Plattformlinie und den Legacy-Kernel von Exponential 6.
keywords: eZ Platform, Ibexa, Ibexa DXP, Ibexa OSS, Umstieg von Ibexa, Legacy-Kernel, Exponential Platform, Nexus, Legacy Bridge, Twig, TPL, Siteaccess-YAML, INI, Feldtypen, Datentypen, RichText, XmlText, Bundles, Erweiterungen, Migration, coming from Ibexa, legacy kernel, field types, datatypes, extensions
type: help
---
# Umstieg von eZ Platform oder Ibexa

Diese Seite richtet sich an Entwickler, die Websites mit eZ Platform oder Ibexa DXP oder OSS gebaut haben und jetzt mit
Exponential arbeiten. Das Inhaltsmodell kennen Sie bereits: Es ist dasselbe. Was sich ändert, ist die Schicht darüber.
Diese Seite sagt Ihnen, was unverändert übernommen wird und was Sie neu lernen müssen, und verweist für echte Projekte
auf das Migrationskapitel.

Exponential wird von 7x und der Exponential Foundation geführt; es steht nicht in Verbindung mit eZ Systems oder Ibexa.

## Inhalt

1. [Zwei Ziele in Exponential](#zwei-ziele-in-exponential)
2. [Was Sie schon kennen](#was-sie-schon-kennen)
3. [Was beim Legacy-Kernel anders ist](#was-beim-legacy-kernel-anders-ist)
4. [Feldtypen und Datentypen](#feldtypen-und-datentypen)
5. [Konfiguration: von YAML zu INI](#konfiguration-von-yaml-zu-ini)
6. [Templates: von Twig zu TPL](#templates-von-twig-zu-tpl)
7. [Von Bundles zu Erweiterungen](#von-bundles-zu-erweiterungen)
8. [Wie es weitergeht](#wie-es-weitergeht)

## Zwei Ziele in Exponential

Exponential hat zwei Linien für jemanden, der von den Symfony-Plattformen kommt
([Kapitel 16.1](../../installation/16-migration-von-ez-platform-und-ibexa.md#161-die-beiden-ziele)):

| | Exponential Platform (Nexus und die Plattform-Distributionen) | Exponential 6 (dieser Kernel), allein oder als Exponential Platform Legacy |
|---|---|---|
| Stack | der Symfony-Stack, den Sie kennen, mit den Paketen ausgetauscht gegen die Forks von `se7enxweb` | der Legacy-PHP-Kernel: INI-Einstellungen, TPL-Templates, Module und Erweiterungen |
| Ihr Twig und Ihre Bundles | bleiben erhalten | werden als Templates und Erweiterungen neu geschrieben |
| Aufwand eines Umzugs | am geringsten: dieselbe Generation, ein Paketaustausch | am höchsten: Darstellung und eigener Code werden neu geschrieben, die Inhalte bleiben |

Die **Legacy Bridge** kann diesen Kernel auch innerhalb einer Installation der Symfony-Plattform ausführen, sodass
während eines Übergangs beide Stacks eine Datenbank bedienen ([Legacy Bridge](../../funktionen/6.0/legacy-bridge.md)).
Die Seiten dieses Bereichs beschreiben Exponential 6, den Legacy-Kernel.

## Was Sie schon kennen

Das Inhaltsmodell jeder Generation ist das des Legacy-Kernels selbst. Klassen (Inhaltstypen), Objekte
(Inhaltselemente), Versionen, Attribute (Felder), Platzierungen, URL-Aliase, Rollen und Richtlinien, Sektionen,
Objektzustände und Sprachen sind dieselben Tabellen mit derselben Bedeutung
([16.6.1](../../installation/16-migration-von-ez-platform-und-ibexa.md#1661-was-beide-gemeinsam-haben)). Diese Seiten werden
Ihnen daher vertraut vorkommen:

| Begriff in Ibexa | Begriff in Exponential | Lesen |
|---|---|---|
| Content Type (Inhaltstyp) | Inhaltsklasse | [Inhaltsklassen und Attribute](inhaltsklassen-und-attribute.md) |
| Felddefinition, Feldtyp | Klassenattribut, Datentyp | [Datentypen wählen](datentypen-waehlen.md) |
| Content Item (Inhaltselement) | Inhaltsobjekt | [Das Inhaltsmodell](das-inhaltsmodell.md) |
| Location | Knoten (Platzierung) | [Objekte, Knoten und Platzierungen](objekte-knoten-und-platzierungen.md) |
| Siteaccess | Siteaccess (der Begriff ist hier entstanden) | [Siteaccesses und Multisite](siteaccesses-und-multisite.md) |
| Richtlinien, Einschränkungen, Sektionen, Objektzustände | dasselbe | [Sektionen, Rollen und Richtlinien](sektionen-rollen-und-richtlinien.md) |

## Was beim Legacy-Kernel anders ist

Aus [16.6.2](../../installation/16-migration-von-ez-platform-und-ibexa.md#1662-was-sie-verlieren-und-was-sie-gewinnen), kurz
gefasst:

- **Administration.** Die Legacy-Administration und der Online Editor für XmlText statt der Admin-Oberfläche von
  Symfony.
- **Ausgabe.** TPL-Templates mit dem Override-System und Template-Fetch-Funktionen statt Twig, Controllern und Query
  Types.
- **APIs.** Die Legacy-REST-Schicht (`ezprestapi`, v1 und v2) und Serverfunktionen von `ezjscore` statt REST v2 und
  GraphQL des Symfony-Stacks ([Die REST-API](die-rest-api.md)).
- **Funktionen, die Sie gewinnen.** Workflow-Events, Informationssammlung, der Shop, RSS-Import und -Export,
  Newsletter, Exponential Layouts (aus den Upstream-Layouts-Paketen portiert), Velocity als Webserver sowie Treiber für
  SQLite, MongoDB und Oracle.

## Feldtypen und Datentypen

Die meisten Feldtypen sind die Legacy-Datentypen mit demselben Bezeichner (`ezstring`, `ezimage`,
`ezobjectrelationlist` und so weiter). Die Ausnahmen, denen Sie begegnen werden:

- `ezrichtext` (DocBook) hat keinen Legacy-Datentyp; Legacy-Rich-Text ist `ezxmltext` ([Rich Text](rich-text.md)).
  Kapitel 16.6.3 enthält die Zuordnung der Elemente und die zwei Wege, mit gespeichertem DocBook umzugehen.
- `ezimageasset` wird zu `ezobjectrelation`; `ezcontentquery` wird zu einem Template-Fetch; Landingpages und
  Formulare der kommerziellen Editionen werden mit Exponential Layouts und Informationssammlung neu gebaut.

Die vollständige Tabelle: [16.6.3 Feldtypen](../../installation/16-migration-von-ez-platform-und-ibexa.md#1663-feldtypen).

## Konfiguration: von YAML zu INI

Siteaccess-YAML (`ibexa.system.<scope>`) wird zu INI-Dateien: `settings/siteaccess/<name>/site.ini.append.php` für
einen Siteaccess, `settings/override/` für globale Werte und `extension/<ext>/settings/` für das, was Ihr Code
mitliefert. Die Geltungsbereiche und ihre Reihenfolge erklärt
[Einstellungen und INI für CMS-Entwickler](einstellungen-und-ini-fuer-cms-entwickler.md); die Zuordnung der Schlüssel steht
in [16.6.5](../../installation/16-migration-von-ez-platform-und-ibexa.md#1665-konfiguration-siteaccess-yaml-zu-ini).

## Templates: von Twig zu TPL

Die Konfiguration der Inhaltsansichten (`content_view` mit `match`-Regeln) wird zu Regeln in `override.ini` mit
`Match[...]`-Schlüsseln; `ibexa_render_field` wird zu `attribute_view_gui`; Query Types und Controller werden zu
`fetch()`-Aufrufen im Template.
[Templates und Overrides für CMS-Entwickler](templates-und-overrides-fuer-cms-entwickler.md) und
[16.6.6 Templates: von Twig zu TPL](../../installation/16-migration-von-ez-platform-und-ibexa.md#1666-templates-twig-zu-tpl)
führen von dort weiter.

## Von Bundles zu Erweiterungen

Ein Symfony-Bundle wird zu einer Erweiterung in `extension/<name>`: Event-Subscriber werden zu Workflow-Events oder
Event-Listenern, Controller werden zu Modul-Views, Twig-Erweiterungen werden zu Template-Operatoren, Befehle werden
zu Konsolenbefehlen oder `bin/php`-Skripten, und Services werden zu PHP-Klassen, die über Autoloads registriert
werden ([Erweiterungen, die Sie schreiben werden](erweiterungen-die-sie-schreiben-werden.md)).

## Wie es weitergeht

- Eine echte Website umziehen: [Kapitel 16](../../installation/16-migration-von-ez-platform-und-ibexa.md) und für die
  Hosting-Seite [Kunden von eZ Platform und Ibexa migrieren](../hosting/migrationen/kunden-von-ez-platform-und-ibexa-migrieren.md).
- Schulung: [Von den Symfony-Plattformen](../trainer/migration/modul-06-von-den-symfony-plattformen/README.md).
- Zwischen den Linien wählen: [Einen Weg wählen](../trainer/migration/modul-06-von-den-symfony-plattformen/zwei-ziele-und-die-wahl.md).

## Verwandte Seiten

- [Exponential für CMS-Entwickler](exponential-fuer-cms-entwickler.md)
- [Wer ist 7x?](../wer-ist-7x.md)
- [Exponential Platform Nexus Starter](../../funktionen/6.0/plattform-nexus-starter.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
