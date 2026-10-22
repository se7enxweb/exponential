---
title: "Die Mitgliederseite: eine eigene Inhaltsklasse, eine Liste und eine Infoseite"
audience: builder, admin
category: books
order: 140
summary: Die Klasse Member anlegen, Mitglieder hinzufügen, eine Listenansicht für den Ordner Members und eine Vollansicht für ein Mitglied, dazu die Frage nach dem Datenschutz, die das Tutorial von 2003 nicht stellte.
keywords: eigene Klasse, Mitglied, Inhaltsklasse, Attribute, Liste, Zeilenansicht, Vollansicht, Override, Datenschutz, personenbezogene Daten, custom class, member, content class, attributes, list, line view, full view, override, privacy, personal data, Building book
type: help
---
# Die Mitgliederseite: eine eigene Inhaltsklasse, eine Liste und eine Infoseite

Der Club möchte eine Mitgliederliste, keine statische Seite. Die Mitglieder sind Daten, also werden sie zu Inhalt: ein Ordner
**Members** mit je einem Objekt **Member**, angezeigt als Liste und, nach einem Klick, als Seite eines Mitglieds. Keine der
mitgelieferten Klassen passt; das ist der übliche Fall und der Grund, weshalb es den Klasseneditor gibt
([eine Inhaltsklasse anlegen](../buch-lernen/eine-inhaltsklasse-anlegen.md)).

## Entscheiden, was gespeichert wird und was nicht

Die Klasse des Buches enthält einen echten Namen, einen Spitznamen, das Geburtsdatum, das Geschlecht, eine Telefonnummer, eine
E-Mail-Adresse und ein Porträtfoto. Bevor Sie das übernehmen, denken Sie über die Daten nach. Eine öffentliche Seite mit dem
Geburtsdatum und der Telefonnummer jedes Mitglieds ist heute ein Risiko für die Privatsphäre und in vielen Ländern auch ein
rechtliches: Speichern Sie nur, was das Mitglied zu veröffentlichen zugestimmt hat, und halten Sie Kontaktdaten aus der
öffentlichen Ansicht heraus (verwenden Sie ein Kontaktformular oder legen Sie sie in einem Attribut ab, das nur eine Rolle
lesen darf). Eine Klasse für die öffentliche Liste des Clubs braucht weniger:

| Attribut | Datentyp | Erforderlich |
|---|---|---|
| Echter Name (`name`) | Textzeile | ja |
| Spitzname (`nickname`) | Textzeile | nein |
| Wertungszahl (`rating`) | Ganzzahl | nein |
| Mitglied seit (`since`) | Datum | nein |
| Foto (`photo`) | Bild | nein |
| Über mich (`about`) | Formatierter Text | nein |

Legen Sie sie unter **Setup > Klassen** in der Gruppe **Content** an: **Neue Klasse**, Name `Member`, Bezeichner `member`,
Objekt-Namensschema `<name>`; fügen Sie dann die Attribute hinzu und setzen Sie für den Namen **Erforderlich**. Die Schritte
und die Bedeutung jedes Feldes finden Sie in [eine Inhaltsklasse anlegen](../buch-lernen/eine-inhaltsklasse-anlegen.md) und
[Inhaltsklassen für Website-Bauer](../erweiterungsentwicklung/teil-2-builder/inhaltsklassen-fuer-builder.md); die Datentypen
beschreibt [der Rundgang durch die Datentypen](../buch-lernen/rundgang-durch-die-datentypen.md).

## Mitglieder hinzufügen

Legen Sie an der Wurzel einen Ordner **Members** an und darin dann Objekte **Member** (**Hier erstellen** im Ordner). Die
Rolle, mit der die Redakteure des Clubs das tun dürfen, ist eine Richtlinie `content/create`, beschränkt auf die Klasse
`member` und den Ordner Members ([Rollen und Richtlinien](../buch-lernen/rollen-und-richtlinien-schritt-fuer-schritt.md)).

## Die Liste

Der Ordner Members zeigt eine Liste seiner Unterelemente, jedes ein Link mit dem Namen des Mitglieds (die Ansicht **line**
der Klasse):

```ini
[full_members_folder]
Source=node/view/full.tpl
MatchFile=full/members_folder.tpl
Subdir=templates
Match[node]=47
```

```
{* full/members_folder.tpl *}
<h1>{$node.name|wash}</h1>
<ul class="member-list">
{foreach fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                         'class_filter_type', 'include',
                                         'class_filter_array', array( 'member' ),
                                         'sort_by', array( 'name', true() ) ) ) as $member}
    <li><a href={$member.url_alias|ezurl}>{$member.name|wash}</a>
        {if $member.data_map.nickname.has_content}({$member.data_map.nickname.content|wash}){/if}</li>
{/foreach}
</ul>
```

(47 steht für die Knoten-ID Ihres Ordners Members.) Für eine lange Liste fügen Sie `limit` und den Navigator von
[den Nachrichtenseiten](die-nachrichtenseiten.md) hinzu.

## Die Mitgliederseite

Eine Vollansicht für die Klasse, `Match[class_identifier]=member`, Quelle `node/view/full.tpl`, Datei `full/member.tpl`:

```
<h1>{$node.name|wash}</h1>
{if $node.data_map.photo.has_content}
    {attribute_view_gui attribute=$node.data_map.photo image_class='medium'}
{/if}
<dl>
    {if $node.data_map.rating.has_content}<dt>Rating</dt><dd>{$node.data_map.rating.content}</dd>{/if}
    {if $node.data_map.since.has_content}<dt>Member since</dt><dd>{$node.data_map.since.content.timestamp|l10n( 'shortdate' )}</dd>{/if}
</dl>
{attribute_view_gui attribute=$node.data_map.about}
```

Testen Sie, indem Sie aus der Liste heraus klicken. `has_content` prüft, ob etwas eingegeben wurde; was `.content` bei jedem
Datentyp enthält, steht in der [Attributreferenz](../template-sprache/zugriff-auf-attribute.md).

Weiter: [das Gästebuch](das-gaestebuch.md).

## Quelle und Lizenz

Bearbeitet nach dem Kapitel über die Mitgliederseite des Tutorials *Building an eZ publish site* (geschrieben für eZ publish
3.2, 2003), ursprünglich von ez.no, 2024 von 7x erneut unter der GNU Free Documentation License veröffentlicht,
[Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site). Copyright (c)
1999-2024 7x und Graham Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren
Version, ohne unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: für Exponential 6 neu geschrieben, mit einem Hinweis zum Datenschutz und einer kleineren Mitgliederklasse.
