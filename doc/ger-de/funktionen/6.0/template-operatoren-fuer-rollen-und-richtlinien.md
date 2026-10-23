# Prüfungen von Rollen und Richtlinien in Templates

Diese Seite ist für Template-Entwickler, die Teile einer Seite je nach Rolle oder Berechtigung zeigen oder verbergen.
Sieben Operatoren tun das in einer Zeile, ohne dass Sie selbst Benutzer holen und über Richtlinien iterieren. Hinzugefügt
im Januar 2026 (Release 6.0.12); eine passende PHP-Methode, `eZRole::hasPolicy()`, lässt Ihren eigenen Code dieselbe
Frage stellen.

## Die sieben Operatoren

Alle Operatoren liegen in der Klasse `eZTemplateRoleOperator`
(`lib/eztemplate/classes/eztemplateroleoperator.php`). Sie geben `true` oder
`false` zurück.

**Der weitergeleitete Wert wird ignoriert.** Ein Operator braucht etwas auf seiner linken Seite, schreiben Sie also
`$current_user|has_role('Editor')`, aber die Antwort hängt nur von den
Parametern ab. Ohne `user_id` wird der **aktuelle Benutzer** geprüft.

| Operator | Parameter (in Reihenfolge) | Wahr, wenn |
|---|---|---|
| `has_role` | `role_name`, optional `user_id` | der Benutzer eine Rolle mit diesem Namen hat |
| `has_role_id` | `role_id`, optional `user_id` | der Benutzer die Rolle mit dieser ID hat |
| `has_any_role` | `roles` (Array von Namen und/oder IDs), optional `user_id`, optional `match_all` | der Benutzer mindestens eine der Rollen hat; mit `match_all=true()` alle |
| `has_role_by_user` | `user` (ein Objekt `eZUser`), `role_id` | dieses Benutzerobjekt die Rolle hat |
| `has_role_by_user_id` | `user_id`, `role_id` | der Benutzer mit dieser ID die Rolle hat |
| `has_policy` | `module`, `function`, optional `user_id` | eine der Rollen des Benutzers eine Richtlinie für dieses Modul und diese Funktion hat |
| `has_policy_by_user` | `user` (ein Objekt `eZUser`), `module`, `function` | dasselbe, für dieses Benutzerobjekt |

In `has_any_role` wird ein Eintrag, der eine Ganzzahl ist (oder eine Zeichenkette aus Ziffern), als Rollen-ID
genommen; alles andere als Rollenname.

## Beispiele

```
{* a block for editors only *}
{if $current_user|has_role('Editor')}
    <a href={'content/edit'|ezurl}>Edit</a>
{/if}

{* administrator or publisher *}
{if $current_user|has_any_role(array('Administrator', 'Publisher'))}
    ...
{/if}

{* must hold both roles, mixing names and ids *}
{if $current_user|has_any_role(array('Editor', 2), 0, true())}
    ...
{/if}

{* does the visitor's role set contain a content/edit policy? *}
{if $current_user|has_policy('content', 'edit')}
    <a href={concat('content/edit/', $node.contentobject_id)|ezurl}>Edit this</a>
{/if}

{* a specific other user, by id *}
{if has_role_by_user_id(42, 1)}
    ...
{/if}
```

(Der Parameter `user_id` ist der zweite von `has_any_role`, daher übergibt das dritte Beispiel `0` für „den aktuellen
Benutzer“ und setzt `match_all` auf true. Das letzte Beispiel fragt nach Benutzer 42 und Rolle 1: `has_role_by_user_id`
nimmt beide als Parameter; ein vorangestellter Wert wie in `42|has_role_by_user_id(1)` wird wie überall ignoriert, diese
Form fragt also nach Benutzer 1 und Rolle 0 und ist immer false. `$current_user` setzt das Pagelayout; in anderen
Templates definieren Sie ihn mit `{def $current_user = fetch( 'user', 'current_user' )}`.)

## In PHP verwenden

```php
$role = eZRole::fetchByName( 'Editor' );
if ( $role && $role->hasPolicy( 'content', 'edit' ) )
{
    // the role has a content/edit policy
}
```

`eZRole::hasPolicy( $moduleName, $functionName = false )` gibt true zurück, wenn die Rolle eine Richtlinie für das Modul
und, wenn `$functionName` angegeben ist, für diese Funktion hat. Mit `false` antwortet sie für jede Funktion des Moduls.

## Die Grenzen kennen

- `has_policy` liest die **Richtlinienzeilen** der Rollen. Er sagt „dieser Benutzer hat eine Rolle mit einer Richtlinie
  content/edit“; er wertet keine Einschränkungen aus (Sektionen, Teilbäume, Eigentümer), eine auf eine Sektion
  eingeschränkte Richtlinie content/edit zählt also. Eine Richtlinie für alle Module (`*`, wie sie die Rolle
  Administrator hat) oder für alle Funktionen eines Moduls (`content/*`) zählt ebenfalls, wie bei der Zugriffsprüfung des
  Kernels (vor dem 23. Oktober 2026 nicht, und der Administrator erhielt false). Die PHP-Methode `eZRole::hasPolicy()`
  vergleicht nur Namen und behandelt `*` nicht so. Um zu fragen, ob der Benutzer einen bestimmten Knoten bearbeiten
  darf, verwenden Sie stattdessen das Attribut `can_edit` des Knotens.
- Verlassen Sie sich bei Prüfungen, die etwas Sicherheitsrelevantes schützen, nicht auf das Verbergen eines Links; die
  Modulansicht prüft die Berechtigungen trotzdem, Verbergen ist eine Bequemlichkeit.
- Operatoren werden bei jeder Verwendung ausgewertet; halten Sie sie aus langen Schleifen heraus und legen Sie die
  Antwort mit `{def $is_editor = $current_user|has_role('Editor')}` in einer Variablen ab.

## Geschichte

Die erste Version (10. Januar 2026) hieß `member_of_role`, mit ihren Geschwistern (`member_of_role_id`,
`member_of_any_role`, `member_of_role_by_user`, `member_of_role_by_user_id`) in einer Klasse
`eZTemplateMemberOfRoleOperator`. Die Richtlinien-Operatoren kamen am selben Tag hinzu, die Klasse wurde am 11. Januar in
`eZTemplateRoleOperator` umbenannt, und die Operatoren erhielten die Namen mit `has_`. Die Namen `member_of_*` waren nie
Teil eines Releases; wenn Sie ein Beispiel aus einem Entwicklungsstand kopiert haben, ersetzen Sie `member_of_` durch
`has_`.

Bis zum 18. Oktober 2026 war die Klasse nicht bei der Template-Engine registriert, sodass ein Template, das einen der
Operatoren verwendete, „unknown operator“ erhielt. Seitdem registriert `lib/eztemplate/classes/eztemplateautoload.php`
alle sieben; `eZTemplateRoleOperatorRegistrationTest` prüft die Registrierung und gibt jeden Operator aus.

Seit dem 23. Oktober 2026 zählen `has_policy` und `has_policy_by_user` Richtlinien für alle Module oder Funktionen
(`*`), `has_role_by_user` antwortet false, statt abzubrechen, wenn er kein Benutzerobjekt erhält (wie es
`has_policy_by_user` schon tat), und eine als Zeichenkette übergebene `user_id` (`'0'`, `$node.object.owner_id`) wird als
Zahl gelesen. `eZTemplateRoleOperatorLiveTest` gibt jedes Beispiel dieser Seite aus, interpretiert und kompiliert, für
den anonymen Benutzer, den Benutzer admin und einen Benutzer mit zwei Testrollen.

Die Idee stammt von der Community-Extension `bcmemberofrole`.

## Verwandte Seiten

- [Rollen: Richtlinien-IDs, Sortierung und Schaltflächen für die Reihenfolge](reihenfolge-der-richtlinien-einer-rolle.md), [Blättern in den Bildschirmen für Rollen und Richtlinien](../../bc/6.0/seitenweise-anzeige-von-rollen-und-richtlinien.md)
- [Operatoren für Zeichenketten](template-operatoren-fuer-zeichenketten.md)
- [Changelog 6.0.12](../../../changelogs/6.0/6.0.12.md)
- [Chronik: Januar 2026](../../geschichte/2026/2026-01.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
