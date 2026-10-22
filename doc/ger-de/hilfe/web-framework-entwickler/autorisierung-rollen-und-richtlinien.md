---
title: "Autorisierung: Rollen, Richtlinien und Einschränkungen"
audience: web-framework-developer, developer
category: web-frameworks
order: 250
summary: Das Berechtigungssystem von Exponential für Entwickler, die Voters, Gates, Policies, Django-Berechtigungen oder CanCan-Abilities kennen - Rollen aus Richtlinien, Modulfunktionen und Einschränkungen, Zuweisungen an Benutzer und Gruppen mit Einschränkung nach Teilbaum oder Bereich, was der Kernel vor einem View prüft, wie Sie in PHP und in Templates prüfen und wie Sie Berechtigungen für Ihr eigenes Modul deklarieren.
keywords: Autorisierung, Berechtigungen, Rollen, Richtlinien, Einschränkungen, Funktionen, authorization, permissions, roles, policies, limitations, functions, hasAccessTo, canRead, canEdit, accessWord, FunctionList, voters, gates, Laravel policies, Django permissions, CanCanCan, Pundit, Spring Security, Web-Framework-Entwickler, web framework developer
type: help
---
# Autorisierung: Rollen, Richtlinien und Einschränkungen

Diese Seite richtet sich an Entwickler, die Voters, Gates, Policy-Klassen oder Berechtigungs-Decorators schreiben. Die
Berechtigungen von Exponential sind **Daten**, die Administratoren unter **Benutzer und Rollen** verwalten: Eine
**Rolle** ist eine Menge von **Richtlinien**; eine Richtlinie gewährt eine **Funktion** eines **Moduls**
(`content/read`, `expdemo/manage`), optional eingeschränkt durch **Einschränkungen** (nur diese Klasse, dieser
Bereich, dieser Teilbaum, diese Sprache, dieser Siteaccess); eine Rolle wird Benutzern oder Gruppen **zugewiesen**,
optional beschränkt auf einen Teilbaum oder Bereich. Ihr Code deklariert die Funktionen und prüft sie; die
Administratoren entscheiden, wer sie erhält
([Richtlinien, Rollen und Einschränkungen](../../standards/architektur/kernel-architektur.md#richtlinien-rollen-und-einschränkungen)).

## Inhalt

1. [Das Modell](#das-modell)
2. [Berechtigungen für Ihr Modul deklarieren](#berechtigungen-für-ihr-modul-deklarieren)
3. [Was der Kernel für Sie prüft](#was-der-kernel-für-sie-prüft)
4. [Prüfen in PHP](#prüfen-in-php)
5. [Prüfen in Templates](#prüfen-in-templates)
6. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)
7. [Regeln](#regeln)

## Das Modell

| Begriff | Klasse | Beispiel |
|---|---|---|
| Rolle | `eZRole` | Redakteur |
| Richtlinie | `eZPolicy` | `content/edit` |
| Einschränkung | `eZPolicyLimitation`, `eZPolicyLimitationValue` | Klasse: Artikel, Bereich: Standard |
| Zuweisung | Rolle an Benutzer oder Gruppe, optional eingeschränkt | Redakteur für die Gruppe Redakteure, eingeschränkt auf den Teilbaum „News“ |

Die Anleitung für Administratoren: [Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md).

## Berechtigungen für Ihr Modul deklarieren

```php
// extension/expdemo/modules/expdemo/module.php
$ViewList['manage'] = array( 'script' => 'manage.php', 'functions' => array( 'manage' ) );

// a limitation whose values are the sections, as kernel/content/module.php defines $SectionID
$SectionLimitation = array( 'name' => 'Section', 'values' => array(), 'class' => 'eZSection',
                            'function' => 'fetchList', 'parameter' => array( false ) );

$FunctionList = array();
$FunctionList['read'] = array();
$FunctionList['manage'] = array( 'Section' => $SectionLimitation );
```

`$FunctionList` benennt die Funktionen und die Einschränkungen, die jede von ihnen annimmt; die Administratoren sehen
dann `expdemo/read` und `expdemo/manage` im Richtlinien-Editor
([Richtlinienfunktionen und Einschränkungen](../ersteller/anwendungsentwickler/richtlinienfunktionen-und-einschraenkungen.md),
[Referenz zu module.php](../ersteller/anwendungsentwickler/referenz-der-moduldefinition.md#functionlist-und-einschränkungen)).

## Was der Kernel für Sie prüft

- Das `'functions' => array( 'manage' )` eines Views wird geprüft, bevor der View läuft: Ohne die Funktion erhält
  der Benutzer „Zugriff verweigert“, ein anonymer Benutzer die Anmeldeseite.
- Diese Prüfung wertet keine Einschränkungen für Ihre eigenen Daten aus; sie weiß nur, dass der Benutzer die Funktion
  hat, möglicherweise eingeschränkt.
- Inhaltsabfragen (`subTreeByNodeID()`, `fetch( 'content', ... )`) liefern nur, was der Benutzer lesen darf, und
  wenden dabei jede Inhaltseinschränkung an.

## Prüfen in PHP

```php
$user = eZUser::currentUser();
$access = $user->hasAccessTo( 'expdemo', 'manage' );
switch ( $access['accessWord'] )
{
    case 'yes':     // unlimited
        break;
    case 'limited': // $access['policies'] holds the limitations, for example allowed section ids
        break;
    default:        // 'no'
        return $Params['Module']->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

if ( !$node->canRead() ) { /* deny */ }
if ( !$object->canEdit() ) { /* deny */ }
```

Fragen Sie bei Inhalten das Objekt oder den Knoten: Diese wenden Einschränkungen nach Klasse, Bereich, Eigentümer,
Teilbaum, Status und Sprache an. Wenden Sie bei Ihren eigenen Tabellen die Einschränkungen aus `$access['policies']`
in Ihrer Abfrage an, und prüfen Sie die Eigentümerschaft selbst
([Zugriffskontrolle im Code](../ersteller/anwendungsentwickler/zugriffskontrolle-im-code.md)).

## Prüfen in Templates

```text
{if fetch( 'user', 'has_access_to', hash( 'module', 'expdemo', 'function', 'manage' ) )}
  <a href={'expdemo/manage'|ezurl}>{'Manage'|i18n( 'extension/expdemo' )}</a>
{/if}
```

Einen Link auszublenden ist Komfort, keine Sicherheit: Der View muss erneut prüfen
([Template-Operatoren für Rollen und Richtlinien](../../funktionen/6.0/template-operatoren-fuer-rollen-und-richtlinien.md)).

## Im Vergleich mit anderen Frameworks

| Framework | Konzept | Exponential |
|---|---|---|
| Symfony | `isGranted()`, Voters, `#[IsGranted]` | `hasAccessTo()`, `canRead()`, die `functions` des Views |
| Laravel | Gates, Policy-Klassen, `can`-Middleware | Funktionen in `$FunctionList`, Prüfungen im View |
| Django | Modellberechtigungen, `@permission_required`, Pakete für Objektberechtigungen | Funktionen und Einschränkungen; Objektprüfungen für Inhalte eingebaut |
| Rails | CanCanCan-Abilities, Pundit-Policies | Rollen als Daten, in der Administrationsoberfläche bearbeitet |
| Spring | Method Security, ACLs | Richtlinien mit Einschränkungen; ACL-ähnliche Prüfungen für Inhalte eingebaut |

Der größte Unterschied: Die Regeln liegen in der Datenbank und werden von Administratoren geändert, nicht im Code. Ihr
Code benennt die Funktionen und stellt die Fragen.

## Regeln

- Über den Zugriff entscheiden Richtlinien, niemals eine Prüfung auf eine Benutzer- oder Gruppen-ID in Code oder
  Templates.
- Eine Funktion pro Art von Aktion (`read`, `create`, `manage`), nicht pro Bildschirm.
- Gewähren Sie eine neue Funktion niemals standardmäßig dem anonymen Benutzer; dokumentieren Sie, was gewährt werden
  soll.
- Wenden Sie auf eine Liste dieselben Prüfungen an wie auf ein einzelnes Element: Filtern Sie die Abfrage, statt
  Zeilen im Template auszublenden.
- Hintergrundarbeit läuft als der Benutzer, der sie eingereiht hat, oder als eigener Integrationsbenutzer mit einer
  eigens dafür angelegten Rolle.

## Verwandte Seiten

- [Authentifizierung und Sitzungen](authentifizierung-und-sitzungen.md)
- [Muster für Richtlinienfunktionen](../../standards/muster/richtlinienfunktion.md)
- [Handler für Inhaltseinschränkungen](../../funktionen/6.0/handler-fuer-inhaltseinschraenkungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
