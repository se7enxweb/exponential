---
title: Das Kopfmenü und der Inhaltsbaum
audience: editor, website-creator, admin, designer
category: build-two-ways
order: 150
summary: Was Redakteure am Kopfmenü ändern, indem sie im Inhaltsbaum arbeiten (Text, Link und Platz des Eintrags), und was stattdessen die Menü-Einstellungen oder eine Änderung am Template braucht. Das mitgelieferte Menü ist eine feste Liste, keine Abfrage.
keywords: Kopfmenü, Inhaltsbaum, Menüeintrag umbenennen, Seite verschieben, Text des Menüeintrags, URL-Alias, Redakteur, feste Liste, dynamisches Menü, header menu, content tree, rename menu item, move page, menu item text, URL alias, editor, fixed list, dynamic menu
type: help
---
# Das Kopfmenü und der Inhaltsbaum

Das Kopfmenü des Themes Fit & Healthy ist eine **feste Liste** von Knoten-IDs (`menu.ini [SiteInfo] MainMenuID[]`), keine
Abfrage auf den Baum. Einen neuen Ordner unter der Wurzel der Site zu veröffentlichen, fügt ihn **nicht** zum Menü hinzu.
Worauf die Liste zeigt, sind jedoch Inhalte, und diesen Teil steuern Redakteure aus dem Inhaltsbaum.

## Inhalt

1. [Was Redakteure aus dem Baum ändern](#was-redakteure-aus-dem-baum-ändern)
2. [Was die Menü-Einstellungen braucht](#was-die-menü-einstellungen-braucht)
3. [Welche Seiten jetzt im Menü stehen](#welche-seiten-jetzt-im-menü-stehen)
4. [Ein Menü, das dem Baum folgt](#ein-menü-das-dem-baum-folgt)

## Was Redakteure aus dem Baum ändern

| Im Inhaltsbaum | Wirkung auf das Kopfmenü |
|---|---|
| Die Seite umbenennen (ihr Namensfeld) und veröffentlichen | Der Eintrag zeigt den neuen Namen: Das Menü gibt den Namen des Knotens aus |
| Die URL der Seite ändern (URL-Alias) | Der Eintrag verlinkt die neue URL: Das Menü gibt den URL-Alias des Knotens aus |
| Die Seite an einen anderen Platz verschieben | Der Eintrag bleibt und folgt der Seite: Eine Verschiebung behält die Knoten-ID |
| Die Seite in den Papierkorb schicken | Der Eintrag verschwindet: Das Template überspringt IDs, deren Knoten sich nicht holen lässt |

Das Veröffentlichen einer Seite leert die Seiten-Caches für sie. Wenn auf anderen Seiten noch ein alter Name erscheint,
leeren Sie im Kasten **Cache leeren** den Cache **Inhalte** ([Caches nach einer Änderung](caches-nach-einer-aenderung.md)).

## Was die Menü-Einstellungen braucht

| Aufgabe | Wo |
|---|---|
| Eine Seite zum Menü hinzufügen | [Einen Eintrag zum Kopfmenü hinzufügen](kopfmenue-eintrag-hinzufuegen.md) |
| Die Reihenfolge ändern | [Einträge des Kopfmenüs umordnen oder entfernen](kopfmenue-eintraege-umordnen-oder-entfernen.md) |
| Eine Seite aus dem Menü nehmen, die Seite aber behalten | Ihre ID aus `MainMenuID[]` entfernen |
| Ein anderes Menü pro Site oder Sprache | Jeder Siteaccess hat seine eigene `menu.ini`; `exp:ini where menu.ini/SiteInfo/MainMenuID <siteaccess>` |

## Welche Seiten jetzt im Menü stehen

Auf der Site Fit & Healthy (Siteaccess `site`) lautet die Liste:

| Knoten-ID | Seite | Inhaltsklasse | URL |
|---|---|---|---|
| 92 | Fitness | Landing page (`ng_landing_page`) | `/fitness` |
| 130 | Healthy eating | Landing page | `/healthy-eating` |
| 144 | Recipes | Category (`ng_category`) | `/recipes` |
| 163 | Running | Topic (`ng_topic`) | `/running` |
| 164 | Video | Landing page | `/video` |
| 26210 | Tour | Landing page | `/exponential-live` |

Alle sechs sind Kinder der Wurzel der Site, **Fit & Healthy** (Knoten 89). Welches Layout eine Seite bekommt, entscheiden
Layouts > Layout-Zuordnungen; Menü und Layouts sind voneinander unabhängig.

## Ein Menü, das dem Baum folgt

Ein Menü, das jeden neuen Bereich von selbst aufnimmt (zum Beispiel „die Kinder der Wurzel der Site mit den Klassen Landing
page, Category und Topic“), ist eine Änderung am **Template**: `pagelayout/header/main_menu.tpl` würde eine Liste von Kindern
holen, statt die `menu.ini` zu lesen. Das ist der klassische Weg über Templates und gehört in Ihre eigene Design-Erweiterung,
damit das Theme unberührt bleibt: [Ein Kopfmenü, das dem Baum folgt](kopfmenue-aus-dem-baum.md) hat den getesteten Code.
Der Layout-Editor kann eine solche Liste schon heute innerhalb einer Seite zeigen: ein **List**-Block mit einer dynamischen
Sammlung auf einen übergeordneten Knoten.

## Verwandte Seiten

- [Wie die Kopfzeile aufgebaut ist](wie-die-kopfzeile-aufgebaut-ist.md)
- [Eine Knoten-ID finden](knoten-id-finden.md)
- [Fehlerbehebung beim Kopfmenü](kopfmenue-fehlerbehebung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
