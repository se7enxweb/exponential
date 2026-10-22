---
title: "Das Gästebuch: Besucher schreiben Inhalte"
audience: builder, admin
category: books
order: 150
summary: Ein Gästebuch als Klasse und Ordner: die Klasse für Einträge, das Anzeige-Template, die Schaltfläche und das Formular, mit denen Besucher einen Eintrag hinzufügen, die Richtlinie, die es erlaubt, und die Spamfrage, die zuerst zu klären ist.
keywords: Gästebuch, Inhaltsklasse, anonym, Richtlinie, Rolle, Bearbeitungstemplate, Formular-Token, Spam, guestbook, content class, anonymous, content/action, NewButton, ClassID, NodeID, policy, role, edit template, form token, spam, Building book
type: help
---
# Das Gästebuch: Besucher schreiben Inhalte

In einem Gästebuch fügen **Besucher** Inhalte hinzu, was kein anderer Teil der Clubwebsite tut. Für das System ist das nichts
Besonderes: Ein Eintrag ist ein Objekt einer Klasse, angelegt in einem Ordner von jemandem, dessen Rolle das erlaubt, hier
vom anonymen Besucher.

## Die Klasse und der Ordner

Legen Sie eine Klasse **Guestbook entry** an (Bezeichner `guestbook_entry`): ein **Name** (Textzeile, erforderlich), eine
**Message** (einfacher Text, erforderlich) und optional eine E-Mail-Adresse (aus der öffentlichen Ansicht herausgehalten).
Namensschema `<name>`. Legen Sie einen Ordner **Guestbook** in der zuvor angelegten Sektion Guestbook an
([Sektionen](sektionen-und-die-startseite.md)) und als Administrator ein oder zwei Einträge, damit es etwas zu zeigen
gibt. Das Buch zeigt den **Inhalt** der Seite als Tabelle aus Name, Datum und Nachricht.

## Die Einträge anzeigen

Eine Vollansicht für den Gästebuch-Ordner (Override auf seinem Knoten) listet die Unterelemente auf, die neuesten zuerst:

```
<h1>{$node.name|wash}</h1>
{foreach fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                         'class_filter_type', 'include',
                                         'class_filter_array', array( 'guestbook_entry' ),
                                         'sort_by', array( 'published', false() ),
                                         'limit', 20 ) ) as $entry}
    <article class="entry">
        <h2>{$entry.data_map.name.content|wash}</h2>
        <p class="date">{$entry.object.published|l10n( 'shortdate' )}</p>
        {$entry.data_map.message.content|wash|break}
    </article>
{/foreach}
```

(`wash` maskiert, was Besucher geschrieben haben, immer; `break` erhält die Zeilenumbrüche.) Das Buch zeigt im Template außerdem
mit `list_count` eine Meldung, wie viele Einträge es gibt.

## Die Schaltfläche, die einen neuen Eintrag beginnt

Eine Seite kann das Anlegen eines neuen Objekts mit einem Formular beginnen, das an `content/action` sendet. Das Formular
braucht den Schaltflächennamen `NewButton`, die Klasse (`ClassID`) und den übergeordneten Knoten (`NodeID`). Ein
mitgeliefertes Template enthält das genaue Markup (übernehmen Sie es von der Schaltfläche **Hier erstellen** einer
Standard-Ordneransicht, etwa `design/standard/templates/node/view/full.tpl`). In groben Zügen:

```
<form method="post" action={'content/action'|ezurl}>
    <input type="hidden" name="ClassID" value="{$class_id}" />
    <input type="hidden" name="NodeID" value="{$node.node_id}" />
    <input type="hidden" name="ContentLanguageCode" value="{ezini( 'RegionalSettings', 'ContentObjectLocale', 'site.ini' )}" />
    <input class="button" type="submit" name="NewButton" value="Add an entry" />
</form>
```

Formulare in Exponential tragen ein Formular-Token (die Erweiterung `ezformtoken` fügt es jedem Formular automatisch hinzu);
das gehört zu dem, was das alte Tutorial nicht hatte ([Sicherheit in Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/sicherheit-in-erweiterungen.md)).
Die Klassen-ID ist `$class_id`, geholt mit `fetch( 'content', 'class', hash( 'class_id', 'guestbook_entry' ) )`.

## Das Eingabeformular

Die Seite, auf der der Besucher landet, ist das Standard-Template **edit** mit allen Bearbeitungsfeldern. Für ein Gästebuch
soll es nur den Namen und die Nachricht zeigen. Überschreiben Sie das Bearbeitungstemplate für die Klasse
(`edit/guestbook_entry.tpl`, Quelle `content/edit.tpl`, `Match[class_identifier]=guestbook_entry`; beginnen Sie mit dem
Standard-`content/edit.tpl`) und behalten Sie die drei Schaltflächen bei (`PublishButton`, `DiscardButton`, `VersionEditButton`
wie dort). Lesen Sie vorher [Ansichtsmodi und Bearbeitungstemplates](../erweiterungsentwicklung/teil-2-builder/view-modi-fuer-builder.md);
ein kaputtes Bearbeitungstemplate verliert Einträge.

## Die Richtlinie

Die Rolle Anonymous muss es dürfen, nur für diese Klasse und diesen Ordner: `content/create`, beschränkt auf die Klasse
`guestbook_entry` und die **Sektion** Guestbook (oder den Knoten), dazu `content/edit` und `content/publish` für dasselbe,
damit der Besucher den Eintrag abschließen kann, den das System für ihn angelegt hat. Alles andere bleibt, wie es war. Siehe
[Rollen und Richtlinien](../buch-lernen/rollen-und-richtlinien-schritt-fuer-schritt.md). Testen Sie es als anonymer Besucher (in einem
privaten Fenster).

## Bevor Sie es für alle öffnen

Ein offenes Formular im öffentlichen Internet wird innerhalb weniger Tage von Spam-Robotern gefunden. Genau deshalb geht das
Buch weiter zu einem **Freigabe-Workflow**: [Gästebucheinträge freigeben](gaestebucheintraege-freigeben.md). Öffnen Sie die
Richtlinie erst, wenn der Workflow eingerichtet ist. Erwägen Sie außerdem eine Captcha-Erweiterung oder eine Ratenbegrenzung
am Webserver.

Weiter: [Gästebucheinträge freigeben](gaestebucheintraege-freigeben.md).

## Quelle und Lizenz

Bearbeitet nach den Kapiteln über das Gästebuch und die Freigabe des Tutorials *Building an eZ publish site* (geschrieben für
eZ publish 3.2, 2003), ursprünglich von ez.no, 2024 von 7x erneut unter der GNU Free Documentation License veröffentlicht,
[Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site). Copyright (c)
1999-2024 7x und Graham Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren
Version, ohne unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: für Exponential 6 neu geschrieben: aktuelle Workflow-Seiten, der Cronjob-Befehl, Formular-Token und eine Warnung vor Spam.
