---
title: "Mit Entwicklern arbeiten"
audience: theme-designer
category: theme-design
order: 32
summary: Wie Theme-Designer und Exponential-Entwickler die Arbeit an einem Theme aufteilen - wer wofür verantwortlich ist, ein gemeinsamer Wortschatz, Arbeit im Git-Repository des Themes, Anfragen nach Feldern und Einstellungen, Templates gegen Mockups prüfen und die Regeln, die beide Seiten einhalten.
keywords: Theme-Designer, Entwickler, Zusammenarbeit, Rollen, Übergabe, Git, Pull-Request, Review, Wortschatz, Anfragen, Theme-Erweiterung, Teamarbeit, theme designer, developers, collaboration, roles, handoff, pull request, review, vocabulary, requests, theme extension, teamwork
type: help
---
# Mit Entwicklern arbeiten

Diese Seite ist für Theme-Designer, die mit einem oder mehreren Exponential-Entwicklern an einem Theme arbeiten. Manche
Designer schreiben Templates und Stylesheets selbst; andere übergeben Designs und prüfen das Ergebnis. So oder so berührt
ein Theme das Inhaltsmodell, Einstellungen und Caches, also den Bereich des Entwicklers; eine klare Aufteilung der
Arbeit spart deshalb Zeit.

## Inhalt

1. [Wer wofür verantwortlich ist](#wer-wofür-verantwortlich-ist)
2. [Dieselben Worte sprechen](#dieselben-worte-sprechen)
3. [Im Repository des Themes arbeiten](#im-repository-des-themes-arbeiten)
4. [Anfragen an den Entwickler](#anfragen-an-den-entwickler)
5. [Templates gegen Mockups prüfen](#templates-gegen-mockups-prüfen)
6. [Regeln, die beide Seiten einhalten](#regeln-die-beide-seiten-einhalten)
7. [Wenn Sie uneins sind](#wenn-sie-uneins-sind)

## Wer wofür verantwortlich ist

| Bereich | Meist verantwortlich | Die andere Seite |
|---|---|---|
| Tokens, Komponenten, Mockups, Zustandsmatrix | Designer | prüft die Machbarkeit |
| Stylesheets | Designer oder Frontend-Entwickler | prüft Packen und Laden |
| Templates (TPL) | Frontend-Entwickler oder Designer, der TPL kennt | prüft gegen die Mockups |
| Inhaltsklassen und Felder | Entwickler | Designer fragt Felder an ([Ein Design auf Inhaltsklassen abbilden](ein-design-auf-inhaltsklassen-abbilden.md)) |
| Overrides, Einstellungen, Siteaccesses | Entwickler | Designer weiß, welche es gibt |
| Bild-Aliase | Entwickler schreibt sie | Designer legt sie fest ([Bildzuschnitte und Art Direction gestalten](bildzuschnitte-und-art-direction-gestalten.md)) |
| Caches, Deployments | Entwickler | Designer weiß, wann eine Änderung eines braucht ([Caching für Designer](../designer/theme-design/caching-fuer-designer.md)) |
| Blocktypen für Layouts | Entwickler | Designer legt Ansichtstypen fest ([Layoutblöcke entwerfen](layoutbloecke-gestalten.md)) |

Schreiben Sie diese Tabelle in der ersten Woche für Ihr Projekt auf; sie beendet die meisten Momente des "Ich dachte,
du machst das".

## Dieselben Worte sprechen

Verwenden Sie in Mockups, Tickets und Dateinamen die Begriffe des Produkts: Klasse, Feld (Attribut), Knoten,
Ansichtsmodus, Override, Pagelayout, Siteaccess, Design, Block, Ansichtstyp, Zone, Token. Nennen Sie die Karte überall
Karte: im Designtool, im [Komponenteninventar](komponenteninventar.md), im Template-Teil und in der CSS-Klasse. Das
[Glossar für Theme-Designer](glossar-fuer-theme-designer.md) und das [Design-Glossar](../designer/theme-design/design-glossar.md)
erklären die Begriffe.

## Im Repository des Themes arbeiten

Das Theme ist eine Design-Erweiterung unter Git ([Ein Theme unter Git stellen](../designer/erweiterungen/ein-theme-unter-git-stellen.md)).
Bewahren Sie Design-Artefakte, von denen der Code abhängt, neben dem Code auf:

- die Exporttabelle der Tokens und das Palettenblatt;
- das Komponenteninventar;
- das Zuordnungsblatt und die Ansichtsmatrix;
- das Bilderblatt und das Blatt für Blockdesigns.

Wenn Sie Stylesheets selbst ändern, arbeiten Sie auf einem Branch, halten Sie jede Änderung klein und beschreiben Sie,
was sich geändert hat und warum. Große binäre Designdateien liegen meist anderswo; verlinken Sie sie aus der
Dokumentation des Themes, statt sie zu committen. Committen Sie nie Einstellungs-Overrides, Zugangsdaten oder
Kundendaten.

## Anfragen an den Entwickler

Eine gute Anfrage ist kurz und vollständig:

- **Was:** "Ein Feld für einen Kurztitel bei Veranstaltung, auf Karten gezeigt."
- **Warum:** "Titel über 60 Zeichen brechen im Kartenraster auf vier Zeilen um."
- **Einzelheiten:** Datentyp, Pflicht oder optional, welche Ansichten, Design für leer.
- **Mockup:** ein Link auf den Frame.

Anfragen, die das Inhaltsmodell ändern, ändern auch die Arbeit der Redakteure; sie werden mit dem Kunden oder den
Redakteuren entschieden, nicht nur zwischen Designer und Entwickler.

## Templates gegen Mockups prüfen

Wenn jemand anderes die Templates schreibt, prüfen Sie jedes auf dem Entwicklungs- oder Vorschau-Siteaccess, nicht auf
Bildschirmfotos:

1. Öffnen Sie die Seite mit eingeschalteten Kommentaren mit Template-Pfaden, damit Sie in Ihren Anmerkungen das Template
   nennen können ([Kommentare mit Template-Pfaden](../designer/vorlagen/template-pfad-kommentare.md)).
2. Prüfen Sie es gegen das Mockup in drei Breiten, hell und dunkel, mit echten Inhalten und Stressfällen.
3. Prüfen Sie Tastaturfokus und Zustände.
4. Schreiben Sie jedes Problem mit der Seitenadresse, der Breite, dem Template und dem erwarteten Ergebnis auf.

Die [Checkliste für das Theme-Design-Review](checkliste-theme-design-review.md) listet auf, worauf Sie achten.

## Regeln, die beide Seiten einhalten

Diese stammen aus den Designregeln des Projekts und sind im Review nicht verhandelbar:

- keine `<style>`-Blöcke und keine `style=""`-Attribute in Templates; keine Inline-Skripte und keine `on*=`-Handler
  ([Kein Inline-CSS](../designer/theme-design/kein-inline-css.md), [Kein Inline-JavaScript](../designer/theme-design/kein-inline-javascript.md));
- Farben, Abstände, Radius und Schriften aus Tokens, jede Farbe mit einem dunklen Wert ([Design-Tokens](../designer/theme-design/design-tokens.md));
- jeder ausgegebene Wert gewaschen, jeder Oberflächentext übersetzbar ([Wash und Maskierung](../designer/vorlagen/wash-und-maskierung.md));
- mitgelieferte Designs werden nie bearbeitet, nur in Ihrer eigenen Erweiterung überschrieben;
- keine `?v=`-Versionsabfragen an Asset-Links;
- getestet bei 1440 px, bei 960 px mit 200 % Zoom und bei 375 px, hell und dunkel, mit der Tastatur.

Ein Design, das sich innerhalb dieser Regeln nicht bauen lässt, muss sich ändern, nicht die Regeln.

## Wenn Sie uneins sind

Uneinigkeit dreht sich meist um Kosten. Bitten Sie den Entwickler zu sagen, was eine Designentscheidung kostet
(Templates, Caches, Performance, Pflege), und bieten Sie Alternativen mit derselben Absicht an: einen Ansichtstyp statt
eines neuen Blocks, einen CSS-Zuschnitt statt eines neuen Bildfelds. Entscheiden Sie mit dem Kunden, wenn die Abwägung
seine ist.

## Verwandte Seiten

- [Vom Mockup zum Theme](vom-mockup-zum-theme.md)
- [Checkliste zur Übergabe](checkliste-zur-uebergabe.md)
- [Checkliste: Code-Review für Templates](../designer/theme-design/checkliste-code-review-fuer-templates.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
