# Entfernen und Verstecken: die Prüfung umgekehrter Beziehungen

Bevor Inhalte entfernt oder versteckt werden, kann die Administrationsoberfläche die anderen Objekte auflisten, die
auf sie verweisen: Artikel, die ein Bild einbetten, Seiten, die in ihrem Rich Text auf eine Datei verlinken, Objekte,
deren Beziehungsfeld sie nennt. Wer solche Inhalte entfernt, lässt diese Verweise ins Leere zeigen; wer sie versteckt,
lässt sie auf Inhalte zeigen, die die Website nicht mehr anzeigt. Die Prüfung macht dies sichtbar, bevor es geschieht,
und verweigert auf Wunsch das Entfernen.

## Einstellungen

Beide Einstellungen stehen in `content.ini`.

| Einstellung | Werte | Standard | Wirkung |
|---|---|---|---|
| `[RemoveSettings] ReverseRelationCheck` | `disabled`, `warn`, `block` | `warn` | content/removeobject, auch dort, wo es als content/removeuserobject und content/removemediaobject erreicht wird |
| `[HideSettings] ReverseRelationCheck` | `disabled`, `warn` | `warn` | content/hide und **Auswahl verstecken** in der Liste der Unterelemente |
| `[ReverseRelationSettings] ProviderClassList[]` | Klassennamen | im Kernel keine; explayouts fügt seine eigene hinzu | Verweise, die außerhalb von `ezcontentobject_link` geführt werden (Layouts-Blöcke) |

Eine fehlende Einstellung nimmt den Standardwert an; ein unbekannter Wert ebenfalls, und es wird eine Warnung ins
Debug-Log geschrieben. `block` unter `[HideSettings]` wird als `warn` behandelt: Ein Verstecken wird nie verweigert.

### Entfernen

- `disabled`: keine Prüfung. Die Bestätigungsseite ist die früherer Releases, mit ihrem allgemeinen Hinweis, unter
  **Fortgeschritten > Verknüpftes für Baum umdrehen** nachzusehen.
- `warn`: Die Bestätigungsseite listet anstelle dieses Hinweises für jedes ausgewählte Element die Objekte auf, die
  auf das Entfernte verweisen. **OK** entfernt wie bisher. Eine Seite ist neu: Wurden alle Orte eines Objekts ohne
  Unterelemente ausgewählt, wurde es bisher sofort entfernt; verweisen andere Objekte darauf, erscheint jetzt zuerst
  die Seite mit der Liste. Mit `HideRemoveConfirmation=true` gibt es keine Bestätigungsseite und damit keine Liste.
- `block`: wie `warn`, und das Entfernen wird verweigert, solange solche Verweise bestehen. Die Schaltfläche **OK**
  ist deaktiviert und nennt den Grund. Die Ansicht prüft beim Absenden der Bestätigung erneut, sodass auch ein ohne
  die Seite abgesendetes Formular (oder eine Seite, die offen blieb, während jemand anderes einen Verweis hinzufügte)
  nichts entfernt; die Seite meldet dann „Es wurde nichts entfernt“. `HideRemoveConfirmation=true` umgeht das nicht:
  Stattdessen wird die Seite angezeigt. Ein umfangreiches Entfernen, das als Inhaltsauftrag laufen würde, wird auf
  dieselbe Weise verweigert, bevor der Auftrag beginnt. Eine Prüfung, die nicht durchgeführt werden kann (ein
  Datenbankfehler), zählt als Verweis.

`warn` ist der Standard, weil es Informationen hinzufügt (und in dem einen oben genannten Fall eine Seite), bevor
Inhalte und ihre Verweise verloren gehen, und nie etwas verweigert; `block` verändert, was ein Redakteur tun kann,
und ist eine Entscheidung für jede Website. Die Sperre selbst liegt auf dem Server und gilt daher in jedem Design;
die Liste und die deaktivierte Schaltfläche **OK** stehen in den Templates admin4, admin und standard von
`node/removeobject.tpl`.

Die Verweigerung (`block`, standardmäßig aus) greift bei jedem Weg, auf dem der Kernel Inhalte aufgrund einer Anfrage
entfernt: die Bestätigung von content/removeobject (auch als content/removeuserobject und
content/removemediaobject), `HideRemoveConfirmation=true`, ein Entfernen als Inhaltsauftrag, das Verschieben in den
Papierkorb (es ist dasselbe Entfernen), das Entfernen der letzten Orte eines Objekts auf dem Reiter **Orte**
(content/action sendet ein Entfernen mit Befunden an content/removeobject, das es auflistet oder verweigert) und ein
Löschen über WebDAV (als verboten abgelehnt). ezjscore und die REST-Schnittstelle des Kernels bieten kein Entfernen.
Skripte, Cronjobs und die direkt aufgerufene Inhalts-API werden nicht geprüft.

### Verstecken

- `disabled`: keine Prüfung; der Knoten wird wie bisher sofort versteckt.
- `warn`: Hat der zu versteckende Knoten Verweise oder hat sein Objekt weitere Orte, listet eine Bestätigungsseite
  diese zuerst auf. Das gilt für jeden Weg des Versteckens in der Administrationsoberfläche: **Verstecken / Zeigen**
  im Kontextmenü, **Verstecken** auf dem Reiter **Orte** der Knotenansicht, ein Link auf content/hide und **Auswahl
  verstecken** in der Liste der Unterelemente (eine Liste pro ausgewähltem Element). **OK** sendet das Verstecken
  erneut mit `ReverseRelationCheckConfirmed`, und es wird ausgeführt. Ein umfangreiches Verstecken bietet auf
  derselben Seite außerdem die Wahl zwischen sofort und einem Inhaltsauftrag. Das Wiedereinblenden eines versteckten
  Knotens wird nie geprüft.

`warn` ist der Standard: Ein Verstecken ohne Verweise und ohne weitere Orte wird wie bisher ausgeführt, und eines,
das solche hat, zeigt zuerst, was es betrifft (aus dem Kontextmenü ist ein Verstecken ein POST ohne eigene Seite).
Setzen Sie `disabled` für das Verhalten früherer Releases.

## Was als Verweis zählt

Eine Zeile von `ezcontentobject_link` zählt, wenn

- ihr Quellobjekt veröffentlicht ist (`status = 1`) und die Zeile zur aktuellen Version der Quelle gehört
  (`from_contentobject_version = current_version`): Verweise aus Entwürfen, aus archivierten Versionen und aus
  Objekten im Papierkorb zählen nicht;
- ihre Art eine der folgenden ist: Objektbeziehung (`common`, 1), Einbettung in Rich Text (`embed`, 2), Link in Rich
  Text (`link`, 4) und Beziehungsfeld (`attribute`, 8). Eine Zeile, deren Arten alle zurückgenommen wurden
  (`relation_type = 0`), zählt nicht;
- ihr Ziel ein Objekt ist, das der Vorgang entfernt, und ihre Quelle nicht.

Ein Objekt wird entfernt, wenn jeder seiner Orte innerhalb der ausgewählten Teilbäume liegt (ein Knoten mit allem
darunter). Ein Objekt, das anderswo einen Ort behält, bleibt erhalten, und die Verweise darauf bleiben gültig; das
Entfernen nur einiger Orte eines Objekts zeigt die Liste daher nie. Aus demselben Grund wird ein Verweis von einem
Objekt, das ebenfalls entfernt wird, nicht aufgeführt.

### Layouts-Blöcke und andere Verweise außerhalb von ezcontentobject_link

Klassen, die in `[ReverseRelationSettings] ProviderClassList[]` aufgeführt sind, fügen Verweise hinzu, die der Kernel
nicht als Beziehungen speichert. Jede hat eine statische Methode `reverseRelations( array $paths, int $limit )`, die
die Pfad-Strings der ausgewählten Teilbäume erhält und `array( 'total' => int, 'rows' => array( ... ) )` zurückgibt
(jede Zeile: `to_node_id`, `to_object_id`, `to_name`, `to_path`, `label`, `type`, `url`, `readable`), oder `false`,
wenn sie nicht prüfen konnte (die Prüfung gilt dann als fehlgeschlagen, sodass `block` verweigert). Eine Klasse, die
nicht existiert, wird übersprungen, sodass ein Prozess, der gestartet wurde, bevor eine Extension ihre Klasse
hinzufügte, weiter funktioniert.

explayouts registriert `expLayoutsReverseRelationProvider` in seiner `content.ini.append.php`: jeden Block eines
veröffentlichten Layouts, dessen Collection einen Knoten innerhalb der Teilbäume nennt (Collection-Einträge enthalten
Knoten-IDs, unabhängig von ihrem Werttyp). Anders als eine Beziehung zählt dies einen Ort, nicht ein Objekt: Der
Block verliert seinen Eintrag, wenn dieser Knoten entfernt wird, auch wenn das Objekt anderswo einen Ort behält. Die
Liste zeigt „Layout: Block“ mit einem Link zum Layout-Editor und den Typ **Layouts-Block**; ein Benutzer ohne
`explayouts/read` sieht ihn nur mitgezählt.

### Die Liste

Die Liste zeigt für jeden Verweis das verweisende Objekt (ein Link auf seinen Hauptort), seine Klasse, die Art des
Verweises und bei einem Beziehungsfeld den Namen des Feldes sowie das Objekt, auf das er zeigt („Dieses Element“ für
das ausgewählte, sonst den Namen des Objekts darunter).

Beschrieben wird nur, was der aktuelle Benutzer lesen darf. Ein Verweis von einem Objekt, das der Benutzer nicht
lesen darf (wegen Sektion, Status, Teilbaum oder Klasse; die Leseberechtigung wird einmal für alle Objekte der Liste
geprüft), wird nicht aufgeführt; das Element zählt sie: „1 Verweis, den Sie nicht sehen dürfen“, „3 Verweise, die
Sie nicht sehen dürfen“. Dasselbe gilt für weitere Orte, die der Benutzer nicht lesen darf („2 weitere Orte, die Sie
nicht sehen dürfen“). Ein Ziel, das der Benutzer nicht lesen darf, wird als „Ein Objekt, das Sie nicht lesen dürfen“
bezeichnet. Kein Name, keine Klasse und keine ID solcher Objekte gelangt in die Seite, in ein Log oder in einen
Audit-Eintrag; die Prüfung hat keine JSON- oder AJAX-Ausgabe.

Die Zählung lässt sich nicht vermeiden. Die Prüfungen zählen jeden Verweis, sodass `block` ein Entfernen auch dann
verweigert, wenn der Benutzer keinen davon sieht, und ein Verstecken auch dann zuerst nachfragt, wenn jeder Verweis
von einem Objekt stammt, das der Benutzer nicht lesen darf; ohne den Satz könnte die Seite nicht erklären, warum, und
mit der Zahl weiß der Redakteur, wie viel er bei einem Administrator erfragen muss. Sie teilt dem Benutzer mit, wie
viele solche Verweise es pro ausgewähltem Element gibt, nie welche.

Höchstens 100 Verweise werden pro Seite gelesen; die Gesamtzahl wird vollständig gezählt, und wenn mehr existieren,
als gelesen werden, sagt die Seite das in einem festen Satz, ohne Zahl.

Nicht abgedeckt: Links auf einen Ort (`eznode://`) im Rich Text werden als Beziehung zum Objekt des Orts gespeichert
und zählen nur, wenn dieses Objekt entfernt wird. URL-Aliase und URL-Platzhalter, die einen Knoten nennen, sind keine
Verweise (das System entfernt sie mit dem Knoten oder leitet sie um). Andere Block-Parameter von Layouts als
Collections werden nicht gelesen. Nicht geprüft: die Inhalts-API und direkt aufgerufene Operationen, Skripte und
Cronjobs sowie das Leeren des Papierkorbs (der Inhalt wurde geprüft, als er in den Papierkorb kam).

Leistung: zwei Anweisungen über `ezcontentobject_link` (eine Zählung und höchstens 100 Zeilen), eine über die
Zielorte, eine pro Provider (eine Zählung und höchstens 100 Zeilen), die ausgewählten Knoten in einem Abruf, die
Leseberechtigung aller genannten Objekte in einem Abruf, Klassen- und Feldnamen einmal pro Anfrage. Das Entfernen
vieler Elemente oder eines Teilbaums kostet dieselbe Zahl von Anweisungen; der Teilbaum wird mit `path_string LIKE`
abgeglichen, was der Index von `ezcontentobject_tree` bedient.

## Implementierung

- `Exponential\Service\ReverseRelationCheck` (`kernel/private/classes/services/reverserelationcheck.php`): die
  Modi, die Abfrage (zwei SQL-Anweisungen über `ezcontentobject_link`, `ezcontentobject` und
  `ezcontentobject_tree`, mit jeder Datenbank-Engine des Kernels) und die Beschreibung für die Seite.
- `content/removeobject`, `content/hide` und die Aktion HideButton von `content/action` rufen sie auf. Das Entfernen
  verweigert im Modus `block` vor jedem Weg, auf dem es entfernt (der Inhaltsauftrag, die Bestätigung und das direkte
  Entfernen von Orten). Verschachtelte Auswahlen werden einmal abgefragt, über den äußeren Teilbaum.
- Templates: `design/standard/templates/content/reverse_relation_check.tpl` (die Liste), eingebunden von
  `node/removeobject.tpl` (admin4, admin und standard) und `content/job_confirm.tpl` (admin4, admin, standard). Die
  Texte stehen im Übersetzungskontext `design/admin/content/reverserelations`.
- Berechtigungen, Formular-Tokens und Audit-Einträge sind die der Operationen selbst: Die Bestätigungsseiten senden
  mit dem Formular-Token, wo `ezformtoken` aktiv ist, und nichts wird geschrieben, bevor die Operation läuft.
- Tests: `expReverseRelationCheckTest` (Modi, Entscheidungen, die Abfrage auf SQLite, die Reihenfolge der Prüfungen
  in den Ansichten, die Provider, der Reiter **Orte** und WebDAV) und `AdminReverseRelationCheckTest` (die Seiten in
  admin4, die Zählungen, die Layouts-Zeile, die Übersetzungen); explayouts:
  `tests/explayoutsreverserelationprovidertest.php`.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
