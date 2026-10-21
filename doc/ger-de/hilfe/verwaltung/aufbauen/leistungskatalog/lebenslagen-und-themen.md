---
title: Lebenslagen und Themen
audience: government, website-creator, project-manager, editor
category: public-sector
order: 40
summary: Wie Sie Leistungen in Exponential mit eztags nach Thema und nach Lebenslage ordnen - ein Schlagwortbaum, Synonyme, geschlossene Vokabulare, Schlagwortseiten und Listen - damit eine Leistung überall erscheint, wo Menschen sie suchen.
keywords: Lebenslagen, Themen, Taxonomie, Schlagwortbaum, Synonyme, geschlossenes Vokabular, Schlagwortseiten, Leistungskatalog, Navigation, life events, topics, taxonomy, eztags, tag tree, synonyms, closed vocabulary, tag pages, tags view, service catalog, navigation
type: help
---
# Lebenslagen und Themen

Diese Seite richtet sich an Site-Ersteller, Projektleiter und Redakteure, die Leistungen für Menschen ordnen, die nicht
wissen, welche Abteilung sie anbietet. Menschen denken in Themen („Wohnen“, „Verkehr“) und in Lebenslagen („ein Kind
bekommen“, „umziehen“, „ein Unternehmen gründen“, „wenn jemand stirbt“). Mit Schlagworten erscheint eine Leistung unter
jedem Thema und jeder Lebenslage, zu der sie gehört, während sie an einer Stelle im Baum liegt. Die Erweiterung für
Schlagworte ist in [eztags](../../../../../eng-us/features/6.0/extensions/eztags.md) und
[Verschlagworten mit eztags](../../../../../eng-us/help/creators/website-creators/tagging-with-eztags.md) dokumentiert.

## Inhalt

1. [Zwei Schlagwortbäume](#zwei-schlagwortbäume)
2. [Die Bäume aufbauen](#die-bäume-aufbauen)
3. [Leistungen verschlagworten](#leistungen-verschlagworten)
4. [Themen und Lebenslagen zeigen](#themen-und-lebenslagen-zeigen)
5. [Das Vokabular steuern](#das-vokabular-steuern)

## Zwei Schlagwortbäume

| Baum | Beispielstruktur | Zweck |
|---|---|---|
| Themen | Wohnen > Sozialer Wohnungsbau, Wohnen > Baugenehmigungen; Verkehr > Parken | worum es bei der Leistung geht |
| Lebenslagen | Ein Kind bekommen; Umziehen; Schulanfang; Ein Unternehmen gründen; In Rente gehen; Wenn jemand stirbt | die Situation, in der sich die Person befindet |

Halten Sie die Lebenslagen in einer kurzen Liste (oft zehn bis zwanzig); jede ist eine Seite, die jede in dieser
Situation nötige Leistung in sinnvoller Reihenfolge sammelt.

## Die Bäume aufbauen

1. Öffnen Sie in der Administration **Tags** (`/tags/dashboard`).
2. Legen Sie ein oberstes Schlagwort „Themen“ und eines „Lebenslagen“ an; fügen Sie darunter jeweils untergeordnete
   Schlagworte hinzu.
3. Fügen Sie **Synonyme** für die Wörter hinzu, die Menschen verwenden: „Müll“, „Kehricht“ und „Unrat“ als Synonyme des
   Themas „Abfall“. Synonyme zeigen auf das Hauptschlagwort, sodass Suche und Schlagwortseiten sie als eines behandeln.
4. Übersetzen Sie Schlagworte, wenn die Site mehrsprachig ist: Schlagworte haben eigene Übersetzungen.

Das Dashboard **Tags** und die Funktionen zum Hinzufügen, Bearbeiten, Verschieben und Zusammenführen gehören zu eztags;
die Richtlinien liegen im Modul `tags`
([Verschlagworten mit eztags](../../../../../eng-us/help/creators/website-creators/tagging-with-eztags.md#permissions)).

## Leistungen verschlagworten

1. In der Klasse Service verwendet das Attribut **Themen und Lebenslagen** den Datentyp Tags. Wählen Sie in seinen
   Klasseneinstellungen die **Edit view** (Default, Select oder Tree) und setzen Sie **Limit by tags subtree** auf ein
   Wurzelschlagwort; ein einzelnes Attribut nimmt einen Teilbaum, legen Sie also entweder „Themen“ und „Lebenslagen“
   unter ein Wurzelschlagwort oder verwenden Sie zwei Tags-Attribute, eines je Baum, wenn Sie ein geschlossenes
   Vokabular möchten.
2. Redakteure tippen in das Feld; die Autovervollständigung schlägt vorhandene Schlagworte vor.
3. Entscheiden Sie, ob Redakteure neue Schlagworte anlegen dürfen (offenes Vokabular) oder nur wählen (geschlossen). Für
   einen Leistungskatalog ist geschlossen meist besser: Das Webteam fügt Schlagworte auf Anfrage hinzu.

## Themen und Lebenslagen zeigen

| Seite | Wie |
|---|---|
| Eine Themenseite | die Schlagwortseite `/tags/view/<tag>` listet alles Verschlagwortete, mit Ihrem Template |
| Eine Lebenslagenseite | ein Article oder Folder, für die Lebenslage geschrieben (eine Einleitung, die Reihenfolge der Schritte), dazu eine Liste der damit verschlagworteten Leistungen |
| Auf jeder Leistungsseite | die Schlagworte als Links (`attribute_view_gui` am Schlagwortattribut) |
| „Beliebte Themen“ auf der Startseite | eine feste Liste von Schlagwortlinks, vom Webteam gewählt |

eztags fügt den Fetches `content/list` und `content/tree` einen erweiterten Attributfilter hinzu, sodass ein Template
„die mit Umziehen verschlagworteten Leistungen“ listen kann; die Syntax des Filters steht in der eigenen Dokumentation
der Erweiterung in `extension/eztags/doc/`.

## Das Vokabular steuern

| Regel | Warum |
|---|---|
| Eine verantwortliche Person für die Schlagwortbäume | Einheitlichkeit |
| Dubletten zusammenführen statt beide zu behalten | eztags hat in der Verwaltung der Schlagworte eine Funktion zum Zusammenführen |
| Die Wörter der Einwohner für Hauptschlagworte, amtliche Begriffe als Synonyme | Auffindbarkeit |
| Schlagworte ohne Inhalte jährlich überprüfen | eine Schlagwortseite ohne Leistungen ist eine Sackgasse |

## Verwandte Seiten

- [Einen Leistungskatalog aufbauen](README.md)
- [Leistungen finden](leistungen-finden.md)
- [Den Inhaltsbaum strukturieren](../../../../../eng-us/help/creators/website-creators/structuring-the-content-tree.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
