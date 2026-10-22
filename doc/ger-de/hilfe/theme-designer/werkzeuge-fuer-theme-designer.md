---
title: "Werkzeuge für Theme-Designer"
audience: theme-designer
category: theme-design
order: 34
summary: Die Werkzeuge, die ein Theme-Designer mit Exponential verwendet - die Einstellungen, die zeigen, welches Template was gezeichnet hat, die Debug-Ausgabe, gepackte oder ungepackte Stylesheets, der Vorschau-Siteaccess, der Template-Editor und der Assistent für Design-Erweiterungen in der Administration und die Browser-Werkzeuge, die den Rest erledigen.
keywords: Theme-Designer, Werkzeuge, Kommentare mit Template-Pfaden, ShowTemplatePathComments, Debug-Ausgabe, ShowUsedTemplates, Packer, DevelopmentMode, Vorschau-Siteaccess, Template-Editor, Design-Assistent, Entwicklerwerkzeuge des Browsers, theme designer, tools, template path comments, debug output, packer, preview siteaccess, template editor, design wizard, browser devtools
type: help
---
# Werkzeuge für Theme-Designer

Diese Seite ist für Theme-Designer, die wissen möchten, welche Werkzeuge es in Exponential und drumherum gibt und welche
Einstellungen ein Entwickler für sie einschaltet. Die meisten davon sind Entwicklungseinstellungen: Sie gehören auf einen
Entwicklungs- oder Vorschau-Siteaccess, nie auf die Live-Website. Jede Zeile verlinkt auf die Seite, die sie ausführlich
erklärt.

## Inhalt

1. [Sehen, welches Template was gezeichnet hat](#sehen-welches-template-was-gezeichnet-hat)
2. [Die Debug-Ausgabe](#die-debug-ausgabe)
3. [Gepackte oder ungepackte Stylesheets](#gepackte-oder-ungepackte-stylesheets)
4. [Ein Design neben dem Live-Design ausprobieren](#ein-design-neben-dem-live-design-ausprobieren)
5. [Werkzeuge in der Administration](#werkzeuge-in-der-administration)
6. [Browser-Werkzeuge](#browser-werkzeuge)
7. [Werkzeuge zur Qualitätsprüfung](#werkzeuge-zur-qualitätsprüfung)
8. [Welche Einstellung wohin gehört](#welche-einstellung-wohin-gehört)

## Sehen, welches Template was gezeichnet hat

| Einstellung (`site.ini`, `[TemplateSettings]`) | Was Sie sehen | Seite |
|---|---|---|
| `ShowTemplatePathComments=enabled` | jedes ausgegebene Template in Kommentare `<!-- START ... -->` und `<!-- STOP ... -->` mit seinem Pfad gehüllt; funktioniert bei eingeschaltetem Template-Compiler | [Kommentare mit Template-Pfaden](../designer/vorlagen/template-pfad-kommentare.md) |
| `Debug=enabled` mit `ShowXHTMLCode=enabled` | die älteren Debug-Markierungen der Templates in der Seite | [Template-Debugging](../designer/vorlagen/template-debugging.md) |
| `ShowUsedTemplates=enabled` (braucht die Debug-Ausgabe) | eine Tabelle aller für die Seite verwendeten Templates | [Ein Design debuggen](../designer/theme-design/ein-design-debuggen.md#welche-templates-wurden-überhaupt-verwendet) |

Die Einstellungsdatei warnt selbst, dass diese Einstellungen jeder Ausgabe Markup hinzufügen, auch E-Mails, JSON und
Feeds. Verwenden Sie sie nur auf einem Entwicklungs-Siteaccess.

## Die Debug-Ausgabe

`[DebugSettings] DebugOutput=enabled` fügt unter der Seite einen Bericht mit Fehlern, Warnungen, Zeitmessungen und, mit
der Einstellung oben, den verwendeten Templates hinzu. Er lässt sich auf bestimmte Adressen beschränken (`DebugByIP` mit
`DebugIPList[]`). Warnungen über fehlende Templates oder unbekannte Operatoren sind oft die Antwort auf "Warum erscheint
meine Änderung nicht?" ([Ein Design debuggen](../designer/theme-design/ein-design-debuggen.md#fehler-und-warnungen)).

## Gepackte oder ungepackte Stylesheets

Im normalen Betrieb packt ezjscore die Stylesheets und Skripte einer Seite in gecachte Dateien. Während der Arbeit an
einem Design sind ungepackte Dateien im Stil-Inspektor des Browsers leichter zu lesen. Der Packer folgt standardmäßig
`[TemplateSettings] DevelopmentMode` und lässt sich mit `[eZJSCore] Packer` in `ezjscore.ini` ausdrücklich setzen (laut
Kommentar in den Einstellungen nimmt er `enabled`, `disabled` oder eine Stufe von 0 bis 3 an). Nach einer Änderung am
Stylesheet muss auf einer Website mit eingeschaltetem Packen die gepackte Datei erneuert werden; das erledigt ein
Entwickler mit den Deploy-Schritten ([Der Packer-Cache und Deployments](../designer/theme-design/packer-cache-und-deployment.md)).
Die Namen gepackter Dateien tragen bereits die Änderungszeit; hängen Sie also nie `?v=` an einen Link.

## Ein Design neben dem Live-Design ausprobieren

Ein **Vorschau-Siteaccess** liefert dieselben Inhalte mit Ihrem Design unter einer eigenen Adresse aus, während Besucher
das Live-Design behalten. Er ist der richtige Ort für Reviews
([Ein Design auf einem Siteaccess in der Vorschau ansehen](../designer/theme-design/design-vorschau-auf-einem-siteaccess.md)).

## Werkzeuge in der Administration

| Werkzeug | Wo | Verwendung |
|---|---|---|
| Template-Editor | Reiter Design, Template-Editor | die Templates eines Siteaccess und ihre Overrides der Reihe nach sehen; schnelle Änderungen auf einer Testwebsite ([Der Template-Editor](../designer/vorlagen/der-template-editor.md)) |
| Assistent für Design-Erweiterungen | Setup > RAD, `/setup/designextension` | eine vollständige Theme-Erweiterung als Ausgangspunkt erzeugen ([Der Assistent für Design-Erweiterungen](../designer/erweiterungen/der-design-erweiterungs-assistent.md)) |
| Layout-Editor | Layouts | Testlayouts mit jedem Ansichtstyp von Blöcken bauen ([Der Layout-Editor](../layout-editor.md)) |
| Klassendefinitionen | Setup > Klassen | Felder für das [Zuordnungsblatt](ein-design-auf-inhaltsklassen-abbilden.md) ablesen |

Änderungen im Template-Editor existieren nur auf dieser Installation; ein Theme gehört in Dateien unter
Versionskontrolle.

## Browser-Werkzeuge

Die Entwicklerwerkzeuge des Browsers erledigen den größten Teil der visuellen Arbeit:

- den Element-Inspektor, mit sichtbaren Kommentaren mit Template-Pfaden im DOM;
- Geräteemulation bei 375 px und echten Zoom auf 200 % ([Bei verschiedenen Zoomstufen testen](../designer/theme-design/testen-bei-zoomstufen.md));
- Emulation des Farbschemas (hell und dunkel) und Emulation reduzierter Bewegung;
- Emulation von Farbsehschwächen;
- das Netzwerk-Panel für Anfragen an andere Hosts und die Performance-Prüfung für LCP und CLS
  ([Für Performance-Budgets gestalten](fuer-performance-budgets-gestalten.md#eine-seite-gegen-das-budget-prüfen));
- die Konsole, um Verstöße gegen die Content Security Policy zu sehen ([Checkliste: CSP](../designer/theme-design/checkliste-csp.md)).

## Werkzeuge zur Qualitätsprüfung

| Prüfung | Werkzeug | Seite |
|---|---|---|
| Kontrast | ein beliebiger Kontrastprüfer; die Verhältnisse im Palettenblatt notiert | [Farbkontrast](../designer/theme-design/farbkontrast.md) |
| Barrierefreiheit | der Barrierefreiheitsbaum des Browsers und ein automatischer Prüfer, dazu Tastatur und Screenreader von Hand | [Checkliste: Barrierefreiheit](../designer/theme-design/checkliste-barrierefreiheit.md) |
| Visuelle Änderungen zwischen Releases | Bildschirmfotos hell, dunkel und gezoomt, verglichen | [Visuelle Regressionsprüfungen](../designer/lebenszyklus/visuelle-regressionspruefungen.md) |
| Template-Syntax | der Befehl zur Template-Prüfung, den ein Entwickler ausführt | [Template-Debugging](../designer/vorlagen/template-debugging.md#syntaxprüfung) |
| Stilregeln der Administration | der Design-Test in der Testsuite | [Der Test der Admin-Stilregeln](../designer/theme-design/der-test-der-admin-gestaltungsregeln.md) |

## Welche Einstellung wohin gehört

Entwicklungseinstellungen gehören in die `site.ini.append.php` des Entwicklungs-Siteaccess oder in einen
Einstellungs-Override der Entwicklungsinstallation, gesetzt vom Entwickler oder Administrator. Sie gehören nie in Ihre
Theme-Erweiterung, denn ein Theme, das die Debug-Ausgabe einschaltet, schaltet sie für jede Website ein, die es
installiert ([Die Design-Einstellungen, die ein Theme mitliefert](../designer/erweiterungen/design-einstellungen-eines-themes.md)).

## Verwandte Seiten

- [Ein Design debuggen](../designer/theme-design/ein-design-debuggen.md)
- [Lektion 1.3: Welches Template hat dieses Markup geschrieben?](../trainer/theme-designer/modul-01-designs-und-die-fallback-kette/welches-template-dieses-markup-geschrieben-hat.md)
- [Caching für Designer](../designer/theme-design/caching-fuer-designer.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
