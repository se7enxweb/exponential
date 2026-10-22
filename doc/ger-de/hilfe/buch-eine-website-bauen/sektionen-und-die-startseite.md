---
title: "Sektionen und die Startseite"
audience: builder, editor
category: books
order: 120
summary: Die ersten Ordner und Sektionen der Clubwebsite anlegen, den Begrüßungsartikel schreiben, ihn mit IndexPage zur Startseite machen und ihm mit einer Override-Regel ein eigenes Template geben.
keywords: Sektion, Ordner, Artikel, Startseite, Begrüßungsseite, Template-Override, section, folder, article, IndexPage, welcome page, front page, template override, Match node, override.ini, Building book
type: help
---
# Sektionen und die Startseite

## Sektionen

Die Clubwebsite hat fünf Teile: die Startseite, Neuigkeiten, Mitglieder, das Gästebuch und Links. Geben Sie dem
**Gästebuch** eine eigene Sektion: Später werden der Freigabe-Workflow und das Recht der Besucher, Einträge zu schreiben, auf
sie beschränkt, und nichts anderes auf der Website soll davon betroffen sein. Legen Sie die Sektion an (**Setup > Sektionen**,
Name `Guestbook`) und weisen Sie sie dem Gästebuch-Ordner zu, sobald Sie ihn angelegt haben. Wie ein neues Unterelement die
Sektion seines übergeordneten Elements erbt, zeigen [Sektionen](../../anleitungen/sektionen.md) und
[der Knotenbaum und die Sektionen](../buch-lernen/der-knotenbaum-und-die-sektionen.md).

## Der Begrüßungsartikel

Legen Sie den Inhalt in der Administration an, wie es ein Redakteur tun würde. In der **Inhaltsstruktur** an der Wurzel:

1. Wählen Sie in der Liste **Hier erstellen** die Klasse **Article** und klicken Sie auf die Schaltfläche.
2. Füllen Sie **Title** („Welcome to the TSCM website!“), **Intro** und **Body** aus; die Klasse Article von Exponential 6
   hat außerdem einen Kurztitel, einen Autor, einen Schalter für Kommentare und ein **Image**, das auf ein Bildobjekt
   verweist.
3. Fügen Sie ein Bild hinzu: Das Bild ist ein eigenes Objekt im Baum **Medien** (laden Sie es dort hoch und wählen Sie es
   dann im Feld **Image** aus), siehe [Bilder einfügen und hochladen](../medien-und-uploads.md).
4. **Zur Veröffentlichung schicken**.

(Das Buch erwähnt einen Fehler in Version 3.2, bei dem ein erster Bild-Upload wiederholt werden musste. Diesen Fehler gibt es
längst nicht mehr.)

## Zur Startseite machen

Standardmäßig zeigt die Website den Wurzelknoten. Um stattdessen den Begrüßungsartikel zu zeigen, ermitteln Sie seine
**Knoten-ID**: die Zahl am Ende der Adresse des Elements in der Administration (`.../content/view/full/44`) oder in seinen
Details. Setzen Sie dann in Ihrem Siteaccess (`extension/exp_tscm_settings/settings/siteaccess/tscm/site.ini.append.php`):

```ini
[SiteSettings]
IndexPage=/content/view/full/44
```

Der ausgelieferte Wert ist `/content/view/full/2/`, der Wurzelknoten. Leeren Sie den INI-Cache
(`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`) und laden Sie die Website: Der Begrüßungsartikel erscheint mit dem
Standard-Template für Artikel.

## Ein eigenes Template

Das Standardaussehen ist nicht das, was der Club für seine Startseite möchte. Geben Sie diesem einen Knoten ein Template. Das
Buch tat das im Template-Editor der Administration, den es noch gibt (**Design > Template editor**; siehe
[Template-Overrides: die Grundlagen](../erweiterungsentwicklung/teil-2-builder/grundlagen-der-template-overrides.md)). Die Regel von
Hand zu schreiben ist dasselbe, und sie bleibt in der Erweiterung:

```ini
# extension/exp_tscm_settings/settings/siteaccess/tscm/override.ini.append.php
[full_view_welcome]
Source=node/view/full.tpl
MatchFile=full/welcome.tpl
Subdir=templates
Match[node]=44
```

und das Template `extension/exp_tscm_design/design/tscm/override/templates/full/welcome.tpl`:

```
{* The welcome page: show the article's own attributes. *}
<h1>{$node.name|wash}</h1>
{attribute_view_gui attribute=$node.data_map.intro}
{attribute_view_gui attribute=$node.data_map.body}
```

(`$node` ist der angezeigte Knoten; `$node.data_map` enthält seine Attribute nach Bezeichner.) Leeren Sie den Override-Cache,
wie immer bei einem neuen Template ([welcher Cache?](../handbuch/welchen-cache-leere-ich.md)), und laden Sie die Startseite neu.
Der Blockname der Regel ist frei wählbar; auch das Buch nannte ihn `full_view_welcome`. Weitere Bedingungen, für eine Klasse,
eine Sektion, eine Tiefe: [Override-Bedingungen](../erweiterungsentwicklung/teil-2-builder/override-bedingungen.md).

Weiter: [die Nachrichtenseiten](die-nachrichtenseiten.md).

## Quelle und Lizenz

Bearbeitet nach den Kapiteln über die Startseite und die Neuigkeiten des Tutorials *Building an eZ publish site* (geschrieben
für eZ publish 3.2, 2003), ursprünglich von ez.no, 2024 von 7x erneut unter der GNU Free Documentation License veröffentlicht,
[Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site). Copyright (c)
1999-2024 7x und Graham Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren
Version, ohne unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: für Exponential 6 neu geschrieben: Override-Regeln in einer Erweiterung, aktuelle Fetch- und Ansichtsparameter.
