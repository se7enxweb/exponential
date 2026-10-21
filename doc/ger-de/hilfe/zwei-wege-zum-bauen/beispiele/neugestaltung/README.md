---
title: "Beispiele zur Neugestaltung, alle Seiten"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1399
summary: "Die 34 Beispiele der Neugestaltungsphase im Leben einer Site, jedes eine Aufgabe, im Layout-Editor und mit Templates erledigt. Aussehen und Struktur ändern: Overrides, neue Ansichts- und Blocktypen, neue gemeinsame Zonen."
keywords: Neugestaltung, Redesign, Beispiele, Lebenszyklus, Layouts, Templates, zwei Wege zum Bauen, redesign, examples, life cycle, layouts, templates, two ways to build
type: help
---
# Beispiele zur Neugestaltung, alle Seiten

Aussehen und Struktur ändern: Overrides, neue Ansichts- und Blocktypen, neue gemeinsame Zonen.

1. [Neues Markup für jedes Raster](neues-markup-fuer-jedes-raster.md): Das Raster-Template für Listen des Media-Designs in Ihrer Design-Erweiterung überschreiben; jeder List-Block in der Rasteransicht ändert sich.
2. [Ein neues Aussehen für Abschnittstitel](abschnittstitel-neu-gestalten.md): Der Title-Block verteilt nach Ansichtstyp auf explayouts/block/title/<view type>.tpl; überschreiben Sie section_centered für den Stil des Prefooters.
3. [Das Markup der Kopfzeile neu bauen](neues-markup-der-kopfzeile.md): pagelayout/header.tpl (und seine Teile) in Ihrer Design-Erweiterung überschreiben; das gemeinsame Layout platziert es weiterhin.
4. [Das Markup der Fußzeile neu bauen](neues-markup-der-fusszeile.md): pagelayout/footer.tpl überschreiben; seine Datenquellen (FooterMenuID, Site-Info, Footer links) behalten oder ändern.
5. [Das feste Kopfmenü durch den Baum ersetzen](menue-aus-dem-baum.md): pagelayout/header/main_menu.tpl überschreiben, um die Kinder des Wurzelknotens nach Klasse zu listen; getesteter Code.
6. [Ein neues Aussehen für Listen, von Redakteuren gewählt](neues-aussehen-fuer-listen.md): Einen Ansichtstyp hinzufügen (eine INI-Zeile, ein Template), statt die vorhandenen zu ändern.
7. [Ein neuer Komponentenblock für die Neugestaltung](neuer-komponentenblock.md): Ein neuer Blocktyp für eine Komponente, die das alte Design nicht hatte, in der INI mit einem vorhandenen Handler definiert.
8. [Blöcke neu gestalten, ohne das Markup anzufassen](neu-gestalten-ohne-markup.md): Die meisten Neugestaltungen brauchen nur CSS für die Wrapper-Klassen ngl-<type>, ngl-vt-<view type>, bg-color-*, whitespace-*.
9. [Einen Seitentyp auf ein Layout mit Seitenleiste umstellen](layout-mit-seitenleiste.md): Der Typ eines Layouts lässt sich nicht an Ort und Stelle wechseln: ein Layout des neuen Typs anlegen, neu bauen, neu zuordnen.
10. [Blöcke in eine andere Zone verschieben](bloecke-zwischen-zonen-verschieben.md): Blöcke im Editor zwischen Zonen ziehen; zwischen Layouts neu bauen oder Export und Import verwenden.
11. [Ein neues gemeinsames Band für viele Seitentypen](neues-gemeinsames-band.md): Ein gemeinsames Layout (Single zone) für ein Band wie eine Newsletter-Anmeldung anlegen und eine Zone jedes Seitenlayouts damit verknüpfen.
12. [Den Prefooter auf mehr Seitentypen zeigen](prefooter-auf-mehr-seitentypen.md): Die Zone pre_footer weiterer Layouts mit der Zone main des Prefooters verknüpfen.
13. [Einem Seitentyp eine eigene Fußzeile geben](eigene-fusszeile-fuer-einen-seitentyp.md): Die Verknüpfung der Zone footer eines Layouts aufheben und dort eigene Blöcke bauen.
14. [Eine handverlesene Liste in eine sich selbst füllende umwandeln](manuell-zu-dynamisch.md): Den Sammlungstyp auf Dynamisch ändern; die manuellen Einträge werden entfernt (der Editor warnt), die Abfrage füllt die Liste.
15. [Eine mit Templates gebaute Seite in ein Layout umwandeln](template-seite-in-layout-umwandeln.md): Das Template der Seite in Blöcke aufteilen: wiederverwendbare Teile als Template-Blöcke, der Rest als Standardblöcke.
16. [Einen Block in ein Template einfrieren](block-in-template-einfrieren.md): Wenn sich der Inhalt eines Blocks nicht mehr ändert und nicht bearbeitet werden darf, sein Markup in einen Template-Block verschieben.
17. [Ein neues Aussehen für eine Klasse in Listen](neues-aussehen-einer-klasse-in-listen.md): content/views/<view>/<class>.tpl in Ihrer Erweiterung hinzufügen; Listen mit diesem Ansichtstyp der Elemente verwenden es für diese Klasse.
18. [Einen Ansichtstyp nach einer Neugestaltung ausmustern](ansichtstyp-ausmustern.md): Ihn erst aus ViewTypes[] entfernen, wenn kein Block ihn mehr verwendet; finden Sie diese in einem Export.
19. [Die Neugestaltung auf einer Kopie durchführen](neugestaltung-auf-einer-kopie.md): Templates in einem Branch Ihrer Erweiterung, Layouts in einer Sandbox-Kopie; dann die Templates einspielen und die Layouts importieren.
20. [Den Hero neu gestalten](hero-neu-gestalten.md): explayouts/block/hero.tpl aus dem Standard-Design von Layouts in Ihrer Design-Erweiterung überschreiben.
21. [Den Brotkrumenpfad ändern](brotkrumenpfad-aendern.md): Der Brotkrumenpfad ist ein Template-Block in der Zone header, dargestellt von pagelayout/breadcrumbs.tpl; überschreiben Sie es.
22. [Kein Brotkrumenpfad auf Landingpages](kein-brotkrumenpfad-auf-landingpages.md): Landingpage-Layouts verknüpfen die gemeinsame Kopfzeile samt Brotkrumenpfad; die Verknüpfung aufheben und neu bauen oder ihn per CSS auf diesen Layouts ausblenden.
23. [Die Sprachauswahl ändern](sprachauswahl-aendern.md): pagelayout/header/language_selector.tpl in Ihrer Design-Erweiterung überschreiben.
24. [Das Benutzermenü der Kopfzeile ändern](benutzermenue-aendern.md): Das Benutzermenü (Anmelden, Registrieren, Warenkorb, Administration ...) ist pagelayout/header/user_menu.tpl und menu.ini [HeaderUserMenu].
25. [Die Suche in der Kopfzeile ändern](suche-in-der-kopfzeile.md): pagelayout/header/search_box.tpl überschreiben; das Formular sendet an /content/search.
26. [Das Logo ändern](logo-aendern.md): Das Logo ist content/parts/site_logo.tpl und das Feld site_logo des Site-Info-Objekts.
27. [Die Landingpages im Editor neu gestalten](landingpages-im-editor-neu-gestalten.md): Landingpages sind Layouts: ihre Blöcke neu anordnen, Ansichtstypen und Ansichten der Einträge ändern, ohne Deployment.
28. [Ein anderer Hintergrund pro Layout](hintergrund-pro-layout.md): Eine Column (mit Hintergrundfarbe und Container) als ersten Block einer Zone setzen, um einem Layout einen eigenen Bandstil zu geben.
29. [Die Stilvarianten der Komponenten verwenden](stilvarianten-der-komponenten.md): Die Blöcke exp_component_* bringen Stil-Ansichtstypen mit (hero_style_1 bis 3, features_style_1 bis 7, about_style_1 bis 5, logos_style_1 und 2, lead_style_1 und 2, quote_style_1).
30. [Druckstile für Artikel](druckstile-fuer-artikel.md): Druckregeln gehören in Ihr Stylesheet (@media print), für die Klassen, die die Templates schreiben.
31. [Ein dunkles Farbschema für die Site](dunkles-farbschema.md): CSS mit prefers-color-scheme für die Klassen des Designs; keine Änderung an Templates oder Layouts.
32. [Eine Neugestaltung schneller machen](neugestaltung-schneller-machen.md): Weniger Blöcke pro Layout und Seiten aus dem Cache: vorher und nachher messen.
33. [Die Barrierefreiheit nach einer Neugestaltung prüfen](barrierefreiheit-pruefen.md): Überschriften, Alternativtexte, Kontrast, Tastatur: Templates und die von Redakteuren gefüllten Blöcke prüfen.
34. [Eine Neugestaltung Schritt für Schritt einführen](neugestaltung-schrittweise-einfuehren.md): Zuerst neue Templates hinter neuen Ansichtstypen, dann Blöcke Seite für Seite umstellen, dann die alten Ansichten ausmustern.

## Verwandte Seiten

- [Beispiele zum Lebenszyklus](../README.md)
- [Zwei Wege zum Bauen](../../zwei-wege-zum-bauen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
