---
title: Fußzeilen-Links aus dem Inhaltsbaum
audience: editor, website-creator, admin
category: build-two-ways
order: 210
summary: Schritt für Schritt mit Bildschirmfotos; einen Link zur zweiten Zeile der Fußzeile hinzufügen, indem Sie ein Menu item in Media > Configuration > Footer links anlegen; keine Einstellungen, kein Code, kein Leeren eines Cache. Außerdem Sortieren, Verstecken und die besonderen URLs, die die Fußzeile versteht.
keywords: Fußzeilen-Links, Menüeintrag, ng_menu_item, Ordner Footer links, Inhaltsbaum, Item URL, Target blank, Priorität, Sortierung, Redakteur, ohne Code, footer links, menu item, Footer links folder, content tree, item URL, target blank, priority, sort, editor, no code
type: help
---
# Fußzeilen-Links aus dem Inhaltsbaum

Die zweite Zeile der Fußzeile von Fit & Healthy wird aus Inhalten gebaut: Jedes **Menu item** im Ordner **Footer links**
ist ein Link. Das ist das einzige Menü des Themes, das Redakteure vollständig im Inhaltsbaum ändern.

## Inhalt

1. [Den Ordner öffnen](#1-den-ordner-öffnen)
2. [Ein Menu item anlegen](#2-ein-menu-item-anlegen)
3. [Titel und URL ausfüllen](#3-titel-und-url-ausfüllen)
4. [Veröffentlichen und prüfen](#4-veröffentlichen-und-prüfen)
5. [Ordnen, verstecken, entfernen](#ordnen-verstecken-entfernen)
6. [Besondere URLs](#besondere-urls)
7. [Wo der Ordner festgelegt ist](#wo-der-ordner-festgelegt-ist)

## 1. Den Ordner öffnen

Öffnen Sie in der Administration **Medien**, dann **Configuration > Footer links**. Es ist ein nach Priorität sortierter
Ordner und enthält auf der Site Fit & Healthy sieben Menu items: Login, Signup, Cart, Edit, Tagcloud, Sitemap und Forgot Password.

## 2. Ein Menu item anlegen

Klicken Sie über der Liste der Unterelemente auf **Neu erstellen** und wählen Sie **Menu item** (Gruppe *Netgen*). Wählen
Sie die Sprache (English (American) ist vorausgewählt) und klicken Sie auf **OK**.

## 3. Titel und URL ausfüllen

| Feld | Eingabe | Von der Fußzeile verwendet |
|---|---|---|
| Title (Pflichtfeld) | Der Linktext, zum Beispiel `Showcase` | ja, der Linktext |
| Item URL > URL | Die Adresse: ein Pfad der Site (`/showcase`) oder eine vollständige URL (`https://...`) | ja, der Link |
| Target blank | Anhaken, um den Link in einem neuen Fenster zu öffnen (`target="_blank" rel="noopener noreferrer"`) | ja |
| Item object, Menu items, Parent node, Class filter, Max depth, Limit, Use menu item name | andere Verwendungen der Klasse | nicht von dieser Fußzeile |

![Das Bearbeitungsformular eines neuen Menu item mit dem Titel Showcase und der URL /showcase](../../../eng-us/help/build-two-ways/images/ft-03-menu-item-form.webp)

Ein Menu item ohne URL wird von der Fußzeile übersprungen.

## 4. Veröffentlichen und prüfen

Klicken Sie auf **Zur Veröffentlichung schicken**. Der Ordner listet jetzt den neuen Eintrag.

![Der Ordner Footer links mit acht Unterelementen, Showcase zuerst](../../../eng-us/help/build-two-ways/images/ft-04-footer-links-after-publish.webp)

Auf der Testkopie musste kein Cache geleert werden: Die nächste Seite zeigte den Link bereits, an erster Stelle der zweiten
Zeile, weil ein neuer Eintrag die Priorität 0 bekommt und die anderen 10 bis 70 haben.

![Die Fußzeile mit Showcase an erster Stelle der zweiten Zeile](../../../eng-us/help/build-two-ways/images/ft-05-footer-after.webp)

## Ordnen, verstecken, entfernen

- **Ordnen**: Der Ordner sortiert nach Priorität. Ändern Sie die Zahlen in der Spalte **Priorität** der Liste der
  Unterelemente (oder im Reiter **Sortierung**) und speichern Sie: Niedrigere Zahlen kommen zuerst.
- **Verstecken**: Verstecken Sie das Menu item (über sein Menü in der Liste oder die Sichtbarkeits-Schaltfläche). Die
  Fußzeile listet nur, was der Besucher sehen darf.
- **Entfernen**: Verschieben Sie es in den Papierkorb.

## Besondere URLs

Die Fußzeile schreibt drei URLs um, damit dieselben Einträge auf jeder Site funktionieren:

| URL im Eintrag | Die Fußzeile verlinkt auf |
|---|---|
| `/content/edit` | das Bearbeitungsformular der angezeigten Seite; für anonyme Besucher die Anmeldeseite |
| `/content/view/sitemap` | die Sitemap des Startknotens des Siteaccess (`[SiteSettings] IndexPage`) |
| `/content/view/tagcloud` | die Tag-Wolke des Startknotens |

## Wo der Ordner festgelegt ist

Die Fußzeile findet den Ordner über seine Remote-ID: `menu.ini [SiteInfo] FooterLinksRemoteID`, Standard
`media-o-footer-links`. Um einen anderen Ordner zu verwenden (zum Beispiel einen pro Site), geben Sie die Remote-ID dieses
Ordners in der `menu.ini` des Siteaccess an. Es werden nur Kinder der Klasse `ng_menu_item` gezeigt.

## Verwandte Seiten

- [Die Einträge des Fußzeilenmenüs](eintraege-des-fusszeilenmenues.md)
- [Wie die Fußzeile aufgebaut ist](wie-die-fusszeile-aufgebaut-ist.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
