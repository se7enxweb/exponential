---
title: Einen Eintrag zum Kopfmenü hinzufügen
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 100
featured: true
summary: Eine Seite aus dem Inhaltsbaum zum Hauptmenü der Kopfzeile in Exponential 6.0.15+ hinzufügen, in der Administration (ohne Code) oder mit einem Konsolenbefehl, und sehen, warum der Layout-Editor die Menüeinträge nicht listet.
keywords: Kopfzeile, Hauptmenü, Menüeintrag, Navigation, MainMenuID, menu.ini, Layouts, gemeinsames Layout, Header / Footer, Template-Block, Seite zum Menü hinzufügen, exp:ini, header, main menu, menu item, navigation, layouts, shared layout, template block, add page to menu
type: help
---
# Einen Eintrag zum Kopfmenü hinzufügen

Diese Seite beantwortet eine Frage: **Wie bringe ich eine weitere Seite ins Kopfmenü?** Sie wurde auf der echten Site
Fit & Healthy ausgearbeitet, die mit Exponential 6.0.15+ ausgeliefert wird (das Theme der Media-Site, Erweiterung
`sevenx_themes_media`). Jedes Bildschirmfoto wurde auf einer Wegwerfkopie dieser Installation aufgenommen.

![Die Kopfzeile der Site Fit & Healthy mit den Menüeinträgen Fitness, Healthy eating, Recipes, Running, Video und Tour](../../../eng-us/help/build-two-ways/images/hm-01-header-before.webp)

## Inhalt

1. [Die kurze Antwort](#die-kurze-antwort)
2. [Warum der Layout-Editor die Menüeinträge nicht zeigt](#warum-der-layout-editor-die-menüeinträge-nicht-zeigt)
3. [Die drei Wege](#die-drei-wege)
4. [Nach der Änderung](#nach-der-änderung)

## Die kurze Antwort

Das Kopfmenü ist eine **Liste von Knoten-IDs** in der Einstellung `[SiteInfo] MainMenuID[]` der `menu.ini`, für den
Siteaccess `site`. Jede ID ist ein Menüeintrag; der Text des Eintrags ist der Name des Knotens und sein Link die URL des
Knotens. Um eine Seite hinzuzufügen:

1. Finden Sie die Knoten-ID der Seite ([Eine Knoten-ID finden](knoten-id-finden.md)).
2. Fügen Sie sie zu `MainMenuID[]` hinzu:
   - in der Administration: **Setup > INI-Einstellungen**, Datei `menu.ini`, Siteaccess `site`, **Bearbeiten** bei `MainMenuID[]`
     ([Schritt für Schritt](kopfmenue-in-der-administration.md)), oder
   - in einem Terminal: `./console exp:ini add menu.ini/SiteInfo/MainMenuID[] 192 siteaccess:site`
     ([Schritt für Schritt](kopfmenue-per-kommandozeile.md)).
3. Leeren Sie die Caches **Template & Inhalt** (das kompilierte Menü-Template enthält die Liste).

Das ist alles. „Showcase“ (Knoten 192) hinzuzufügen, brauchte in der Administration zehn Aktionen (Klicks, Auswahlen und eine
getippte Zeile) und auf der Konsole zwei Befehle ([Gemessene Schritte und Zeiten](gemessene-schritte-und-zeiten.md)).

![Dieselbe Kopfzeile nach der Änderung, mit einem siebten Eintrag, Showcase](../../../eng-us/help/build-two-ways/images/hm-06-header-after.webp)

## Warum der Layout-Editor die Menüeinträge nicht zeigt

Öffnen Sie **Layouts > Gemeinsame Layouts > Header / Footer**, und Sie sehen Blöcke, keine Menüeinträge: Die Zone header
enthält einen **Template block**, dessen Template-Name `header` ist.

![Das Layout Header / Footer im Editor: Die Zone header enthält einen Template-Block namens header und eine Spalte mit dem Template-Block des Brotkrumenpfads](../../../eng-us/help/build-two-ways/images/hm-08-layout-2-header-template-block.webp)

Ein Template-Block ist ein Platzhalter: Er sagt der Seite „stelle hier das Template namens `header` dar“. Das Menü in diesem
Template ist nicht im Layout gespeichert. Es kommt aus der `menu.ini`. Also:

| Sie wollen | Wo |
|---|---|
| Einträge des Kopfmenüs hinzufügen, entfernen oder umordnen | `menu.ini [SiteInfo] MainMenuID[]` (Administration: Setup > INI-Einstellungen; Konsole: `exp:ini`) |
| Einen Menüeintrag umbenennen | Die Seite (den Knoten) im Inhaltsbaum umbenennen; der Eintrag zeigt den Namen des Knotens |
| Das Markup der Kopfzeile ändern (Logo, Suche, Benutzermenü) | Die Templates, siehe [Wie die Kopfzeile aufgebaut ist](wie-die-kopfzeile-aufgebaut-ist.md) |
| Auf jeder Seite Blöcke über oder unter die Kopfzeile setzen | Der Layout-Editor, gemeinsames Layout **Header / Footer**, Zone `header` oder `post_header` |

Die vollständige Kette vom Layout-Block bis zum Menü-Template steht unter [Wie die Kopfzeile aufgebaut ist](wie-die-kopfzeile-aufgebaut-ist.md).

## Die drei Wege

| Weg | Werkzeug | Wer | Seite |
|---|---|---|---|
| Administration | Setup > INI-Einstellungen | Administrator, Website-Ersteller | [Das Kopfmenü in der Administration](kopfmenue-in-der-administration.md) |
| Kommandozeile | `./console exp:ini` | Administrator, Entwickler, Hosting | [Das Kopfmenü über die Kommandozeile](kopfmenue-per-kommandozeile.md) |
| Inhaltsbaum | Die Seite, deren ID gelistet ist, umbenennen, verschieben oder ersetzen | Redakteur | [Das Kopfmenü und der Inhaltsbaum](kopfmenue-und-inhaltsbaum.md) |

Die Reihenfolge der Einträge ist die Reihenfolge der IDs. Um einen Eintrag zu verschieben oder herauszunehmen, siehe
[Einträge des Kopfmenüs umordnen oder entfernen](kopfmenue-eintraege-umordnen-oder-entfernen.md).

## Nach der Änderung

Das Menü-Template wird mit der Liste der IDs darin kompiliert, ein geändertes `MainMenuID[]` erscheint also erst, nachdem der
Template-Cache geleert wurde. Wählen Sie in der Administration im Kasten **Cache leeren** **Template & Inhalt** und klicken Sie
auf **Leeren**; führen Sie in einem Terminal `./console exp:cache clear --tag=template,content --allow-root-user` aus. Das
leert auch die Seiten-Caches. Wenn der Eintrag immer noch nicht erscheint, arbeiten Sie
[Fehlerbehebung beim Kopfmenü](kopfmenue-fehlerbehebung.md) durch.

## Verwandte Seiten

- [Wie die Kopfzeile aufgebaut ist](wie-die-kopfzeile-aufgebaut-ist.md)
- [Die Einträge des Fußzeilenmenüs](eintraege-des-fusszeilenmenues.md)
- [Zwei Wege zum Bauen](zwei-wege-zum-bauen.md)
- [Der Layout-Editor](../layout-editor.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
