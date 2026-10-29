# Hinweis zum Aktualisieren: Die Installationsprogramme schreiben den Cronjob (6.0.15)

Eine neue Installation aktiviert Erweiterungen, deren Arbeit in Cronjob-Teilen läuft: die Job-Warteschlange
(`expqueue`), die Zeitpläne der Importe (`sqliimport`) und in der Administration die KI-Warteschlange (`expai`). Die
Teile des Kernels versenden die Benachrichtigungen und führen die Workflows, die Inhaltsjobs, das Audit und den
Suchindex aus. Bisher hat keiner davon von selbst gearbeitet: Jemand musste `runcronjobs.php` von Hand in eine Crontab
eintragen, und auf den meisten neuen Websites tat das niemand, sodass Jobs in der Warteschlange und geplante Importe
ewig warteten.

Ab 6.0.15 schreibt jedes Installationsprogramm den Cronjob der neuen Installation, und ein Befehl fügt ihn bei einer
bestehenden Installation hinzu, zeigt ihn an oder entfernt ihn. Das Dashboard warnt, wenn die Teile nicht laufen.

## Was die Installationsprogramme tun

Der Setup-Assistent, der Kickstarter und `exp:install` (das den Kickstarter ausführt) schreiben die Crontab-Zeilen im
letzten Schritt (`eZStepFinal`, Code `kernel/classes/expcrontabsetup.php`). Die Website-Pakete (`sevenx_*`,
`cjw_multisite`, `plain_site`) brauchen nichts Eigenes: Der Schritt des Kernels läuft für jedes Paket.

- **Wessen Crontab**: die Crontab des Systembenutzers, dem das Installationsverzeichnis gehört. Läuft das
  Installationsprogramm als dieser Benutzer (im Assistenten der Benutzer des Webservers, auf der Shell
  `runuser -u <Eigentümer> -- php bin/php/console exp:install --apply`), schreibt es die eigene Crontab; läuft es als root,
  schreibt es `crontab -u <Eigentümer>`. Die Crontab von root wird nie für eine Website geschrieben, und nie die eines
  anderen Benutzers: Eine Installation, die root gehört, oder ein Installationsprogramm, das als dritter Benutzer
  läuft, bekommt die Zeilen stattdessen ausgegeben.
- **Welche Teile**: `cronjob.ini [CrontabSettings] Parts[]`, jeder nur, wenn die Installation ihn mit mindestens einem
  Skript definiert: die Kernel-Teile `global`, `frequent` und `infrequent` immer, `expqueue`, `sqliimport` und `expai`,
  wenn ihre Erweiterung aktiv ist (`ActiveExtensions[]` oder `ActiveAccessExtensions[]` des Admin-Siteaccess). Eine
  Erweiterung kann mit `Parts[]` in ihrer `settings/cronjob.ini.append.php` einen eigenen Teil hinzufügen.
- **Mit welchem Siteaccess**: Die Teile aus `AdminParts[]` (`expqueue`, `sqliimport`, `expai`) laufen mit dem
  Admin-Siteaccess (`-s admin`), in dem ihre Erweiterungen und Jobtypen bekannt sind; die anderen mit dem
  Standard-Siteaccess.
- **Wann**: `cronjob.ini [AdminSettings] CrontabSchedule_<Teil>`, derselbe Zeitplan, den Setup > Cronjobs vorschlägt:
  `frequent` alle fünf Minuten, `global` jede Viertelstunde, `infrequent` stündlich um :17, `expqueue` und
  `sqliimport` jede Minute, `expai` alle fünf Minuten.
- **Mit welchem PHP**: `[AdminSettings] PhpCliPath`, sonst das PHP, mit dem das Installationsprogramm auf der
  Kommandozeile läuft, sonst das, das Setup > Cronjobs findet.

Eine Standardinstallation (`sevenx_multisite`) in `/var/www/example` bekommt:

```
# BEGIN Exponential cronjobs: /var/www/example
# The cronjob parts of this installation; php bin/php/installcrontab.php --show, --add or --remove (in /var/www/example).
*/15 * * * * cd /var/www/example && /usr/bin/php runcronjobs.php -q >> var/log/cronjob-global.log 2>&1
*/5 * * * * cd /var/www/example && /usr/bin/php runcronjobs.php -q frequent >> var/log/cronjob-frequent.log 2>&1
17 * * * * cd /var/www/example && /usr/bin/php runcronjobs.php -q infrequent >> var/log/cronjob-infrequent.log 2>&1
* * * * * cd /var/www/example && /usr/bin/php runcronjobs.php -q -s admin expqueue >> var/log/cronjob-expqueue.log 2>&1
* * * * * cd /var/www/example && /usr/bin/php runcronjobs.php -q -s admin sqliimport >> var/log/cronjob-sqliimport.log 2>&1
*/5 * * * * cd /var/www/example && /usr/bin/php runcronjobs.php -q -s admin expai >> var/log/cronjob-expai.log 2>&1
# END Exponential cronjobs: /var/www/example
```

und das Installationsprogramm meldet:

```
Cron job: added to the crontab of example (6 lines; php bin/php/installcrontab.php --show)
```

### Markiert, wiederholbar, andere Zeilen unberührt

- Die Zeilen stehen zwischen `# BEGIN Exponential cronjobs: <Wurzel>` und `# END Exponential cronjobs: <Wurzel>`, so
  kann ein Benutzer mehrere Installationen betreiben, jede mit ihrem eigenen Block.
- Ein zweiter Lauf ersetzt den Block an seiner Stelle oder schreibt nichts, wenn er gleich ist (`already in the
  crontab`).
- Jede andere Zeile der Crontab (ein `MAILTO=`, andere Jobs, die Blöcke anderer Installationen) bleibt, wie sie ist,
  an ihrer Stelle.
- Einen Teil, den eine Zeile außerhalb des Blocks für diese Installation schon ausführt (von Hand geschrieben, aus
  `exponential.cron`), lässt der Block weg und nennt ihn, damit er nie zweimal läuft.
- Nach dem Schreiben wird die Crontab zurückgelesen und verglichen.

### Wenn es keine Crontab gibt

In einem Container, ohne `crontab`-Befehl, ohne Erlaubnis (`/etc/cron.allow`) oder wenn der Benutzer nicht bestimmt
werden kann, scheitert nichts: Die Installation wird fertig, und das Installationsprogramm gibt die genauen Zeilen
aus, die einzutragen sind:

```
Cron job: not written: there is no crontab command on this system (or it cannot be run). Add these lines to the crontab of example:
  # BEGIN Exponential cronjobs: /var/www/example
  ...
```

Der Setup-Assistent zeigt dasselbe auf seiner letzten Seite unter **Cronjobs**. Die Zeilen stehen auch in
`var/log/setup.log`.

### Abschalten

- Für jede Installation, die die Einstellung liest: `settings/override/cronjob.ini.append.php`

  ```ini
  [CrontabSettings]
  Install=disabled
  ```

- Für einen Lauf von `exp:install`: `--no-crontab` (oder für den Kickstarter die Umgebungsvariable
  `EXP_INSTALL_CRONTAB=0`).

Abgeschaltet gibt das Installationsprogramm die Zeilen aus, statt sie zu schreiben.

## Bei einer bestehenden Installation: bin/php/installcrontab.php

```bash
php bin/php/installcrontab.php --show            # geplante Zeilen, der Block in der Crontab, die letzten Läufe
php bin/php/installcrontab.php --add --dry-run   # was --add ändern würde
php bin/php/installcrontab.php --add --apply             # den Block schreiben oder aktualisieren
php bin/php/installcrontab.php --remove --dry-run
php bin/php/installcrontab.php --remove --apply          # den Block herausnehmen, sonst nichts
```

Als Eigentümer der Installation ausführen, oder als root mit `--allow-root-user` (dann wird `crontab -u <Eigentümer>`
geschrieben). `--admin-siteaccess=<Name>` nennt den Admin-Siteaccess, wenn er nicht `admin` heißt (oder
`[CrontabSettings] AdminSiteAccess` in `settings/override` setzen). `--add` und `--remove` geben `PASS` oder `FAIL`
aus; kann die Crontab nicht geschrieben werden, gibt `--add` die Zeilen zum Eintragen von Hand aus und endet mit
`FAIL` (Exit-Code 1). `--dry-run` listet die Zeilen, die gingen (`-`) und kämen (`+`).

Eine Installation mit von Hand geschriebenen Zeilen für einige Teile behält sie: `--add` fügt nur die Teile hinzu, die
noch keine Zeile ausführt. Um eine solche Installation auf den markierten Block umzustellen, zuerst die von Hand
geschriebenen Zeilen entfernen, dann `--add` ausführen.

## Wann die Teile zuletzt liefen

`runcronjobs.php` hält jeden Teil, den es startet und beendet, in `var/log/cronjob-lastrun.json` fest (Teil,
Siteaccess, Start, Ende; ein einzelnes Skript mit `--script` wird nicht festgehalten). Mit `-q` schreiben die Zeilen
kein Log, solange alles gut geht; an diesem Eintrag ist ein Lauf zu erkennen.

- **Das Dashboard** hat den Block **Cronjobs** (`dashboard.ini [DashboardBlock_cronjobs]`, für Benutzer mit
  `setup/managecronjobs`): jeder Teil des Cronjobs, sein Zeitplan, wann er zuletzt startete und ob das pünktlich ist.
  Er warnt, wenn noch kein Teil gelaufen ist und wenn ein Teil länger als das Doppelte seines Intervalls plus fünf
  Minuten (mindestens eine Stunde) nicht gestartet ist. Template `design/standard/templates/dashboard/cronjobs.tpl`,
  Fetch `fetch( 'setup', 'cronjob_status' )`.
- **`installcrontab.php --show`** gibt dieselbe Tabelle und eine `WARN`-Zeile aus.
- **`exp:syscard`** und seine Cronjob-Angabe lesen den Eintrag ebenfalls.

## Einstellungen

`settings/cronjob.ini`:

| Einstellung | Standard | Bedeutung |
| --- | --- | --- |
| `[CrontabSettings] Install` | `enabled` | `disabled`: Die Installationsprogramme geben die Zeilen aus, statt sie zu schreiben |
| `[CrontabSettings] Parts[]` | `global`, `frequent`, `infrequent`, `expqueue`, `sqliimport`, `expai` | die Teile des Cronjobs, jeder, wenn die Installation ihn definiert |
| `[CrontabSettings] AdminParts[]` | `expqueue`, `sqliimport`, `expai` | die Teile, die mit dem Admin-Siteaccess laufen |
| `[CrontabSettings] AdminSiteAccess` | leer (`admin`, wenn es ihn gibt) | der Admin-Siteaccess, in `settings/override` gesetzt |
| `[CrontabSettings] CrontabCommand` | `crontab` | der Crontab-Befehl, ein Name im `PATH` oder ein voller Pfad |
| `[AdminSettings] CrontabSchedule_<Teil>` | siehe oben | der Zeitplan eines Teils |
| `[AdminSettings] PhpCliPath` | leer | das PHP der Zeilen |

## Was beim Aktualisieren zu tun ist

Bei einer bestehenden Installation ändert sich nichts, bis der Befehl läuft. Prüfen, was er täte, dann hinzufügen:

```bash
php bin/php/installcrontab.php --show
php bin/php/installcrontab.php --add --dry-run
php bin/php/installcrontab.php --add --apply
```

Führt die Crontab die Teile schon aus, lässt `--add` diese Teile weg (oder schreibt nichts). Danach das Dashboard
öffnen: Der Block **Cronjobs** zeigt die Teile als *never ran*, bis jeder einmal gestartet ist.

Code: `kernel/classes/expcrontabsetup.php`, Befehl `kernel/private/classes/commands/installcrontab.php`, Schritt
`kernel/setup/steps/ezstep_final.php`. Test ohne Datenbank und ohne Crontab:
`tests/tests/kernel/classes/expCrontabSetupTest.php`. Siehe auch [Cronjobs](../../anleitungen/cronjobs.md).
