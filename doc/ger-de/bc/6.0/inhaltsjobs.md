# Inhaltsjobs: große Entfernen-, Kopier- und Verschiebevorgänge im Hintergrund

Lesen Sie diese Seite, wenn Ihre Redakteure große Teilbäume entfernen, kopieren oder verschieben, wenn Sie einen
Cluster betreiben oder wenn Sie die Bestätigungsseiten der Administration überschreiben. Ab Exponential 6.0.15 läuft
ein großer Vorgang als **Inhaltsjob** im Hintergrund statt innerhalb der Webanfrage.

Bisher geschah das Entfernen, Kopieren oder Verschieben eines Teilbaums in der Anfrage, die es bestätigte. Für zehn
Knoten ist das in Ordnung. Bei einigen hundert stößt es an das Zeitlimit der Anfrage, das Speicherlimit oder eine
riesige Datenbanktransaktion, und die Anfrage scheitert auf halbem Weg: Ein Teil des Teilbaums ist weg oder kopiert,
ein Teil nicht. Die Administration verweigerte solche Vorgänge außerdem oberhalb von `MaxNodesRemoveSubtree` /
`MaxNodesCopySubtree` und schickte den Redakteur zu einem Kommandozeilenskript.

Ein Inhaltsjob läuft in einem Hintergrund-Worker, in Stapeln von 50 Knoten, jeder Stapel in seiner eigenen
Datenbanktransaktion, mit einem Kontrollpunkt nach jedem Stapel. Der Redakteur sieht eine Fortschrittsseite, kann sie
verlassen, den Job abbrechen oder fortsetzen, und ein Job wird auch dann fertig, wenn sein Worker beendet wird. Kleine
Vorgänge sind unverändert: Unterhalb von `SynchronousLimit` (50 Knoten) erhält der Redakteur die Seite und das
Ergebnis, die er schon immer erhielt.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Große Vorgänge für Entfernen, Kopieren, Verschieben, Verbergen/Anzeigen, Sektionen, Zustände eines Teilbaums und Orte werden zu Jobs (`content/job/<id>`, `content/jobs`). Neuer Einstellungsblock `content.ini [ContentJobSettings]`, neue Richtlinie `content/jobs`, neuer Cronjob-Teil `contentjobs`, neues CLI `bin/php/expcontentjob.php`. `MaxNodesRemoveSubtree` und `MaxNodesCopySubtree` begrenzen jetzt nur noch „jetzt“. |
| Wer betroffen ist | Redakteure großer Teilbäume (sie erhalten eine Wahl und eine Fortschrittsseite). Cluster: Das Job-Verzeichnis muss gemeinsam genutzt werden. Overrides der Bestätigungs- und Durchsuchen-Seiten (neue Blöcke). Rollen, die die Jobs aller Benutzer sehen sollen. |
| Wie Sie es prüfen | `grep -n -A12 "\[ContentJobSettings\]" settings/content.ini` und `grep -n -A3 "CronjobPart-contentjobs" settings/cronjob.ini` |
| Wie Sie es beheben | Führen Sie die Cronjob-Gruppe `frequent` aus; nutzen Sie `var/<site var dir>/jobs/content/` auf allen Cluster-Knoten gemeinsam; geben Sie `content/jobs` an Rollen, die beaufsichtigen; vergleichen Sie Template-Overrides mit den ausgelieferten. |

## Checkliste für das Upgrade

1. Stellen Sie sicher, dass der Cronjob-Teil `contentjobs` läuft (er gehört zur Gruppe frequent). Er startet Jobs,
   deren Worker nie gestartet ist, und setzt Jobs fort, deren Worker gestorben ist:
   `php bin/php/console cron:frequent --allow-root-user`.
2. Geben Sie in einem Cluster jedem Knoten, der die Administration ausliefert oder den Cronjob ausführt, dasselbe
   `var/` (siehe [Hinweis zum Cluster](#hinweis-zum-cluster)).
3. Geben Sie die Richtlinie `content/jobs` an Rollen, die die Jobs aller Benutzer sehen müssen. Sie ist Teil von
   `content/*`.
4. Ist `exec` von PHP ausgeschaltet oder das Prozesslimit niedrig, starten Worker innerhalb einer Minute aus dem
   Cronjob-Teil. Setzen Sie `PhpBinary`, wenn das PHP der Kommandozeile nicht gefunden wird.
5. Unter SQLite sollten Sie `BatchPause` von 100 bis 200 Millisekunden erwägen.
6. Überschreiben Sie die unter [Was der Redakteur sieht](#was-der-redakteur-sieht) aufgeführten Bestätigungs- oder
   Durchsuchen-Seiten, vergleichen Sie sie mit den ausgelieferten Templates: Die neuen Blöcke und die Wahl werden dort
   hinzugefügt.

## Was der Redakteur sieht

### Vorher: die Bestätigung, mit dem, was sie betrifft, und einer Wahl

Die Bestätigung des Entfernens (`content/removeobject`), die Durchsuchen-Seite einer Teilbaumkopie
(`content/copysubtree`), eines Verschiebens (`content/action`, Verschieben) und von „Add a location for selected“
(siehe unten) und, für große Vorgänge, eine neue Bestätigung für Verbergen/Anzeigen (`content/hide`), für die
Zuweisung einer Sektion (`section/assign`), für das Setzen eines Zustands auf einen Teilbaum (`state/assign`) und für
das Entfernen vieler Orte (`content/action`, RemoveAssignment) zeigen:

- **Was davon betroffen ist**: die Zahl der Orte und Objekte, wie viele Objekte außerdem Orte außerhalb haben (beim
  Entfernen nur dieser Orte bestehen sie weiter), die beteiligten Klassen mit ihrer Anzahl und wer jede Wurzel zuletzt
  geändert hat und wann.
- **Hintergrundjobs, die an diesem Teil des Baums arbeiten**, mit Links, wenn es welche gibt: Ein überlappender
  Vorgang wird abgelehnt, bis sie fertig sind.
- **Wie es ausgeführt wird**:
  - „Im Hintergrund ausführen (empfohlen für N Elemente: Sie können die Seite verlassen)“
  - „Jetzt ausführen (diese Seite wartet, bis es erledigt ist)“

  Unterhalb von `SynchronousLimit` Elementen ist „jetzt“ vorausgewählt, ab da „Hintergrund“. Die letzte Wahl des
  Redakteurs pro Vorgang wird gespeichert (die Benutzereinstellungen `admin_content_job_mode_<operation>`) und beim
  nächsten Mal vorausgewählt. Oberhalb von `NowLimit` (1000) ist „jetzt“ ausgegraut, mit dem Grund; ebenso oberhalb
  eines älteren Limits der Seite (`MaxNodesRemoveSubtree`, `MaxNodesCopySubtree`), weil der alte Code es dort
  ablehnen würde.
- Bei einem Entfernen, was „Verschiebe in den Papierkorb“ im Gegensatz zum Löschen bedeutet (aus dem Papierkorb
  wiederherstellbar), und dass Elemente, die der Redakteur nicht entfernen darf, rot markiert sind und das ganze
  Entfernen aufhalten.
- Bei einer Kopie, welche Versionen und welchen Ersteller die Kopie erhält (`content.ini [CopySettings]`).

Verbergen/Anzeigen und die Zuweisung einer Sektion hatten keine Bestätigungsseite und haben für kleine Teilbäume
weiterhin keine: Die Seite erscheint nur, wenn der Hintergrund vorausgewählt ist (ein großer Teilbaum, oder der
Redakteur hat beim letzten Mal den Hintergrund gewählt). Dasselbe gilt für die drei Vorgänge unten.

Auf einer Durchsuchen-Seite wird die Wahl über dem Formular zum Durchsuchen angezeigt; sie wird mit diesem Formular
gesendet, gleich was auf der Seite zuerst kommt. (Bis 6.0.15 wurde die Wahl der Durchsuchen-Seiten für Verschieben und
Kopieren nicht gesendet, und die automatische Entscheidung nach `SynchronousLimit` galt, egal was der Redakteur
wählte.)

### Zustände für einen Teilbaum sowie das Hinzufügen oder Entfernen vieler Orte

- **Zustände für einen Teilbaum** (Jobtyp `state`): Das Formular für Zustände in der Registerkarte Details des
  Knotens hat **Auch für alles unterhalb dieses Knotens**, wenn der Knoten Unterelemente hat und der Redakteur einen
  Zustand zuweisen darf. „Jetzt“ setzt die gewählten Zustände auf jedes Objekt des Teilbaums in der Anfrage, über die
  Operation `updateobjectstate` des Kernels, die Zustände auslässt, die der Redakteur einem Objekt nicht zuweisen
  darf. „Hintergrund“ startet einen Job `state`, der solche Objekte mit einer Warnung überspringt. Ein Job setzt einen
  Zustand, daher braucht der Hintergrund eine geänderte Zustandsgruppe; zwei Gruppen auf einmal zu ändern kann nur
  jetzt laufen, bis `NowLimit`. Der Redakteur muss den Zustand dem eigenen Objekt des Knotens zuweisen dürfen, sonst
  wird die ganze Anfrage mit dem Grund abgelehnt. Ohne die Option setzt das Formular die Zustände des Objekts wie
  bisher. Eine Installation, deren einzige Zustandsgruppe das interne `ez_lock` ist, zeigt kein Formular für Zustände
  und damit keine Option.
- **Viele Orte entfernen** (Jobtyp `removelocation`): **Ausgewählte entfernen** im Fenster der Orte. Ein Ort mit
  Unterelementen geht weiterhin wie bisher zur Bestätigung des Entfernens; andernfalls erhalten viele ausgewählte Orte
  (oder die letzte Wahl „Hintergrund“ des Redakteurs) die Bestätigung („Ausgewählte Positionen“, die Objekte, die
  ihre anderen Orte behalten) und einen Job `removelocation`; wenige werden wie bisher sofort entfernt.
- **Einen Ort für viele Elemente hinzufügen** (Jobtyp `addlocation`): **Add a location for selected** unter
  **Weitere Aktionen** der Liste der Unterelemente öffnet eine Durchsuchen-Seite für den neuen Elternknoten, mit dem,
  was davon betroffen ist, und der Wahl. „Jetzt“ fügt den Ort pro Objekt über die Operation `addlocation` des Kernels
  hinzu, wie es das Fenster der Orte für ein Objekt tut; „Hintergrund“ startet einen Job `addlocation`. Objekte, die
  bereits unter dem gewählten Knoten platziert sind, und Elemente, die unter sich selbst platziert würden, werden
  ausgelassen. Das eigene **Ort hinzufügen** des Fensters der Orte (ein Objekt unter mehreren neuen Elternknoten) ist
  unverändert: Kein Jobtyp deckt diese Form ab, und es fügt einen Knoten pro gewähltem Elternknoten hinzu.

### Während: die Fortschrittsseite `content/job/<id>`

- der Titel („Copying Fit & Healthy“), der Zustand (Wartend, Läuft, Fertig, Fehlgeschlagen, Abgebrochen), „120 von
  400 Knoten“, die Sekunden, die er gelaufen ist, ein Fortschrittsbalken und der aktuelle Schritt;
- die letzten 40 Zeilen des Worker-Logs, live aktualisiert, und **Gesamtes Protokoll herunterladen** (`?log=1`);
- **Wer**: der Benutzer, unter dem er läuft (verlinkt), sein Anmeldename, wer ihn in seinem Auftrag angelegt hat, wenn
  das abweicht (das Entfernen der Teilkopie, der Cronjob), wer ihn abgebrochen oder fortgesetzt hat und wann;
- **Was**: der Vorgang, Quelle und Ziel mit ihren vollständigen Pfaden (verlinkt), wann die Quelle zuletzt geändert
  wurde und von wem, der alte und der neue Platz bei einem Verschieben, die Optionen (Papierkorb oder Löschen;
  Versionen, Ersteller und Zeit einer Kopie; der gewählte Modus) und danach die neue Kopie (verlinkt) oder, bei einem
  Entfernen in den Papierkorb, ein Link zum Papierkorb;
- **Anzahlen**: erledigte Elemente von der Gesamtzahl und was die Engine zählt: entfernte oder kopierte Orte und
  Objekte, in den Papierkorb verschobene Objekte, entfernte Orte, deren Objekt bleibt, übersprungene,
  fehlgeschlagene, Warnungen;
- **Wann**: angelegt, gestartet, die Wartezeit auf einen Worker, beendet, die Dauer und, während er läuft, eine
  Schätzung der Restzeit aus der bisherigen Rate;
- **Stapel**: die Stapelgröße, der aktuelle Stapel, der durchschnittliche und der langsamste Stapel, der höchste
  Speicherverbrauch;
- **Wo es läuft**: der Siteaccess, woher er gestartet wurde und wo sein Worker läuft (Apache / PHP-FPM, Exponential
  Velocity oder die Kommandozeile, der Host, die PID, der Systembenutzer), ob der Worker lebt (mit dem Alter seines
  letzten Heartbeats) oder steht, wie oft ein Worker gestartet wurde und welche Sperre er hält.

Die Seite fragt alle zwei Sekunden `content/job/<id>?json=1` nach dem Status. Ist der Job fertig, öffnet sie nach drei
Sekunden das Ergebnis: die neue Kopie, den verschobenen Knoten an seinem neuen Platz oder den Elternknoten dessen, was
entfernt wurde. Schlägt er fehl oder wird er abgebrochen, lädt die Seite neu, um die passenden Schaltflächen zu zeigen.

| Schaltfläche | Wann | Tut |
|---|---|---|
| Abbrechen | wartend, laufend, fehlgeschlagen | ein wartender oder fehlgeschlagener Job hält sofort an; ein laufender nach seinem aktuellen Stapel. Was erledigt ist, bleibt. |
| Fortsetzen | fehlgeschlagen, oder der Worker ist gestorben | macht ab dem letzten fertigen Stapel weiter; nichts wird zweimal getan |
| Die Teilkopie entfernen | eine abgebrochene Kopie, die etwas angelegt hat | startet einen Job zum Entfernen (Löschen, nicht Papierkorb) für die Teilkopie |

Ein fehlgeschlagener Job zeigt den Fehler und einen Link zu dem Knoten, um den es geht.

### Danach: die Liste der Jobs `content/jobs`

Die eigenen Jobs des Redakteurs; mit der Richtlinie `content/jobs` auch **Alle Benutzer** (`content/jobs/(all)/1`). Pro
Job: der Benutzer (Anmeldename, voller Name beim Darüberfahren), der Vorgang, Quelle und Ziel, der Zustand, Fortschritt
und Elemente, Startzeit und Dauer und der Server (woher er gestartet wurde, und der Siteaccess). Über der Liste: die
Zahl der Jobs pro Zustand (jeweils ein Filter), Filter nach Zustand, Vorgang und Benutzer, Sortierung nach Zustand,
Elementen, Startzeit und Dauer, 25 Jobs pro Seite. Während ein Job läuft, lädt die Liste alle fünf Sekunden neu.

## Wann ein Job verwendet wird

| Der Redakteur wählt | Elemente | Was geschieht |
|---|---|---|
| Hintergrund | beliebig | ein Job |
| jetzt | bis `NowLimit` (und bis zum alten Limit der Seite) | der alte synchrone Code, genau wie bisher |
| jetzt | darüber | im Formular abgelehnt (ausgegraut); ein gefälschtes oder veraltetes Formular erhält einen Job |
| (keine Wahl: ein älteres Formular, ein Skript, das an die View sendet) | unterhalb von `SynchronousLimit` und des alten Limits | der alte synchrone Code |
| | bei oder oberhalb von einem der beiden | ein Job |

Der kleine synchrone Pfad lädt die Engine überhaupt nicht: Die View zählt die Knoten aus dem Baum und liest den Index
der Sperren (`locks.json`) selbst. Ein Entfernen oder Kopieren weniger Knoten führt denselben Code mit denselben
Seiten wie bisher aus; nur der neue Informationsblock und die Wahl kommen hinzu (nachgewiesen, siehe Tests).

## Sperren

Ein Job sperrt seine Teilbäume vom Moment seiner Anlage, bis er fertig oder abgebrochen ist (ein fehlgeschlagener Job
behält seine Sperre, bis er fortgesetzt und fertig oder abgebrochen ist): ein Entfernen jeden entfernten Teilbaum, eine
Kopie den Quellteilbaum, den Zielknoten und die neue Kopie, ein Verschieben den Teilbaum an seinem alten und neuen
Platz, ein Job `state` seinen Teilbaum, ein Job `removelocation` jeden entfernten Ort, ein Job `addlocation` das Ziel
und jeden ausgewählten Knoten. Jedes Entfernen, Kopieren, Verschieben, Verbergen, jede Zuweisung einer Sektion oder
eines Teilbaumzustands, jedes Entfernen von Orten oder Hinzufügen eines Ortes, das einen gesperrten Teilbaum
überlappt, ob synchron oder als Job, wird mit einer Seite abgelehnt, die das sagt und auf den Job verlinkt, der die
Sperre hält. Nichts wird stillschweigend eingereiht.

## Berechtigungen

| Wer | Sieht und bedient |
|---|---|
| der Benutzer, der den Job gestartet hat | seinen Job: Fortschrittsseite, JSON-Status, Log, Abbrechen, Fortsetzen, Die Teilkopie entfernen |
| ein Benutzer mit der Richtlinie `content/jobs` (neu; Teil von `content/*`) | jeden Job und die Liste **Alle Benutzer** |
| ein Benutzer mit uneingeschränktem `content/remove` (Jobs zum Entfernen) oder `content/create` (Jobs zum Kopieren) | diese Jobs |

`content/job` und `content/jobs` brauchen `content/read`, um überhaupt geöffnet zu werden. Die Engine prüft die
eigenen Berechtigungen des Vorgangs für die ganze Menge, wenn der Job angelegt wird, und im Worker pro Knoten als der
anfragende Benutzer.

## Der Befehl und der Cronjob-Teil

```bash
php bin/php/expcontentjob.php list [--all]                 # or: php bin/php/console exp:expcontentjob list
php bin/php/expcontentjob.php show <id>
php bin/php/expcontentjob.php remove <node>[,<node>...] [--trash|--delete] [--background]
php bin/php/expcontentjob.php copy <source> <new parent> [--all-versions] [--keep-creator] [--keep-time] [--background]
php bin/php/expcontentjob.php move <node> <new parent> | hide <node> | reveal <node> | section <node> <section id>
php bin/php/expcontentjob.php run <id> | resume <id> [--background] | cancel <id>
```

Der Cronjob-Teil `contentjobs` (Gruppe frequent, `settings/cronjob.ini`) startet Jobs, deren Worker nie gestartet ist
(nach `QueuedGrace` Sekunden), und setzt Jobs fort, deren Worker gestorben ist. Ein Job geht daher nie verloren, wenn
der Webserver keinen Worker starten kann (`exec` ausgeschaltet, das Prozesslimit erreicht): Er startet innerhalb einer
Minute.

## Einstellungen: `content.ini [ContentJobSettings]`

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `SynchronousLimit` | 50 | ab so vielen Elementen ist der Hintergrund vorausgewählt (0: immer) |
| `NowLimit` | 1000 | oberhalb so vieler Elemente wird „jetzt“ nicht angeboten (0: keine Obergrenze) |
| `BatchSize` | 50 | Knoten pro Stapel, je eine Transaktion |
| `BatchPause` | 0 | Millisekunden zwischen Stapeln, Raum für andere Schreiber (unter SQLite lohnen 100 bis 200) |
| `QueuedGrace` | 60 | Sekunden, bevor der Cronjob-Teil einen Job startet, den niemand gestartet hat |
| `MaxAttempts` | 5 | Starts eines Workers ohne fertigen Stapel, bevor der Job als fehlgeschlagen gilt |
| `KeepDays` | 30 | fertige Jobs werden nach so vielen Tagen entfernt |
| `PhpBinary` | (leer) | die PHP-Binärdatei der Kommandozeile für den Worker |
| `JobTypes[<name>]` | remove, copy, move, hide, reveal, section, state, addlocation, removelocation, packageinstall, auditexport | die Jobtypen |

Die älteren Einstellungen `[RemoveSettings] MaxNodesRemoveSubtree` und `[CopySettings] MaxNodesCopySubtree` begrenzen
jetzt nur noch, was „jetzt“ laufen darf; mit verfügbaren Hintergrundjobs wird ein größerer Teilbaum zu einem Job,
statt abgelehnt zu werden. Sie lehnen wie bisher nur ab, wenn die Job-Schicht nicht verwendet werden kann, und sagen
das dann.

## Wie es skaliert

Gemessen auf einer Testinstallation (SQLite, PHP 8.5, 2026-10-02), Stapel von 50:

| Vorgang | Elemente | Zeit | Pro Stapel | Höchster Speicher |
|---|---|---|---|---|
| Kopie von „Fit & Healthy“ (gemischte Klassen, Bilder, Relationen) | 142 | 11 s | 3,0 bis 4,4 s | 17,6 bis 19,7 MB |
| Entfernen dieser Kopie (Löschen) | 143 | 5 s | 1,4 bis 1,8 s | 16,4 bis 17,6 MB |
| Kopie eines erzeugten Testbaums, Tiefe 5 | 2.340 | 2 min 9 s | etwa 2,8 s | 15,7 MB |

Der Speicher bleibt flach: Die Engine leert die Caches im Speicher nach jedem Stapel, daher ist der Höchstwert von
Stapel 90 der von Stapel 1. Die Zeit wächst linear mit der Zahl der Knoten. Die eigenen Tests der Engine zu
Skalierung, kill -9 und Nebenläufigkeit sind in ihrer Testsuite beschrieben (`tests/tests/kernel/classes/contentjob/`).

## Umgang mit Fehlern

- **Ein Stapel schlägt fehl**: Seine Transaktion wird zurückgerollt, der Job ist `failed` mit dem Fehler (und dem
  Knoten, um den es geht), er behält seine Sperre, und Fortsetzen macht ab dem letzten fertigen Stapel weiter.
- **Der Worker wird beendet** (kill -9, ein Neustart, ein Deploy): Der Job bleibt ohne Worker auf `running`; die
  Fortschrittsseite zeigt den Worker als stehend und bietet Fortsetzen an; der Cronjob-Teil setzt ihn von selbst fort.
- **Der Webserver kann keinen Worker starten**: Der Job wartet; der Cronjob-Teil startet ihn nach `QueuedGrace`.
- **Abbrechen**: nach dem aktuellen Stapel. Ein abgebrochenes Entfernen lässt den Rest stehen; eine abgebrochene Kopie
  lässt die Teilkopie stehen und bietet an, sie zu entfernen.
- **Die Engine fehlt oder ist defekt**: Jede View fällt auf ihren alten synchronen Code zurück und protokolliert den
  Fehler.

## Jobtypen anderer Teile des Kernels

Zwei Jobtypen außerhalb des Inhaltsbaums verwenden seit dem 2026-10-12 dieselbe Engine (Speicher, Worker, Stapel,
Kontrollpunkt, Fortschrittsseite, Fortsetzen):

| Typ | Klasse | Gestartet von | Was er tut |
|---|---|---|---|
| `packageinstall` | `expContentJobPackageInstall` | der Installationsseite unter Setup > Pakete, „Im Hintergrund installieren“ | installiert ein Paket mit den Entscheidungen des Assistenten als der anfragende Benutzer; die Seite des Pakets zeigt den Fortschritt Objekt für Objekt und führt die Entwürfe auf ([Anleitung zu Paketen](../../../eng-us/guides/packages.md#7-installing-and-uninstalling)) |
| `auditexport` | `expContentJobAuditExport` | der Exportseite des Audit-Protokolls, „Im Hintergrund exportieren“ | schreibt den Filter der Konsole als CSV, JSON Lines oder JSON in eine Datei neben dem Job, in Stapeln von `BatchSize` x 100 Einträgen, bis `audit.ini [AuditConsoleSettings] MaxBackgroundExportRecords`; nur der Benutzer, der ihn angefordert hat, darf die Datei herunterladen, die mit dem Job verschwindet, wenn fertige Jobs entfernt werden ([Anleitung zum Audit-Protokoll](../../../eng-us/bc/6.0/audit.md#streaming-the-export)) |

Ein Job, der in `gui_return_url` eine Seite nennt, öffnet sie, wenn er fertig ist: Der Audit-Export kehrt mit der
Datei zur Exportseite zurück.

## Einen Jobtyp hinzufügen

Ein Jobtyp ist eine Klasse, die `expContentJobType` implementiert (`kernel/classes/contentjob/expcontentjobtype.php`),
registriert in `content.ini [ContentJobSettings] JobTypes[<name>]=<class>`. Den Rest erledigt die Engine: den
Speicher, die Sperren, den Worker, die Stapel mit je einer Transaktion, den Kontrollpunkt, den Heartbeat, Abbrechen und
Fortsetzen. Der Vertrag, der einen Job einen Abbruch an jeder Stelle überstehen lässt: `runBatch()` schreibt nur in die
Datenbank (der Worker schließt es in eine Transaktion ein), und es ist idempotent: Nach einem Absturz erneut ausgeführt,
findet es seine Arbeit bereits erledigt vor.

Dieses Beispiel verbirgt jeden Knoten eines Teilbaums, den tiefsten zuerst, in Stapeln. Es ist so, wie es steht,
getestet (61 Knoten, BatchSize 20, nach 4 Stapeln fertig, jeder Knoten verborgen, ein zweiter Lauf tut nichts, die
Sperre freigegeben). Der Kernel liefert einen echten Typ `hide` aus; dieser dient dem Erlernen des Vertrags und ist
unter einem anderen Namen registriert.

```php
<?php
// extension/myext/classes/myextjobhidesubtree.php
class myExtJobHideSubtree implements expContentJobType
{
    public function validate( array $params, eZUser $user )
    {
        $node = eZContentObjectTreeNode::fetch( (int) ( isset( $params['node_ids'][0] ) ? $params['node_ids'][0] : 0 ) );
        if ( !$node instanceof eZContentObjectTreeNode )
            throw new expContentJobException( 'The node does not exist.' );
        if ( !$node->canHide() )
            throw new expContentJobException( 'You may not hide this node.' );
        return array( 'node_ids' => array( (int) $node->attribute( 'node_id' ) ),
                      'path' => $node->attribute( 'path_string' ) );
    }

    public function countNodes( array $params )
    {
        $node = eZContentObjectTreeNode::fetch( (int) $params['node_ids'][0] );
        return $node ? $node->subTreeCount( array( 'Limitation' => array() ) ) + 1 : 0;
    }

    public function locks( array $params )
    {
        return array( array( 'path' => $params['path'], 'mode' => expContentJobLock::SUBTREE ) );
    }

    public function describe( array $params )
    {
        return 'Hide the subtree of node ' . $params['node_ids'][0];
    }

    public function prepare( expContentJob $job )
    {
        $checkpoint =& $job->checkpoint();
        $checkpoint += array( 'hidden' => 0 );
    }

    public function runBatch( expContentJob $job, $batchSize )
    {
        $params = $job->params();
        $db = eZDB::instance();
        // what is still visible, deepest first; a batch run twice finds its nodes hidden and skips them
        $rows = $db->arrayQuery( "SELECT node_id FROM ezcontentobject_tree
                                  WHERE path_string LIKE '" . $db->escapeString( $params['path'] ) . "%' AND is_hidden = 0
                                  ORDER BY depth DESC, node_id", array( 'limit' => (int) $batchSize ) );
        foreach ( $rows as $row )
            eZContentObjectTreeNode::hideSubtree( eZContentObjectTreeNode::fetch( (int) $row['node_id'] ) );
        $checkpoint =& $job->checkpoint();
        $checkpoint['hidden'] += count( $rows );
        return array( 'done' => count( $rows ), 'finished' => count( $rows ) < (int) $batchSize,
                      'message' => 'Hidden ' . $checkpoint['hidden'] . ' nodes' );
    }

    public function afterBatch( expContentJob $job )
    {
        eZContentCacheManager::clearAllContentCache();
    }

    public function finish( expContentJob $job )
    {
        $checkpoint =& $job->checkpoint();
        $job->setResult( 'hidden', $checkpoint['hidden'] );
    }
}
```

```ini
# extension/myext/settings/content.ini.append.php
[ContentJobSettings]
JobTypes[myhide]=myExtJobHideSubtree
```

```php
$job = expContentJob::create( 'myhide', array( 'node_ids' => array( 123 ) ), eZUser::currentUser() );
$job->spawn();                                  // the progress page: content/job/<id>
```

Die Fortschrittsseite und die Liste der Jobs zeigen jeden registrierten Typ: sein `describe()` als Titel, die Zahlen
des Ergebnisses, die Stapel und das Log. Um einer View eine Wahl dafür zu geben, rufen Sie
`Exponential\View\Kernel\Content\Job::modeChoice()` für die Bestätigungsseite auf, `chosenAsJob()`, wenn sie gesendet
wird, und `startJob()`; `interstitial()` erledigt alle drei für einen Vorgang, der keine eigene Bestätigungsseite hat.
Die RAD-Übersicht (`setup/radsurvey`) zählt die registrierten Jobtypen.

## Hinweis zum Cluster

Jobs liegen als Dateien in `var/<site var dir>/jobs/content/` (`<id>.json`, `<id>.log`, die Arbeitsliste und die
ID-Zuordnung der Kopie, `locks.json`), atomar unter `flock` geschrieben. In einem Cluster muss jeder Knoten, der die
Administration ausliefert oder den Cronjob ausführt, dasselbe Verzeichnis sehen (ein gemeinsames `var/`), wie bei der
Warteschlange für Reparaturen; sonst ist ein auf einem Knoten gestarteter Job auf den anderen unsichtbar und nicht
gesperrt. Die Dateien gehören dem Benutzer der Website, auch wenn der Job unter Exponential Velocity (root) angelegt
wird.

## Tests

- `tests/tests/kernel/classes/contentjob/`: die Engine (Speicher, Sperren, Start, Worker, Typen), einschließlich ihrer
  Tests zu Skalierung, kill -9 und Nebenläufigkeit.
- Die Abläufe der Administration wurden im Browser bei 960 Pixeln mit Geräteskalierung 2 (200 % Zoom) geprüft, unter
  Apache und unter Velocity, in den Designs admin und admin4:
  - ein kleines Entfernen und Kopieren haben dieselben Seiten, Felder und Ergebnisse wie bisher (abgesehen von den
    neuen Blöcken);
  - Entfernen und Kopieren von 201 Knoten über die Fortschrittsseite bis zur Weiterleitung, Abbrechen und „Die
    Teilkopie entfernen“, kill -9 eines Workers und Fortsetzen, die Ablehnung eines überlappenden Vorgangs, die Liste
    der Jobs;
  - eine Kopie oder ein Entfernen einer echten Sektion („Fit & Healthy“);
  - Verbergen und Anzeigen (klein: der alte Ablauf ohne Seite; groß: die Bestätigung, ein Job und „jetzt“), Verschieben
    klein mit „jetzt“ und groß mit „Hintergrund“ (alter und neuer Platz angezeigt), Zuweisung einer Sektion als Job und
    „jetzt“;
  - ein Senden eines Teilbaumzustands, mit dem Grund abgelehnt (jetzt und Hintergrund), und der alte Pfad ohne die
    Option; Entfernen eines Ortes (der alte Pfad) und zweier mit „Hintergrund“ (Bestätigung, Job, beide weg);
    Hinzufügen eines Ortes für ein Element „jetzt“ und für zwei „Hintergrund“ (Job, beide platziert);
  - der Job `state` mit einem Zustand, den der Benutzer zuweisen darf, über einen Mock-Typ, der nur in seinem eigenen
    Prozess registriert ist (die Testinstallation hat keinen zuweisbaren Zustand; `ez_lock` kann nicht zugewiesen
    werden): die Wahl, die Sperre, während der Job wartet, der Job fertig mit jedem übergebenen Objekt, die Sperre
    freigegeben und die Zustandsverknüpfungen unverändert;
  - das ausgearbeitete Beispiel eines Jobtyps oben;
  - keine View antwortet auf einem der beiden Server mit 5xx.
- Nach jedem Entfernen bestätigte ein Bericht, dass nichts zurückgeblieben war, und nach jeder Kopie bestätigte ein
  Fingerabdruck, dass der Quellteilbaum unverändert war.

## Verwandte Seiten

- [Inhaltsjobs (Funktion)](../../../eng-us/features/6.0/content-jobs.md)
- [Verhaltensänderungen vom 1. und 2. Oktober 2026](verhaltensaenderungen-2026-10.md)
- [Die Exponential-Konsole](konsole.md)
- [Anleitung zu Inhaltsmodell und Bearbeitung](../../anleitungen/inhaltsmodell-und-bearbeitung.md)
