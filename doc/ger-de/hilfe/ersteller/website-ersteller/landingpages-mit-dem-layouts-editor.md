---
title: Landingpages mit dem Layouts-Editor
audience: website-creator, editor
category: website-creation
order: 280
summary: Landingpages ohne Templates bauen - die fünf Grundbegriffe des Layouts-Editors (Layout, Zone, Block, Collection, Regel), eine vollständige Kampagnenseite von Grund auf, Veröffentlichen und die Übergabe an die Redaktion.
keywords: Layouts-Editor, Landingpage, Kampagnenseite, Layout, Zone, Block, Collection, Regel, Seitenbaukasten, Hero, veröffentlichen, Website-Ersteller, layouts editor, landing page, campaign page, collection, rule, explayouts, page builder, hero, publish, website creator
type: help
---
# Landingpages mit dem Layouts-Editor

Diese Seite richtet sich an Website-Ersteller, die Landingpages, Kampagnenseiten und Startseiten bauen. Der
Layouts-Editor (die Erweiterungen `explayouts*`) ordnet Blöcke in Zonen eines Layouts an und ordnet das Layout über
Regeln Seiten zu, alles im Browser. Die Redaktion verwendet ihn täglich ([Der Layouts-Editor](../../layout-editor.md));
diese Seite zeigt die Sicht des Website-Erstellers. Die vollständige Referenz ist
[Exponential Layouts](../../../bc/6.0/layouts.md).

## Inhalt

1. [Die fünf Grundbegriffe](#die-fünf-grundbegriffe)
2. [Wo der Editor ist](#wo-der-editor-ist)
3. [Eine Kampagnenseite von Grund auf](#eine-kampagnenseite-von-grund-auf)
4. [Entwürfe und Veröffentlichen](#entwürfe-und-veröffentlichen)
5. [Wann Layouts und wann Templates](#wann-layouts-und-wann-templates)
6. [Übergabe an die Redaktion](#übergabe-an-die-redaktion)

## Die fünf Grundbegriffe

Aus [Die fünf Grundbegriffe, die Sie brauchen](../../../bc/6.0/layouts.md#die-fünf-ideen-die-sie-brauchen):

| Begriff | Bedeutung |
|---|---|
| **Layout** | eine benannte Anordnung auf Basis eines Layouttyps, der ihre Zonen festlegt; der Typ lässt sich später nicht ändern |
| **Zone** | ein benannter Bereich eines Layouts (`main`, `sidebar`, `footer`); befüllen Sie ihn oder verknüpfen Sie ihn mit einem gemeinsamen Layout |
| **Block** | ein Element in einer Zone: Text, Bild, eine Liste von Artikeln, ein Hero; er hat eine Definition, einen Ansichtstyp und Parameter |
| **Collection** | der Inhalt, den ein Listenblock zeigt: manuell (ausgewählt) oder dynamisch (eine Abfrage) |
| **Regel** | entscheidet, welche Anfragen welches Layout bekommen, anhand von Zielen und Bedingungen, höchste Priorität zuerst |

> Eine **Regel** entscheidet, dass diese Anfrage jenes **Layout** bekommt; die **Zonen** des Layouts enthalten
> **Blöcke**; ein Block kann eine **Collection** besitzen, die seinen Inhalt liefert.

## Wo der Editor ist

- Der Editor: `/explayouts_ui_api/app` in der Administration.
- Die formularbasierten Bildschirme (Regeln, gemeinsame Layouts, Import, Einrichtung): `/explayouts_ui/dashboard`.
- Rechte: die Richtlinien des Moduls `explayouts`; mindestens `explayouts/read`
  ([Rechte](../../../bc/6.0/layouts.md#berechtigungen)).

![Der Reiter Layouts: die Layouts der Website mit Typ, Status und den Seiten, die sie verwenden](../../../../eng-us/help/images/creators/layouts-dashboard.webp)

## Eine Kampagnenseite von Grund auf

1. **Die Inhaltsseite anlegen.** Legen Sie ein Landingpage-Objekt an (zum Beispiel der Klasse Landing page), dort, wo die
   Kampagne im Baum liegen soll, etwa `/Autumn-campaign`.
2. **Das Layout anlegen.** Im Editor **Layouts > Create**: Layouttyp `hero` (Zonen `top`, `left`, `main`, `right`),
   Bezeichner `autumn_campaign`, Name „Herbstkampagne“.
3. **Die Zonen befüllen.**
   - `top`: ein Block `hero` mit Titel, Text und einer Schaltfläche.
   - `main`: ein Block `list` mit dem Ansichtstyp `grid`, einer dynamischen Collection „Unterelemente des Ordners
     Products“, Limit 6.
   - `left` und `right`: je ein Block `rich_text` oder eine `card`.
4. **Zuordnen.** Unter **Mappings** eine Regel mit dem Ziel `node` (die Landingpage) und, für eine zeitlich begrenzte
   Kampagne, eine Bedingung `time` mit `from` und `to` ([Layout-Regeln](layout-regeln.md)).
5. **Preview**, dann **Publish**.
6. **Prüfen** Sie die Seite auf der Website, abgemeldet.

## Entwürfe und Veröffentlichen

Der Editor arbeitet immer an einem Entwurf; Besucher sehen nur veröffentlichte Layouts. Änderungen werden laufend im
Entwurf gespeichert; **Publish** macht sie live, **Discard draft** verwirft sie. Entwürfe gibt es pro Layout, nicht pro
Benutzer: Zwei Personen, die dasselbe Layout bearbeiten, überschreiben sich gegenseitig
([Entwürfe, Veröffentlichen und Versionen](../../../bc/6.0/layouts.md#entwürfe-veröffentlichen-und-versionen)).

## Wann Layouts und wann Templates

| Verwenden Sie den Layouts-Editor für | Verwenden Sie Templates für |
|---|---|
| Startseiten, Landing- und Kampagnenseiten, die sich oft ändern | die Full-Ansicht jeder Klasse |
| das Anordnen bestehender Inhalte in Abschnitten | Markup, das jede Seite teilt (das Pagelayout) |
| Seiten, die die Redaktion ohne Entwickler ändern muss | Logik: Formulare, Shop-Schritte, Mitgliederseiten |
| einen gemeinsamen Kopf- und Fußbereich | |

Ein Block `full_view` rendert das aktuelle Inhaltselement innerhalb eines Layouts, sodass ein Layout auch normale
Inhaltsseiten einrahmen kann. Ein `tpl_block` rendert ein Template, das ein Entwickler geschrieben hat, wenn kein
Blocktyp passt.

## Übergabe an die Redaktion

- [ ] Geben Sie der Redaktion Richtlinien für `explayouts` und nur die Layouts, die ihr gehören.
- [ ] Benennen Sie jeden Block eindeutig; vermerken Sie im Namen, warum ein Element angeheftet ist.
- [ ] Zeigen Sie ihr die Hilfeseite [Der Layouts-Editor](../../layout-editor.md) und die Tour in der Tour durch die
  Administration.
- [ ] Vereinbaren Sie, wer veröffentlicht und dass jeweils nur eine Person ein Layout bearbeitet.

## Verwandte Seiten

- [Layouttypen, Zonen und Blöcke](layouttypen-zonen-und-bloecke.md)
- [Layout-Regeln](layout-regeln.md)
- [Gemeinsamer Kopf- und Fußbereich](gemeinsamer-kopf-und-fussbereich.md)
- [explayouts_ui](../../../../eng-us/features/6.0/extensions/explayouts_ui.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
