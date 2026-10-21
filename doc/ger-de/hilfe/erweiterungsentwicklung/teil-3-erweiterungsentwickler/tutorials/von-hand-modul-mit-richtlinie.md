---
title: "Von Hand: ein Modul, das durch eine Richtlinie geschützt ist"
audience: developer, app-creator
category: extension-development
order: 140
summary: Eine Admin-Seite mit einem Formular - eine Richtlinie mit einer Einschränkung, POST-Aktionen, Eingabeprüfung, eine eingeschränkte Zugriffsprüfung in PHP und ein admin4-Template, geprüft mit php -l und der Engine.
keywords: Tutorial, von Hand, Richtlinie, Einschränkung, Zugriffsprüfung, Formular, Formular-Token, tutorial, by hand, policy, FunctionList, limitation, values, hasAccessTo, accessWord, limited, single_post_actions, isCurrentAction, form, POST, form token, admin4, a4-form
type: help
---
# Von Hand: ein Modul, das durch eine Richtlinie geschützt ist

Tutorial 13. Sie fügen dem Modul `hello` eine Seite in der Administration hinzu: ein kleines Formular, das einen Gruß
zusammenstellt. Es zeigt das ganze Sicherheitsmuster einer Admin-Seite: eine Richtlinienfunktion mit einer
**Einschränkung**, POST-Aktionen, geprüfte Eingaben, eine Zugriffsprüfung, die die Einschränkung beachtet, und ein
admin4-Template ohne Inline-CSS oder -Skript. Die PHP-Dateien haben `php -l` bestanden; das Template wurde von der
Template-Engine gerendert.

## 1. Die View und ihre Richtlinie

Erweitern Sie `extension/mysite_hello/modules/hello/module.php`:

```php
<?php
$Module = array( 'name' => 'Hello' );

$ViewList = array();
$ViewList['world'] = array(
    'script' => 'world.php',
    'params' => array( 'Name' ),
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezcontentnavigationpart' );

$ViewList['compose'] = array(
    'script' => 'compose.php',
    'functions' => array( 'compose' ),
    'default_navigation_part' => 'ezsetupnavigationpart',
    'single_post_actions' => array( 'PreviewButton' => 'Preview',
                                    'CancelButton' => 'Cancel' ) );

$Greeting = array(
    'name' => 'Greeting',
    'values' => array( array( 'Name' => 'Hello', 'value' => 'hello' ),
                       array( 'Name' => 'Welcome', 'value' => 'welcome' ) ) );

$FunctionList = array();
$FunctionList['read'] = array();
$FunctionList['compose'] = array( 'Greeting' => $Greeting );
```

Die `values` der Einschränkung sind fest vorgegeben, in derselben Form wie bei `audit/read` des Kernels und dessen
Einschränkung `Channel` (`kernel/audit/module.php`). Im Rolleneditor lässt sich nun eine Richtlinie `hello/compose` für
alle Grüße oder nur für „Hello“ gewähren.

## 2. Das View-Skript

`extension/mysite_hello/modules/hello/compose.php`:

```php
<?php
/** @var eZModule $Module */
$Module = $Params['Module'];
$http = eZHTTPTool::instance();

if ( $Module->isCurrentAction( 'Cancel' ) )
{
    return $Module->redirectTo( '/content/dashboard' );
}

// Which greetings may this user compose? 'yes' = all, 'limited' = those of the Greeting limitation.
$all = array( 'hello' => 'Hello', 'welcome' => 'Welcome' );
$access = eZUser::currentUser()->hasAccessTo( 'hello', 'compose' );
$allowed = array();
if ( $access['accessWord'] === 'yes' )
{
    $allowed = $all;
}
else if ( $access['accessWord'] === 'limited' )
{
    foreach ( $access['policies'] as $limitations )
    {
        foreach ( isset( $limitations['Greeting'] ) ? (array)$limitations['Greeting'] : array() as $value )
        {
            if ( isset( $all[$value] ) )
                $allowed[$value] = $all[$value];
        }
    }
}

$preview = false;
$error = false;
if ( $Module->isCurrentAction( 'Preview' ) )
{
    $name = $http->postVariable( 'Name', '' );
    $greeting = $http->postVariable( 'Greeting', '' );
    if ( !is_string( $name ) || trim( $name ) === '' || mb_strlen( $name ) > 100 )
        $error = ezpI18n::tr( 'extension/mysite_hello', 'Enter a name of at most 100 characters.' );
    else if ( !is_string( $greeting ) || !isset( $allowed[$greeting] ) )
        $error = ezpI18n::tr( 'extension/mysite_hello', 'Choose one of the greetings you may use.' );
    else
        $preview = $allowed[$greeting] . ', ' . trim( $name ) . '!';
}

$tpl = eZTemplate::factory();
$tpl->setVariable( 'greetings', $allowed );
$tpl->setVariable( 'preview', $preview );
$tpl->setVariable( 'error', $error );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:hello/compose.tpl' );
$Result['path'] = array( array( 'text' => ezpI18n::tr( 'extension/mysite_hello', 'Compose a greeting' ), 'url' => false ) );
```

Was es zeigt:

- Der Kernel hat Benutzer ohne `hello/compose` bereits abgewiesen; das Skript grenzt nur anhand der Einschränkung ein.
- Ein gesendeter Wert wird auf seinen Typ (`is_string()`) und gegen die Liste des Erlaubten geprüft, nie einfach
  vertraut.
- Diese View ändert nichts, daher braucht eine Vorschau keine Weiterleitung; eine View, die speichert, muss nach dem
  POST weiterleiten.
- Die Erweiterung `ezformtoken` fügt das Formular-Token automatisch hinzu und prüft es, daher trägt das Formular unten
  keines von Hand.

## 3. Das admin4-Template

`extension/mysite_hello/design/admin4/templates/hello/compose.tpl`:

```text
<div class="a4-page">
    <h1 class="a4-pagehead-title">{'Compose a greeting'|i18n( 'extension/mysite_hello' )}</h1>
    {if $error}
        <div class="a4-notice is-bad" role="alert"><p>{$error|wash}</p></div>
    {/if}
    {if $preview}
        <div class="a4-notice is-ok" role="status"><p>{$preview|wash}</p></div>
    {/if}
    {if $greetings|count}
    <form method="post" action={'/hello/compose'|ezurl} class="a4-form">
        <div class="a4-field">
            <label class="a4-label" for="mysite-hello-name">{'Name'|i18n( 'extension/mysite_hello' )}</label>
            <input class="a4-input" id="mysite-hello-name" type="text" name="Name" maxlength="100" required="required" />
        </div>
        <div class="a4-field">
            <label class="a4-label" for="mysite-hello-greeting">{'Greeting'|i18n( 'extension/mysite_hello' )}</label>
            <select class="a4-input" id="mysite-hello-greeting" name="Greeting">
            {foreach $greetings as $value => $label}
                <option value="{$value|wash}">{$label|wash}</option>
            {/foreach}
            </select>
        </div>
        <div class="a4-actions">
            <button type="submit" class="a4-btn a4-btn-primary" name="PreviewButton">{'Preview'|i18n( 'extension/mysite_hello' )}</button>
            <button type="submit" class="a4-btn" name="CancelButton">{'Cancel'|i18n( 'extension/mysite_hello' )}</button>
        </div>
    </form>
    {else}
        <p class="a4-empty">{'Your roles allow no greeting.'|i18n( 'extension/mysite_hello' )}</p>
    {/if}
</div>
```

Mit einem erlaubten Gruß und einer Vorschau gerendert, gab es den Erfolgshinweis `Hello, Ada!` und eine Option aus. Jede
Gestaltung stammt aus den gemeinsamen Komponenten; es enthält weder `style=""` noch ein Skript.

## 4. Die Richtlinie ausprobieren

1. `php -l` für beide Dateien, die Caches leeren.
2. Gewähren Sie einer Testrolle `hello/compose` mit der Einschränkung Greeting = Hello und weisen Sie sie einem
   Testbenutzer zu.
3. Melden Sie sich als dieser Benutzer an: Das Formular bietet nur „Hello“ an. Senden Sie `welcome`, indem Sie das
   Formular in den Entwicklerwerkzeugen des Browsers bearbeiten: Die Seite antwortet „Choose one of the greetings you
   may use.“
4. Ein Benutzer ohne `hello/compose` erhält die Seite „Zugriff verweigert“.

Verwenden Sie dafür einen Testbenutzer, den Sie danach wegwerfen, nie ein gemeinsam genutztes Administratorkonto.

## Verwandte Seiten

- Zurück: [Von Hand: ein Modul mit einer View](von-hand-modul-mit-view.md)
- Weiter: [Von Hand: ein Datentyp](von-hand-datentyp.md)
- [Richtlinienfunktionen und Zugriffsprüfungen](../richtlinienfunktionen-und-zugriff.md), [Sicherheit in Erweiterungen](../sicherheit-in-erweiterungen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
