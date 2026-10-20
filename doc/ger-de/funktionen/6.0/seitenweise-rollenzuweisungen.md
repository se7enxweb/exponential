# Die Benutzer und Gruppen einer Rolle, eine Seite auf einmal

Diese Seite ist für Administratoren, deren Rollen vielen Benutzern gegeben sind, und für Entwickler, die `role/view.tpl`
überschreiben.

Eine Rolle kann Tausenden von Benutzern und Benutzergruppen zugewiesen sein, zum Beispiel eine Rolle für jedes Mitglied
eines Vereins. `role/view` lud früher jede dieser Zuweisungen zusammen mit ihrem Inhaltsobjekt, bevor es die Seite
zeichnete, was so lange dauerte, dass die Seite unbrauchbar wurde. Jetzt zeigt es sie seitenweise, nach Namen sortiert,
mit einem Namensfilter, und die Rollenliste sagt, wie viele jede Rolle hat.

## Was Sie sehen

Auf `role/view/<role id>`, unter den Richtlinien:

- Die Überschrift zählt jede Zuweisung der Rolle, eine eingeschränkte (Teilbaum oder Sektion) für sich gezählt.
- Die Liste zeigt eine Seite, sortiert nach dem Namen des Benutzers oder der Gruppe ohne Rücksicht auf Groß- und
  Kleinschreibung, dann nach der Reihenfolge, in der die Zuweisungen gemacht wurden. Eine Seitennavigation unter der
  Liste blättert durch die Seiten.
- Ein Feld **Name enthält** (gezeigt, wenn es mehr als eine Seite gibt oder ein Filter gesetzt ist) behält nur die
  Benutzer und Gruppen, deren Name den Text enthält, ohne Rücksicht auf Groß- und Kleinschreibung. `%` und `_` passen
  nur auf sich selbst. **Alle anzeigen** entfernt den Filter. Die Eingabetaste im Feld filtert; sie öffnet die Rolle
  nicht zum Bearbeiten.
- Eine Zuweisung, deren Benutzer oder Gruppe nicht mehr existiert (ihr Inhaltsobjekt wurde ohne die Zuweisung
  entfernt), wird zuerst aufgeführt als *User or user group no longer exists (object 1234)*, und ein Hinweis über der
  Liste zählt sie, sodass sie sich auswählen und entfernen lassen. Vorher wurde eine solche Zeile als leerer Link
  gezeichnet.
- Eine Einschränkung zeigt den Knoten oder die Sektion, auf die sie zeigt, mit einem Link; eine Einschränkung, deren
  Knoten oder Sektion weg ist, sagt *not found* statt eines leeren Namens.

Die Rollenliste (`role/list`) hat eine Spalte **Zugewiesen** mit der Anzahl der Zuweisungen jeder Rolle; sie verlinkt
auf die Rolle.

Die Richtlinien der Rolle behalten ihre eigene Seitennavigation und Sortierung. Blättern oder Sortieren in einer Liste
lässt die andere, wo sie ist, und **Ausgewählte entfernen**, **Zuweisen** und der Filter kommen an dieselbe Stelle
zurück.

## Die Einstellung

```ini
# settings/override/site.ini.append.php
[RoleSettings]
# How many assignments role/view shows at once (default 50). 0 lists all of them on one page.
AssignmentsPerPage=100
```

Leeren Sie nach der Änderung den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`); unter
Exponential Velocity führen Sie `exp:velocity deploy` aus.

Die übrigen Seitengrößen der Rollenseiten stehen in [Wo die Seitengrößen liegen](../../../eng-us/bc/6.0/pagination-settings.md).

## Die Adresse

| Parameter | Bedeutung |
|---|---|
| `(assignment_offset)/<n>` | Die erste Zuweisung der Seite, ab 0 gezählt. Hinter dem Ende wird die letzte Seite gezeigt; alles, was keine ganze Zahl ist, zeigt die erste. |
| `(assignment_filter)/<text>` | Der Namensfilter, URL-kodiert. `/`, `(` und `)` in einem Filter werden zu Leerzeichen (die Adresse wird an `/` geteilt, und ein ungeordneter Parameter beginnt mit `(`); ein Filter wird auf 100 Zeichen gekürzt. |
| `(policy_offset)`, `(policy_sort)`, `(policy_dir)` | Die Richtlinienliste, wie bisher. |

Beispiel: `/role/view/5/(assignment_offset)/50/(assignment_filter)/editor`.

## Sortierung auf jeder Datenbank

Die Zuweisungen einer Seite kommen aus einer Abfrage, die die Inhaltsobjekte einbezieht, sodass die Datenbank sie
sortiert und die Seiten einer Rolle nie eine Zuweisung auslassen oder wiederholen (Gleichstände entscheidet die ID der
Zuweisung). Zuweisungen, deren Objekt weg ist, kommen zuerst, dann die, deren Objekt einen leeren Namen hat, dann die
übrigen nach Namen.

| Datenbank | Groß- und Kleinschreibung von a-z | Akzente, Umlaute, Satzzeichen |
|---|---|---|
| MySQL / MariaDB | zusammen | die Kollation der Spalte (`utf8mb4_general_ci`: „Müller“ neben „Muller“, `_` nach den Buchstaben) |
| PostgreSQL | zusammen | die Kollation der Datenbank |
| SQLite | zusammen | nach ihren Bytes (`LOWER()` von SQLite wandelt nur a-z um) |
| Oracle | zusammen | die Sortierfolge der Sitzung; ein leerer Name wird als null gespeichert und sortiert trotzdem zuerst |
| MongoDB | zusammen | in PHP nach den Bytes des kleingeschriebenen Namens sortiert (`mb_strtolower`) |

Der Name, nach dem sortiert und gefiltert wird, ist der mit dem Objekt gespeicherte (`ezcontentobject.name`, der Name in
seiner Hauptsprache). Die Liste zeigt den Namen in der Sprache der Administrationsoberfläche, der für fast jeden Benutzer
und jede Gruppe derselbe ist.

## Kosten

Eine Seite kostet dieselben wenigen Abfragen, gleich, wie groß sie ist und wie viele Zuweisungen die Rolle hat: die Seite
der Zuweisungen, ihre Objekte, ihre Hauptknoten, die Knoten ihrer Teilbaum-Einschränkungen und die Anzahlen. Auf dem
Demonstrationsserver brauchte eine Rolle mit 114 Zuweisungen vorher 106 Abfragen, um `role/view` zu zeichnen, und braucht
jetzt 10, für eine Seite mit 50.

Zwischen Anfragen wird nichts aufbewahrt, sodass sich die Seiten unter PHP-FPM und auf den dauerhaften Workern von
Exponential Velocity gleich verhalten.

## Für Entwickler

| Methode | Was sie zurückgibt |
|---|---|
| `eZRole::assignmentCount( $filter = '' )` | Die Anzahl der Zuweisungen oder derer, deren Name `$filter` enthält. |
| `eZRole::assignmentOrphanCount()` | Die Anzahl der Zuweisungen, deren Inhaltsobjekt weg ist. |
| `eZRole::assignmentPage( $offset, $limit, $filter = '' )` | Eine Seite, in der Form von `fetchUserByRole()` (`user_object`, `user_role_id`, `limit_ident`, `limit_value`) plus `user_id`, `user_name`, `main_node_id`, `limit_node` und `limit_section`. `user_object` ist `null` für ein verschwundenes Objekt. |
| `eZRole::assignmentRows( $offset, $limit, $filter = '' )` | Dieselbe Seite als einfache Zeilen, ohne Objekte. |
| `eZRole::assignmentCounts( $roleIDs )` | Rollen-ID => Anzahl der Zuweisungen, in einer Abfrage. |

`fetchUserByRole()` ist unverändert und gibt weiterhin jede Zuweisung mit ihrem Objekt zurück, für die
Berechtigungsprüfungen und alles andere, was sie alle braucht.

Die Templates erhalten `$user_array` (eine Seite), `$assignment_total` (jede Zuweisung), `$assignment_count` (die, die
der Filter behält; ohne Filter alle), `$assignment_orphan_count`, `$assignment_limit`, `$assignment_offset`,
`$assignment_filter` und die Adressteile `$policy_uri_suffix`, `$assignment_uri_suffix` und
`$assignment_filter_uri_suffix`. `role/list.tpl` erhält `$assignment_counts`.

Ein Design, das `role/view.tpl` überschreibt, zeigt nur die erste Seite, bis es eine Seitennavigation hinzufügt; siehe
die [Verhaltensänderungen](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md). `design/admin/templates/role/view.tpl` ist das
Beispiel zum Kopieren.

## Tests

- `tests/tests/kernel/classes/eZRoleAssignmentSortTest.php`: Sortieren und Filtern in PHP, der Filter in der Adresse und
  in SQL, die Adressteile der Seite. Ohne Datenbank.
- `tests/tests/kernel/classes/eZRoleAssignmentPageLiveTest.php`: Anzahlen, Seiten, Reihenfolge, ein verschwundenes
  Objekt, der Filter, die Anzahlen der Rollenliste und die Anzahl der Abfragen, auf einer Installation (ohne eine
  übersprungen).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
