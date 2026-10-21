---
title: Eine Website bauen, ohne Code zu schreiben
audience: developer, designer, app-creator
category: extension-development
order: 210
summary: Eine vollständige kleine Website, nur mit den Werkzeugen aus Teil 2 gebaut - eine Einstellungserweiterung und eine Design-Erweiterung, Klassen, Overrides für die Full- und Line-Ansicht, ein Menü in einem cache-block, ein Mitgliederbereich - als geordneter Plan mit Prüfungen.
keywords: Website bauen, ohne Code, Design-Erweiterung, Einstellungserweiterung, Overrides, Klassen, Pagelayout, Menü, Mitgliederbereich, Plan, Checkliste, Projekt, site building, no code, design extension, settings extension, classes, members area, checklist
type: help
---
# Eine Website bauen, ohne Code zu schreiben

Teil 2, Kapitel 21. Dieses Kapitel fügt den ganzen Teil zusammen: eine kleine Firmenwebsite mit Nachrichten,
Veranstaltungen und einem Bereich nur für Mitglieder, gebaut aus Einstellungen, Klassen, Templates und Overrides, ganz
ohne PHP. Es ist ein Plan, dem Sie auf Ihrer Entwicklungsinstallation folgen können; jeder Schritt verweist auf das
Kapitel, das ihn erklärt.

## Die Website

- Eine Startseite mit den drei neuesten Nachrichten und den nächsten Veranstaltungen.
- Nachrichtenartikel und Veranstaltungen, jeweils mit eigener Vollansicht und Listenansicht.
- Ein Mitgliederbereich, den nur angemeldete Mitglieder lesen können.
- Ein Menü der obersten Ordner auf jeder Seite.

## Schritt 1: zwei Erweiterungen

```bash
mkdir -p extension/mysite_settings/settings/siteaccess/mysite
mkdir -p extension/mysite_design/settings extension/mysite_design/design/mysite/templates \
         extension/mysite_design/design/mysite/override/templates extension/mysite_design/design/mysite/stylesheets
```

Geben Sie beiden eine `ezinfo.php` und eine `extension.xml` ([ezinfo.php](../teil-3-erweiterungsentwickler/ezinfo-php.md)),
registrieren Sie das Design in `extension/mysite_design/settings/design.ini.append.php`:

```ini
[ExtensionSettings]
DesignExtensions[]=mysite_design
```

und aktivieren Sie beide, die Einstellungserweiterung zuerst ([Einstellungen in einer Erweiterung](erweiterungseinstellungen-fuer-builder.md)).

## Schritt 2: Der Siteaccess verwendet Ihr Design

In `extension/mysite_settings/settings/siteaccess/mysite/site.ini.append.php`:

```ini
[DesignSettings]
SiteDesign=mysite
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=base
```

Prüfen Sie es mit dem Skript zur Design-Kette aus [Die Design-Kette](die-design-kette.md): Ihr Design steht an erster
Stelle.

## Schritt 3: das Inhaltsmodell

Legen Sie in der Administration die Klassen an ([Inhaltsklassen für Builder](inhaltsklassen-fuer-builder.md)):

| Klasse | Bezeichner | Attribute |
|---|---|---|
| Nachrichtenartikel | `news_article` | `title` (Textzeile), `intro` (Rich Text), `body` (Rich Text), `image` (Bild) |
| Veranstaltung | `event` | `title`, `intro`, `start_date` (Datum und Uhrzeit), `location` (Textzeile) |

Verwenden Sie für Container die vorhandene Klasse Folder. Legen Sie einen Ordner „News“, einen Ordner „Events“ und
einen Ordner „Members“ an.

## Schritt 4: Ansichten

Overrides in `extension/mysite_settings/settings/siteaccess/mysite/override.ini.append.php`:

```ini
[full_news_article]
Source=node/view/full.tpl
MatchFile=full/news_article.tpl
Subdir=templates
Match[class_identifier]=news_article

[line_news_article]
Source=node/view/line.tpl
MatchFile=line/news_article.tpl
Subdir=templates
Match[class_identifier]=news_article

[full_event]
Source=node/view/full.tpl
MatchFile=full/event.tpl
Subdir=templates
Match[class_identifier]=event

[full_frontpage]
Source=node/view/full.tpl
MatchFile=full/frontpage.tpl
Subdir=templates
Match[node]=2
```

Schreiben Sie die vier Templates in `extension/mysite_design/design/mysite/override/templates/`
([Template-Overrides: die Grundlagen](grundlagen-der-template-overrides.md)). Die Startseite ruft die neuesten Nachrichten ab
([Inhalte in Templates abrufen](fetch-fuer-builder.md)):

```text
{def $news = fetch( 'content', 'tree', hash( 'parent_node_id', 2,
                                             'class_filter_type', 'include',
                                             'class_filter_array', array( 'news_article' ),
                                             'sort_by', array( 'published', false() ),
                                             'limit', 3 ) )}
{foreach $news as $item}
    {node_view_gui content_node=$item view='line'}
{/foreach}
{undef $news}
```

## Schritt 5: das Menü

Setzen Sie das Menü in der `templates/pagelayout.tpl` Ihres Designs (beginnen Sie mit einer Kopie des Pagelayouts des
Designs, auf das Sie zurückfallen) in einen `cache-block` ([Caching und Templates](caching-und-templates.md)):

```text
{cache-block keys=array( $module_result.node_id )}
<nav>
{foreach fetch( 'content', 'list', hash( 'parent_node_id', 2,
                                       'class_filter_type', 'include',
                                       'class_filter_array', array( 'folder' ),
                                       'sort_by', array( 'priority', true() ) ) ) as $item}
    <a href={$item.url_alias|ezurl}>{$item.name|wash}</a>
{/foreach}
</nav>
{/cache-block}
```

## Schritt 6: Styles

Legen Sie Ihr CSS in `extension/mysite_design/design/mysite/stylesheets/site.css` ab und laden Sie es über die
Einstellungen des Designs (`design.ini [StylesheetSettings]`), nie mit `style=""` oder einem `<style>`-Block; siehe
[Theme-Assets, der Packer und CSP](../teil-6-theme-entwickler/theme-assets-der-packer-und-csp.md).

## Schritt 7: der Mitgliederbereich

Folgen Sie dem ausgearbeiteten Plan in [Sektionen, Rollen und Zugriff für Builder](sektionen-rollen-und-zugriff-fuer-builder.md):
die Sektion `members`, dem Ordner Members zugewiesen, eine Rolle mit `content/read`, beschränkt auf diese Sektion, der
Benutzergruppe Members zugewiesen.

## Schritt 8: prüfen

- [ ] Jede Seite wird ohne Fehler in der Debug-Ausgabe dargestellt.
- [ ] Die Tabelle der verwendeten Templates zeigt Ihre Overrides für Nachrichten, Veranstaltungen und die Startseite.
- [ ] Der Mitgliederbereich wird abgemeldet verweigert und ist als Mitglied lesbar.
- [ ] Das Veröffentlichen einer Nachricht aktualisiert die Startseite (der View-Cache wird beim Veröffentlichen geleert).
- [ ] Beide Erweiterungen sind Git-Repositories mit ihrem ersten Commit; `settings/override/` ist in keinem von beiden.

## Was Sie gebaut haben und wie es weitergeht

Eine echte Website unter Versionskontrolle, ohne Code. Taucht eine Anforderung auf, die Einstellungen und Templates
nicht erfüllen können (ein Import aus einem anderen System, ein Feldtyp, den es nicht gibt, eine Seite, die etwas
berechnet), ist es Zeit für [Teil 3](../teil-3-erweiterungsentwickler/README.md); das nächste Kapitel hilft Ihnen bei der
Entscheidung.

## Verwandte Seiten

- Zurück: [Cronjobs für Builder](cronjobs-fuer-builder.md)
- Weiter: [Wenn Einstellungen nicht reichen](wenn-einstellungen-nicht-reichen.md)
- [Templates und Design](../../../anleitungen/templates-und-design.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
