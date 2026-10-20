# Einen Cronjob-Teil auslassen

Lesen Sie diese Seite, wenn Sie Cronjob-Teile eine Weile anhalten möchten, ohne die Crontab zu ändern (während eines
Releases, einer Datenmigration oder eines Neuaufbaus des Suchindex), oder wenn Sie eine Extension bauen, die entscheidet,
ob ein Teil laufen darf.

## Kurz gesagt

- Bevor `runcronjobs.php` die Skripte eines Teils ausführt, fragt es den `ezpEvent`-Filter `cronjob/part/run`.
- Der Listener erhält `true` und gibt `true` zurück, um den Teil auszuführen. Alles andere lässt den Teil aus: Der Lauf
  gibt einen Hinweis aus, führt keines seiner Skripte aus und endet mit Exit-Code 0.
- Registrieren Sie den Listener in `site.ini [RunnableSettings] Listeners[]`. Diese Liste wird an die Kommandozeile
  gehängt; `[Event] Listeners[]` wird nur an Webanfragen gehängt.
- Ohne Listener läuft jeder Teil wie bisher.
- Ein unter **Setup > Cronjobs** pausierter Teil wird ausgelassen, bevor der Filter gefragt wird; siehe
  [Cronjob-Teile pausieren](../../../eng-us/features/6.0/cronjob-pause.md). Für einen Stoppschalter verwenden Sie lieber das als einen eigenen
  Listener.

## Der Filter

```php
$run = ezpEvent::getInstance()->filter( 'cronjob/part/run', true, $part, $siteaccess, $scripts, $single );
```

| Argument | Wert |
|---|---|
| `$run` | `true` oder das, was der vorige Listener zurückgegeben hat |
| `$part` | der Teil, wie auf der Kommandozeile angegeben (`frequent`, `infrequent`, ...); `''` für die Skripte von `[CronjobSettings]` (kein Teil angegeben) und für ein einzelnes Skript, das mit `--script=<file>` läuft |
| `$siteaccess` | der Siteaccess, in dem die Skripte laufen |
| `$scripts` | die Dateinamen der Skripte, die der Lauf starten würde, wie `cronjob.ini` sie aufführt (ein Name bei `--script`) |
| `$single` | `true` für ein einzelnes Skript, das mit `--script` läuft, `false` für einen Teil und für die Skripte von `[CronjobSettings]` |

Ein einzelnes Skript, das mit `--script` läuft (so startet auch **Setup > Cronjobs** ein Skript), wird mit dem Teil `''`
gefragt: Es sagt nicht, zu welchem Teil das Skript gehört. Ein Listener, der einen Teil anhält, muss `$scripts` ansehen,
um dessen Skripte anzuhalten, wenn sie allein laufen, wie das Beispiel unten es tut.

**Nur `true` führt den Teil aus.** `false`, `null`, `1` oder `'yes'` lassen ihn aus, sodass ein Listener, der sein
`return` vergisst, jeden Teil anhält: Geben Sie `$run` zurück, wenn der Listener keine Meinung hat. Mehrere Listener
laufen in der Reihenfolge ihrer Registrierung, jeder mit dem, was der vorige zurückgegeben hat.

Ein Listener, der sich nicht aufrufen lässt (seine Klasse ist nicht per Autoload ladbar, etwa weil die Autoload-Arrays
nicht neu erzeugt wurden), wird übersprungen und protokolliert, und der Teil läuft. Probieren Sie einen Stoppschalter
einmal aus, bevor Sie sich auf ihn verlassen.

Ein Listener, der eine Ausnahme wirft, beendet den Lauf wie eine Ausnahme in einem Cronjob-Skript: Keines der Skripte des
Teils läuft, die Kommandozeile zeigt „An unexpected error has occurred“, `var/log/error.log` enthält die Meldung, und der
Exit-Code ist 1. An diesem Punkt wird keine Skriptsperre gehalten, sodass der nächste Lauf nicht blockiert ist.

Der Hinweis zu einem ausgelassenen Teil geht auf die Standardfehlerausgabe, auch mit `-q`, wie der Hinweis zu einem Teil
ohne Skripte; die Zeile `Running cronjob part '<part>'` (oder `Running cronjob script '<file>'`) wird ausgegeben, bevor
der Filter gefragt wird.

Der Filter wird gefragt, nachdem die Skripte gewählt sind und bevor das erste startet, sodass ein ausgelassener Teil
keine Skriptsperre nimmt und keinen Audit-Eintrag `system.cronjob.run` schreibt; `runcronjobs.php` selbst wird als
`system.command.run` mit Exit-Code 0 erfasst, wie jeder Befehl. `--list` ist nicht betroffen.

`./console cron:<part>` und die Cronjob-Seite der Administrationsoberfläche (**Setup > Cronjobs**) starten
`runcronjobs.php`, daher gilt der Filter auch für sie.

## Beispiel: eine Stoppdatei pro Teil

In `extension/<name>/settings/site.ini.append.php`:

```ini
[RunnableSettings]
Listeners[]=cronjob/part/run@myExtCronjobStop::partRun
```

```php
class myExtCronjobStop
{
    /**
     * Leaves a run out while var/cronjob_stop/all.stop exists, a part while var/cronjob_stop/<part>.stop
     * exists, and a script run alone while var/cronjob_stop/<script>.stop exists (notification.php.stop).
     *
     * @param bool $run
     * @param string $part
     * @param string $siteaccess
     * @param string[] $scripts
     * @param bool $single
     * @return bool
     */
    public static function partRun( $run, $part, $siteaccess, $scripts, $single = false )
    {
        $dir = eZSys::varDirectory() . '/cronjob_stop';
        if ( file_exists( "$dir/all.stop" ) )
            return false;
        if ( $single )
            return file_exists( $dir . '/' . basename( $scripts[0] ) . '.stop' ) ? false : $run;
        $name = $part !== '' ? $part : 'default';
        return file_exists( "$dir/$name.stop" ) ? false : $run;
    }
}
```

Ein Listener, der seine Entscheidung anderswo als in einer Datei hält (in der Datenbank, in einem Cache), funktioniert
genauso; er wird einmal pro Lauf von `runcronjobs.php` aufgerufen, nachdem der Siteaccess und die
Extension-Einstellungen geladen sind, und kann daher in einer Extension oder einem Siteaccess registriert werden.

## Tests

`CronjobPartRunFilterTest` (ohne Datenbank): Ohne Listener läuft jeder Teil; ein Listener erhält den Teil, den
Siteaccess, die Skripte und ob ein einzelnes Skript läuft, und kann den Teil auslassen; nur `true` führt ihn aus; ein
Listener in `[RunnableSettings] Listeners[]` wird angehängt; ein Listener, der eine Ausnahme wirft, lässt den Teil nicht
laufen; `runcronjobs.php` fragt den Filter vor dem ersten Skript und endet ohne Fehler, wenn der Teil ausgelassen wird.

## Verwandte Seiten

- [Befehle, Cronjob-Teile und Modulansichten als Klassen](../../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md)
- [Filter für Zugriff und Ansichts-Cache für Extensions](../../../eng-us/features/6.0/access-and-cache-filters.md)
- [Erweiterungspunkte](../../../eng-us/bc/6.0/rad-extension-points.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
