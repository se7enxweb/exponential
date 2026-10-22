---
title: Inhaltsmodellierung für Apps
audience: mobile-app-developer, app-creator, developer, website-creator
category: mobile-apps
order: 400
summary: Die Einstiegsseite der Seiten zur Inhaltsmodellierung für App-Entwickler - warum die Inhaltsklassen, IDs, Sprachen und der Rich Text einer Exponential-Website jeden Bildschirm einer App prägen und was Sie vor dem Bauen mit dem Website-Team vereinbaren.
keywords: Inhaltsmodell, Inhaltsklassen, Felder, Remote-ID, Deep Links, Sprachen, Übersetzungen, Rich Text, ezxmltext, Website-Team, Mobile App, content model, content classes, fields, remote id, deep links, languages, translations, rich text, site team, mobile app
type: help
---
# Inhaltsmodellierung für Apps

Dieser Ordner richtet sich an App-Entwickler, die auf Inhalten aufbauen, die Redakteure in Exponential pflegen. Die App
besitzt das Inhaltsmodell nicht: Das Website-Team definiert die Klassen und den Baum, und die App liest sie. Die Seiten
hier erklären, worauf Sie achten und was Sie vereinbaren, damit die App nicht bricht, wenn Redakteure ihre normale
Arbeit tun.

## Inhalt

1. [Warum das Modell für eine App zählt](#warum-das-modell-für-eine-app-zählt)
2. [Vor dem Bauen vereinbaren](#vor-dem-bauen-vereinbaren)
3. [Seiten in diesem Ordner](#seiten-in-diesem-ordner)

## Warum das Modell für eine App zählt

- Jeder Bildschirm entspricht einer **Inhaltsklasse**: eine Listenzelle für `article`, eine Detailansicht für `product`.
- Jeder Einstiegspunkt entspricht einem **Knoten**: dem Ordner, bei dem die Nachrichtenliste beginnt, der Seite, die
  ein Deep Link öffnet.
- Jeder Text hat eine **Sprache**, und ein Element kann nur in einigen Sprachen existieren.
- Artikeltexte sind **Rich Text**, der die REST-API als reiner Text erreicht.

## Vor dem Bauen vereinbaren

| Thema | Vereinbaren Sie | Lesen |
|---|---|---|
| Klassen und Felder | welche Klassen die App zeigt, welche Felder und wer Änderungen ankündigt | [Inhaltsklassen für Apps](inhaltsklassen-fuer-apps.md) |
| Einstiegspunkte | die Ordner, bei denen die App beginnt, erkennbar an der Remote-ID | [Knoten-IDs, Remote-IDs und Deep Links](knoten-ids-remote-ids-und-deep-links.md) |
| Sprachen | welche Locales die App unterstützt und was sie zeigt, wenn eine Übersetzung fehlt | [Mehrsprachige Inhalte](mehrsprachige-inhalte.md) |
| Rich Text | reiner Text, gerendertes XHTML oder ein strukturierter Export | [Rich Text in Apps](rich-text-in-apps.md) |
| Bilder | welche benannten Größen (Aliase) die App verwendet | [Bildaliase für Bildschirmgrößen](../medien-und-bilder/bildaliase-fuer-bildschirmgroessen.md) |

## Seiten in diesem Ordner

| Seite | Beantwortet |
|---|---|
| [Inhaltsklassen für Apps](inhaltsklassen-fuer-apps.md) | Klassendefinitionen lesen, Klassen Bildschirmen zuordnen, Klassenänderungen überstehen |
| [Knoten-IDs, Remote-IDs und Deep Links](knoten-ids-remote-ids-und-deep-links.md) | Stabile IDs, URL-Pfade und Links aus dem Web in die App |
| [Mehrsprachige Inhalte](mehrsprachige-inhalte.md) | `Translation`, `languageLocale`, die Sprachen der Website und Ersatzsprachen |
| [Rich Text in Apps](rich-text-in-apps.md) | Die drei Wege, Artikeltexte anzuzeigen |

## Verwandte Seiten

- [Wie Exponential-Inhalte in eine App gelangen](../wie-exponential-inhalte-in-eine-app-gelangen.md)
- [Ihr Inhaltsmodell entwerfen](../../ersteller/website-ersteller/ihr-inhaltsmodell-entwerfen.md)
- [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../anleitungen/inhaltsmodell-und-bearbeitung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
