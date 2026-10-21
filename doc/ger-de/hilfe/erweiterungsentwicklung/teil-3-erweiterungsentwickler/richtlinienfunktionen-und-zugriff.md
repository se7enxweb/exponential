---
title: Richtlinienfunktionen und Zugriffsprüfungen
audience: developer, app-creator
category: extension-development
order: 110
summary: Ein Modul schützen - Richtlinienfunktionen und Einschränkungen in module.php, Views, die sie verlangen, eZUser::hasAccessTo() mit seinen Antworten ja, nein und eingeschränkt, und das Gewähren über Rollen.
keywords: Richtlinie, Richtlinienfunktion, Einschränkung, Rolle, Berechtigung, Zugriff verweigert, Modulsicherheit, policy, FunctionList, functions, limitation, hasAccessTo, accessWord, limited, role, permission, access denied, module security, eZUser, limitation values
type: help
---
# Richtlinienfunktionen und Zugriffsprüfungen

Teil 3, Kapitel 11. Eine View ohne Richtlinienfunktion kann jeder öffnen, der die Website erreicht. Dieses Kapitel
zeigt, wie Sie die Funktionen Ihres Moduls deklarieren, wie der Kernel sie durchsetzt und wie Sie den Zugriff selbst
prüfen, wenn eine View mehr braucht als „darf oder darf nicht“.

## Funktionen deklarieren

In `module.php`:

```php
<?php
$Module = array( 'name' => 'Hello' );

$ViewList = array();
$ViewList['world'] = array( 'script' => 'world.php', 'params' => array( 'Name' ), 'functions' => array( 'read' ) );
$ViewList['admin'] = array( 'script' => 'admin.php', 'functions' => array( 'administrate' ) );

$FunctionList = array();
$FunctionList['read'] = array();
$FunctionList['administrate'] = array();
```

- `$FunctionList` führt auf, was Rollen gewähren können: **Benutzer > Rollen und Richtlinien** bietet nun `hello/read`
  und `hello/administrate` an.
- `'functions'` an einer View sagt, was ein Benutzer braucht. Der Kernel prüft das, **bevor** die View läuft, und zeigt
  andernfalls die Seite „Zugriff verweigert“; Ihr Skript muss die Prüfung nicht wiederholen.
- `'functions' => array( 'read', 'administrate' )` bedeutet, dass eine der beiden genügt.

Anonyme Besucher erhalten nichts, bis Sie die Funktion der Rolle Anonymous gewähren; das ist die sichere Voreinstellung.

## Einschränkungen

Eine Funktion kann eingeschränkt werden. Das `content/read` des Kernels ist nach Klasse, Sektion, Eigentümer, Gruppe,
Knoten, Teilbaum und Objektzustand einschränkbar (`kernel/content/module.php`):

```php
$FunctionList['read'] = array( 'Class' => $ClassID,
                               'Section' => $SectionID,
                               'Owner' => $Assigned,
                               'Group' => $AssignedGroup,
                               'Node' => $Node,
                               'Subtree' => $Subtree );
```

Eine Einschränkung wird dadurch definiert, woher ihre möglichen Werte kommen. Aus der Erweiterung expai
(`modules/expai/module.php`):

```php
$Provider = array( 'name' => 'Provider', 'values' => array(), 'class' => 'expAiPolicy',
                   'function' => 'providers', 'parameter' => array() );
$FunctionList['model_use'] = array( 'Provider' => $Provider );
```

| Schlüssel | Bedeutung |
|---|---|
| `name` | der Name der Einschränkung, wie ihn der Rolleneditor zeigt |
| `values` | eine feste Liste von `array( 'Name' => ..., 'value' => ... )`, oder leer, wenn eine Klasse die Werte liefert |
| `class`, `function`, `parameter` | eine statische Methode, die die möglichen Werte zurückgibt |

Der Kernel zeigt die Auswahl im Rolleneditor an. **Ihre View muss eine Einschränkung selbst durchsetzen**: Der Kernel
prüft nur, ob der Benutzer die Funktion in irgendeiner Form besitzt.

## Den Zugriff aus PHP prüfen

`eZUser::hasAccessTo( $module, $function = false )` (`kernel/classes/datatypes/ezuser/ezuser.php`) antwortet mit einem
Array, dessen `accessWord` lautet:

| `accessWord` | Bedeutung | Zusätzlich zurückgegeben |
|---|---|---|
| `yes` | der Benutzer darf, ohne Einschränkungen | |
| `no` | der Benutzer darf nicht | |
| `limited` | der Benutzer darf, innerhalb von Einschränkungen | `policies`: die Einschränkungen jeder Richtlinie |

```php
<?php
$user = eZUser::currentUser();
$access = $user->hasAccessTo( 'hello', 'administrate' );

if ( $access['accessWord'] === 'no' )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}
if ( $access['accessWord'] === 'limited' )
{
    // collect the values of one limitation from every policy that grants the function
    $allowed = array();
    foreach ( $access['policies'] as $limitations )
    {
        if ( isset( $limitations['Provider'] ) )
        {
            $allowed = array_merge( $allowed, (array)$limitations['Provider'] );
        }
    }
    // ... allow only what is in $allowed
}
```

Verwenden Sie für Inhalte die eigenen Prüfungen des Inhaltsobjekts: `$object->canRead()`, `$node->canEdit()` und
ähnliche, die jede Inhaltseinschränkung korrekt anwenden. Implementieren Sie Inhaltsberechtigungen nie selbst nach.

## In Templates

```text
{if fetch( 'user', 'has_access_to', hash( 'module', 'hello', 'function', 'administrate' ) )}
    <a href={'/hello/admin'|ezurl}>Administration</a>
{/if}
```

Einen Link auszublenden ist eine Höflichkeit; der Schutz ist das `'functions'` der View.

## Die Funktion gewähren

Rollen werden in der Administration eingerichtet. Für eine Erweiterung, deren Funktion bei der Installation gewährt
werden muss (zum Beispiel der Rolle Editor), liefern Sie einen Befehl mit oder dokumentieren den Schritt. Das eigene
Beispiel des Kernels ist `./console exp:addexphelpreadpolicy`, das `exphelp/read` gewährt und die Rollen mit
`--dry-run` zuerst auflistet.

## Einen Benutzer prüfen

```bash
./console exp:accesscheck --help
```

beantwortet, was ein Benutzer mit einem Knoten oder Objekt tun darf und welche Einschränkung es verweigert.

## Verwandte Seiten

- Zurück: [View-Parameter, ungeordnete Parameter und POST](view-parameter.md)
- Weiter: [View-Ergebnisse, der Pfad und das Pagelayout](view-ergebnisse-und-das-pagelayout.md)
- [Sektionen, Rollen und Zugriff für Builder](../teil-2-builder/sektionen-rollen-und-zugriff-fuer-builder.md)
- [eZUser und Zugriffsprüfungen](../teil-8-referenz/ezuser-und-zugriff.md)
- [Inhaltsrichtlinien-Einschränkungen von Erweiterungen](../../../../eng-us/features/6.0/content-limitation-handlers.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
