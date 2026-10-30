---
title: "Mit echten Inhalten gestalten"
audience: theme-designer
category: theme-design
order: 7
summary: Warum und wie Theme-Designer mit den Inhalten zeichnen, die eine Exponential-Website wirklich enthält - woher Sie sie bekommen, wie Sie die Extreme messen, eine Reihe von Stressfällen und wie Sie ein Design auf einer Entwicklungsinstallation gegen Inhalte testen.
keywords: Theme-Designer, echte Inhalte, Lorem ipsum, Inhaltsprüfung, Textlänge, Stresstest, Extreme, Demo-Inhalte, Entwicklungsinstallation, Beispielinhalte, theme designer, real content, content audit, text length, stress test, extremes, demo content, development installation, sample content
type: help
---
# Mit echten Inhalten gestalten

Diese Seite ist für Theme-Designer, die möchten, dass ihre Mockups den Kontakt mit den Inhalten überstehen, die
Redakteure schreiben. Eine inhaltsverwaltete Website zeigt Texte und Bilder, die niemand im Designteam ausgewählt hat.
Platzhaltertext verbirgt genau die Probleme, die nach dem Start auftauchen: die dreizeilige Schlagzeile, das fehlende
Bild, das Wort mit 40 Zeichen im Deutschen.

## Inhalt

1. [Woher Sie echte Inhalte bekommen](#woher-sie-echte-inhalte-bekommen)
2. [Die Extreme messen](#die-extreme-messen)
3. [Die Stressfälle](#die-stressfälle)
4. [Echte Inhalte im Mockup](#echte-inhalte-im-mockup)
5. [Auf einer Entwicklungsinstallation testen](#auf-einer-entwicklungsinstallation-testen)
6. [Wenn es noch keine Inhalte gibt](#wenn-es-noch-keine-inhalte-gibt)

## Woher Sie echte Inhalte bekommen

| Situation | Quelle |
|---|---|
| Neugestaltung einer laufenden Website | Die Website selbst; eine Entwicklungskopie ihrer Datenbank und ihres Speichers, vom Entwickler vorbereitet |
| Neue Website, Inhalte werden geschrieben | Die Entwürfe der Redakteure, die alte Website, der Inhaltsplan |
| Ein Theme für viele Websites | Die Demo-Inhalte, die ein Theme mitliefert ([Demo-Inhalte für ein Theme](../designer/veroeffentlichung/demo-inhalte.md)), plus Ihre eigenen Stressfälle |

Bitten Sie den Entwickler um eine Entwicklungskopie, statt in der Live-Administration zu stöbern: Sie werden Inhalte
bearbeiten wollen, um Ihr Design zu testen, und das darf nie auf der Live-Website geschehen. Personenbezogene Daten in
einer Kopie (Benutzer, Formulareinsendungen, Abonnenten des Newsletters) werden für die Designarbeit nicht gebraucht und
sollten entfernt oder durch Adressen unter `example.invalid` ersetzt werden, bevor die Kopie Sie erreicht
([Kundendaten und Datenschutz](../designer/verkauf/kundendaten-und-datenschutz.md)).

## Die Extreme messen

Notieren Sie für jedes Feld in Ihrem [Zuordnungsblatt](ein-design-auf-inhaltsklassen-abbilden.md#das-zuordnungsblatt):

- den **kürzesten** realistischen Wert (ein Titel aus einem Wort, eine Einleitung aus einem Satz);
- den **typischen** Wert;
- den **längsten** realistischen Wert (der längste Titel im Archiv, eine Einleitung mit 900 Wörtern);
- wie oft es **leer** ist, wenn es optional ist.

Blättern Sie auf einer laufenden Website durch eine lange Liste, nach verschiedenen Kriterien sortiert, oder bitten Sie
den Entwickler um Zählungen aus der Entwicklungskopie. Tragen Sie die Zahlen in das Zuordnungsblatt ein; sie sind
Anforderungen an das Design.

## Die Stressfälle

Halten Sie eine kleine Menge von Testobjekten bereit, die jedes Design überstehen muss. Legen Sie sie auf der
Entwicklungsinstallation in einem Ordner an, den Redakteure nie sehen:

| Fall | Beispiel |
|---|---|
| Lange Schlagzeile | 140 Zeichen ohne Kurztitel |
| Langes einzelnes Wort | ein zusammengesetztes Wort oder eine URL in einem Titel |
| Kein Bild | jedes optionale Bild leer |
| Ungewöhnliches Bild | ein sehr hohes Hochformat und ein sehr breites Panorama |
| Formatierter Text mit allem | jede Überschriftenebene, verschachtelte Listen, eine Tabelle, eine Einbettung, ein Zitat, ein langer Link |
| Viele Autoren | fünf Autoren in einem Feld `ezauthor` |
| Andere Schriften | ein Titel von rechts nach links, ein Titel in einer Schrift mit hohen Zeichen ([Für viele Sprachen gestalten](fuer-viele-sprachen-gestalten.md)) |
| Leere Liste | ein Ordner ohne Unterelemente ([Leere Zustände und Fehlerzustände gestalten](leere-zustaende-und-fehlerzustaende-gestalten.md)) |
| Riesige Liste | ein Ordner mit Hunderten von Unterelementen, um die Seitennavigation zu sehen |

## Echte Inhalte im Mockup

Behalten Sie im Designtool echte Texte in den Komponenten, und duplizieren Sie jeden wichtigen Frame für den kürzesten
und den längsten Fall. Zwei Regeln lassen das funktionieren:

1. Textfelder wachsen; nichts wird abgeschnitten, es sei denn, das Design entscheidet sich dafür, und dann zeigt das
   Design, wie (eine Ellipse, ein Link "mehr").
2. Bilder werden mit echten, unvollkommenen Bildern in ihre Zuschnittsrahmen gesetzt, nicht mit passend ausgesuchten
   Stockfotos.

## Auf einer Entwicklungsinstallation testen

Sobald Templates existieren, ist der beste Test die Website selbst:

1. Öffnen Sie jedes Objekt der Stressfälle in seiner Vollansicht und den Ordner der Stressfälle in seiner Listenansicht.
2. Prüfen Sie bei 1440 px, bei 960 px mit 200 % Zoom und bei 375 px, hell und dunkel.
3. Setzen Sie die Stressobjekte in jeden Ansichtstyp von Layoutblöcken, wenn die Website Layouts verwendet.
4. Notieren Sie jeden Bruch mit einem Bildschirmfoto und dem Template, das ihn gezeichnet hat (die Kommentare mit
   Template-Pfaden sagen Ihnen, welches: [Kommentare mit Template-Pfaden](../designer/vorlagen/template-pfad-kommentare.md)).

Die [Checkliste zur Design-Qualitätssicherung](checkliste-design-qualitaetssicherung.md) wiederholt diese Schritte als Kästchen zum
Abhaken.

## Wenn es noch keine Inhalte gibt

Schreiben Sie Text in der Form von Inhalten, kein Lorem ipsum: Schlagzeilen, die wie Schlagzeilen klingen, in den
gemessenen Längen einer vergleichbaren Website. Vereinbaren Sie mit den Redakteuren Ziellängen, und setzen Sie diese
Ziele in das Bearbeitungsformular, wo die Klasse eine Beschreibung des Felds erlaubt, damit Redakteure sie beim
Schreiben sehen. Längenbegrenzungen für ein Feld sind eine Entscheidung des Entwicklers über das Inhaltsmodell
([Datentypen wählen](../cms-entwickler/datentypen-waehlen.md)).

## Verwandte Seiten

- [Für Redakteure gestalten](fuer-redakteure-gestalten.md)
- [Checkliste zur Design-Qualitätssicherung](checkliste-design-qualitaetssicherung.md)
- [Häufige Fehler in Templates](../designer/vorlagen/haeufige-template-fehler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
