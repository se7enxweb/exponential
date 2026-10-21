# Schnellere Zugriffsarrays für Benutzer mit vielen Rollenzuweisungen

Lesen Sie diese Seite, wenn Ihre Installation viele Benutzer mit vielen Rollenzuweisungen hat, zum Beispiel Mitglieder
von Dutzenden oder Hunderten von Teamrooms, deren Mitgliederrollen pro Teamroom-Teilbaum zugewiesen sind, und die erste
Anfrage eines Benutzers nach einer Rollenänderung oder dem Leeren des Caches langsam ist. Sie beschreibt, wie das
Zugriffsarray eines Benutzers jetzt aufgebaut wird, was das gebracht hat und mit welcher Einstellung Sie es abschalten.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Das Zugriffsarray eines Benutzers wird aus den Zeilen aller seiner Rollen aufgebaut, die in drei Abfragen geladen werden, und die Einschränkungen einer Richtlinie werden einmal in ihren Teil des Arrays umgewandelt, nicht einmal pro Zuweisung. |
| Gemessen | Ein Benutzer mit 600 Teilbaum-Zuweisungen (120 Teamrooms × 5 Mitgliederrollen mit je 20 Richtlinien): vorher 3.550 ms und etwa 48.600 Abfragen, jetzt 244 ms und 4 Abfragen (14,6×). |
| Ergebnis | Dasselbe Zugriffsarray in derselben Reihenfolge: dieselben Module, Funktionen, Richtlinien, Einschränkungen und Werte. |
| Einstellung | `site.ini [RoleSettings] AccessArrayPrefetch=enabled` (Standard); `disabled` baut es wie bisher Rolle für Rolle auf. |
| Wer handeln muss | Niemand. |

## Wann das Zugriffsarray aufgebaut wird

`eZUser::hasAccessTo()` prüft jede Richtlinie gegen das Zugriffsarray des Benutzers: alle Richtlinien aller Rollen, die
dem Benutzer und seinen Gruppen zugewiesen sind, jede Zuweisung mit ihrer Einschränkung (Subtree, Section). Das Array
wird pro Benutzer zwischengespeichert (`[RoleSettings] EnableCaching`). Es wird für jeden Benutzer neu aufgebaut, nachdem
sich eine Rolle oder eine Zuweisung geändert hat, nachdem der Benutzer-Cache geleert wurde und wenn sich ein Benutzer zum
ersten Mal anmeldet. Bei einer Installation mit 30.000 Benutzern bedeutet die Änderung einer Mitgliederrolle 30.000
Neuaufbauten, einen bei der ersten Anfrage jedes Benutzers.

## Warum es langsam war

Eine Rolle, die für 120 Teilbäume zugewiesen ist, kommt aus der Datenbank als 120 Rollenobjekte zurück, eines pro
Zuweisung, jedes mit allen seinen Richtlinien. Für den Aufbau des Arrays wurde die Datenbank befragt

- einmal nach den Rollen des Benutzers,
- einmal pro Zuweisung nach den Richtlinien ihrer Rolle,
- einmal pro Richtlinie nach ihren Einschränkungen,
- einmal pro Einschränkung nach ihren Werten und noch einmal nach der Einschränkung, die die Zuweisung hinzufügt,

und jede Zeile wurde in ein Objekt umgewandelt. Für den obigen Benutzer waren das etwa 48.600 Abfragen. Bei einem
Datenbankserver, der über das Netzwerk erreicht wird, ist jede Abfrage ein Roundtrip von einigen Zehntelmillisekunden,
sodass allein die Abfragen dort 10 bis 25 Sekunden dauerten; lokal auf SQLite dauerte der gesamte Aufbau 3,5 Sekunden.

Hinzu kam, dass die Teile einzeln mit `array_merge_recursive()` zusammengeführt wurden, was das wachsende Array für jede
Rolle kopiert: 74 ms der 3,5 Sekunden für diesen Benutzer.

## Was es jetzt tut

1. **Drei Abfragen.** `eZRole::accessArrayByUserID()` lädt die Richtlinien aller Rollen des Benutzers, ihre
   Einschränkungen und die Werte dieser Einschränkungen in drei Abfragen (`eZRole::prefetchAccessRows()`).
   `eZRole::policyList()`, `eZPolicy::limitationList()` und `eZPolicyLimitation::valueList()` nehmen ihre Zeilen von
   dort, solange das Array aufgebaut wird, und befragen zu jeder anderen Zeit wie bisher die Datenbank.
2. **Einmal pro Richtlinie.** Die 120 Zuweisungen einer Rolle unterscheiden sich nur in der Einschränkung, die die
   Zuweisung hinzufügt (`User_Subtree`, `User_Section`) und die `limitationList()` als letzte Einschränkung anfügt.
   Deshalb wird der Teil des Arrays, den die eigenen Einschränkungen der Richtlinie bilden, einmal mit
   `eZPolicyLimitation::limitArray()` berechnet und für jede Zuweisung wiederverwendet; die Einschränkung der Zuweisung
   wird ihm auf dieselbe Weise hinzugefügt (`eZPolicy::prefetchedAccessArray()`). Eine Richtlinie, die selbst eine
   Einschränkung mit dem Bezeichner der Zuweisung hat, nimmt den Weg über `limitationList()`, das eine solche
   Einschränkung eingrenzt.
3. **Eine Zusammenführung.** Die Teile einer Rolle und die Rollen eines Benutzers werden in einem einzigen Aufruf von
   `array_merge_recursive()` zusammengeführt, was dasselbe Array ergibt wie die einzelne Zusammenführung. Das
   beschleunigt auch den Aufbau mit `AccessArrayPrefetch=disabled`.

Die Zeilen werden nur während des Aufbaus des Arrays gehalten und danach verworfen, auch wenn der Aufbau fehlschlägt,
sodass ein persistenter Worker (Velocity) eine spätere Anfrage nie aus ihnen beantwortet. Ein Aufbau innerhalb eines
Aufbaus (eine Extension, die während des Aufbaus das Zugriffsarray eines anderen Benutzers anfordert) erhält eigene
Zeilen und gibt dem äußeren Aufbau dessen Zeilen zurück.

Nur die Rollen, Richtlinien und Einschränkungen, die vorab geladen wurden, werden aus den Zeilen beantwortet; jede andere
befragt wie bisher die Datenbank, auch während ein Array aufgebaut wird. Die Einschränkung, die eine Zuweisung
hinzufügt, wird im Speicher aufgebaut und fragt nichts ab (vorher war das eine Abfrage pro Richtlinie und Zuweisung, die
ohne Zeilen beantwortet wurde).

Jede IN-()-Liste enthält höchstens 500 IDs (`eZRole::PREFETCH_IN_LIST_SIZE`): Oracle lehnt mehr als 1000 ab, und die
Listen eines Benutzers, dessen Rollen mehr Richtlinien oder Einschränkungen haben, werden in mehreren Abfragen geladen.
Alle Zeilen einer Rolle, Richtlinie oder Einschränkung stammen aus einer Abfrage. Schlägt eine Abfrage fehl, wird das
Array Rolle für Rolle aufgebaut. Auf MongoDB, dessen Persistenzschicht keine IN-()-Bedingung kennt, wird das Array
Rolle für Rolle aufgebaut.

| Datenbank | Was abgefragt wird |
|---|---|
| SQLite, MySQL/MariaDB, PostgreSQL, Oracle | `ezpolicy WHERE role_id IN (...) AND original_id = 0 ORDER BY id`, `ezpolicy_limitation WHERE policy_id IN (...) ORDER BY id`, `ezpolicy_limitation_value WHERE limitation_id IN (...) ORDER BY value` |
| MongoDB | Rolle für Rolle, wie bisher |

## Wenn ein Lesevorgang fehlschlägt

Das Zugriffsarray schlägt geschlossen fehl (fail closed). Wenn die Datenbank einen Lesevorgang mit einem Fehler
beantwortet, wird verweigert, was nicht gelesen werden konnte:

| Fehlgeschlagener Lesevorgang | Was das Zugriffsarray erhält |
|---|---|
| Die Rollen des Benutzers (`eZRole::fetchByUser()`) | keine Rollen |
| Die Richtlinien einer Rolle (`policyList()`) | nichts aus dieser Rolle |
| Die Einschränkungen einer Richtlinie (`eZPolicy::limitationList()`) | nichts aus dieser Richtlinie (sie wird als `Disabled` markiert); vorher war sie uneingeschränkt (`*`) |
| Die Werte einer Einschränkung (`eZPolicyLimitation::valueList()`) | nichts aus dieser Richtlinie (die Einschränkung hat `ReadFailed`) |
| Das Vorabladen der Zeilen | das Array wird Rolle für Rolle aufgebaut, was selbst liest und geschlossen fehlschlägt |

`eZRole::accessArrayByUserID()` protokolliert pro Aufbau einen Fehler mit der Anzahl der fehlgeschlagenen Lesevorgänge
und dem ersten davon. `eZUser` speichert keinen Benutzer-Cache, der mit einem fehlgeschlagenen Lesevorgang aufgebaut
wurde (`eZRole::$accessReadFailures` zählt sie), sodass die Verweigerung nur für diese Anfrage gilt und die nächste das
Array neu aufbaut. MongoDB-Lesevorgänge sind nicht abgedeckt: Seine Persistenzschicht beantwortet einen fehlgeschlagenen
Lesevorgang ohne Zeilen.

## Messungen

Jeweils dreimal auf einer lokalen Installation (SQLite, PHP 8.4) aufgebaut, für einen Benutzer, dem 5 Mitgliederrollen
mit 20 bis 22 Richtlinien (jeweils 2 bis 4 Einschränkungen mit 2 bis 4 Werten) für jeweils 120 Teilbäume zugewiesen
sind, also 600 Zuweisungen:

| Verfahren | Zeit pro Aufbau | Abfragen |
|---|---|---|
| Vorher: Rolle für Rolle, einzeln zusammengeführt | 3.550 ms | etwa 48.600 |
| Rolle für Rolle, einmal zusammengeführt (`AccessArrayPrefetch=disabled`) | 3.140 ms | etwa 48.600 |
| Zeilen vorab, Einschränkungen einmal pro Richtlinie, einmal zusammengeführt (`enabled`) | 244 ms | 4 |

Wohin die Zeit des Aufbaus für diesen Benutzer geht, mit vorab geladenen Zeilen, aber noch pro Zuweisung berechneten
Einschränkungen: 2 ms für die 600 Rollenobjekte, 3 ms für die drei Abfragen, 651 ms für den Aufbau der Teile der 600
Zuweisungen (der Schritt, den die Wiederverwendung pro Richtlinie entfernt), 74 ms für die einzelne Zusammenführung
(1,4 ms in einem Aufruf).

Bei Benutzern mit wenigen Rollen ist der Gewinn kleiner: Der anonyme Benutzer einer Demo-Installation (eine Rolle,
12 Richtlinien) braucht 4 statt 25 Abfragen und wird 2,6-mal so schnell aufgebaut.

Eine Alternative wurde ausprobiert und nicht übernommen: eine einzige SQL-Abfrage, die Rollen, Richtlinien,
Einschränkungen und Werte verknüpft, wobei das Zugriffsarray mit eigenem Code aus ihren Zeilen zusammengesetzt wird (wie
es ein älterer Projekt-Patch tat). Sie dauerte für den obigen Benutzer 862 ms, übertrug etwa 67.800 Zeilen und ergab ein
anderes Zugriffsarray, sobald eine Richtlinie eine Einschränkung mit dem Bezeichner der Zuweisung trug: Eine zweite
Implementierung der Berechtigungsregeln entfernt sich von der ersten.

## Dasselbe Zugriffsarray

Das Array ist dasselbe wie vorher, in derselben Reihenfolge (verglichen mit `===`). Die vorab geladenen Zeilen werden in
der Reihenfolge gelesen, in der die einzeln abfragenden Methoden sie lesen: Richtlinien und Einschränkungen nach ID, die
Werte einer Einschränkung nach Wert (die Standardsortierung von `eZPolicyLimitationValue`). Die Objekte werden aus ihnen
wie aus der Datenbank aufgebaut, die Methoden, die sie in das Array umwandeln, sind dieselben, und eine Richtlinie, deren
eigene Einschränkung den Bezeichner der Zuweisung hat, wird wie bisher von `limitationList()` aufgebaut.

Auf dem Demonstrationsserver (SQLite) wurden jeder Benutzer, jeder Benutzer und jede Gruppe mit einer eigenen Rolle sowie Wegwerf-Rollen mit einer Richtlinie jeder
Einschränkungsart auf beide Weisen aufgebaut, mit dem Code vor der Änderung und mit diesem, und mit `===` verglichen:
31 Testobjekte, alle identisch. Die Wegwerf-Rollen trugen Class, Section, Owner, Group, Node, Subtree, Language, eine
Zustandsgruppe, ParentOwner, ParentGroup, ParentClass, ParentDepth, SiteAccess, eine Einschränkung einer Extension (wie
sie Handler für Inhaltseinschränkungen hinzufügen), User_Subtree und User_Section an der Richtlinie selbst, eine
Einschränkung ohne Werte, Werte in falscher Reihenfolge und wiederholt, eine temporäre Bearbeitungskopie einer
Richtlinie, uneingeschränkte und `*`-Richtlinien sowie eine Rolle mit 520 Richtlinien; sie wurden einfach, für
40 Teilbäume und für Sektionen zugewiesen, einem Benutzer, Gruppen, einer verschachtelten Gruppe und zusammen mit den
Gruppen des anonymen Benutzers.

| Aufgebaut auf dem Demonstrationsserver (SQLite, PHP 8.5), Abfrage-Cache aus, schnellster von 5 Aufbauten | Vorher: Abfragen, Zeit | Jetzt: Abfragen, Zeit |
|---|---|---|
| Administrator (1 Rolle) | 3, 0,2 ms | 3, 0,2 ms |
| Anonym (1 Rolle) | 27, 1,3 ms | 4, 1,6 ms |
| Ein Redakteur (3 Rollen) | 156, 12,5 ms | 4, 3,0 ms |
| Benutzer in Gruppen, 50 Zuweisungen von 2 Rollen | 1.843, 130 ms | 4, 15 ms |
| 520 Richtlinien, 41-mal zugewiesen | 84.802, 7.057 ms | 7, 850 ms |
| Alle 31 Testobjekte | 177.651, 14,3 s | 104, 1,6 s |

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `settings/site.ini` | `RoleSettings` | `AccessArrayPrefetch` | `enabled` | `disabled` baut das Zugriffsarray Rolle für Rolle auf und befragt die Datenbank für jede Rolle, Richtlinie und Einschränkung. |

Beide Verfahren ergeben dasselbe Array, sodass die mit dem einen aufgebauten Benutzer-Caches auch unter dem anderen
gültig bleiben.

## Tests

- `eZPolicyPrefetchedAccessArrayTest` (ohne Datenbank): Der einmal berechnete und wiederverwendete Teil einer Richtlinie
  ergibt dasselbe Array wie `limitationList()` aus denselben Zeilen, uneingeschränkt, mit Einschränkungen, für einen
  Teilbaum oder eine Sektion zugewiesen, für eine Richtlinie mit einer Einschränkung mit dem Bezeichner der Zuweisung,
  und die Zusammenführung in einem Aufruf ergibt, was die einzelne Zusammenführung ergab. Eine nicht vorab geladene
  Richtlinie oder Einschränkung befragt die Datenbank; die Einschränkung, die eine Zuweisung hinzufügt, fragt nichts ab;
  ein Aufbau innerhalb eines Aufbaus gibt dem äußeren seine Zeilen zurück; die IN-()-Listen bleiben unter dem Limit von
  Oracle.
- `eZRoleAccessArrayPrefetchLiveTest` (auf einer Installation): beide Verfahren für jeden Benutzer und jede
  Benutzergruppe, identisch mit `===`, mit einer eigenen Rolle, die für 40 Teilbäume und für eine Sektion zugewiesen
  ist; die geladenen Zeilen werden danach verworfen.
- `eZAccessArrayFailClosedTest` (ohne Datenbank): Ein Stub beantwortet jeden Lesevorgang mit einem Fehler; Rollen,
  Rollen-IDs, Richtlinien, Einschränkungen und Werte, die nicht gelesen werden können, verweigern, mit vorab geladenen
  Zeilen und Rolle für Rolle, und nichts wird gezählt, wenn jeder Lesevorgang beantwortet wird.

## Verwandte Seiten

- [Inhaltseinschränkungen von Extensions](handler-fuer-inhaltseinschraenkungen.md)
- [Velocity-Antwort-Cache](velocity-antwort-cache.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
