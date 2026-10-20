---
title: "Eine Bestandsaufnahme der Seitentypen machen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1000
summary: "Vor dem Bauen auflisten, welche Arten von Seiten die Site hat und welches Layout jede bekommt: die Inhaltsklassen im Baum und die Layout-Zuordnungen."
keywords: Bestandsaufnahme, Seitentypen, Inhaltsklassen, Layout-Zuordnungen, Planung, Prüfung, inventory, page types, content classes, layout mappings, planning, audit
type: help
---
# Eine Bestandsaufnahme der Seitentypen machen

Jeden Seitentyp kennen (Startseite, Landingpage, Artikel, Rezept, Thema, Video, Kontakt, rechtliche Seite) und wissen, wie er heute gezeichnet wird.

*Lebenszyklus: Planen, Beispiel 1 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Im Inhaltsbaum und auf der Konsole](#im-inhaltsbaum-und-auf-der-konsole)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layout-Zuordnungen**: Jede Zuordnung nennt ein Layout, seine Ziele und seine Bedingungen.
2. Notieren Sie pro Zuordnung die Klassen-Bedingung (zum Beispiel `ng_recipe` für Full / Recipe) und das Ziel (Ort 89 für die Startseite).
3. Öffnen Sie **Layouts > Gemeinsame Layouts**, um zu sehen, was alle gemeinsam haben (Header / Footer, Prefooter).

## Im Inhaltsbaum und auf der Konsole

1. Öffnen Sie in der **Inhaltsstruktur** die Wurzel der Site (Fit & Healthy) und notieren Sie die Klassen ihrer Kinder: Landing page, Topic, Category, Contact form, Article, Container.
2. Führen Sie `./console exp:layouts list` aus für die Layouts mit ihren Zuordnungen (Spalte *Rules*) und Verknüpfungen (Spalte *Links to*).

## Welchen Weg Sie wählen

Beide Sichten werden gebraucht: Die Inhaltsklassen sagen, was es gibt, die Zuordnungen sagen, wie jedes gezeichnet wird.

## Prüfen

- Jede Klasse, die Seiten hat, erscheint in einer Zuordnung oder wird bewusst Default Layout überlassen (Pfadpräfix `/`, Priorität 10).

## Verwandte Seiten

- [Welches Layout bekommt eine Seite?](../../../../../eng-us/help/build-two-ways/layout-mappings.md)
- [Gemeinsame Layouts und Zonenverknüpfung](../../../../../eng-us/help/build-two-ways/shared-layouts-and-zone-linking.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
