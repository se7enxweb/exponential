# Seitenweise Anzeige der Bildschirme für Rollen und Richtlinien

Lesen Sie diese Seite, wenn Ihre Installation Rollen mit vielen Richtlinien hat (eine große Multi-Site-Installation
hat das oft), wenn Sie die Templates für Rollen oder Richtlinien überschreiben oder wenn Ihr Code `policyList()` liest
und die Reihenfolge nach Modul erwartet. Die Bildschirme für Rollen und Richtlinien blättern und sortieren jetzt in der
Datenbank, so wie es der [Tab Orte](tab-orte-blaettern-und-sortieren.md) tut, und der
Rolleneditor kann Richtlinien neu anordnen.

Eine Rolle kann beliebig viele Richtlinien tragen, auf einer großen Installation Hunderttausende. Die Bildschirme, die
sie zeigten, luden jede Richtlinie auf einmal, und bei einer großen Rolle ging der Seite der Speicher aus, bevor die
erste Zeile geschrieben war.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | `role/edit` und `role/view` zeigen eine Seite mit Richtlinien (`site.ini [RoleSettings] PoliciesPerPage`, Standard `25`) mit `(policy_offset)`, sortierbaren Spaltenköpfen ID, Modul, Funktion und Einschränkungen und, im Editor, Schaltflächen nach oben und nach unten. `role/list` hat eine Spalte ID und sortierbare Spaltenköpfe. Das Fenster mit den Richtlinien eines Benutzers zeigt pro Rolle eine begrenzte Vorschau (`PolicyPreviewPerRole`, Standard `10`). Neu sind `eZRole::policyCount()`, `policyPage()`, `movePolicy()`; die Fetches `role/policy_count` und `role/policies`. |
| Wer betroffen ist | Code, der sich darauf verlässt, dass `policyList()` Richtlinien nach Modul und Funktion zurückgibt: Es gibt sie jetzt in der Reihenfolge der IDs zurück. Overrides von `role/list.tpl`, `role/edit.tpl`, `role/view.tpl`, `policies.tpl` und `tabs/user/policies.tpl`. |
| Wie Sie es prüfen | `grep -n "function policyCount\|function policyPage\|function movePolicy\|function sortColumnsForPolicyList\|function sortColumnsForList" kernel/classes/ezrole.php` |
| Wie Sie es beheben | Sortieren Sie in Ihrem eigenen Code, wenn Sie die Reihenfolge nach Modul brauchen. Führen Sie Ihre Template-Overrides mit den ausgelieferten zusammen. Ein Datenbank-Update ist nicht nötig: `ezpolicy` ist unverändert. |

Die Berechtigungen ändern sich nicht: `eZRole::accessArray()` liest die Reihenfolge nicht (siehe
[Das Berechtigungssystem liest die Reihenfolge nicht](#das-berechtigungssystem-liest-die-reihenfolge-nicht)).

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `site.ini` | `[RoleSettings]` | `PoliciesPerPage` | `25` | global oder Siteaccess |
| `site.ini` | `[RoleSettings]` | `PolicyPreviewPerRole` | `10` | global oder Siteaccess |
| `site.ini` | `[RoleSettings]` | `RolesPerPageList[]` | `10`, `25`, `50` (die Auswahl der Rollenliste; die gespeicherte Benutzereinstellung ist die Position in dieser Liste) | global oder Siteaccess |

## Was falsch war

### `role/edit` und `role/view`

Beide taten Folgendes:

```php
$policies = $role->attribute( 'policies' );
```

`policyList()` holt **jede** Richtlinie der Rolle, ohne Limit, und behält sie am Objekt. Für das Berechtigungssystem
ist das richtig — es muss alle sehen, um eine Frage zu beantworten —, und für einen Bildschirm, der fünfundzwanzig
zeigt, falsch. Bei einer Rolle mit Millionen von Richtlinien erschöpft die Seite den Speicher, bevor die erste Zeile
geschrieben ist.

Die Überschrift rief dann `$policies|count` auf, was nur kostenlos ist, weil die ganze Liste schon vorlag — genau die
Kosten, die wegmussten.

### `role/list`

Blätterte seine Rollen bereits in der Datenbank — `fetchByOffset()` mit einem echten `LIMIT` und `roleCount()` mit
einem echten `COUNT(*)`. Es hat auch eine Auswahl von 10/25/50 pro Seite, als Benutzereinstellung gespeichert. Was ihm
fehlte, war jede Möglichkeit, die Liste zu ordnen, und die Rollen-ID irgendwo auf dem Bildschirm.

Außerdem führte es bei jeder Ansicht aus:

```php
$tempRoles = eZRole::fetchList( $temporaryVersions = true );
$tpl->setVariable( 'temp_roles', $tempRoles );
```

Das ist eine Zeile pro Rolle, die irgendjemand im Editor geöffnet hat, unbegrenzt, jede mit ihren Richtlinien geladen
— und **kein Template hat `$temp_roles` je verwendet**. Entfernt.

### Das Fenster mit den Richtlinien eines Benutzers oder einer Benutzergruppe

`policies.tpl` war das schlimmste davon:

```
{let assigned_policies=fetch( user, member_of ... )
     assigned_policies=fetch( user, user_role, hash( user_id, ... ) )}
```

`fetch( user, user_role )` baut in PHP das **gesamte Zugriffs-Array** des Benutzers, indem es `accessArray()` aus jeder
Rolle zusammenführt, die er hat — und es wurde nur angefragt, um eine Zahl in die Überschrift zu setzen. Dann lief das
Template für jede zugewiesene Rolle über **jede** Richtlinie dieser Rolle, und jede Zeile fragte die Datenbank nach
ihren Einschränkungen und löste deren Wertnamen auf: unbegrenzt × unbegrenzt, mit einem N+1 darin.


## Was es jetzt tut

### `eZRole::policyCount()` und `eZRole::policyPage()`

```php
$role->policyCount();                 // SELECT COUNT(*), no rows loaded
$role->policyPage( $offset, $limit ); // one page, in the query
```

`policyPage()` speichert sein Ergebnis bewusst **nicht** in `$this->Policies`. Diese Eigenschaft ist für jeden anderen
Aufrufer die ganze Liste, und eine dort hinterlassene Seite wäre für alle stillschweigend falsch.

`policyList()` erhielt `id` als letzten Sortierschlüssel. Ohne ihn werden zwei Richtlinien desselben Moduls und
derselben Funktion in der Reihenfolge geliefert, die die Datenbank zurückgibt, und die darf sich zwischen Abfragen
unterscheiden — eine seitenweise Ansicht davon lässt beim Blättern Zeilen aus und wiederholt andere.

### Fetch-Funktionen

```
{fetch( 'role', 'policy_count', hash( 'role_id', 17 ) )}
{fetch( 'role', 'policies', hash( 'role_id', 17, 'offset', 0, 'limit', 25 ) )}
```

### Die Rollenliste: eine Spalte ID und sortierbare Spaltenköpfe

Die ID ist das, worüber der Rest der Oberfläche eine Rolle anspricht — `/role/view/17`, `/role/edit/17` —, und sie
stand nirgends auf der Seite, die sie auflistet. Sie ist jetzt die zweite Spalte, und sie und Name sortieren:

```
/role/list/(sort)/id/(dir)/desc
```

Sortiert wird in der Datenbank, aus demselben Grund wie überall hier: Die Liste wird seitenweise gezeigt, ein Umordnen
der Zeilen auf dem Bildschirm würde also zehn von wie vielen auch immer sortieren. Die Spalte wird gegen
`eZRole::sortColumnsForList()` geprüft, sodass der Wert direkt aus der Adresse kommen kann; alles andere sortiert nach
Name, wie es immer war. `id` ist der letzte Sortierschlüssel, sodass zwei Rollen mit demselben Namen eine stabile
Reihenfolge behalten und das Blättern keine auslassen oder wiederholen kann.

Die Spaltenköpfe sind `parts/sortheader.tpl` — dieselbe Komponente, die die RSS-Liste und der Tab Orte
verwenden.

#### Eine Falle in eZPersistentObject

`fetchObjectList()` entscheidet die Richtung mit:

```php
if ( $sort_type == "desc" )
```

Das unterscheidet Groß- und Kleinschreibung. `array( 'name' => 'DESC' )` ist daher **aufsteigend**, stillschweigend,
ohne Fehler und ohne Warnung — genau das tat die erste Version davon: Sie kennzeichnete den Spaltenkopf als absteigend,
während die Zeilen aufsteigend zurückkamen. Die Sortierrichtung wird jetzt in Kleinbuchstaben umgewandelt, bevor sie
übergeben wird.

Gut zu wissen, bevor man einen anderen sortierten Aufruf von `fetchObjectList()` schreibt.

### Die Bildschirme

`role/edit` und `role/view` holen eine Seite und eine Anzahl und tragen den Offset als **`(policy_offset)`** — nicht
`(offset)`, damit eine zweite Liste, die später einer der Seiten hinzugefügt wird, nicht mitwandert:

```
/role/view/17/(policy_offset)/25
```

Die Seitengröße steht in `site.ini`:

```ini
[RoleSettings]
PoliciesPerPage=25
PolicyPreviewPerRole=10
```

#### Eine Falle, die man benennen sollte

Das Bearbeiten einer Rolle arbeitet auf einer **temporären Version**, die eine eigene Zeile mit eigener ID ist. Die
erste Version der Seitennavigation baute ihre Links aus `$role.id` und zeigte daher auf `/role/edit/20`, während die
Seite `/role/edit/17` war. Das ist nicht kosmetisch: `/role/edit/<the draft>` bearbeitet den Entwurf direkt, und
Anwenden würde dann in die falsche Zeile zurückschreiben. Die URI der Seite wird jetzt aus PHP übergeben, gebildet aus
der ID in der Adresse.

### Das Fenster mit den Richtlinien

Jede Rolle zeigt ihre ersten `PolicyPreviewPerRole` Richtlinien und dann eine Zeile, die sagt, wie viele es noch gibt,
mit einem Link auf die eigene Seite der Rolle — die seitenweise angezeigt wird. Die Anzahl in der Überschrift kommt aus
`policy_count` pro Rolle, nicht aus dem Aufbau des Zugriffs-Arrays.

Damit ist das Fenster durch *Rollen × Vorschaugröße* begrenzt statt durch *Rollen × Richtlinien*.


## Gemessen

Rolle 17, 401 Richtlinien:

| | vorher | nachher |
|---|---|---|
| `role/edit/17` | alle 401 Zeilen gezeichnet | 25 Zeilen, **0,5 s** |
| `role/view/17` | alle 401 Zeilen gezeichnet | 25 Zeilen, **0,5 s** |
| Überschrift | zählte die vorliegende Liste | `Richtlinien (401)` aus der Datenbank |
| Seitennavigation | keine | `/role/view/17/(policy_offset)/25` |


## Dateien

| Datei | |
|---|---|
| `kernel/classes/ezrole.php` | `policyCount()`, `policyPage()`, stabile Sortierung |
| `kernel/role/ezrolefunctioncollection.php` | `fetchRolePolicies()`, `fetchRolePolicyCount()` |
| `kernel/role/function_definition.php` | `policies`, `policy_count` |
| `kernel/role/edit.php`, `kernel/role/view.php` | Einstiegspunkte; der Code liegt in `kernel/private/classes/views/role/edit.php` und `view.php` (eine Seite, eine Anzahl, `policy_page_uri`) |
| `kernel/role/list.php` (Code in `kernel/private/classes/views/role/list.php`) | Sortierung, und das ungenutzte unbegrenzte Holen temporärer Rollen entfernt |
| `design/admin/templates/role/list.tpl` | die Spalte ID und sortierbare Spaltenköpfe |
| `design/admin/templates/role/edit.tpl`, `role/view.tpl` | Seitennavigation, Anzahl aus der Datenbank |
| `design/admin/templates/policies.tpl` | begrenzte Vorschau pro Rolle |
| `settings/site.ini` | `PoliciesPerPage`, `PolicyPreviewPerRole` |


## Tests

Private Funktionsskripte haben diese Bildschirme mit einer Rolle mit 401 Richtlinien und mit 26 Rollen durchgespielt;
sie werden nicht ausgeliefert. Das geprüfte Verhalten, das Sie auf einer Kopie der Installation von Hand prüfen können:

- `role/edit/<id>` und `role/view/<id>` rendern eine Seite, die Seitennavigation existiert und verwendet
  `(policy_offset)`, die Adresse behält die ID der Rolle statt der des Entwurfs, die Überschrift zählt alle
  Richtlinien, und die letzte Seite lädt.
- `role/list` rendert eine Seite mit zehn Einträgen und einer Seitennavigation auf `(offset)`, die Auswahl pro Seite
  ist intakt, beide Spaltenköpfe sortieren, jede Spalte sortiert in beide Richtungen, die sortierte ist gekennzeichnet,
  die Seitennavigation trägt die Sortierung mit, und ein Sortierwert, der keine Spalte ist, wird ignoriert.

Um für einen Test von Hand viele Richtlinien zu erzeugen, legen Sie eine Wegwerf-Rolle an (fügen Sie der Rolle
Administrator nie Tausende von Richtlinien hinzu: Das ändert die Berechtigungen des Kontos, unter dem alles läuft) und
entfernen Sie sie danach.

Prüfung ohne Schreibzugriff, dass die API vorhanden ist: `grep -n "function policyCount\|function policyPage\|function movePolicy\|function sortColumnsForPolicyList\|function sortColumnsForList" kernel/classes/ezrole.php`.


## Die Reihenfolge der Richtlinien, eine Spalte ID und sortierbare Spaltenköpfe in `role/edit`

Die Liste der Richtlinien im Rolleneditor zeigt die **ID** jeder Richtlinie, sortiert nach den Spaltenköpfen **ID**,
**Modul**, **Funktion** und **Einschränkungen** und hat **Schaltflächen nach oben und nach unten**, die die Reihenfolge
der Richtlinien ändern.

### Wo die Reihenfolge gehalten wird: in den IDs, ohne neue Spalte

Die Richtlinien einer Rolle stehen in der Reihenfolge ihrer IDs. `ezpolicy` hat sich nicht geändert: Es gibt keine
Spalte für die Reihenfolge, und kein Upgrade-Skript ist nötig.

Das funktioniert wegen der Art, wie der Editor ohnehin arbeitet. `role/edit` bearbeitet eine temporäre Version der
Rolle, deren Richtlinien frische Kopien sind, die in der Reihenfolge angelegt werden, die `policyList()` zurückgibt;
Speichern verschiebt diese Zeilen auf die Rolle, und Abbrechen löscht sie. Die IDs sind also die Reihenfolge, und der
Editor schreibt sie ohnehin neu.

`eZRole::movePolicy( $policyID, 'up'|'down' )` tauscht die **Inhalte** einer Richtlinie und ihres Nachbarn: Modul,
Funktion, Einschränkungen (`ezpolicy_limitation.policy_id`) und jede temporäre Kopie, die der Richtlinieneditor von
einer der beiden hat (`original_id`). Beide IDs bleiben, wo sie sind. Die Verschiebung geht in die temporäre Version,
sodass Speichern sie behält und Abbrechen sie verwirft, wie jede andere Änderung im Editor. Eine Richtlinien-ID, die
nicht zu dieser Rolle gehört, wird abgelehnt.

**Was Aufrufer geändert sehen:** `policyList()` und `policyPage()` geben die Richtlinien jetzt in der Reihenfolge der
IDs zurück, nicht nach Modul und Funktion. Täten sie das nicht, würde die nächste temporäre Kopie die Liste wieder
alphabetisch ordnen und die im Editor gesetzte Reihenfolge verlieren. `role/view` zeigt dieselbe Reihenfolge.

Die im Editor angezeigten IDs gehören zur temporären Version: Es sind die IDs, die die Richtlinien nach dem Speichern
haben werden. Das war schon immer so. Jedes Speichern einer Rolle hat ihren Richtlinien neue IDs gegeben.

### Das Berechtigungssystem liest die Reihenfolge nicht

`eZRole::accessArray()` führt die Richtlinien zu `module => function => p_<id> => limitations` zusammen. Jede
Richtlinie gewährt Zugriff für sich, und eine Prüfung ist erfolgreich, wenn irgendeine passt, daher ändern Reihenfolge
und IDs nichts daran, wer was tun darf. Geprüft auf einer Sandbox-Kopie: Das Zugriffs-Array der Rolle hat, ohne die
Richtlinien-IDs, vor und nach einem Umordnen und Speichern denselben Fingerabdruck.

### Sortieren

Sortiert wird in der Datenbank, wie bei `role/list`, weil die Liste seitenweise gezeigt wird. Die Sortierung wird als
`(policy_sort)` / `(policy_dir)` mitgeführt, neben `(policy_offset)`:

```
/role/edit/1/(policy_sort)/module/(policy_dir)/desc
```

Die Adresse des Formulars trägt Offset und Sortierung, sodass eine auf Seite 3 einer sortierten Liste gedrückte
Schaltfläche auf Seite 3 zurückkehrt, weiterhin sortiert. Die Seitennavigation behält die Sortierung ebenfalls.
`eZRole::sortColumnsForPolicyList()` ist die Liste erlaubter Werte: `id`, `module`, `function`, `limitation`.
`limitation` sortiert nach dem ersten Einschränkungsbezeichner der Richtlinie, dann nach der Zahl ihrer
Einschränkungen. Richtlinien ohne Einschränkung kommen zuerst. SQL-Engines tun das mit einer Abfrage mit
`LEFT JOIN … GROUP BY`. MongoDB, das hier keinen Join hat, sortiert in PHP.

Die Schaltflächen nach oben und nach unten werden nur in der eigenen Reihenfolge der Rolle angeboten, also ID
aufsteigend, dem Standard. Bei jeder anderen Sortierung sind sie ausgegraut, und ihr Titel sagt, nach ID zu sortieren.
Bei der ersten Richtlinie gibt es keine Schaltfläche nach oben und bei der letzten keine nach unten. In einer
seitenweisen Liste funktionieren sie über die Seitengrenze hinweg.

`role/view` hat dieselbe Spalte **ID** und dieselben vier sortierbaren Spaltenköpfe, mit denselben Parametern
`(policy_sort)` / `(policy_dir)`. Seine Seitennavigation behält die Sortierung. Es hat keine Schaltflächen für die
Reihenfolge, weil es nur liest. Seine anderen Abschnitte, etwa die Benutzer und Gruppen, denen die Rolle zugewiesen
ist, sind unverändert.

Die Eingabetaste im Namensfeld drückte bisher die erste Absende-Schaltfläche des Formulars. Mit den neuen
Schaltflächen hätte das eine Richtlinie verschoben. Eine versteckte Schaltfläche `ChangeRoleName` steht jetzt als erste
im Formular, sodass die Eingabetaste nur den Namen übernimmt.


## Zwei Kopien des Richtlinienfensters, und nur eine wird gerendert

`design/admin/templates/policies.tpl` und `roles.tpl` werden nur von
`design/admin/override/templates/windows_user.tpl` eingebunden, und **keine ausgelieferte `override.ini` registriert
diese Datei**. Sofern Ihr eigenes Override sie nicht registriert, werden sie nicht verwendet.

Die tatsächlich gerenderten sind `design/admin/templates/tabs/user/policies.tpl` und `roles.tpl` — zusätzliche Tabs,
deklariert in `admininterface.ini` und über `window_controls.tpl` eingebunden. Sie hatten dieselbe unbegrenzte Form,
und in dieser Kopie muss die Korrektur stehen. Beide sind jetzt begrenzt; das tote Paar wurde dazu passend gehalten,
statt es zurückzulassen.

Die Engine klärt, welches welches ist. `Number of unique templates used` im Debug-Bericht listet bei einem **kalten**
Cache jedes gerenderte Template auf — 64 davon für die Knotenansicht einer Benutzergruppe, darunter `locations.tpl` und
`tabs/user/policies.tpl`. Bei einem warmen Cache meldet dieselbe Seite drei, weil nur aufgeführt wird, was frisch
gerendert wurde: Eine gecachte Seite ist kein Beleg dafür, was eine Seite verwendet.


## Verwandte Seiten

- [Blättern, Sortieren und Seitengrößen](../../../eng-us/features/6.0/admin-list-paging.md) und [Wo die Seitengrößen stehen](einstellungen-fuer-seitengroessen.md)
- [Reihenfolge von Rollen und Richtlinien](../../../eng-us/features/6.0/role-policy-order.md) und [Template-Operatoren für Rollen und Richtlinien](../../../eng-us/features/6.0/role-and-policy-template-operators.md)
- [Der Tab Orte: Blättern und Sortieren](tab-orte-blaettern-und-sortieren.md)
- [September 2026, erste Hälfte: 15. September](../../../eng-us/history/2026/2026-09a.md#15-september-paging-everywhere)
- [Januar 2026](../../../eng-us/history/2026/2026-01.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
