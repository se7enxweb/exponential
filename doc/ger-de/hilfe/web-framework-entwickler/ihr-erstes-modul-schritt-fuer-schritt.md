---
title: "Aufgabe: ein kleines CRUD-Modul Schritt für Schritt bauen"
audience: web-framework-developer, developer
category: web-frameworks
order: 400
summary: Eine praktische Aufgabe für Framework-Entwickler - eine Erweiterung mit eigener Tabelle, einem Modell auf Basis persistenter Objekte, einer Listen-View und einem Bearbeitungsformular mit Aktionen, Validierung, dem CSRF-Token, einer Richtlinienfunktion, Templates und einem Deployment - der ganze Weg, den anderswo ein Befehl wie "make:crud" oder ein Scaffold erzeugen würde, einmal von Hand geschrieben, damit jedes Teil verstanden ist.
keywords: Tutorial, Aufgabe, CRUD, Scaffold, erstes Modul, Erweiterung, View, Template, Formular, Aktionen, Richtlinie, Deployment, tutorial, task, first module, extension, module.php, eZPersistentObject, view, template, form, actions, policy, design.ini, module.ini, deploy, Web-Framework-Entwickler, web framework developer
type: help
---
# Aufgabe: ein kleines CRUD-Modul Schritt für Schritt bauen

Diese Seite richtet sich an Entwickler, die jedes Teil einmal sehen möchten, so wie `make:crud`,
`rails generate scaffold` oder ein Modell in der Django-Administration sie erzeugen würden. Sie bauen eine Erweiterung
`expdemo` mit einer Tabelle von Einträgen, einer Liste unter `/expdemo/list` und einem Bearbeitungsformular unter
`/expdemo/edit/<id>`. **Setup > RAD** kann Ihnen danach einen Großteil dieses Grundgerüsts schreiben; wer es zuerst von
Hand macht, sieht, was die Assistenten schreiben ([RAD-Werkzeuge](../../funktionen/6.0/rad-werkzeuge.md)).

Zeitbedarf: etwa eine Stunde auf einer Entwicklungsinstallation (`./console exp:install --apply` richtet in wenigen Minuten
eine auf SQLite ein: [Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md)).

## Inhalt

1. [Die Dateien](#die-dateien)
2. [Das Modul registrieren](#das-modul-registrieren)
3. [Die Tabelle und das Modell](#die-tabelle-und-das-modell)
4. [Die Listen-View](#die-listen-view)
5. [Die Bearbeitungs-View](#die-bearbeitungs-view)
6. [Die Templates](#die-templates)
7. [Aktivieren, deployen und berechtigen](#aktivieren-deployen-und-berechtigen)
8. [Prüfen](#prüfen)

## Die Dateien

```text
extension/expdemo/
├── classes/expdemoitem.php
├── modules/expdemo/module.php
├── modules/expdemo/list.php
├── modules/expdemo/edit.php
├── design/standard/templates/expdemo/list.tpl
├── design/standard/templates/expdemo/edit.tpl
├── settings/module.ini.append.php
├── settings/design.ini.append.php
└── share/db_schema.dba
```

## Das Modul registrieren

```ini
# settings/module.ini.append.php
<?php /* #?ini charset="utf-8"?
[ModuleSettings]
ExtensionRepositories[]=expdemo
ModuleList[]=expdemo
*/ ?>
```

```ini
# settings/design.ini.append.php
<?php /* #?ini charset="utf-8"?
[ExtensionSettings]
DesignExtensions[]=expdemo
*/ ?>
```

```php
<?php
// modules/expdemo/module.php
$Module = array( 'name' => 'Demo' );

$ViewList = array();
$ViewList['list'] = array(
    'script' => 'list.php',
    'functions' => array( 'read' ),
    'unordered_params' => array( 'offset' => 'Offset' ) );
$ViewList['edit'] = array(
    'script' => 'edit.php',
    'functions' => array( 'manage' ),
    'params' => array( 'ItemID' ),
    'single_post_actions' => array( 'StoreButton' => 'Store', 'CancelButton' => 'Cancel' ),
    'post_action_parameters' => array( 'Store' => array( 'Title' => 'ItemTitle' ) ) );

$FunctionList = array();
$FunctionList['read'] = array();
$FunctionList['manage'] = array();
```

## Die Tabelle und das Modell

Legen Sie die Tabelle in Ihrer Entwicklungsdatenbank an, zum Beispiel unter SQLite:

```sql
CREATE TABLE expdemo_item ( id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL DEFAULT 0,
                            title VARCHAR(200) NOT NULL DEFAULT '', created INTEGER NOT NULL DEFAULT 0 );
```

Exportieren Sie sie anschließend für jede weitere Installation in eine datenbankneutrale Schemadatei
([Migrationen und Startdaten](migrationen-und-dba-schemadateien.md)):

```bash
php bin/php/ezsqldumpschema.php --type=sqlite --table-include=expdemo_item --output-array \
  var/storage/sqlite3/exponential.db extension/expdemo/share/db_schema.dba --allow-root-user
```

Das Modell ist die Klasse `expDemoItem` aus [ORM: eZPersistentObject](orm-und-ezpersistentobject.md#ein-modell),
gespeichert als `classes/expdemoitem.php`, ergänzt um eine Hilfsmethode:

```php
    public static function fetch( $id )
    {
        return eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => (int)$id ) );
    }
```

## Die Listen-View

```php
<?php
// modules/expdemo/list.php
$offset = isset( $Params['Offset'] ) ? max( 0, (int)$Params['Offset'] ) : 0;
$items = eZPersistentObject::fetchObjectList( expDemoItem::definition(), null, null,
                                              array( 'created' => 'desc' ),
                                              array( 'offset' => $offset, 'length' => 20 ) );

$tpl = eZTemplate::factory();
$tpl->setVariable( 'items', $items ?: array() );
$tpl->setVariable( 'offset', $offset );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:expdemo/list.tpl' );
$Result['path'] = array( array( 'text' => 'Items', 'url' => false ) );
```

## Die Bearbeitungs-View

```php
<?php
// modules/expdemo/edit.php
$Module = $Params['Module'];
$id = (int)$Params['ItemID'];
$item = $id > 0 ? expDemoItem::fetch( $id ) : null;
if ( $id > 0 && !$item )
    return $Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );

if ( $Module->isCurrentAction( 'Cancel' ) )
    return $Module->redirectTo( '/expdemo/list' );

$errors = array();
$title = $item ? $item->attribute( 'title' ) : '';
if ( $Module->isCurrentAction( 'Store' ) )
{
    $title = $Module->hasActionParameter( 'Title' ) ? trim( (string)$Module->actionParameter( 'Title' ) ) : '';
    if ( $title === '' || mb_strlen( $title ) > 200 )
        $errors['title'] = ezpI18n::tr( 'extension/expdemo', 'Enter a title of at most 200 characters.' );

    if ( $errors === array() )
    {
        if ( !$item )
            $item = new expDemoItem( array( 'user_id' => eZUser::currentUserID(), 'created' => time() ) );
        $item->setAttribute( 'title', $title );
        $item->store();
        return $Module->redirectTo( '/expdemo/list' );   // POST, redirect, GET
    }
}

$tpl = eZTemplate::factory();
$tpl->setVariable( 'item', $item );
$tpl->setVariable( 'title', $title );
$tpl->setVariable( 'errors', $errors );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:expdemo/edit.tpl' );
$Result['path'] = array( array( 'text' => 'Items', 'url' => 'expdemo/list' ),
                         array( 'text' => 'Edit', 'url' => false ) );
```

`/expdemo/edit/0` legt einen neuen Eintrag an.

## Die Templates

```text
{* design/standard/templates/expdemo/list.tpl *}
<h1>{'Items'|i18n( 'extension/expdemo' )}</h1>
<p><a href={'expdemo/edit/0'|ezurl}>{'New item'|i18n( 'extension/expdemo' )}</a></p>
<ul>
{foreach $items as $item}
  <li><a href={concat( 'expdemo/edit/', $item.id )|ezurl}>{$item.title|wash}</a></li>
{/foreach}
</ul>
```

```text
{* design/standard/templates/expdemo/edit.tpl *}
<form method="post" action={concat( 'expdemo/edit/', first_set( $item.id, 0 ) )|ezurl}>
  <label for="expdemo-title">{'Title'|i18n( 'extension/expdemo' )}</label>
  <input id="expdemo-title" type="text" name="ItemTitle" value="{$title|wash}" maxlength="200" />
  {if is_set( $errors.title )}<p class="error">{$errors.title|wash}</p>{/if}
  <button type="submit" name="StoreButton">{'Save'|i18n( 'extension/expdemo' )}</button>
  <button type="submit" name="CancelButton">{'Cancel'|i18n( 'extension/expdemo' )}</button>
</form>
```

Kein CSRF-Code: Für angemeldete Benutzer fügt `ezformtoken` das Token in das Formular ein und prüft es beim POST
([Formulare und CSRF](formulare-und-csrf-schutz.md)). Jeder ausgegebene Wert läuft durch `|wash`.

## Aktivieren, deployen und berechtigen

```ini
# settings/override/site.ini.append.php
[ExtensionSettings]
ActiveExtensions[]=expdemo
```

```bash
php bin/php/ezpgenerateautoloads.php -e
./console exp:velocity deploy --allow-root-user     # or clear the ini, design_base and template-override caches
```

Geben Sie unter **Benutzer > Rollen** der Rolle der Redakteure die Richtlinien `expdemo / read` und `expdemo / manage`.

## Prüfen

1. Mit der Rolle angemeldet: `/expdemo/list` zeigt eine leere Liste; **New item** öffnet das Formular.
2. Speichern Sie einen leeren Titel: Der Fehler erscheint neben dem Feld, und das Formular behält Ihre Eingabe.
3. Speichern Sie einen Titel: Sie werden zur Liste weitergeleitet, die ihn anzeigt.
4. Angemeldet ohne `expdemo / manage`: `/expdemo/edit/0` antwortet mit „Zugriff verweigert“. Abgemeldet: die
   Anmeldeseite.
5. `/expdemo/edit/abc` wird durch die Typumwandlung als `0` gelesen und zeigt das Formular für einen neuen Eintrag,
   nie einen Fehler.

Nächste Schritte: eine Fetch-Funktion, damit andere Templates Einträge auflisten können
([Fetch-Funktionen](fetch-funktionen-und-template-daten.md)), die Klassenform der Views
([Controller](controller-als-modul-views.md#die-klassenform)), ein Installationsbefehl für die Tabelle und Tests für
eine Validator-Klasse ([Testen](testen.md)).

## Verwandte Seiten

- [Ihre erste Erweiterung](../ersteller/anwendungsentwickler/ihre-erste-erweiterung.md)
- [Eigene Module und Views](../ersteller/anwendungsentwickler/eigene-module-und-ansichten.md)
- [Erweiterungsentwicklung, Teil 3](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
