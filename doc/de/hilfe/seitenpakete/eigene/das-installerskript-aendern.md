---
title: Das Installerskript ändern
audience: developer, partner
category: install-upgrade
order: 87
summary: Wie der PHP-Installer eines Seitenpakets in CreateSites eingebunden ist - die Einstellungsdateien, die Hook-Funktionen eZSite* und wann sie laufen, die Installer-Klasse und ihre Schritte der Nachinstallation, die Erweiterungen und Tabellen, die sie einrichtet - und wie Sie ihn in Ihrem eigenen Paket sicher ändern.
keywords: Installerskript, eZSitePreInstall, eZSitePostInstall, eZSiteINISettings, eZSiteAdminINISettings, eZSiteCommonINISettings, eZSiteRoles, eZSitePreferences, eZSiteFinalText, eZSiteInstaller, initSteps, executeSteps, extension_list, settings-file, installer script
type: help
---
# Das Installerskript ändern

Diese Seite ist für Entwickler, die ändern, was ein Seitenpaket tut, wenn es installiert wird: welche Erweiterungen es einschaltet, welche
Einstellungen und Siteaccesses es schreibt, welche Rollen es anlegt und welche Reparaturen laufen, nachdem die Inhalte an ihrem Platz sind.
Sie erklärt, wie der Kernel die PHP-Dateien des Pakets aufruft und wie die Installer-Klasse von 7x aufgebaut ist, damit Sie einen Fork
ändern können, ohne die Kette zu unterbrechen.

7x und die Exponential Foundation ermutigen Sie ausdrücklich, das fertige Paket per Pull Request an
se7enxweb/exponential.packages.exponential.earth zu teilen: siehe [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Inhalt

1. [Wie die Dateien geladen werden](#wie-die-dateien-geladen-werden)
2. [Die Hook-Funktionen](#die-hook-funktionen)
3. [Die Installer-Klasse](#die-installer-klasse)
4. [Schritte der Nachinstallation](#schritte-der-nachinstallation)
5. [Erweiterungen und ihre Tabellen](#erweiterungen-und-ihre-tabellen)
6. [Eine Änderung vornehmen](#eine-änderung-vornehmen)

## Wie die Dateien geladen werden

`package.xml` führt die Dateien des Installers in `<settings>` auf:

```xml
<settings>
  <settings-file filename='ini-site.php'/>
  <settings-file filename='sevenx-multi-site-installer.php'/>
  <settings-file filename='install-scripts.php'/>
  ...
</settings>
```

Während `CreateSites` liest `kernel/setup/steps/ezstep_create_sites.php` die `settings-files` des Seitenpakets und führt für jede
existierende Datei `include_once( <package path>/settings/<file> )` aus. Nichts anderes in einer Datei läuft von selbst: Der Kernel ruft dann
die Hook-Funktionen unten auf, wenn sie definiert sind (`function_exists()`). Dasselbe geschieht, gleich welches Frontend die Installation
gestartet hat: der Setup-Assistent, der Kickstarter oder `exp:install`
([Installationsskripte in einem Seitenpaket](../grundlagen/installationsskripte-in-einem-seitenpaket.md)).

## Die Hook-Funktionen

In der Reihenfolge, in der `ezstep_create_sites.php` sie erreicht:

| Funktion | Im Paket von 7x definiert durch | Was ihr Rückgabewert tut |
|---|---|---|
| `eZSitePreInstall( $siteType )` | `install-scripts.php` | nichts; läuft, bevor die verlangten Pakete installiert werden (7x: `preInstall()` des Installers) |
| `eZSiteINISettings( $parameters )` | `ini-site.php` | zusätzliche Einstellungen für den öffentlichen Siteaccess |
| `eZSiteAdminINISettings( $parameters )` | `ini-admin.php` | zusätzliche Einstellungen für den Siteaccess der Administration |
| `eZSiteCommonINISettings( $parameters )` | `ini-common.php` | Einstellungen für jeden Siteaccess |
| `eZSiteRoles( $parameters )` | `roles.php` | zusätzliche Rollen und Richtlinien |
| `eZSitePreferences( $parameters )` | `preferences.php` | zu setzende Benutzervoreinstellungen |
| `eZSitePostInstall( &$parameters )` | `install-scripts.php` | nichts; läuft nach Inhalten, Einstellungen und Rollen (7x: `postInstall()` des Installers) |
| `eZSiteFinalText( $parameters )` | von 7x nicht genutzt | Text, der der Seite **Beendet** des Assistenten hinzugefügt wird |

Jede Hook-Datei von 7x hat wenige Zeilen, die den Installer erzeugen und eine seiner Methoden zurückgeben:

```php
function eZSiteINISettings( $parameters )
{
    $installer = new sevenxMultiSiteInstaller( $parameters );

    return $installer->siteINISettings();
}
```

> **Warnung**
> Die Namen der Hooks sind globale Funktionen. Pro Lauf wird nur ein Seitenpaket installiert, aber jede aufgeführte Datei wird eingebunden:
> Zwei Dateien, die dieselbe Funktion definieren, beenden die Installation mit einem fatalen Fehler.

## Die Installer-Klasse

`settings/sevenx-multi-site-installer.php` definiert `class sevenxMultiSiteInstaller extends eZSiteInstaller`
(`kernel/classes/ezsiteinstaller.php`). Ihre Teile:

| Teil | Rolle |
|---|---|
| `initSettings( $parameters )` | sammelt die Werte, die die Schritte nutzen, mit `addSetting( name, value )`; zurückgelesen mit `setting( name )` |
| `addSetting( 'extension_list', array( ... ) )` | die Erweiterungen, die die Website einschaltet, in Reihenfolge (eine früher aufgeführte Erweiterung gewinnt bei Einstellungen) |
| `initSteps()` | füllt `$this->Steps['post_install']` mit den Schritten der Nachinstallation |
| `preInstall()` | Arbeit, bevor die Pakete installiert werden |
| `siteINISettings()`, `adminINISettings()`, `commonINISettings()` | die Arrays mit Einstellungen, die die Hooks zurückgeben |
| `siteRoles()`, `sitePreferences()` | die Rollen und Voreinstellungen |
| `solutionName()`, `solutionExtensionName()`, `solutionVersion()` | die Namen und die Version, die der Installer festhält (`simple`, `sevenx_multisite_default_installer`, `1.0` in der Klasse von 7x) |

Die Klassendatei lädt außerdem `post-install-fix.php` mit `require_once` (Reparaturen, die der Installer teilt); ein Fork lässt diese Zeile
auf seine eigene Kopie zeigen.

## Schritte der Nachinstallation

`initSteps()` beschreibt die Nachinstallation als Liste; `eZSiteInstaller::postInstall()` führt sie mit `executeSteps()` aus:

```php
$postInstallSteps = array(
    array( '_function' => 'dbBegin', '_params' => array() ),
    array( '_function' => 'setVersion', '_params' => array() ),
    array( '_function' => 'postInstallFixContentObjectNameLanguages', '_params' => array() ),
    // ...
);
$this->Steps['post_install'] = $postInstallSteps;
```

Jede `_function` ist eine Methode des Installers (Ihre eigene oder eine, die `eZSiteInstaller` bereitstellt), aufgerufen mit `_params`. Wenn
ein Schritt einen Fehler meldet und der Fehlerhandler mit `ERR_ABORT` antwortet, wird der Rest der Liste übersprungen, und der Schritt wird in
`eZSiteInstaller::abortedStep()` behalten (Nummer, Funktion, Anzahl), sodass das Setup weiß, dass die Nachinstallation nicht fertig wurde.
Die Liste von 7x führt ihre Reparaturen deshalb früh aus: Ein Kommentar in `initSteps()` vermerkt, dass `executeSteps` die Kette beim ersten
Schritt abbricht, der einen Fehler meldet, und dass mehrere spätere Schritte empfindlich sind.

> **Tipp**
> Fügen Sie einen neuen Schritt als Methode namens `postInstall<What>( $params = false )` hinzu, machen Sie ihn idempotent (zweimal
> ausführbar ohne Schaden) und lassen Sie ihn bei Daten, die er nicht findet, protokollieren und weitermachen statt abbrechen, wie es die
> Schritte von 7x tun.

## Erweiterungen und ihre Tabellen

Einige Daten, die eine Website braucht, sind keine Inhalte und stehen in keinem Paket. Der Installer von 7x legt die Tabellen der
Erweiterungen an, die er aktiviert, und befüllt einige davon aus den eigenen Dateien der Erweiterungen: `declaredExtensionSchemas()` nennt
`sevenx_themes_media` (Schema und Daten: die Layouts, Tags und Bewertungen der Demo-Inhalte), `explayouts` (Schema und Daten) und die Schemas
von `ezstarrating`, `ezgmaplocation`, `eztags`, `enhancedselection2`, `cjw_newsletter` und `syndication`; `extensionSchemas()` fügt jede
andere aufgeführte Erweiterung hinzu, die `share/db_schema.dba` oder `sql/<engine>/schema.sql` mitbringt. Die Details stehen auf
[Layoutdaten und share-DBA-Dateien](../sevenx-multisite/layoutdaten-und-share-dba-dateien.md).

Für Ihren Fork heißt das: Ein Layout oder Tag, das Sie in einer neuen Installation haben wollen, gehört in die `share/db_data.dba` der
Erweiterung, der es gehört, nicht in das Seitenpaket.

## Eine Änderung vornehmen

1. Ändern Sie die Methode oder fügen Sie einen Schritt in Ihrer umbenannten Installer-Klasse hinzu.
2. Prüfen Sie die Syntax: `php -l packages/<name>/settings/<your-installer>.php`.
3. Bauen Sie das Archiv neu ([Mit build.sh neu bauen](mit-build-sh-neu-bauen.md)).
4. Installieren Sie es in einer Sandbox und lesen Sie `var/log/setup.log` ([In einer Sandbox testen](in-einer-sandbox-testen.md)).
5. Schreiben Sie die Änderung in den `<changelog>` des Pakets und in den Changelog des Repositorys.

Das Repository von 7x zeigt den Stil der Commits für solche Änderungen, zum Beispiel einen Commit, mit dem der Installer des Pakets
sevenx_multisite ezownerchange aktiviert (15. Oktober 2026).

## Verwandte Seiten

- [Ein eigenes Seitenpaket erstellen](README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Das Skript des Multisite-Installers](../sevenx-multisite/das-skript-des-multisite-installers.md)
- [Korrekturen nach der Installation](../sevenx-multisite/korrekturen-nach-der-installation-von-sevenx-multisite.md)
- [Die Installerskripte von CJW](../cjw-multisite/die-installerskripte-von-cjw-multisite.md)
- [Installationsskripte von Paketen](../../../../en/us/install/04-choosing-an-install-method.md#package-install-scripts-the-multi-site-installer)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
