---
title: Rollen- und Richtlinienoperatoren
audience: designer, developer
category: templates
order: 290
summary: Teile eines Designs nach Rolle oder Berechtigung zeigen oder ausblenden - die Operatorfamilie has_role und has_policy, die Fetch-Alternativen user/has_access_to und user/can_open und warum das Ausblenden eines Links nie dasselbe ist wie das Schützen einer Seite.
keywords: has_role, has_policy, has_any_role, has_access_to, can_open, Berechtigungen in Templates, Rollen, Richtlinien, für Redakteure zeigen, für Anonyme ausblenden, Zugriffsprüfung, permissions in templates, roles, policies, show for editors, hide for anonymous, access check
type: help
---
# Rollen- und Richtlinienoperatoren

Diese Seite ist für Designer, die etwas nur manchen Benutzern zeigen: einen Link „Bearbeiten“ für Redakteure, einen Link zum
Mitgliederbereich für angemeldete Mitglieder.

## Inhalt

1. [Die Rollen- und Richtlinienoperatoren](#die-rollen--und-richtlinienoperatoren)
2. [Die Fetch-Alternativen](#die-fetch-alternativen)
3. [Berechtigungen an Knoten](#berechtigungen-an-knoten)
4. [Zeigen ist nicht schützen](#zeigen-ist-nicht-schützen)
5. [Caching](#caching)

## Die Rollen- und Richtlinienoperatoren

Sieben Operatoren von `eZTemplateRoleOperator` (`lib/eztemplate/classes/eztemplateroleoperator.php`), beschrieben in
[Rollen- und Richtlinienprüfungen in Templates](../../../funktionen/6.0/template-operatoren-fuer-rollen-und-richtlinien.md):

```tpl
{if $current_user|has_role( 'Editor' )} ... {/if}
{if $current_user|has_policy( 'content', 'edit' )} ... {/if}
{if $current_user|has_any_role( array( 'Editor', 'Administrator' ) )} ... {/if}
```

Der per Pipe übergebene Wert wird ignoriert; ohne den Parameter `user_id` wird der aktuelle Benutzer geprüft.

> **Hinweis**
> Auf manchen Installationen waren diese Operatoren nicht bei der Template-Engine registriert, sodass ein Template, das sie
> verwendete, die Warnung „Operator 'has_role' is not registered“ bekam; eine Korrektur der Registrierung entsteht für 6.0.15.
> Meldet eine Installation den Operator als unbekannt, verwenden Sie die Fetch-Funktionen unten, die überall funktionieren.

## Die Fetch-Alternativen

```tpl
{if fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'edit' ) )}
    ...
{/if}

{if fetch( 'user', 'can_open', hash( 'uri', 'setup/info' ) )}
    <a href={'setup/info'|ezurl}>...</a>
{/if}
```

(`kernel/user/function_definition.php`: `has_access_to` mit `module`, `function` und einer optionalen `user_id`; `can_open` mit
einer `uri`.)

## Berechtigungen an Knoten

Für einen bestimmten Knoten weiß es der Knoten selbst: `$node.can_edit`, `$node.can_create`, `$node.can_remove`,
`$node.can_read` ([Knoten, Objekte und Attribute](knoten-objekte-und-attribute.md)).

```tpl
{if $node.can_edit}
    <a href={concat( 'content/edit/', $node.contentobject_id )|ezurl}>{'Edit'|i18n( 'design/mytheme' )}</a>
{/if}
```

## Zeigen ist nicht schützen

Einen Link auszublenden, hält niemanden davon ab, die URL zu öffnen. Der Zugriff wird über Rollen und Richtlinien in der
Administration durchgesetzt; Templates entscheiden nur, was gezeigt wird.

## Caching

Ausgabe, die von den Rollen des Benutzers abhängt, muss pro Rollensatz gecacht werden: Der View-Cache variiert schon nach den
Rollen des Benutzers; ein Cache-Block braucht `$current_user.role_id_list|implode( ',' )` in seinen Schlüsseln ([Cache-Block](cache-block.md)).

## Verwandte Seiten

- [Logik- und Vergleichsoperatoren](logik-und-vergleichsoperatoren.md)
- [Benutzer- und Anmelde-Templates](benutzer-und-anmelde-templates.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
