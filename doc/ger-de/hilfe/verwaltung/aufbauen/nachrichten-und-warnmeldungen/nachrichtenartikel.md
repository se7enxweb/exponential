---
title: Nachrichtenartikel
audience: government, website-creator, editor
category: public-sector
order: 20
summary: Wie Sie Nachrichten auf einer Exponential-Site des öffentlichen Sektors einrichten - die mitgelieferte Klasse Article und ihre Felder, ein nach Jahren geordneter Nachrichtenordner, Listen der neuesten Nachrichten mit einem Fetch, Veröffentlichungsdaten, Bilder mit Alternativtext und Themen mit Schlagworten.
keywords: Nachrichten, Klasse Article, Nachrichtenordner, neueste Nachrichten, Fetch, Veröffentlichungsdatum, Bilder, Alternativtext, Schlagworte, Archiv nach Jahr, news, article class, news folder, latest news, fetch, sort by published, publication date, images, alternative text, tags, archive by year
type: help
---
# Nachrichtenartikel

Diese Seite richtet sich an Site-Ersteller und Redakteure, die den Nachrichtenbereich einer Site des öffentlichen
Sektors einrichten und betreiben. Sie verwendet die mitgelieferte Klasse Article und zeigt, wie Nachrichten geordnet,
aufgelistet und gepflegt werden. Die Fakten zur Klasse stammen aus
[sevenx_classes](../../../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md) und der Klassenansicht des
Demonstrationsservers.

## Inhalt

1. [Die Klasse Article](#die-klasse-article)
2. [Nachrichten ordnen](#nachrichten-ordnen)
3. [Listen der neuesten Nachrichten](#listen-der-neuesten-nachrichten)
4. [Daten](#daten)
5. [Bilder und Barrierefreiheit](#bilder-und-barrierefreiheit)

## Die Klasse Article

Bei einer neuen Installation hat `article` die Definition der Basisdaten:

| Attribut | Bezeichner | Datentyp | Erforderlich |
|---|---|---|---|
| Title | `title` | Textzeile | ja |
| Short title | `short_title` | Textzeile | nein |
| Author | `author` | Autoren (`ezauthor`) | nein |
| Intro | `intro` | XML Block | ja |
| Body | `body` | XML Block | nein |
| Enable comments | `enable_comments` | Kontrollkästchen | nein |
| Image | `image` | Objekt Verknüpfung (zu einem Bildobjekt) | nein |

Nützliche Ergänzungen für Nachrichten des öffentlichen Sektors (fügen Sie sie unter **Setup > Klassen** hinzu): Tags
(`eztags`) für Themen und Metadaten (`xrowmetadata`) für Beschreibungen und Bilder zum Teilen
([Metadaten und Teilen in sozialen Netzwerken](metadaten-und-teilen-in-sozialen-netzwerken.md)). Schalten Sie **Enable comments** im
Template aus oder entfernen Sie es, wenn Sie Kommentare nicht moderieren.

## Nachrichten ordnen

```text
News              a folder with Display sub items on, sorted by publication date, newest first
├── 2026          a folder per year, created at the start of the year
│   └── <articles>
└── 2025
```

Jahresordner halten Listen in der Administration kurz und geben stabile Archivadressen. Legen Sie die Sortierung des
Ordners News in seinem Bearbeitungsformular fest (**Sortierung der untergeordneten Einträge**).

## Listen der neuesten Nachrichten

Auf der Startseite oder einer Themenseite listet ein Fetch die neuesten Artikel unter dem Ordner News:

```text
{def $news = fetch( 'content', 'tree', hash(
        'parent_node_id', $news_node_id,
        'class_filter_type', 'include',
        'class_filter_array', array( 'article' ),
        'sort_by', array( 'published', false() ),
        'limit', 5 ) )}
{foreach $news as $item}
  <article>
    <h3><a href={$item.url_alias|ezurl}>{$item.name|wash}</a></h3>
    <p><time>{$item.object.published|l10n( 'shortdate' )}</time></p>
    {attribute_view_gui attribute=$item.data_map.intro}
  </article>
{/foreach}
{undef $news}
```

Der Layout-Editor kann Listen von Inhalten auch ohne Template-Arbeit in Blöcken zeigen
([Der Layout-Editor](../../../layout-editor.md)). Listen in Cache-Blöcken brauchen einen Ablauf, damit neue Elemente
erscheinen ([View-Cache und Cache-Blöcke](../../../ersteller/website-ersteller/view-cache-und-cache-bloecke.md)).

## Daten

| Datum | Woher es kommt |
|---|---|
| Veröffentlicht | bei der ersten Veröffentlichung gesetzt; als Datum der Nachricht gezeigt |
| Geändert | jede neue Version |
| Ein korrigiertes Element | zeigen Sie „Aktualisiert am <Datum>“ aus der Änderungszeit, wenn Sie ein veröffentlichtes Element korrigieren |

Um zu einem späteren Zeitpunkt zu veröffentlichen, siehe [Pressemitteilungen](pressemitteilungen.md#sperrfrist).

## Bilder und Barrierefreiheit

- Schalten Sie für das Bildattribut der Klasse Image **Alternativtext ist erforderlich** ein
  ([Inhaltsmodell und Bearbeitung, 9](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#9-pflicht-alternativtext-gekürzte-textzeilen-und-von-php-erzeugte-namen)).
- Schreiben Sie den Alternativtext für das, was das Bild im Zusammenhang des Artikels zeigt; ein dekoratives Bild
  braucht keinen, sollte dann aber nicht im Feld Image stehen.
- Bildgrößen kommen aus Aliasen
  ([Bilder und Bildvarianten](../../../ersteller/website-ersteller/bilder-und-bildvarianten.md)).
- Fotos von Personen brauchen deren Einwilligung, wo Ihre Regeln es verlangen.

## Verwandte Seiten

- [Nachrichten und Warnmeldungen](README.md)
- [Pressemitteilungen](pressemitteilungen.md)
- [Inhalte anlegen und bearbeiten](../../../inhalte-bearbeiten.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
