---
title: eZUser und Zugriffsprüfungen
audience: developer
category: extension-development
order: 90
summary: Der aktuelle Benutzer und Zugriffsprüfungen aus PHP und Templates - eZUser::currentUser(), isAnonymous(), isLoggedIn(), hasAccessTo() und sein accessWord yes, no oder limited, Prüfungen auf Objektebene und die Fetch-Funktionen für Benutzer.
keywords: Benutzer, aktueller Benutzer, Zugriffsprüfung, Richtlinien, Rollen, anonym, angemeldet, eZUser, currentUser, currentUserID, isAnonymous, isLoggedIn, hasAccessTo, accessWord, limited, policies, roles, has_access_to, canRead, access check
type: help
---
# eZUser und Zugriffsprüfungen

Teil 8, Referenz 9. `eZUser` (`kernel/classes/datatypes/ezuser/ezuser.php`) ist ein Benutzerkonto. Jeder Request hat
einen aktuellen Benutzer; auf einer öffentlichen Seite ist das meist der anonyme Benutzer. Zugriff wird über Rollen und
ihre Richtlinien gewährt ([Sektionen, Rollen und Zugriff für Builder](../teil-2-builder/sektionen-rollen-und-zugriff-fuer-builder.md)),
und eine Erweiterung deklariert ihre eigenen Richtlinienfunktionen
([Richtlinienfunktionen und Zugriff](../teil-3-erweiterungsentwickler/richtlinienfunktionen-und-zugriff.md)).

## Der aktuelle Benutzer

| Methode (statisch) | Gibt zurück |
|---|---|
| `eZUser::currentUser()` | den Benutzer dieses Requests |
| `eZUser::currentUserID()` | die ID |
| `eZUser::fetch( $id, $asObject = true )`, `fetchByName( $login, ... )`, `fetchByEmail( $email, ... )` | andere Benutzer |

| Methode | Gibt zurück |
|---|---|
| `isAnonymous()` | `true` für den anonymen Benutzer |
| `isLoggedIn()` | `true`, wenn angemeldet |
| `contentObject()` | das Inhaltsobjekt des Benutzers (Name, Foto ...) |
| `groups( $asObject = false )` | die Gruppen des Benutzers |
| `roles()` | die Rollen des Benutzers |

## hasAccessTo()

```php
function hasAccessTo( $module, $function = false )
```

gibt ein Array zurück, dessen `accessWord` einer dieser Werte ist:

| accessWord | Bedeutung |
|---|---|
| `yes` | voller Zugriff |
| `no` | kein Zugriff |
| `limited` | Zugriff mit Einschränkungen (Sektionen, Klassen, Teilbäume ...), aufgeführt in `policies` |

```php
<?php
$user = eZUser::currentUser();
$access = $user->hasAccessTo( 'myext', 'manage' );
if ( $access['accessWord'] === 'no' )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}
```

In einem Kommandozeilenskript als anonymer Benutzer ausgeführt, gab `hasAccessTo( 'content', 'read' )` `limited`
zurück und `hasAccessTo( 'setup', 'setup' )` `no`.

Bei einer Richtlinienfunktion ohne Einschränkungen (den meisten Funktionen von Erweiterungen) behandeln Sie alles außer
`yes` als keinen Zugriff, es sei denn, Ihre Funktion deklariert Einschränkungen und Sie werten sie aus.

## Zugriff auf Inhalte

Verwenden Sie für Inhalte die eigenen Prüfungen des Objekts und des Knotens, die alle Einschränkungen auswerten:

| Methode | An |
|---|---|
| `canRead()`, `canEdit( ... )`, `checkAccess( $functionName, ... )` | `eZContentObject` |
| `canRead()`, `canEdit()`, `canCreate()`, `canRemove()` | `eZContentObjectTreeNode` |

`subTreeByNodeID()` und die Fetch-Funktionen für Inhalte wenden den Lesezugriff selbst an.

## In Templates

```text
{if fetch( 'user', 'has_access_to', hash( 'module', 'myext', 'function', 'manage' ) )}
    <a href={'/myext/manage'|ezurl}>{'Manage'|i18n( 'extension/myext' )}</a>
{/if}
```

`fetch( 'user', 'current_user' )` liefert den aktuellen Benutzer. In Administrationsmenüs verbirgt
`menu.ini [MenuAccessSettings]` Links, die ein Benutzer nicht öffnen kann
([Kontextmenüs und Integration in die Administration](../teil-4-aktualisieren/kontextmenues-und-admin-integration.md)).

## Regeln

- Prüfen Sie den Zugriff in der View, auch wenn der Link dorthin verborgen ist: Jeder kann eine URL eintippen.
- Vergleichen Sie im Code nie Benutzer-IDs oder Gruppennamen, um Zugriff zu gewähren; gewähren Sie ihn über eine
  Richtlinienfunktion, damit Administratoren ihn zuweisen können.
- Einen Benutzer aus Code anzumelden (`eZUser::loginUser()`) ist Sache von Login-Handlern; eine gewöhnliche
  Erweiterung tut das nie.

**Demomodus.** `eZUser::store()` behält das gespeicherte Passwort eines Administrators bei, solange
`EXP_DEMO_MODE=1` gilt; fragen Sie `expDemoMode::protectsPassword( $user )`, bevor Sie ein Passwort ändern, damit der
Benutzer den Grund erfährt. [Demomodus für Entwickler](demomodus-fuer-entwickler.md).

## Verwandte Seiten

- Zurück: [eZContentClass und ihre Attribute](ezcontentclass.md)
- Weiter: [eZHTTPTool: Requests, Sitzungen und Weiterleitungen](ezhttptool.md)
- [Sicherheit in Erweiterungen](../teil-3-erweiterungsentwickler/sicherheit-in-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
