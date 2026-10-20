---
title: "Beispiele zum Livegang, alle Seiten"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1199
summary: Die 33 Beispiele der Livegangsphase im Leben einer Site, jedes eine Aufgabe, im Layout-Editor und mit Templates erledigt. Jedem Seitentyp ein Layout zuordnen, die Menüs füllen, die Seiten und die Caches prüfen, eine Ausgangsbasis sichern.
keywords: Livegang, Start, Beispiele, Lebenszyklus, Layouts, Templates, zwei Wege zum Bauen, launch, examples, life cycle, layouts, templates, two ways to build
type: help
---
# Beispiele zum Livegang, alle Seiten

Jedem Seitentyp ein Layout zuordnen, die Menüs füllen, die Seiten und die Caches prüfen, eine Ausgangsbasis sichern.

1. [Der Startseite ihr Layout geben](layout-der-startseite.md): Eine Zuordnung vom Typ Ort auf den Wurzelknoten der Site: auf Fit & Healthy Landing Page / Home auf Ort 89 mit Priorität 210.
2. [Jedem Artikel das Artikel-Layout geben](artikel-layout-fuer-jeden-artikel.md): Eine Teilbaum-Zuordnung mit einer Klassen-Bedingung: Full / Article auf Teilbaum 89 für ng_article, ng_blog_post, ng_news mit Priorität 130.
3. [Einer einzelnen Seite ein eigenes Layout geben](eigenes-layout-fuer-eine-seite.md): Eine Zuordnung vom Typ Ort (oder Pfad) mit hoher Priorität schlägt für eine Seite die Klassen-Zuordnung.
4. [Ein schlichtes Layout für die rechtlichen Seiten](schlichtes-layout-fuer-rechtliche-seiten.md): Privacy Policy und Cookie Policy sind Artikel, verwenden aber Full / Article (Plain): eine Zuordnung mit zwei Ort-Zielen und der Bedingung auf die Artikelklasse mit Priorität 150.
5. [Ein Layout für alle übrigen Seiten](layout-fuer-alle-uebrigen-seiten.md): Default Layout fängt den Rest auf: eine Zuordnung mit Pfadpräfix „/“ und Priorität 10 und [ResolverSettings] DefaultLayout als Rückfall.
6. [Prüfen, dass jedes Layout Kopf- und Fußzeile verknüpft](kopf-und-fusszeile-in-jedem-layout.md): Vor dem Livegang müssen die Zonen header und footer jedes Seitenlayouts mit Header / Footer verknüpft sein; exp:layouts list zeigt es.
7. [Das Kopfmenü für den Livegang füllen](kopfmenue-fuellen.md): Die endgültige Liste MainMenuID[] in einer Bearbeitung schreiben (Administration) oder mit clear plus add (Konsole).
8. [Beide Zeilen der Fußzeile für den Livegang füllen](fusszeile-fuellen.md): FooterMenuID[] für die erste Zeile, Menu items im Ordner Footer links für die zweite.
9. [Die Social-Media-Links setzen](social-media-links-setzen.md): Die Social-Media-Symbole der Fußzeile kommen aus den Feldern facebook, twitter, instagram und linkedin des Site-Info-Objekts; jedes Symbol erscheint nur, wenn sein Feld Inhalt hat.
10. [Den Prefooter für den Livegang füllen](prefooter-fuer-den-livegang.md): Die Startartikel des gemeinsamen Prefooters im Layout-Editor auswählen; er erscheint auf Artikeln und Rezepten.
11. [Jeden Seitentyp an seinem Markup prüfen](seitentypen-am-markup-pruefen.md): Layouts und Templates hinterlassen Markierungen im HTML (Zonen-Abschnitte, ngl-block-Klassen, IDs der Menüeinträge), die ein Skript vor dem Livegang prüfen kann.
12. [Die Caches vor dem Livegang einmal leeren](caches-vor-dem-livegang-leeren.md): Nach den letzten Änderungen an Templates und Einstellungen einmal Template & Inhalt leeren, dann die Hauptseiten vorwärmen.
13. [Beim Livegang einen Basis-Export machen](basis-export-beim-livegang.md): Am Tag des Livegangs jedes Layout, jedes gemeinsame Layout und jede Zuordnung exportieren, um später zu vergleichen und wiederherzustellen.
14. [Sicherstellen, dass kein Entwurf offen bleibt](keine-offenen-entwuerfe.md): Unveröffentlichte Entwürfe sind für Besucher unsichtbar, verwirren aber den nächsten Redakteur; listen Sie sie vor dem Livegang auf.
15. [Prüfen, dass Seiten aus dem Seiten-Cache kommen](seiten-cache-pruefen.md): Der Antwort-Header X-Exp-Cache meldet HIT, MISS oder MISS (purged); prüfen Sie die Hauptseiten nach dem Livegang.
16. [Das Kopfmenü einer zweiten Sprache](kopfmenue-einer-zweiten-sprache.md): Ein deutscher Siteaccess (auf dieser Installation bold_ger, starter_ger) hat seine eigene menu.ini; schreiben Sie seine Liste in seinem eigenen Geltungsbereich.
17. [Ein anderes Layout für eine Sprache](layout-pro-sprache.md): Eine Zuordnung mit einer Siteaccess-Bedingung gibt der deutschen Version eines Seitentyps ein eigenes Layout.
18. [Die Kontaktseite](die-kontaktseite.md): Contact (Knoten 185, Klasse Contact form) ist eine Formularseite; geben Sie ihr ein Layout mit einer Karte und Text um das Formular.
19. [Die Blog-Übersichtsseite live schalten](blog-uebersicht-live-schalten.md): Eine Landingpage mit einer dynamischen Liste von NG Blog posts, über Ort zugeordnet; die Beiträge verwenden bereits das Artikel-Layout.
20. [Der Layout-Teil einer Checkliste für den Livegang](checkliste-livegang-layouts.md): Zuordnungen, Verknüpfungen, Entwürfe, gemeinsame Layouts, Export: die fünf Prüfungen auf der Layout-Seite vor dem Livegang.
21. [Der Template-Teil einer Checkliste für den Livegang](checkliste-livegang-templates.md): Reihenfolge der Erweiterungen, Overrides, Styles, Menüs, Caches: die Prüfungen auf der Template-Seite vor dem Livegang.
22. [Testseiten aus dem Livegang heraushalten](testseiten-heraushalten.md): Test-Layouts und -Seiten, die während des Projekts entstanden sind: Zuordnung aufheben oder vor dem Livegang löschen.
23. [Eine Änderung am Kopfmenü in letzter Minute am Tag des Livegangs](kopfmenue-aenderung-am-tag-des-livegangs.md): Der schnellste sichere Weg: zuerst exp:ini add mit --dry-run, dann das Leeren des Cache; etwa zwei Befehle.
24. [Livegang auf Exponential Velocity](livegang-auf-velocity.md): Auf Velocity brauchen Änderungen an Einstellungen exp:velocity restart (wie exp:ini sagt); planen Sie es in die Schritte des Livegangs ein.
25. [Die Site an die Redakteure übergeben](uebergabe-an-die-redakteure.md): Zeigen Sie den Redakteuren die drei Orte, an denen sie arbeiten: den Inhaltsbaum, den Layout-Editor und den Ordner der Fußzeilen-Links.
26. [Die Landingpages der Bereiche live schalten](landingpages-der-bereiche.md): Fitness, Healthy eating, Running, Video und Showcase haben jeweils eine Zuordnung vom Typ Ort auf ihr eigenes Landingpage-Layout.
27. [Alte Adressen nach einem Relaunch](alte-adressen-nach-relaunch.md): URL-Aliase und Platzhalter sind Inhaltseinstellungen, keine Layouts; halten Sie alte Adressen funktionsfähig, wenn Seiten verschoben werden.
28. [Beim Livegang ein Analyse-Tag hinzufügen](analyse-tag-beim-livegang.md): Das Theme hat ein Include für Google Tag Manager (parts/google_tag_manager_code_script.tpl und _noscript.tpl); Messcode gehört in Templates, nicht in HTML-Snippet-Blöcke.
29. [Die Seiten des Livegangs messen](seiten-des-livegangs-messen.md): Messen Sie die Hauptseiten mit und ohne Cache, wie auf der Testkopie, und bewahren Sie die Zahlen auf.
30. [Ein Layout vor dem Livegang in der Vorschau ansehen](layout-vorschau-vor-dem-livegang.md): Layouts > Layouts und Gemeinsame Layouts haben für jedes Layout eine Vorschau; der mittlere Bereich des Editors zeigt eine Vorschau des Entwurfs.
31. [Eine Zuordnung vorbereiten und beim Livegang einschalten](zuordnung-vorbereiten-und-einschalten.md): Legen Sie die Zuordnung für den Livegang mit ausgeschaltetem Aktiviert an und haken Sie Aktiviert beim Livegang an.
32. [Eine zweite Site auf derselben Installation live schalten](zweite-site-live-schalten.md): Bold Agency und Starter laufen hier neben Fit & Healthy: eigene Wurzelknoten, Siteaccesse, Menüs und Layouts, mit Zuordnungen nach Teilbaum und Siteaccess.
33. [Den Redakteuren sagen, was sich beim Livegang geändert hat](aenderungen-beim-livegang-mitteilen.md): Listen Sie auf, was von Templates zu Layouts gewandert ist (oder umgekehrt), damit Redakteure wissen, wo sie nach dem Livegang etwas ändern.

## Verwandte Seiten

- [Beispiele zum Lebenszyklus](../../../../../eng-us/help/build-two-ways/examples/README.md)
- [Zwei Wege zum Bauen](../../../../../eng-us/help/build-two-ways/two-ways-to-build.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
