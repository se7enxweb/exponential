---
title: "Inhalte für eine neue Site anlegen"
audience: developer, software-engineer, php-developer, website-creator, admin
category: migrations
order: 9
summary: "Eine Migration, die die Startinhalte einer Site veröffentlicht - einen Ordner und seine ersten Artikel - über Remote-IDs, nur was fehlt, in der Sprache der Inhaltswurzel, sodass jede neue Installation und jede Entwicklerkopie gleich beginnt."
keywords: Migration, Migrationen, Startinhalte, Standardinhalte, neue Site, Remote-ID, Entwicklerkopie, Beispielinhalte, migration, migrations, seed content, starting content, default content, new site, createAndPublishObject, remote id, idempotent, fixtures, demo content, exp:migration:migrate
type: help
---
# Inhalte für eine neue Site anlegen

Dieses Rezept richtet sich an Entwickler, die eine neue Site aufsetzen und wollen, dass jede Installation davon, auch
die Entwicklungskopie einer Kollegin, mit denselben Inhalten beginnt: den Bereichen, den ersten Seiten, den Texten,
die ein Template braucht, um etwas zu zeigen. Eine Migration veröffentlicht sie einmal je Installation, über
Remote-IDs, und die Migrationen von Klasse, Rolle und Tabelle, von denen sie abhängt, laufen vor ihr. Das Beispiel
veröffentlicht den Ordner **News** mit drei Artikeln.

Eine ganze fertige Site mit allen Inhalten auf eine andere Installation zu kopieren ist eine andere Aufgabe:
[ein Site-Paket](../../../anleitungen/pakete.md), siehe auch
[Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md).

## Was Sie am Ende haben

- die Migration `20261101170000_seed_news_content.php`, nach der Klasse und ihrem Attribut Subtitle geordnet;
- den Ordner News unter der Inhaltswurzel und drei News-Artikel darin, mit festen Remote-IDs (`site-seed-news-*`),
  die spätere Migrationen und Templates finden;
- eine Migration, die nur Fehlendes veröffentlicht, und ein `down()`, das entfernt, was sie veröffentlicht hat.

## Bevor Sie beginnen

- Exponential 6.0.15 mit erledigtem `6.0.15-migration-tables`.
- Die Klasse `news_article` mit `subtitle`
  ([Eine Inhaltsklasse anlegen oder ändern](eine-inhaltsklasse-anlegen-oder-aendern.md),
  [Ein Klassenattribut hinzufügen](ein-klassenattribut-hinzufuegen.md)).
- Ausführen als Benutzer des Webservers (als root mit `--allow-root-user`); die Inhalte gehören dem Benutzer des
  Laufs (`[RunSettings] RunAsUser`, voreingestellt `admin`, oder `--login=<login>`).

## Schritte

### 1. Erzeugen und füllen

```bash
./console exp:migration:generate --skeleton seed_news_content --description="The starting content of the site: the folder News with three articles" --apply
```

Ergänzen Sie die Kopfzeile `// requires: 20261101110000_news_article_class, 20261101120000_news_article_subtitle`
und die Methoden unten.

### 2. Die ganze Kette auf einer frischen Installation ausführen

Auf einer neuen Entwicklungsinstallation sind alle Migrationen der Site ausstehend, und ein Lauf wendet sie der Reihe
nach an. Hier hat die Site vier:

```bash
./console exp:migration:migrate --apply --no-commit
```

```text
Exponential migrations: run
Run 20261101-170412-c6d2e9, environment development, database sqlite (transactions: full), as admin

  applied     20261101090000_news_source_table             site               The table exp_news_source: where the news articles come from
              created exp_news_source
              wrote the source newsroom
  applied     20261101110000_news_article_class            site               The content class News article (news_article) in the class group Content
              created the class news_article (id 48) with 4 attributes
              added it to the class group Content
  applied     20261101120000_news_article_subtitle         site               The attribute Subtitle (subtitle, ezstring) of the class news_article, after Title
              added the attribute subtitle to news_article at position 2
              gave the 0 existing objects the attribute
  applied     20261101170000_seed_news_content             site               The starting content of the site: the folder News with three articles
              published folder "News" (object 57)
              published news_article "Welcome to our news" (object 58)
              published news_article "Meet the team" (object 59)
              published news_article "Send us your story" (object 60)

Captions: pre 20261101-170412, post 20261101-170415
Repository: no commit, push disabled
Summary: 4 migration(s): 4 done, 0 pending, 0 not needed, 0 failed, 0 unknown; 4 applied
PASS
```

### 3. Auf einer Installation ausführen, die einen Teil schon hat

Hat eine Redakteurin auf einer Installation den Ordner News mit derselben Remote-ID schon angelegt, nennt der Plan
nur die Artikel; `ensure()` nutzt, was existiert, und veröffentlicht den Rest.

## Die Migration

```php
<?php
// description: The starting content of the site: the folder News with three articles
// requires: 20261101110000_news_article_class, 20261101120000_news_article_subtitle
// author: Site team
namespace Site\Migrations;

final class SeedNewsContent20261101170000 extends \expMigrationPhp
{
    const FOLDER = 'site-seed-news-folder';

    /** @return array[] remote id => attributes of the articles, in their order */
    public static function articles()
    {
        return array(
            'site-seed-news-welcome' => array( 'title' => 'Welcome to our news', 'subtitle' => 'What you will read here',
                                               'intro' => 'News from the team, every week.', 'source' => 'Newsroom' ),
            'site-seed-news-team' => array( 'title' => 'Meet the team', 'subtitle' => 'Who writes the news',
                                            'intro' => 'Five people, one newsroom.', 'source' => 'Newsroom' ),
            'site-seed-news-contact' => array( 'title' => 'Send us your story', 'subtitle' => 'How to reach the newsroom',
                                               'intro' => 'Tips and stories are welcome.', 'source' => 'Newsroom' ),
        );
    }

    private static function language()
    {
        $root = \eZContentObjectTreeNode::fetch( 2 );
        if ( !$root instanceof \eZContentObjectTreeNode )
            throw new \RuntimeException( 'there is no content root (node 2)' );
        return $root->attribute( 'object' )->attribute( 'initial_language_code' );
    }

    /** @return \eZContentObject the object of a remote id, published now when it is missing */
    private static function ensure( $remoteId, $parentNodeId, $class, array $attributes, $language, array &$done )
    {
        $object = \eZContentObject::fetchByRemoteID( $remoteId );
        if ( $object instanceof \eZContentObject )
            return $object;
        $object = \eZContentFunctions::createAndPublishObject( array(
            'parent_node_id' => $parentNodeId,
            'class_identifier' => $class,
            'creator_id' => \eZUser::currentUserID(),
            'remote_id' => $remoteId,
            'language' => $language,
            'attributes' => $attributes,
        ), false );
        if ( !$object instanceof \eZContentObject )
            throw new \RuntimeException( "$remoteId could not be published" );
        $done[] = "published $class \"" . reset( $attributes ) . '" (object ' . $object->attribute( 'id' ) . ')';
        return $object;
    }

    public function plan( $context )
    {
        $lines = array();
        if ( !\eZContentObject::fetchByRemoteID( self::FOLDER ) )
            $lines[] = 'publish the folder News below the content root';
        foreach ( self::articles() as $remoteId => $attributes )
            if ( !\eZContentObject::fetchByRemoteID( $remoteId ) )
                $lines[] = 'publish the article "' . $attributes['title'] . '" in News';
        return $lines ? $lines : array( 'nothing: the starting content is there' );
    }

    public function up( $context )
    {
        $language = self::language();
        $done = array();
        $folder = self::ensure( self::FOLDER, 2, 'folder', array( 'name' => 'News' ), $language, $done );
        $folderNode = (int)$folder->attribute( 'main_node_id' );
        foreach ( self::articles() as $remoteId => $attributes )
            self::ensure( $remoteId, $folderNode, 'news_article', $attributes, $language, $done );
        $context->setReference( 'news_folder_node', $folderNode );
        return $done ? $done : array( 'nothing to publish' );
    }

    public function down( $context )
    {
        $done = array();
        foreach ( array_merge( array_keys( self::articles() ), array( self::FOLDER ) ) as $remoteId )
        {
            $object = \eZContentObject::fetchByRemoteID( $remoteId );
            if ( $object instanceof \eZContentObject )
            {
                $object->purge();
                $done[] = "removed $remoteId";
            }
        }
        return $done ? $done : array( 'nothing to remove' );
    }
}
```

- **Remote-IDs, nie Knoten- oder Objekt-IDs.** IDs unterscheiden sich zwischen Installationen; die Remote-ID wählen
  Sie, und sie ist überall gleich. Spätere Migrationen und Templates finden die Inhalte mit
  `eZContentObject::fetchByRemoteID( 'site-seed-news-folder' )`.
- **Nur was fehlt.** Ein zweiter Lauf oder ein Lauf, nachdem eine Redakteurin einen Teil von Hand angelegt hat,
  veröffentlicht nichts doppelt.
- **Die Sprache der Inhaltswurzel**, damit die Migration auch auf einer Installation funktioniert, deren Hauptsprache
  nicht Englisch ist.
- **`setReference( 'news_folder_node', ... )`** hinterlässt die Knoten-ID des Ordners für spätere Migrationen im selben
  Lauf (eine Referenz lebt nur einen Lauf lang).
- **Veröffentlichen ohne Benachrichtigungen** (`false`): Abonnenten bekommen keine Mail über Startinhalte.
- Für Startinhalte, die nur Entwicklungs- und Testkopien erreichen sollen (Demo-Benutzer, Beispielbestellungen),
  geben Sie den Kopf `environments: development, test`; siehe [Migrationen je Umgebung](migrationen-je-umgebung.md).
- Halten Sie personenbezogene Daten aus Start-Migrationen heraus: Sie werden in Git committet und gepusht.

## Prüfen

- Der Inhaltsbaum zeigt News mit drei Artikeln; die Artikel haben einen Subtitle.
- `./console exp:migration:migrate` zeigt jede Migration als `done` und ändert nichts.
- Auf einer zweiten frischen Installation existieren dieselben Remote-IDs; die Objekt-IDs können abweichen.

## Zurücknehmen

```bash
./console exp:migration:rollback 20261101170000_seed_news_content --apply
```

```text
  undone      20261101170000_seed_news_content             site               The starting content of the site: the folder News with three articles
              removed site-seed-news-welcome
              removed site-seed-news-team
              removed site-seed-news-contact
              removed site-seed-news-folder
```

`purge()` entfernt die Objekte endgültig, ohne Papierkorb; was eine Redakteurin unter News ergänzt hat, ginge mit dem
Ordner. Auf einer Installation, auf der Redakteure mit den Inhalten gearbeitet haben, stellen Sie stattdessen den
Sicherungspunkt des Laufs wieder her oder lassen die Inhalte stehen.

## Nächster Schritt

Begrenzen Sie Migrationen auf einige Installationen: [Migrationen je Umgebung](migrationen-je-umgebung.md).

## Verwandte Seiten

- [Migrationen: der Einstieg](../README.md) · [Alle Rezepte](README.md)
- [Die Anleitung](../../../anleitungen/migrationen.md), [das Tutorial](../../../anleitungen/migrationen-tutorial.md), [PHP-Migrationen](../../../anleitungen/migrationen-php.md)
- [In der Administration](../in-der-administration.md), [Befehle](../befehle.md), [Einstellungen](../einstellungen.md)
- [Das Dateiformat](../../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](../wenn-eine-migration-scheitert.md), [Meldungen und ihre Behebung](../../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](../migrationen-oder-umzug-einer-site.md)
- [Eine Inhaltsklasse anlegen oder ändern](eine-inhaltsklasse-anlegen-oder-aendern.md), [Teamarbeit mit Git](teamarbeit-mit-git.md), [Pakete](../../../anleitungen/pakete.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
