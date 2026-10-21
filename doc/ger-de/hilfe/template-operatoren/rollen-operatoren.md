---
title: Rollen-Operatoren
audience: developer, designer, app-creator
category: development
order: 260
summary: Die 7 Operatoren von eZTemplateRoleOperator (has_role, has_role_id, has_any_role, has_role_by_user, has_role_by_user_id, has_policy, has_policy_by_user) - was sie prüfen, warum die gepipte Eingabe ignoriert wird, warum sie keine Zugriffsprüfung sind, mit nur lesenden Beispielen, auf alpha als anonymer Benutzer gerendert.
keywords: Rolle, Richtlinie, Berechtigung, Zugriffsprüfung, anonymer Benutzer, has_role, has_role_id, has_any_role, has_role_by_user, has_role_by_user_id, has_policy, has_policy_by_user, role, policy, permission, eZRole, eZTemplateRoleOperator
type: help
---
# Rollen-Operatoren

Diese Operatoren fragen, ob ein Benutzer eine Rolle hat oder eine Richtlinie für ein Modul und eine Funktion, damit ein
Template Teile einer Seite je nach Rolle zeigen oder verbergen kann. Sie werden von `eZTemplateRoleOperator`
(`lib/eztemplate/classes/eztemplateroleoperator.php`) über `lib/eztemplate/classes/eztemplateautoload.php` registriert.
Alle geben einen booleschen Wert zurück und lesen die Rollen mit `eZUser::roles()`, das auch die Rollen einschließt, die
der Benutzer über Benutzergruppen erhält.

Die Beispiele wurden von einem Kommandozeilenlauf gerendert, der nicht angemeldet ist; der aktuelle Benutzer ist also der
anonyme Benutzer (auf alpha die ID 10) mit der Rolle "Anonymous" (ID 1). Es wurde nichts verändert.

```tpl
{def $me = fetch( 'user', 'current_user' )}
current user: id {$me.contentobject_id}, roles: {foreach $me.roles as $r}{$r.id} {$r.name}{delimiter}, {/delimiter}{/foreach}
```

Ausgabe auf alpha:

```text
current user: id 10, roles: 1 Anonymous
```

## Inhalt

1. [Zwei Regeln, bevor Sie sie verwenden](#zwei-regeln-bevor-sie-sie-verwenden)
2. [has_role, has_role_id und has_any_role](#has_role-has_role_id-und-has_any_role)
3. [has_role_by_user und has_role_by_user_id](#has_role_by_user-und-has_role_by_user_id)
4. [has_policy und has_policy_by_user](#has_policy-und-has_policy_by_user)
5. [Verwandte Seiten](#verwandte-seiten)

## Zwei Regeln, bevor Sie sie verwenden

**Die gepipte Eingabe wird ignoriert.** `modify()` liest nur die Parameter. Der Kommentar am Anfang der Klasse zeigt
Formen wie `$current_user|has_role( 'Administrator' )`, aber der Wert vor dem `|` spielt keine Rolle: Der Operator prüft
den Benutzer, der mit `user_id` angegeben ist, und ohne ihn den **aktuellen** Benutzer. `$someone|has_role( ... )`
antwortet also stillschweigend für den Besucher, nicht für `$someone`:

```tpl
'any value'|has_role( 'Anonymous' ) = {'any value'|has_role( 'Anonymous' )|choose( 'false', 'true' )}
```

Ausgabe auf alpha:

```text
'any value'|has_role( 'Anonymous' ) = true
```

Verwenden Sie die Aufrufform und übergeben Sie den Benutzer ausdrücklich, wenn Sie einen anderen Benutzer meinen.

**Sie sind keine Zugriffsprüfung.** Eine Rolle oder eine Richtlinie sagt nichts über ihre Einschränkungen aus (Bereiche,
Teilbäume, Klassen, Besitzer). `has_policy( 'content', 'edit' )` ist auch für einen Benutzer wahr, der nur einen einzigen
Ordner bearbeiten darf. Um zu entscheiden, ob der Benutzer mit einem bestimmten Objekt etwas tun darf, verwenden Sie die
eigenen Prüfungen des Objekts (`$node.can_edit`, `$node.object.can_read`) oder die Fetch-Funktion `user` `has_access_to`,
und denken Sie daran, dass der Server beim Ausführen der Aktion erneut prüfen muss: Eine verborgene Schaltfläche ist keine
Berechtigung.

## has_role, has_role_id und has_any_role

### has_role

Ob der Benutzer eine Rolle mit genau diesem Namen hat (verglichen mit `==`, Groß- und Kleinschreibung zählt also).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `role_name` | Zeichenkette | ja | `''` |
| `user_id` | Ganzzahl | nein | `0` (der aktuelle Benutzer) |

Generierte Referenz: [has_role](../../beispiele/template-operatoren/has_role.md).

### has_role_id

Dasselbe über die ID der Rolle.

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `role_id` | Ganzzahl | ja | `0` |
| `user_id` | Ganzzahl | nein | `0` (der aktuelle Benutzer) |

Generierte Referenz: [has_role_id](../../beispiele/template-operatoren/has_role_id.md).

### has_any_role

Ob der Benutzer mindestens eine der aufgeführten Rollen hat, oder mit `match_all` alle. Jeder Eintrag ist eine Rollen-ID,
wenn er eine Ganzzahl oder eine Zeichenkette aus Ziffern ist, sonst ein Rollenname.

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `roles` | Array aus Namen und IDs | ja | `array()` |
| `user_id` | Ganzzahl | nein | `0` (der aktuelle Benutzer) |
| `match_all` | boolescher Wert | nein | `false` |

Generierte Referenz: [has_any_role](../../beispiele/template-operatoren/has_any_role.md).

```tpl
has_role( 'Anonymous' ) = {has_role( 'Anonymous' )|choose( 'false', 'true' )}
has_role( 'Administrator' ) = {has_role( 'Administrator' )|choose( 'false', 'true' )}
has_role_id( 1 ) = {has_role_id( 1 )|choose( 'false', 'true' )}
has_any_role( array( 'Editor', 1 ) ) = {has_any_role( array( 'Editor', 1 ) )|choose( 'false', 'true' )}
has_any_role( array( 'Anonymous', 'Editor' ), 0, true() ) = {has_any_role( array( 'Anonymous', 'Editor' ), 0, true() )|choose( 'false', 'true' )}
```

Ausgabe auf alpha:

```text
has_role( 'Anonymous' ) = true
has_role( 'Administrator' ) = false
has_role_id( 1 ) = true
has_any_role( array( 'Editor', 1 ) ) = true
has_any_role( array( 'Anonymous', 'Editor' ), 0, true() ) = false
```

## has_role_by_user und has_role_by_user_id

### has_role_by_user

Ob das übergebene Benutzerobjekt (ein `eZUser`, etwa `fetch( 'user', 'current_user' )`) die Rolle mit dieser ID hat.
Das Objekt muss ein `eZUser` sein: Die Methode liest sein Attribut `contentobject_id`.

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `user` | eZUser-Objekt | ja | `null` |
| `role_id` | Ganzzahl | ja | `0` |

Generierte Referenz: [has_role_by_user](../../beispiele/template-operatoren/has_role_by_user.md).

### has_role_by_user_id

Dasselbe mit der Inhaltsobjekt-ID des Benutzers.

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `user_id` | Ganzzahl | ja | `0` |
| `role_id` | Ganzzahl | ja | `0` |

Generierte Referenz: [has_role_by_user_id](../../beispiele/template-operatoren/has_role_by_user_id.md).

```tpl
{def $me = fetch( 'user', 'current_user' )}
has_role_by_user( $me, 1 ) = {has_role_by_user( $me, 1 )|choose( 'false', 'true' )}
has_role_by_user_id( $me.contentobject_id, 1 ) = {has_role_by_user_id( $me.contentobject_id, 1 )|choose( 'false', 'true' )}
```

Ausgabe auf alpha:

```text
has_role_by_user( $me, 1 ) = true
has_role_by_user_id( $me.contentobject_id, 1 ) = true
```

## has_policy und has_policy_by_user

### has_policy

Ob irgendeine Rolle des Benutzers eine Richtlinie für das Modul und die Funktion hat. Hat die Rollenklasse eine Methode
`hasPolicy()`, wird diese verwendet; sonst werden die Richtlinien nach Modul- und Funktionsname verglichen, wobei `*` in
einer Richtlinie auf alles passt. Einschränkungen werden nicht betrachtet (siehe die Regeln oben).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `module` | Zeichenkette, etwa `content` | ja | `''` |
| `function` | Zeichenkette, etwa `read` | ja | `''` |
| `user_id` | Ganzzahl | nein | `0` (der aktuelle Benutzer) |

Generierte Referenz: [has_policy](../../beispiele/template-operatoren/has_policy.md).

### has_policy_by_user

Dasselbe für ein übergebenes `eZUser`-Objekt; alles andere ergibt false.

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `user` | eZUser-Objekt | ja | `null` |
| `module` | Zeichenkette | ja | `''` |
| `function` | Zeichenkette | ja | `''` |

Generierte Referenz: [has_policy_by_user](../../beispiele/template-operatoren/has_policy_by_user.md).

```tpl
{def $me = fetch( 'user', 'current_user' )}
has_policy( 'content', 'read' ) = {has_policy( 'content', 'read' )|choose( 'false', 'true' )}
has_policy( 'content', 'edit' ) = {has_policy( 'content', 'edit' )|choose( 'false', 'true' )}
has_policy_by_user( $me, 'user', 'login' ) = {has_policy_by_user( $me, 'user', 'login' )|choose( 'false', 'true' )}
```

Ausgabe auf alpha:

```text
has_policy( 'content', 'read' ) = true
has_policy( 'content', 'edit' ) = false
has_policy_by_user( $me, 'user', 'login' ) = true
```

Alle Beispiele lieferten auf alpha interpretiert und kompiliert dieselbe Ausgabe.

## Verwandte Seiten

- [Rollen- und Richtlinien-Operatoren für Designer](../designer/vorlagen/rollen-und-richtlinien-operatoren.md)
- [Richtlinienfunktionen und Einschränkungen](../ersteller/anwendungsentwickler/richtlinienfunktionen-und-einschraenkungen.md)
- [Zugriffskontrolle im Code](../ersteller/anwendungsentwickler/zugriffskontrolle-im-code.md)
- [Alle Template-Operatoren (generiert)](../../beispiele/template-operatoren/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
