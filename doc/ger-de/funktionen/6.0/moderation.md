# Neue Inhalte: moderieren, was erstellt und geändert wurde (6.0.15)

Lesen Sie diese Seite, wenn Besucher Inhalte auf Ihrer Website einreichen (Kommentare, Bewertungen, Forenbeiträge,
Formulare, die Objekte anlegen), wenn Redakteure einen Ort brauchen, an dem sie sehen, was sich zuletzt geändert hat,
oder wenn Sie Rollen für Moderatoren einrichten.

**Dashboard > Neue Inhalte** (`moderation/recent`, der zweite Link des Dashboard-Menüs, nach *Dashboard*) listet die
zuletzt erstellten und geänderten Objekte der ganzen Installation auf, die neuesten zuerst, und lässt einen Moderator
auf eine Auswahl davon einwirken.

## Die Liste

| Spalte | Was sie zeigt |
|---|---|
| Geändert, Erstellt | die letzte Änderung des Objekts und seine erste Veröffentlichung (*noch nicht* bei einem Entwurf) |
| Name, Klasse | verlinkt auf den Hauptort des Objekts; seine ID |
| Status | Veröffentlicht, Entwurf (nie veröffentlicht) oder Im Papierkorb; Schwebend (eine Version wartet auf eine Freigabe oder ein Datum); Versteckt; Zur Prüfung zurückgehalten (*Zurückgehalten: neue Einreichung*, wenn das [automatische Zurückhalten](zurueckhaltung-zur-pruefung.md#automatische-zurückhaltung) es zurückgehalten hat), mit dem Grund und wer es veranlasst hat |
| Veröffentlichung | die Version, die veröffentlicht, in Bearbeitung oder schwebend ist, und **Geplant für** das Datum eines Workflow-Ereignisses *Wait until date* |
| Sektion, Ersteller | der Ersteller, verlinkt auf sein Benutzerkonto |
| Herkunft der Einreichung | die Adresse, der Browser und der Siteaccess, von dem die Einreichung kam (nur mit `moderation/personaldata`, siehe unten) |

Filter: ein Datumsbereich für eines der beiden Daten, die Klasse, der Status, zurückgehalten oder nicht, versteckt oder
sichtbar, die Sektion, der Siteaccess, von dem die Einreichung kam, und der Ersteller (ein Login oder eine
Benutzer-ID). Die Spaltenköpfe Geändert, Erstellt, Name, Klasse, Sektion und Ersteller sortieren die Liste; ein zweiter
Klick kehrt die Reihenfolge um. Die Seitengrößen sind 25, 100, 300, 700 und 1000 (admininterface.ini
`[PaginationSettings] ItemsPerPageList_moderation_recent[]`); die Wahl jedes Benutzers wird gemerkt (die Präferenz
`admin_moderation_limit`). Jeder Filter, jede Sortierung und jede Seite hat eine eigene Adresse, sodass eine gefilterte
Ansicht als Lesezeichen gespeichert werden kann.

### Schnell bei einer großen Datenbank

Die Seite ist ein indizierter Lesezugriff auf `ezcontentobject` (der Index `ezcontentobject_modified`, neu in 6.0.15,
oder `ezcontentobject_pub` für das Erstellungsdatum), mit einem Offset und einem Limit zugeschnitten, plus eine Zählung.
Ein Filter auf eine andere Tabelle (zurückgehalten, versteckt, schwebend, Siteaccess) wird zuerst als Liste von IDs aus
dem eigenen Index dieser Tabelle gelesen. Die Zeilen einer Seite werden mit einer Abfrage je Tabelle vervollständigt
(Orte, Versionen, Workflow-Prozesse, Zustände, Daten der Zurückhaltung, Ersteller, Audit-Ereignisse): etwa zehn
Abfragen, unabhängig von der Seitengröße, nie eine je Zeile. Jede Abfrage liest eine Tabelle, sodass die Liste unter
MySQL/MariaDB, PostgreSQL, SQLite, Oracle und MongoDB gleich funktioniert. Gemessen: 1000 Zeilen in deutlich unter
einer Sekunde unter SQLite mit 1.500 Objekten; die Engine-Tests prüfen eine Seite mit 1000 Zeilen unter zwei Sekunden
auf jeder Engine.

Der Aktualisierungsschritt `6.0.15-contentobject-modified-index` fügt den Index einer bestehenden Datenbank hinzu
(`./console exp:upgrade`; nicht in den SQL-Aktualisierungsdateien, die auch auf Datenbanken ohne die Inhaltstabellen
laufen).

### Woher die Adresse und der Browser kommen

Der Kernel speichert nicht, wer ein Objekt von wo eingereicht hat; das [Audit](../../bc/6.0/audit.md) tut es. Die
Spalte zeigt je Objekt das früheste der Ereignisse in content.ini `[ModerationSettings] SubmissionEvents[]`:
`content.object.hold.auto` (wird immer aufgezeichnet, wenn das automatische Zurückhalten etwas zurückhält),
`content.object.publish` und `content.object.create` (beide standardmäßig aus; schalten Sie sie in audit.ini
`[AuditEventSettings]` ein, um jede Einreichung zu sehen). Die Werte sind so, wie das Audit sie aufbewahrt:
Standardmäßig wird die Adresse auf ihr /24-Netz (IPv4) oder /48-Netz (IPv6) gekürzt und der Browser auf seinen ersten
Teil (audit.ini `[AuditPrivacySettings]`). Ohne ein Ereignis sagt die Spalte *nicht aufgezeichnet*. Sie kommen aus dem
Audit-Index; die Seite bringt ihn eine halbe Sekunde lang auf den neuesten Stand, bevor sie ihn liest.

## Aktionen auf einer Auswahl

Haken Sie Einträge an (oder *Alle auf dieser Seite auswählen*), wählen Sie eine Aktion und *Auf die ausgewählten
anwenden*:

| Aktion | Führt aus | Entschieden durch |
|---|---|---|
| Zurückhaltung aufheben | die Operation `updateobjectstate` (not_held), deckt dann die Orte auf, die das automatische Zurückhalten versteckt hat | `state/assign` von not_held; `content/hide` zum Aufdecken |
| Zur Prüfung zurückhalten (mit einem Grund) | `updateobjectstate` (held); der Grund wird mit der Zurückhaltung gespeichert | `state/assign` von held |
| Verstecken, Aufdecken | den Rumpf der Operation `content/hide` je Ort | `content/hide` |
| In den Papierkorb verschieben | die Operation `content/delete`, in den Papierkorb | `content/remove`; ein Eintrag mit Untereinträgen bleibt unangetastet |
| Löschen | die Operation `content/delete`; ein Entwurf, der nie veröffentlicht wurde, wird endgültig entfernt | `content/remove` |
| Sektion zuweisen | `updateSection` für das Objekt | `section/assign` (die erlaubten Sektionen des Objekts) |
| Status zuweisen | `updateobjectstate` | `state/assign` (die erlaubten Zustände des Objekts) |

Jeder Eintrag wird einzeln gegen die eigenen Richtlinien des Moderators geprüft, und die Ergebnisseite listet jeden
Eintrag als **Erledigt**, **Abgelehnt** (die Richtlinie, oder der Eintrag ist nicht in einem Zustand, für den die
Aktion gilt: „Es ist bereits zurückgehalten.“) oder **Fehlgeschlagen** (ein Fehler), mit dem Grund. *In den Papierkorb
verschieben* und *Löschen* zeigen zuerst eine Bestätigung, und deren Schaltfläche fragt **Bestätigen Sie, dass Sie es
sind** (site.ini `[ReauthGroup_delete]`, Regel `moderation/action ConfirmDestructiveButton`).

Eine Auswahl von mehr als content.ini `[ModerationSettings] JobThreshold` Einträgen (100) läuft als Inhaltsjob
`moderation` im Hintergrund: Seine Fortschrittsseite `content/job/<id>` öffnet sich, der Moderator kann sie verlassen,
und wenn der Job fertig ist, öffnet die Seite `moderation/report/<id>` mit dem Ergebnis je Eintrag. Der Job läuft als der
Moderator, in Stapeln, jeder in seiner eigenen Transaktion ([Inhaltsjobs](../../bc/6.0/inhaltsjobs.md)).

Jede Aktion wird im Audit als `content.moderation.action` aufgezeichnet (die Aktion, die Zahlen und die IDs der
erledigten, abgelehnten und fehlgeschlagenen Einträge); die eigene Änderung jedes Eintrags zeichnet die Operation auf
(`content.object.state`, `content.node.hide`, `content.object.remove` ...), und Zurückhaltungen als
`content.object.hold.place` und `content.object.hold.release`.

## Richtlinien

| Richtlinie | Gibt |
|---|---|
| `moderation/read` | die Liste und den Dashboard-Block: Name, Klasse, Status, Zurückhaltung, Sektion und Ersteller jedes Objekts |
| `moderation/act` | die Aktionen (die eigenen Inhalts-, Sektions- und Zustandsrichtlinien jedes Eintrags entscheiden weiterhin) |
| `moderation/personaldata` | die Spalte *Herkunft der Einreichung*: Adressen und Browser sind personenbezogene Daten |

Neue Installationen geben der Rolle Editor `moderation/read` und `moderation/act` (die alltäglichen Richtlinien der
Redakteure, `expEditorPolicies`); Administratoren haben jede Richtlinie. `moderation/personaldata` wird standardmäßig
niemandem gegeben. Bei einer bestehenden Installation fügt der Aktualisierungsschritt `6.0.15-editor-policies` die
beiden der Rolle Editor hinzu, wenn er läuft.

## Dashboard

Der Block **Neue Inhalte** (dashboard.ini `[DashboardBlock_moderation]`, für Benutzer mit `moderation/read`) zeigt, wie
viele Objekte zur Prüfung zurückgehalten sind und wie viele heute erstellt wurden, jeweils verlinkt auf die danach
gefilterte Liste.

## Die Website-Seitenleiste

Die [Website-Seitenleiste](https://github.com/se7enxweb/expwt) verlinkt *Neue Inhalte* für Moderatoren, und ihr Panel
Seite hebt eine Zurückhaltung auf dieselbe Weise auf (die versteckten Orte werden aufgedeckt).

## Einstellungen

```ini
# content.ini
[ModerationSettings]
JobThreshold=100
SubmissionEvents[]=content.object.hold.auto
SubmissionEvents[]=content.object.publish
SubmissionEvents[]=content.object.create
```
