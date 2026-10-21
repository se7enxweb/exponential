---
title: Bekanntes Verhalten des Editors
audience: editor, website-creator, admin, designer, developer
category: build-two-ways
order: 280
summary: Verhalten des Layout-Editors und seiner Werkzeuge, das beim Schreiben dieser Seiten auf Exponential 6.0.15+ beobachtet wurde, jeweils mit dem Beobachteten und dem Umgang damit; Änderungen des Ansichtstyps, Positionen von Einträgen, die Suche im Content Browser, Template-Blöcke in Kopf- und Fußzeile sowie das seit explayouts_core 1.0.10 und explayouts 1.4.42 behobene Verhalten von Importen und Hilfsbefehlen.
keywords: bekannte Probleme, Verhalten, Layout-Editor, Ansichtstyp nicht gespeichert, js-skip-on-change, Position von Einträgen, Suche im Content Browser, Import-Cache, block_list, layout_info, collection_list, Umgehung, known issues, behaviors, layouts editor, view type not saved, collection item position, content browser search, import cache, workaround
type: help
---
# Bekanntes Verhalten des Editors

Jeder Eintrag wurde beim Schreiben dieser Hilfe auf einer Kopie einer Live-Installation beobachtet. Sie werden genannt,
damit Sie wissen, was Sie erwartet; prüfen Sie die Release Notes Ihrer Version, denn dort können sie behoben sein.

## Eine Änderung nur des Ansichtstyps wird nicht gespeichert

**Beobachtet:** Im Reiter **Design** änderte die Auswahl eines anderen **Ansichtstyps** das Formular, aber der Entwurf
behielt den alten Ansichtstyp; nach **Layout veröffentlichen** war der Block unverändert. Die Auswahlliste trägt die
Klasse `js-skip-on-change`, mit der das Skript des Editors das Speichern bei dieser Änderung überspringt.

**Umgang damit:** Ändern Sie nach der Auswahl des Ansichtstyps ein beliebiges anderes Feld desselben Formulars (tippen
und löschen Sie ein Zeichen in **CSS class**): Dann wird das ganze Formular gespeichert, samt Ansichtstyp. Der Blockkopf
zeigt anschließend den neuen Ansichtstyp in Großbuchstaben.

## Zwei Einträge mit Position 0

**Beobachtet:** Nachdem **+ Elemente hinzufügen** einer manuellen Sammlung mit drei Artikeln einen vierten hinzugefügt
hatte, hatte die veröffentlichte Sammlung die Positionen 0, 0, 1, 2 (der neue Eintrag und der erste beide 0). Die Seite
zeigte alle vier.

**Umgang damit:** Wenn die Reihenfolge auf der Site nicht der im Editor entspricht, ziehen Sie die Einträge vor dem
Veröffentlichen noch einmal in die gewünschte Reihenfolge.

## Die Suche im Content Browser

**Beobachtet:** In **Inhalt zur Sammlung hinzufügen** lieferte das Suchfeld für das ausprobierte Wort „Kein Inhalt
gefunden.“, während das Durchblättern mit **Öffnen** die Artikel fand.

**Umgang damit:** Blättern Sie durch den Baum; die Suche hängt von der Sucheinrichtung der Installation ab.

## Ein Import auf der Kommandozeile leert den Seiten-Cache

**Behoben:** Seit explayouts_core 1.0.10 leert ein `exp:layouts import`, der ein Layout veröffentlicht (`--publish`)
oder eine Regel schreibt, selbst jede Seite des Seiten-Cache, auch auf der Kommandozeile, so wie das Veröffentlichen im
Editor. Den Inhalts-Cache nach einem Import auf der Kommandozeile von Hand zu leeren ist nicht mehr nötig. Entwürfe,
Probeläufe und abgelehnte Importe leeren nichts.

## Die Hilfsbefehle von explayouts laufen eigenständig

**Behoben:** Seit explayouts 1.4.42 laufen `./console ext:explayouts:block_list <layout_id>`, `layout_info <id>`,
`collection_list <block_id>`, `layout_copy`, `layout_list`, `rule_list <layout_id>` und `share_create` (und die anderen
Helfer in `extension/explayouts/bin/php`) eigenständig; sie enden nicht mehr mit `Class "..." not found`.
`-s <siteaccess>` wählt den Siteaccess. `./console ext:explayouts:layout_info 2` gibt unter anderem aus:

```text
ID:          2
Identifier:  fd0aa7af77a85a03bf1080018f5db1dc
Name:        Header / Footer
```

`exp:layouts` bleibt das Werkzeug für Export und Import.

## Template-Blöcke in den Zonen header und footer

**Beobachtet:** Im Media-Theme stellt ein Template-Block an der Wurzel einer Zone `header` die Kopfzeile des Themes dar
und einer in einer Zone `footer` die Fußzeile des Themes, unabhängig von seinem Template-Namen, weil das Theme
Template-Blöcke nach ihrem Platz zuordnet.

**Umgang damit:** Setzen Sie Ihre eigenen Template-Blöcke in andere Zonen ([Eigene Template-Blöcke](eigene-template-bloecke.md)).

## Verwandte Seiten

- [Der Layout-Editor, ein Rundgang](rundgang-durch-den-layout-editor.md)
- [Warum erscheint meine Änderung nicht?](warum-erscheint-meine-aenderung-nicht.md)
- [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
