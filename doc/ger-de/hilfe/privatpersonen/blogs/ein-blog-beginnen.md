---
title: Ein Blog beginnen
audience: individual, website-creator, editor
category: personal-sites
order: 1621
summary: Wie eine einzelne Person mit den mitgelieferten Klassen Blog und Blog post ein Blog in Exponential einrichtet - wo es im Baum liegt, Name und Beschreibung des Blogs, die ersten Beiträge, Adressen, ein Feed, das Menü und die Entscheidungen, die vor dem ersten Leser fallen sollten.
keywords: Blog beginnen, Blog einrichten, Klasse Blog, Blog post, erster Beitrag, Blogname, Beschreibung, URL, Menü, Feed, Entscheidung zu Kommentaren, Tag-Liste, über den Autor, start a blog, set up blog, first post
type: help
---
# Ein Blog beginnen

Diese Seite ist für Privatpersonen, die ein Blog einrichten möchten. Im Standard-Seitenpaket sind die Klassen *Blog*
und *Blog post* bereit; die Einrichtung besteht vor allem aus Entscheidungen und danach aus ein paar Minuten Klicken.

## Inhalt

1. [Zuerst entscheiden](#zuerst-entscheiden)
2. [Wo das Blog liegt](#wo-das-blog-liegt)
3. [Das Blog anlegen](#das-blog-anlegen)
4. [Die ersten drei Beiträge](#die-ersten-drei-beiträge)
5. [Auffindbar machen](#auffindbar-machen)
6. [Wenn Ihr Seitenpaket keine Klasse Blog hat](#wenn-ihr-seitenpaket-keine-klasse-blog-hat)
7. [Checkliste](#checkliste)

## Zuerst entscheiden

| Entscheidung | Möglichkeiten | Empfehlung |
|---|---|---|
| Worum geht es? | Ein Thema oder Ihr Leben allgemein | Ein Thema findet schneller Leser |
| Wie oft? | Wöchentlich, zweimal im Monat, monatlich | Wählen Sie, was Sie ein Jahr lang durchhalten |
| Kommentare? | Aus, nur angemeldete Leser, alle | Beginnen Sie mit „aus" oder „nur angemeldete" ([Kommentare in Ihrem Blog](kommentare-in-ihrem-blog.md)) |
| Tags? | Eine kurze feste Liste | Notieren Sie jetzt fünf bis zehn ([Tags und Kategorien](tags-und-kategorien.md)) |
| Ihr Name? | Echter Name, Vorname, Pseudonym | Entscheiden Sie vor dem ersten Beitrag; eine spätere Änderung ist umständlich |
| Allein oder mit jemandem? | Ein Autor, oder zwei mit eigenen Konten | Jeder Autor bekommt ein eigenes Konto ([Rollen für Ihr Team](../../kunden/alltag/rollen-fuer-ihr-team.md)) |

## Wo das Blog liegt

| Aufbau | Baum | Beispieladresse |
|---|---|---|
| Das Blog ist die ganze Website | Startseite > Blogbeiträge direkt, oder Startseite > *Blog* | `/` oder `/Blog` |
| Das Blog ist Teil einer persönlichen Website | Startseite > Über mich, Arbeiten, **Notes** (*Blog*) | `/Notes` |
| Zwei Blogs (zum Beispiel zwei Sprachen oder zwei Themen) | Zwei *Blog*-Objekte | `/Notes`, `/Travel` |

Adressen entstehen aus Namen ([URL-Aliase und sprechende URLs](../../ersteller/website-ersteller/url-aliasse-und-sprechende-urls.md)),
wählen Sie also einen kurzen, dauerhaften Namen.

## Das Blog anlegen

1. Gehen Sie in der Administration zum übergeordneten Ordner und wählen Sie **Hier erstellen** > *Blog*.
2. **Name**: wie Leser es nennen. **Beschreibung**: ein oder zwei Sätze darüber, worüber und wie oft Sie schreiben.
3. **Tags** am Blog selbst sind optional.
4. **Metadaten**: ein Titel und eine Beschreibung für Suchmaschinen
   ([Metadaten und Teilen in sozialen Netzwerken](../../ersteller/website-ersteller/metadaten-und-teilen-in-sozialen-medien.md)).
5. Veröffentlichen. Die Blogseite listet ihre Beiträge, die neuesten zuerst, sobald es welche gibt.

## Die ersten drei Beiträge

Starten Sie mit drei Beiträgen, nicht mit einem: Wer den ersten mag, möchte einen zweiten.

1. **Hallo**: wer Sie sind, worum es im Blog geht, wie oft Sie schreiben.
2. **Ihr bester Text** zum Thema.
3. **Ein kürzerer, praktischer Beitrag**, der die Bandbreite zeigt.

Jeweils: **Hier erstellen** > *Blog post* unter dem Blog, Titel, Text, Tags, Entscheidung zu Kommentaren,
veröffentlichen ([Beiträge schreiben und planen](beitraege-schreiben-und-planen.md)).

## Auffindbar machen

- **Menü**: Stellen Sie sicher, dass das Blog im Hauptmenü der Website erscheint
  ([Menüs und Navigation](../../ersteller/website-ersteller/menues-und-navigation.md)).
- **Feed**: Legen Sie einen RSS-Export des Blogs an ([RSS-Feeds für Ihr Blog](rss-feeds-fuer-ihr-blog.md)).
- **Sitemap und Suchmaschinen**: [Sitemaps](../../ersteller/website-ersteller/sitemaps.md),
  [SEO-Grundlagen](../../ersteller/website-ersteller/seo-grundlagen.md).
- **Erzählen Sie davon**: ein Link von Ihrer Über-mich-Seite, Ihren Profilen anderswo und Ihrer E-Mail-Signatur.

## Wenn Ihr Seitenpaket keine Klasse Blog hat

Die `_clean`-Pakete enthalten dieselben Klassen ohne Demo-Inhalte. Einer älteren oder eigenen Installation können
sie fehlen; dann funktioniert ein *Folder* mit *Article*-Objekten als einfaches Blog, oder die Klassen lassen sich
aus einem Paket importieren ([Pakete](../../../anleitungen/pakete.md)). Prüfen Sie zuerst **Setup > Klassen**.

## Checkliste

- [ ] Thema, Tempo, Kommentarregel, Tag-Liste und Autorenname entschieden.
- [ ] *Blog*-Objekt mit klarem Namen und klarer Beschreibung.
- [ ] Drei Beiträge veröffentlicht.
- [ ] Blog im Menü.
- [ ] RSS-Export angelegt und verlinkt.
- [ ] Ein Test auf einem Smartphone.

## Verwandte Seiten

- [Blogs](README.md)
- [Beiträge schreiben und planen](beitraege-schreiben-und-planen.md)
- [Blogs: hier beginnen (Kunden)](../../kunden/typen/blogs-hier-beginnen.md)
- [Redaktionskalender](../../kunden/alltag/redaktionskalender.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
