# Cronjob-Teile anhalten

Lesen Sie diese Seite, wenn Sie Cronjob-Teile eine Zeit lang zurückhalten möchten - während eines Releases, einer
Datenmigration oder eines Index-Neuaufbaus -, ohne die Crontab auf jedem Server zu bearbeiten, oder wenn eine Extension
Teile bisher über Dateien `cronpart_deactivated_<part>.txt` angehalten hat.

## Kurz gesagt

- **Setup > Cronjobs** hält einen Teil an (**Anhalten** auf seiner Karte) oder alle Teile (**Alle Teile anhalten**),
  bis jemand ihn fortsetzt. Ein angehaltener Teil wird von `runcronjobs.php` ausgelassen, wer ihn auch startet: die
  Crontab, `./console cron:<part>`, die `cron:`-Befehle von Velocity oder die Konsole. Ein Teil, der angehalten wird,
  während er läuft, beendet das gerade laufende Skript und lässt die Skripte danach aus.
- Ein einzeln ausgeführtes Skript (`--script`, oder **Starten** neben einem Skript) wird ausgelassen, solange ein Teil,
  der es aufführt, angehalten ist.
- Die Konsole zeigt auf der Karte **Angehalten**, seit wann, von wem und die beim Anhalten angegebene Notiz, einen
  Hinweis, solange alle Teile angehalten sind, einen Wert **Angehalten** in der Übersicht und einen Abschnitt
  **Pausen** mit der Angabe, wer was angehalten und fortgesetzt hat und warum. Ein angehaltener Teil kann nicht aus der
  Konsole gestartet werden.
- Die Kachel **Cronjobs** in der Setup-Übersicht (admin4) gibt an, wie viele Teile angehalten sind, solange einer es
  ist.
- Der Hinweis von `runcronjobs.php` gibt an, seit wann, von wem und warum ein Teil angehalten ist.
- Der Zustand ist eine Datei pro Teil, in dem Aufbau, den solche Extensions bereits verwenden, sodass eine Website, die
  Teile auf diese Weise angehalten hat, sie behält, und eine solche Seite denselben Zustand zeigt.
  `cronjob.ini [PauseSettings] Pause=disabled` schaltet die Prüfung ab.

## So funktioniert es

`runcronjobs.php` fragt `expCronjobPause::notice()`, nachdem es die Skripte ausgewählt hat und bevor es den
[Filter cronjob/part/run](filter-fuer-cronjob-teile.md) fragt. Ist der Lauf angehalten, gibt es einen Hinweis aus und endet
mit Exit-Code 0, wie bei einem Teil, den ein Listener auslässt:

```
Running cronjob part 'frequent'
Notice: The cronjob part 'frequent' is paused (Setup > Cronjobs). Paused since 2026-10-15 13:05 CEST by admin: Index rebuild until noon.
```

Die Zeit ist die Zeit der Zustandsdatei, in der Zeitzone des Servers; *by* und die Notiz stammen aus der Historie und
entfallen bei einer auf anderem Weg vorgenommenen Pause (siehe unten), die Notiz außerdem, wenn keine angegeben wurde.

Angehalten bedeutet, dass die Datei `cronpart_deactivated_<part>.txt` den Inhalt `1` hat; jeder andere Inhalt oder keine
Datei bedeutet, dass der Teil läuft. `all` hält alle Teile an; die Skripte von `[CronjobSettings]` (der Teil *global* in
der Konsole) haben den leeren Namen, `cronpart_deactivated_.txt`. Die Dateien liegen in
`<cache directory>/cronjob_control/` oder in dem Verzeichnis unterhalb des var-Verzeichnisses, das
`[PauseSettings] Directory` nennt.

**Eine Pause, während ein Teil läuft.** `runcronjobs.php` fragt vor jedem weiteren Skript des Teils erneut. Das Skript,
das läuft, wenn der Teil angehalten wird, läuft bis zu seinem Ende weiter (nichts wird auf halbem Weg gestoppt); die
Skripte danach werden ausgelassen:

```
Completing cronjobs/notification.php at: 10/15/2026 05:10 am
Notice: The cronjob part 'frequent' is paused (Setup > Cronjobs). Left out: 'workflow.php' and the scripts after it.
```

**Jeder Lauf, den eine Pause auslässt, wird protokolliert**, und zwar in `<log directory>/cronjob_skipped.log` (der
Hinweis, der Teil, das Skript und der Siteaccess), zusätzlich zum Hinweis in der Ausgabe des Laufs selbst, sodass Sie
nachträglich feststellen können, welche Läufe eine Pause zurückgehalten hat. Die Konsole weigert sich, einen
angehaltenen Teil zu starten, sodass ihre Läufe dieses Protokoll nie erreichen.

**Was eine Pause nicht zurückhält.** Eine Pause wird nur von `runcronjobs.php` gelesen. Die `cron:`-Befehle von
`./console` und von Velocity führen `runcronjobs.php` aus, werden also ebenfalls zurückgehalten; die Queue-Worker von
`expqueue`, die als Dienst (systemd) laufen, sind keine Cronjobs und arbeiten weiter - stoppen Sie dafür den Dienst.
Das Anhalten des Teils, der einen Worker aus der Crontab startet, hält diesen Start zurück.

Die Prüfung kommt vor dem Filter, damit die Listener einer Website weiterhin über jeden Teil entscheiden, der nicht
angehalten ist, und damit eine Pause auch dann gilt, wenn eine Website `[RunnableSettings] Listeners[]` zurücksetzt.

**Das Verzeichnis folgt dem Siteaccess.** Das Cache- und das var-Verzeichnis stammen aus dem Siteaccess, in dem ein
Skript läuft. Wenn die Crontab `runcronjobs.php -s <site>` ausführt und diese Website ein anderes `VarDir` hat als der
Administrations-Siteaccess, in dem die Konsole läuft, schreibt die Konsole Dateien, die der Cronjob nie liest, und das
Anhalten bewirkt nichts. Führen Sie die Crontab mit demselben Siteaccess aus, oder setzen Sie `[PauseSettings] Directory`
in `settings/override/cronjob.ini.append.php`, damit beide dasselbe Verzeichnis finden.

Die Namen `all` und `global` sind reserviert: `all` hält alle Teile an, und `global` ist der Name, unter dem die Konsole
die Skripte von `[CronjobSettings]` führt. Ein Cronjob-Teil namens `all` oder `global` kann nicht einzeln angehalten
werden.

**Mehrere Server.** Die Dateien sind lokal auf einem Server, sofern das Verzeichnis nicht geteilt wird. Wenn die
Crontab auf einem anderen Server läuft als dem, von dem die Konsole ausgeliefert wird (ein Cluster hinter einem Load
Balancer), setzen Sie `[PauseSettings]
Directory` auf ein Verzeichnis unterhalb des var-Verzeichnisses, das alle Server teilen (zum Beispiel auf dem gemeinsamen
Speicher des Clusters), oder halten Sie auf dem Server an, auf dem die Crontab läuft.

**Benutzer.** Die Kommandozeile und die Worker von Velocity können als root laufen, der Webserver als Website-Benutzer;
beide schreiben diese Dateien. Eine Pause wird in eine temporäre Datei geschrieben, die über die alte umbenannt wird
(sodass eine Datei, die dem anderen Benutzer gehört, ersetzt und nicht geöffnet wird), und die Datei und die Historie
erhalten den Dateimodus der Installation (`site.ini [FileSettings]
StorageFilePermissions`, innerhalb seiner Grenzen), sodass der andere Benutzer sie seinerseits ersetzen und ergänzen
kann.

**Audit.** Jedes Anhalten und Fortsetzen wird als `system.cronjob.pause` oder `system.cronjob.resume` aufgezeichnet
(Objekt: der Teil, `all` oder `global`; `after.note` mit der Notiz, wenn eine angegeben wurde), siehe
[das Prüfprotokoll](../../../eng-us/bc/6.0/audit.md). Beide sind standardmäßig eingeschaltet, im Kanal `system`.

**Eine Notiz.** Die Konsole hat ein Feld, *Notiz zum Anhalten oder Fortsetzen (optional)*, unterhalb der Schaltflächen
der Werkzeugleiste. Das nächste auf der Seite gedrückte **Anhalten** oder **Fortsetzen** (das eines Teils oder *Alle
Teile anhalten*) speichert sie mit seiner Zeile in der Historie: warum ein Teil zurückgehalten wird und bis wann. Sie
ist eine Zeile mit höchstens 200 Zeichen (`expCronjobPause::NOTE_LENGTH`; Zeilenumbrüche werden zu Leerzeichen) und
erscheint beim angehaltenen Teil, im Hinweis bei einer Pause aller Teile, unter **Pausen**, im Hinweis von
`runcronjobs.php` und damit in `cronjob_skipped.log`. Aus Code: `expCronjobPause::pause( $part, $user,
$note )`, `resume( $part, $user, $note )`.

**Die Setup-Übersicht.** Die Kachel **Cronjobs** zeigt *1 Teil angehalten*, *n Teile angehalten* oder *Alle Teile
angehalten*, solange eine Pause gilt, und sonst nichts (`menu.ini [Leftmenu_setup] LinkFigureTemplates[cronjobs]`, die
Fetch-Funktion `fetch( 'setup', 'cronjob_pauses' )`: ein Blick in das Verzeichnis der Zustandsdateien). Wie jeder Wert
dort wird sie nur auf einer Kachel gezeichnet, die der Benutzer öffnen darf (`setup/managecronjobs`).

Das Anhalten aus der Konsole schreibt die Datei über `eZFile::create()` (es gelten die Grenzen des Dateimodus der
Installation) und fügt eine Zeile zu `var/<vardir>/log/cronjob_pause.log` hinzu: Zeit, Teil, Anhalten oder Fortsetzen,
die Objekt-ID des Benutzers (keine Kopie des Namens: Die Seite und der Hinweis
zeigen das aktuelle Login des Benutzers, nachgeschlagen über die ID, sodass ein umbenannter Benutzer unter dem neuen Namen erscheint; das Login einer älteren Zeile nur, wenn der Benutzer nicht mehr existiert) und
die Notiz (leer, wenn keine angegeben wurde; Zeilen, die geschrieben wurden, bevor es Notizen gab, haben keine). Eine
auf anderem Weg vorgenommene Änderung - eine Shell, eine Extension - hat dort keine Zeile; die Karte zeigt trotzdem, seit wann
der Teil angehalten ist (die Zeit der Datei), und keinen Namen, wenn die Datei neuer ist als die letzte hier
aufgezeichnete Pause. Die Konsole liest nur das Ende des Protokolls (256 KB), sodass es wachsen kann, ohne die Seite zu
verlangsamen; rotieren Sie es nach Belieben mit den anderen Protokollen.

Aus der Konsole lassen sich nur Teilnamen anhalten, die diese Installation definiert, sowie `all`; ein Name besteht nie
aus mehr als Buchstaben, Ziffern, `_`, `.` und `-`, sodass er das Verzeichnis nicht verlassen kann.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `settings/cronjob.ini` | `PauseSettings` | `Pause` | `enabled` | `disabled`: `runcronjobs.php` ignoriert die Pausendateien, und die Konsole bietet kein Anhalten an |
| `settings/cronjob.ini` | `PauseSettings` | `Directory` | leer | Verzeichnis der Pausendateien unterhalb des var-Verzeichnisses; leer: `<cache directory>/cronjob_control` |

## Umstieg von einer Extension, die Teile angehalten hat

Solche Seiten stützten sich auf einen Patch von `runcronjobs.php`, den Exponential nicht enthält; unter Exponential
schalteten sie Dateien um, die nichts las, sofern die Website keinen eigenen Listener für den Filter hatte. Jetzt liest
der Kernel sie:

- Ein angehalten gebliebener Teil (eine Datei mit `1`) **wird von nun an ausgelassen**. Sehen Sie nach der
  Aktualisierung unter **Setup > Cronjobs** nach und setzen Sie fort, was laufen soll.
- Ein eigener Listener einer Website, der dieselben Dateien gelesen hat, kann bleiben; er wird nur für Teile gefragt,
  die nicht angehalten sind.

## Tests

`expCronjobPauseTest` (keine Datenbank): Zu Beginn ist nichts angehalten; Anhalten und Fortsetzen eines Teils, mit der
Historie und wer es getan hat; `all`; ein einzeln ausgeführtes Skript, während sein Teil angehalten ist, auch mit einem
Verzeichnis im Namen; wer angehalten hat, wird vergessen, wenn sich die Datei seitdem geändert hat; von anderen
geschriebene Zustandsdateien; Teilnamen, die das Verzeichnis verlassen würden; beschädigte Zeilen in der Historie;
`Pause=disabled`; `runcronjobs.php` fragt die Pause vor dem Filter und erneut vor jedem weiteren Skript ab und
protokolliert beide Arten des Auslassens (aus seinem Quelltext gelesen); eine Zustandsdatei, die sich nicht öffnen
lässt, wird ersetzt; die Zustandsdatei und die Historie erhalten `StorageFilePermissions`; eine Notiz wird beim Anhalten
und Fortsetzen gespeichert, auf eine kurze Zeile gekürzt, und der Hinweis gibt an, seit wann, von wem und warum;
Zeilen in der Historie ohne Notiz; die Anzahl in der Setup-Übersicht; das Notizfeld der Konsole und wo die Notiz
erscheint, in beiden Designs. `AdminSetupOverviewTest` deckt den Wert der Kachel und seine Übersetzungen ab.

## Verwandte Seiten

- [Einen Cronjob-Teil auslassen (der Filter)](filter-fuer-cronjob-teile.md)
- [Cronjob-Konsole](cronjobs-in-der-konsole.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
