# Extensions: finden, installieren, konfigurieren, bauen und veröffentlichen

Diese Anleitung richtet sich an Site-Entwickler und Entwickler. Eine Extension ist ein Ordner unter `extension/`, der Exponential Einstellungen, Templates, Module, Inhaltstypen, Befehle oder
Cronjobs hinzufügt, ohne den Kernel anzutasten. Am Ende dieser Seite können Sie eine Extension finden, installieren und
einschalten, sie konfigurieren, eine eigene funktionierende Extension bauen (in zehn Minuten von Hand oder erzeugt von
den RAD-Assistenten) und sie so veröffentlichen, dass andere sie installieren können. Sie brauchen etwa 45 Minuten,
ein Terminal und einen Browser. Sie benötigen eine laufende Exponential-6-Installation ([Erste Schritte](erste-schritte.md))
und eine Administrator-Anmeldung. Befehle werden im Wurzelverzeichnis der Installation ausgeführt; die Konsolenbefehle
sind dieselben Programme wie `php bin/php/<name>.php`.

## 1. Wie Extensions funktionieren, in einer Minute

| Tatsache | Wo sie festgelegt wird |
|---|---|
| Eine Extension ist ein Ordner, der nach der Extension benannt ist, zum Beispiel `extension/ezflow`. | `[ExtensionSettings] ExtensionDirectory` in `settings/site.ini` (Voreinstellung `extension`) |
| Sie bewirkt nichts, bis sie eingeschaltet wird. | `[ExtensionSettings] ActiveExtensions[]` in `settings/site.ini` (für die ganze Installation) oder `ActiveAccessExtensions[]` (für einen Siteaccess) |
| Die Reihenfolge der Liste ist die Rangfolge: Eine früher geladene Extension setzt sich bei Einstellungen und Design-Templates gegen eine spätere durch. Mit `ExtensionOrdering=enabled` bestimmen zuerst die in `extension.xml` deklarierten Abhängigkeiten die Ladereihenfolge. | [Die Seite „Extensions“](extensions-seite.md), [Ladereihenfolge](../../eng-us/features/6.0/extension-loading-order.md) |
| Klassen in ihrem Ordner `classes/` werden über ein Autoload-Array gefunden, das Sie neu erzeugen. | `php bin/php/ezpgenerateautoloads.php -e` |
| Ihre Einstellungen sind `.ini.append.php`-Dateien in `extension/<name>/settings/`. | [Ablageorte für Einstellungs-Overrides](../spezifikationen/6.0/ini-override-platzierungen.md) |
| Sie beschreibt sich selbst in `ezinfo.php` und `extension.xml`. | [Extension-Metadaten](../spezifikationen/6.0/extension-metadaten.md) |

So sehen Sie, welche Extensions in dieser Installation eingeschaltet sind:

```bash
./console exp:ini list site.ini/ExtensionSettings --allow-root-user
```

Erwartet: der Block `[ExtensionSettings]` mit `ExtensionDirectory=extension` und je aktiver Extension eine Zeile
`ActiveExtensions[]=<name>`, in Ladereihenfolge.

## 2. Eine Extension finden

1. Öffnen Sie in der Administrationsoberfläche **Setup > Extensions** (`/setup/extensions`). Die Liste zeigt jede
   Extension, die auf der Festplatte liegt, die aktiven zuerst in Ladereihenfolge, dann die übrigen nach Namen.
2. Durchsuchen und filtern Sie die Liste (alle, aktive, inaktive, Probleme) oder sortieren Sie sie nach Namen. Jede Karte zeigt die Beschreibung,
   die Lizenz, die Projektseite und die Abhängigkeiten; **Details** ergänzt den Autor, die verwendeten Designs und die
   Downloads. Die ganze Seite wird in [Die Seite „Extensions“](extensions-seite.md) erklärt.
3. Die Version stammt aus den eigenen Dateien der Extension; ein Strich bedeutet also, dass die Extension keine angibt.

Im Terminal sind die Extensions, die vorhanden, aber nicht aktiv sind, die als inaktiv gekennzeichneten Scopes:

```bash
./console exp:ini scopes extension --allow-root-user
```

Erwartet: eine Zeile je Extension, zum Beispiel
`extension:autonotifications   extension   extension/autonotifications/settings  (inactive extension)`.

Wo Sie nach Extensions suchen, die noch nicht auf Ihrer Festplatte liegen: die Composer-Pakete `se7enxweb/<name>` auf
Packagist (zum Beispiel `se7enxweb/ezflow`) und die Projektseiten, die im
[Extension-Katalog](../../eng-us/features/6.0/extensions/README.md) genannt sind, der für jede mitgelieferte Extension eine Seite hat.

Referenz: [Extension-Liste: sortieren, untersuchen und herunterladen](../../eng-us/features/6.0/extension-list-and-downloads.md).

## 3. Eine Extension installieren

Wählen Sie den passenden Weg. Alle drei enden mit demselben Ordner unter `extension/`.

**Mit Composer** (das Paket der Extension deklariert den Installationstyp `ezpublish-legacy-extension`; den
Paketnamen finden Sie auf der Seite der Extension):

```bash
composer require se7enxweb/ezflow
```

**Durch Kopieren des Ordners** (ein Release-Archiv oder ein Git-Klon):

```bash
cd extension
git clone https://github.com/se7enxweb/ezflow.git ezflow
cd ..
```

Klonen Sie nur dann eine Extension von Hand, wenn `composer.json` sie nicht verlangt. Bei einem Composer-Paket lassen
Sie Composer den Klon anlegen (`composer config --global 'preferred-install.se7enxweb/*' source`, dann
`composer require` oder `composer reinstall`): Ein von Hand angelegter Klon anstelle eines Pakets, das Composer als
Archiv installiert hat, wird beim nächsten Update des Pakets ersetzt, samt Historie und Commits. Siehe
[Mit Git entwickeln](../../eng-us/help/develop-with-git.md).

**Aus einem Paket** (`.ezpkg`, zum Beispiel eines, das Sie in Abschnitt 7 heruntergeladen haben):

```bash
php ezpm.php import /path/to/ezflow.ezpkg
```

`php ezpm.php help` listet die Paketbefehle auf; [ezpm: der Paketmanager auf der Kommandozeile](../../eng-us/features/6.0/ezpm-package-manager-cli.md)
erklärt jeden davon. Installieren Sie zuerst in eine Kopie der Site: Der Paketinstaller schreibt Inhalte.

Prüfen Sie, ob der Ordner dort liegt, wo der Kernel sucht:

```bash
ls extension/ezflow/ezinfo.php extension/ezflow/extension.xml
```

Erwartet: Beide Dateinamen werden ausgegeben. Wenn Sie Ihre eigenen Extensions getrennt von den installierten halten,
fügen Sie einen zweiten Ordner hinzu, wie in [Extensions in mehr als einem Ordner](../../eng-us/features/6.0/additional-extension-directories.md) beschrieben.

## 4. Die Extension einschalten

Im Browser: Öffnen Sie **Setup > Extensions**, klicken Sie auf der Karte der Extension auf **Aktivieren**, dann auf
**Änderungen prüfen** und **Änderungen anwenden**. Die Prüfung zeigt die Risiken und die resultierenden
`ActiveExtensions[]`-Zeilen; die Seite sagt Ihnen danach, was sie gespeichert hat und wo sie eine Sicherungskopie der
Einstellungsdatei abgelegt hat ([Die Seite „Extensions“](extensions-seite.md)).

Im Terminal (dieselbe Änderung, in der globalen Override-Datei):

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' ezflow global --dry-run --allow-root-user
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' ezflow global --allow-root-user
```

Der erste Befehl gibt die Änderung nur aus; erwartet: die ganze Datei `settings/override/site.ini.append.php`, wie sie
aussehen würde, mit markiertem `+ActiveExtensions[]=ezflow` und der Zeile `Dry run: ... nothing written`. Der zweite
schreibt sie (eine Sicherung der Datei kommt nach `var/backup/ini/<timestamp>/`). Um sie nur für einen Siteaccess
einzuschalten, verwenden Sie den Scope `siteaccess:<name>` und die Variable `ActiveAccessExtensions[]`.

Sorgen Sie dann dafür, dass das System die neuen Klassen und Einstellungen sieht:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

Erwartet vom ersten Befehl: `Found <n> classes, added <n> of them to the autoload array.` (fügen Sie zuerst `-n` hinzu,
um die Anzahl zu sehen, ohne etwas zu schreiben). Wenn die Site über PHP-FPM oder Velocity ausgeliefert wird, laden
Sie diesen Dienst danach neu, damit seine Worker die Änderung sehen: siehe [Bereitstellung](bereitstellung.md) und
[Betrieb einer Site](betrieb-einer-website.md).

Viele Extensions brauchen mehr als das Einschalten: eine Datenbanktabelle, eine Inhaltsklasse, einen Cronjob. Lesen
Sie für diese Schritte die Seite der Extension im [Katalog](../../eng-us/features/6.0/extensions/README.md). Denken Sie daran,
dass eine neue `.tpl`-Datei einen geleerten Template-Override-Cache braucht, was `--clear-all` erledigt.

## 5. Die Extension konfigurieren

Die Voreinstellungen einer Extension liegen in `extension/<name>/settings/*.ini.append.php`. Diese bearbeiten Sie nie.
Sie überschreiben sie an einer Stelle, die Vorrang vor ihnen hat und ein Upgrade übersteht.

1. Finden Sie heraus, welche Datei den Wert setzt und was gilt:

   ```bash
   ./console exp:ini where site.ini/ExtensionSettings/ActiveExtensions --allow-root-user
   ```

   Erwartet: jede Datei, die die Variable setzt, in Ladereihenfolge, und der geltende Wert.
2. Ändern Sie ihn in dem Scope, den Sie meinen:

   | Scope-Wort | Geschriebene Datei | Verwenden Sie es für |
   |---|---|---|
   | `global` | `settings/override/<file>.ini.append.php` | diese Installation |
   | `siteaccess:<name>` | `settings/siteaccess/<name>/<file>.ini.append.php` | eine Site einer Multisite-Installation |
   | `extension:<name>` | `extension/<name>/settings/<file>.ini.append.php` | die Voreinstellungen, die eine Extension mitbringt |

   ```bash
   ./console exp:ini set site.ini/SiteSettings/SiteName "My site" siteaccess:site --allow-root-user
   ```
3. Leeren Sie den INI-Cache, damit die Änderung gelesen wird: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.

Committen Sie `settings/override/` nie; das Verzeichnis kann Passwörter enthalten. Mehr zum Befehl:
[Einstellungen über die Kommandozeile ändern](../../eng-us/features/6.0/exp-ini-command.md). Einstellungen, die nur gelten, während ein
Siteaccess läuft, und die in einer Extension mitgeliefert werden: [Site-spezifische Einstellungen in Extensions](../../eng-us/features/6.0/multi-site-ini-overrides.md).

## 6. Eine eigene Extension bauen

### 6.1 Die kleinste funktionierende Extension, von Hand

Sie erstellen eine Seite `/hello/world/<name>`, die mit Namen grüßt. Wählen Sie einen Namen, der Ihnen gehört:
Kleinbuchstaben, Ziffern und Unterstriche. Diese Anleitung verwendet `myhello`.

```bash
mkdir -p extension/myhello/settings extension/myhello/modules/hello \
         extension/myhello/design/standard/templates/hello
```

Legen Sie diese Dateien an.

`extension/myhello/ezinfo.php` (was **Setup > Über** und die Extension-Liste lesen):

```php
<?php
class myhelloInfo
{
    public static function info()
    {
        return array( 'Name'      => "My Hello",
                      'Version'   => "0.1.0",
                      'Copyright' => "Copyright (C) 2026 You",
                      'License'   => "GNU General Public License v2.0 (or any later version)",
                      'Info_url'  => "https://example.com/myhello" );
    }
}
?>
```

`extension/myhello/extension.xml` (wird zuerst gelesen; halten Sie die Version gleich der oben):

```xml
<?xml version="1.0" encoding="utf-8" ?>
<software>
    <metadata>
        <name>My Hello</name>
        <version>0.1.0</version>
        <license>GNU General Public License v2.0 (or any later version)</license>
        <info_url>https://example.com/myhello</info_url>
    </metadata>
</software>
```

`extension/myhello/settings/module.ini.append.php` (sagt dem Kernel, dass er in dieser Extension nach Modulen suchen soll):

```php
<?php /* #?ini charset="utf-8"?

[ModuleSettings]
ExtensionRepositories[]=myhello

*/ ?>
```

`extension/myhello/settings/design.ini.append.php` (sagt dem Template-System, dass es im Design dieser Extension suchen soll):

```php
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=myhello

*/ ?>
```

`extension/myhello/modules/hello/module.php` (das Modul, mit einer View):

```php
<?php
$Module = array( 'name' => 'hello' );

$ViewList = array();
$ViewList['world'] = array( 'script' => 'world.php',
                            'params' => array( 'Name' ) );
?>
```

`extension/myhello/modules/hello/world.php` (die View: Sie liefert den Inhalt der Seite):

```php
<?php
$Module = $Params['Module'];
$name   = $Params['Name'] ? $Params['Name'] : 'world';

$tpl = eZTemplate::factory();
$tpl->setVariable( 'name', $name );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:hello/world.tpl' );
$Result['path']    = array( array( 'text' => 'Hello', 'url' => false ) );
?>
```

`extension/myhello/design/standard/templates/hello/world.tpl` (das Template; `wash` maskiert, was der Besucher eingegeben hat):

```
<div class="content-view-full">
    <h1>Hello, {$name|wash}</h1>
    <p>This page is served by my first extension.</p>
</div>
```

Prüfen Sie, ob PHP jede Datei akzeptiert, und schalten Sie die Extension dann wie in Abschnitt 4 ein:

```bash
php -l extension/myhello/ezinfo.php
php -l extension/myhello/modules/hello/module.php
php -l extension/myhello/modules/hello/world.php
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' myhello global --allow-root-user
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

Erwartet von `php -l`: dreimal `No syntax errors detected in <file>`. Öffnen Sie dann `/hello/world/Ada` auf Ihrer
Site: Die Seite zeigt **Hello, Ada** im Layout Ihrer Site. `/setup/extensions` listet nun **My Hello** mit der Version 0.1.0.

Zwei Dinge sollten Sie wissen, bevor Sie darauf aufbauen:

- Eine View ohne Eintrag `'functions'` hat keine Zugriffskontrolle: Jeder, der die Site erreicht, kann sie öffnen.
  Bevor sie etwas Privates zeigt, deklarieren Sie eine Funktion und eine Richtlinie: `$FunctionList['read'] = array();`
  in `module.php` und `'functions' => array( 'read' )` an der View; erteilen Sie sie dann unter **Benutzer > Rollen und
  Richtlinien**. Siehe [Anfrageregeln](../../eng-us/features/6.0/request-rules.md) und [Sicherheit und Audit](sicherheit-und-audit.md).
- Eine Seite, die von einem persistenten Worker (Velocity) ausgeliefert wird, braucht Worker, die die neuen Klassen
  sehen: Laden Sie neu, wie in [Bereitstellung](bereitstellung.md) beschrieben.

Neuer Code wird als Klassen geschrieben: Ab 6.0.15 sind die eigenen Befehle, Cronjob-Teile und Views des Produkts
Klassen, und die einfachen Dateien, die Sie oben geschrieben haben, funktionieren weiterhin. Wie Sie die View als
Klasse schreiben, lesen Sie in
[Befehle, Cronjob-Teile und Modul-Views als Klassen](../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md#a-module-view).

### 6.2 Die RAD-Werkzeuge schreiben lassen

Die RAD-Werkzeuge (**Setup > RAD**, `/setup/rad`) kennen jede Stelle, an der Exponential erweitert werden kann, und
schreiben für die meisten davon eine vollständige Extension. Sie benötigen die Richtlinie `setup/setup`.

1. Öffnen Sie `/setup/rad`. Die Seite listet die Erweiterungspunkte in sieben Gruppen auf (Inhalte, Templates, Module,
   Workflow, Speicherung, Paketierung, Zugriff); `/setup/rad/(show)/tools` listet nur die mit einem Werkzeug.
2. Klicken Sie auf das Werkzeug des gewünschten Punkts. Der Assistent fragt nach einem Extension-Namen und danach, was
   erzeugt werden soll, und listet dann **jede Datei, die er schreiben würde**, unter `extension/<name>` auf, bevor er
   irgendeine schreibt.
3. Klicken Sie auf **In extension/ anlegen**. Ein Assistent überschreibt nie eine bestehende Extension und schreibt
   nichts außerhalb von `extension/<name>`. **Als ZIP herunterladen** liefert Ihnen dieselben Dateien, ohne sie zu schreiben.
4. Schalten Sie die neue Extension ein (Abschnitt 4) und erzeugen Sie die Autoloads neu.
5. Öffnen Sie **Setup > RAD > Übersicht der Erweiterungspunkte** (`/setup/radsurvey`) und die Zustandsprüfung auf
   `/setup/rad`: Ihre Registrierung wird mitgezählt, und alles Konfigurierte, das nicht funktionieren kann, wird mit
   einem Lösungsweg aufgeführt.

Die Assistenten und ihre Adressen:

| Sie möchten | Öffnen Sie |
|---|---|
| Ein Design (Templates, Stylesheets, Overrides) | `/setup/designextension` |
| Template-Operatoren, Fetch-Funktionen, Template-Funktionen | `/setup/templateoperator` |
| Einen Handler, der einen Teil des Systems ersetzt (Anmeldung, Mail, Sitzung, Suche, Mehrwertsteuer und Versand im Shop, REST-Filter, ...) | `/setup/handlerextension/<kind>` |
| Eine Extension, die nur Einstellungen enthält | `/setup/settingsextension` |
| Eine als Skript geschriebene Inhaltsklasse | `/setup/contentextension` |
| Einen Datentyp | `/setup/datatype` |
| Ein Workflow-Ereignis | `/setup/workflowevent` |
| Ein Modul mit Views oder ein Modul über bestehenden Datenbanktabellen | `/setup/moduleextension`, `/setup/modulewizard` |
| Eine ersetzte Kernel-Klasse (der letzte Ausweg) | `/setup/kerneloverride` |

Referenz: [RAD-Werkzeuge](../../eng-us/features/6.0/rad-tools.md), die kuratierten [Erweiterungspunkte](../../eng-us/bc/6.0/rad-extension-points.md),
[RAD-Sicherheit](../../eng-us/bc/6.0/rad-security.md).

### 6.3 Der Handler-Assistent, Schritt für Schritt

Ein Handler ist eine eigene Klasse, die das System anstelle seiner eigenen Voreinstellung aufruft, ausgewählt über
eine einzige INI-Einstellung. Der Mail-Handler eignet sich gut für den Anfang.

1. Öffnen Sie `/setup/handlerextension/mail`. Die Seite trägt den Titel dieser Art und sagt, was die Voreinstellung
   tut und was Sie ersetzen.
2. Füllen Sie unter **Die Extension** das Feld **Name der Extension** aus (zum Beispiel `my_mail`), dazu einen Titel
   und eine Zusammenfassung, und übernehmen oder ändern Sie Klassenname, Autor, Version und Lizenz, die der Assistent
   vorschlägt.
3. Lassen Sie unter **Was hineinkommt** die angehakten Teile eingeschaltet. **Was geschrieben werden muss** listet die
   Methoden auf, die Ihre Klasse implementieren muss; **Einschalten** zeigt die INI-Zeile, die der Assistent für Sie schreibt.
4. Klicken Sie auf **Vorschau aktualisieren**. **Jede Datei, bevor sie geschrieben wird** listet die Pfade auf.
5. Klicken Sie auf **In extension/ anlegen**. Erwartet: eine Meldung, die die unter `extension/my_mail` geschriebenen
   Dateien aufführt.
6. Schalten Sie die Extension ein (Abschnitt 4), erzeugen Sie die Autoloads neu und leeren Sie die Caches. Die erzeugte
   Klasse wird vom System aufgerufen, sobald die Einstellung, die der Assistent geschrieben hat, sie auswählt; bearbeiten
   Sie die Klasse, damit sie Ihre Arbeit erledigt.

Prüfen Sie, ob sich jede Klasse der Installation, Ihre eingeschlossen, laden lässt:

```bash
php bin/php/checkclasses.php --quiet-ok --allow-root-user
```

Erwartet: keine Ausgabe, wenn sich alles laden lässt. Eine Klasse, die sich nicht laden lässt, wird mit Namen
aufgeführt (andernfalls würde sie beim ersten Zugriff eine ganze Anfrage zum Absturz bringen).

### 6.4 Eigene Einstellungen, Inhalte und Cronjobs

| Sie möchten | Legen Sie in Ihrer Extension ab | Dann |
|---|---|---|
| Eigene Einstellungen | `settings/<name>.ini.append.php` oder eine neue `settings/<name>.ini` mit Voreinstellungen | lesen mit `eZINI::instance( '<name>.ini' )`; anzeigen oder ändern mit `exp:ini` |
| Einen Cronjob-Teil | `cronjobs/<part>.php` und in `settings/cronjob.ini.append.php` die Zeilen `[CronjobSettings]` `ExtensionDirectories[]=<name>`, dann einen Block `[CronjobPart-<part>]` mit `Scripts[]=<part>.php` | `./console list cron` zeigt ihn als `cron:<part>` |
| Einen Befehl | `bin/php/<name>.php` | `php extension/<name>/bin/php/<name>.php --help` |
| Eine Übersetzung | `translations/<locale>/translation.ts` | fügen Sie `TranslationExtensions[]=<name>` in `[RegionalSettings]` hinzu |
| Eine Datenbanktabelle | `share/db_schema.dba` (die Assistenten schreiben sie auf Wunsch) | installieren Sie sie mit dem Paketinstaller |
| Mitsprache bei Downloads, beim Bearbeitungszugriff auf Entwürfe, bei Kollaborationseinträgen, Benachrichtigungen oder View-Cache-Schlüsseln | einen Listener für `content/download/access`, `content/edit/access`, `collaboration/item/access`, `content/notification/create` oder `content/view/cachekeys` in `[Event] Listeners[]` (der Assistent für Einstellungs-Extensions schreibt einen mit Beispiel) | siehe [Zugriffs- und View-Cache-Filter](../../eng-us/features/6.0/access-and-cache-filters.md) |
| Mitsprache darüber, ob ein Cronjob-Teil läuft (ein Stoppschalter während einer Release) | einen Listener für `cronjob/part/run` in `[RunnableSettings] Listeners[]` | siehe [Einen Cronjob-Teil auslassen](../../eng-us/features/6.0/cronjob-part-filter.md) |
| Ein Druck- oder PDF-Layout eines Entwurfs in der Versionsvorschau | einen View-Modus in `content.ini [VersionView] ViewModes[]` und sein `node/view/<mode>.tpl` | siehe [Versionsvorschau in anderen View-Modi](../../eng-us/features/6.0/version-preview-view-modes.md) |
| Eine eigene Richtlinien-Beschränkung für `content/read` und die anderen Inhaltsfunktionen | einen Listener für `module/functionlist` und eine Klasse, die `ezpContentLimitationHandler` implementiert, registriert in `settings/site.ini.append.php` (der Assistent für Einstellungs-Extensions schreibt beides, mit dem Ereignis module/functionlist) | siehe [Inhalts-Richtlinienbeschränkungen von Extensions](../../eng-us/features/6.0/content-limitation-handlers.md); prüfen Sie einen Benutzer mit `./console exp:access:check` |

Die klassenbasierten Formen eines Befehls, eines Cronjob-Teils und einer View finden Sie unter
[Befehle, Cronjob-Teile und Modul-Views als Klassen](../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md) und
[deren Spezifikation](../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md). Um ein ganzes Kernel-Modul durch ein
eigenes zu ersetzen, siehe [Eine Extension kann ein Kernel-Modul ersetzen](../../eng-us/features/6.0/extension-module-override.md).

## 7. Die Extension veröffentlichen

Eine Release ist eine Nummer, die in die Dateien der Extension geschrieben wird, ein Commit und ein Tag. Ein
veröffentlichter Tag lässt sich nie korrigieren; prüfen Sie also, bevor Sie pushen, nicht danach.

1. **Wählen Sie die Nummer anhand dessen, was bereits veröffentlicht ist.** Nie aus dem Gedächtnis:

   ```bash
   git fetch --tags
   git tag -l 'v*' --sort=version:refname | tail -5
   ```

   Sortieren Sie nach Version, nicht nach Namen: `0.1.10` steht bei Sortierung nach Namen vor `0.1.8`. Nach `0.1.9`
   ist die nächste Release `0.1.10`; nur die letzte Stelle zählt hoch, sofern Sie nicht anders entscheiden.
2. **Schreiben Sie die Nummer in beide Metadaten-Dateien**, `'Version'` in `ezinfo.php` und `<version>` in
   `extension.xml`, ohne `v`-Präfix, sowie in `version` in `composer.json`, falls Sie eines führen. Prüfen Sie:

   ```bash
   php -l extension/myhello/ezinfo.php
   xmllint --noout extension/myhello/extension.xml
   ```

   Erwartet: `No syntax errors detected` und keine Ausgabe von `xmllint`.
3. **Wenn die Extension `share/filelist.md5` mitliefert**, aktualisieren Sie die Datei im selben Commit, damit die
   Upgrade-Prüfung nicht jede geänderte Datei als modifiziert auflistet. Das Wartungsskript der Installation erledigt
   das für Extensions, die bereits eine solche Datei mitliefern, wenn es mit `--extensions` aufgerufen wird (lesen Sie
   zuerst `bash bin/shell/generatefilelist.sh --help` und verwenden Sie `--dry-run`); die Erklärung steht in
   [Prüfung der Dateikonsistenz](../../eng-us/features/6.0/file-consistency-check.md).
4. **Erst committen, dann diesen Commit taggen.** Taggen Sie nie zuerst, um danach die Nummer zu erhöhen: Der Tag
   trüge sonst die alte Nummer.

   ```bash
   git add ezinfo.php extension.xml composer.json
   git commit -m "Updated: Version 0.1.1"
   git tag v0.1.1
   git push origin main v0.1.1
   ```
5. **Benachrichtigen Sie Packagist.** Ein Paket auf Packagist liest jeden Tag einmal; erscheint eine neue Version
   nicht, prüfen Sie, ob der Webhook des Repositorys zu Packagist existiert.
6. **Ist eine veröffentlichte Version fehlerhaft, verschieben oder löschen Sie ihren Tag nicht.** Veröffentlichen Sie
   die nächste Version mit der Korrektur und schreiben Sie in deren Hinweise, was an der vorigen falsch war. Packagist
   speichert, was es gelesen hat; ein verschobener Tag erzeugt deshalb zwei verschiedene Pakete unter einer Versionsnummer.

Um eine Extension ohne Repository weiterzugeben, laden Sie sie unter **Setup > Extensions** als Paket herunter: Öffnen
Sie **Details** in der Zeile und klicken Sie auf `.ezpkg`, `.zip`, `.tar.gz` oder `.tar.bz2`. Der Empfänger installiert
sie wie in Abschnitt 3. Eine Extension, die einen geheimen Wert oder Schlüsselmaterial enthält, wird nicht gepackt; die
Seite nennt die Dateien und Einstellungen, und [die Geheimnisprüfung](../../eng-us/bc/6.0/extension-package-secret-check.md)
erklärt, wie ein Maintainer ein bekanntes Beispiel kennzeichnet.

## 8. Wenn es nicht funktioniert

| Symptom | Prüfen Sie |
|---|---|
| Die Seite `/hello/world` meldet, das Modul existiere nicht | Die Extension steht in `ActiveExtensions[]` (Abschnitt 1), `ExtensionRepositories[]=myhello` steht in ihrer `settings/module.ini.append.php`, und die Caches wurden geleert |
| Die Seite ist leer oder zeigt das Layout ohne Inhalt | Den Template-Pfad: `extension/myhello/design/standard/templates/hello/world.tpl`, `DesignExtensions[]=myhello`, und eine neue `.tpl` braucht `php bin/php/ezcache.php --clear-all --allow-root-user` |
| `Class '...' not found` | `php bin/php/ezpgenerateautoloads.php -e`, dann das Leeren des Caches; dann PHP-FPM oder Velocity neu laden |
| Ihre Einstellung wirkt nicht | `./console exp:ini where <file>/<Block>/<Variable> --allow-root-user` zeigt, wer Vorrang hat; das kann eine spätere Extension oder die Override-Datei sein |
| Die Extension-Liste zeigt einen Strich als Version | `ezinfo.php` oder `extension.xml` fehlt oder lässt sich nicht parsen: `php -l` und `xmllint --noout` |
| Etwas in der Extension schlägt stillschweigend fehl | Die Zustandsprüfung auf `/setup/rad` listet Einstellungen auf, die eine nicht ladbare Klasse nennen, und `php bin/php/checkclasses.php --allow-root-user` nennt die Klasse |

## Siehe auch

- Vertiefung zu jedem Schritt: [Ladereihenfolge der Extensions](../../eng-us/features/6.0/extension-loading-order.md), [Extension-Metadaten](../spezifikationen/6.0/extension-metadaten.md),
  [Extensions in mehr als einem Ordner](../../eng-us/features/6.0/additional-extension-directories.md), [RAD-Werkzeuge](../../eng-us/features/6.0/rad-tools.md),
  [Erweiterungspunkte](../../eng-us/bc/6.0/rad-extension-points.md), [Die Extension-Oberfläche](../../eng-us/bc/6.0/rad-extension-surface.md).
- Die Extensions, die mit der Distribution ausgeliefert werden, und was jede tut: [Katalog](../../eng-us/features/6.0/extensions/README.md),
  [was eine neue Installation mitbringt](../../eng-us/features/6.0/default-extension-distribution.md).
- Weitere Anleitungen: [Erste Schritte](erste-schritte.md), [Das Inhaltsmodell und die Bearbeitung](inhaltsmodell-und-bearbeitung.md),
  [Betrieb einer Site](betrieb-einer-website.md), [Bereitstellung](bereitstellung.md), [Sicherheit und Audit](sicherheit-und-audit.md).
- Wie sich das Thema Extensions entwickelt hat: [Januar 2024](../../eng-us/history/2024/2024-01b.md) (Module aus Extensions),
  [August 2026](../../eng-us/history/2026/2026-08.md) (die Extension-Liste), [September 2026, zweite Hälfte](../../eng-us/history/2026/2026-09b.md) (Ladereihenfolge, Metadaten).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
