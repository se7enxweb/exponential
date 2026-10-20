---
title: "Beispiele zur Planung, alle Seiten"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 999
summary: Die 35 Beispiele der Planungsphase im Leben einer Site, jedes eine Aufgabe, im Layout-Editor und mit Templates erledigt. Seitentypen, Layouts, Menüs, Sprachen und Zuständigkeiten vor dem Bauen festlegen.
keywords: planen, Planung, Beispiele, Lebenszyklus, Layouts, Templates, zwei Wege zum Bauen, plan, examples, life cycle, layouts, templates, two ways to build
type: help
---
# Beispiele zur Planung, alle Seiten

Seitentypen, Layouts, Menüs, Sprachen und Zuständigkeiten vor dem Bauen festlegen.

1. [Eine Bestandsaufnahme der Seitentypen machen](bestandsaufnahme-der-seitentypen.md): Vor dem Bauen auflisten, welche Arten von Seiten die Site hat und welches Layout jede bekommt: die Inhaltsklassen im Baum und die Layout-Zuordnungen.
2. [Den Layouttyp für einen Seitentyp wählen](layouttyp-waehlen.md): Der Layouttyp legt die Zonen fest: Header / Main / Footer (layout_2) für die meisten Seiten von Fit & Healthy, Header / Left / Right / Footer (layout_4) für eine Seitenleiste.
3. [Die gemeinsamen Zonen zuerst planen](gemeinsame-zonen-zuerst.md): Festlegen, welche Zonen alle Seiten teilen (Kopfzeile, Fußzeile) und welche einige teilen (Prefooter), bevor Seitenlayouts gebaut werden.
4. [Festlegen, wer was ändert](wer-was-aendert.md): Die Site vor dem Bauen zwischen Redakteuren (Inhalte und Layouts) und Entwicklern (Templates, Einstellungen) aufteilen, damit jede Änderung einen Verantwortlichen hat.
5. [Eine Seite in einem Entwurf als Prototyp bauen](prototyp-im-entwurf.md): Das Layout im Editor bauen, ansehen und mit Verwerfen wegwerfen: Nichts wird veröffentlicht, bis Sie auf Layout veröffentlichen klicken.
6. [Eine Seite mit Titeln und Abständen skizzieren](seite-skizzieren.md): Die Abschnitte einer neuen Seite mit Title-Blöcken und Spacern anlegen, bevor es die echten Inhalte gibt.
7. [Planen, welche Listen handverlesen sind und welche sich selbst füllen](manuell-oder-dynamisch.md): Für jede Liste vor dem Bauen manuelle Sammlung (Redakteure wählen aus) oder dynamische Sammlung (eine Abfrage) festlegen.
8. [Planen, wie Einträge in Listen aussehen](ansichtstypen-der-elemente.md): Ansichtstypen der Elemente (Standard, Overlay, Card, Line, Mini, List item, mit oder ohne Einleitung) bestimmen das Aussehen jedes Eintrags; planen Sie sie pro Liste.
9. [Kopf- und Fußzeilenmenü planen](menues-planen.md): Im Theme von Fit & Healthy sind das Kopfmenü und die erste Zeile der Fußzeile Listen in der menu.ini, die zweite Zeile der Fußzeile ist Inhalt; planen Sie, wer was pflegt.
10. [Eine zweite Sprache planen](zweite-sprache-planen.md): Jede Sprachversion ist ein Siteaccess mit eigenen Einstellungen (Menüs) und kann über eine Siteaccess-Bedingung eigene Layouts bekommen.
11. [Die Prioritäten der Zuordnungen planen](prioritaeten-der-zuordnungen.md): Die erste aktivierte Zuordnung nach Priorität, deren Ziele und Bedingungen passen, gewinnt; planen Sie die Prioritäten so, dass spezifische Zuordnungen zuerst kommen.
12. [Ihre Design-Erweiterung planen](design-erweiterung-planen.md): Eine Erweiterung für alles, was zur Site gehört: Templates, Stylesheets, Einstellungen, Übersetzungen, benannt, versioniert und an der richtigen Stelle aktiviert.
13. [Die Blocktypen auflisten, die Sie brauchen](benoetigte-blocktypen.md): Die Seitenentwürfe mit den Blocktypen vergleichen, die der Editor anbietet; neue Blocktypen nur für das planen, was fehlt.
14. [Planen, welchen Cache jede Änderung braucht](cache-plan.md): Pro Art von Änderung aufschreiben, welcher Cache zu leeren ist, damit Livegänge und Updates nicht mit „es erscheint nicht“ enden.
15. [Planen, wie Layouts aufbewahrt und versioniert werden](layouts-versionieren.md): Layouts liegen in der Datenbank; planen Sie regelmäßige Exporte (exp:layouts export), damit sie verglichen, wiederhergestellt und übertragen werden können.
16. [Bestandsaufnahme von Layouts und Einstellungen auf der Konsole](bestandsaufnahme-auf-der-konsole.md): exp:layouts list und exp:ini where geben ein vollständiges, skriptfähiges Bild der Layouts und Menü-Einstellungen einer Installation.
17. [Die Knoten-IDs für die Menüs sammeln](knoten-ids-fuer-menues.md): Menüs und manuelle Sammlungen verweisen auf Knoten-IDs; sammeln Sie sie (und ihre Remote-IDs), wenn der Baum gebaut wird.
18. [Kampagnen mit Start- und Enddatum planen](kampagnenkalender.md): Eine Zuordnung kann eine Zeit-Bedingung (from, to) haben; planen Sie Kampagnen-Layouts damit und wissen Sie, dass sich der Resolver Antworten bis zu einer Stunde merkt.
19. [A/B-Varianten einer Seite planen](ab-varianten.md): Die Bedingung Abfrageparameter gibt es, aber der Resolver merkt sich seine Antwort pro Pfad, sodass eine Adresse nicht zwei Layouts zugleich zeigen kann; planen Sie Varianten unter zwei Adressen.
20. [Barrierefreiheit in die Blöcke einplanen](barrierefreiheit-der-bloecke.md): Die Felder nutzen, die die Blöcke dafür haben (Image alt text, Heading level, Linktexte), und ein einziges h1 pro Seite behalten.
21. [Bildgrößen planen](bildgroessen-planen.md): Galerien nehmen einen Thumbnail size alias, Templates eine Bildklasse; planen Sie, welche Größen das Design braucht.
22. [Die Formulare planen](formulare-planen.md): Formulare sind auf Fit & Healthy Inhalte (Klassen Contact form, Feedback form, Lead form), gezeigt durch ihre Full Views; Layouts platzieren sie.
23. [Einen Blog-Bereich planen](blog-bereich.md): Blog-Beiträge (Klasse NG Blog post) teilen sich auf Fit & Healthy bereits die Artikel-Zuordnung; planen Sie die Übersichtsseite und ihre Listen.
24. [Einen Rezeptbereich planen](rezeptbereich.md): Rezepte haben ein eigenes Layout (Full / Recipe, layout_4 mit Seitenleiste) und eine eigene Zuordnung; planen Sie die Kategorieseite und ihre Listen.
25. [Themenseiten planen](themenseiten.md): Themen (Klasse Topic: Running, Workout, Health, HIIT) haben das Layout Full / Topic; planen Sie, was eine Themenseite listet.
26. [Einen Videobereich planen](videobereich.md): Videoinhalte (NG Video) liegen unterhalb von Video (Knoten 164) und bekommen Full / Video; planen Sie die Listen der Landingpage.
27. [Planen, woher Galerien kommen](herkunft-der-galerien.md): Gallery-Blöcke lesen Bilder aus einem Ordner; Galerie-Inhaltsobjekte (NG Gallery) sind eigene Geschichten. Planen Sie, was was ist.
28. [Die Übertragung von Layouts von Staging in die Produktion planen](von-staging-in-die-produktion.md): Auf einer Staging-Installation bauen, dann die Layouts exportieren und importieren; Inhalte werden über Remote-ID und Pfad zugeordnet.
29. [Eine Sandbox für riskante Änderungen planen](sandbox-fuer-riskante-aenderungen.md): Änderungen, die man erst nach dem Veröffentlichen sieht, auf einer Kopie der Installation ausprobieren, nie auf der Live-Site.
30. [Das Messen der Arbeit planen](arbeit-messen.md): Die Schritte zählen und die Wartezeiten typischer Änderungen messen, wie auf diesen Seiten, um die beiden Wege auf Ihrer eigenen Site zu vergleichen.
31. [Den Ordner der Fußzeilen-Links planen](ordner-der-fusszeilen-links.md): Die zweite Zeile der Fußzeile sind die Kinder eines Ordners (Remote-ID in menu.ini [SiteInfo] FooterLinksRemoteID); planen Sie seine Einträge und ihre Reihenfolge.
32. [Planen, wer Layouts veröffentlicht](wer-layouts-veroeffentlicht.md): Ein Layout wird in einem Entwurf geändert und als Ganzes veröffentlicht; planen Sie, wer gemeinsame Layouts veröffentlichen darf, da sie viele Seiten auf einmal ändern.
33. [Das Inhaltsmodell vor den Layouts planen](inhaltsmodell-zuerst.md): Layouts ordnen Inhalte an; die Klassen und Felder kommen zuerst. Prüfen Sie, was die vorhandenen Klassen bieten.
34. [Für Geschwindigkeit planen](fuer-geschwindigkeit-planen.md): Seiten aus dem Cache antworteten auf der Testkopie in 22 bis 102 ms, eine Seite nach dem vollständigen Leeren der Templates in etwa 3 s; planen Sie Livegänge und das Leeren von Caches mit diesem Wissen.
35. [Die eigene Dokumentation der Site planen](dokumentation-der-site.md): Die Layouts, Zuordnungen, Menüs und Templates der Site in derselben Struktur wie diese Seiten aufschreiben, damit die nächste Person sie ändern kann.

## Verwandte Seiten

- [Beispiele zum Lebenszyklus](../README.md)
- [Zwei Wege zum Bauen](../../../../../eng-us/help/build-two-ways/two-ways-to-build.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
