# Die Umschaltung des Administrationsdesigns: Was sich für Entwickler geändert hat

Lesen Sie diese Seite, wenn Sie ein Administrationsdesign pflegen, eine Erweiterung, die Kernel-Klassen für die
Administration überschreibt, Code, der die Design-Einstellungen oder den Design-Base-Cache liest, oder eine
Installation, die `sevenx_themes_admin_classic` verwendet. Sie führt die neue Klasse auf, die Änderung am
Cache-Schlüssel, das Verhalten auf den persistenten Workern von Velocity und was die Kernel-Overrides von
`sevenx_themes_admin_classic` ersetzt. Gültig seit dem 15. Oktober 2026.

Die Seite für Benutzer ist [die Funktionsseite](../../../eng-us/features/6.0/ui-switch-design.md), die Einstellungen
stehen in [der Anleitung](../../anleitungen/ui-umschalter-design.md). Das Framework, auf dem dies aufbaut (Registry,
Modul `uiswitch`, Richtlinien, Speicherung in der Sitzung, Varianten-Schlüssel des Caches), ist in
[UI-Umschaltung: Hinweise für Entwickler](ui-umschalter.md) beschrieben.

## Kurz gesagt

- Neue Kernel-Klasse `expUISwitchDesignProvider` (`kernel/classes/uiswitch/expuiswitchdesignprovider.php`), der
  Provider `design` der UI-Umschaltung. Er setzt `site.ini [DesignSettings] SiteDesign` und
  `AdditionalSiteDesignList[]` im Speicher, für die Anfragen eines Benutzers, der ein anderes Administrationsdesign
  gewählt hat.
- Neue statische Methode `eZTemplateDesignResource::designBaseCachePath( $siteAccess = false )`. Der Dateiname des
  Design-Base-Caches des aktuellen Siteaccess enthält den Varianten-Schlüssel der UI-Umschaltung **nur, wenn eine
  Wahl angewendet wird**. Ohne Wahl ist der Dateiname byte-genau derselbe wie bisher. Ein namentlich angefragter
  Siteaccess wird nie umgeschaltet.
- Kompilierte Templates, `{cache-block}` und der Cache der Inhaltsansichten werden durch den Varianten-Schlüssel der
  UI-Umschaltung aus [dem Framework](ui-umschalter.md) getrennt. Der Template-Override-Cache war bereits nach der
  Design-Liste geschlüsselt.
- Eine Umschaltung leert keinen Cache: kein `eZCache::clearByTag()`.
- `apply()` und `reset()` leeren die statischen und globalen Design-Daten, sodass ein persistenter Velocity-Worker nie
  das Design eines Benutzers in die nächste Anfrage mitnimmt.
- Die Kernel-Overrides von `sevenx_themes_admin_classic` (`ezpKernelWeb`, `global_functions.php`) sind ab dessen
  Version 0.2.5 überflüssig und müssen entfernt werden.

## Die Provider-Klasse

`expUISwitchDesignProvider extends expUISwitchProvider` (Kennung `design`, Richtlinie `uiswitch/design`,
Einstellungsgruppe `uiswitch.ini [UISwitch_design]`).

| Methode | Verhalten |
|---|---|
| `__construct( $identifier, array $settings = array(), $siteDefault = null, $root = '', ?array $designExtensions = null )` | Die letzten drei Argumente gibt es für Tests: das Design des Siteaccess, das Wurzelverzeichnis der Installation und die Design-Erweiterungen. Im Kernel werden sie aus den Einstellungen gelesen. |
| `defaultValue()` | Das `[DesignSettings] SiteDesign` des Siteaccess, einmal vor jedem `apply()` gelesen. |
| `allowedValues()` | Zuerst der Standard, dann jeder Eintrag von `AllowedValues[]`, der ein Design-Name ist (`/^[a-z][a-z0-9_]{0,63}$/`), einen Eintrag in `DesignChain[]` hat und als `design/<design>` oder als `design/<design>` einer aktiven Design-Erweiterung existiert (`design.ini [ExtensionSettings] DesignExtensions[]`). |
| `chain( $design )` | `DesignChain[<design>]`, an `;` aufgeteilt, getrimmt, ohne Duplikate, ohne das Design selbst und ohne Namen, die keine Design-Namen sind; `array()` bei einer leeren Kette, `null`, wenn das Design keinen Eintrag hat. |
| `isValid( $value )` | Strikte Zugehörigkeit zu `allowedValues()` (geerbt). Nichts aus der Anfrage wird als Pfad verwendet. |
| `label( $value )` | `DesignLabel[<design>]` über `ezpI18n::tr( 'kernel/uiswitch', ... )`; ohne Eintrag der Design-Name. |
| `title()` | „Administrationsdesign“, Kontext `kernel/uiswitch`. |
| `apply( $value )` | Lehnt einen ungültigen Wert oder den Standard ab. Setzt sonst `SiteDesign` und `AdditionalSiteDesignList` auf `eZINI::instance()` (der Instanz der Anfrage) mit `setVariable()` und leert dann die Design-Daten (siehe unten). Schreibt keine Datei und ändert nichts prozessweit. |
| `reset()` | Leert die Design-Daten und vergisst den angewendeten Wert. |
| `currentValue()` | Der in dieser Anfrage angewendete Wert, sonst der Standard (geerbt). |
| `cacheVariantKey( $value )` | `''` für den Standard, sonst `design-<design>` (geerbt). |

Die Design-Daten zu leeren heißt `eZTemplateDesignResource::clearInMemoryCache()` (das globale
`eZTemplateDesignSetting`, das statische Override-Array und die Match-File-Caches, das globale
`eZOverrideTemplateCacheMap`) und das Entfernen von `$GLOBALS['eZTemplateDesignResourceBases']`. Die Basen
namentlich angefragter Siteaccesses (`eZTemplateDesignResourceSiteAccessBases`) werden nicht angetastet: Sie folgen
nie einer Umschaltung.

Die Registry ruft `apply()` aus `ezpKernelWeb::requestInit()` auf, nach dem Start der Sitzung und vor der
Login-Vorprüfung, dem Request/Input-Ereignis, `eZLocale` und jedem Template, sodass `$site.design` und jedes Template
das gewählte Design sehen. Im Setup-Assistenten (`validity-check-required`) wird es nicht aufgerufen, und
`eZTemplateDesignResource::designSetting( 'site' )` gibt weiterhin zuerst `site-design-override` zurück, wo das
gesetzt ist.

## Der Schlüssel des Design-Base-Caches

`eZTemplateDesignResource::allDesignBases()` speichert die Liste der Design-Verzeichnisse in
`<cache>/<DESIGN_BASE_CACHE_NAME><md5>.php`, wenn `site.ini [DesignSettings] DesignLocationCache=enabled` gilt. Der
Dateiname kommt jetzt aus der neuen öffentlichen statischen Methode:

```php
static function designBaseCachePath( $siteAccess = false )
{
    $siteAccessName = $siteAccess ? (string)$siteAccess : ( $GLOBALS['eZCurrentAccess']['name'] ?? '' );
    if ( !$siteAccess && !empty( $GLOBALS['expUISwitchVariantKey'] ) )
        $siteAccessName .= '-' . $GLOBALS['expUISwitchVariantKey'];

    return eZSys::cacheDirectory() . '/' . self::DESIGN_BASE_CACHE_NAME . md5( $siteAccessName ) . '.php';
}
```

- Keine Wahl angewendet (der Varianten-Schlüssel ist `''`): `md5( <siteaccess> )`, dieselbe Datei wie bisher.
- Eine Wahl angewendet: `md5( <siteaccess> . '-' . <variant key> )`, zum Beispiel `md5( 'admin-design-admin4' )`.
- `$siteAccess` angegeben: `md5( $siteAccess )`, egal was der aktuelle Benutzer gewählt hat. Diese Liste wird aus den
  Einstellungen des Siteaccess auf der Festplatte gelesen, die eine Umschaltung nie ändert.

Der Varianten-Schlüssel ist der gesamte Schlüssel der Anfrage, die Provider nach Kennung sortiert und mit `.`
verbunden (`design-admin4.language-ger-DE`). Ein Benutzer, der nur die Sprache der Oberfläche geändert hat, erhält
daher ebenfalls eine eigene Datei des Design-Base-Caches; ihr Inhalt entspricht dem der Standarddatei.

Code, der den Dateinamen selbst gebildet hat (`DESIGN_BASE_CACHE_NAME . md5( $siteaccess )`), findet weiterhin die
Standarddatei; rufen Sie `designBaseCachePath()` auf, um die Datei der aktuellen Anfrage zu erhalten.

Vor dieser Änderung setzte `sevenx_themes_admin_classic` für seinen Siteaccess `DesignLocationCache=disabled` im
Speicher, weil eine Design-Liste pro Sitzung sich nicht eine Cache-Datei pro Siteaccess teilen kann. Der Kernel lässt
den Cache eingeschaltet.

## Die anderen Caches

| Cache | Schlüssel | Änderung |
|---|---|---|
| Design-Base-Cache | Siteaccess | + Varianten-Schlüssel, wenn eine Wahl angewendet wird (siehe oben) |
| Template-Override-Cache (`override/override_<md5>.php`) | md5 von `overrideKeys()`: Siteaccess, Standard-Design, `SiteDesign`, `AdditionalSiteDesignList[]`, Design-Erweiterungen | keine: bereits eine Datei pro Design-Liste |
| Kompilierte Templates (`eZTemplateCompiler::compilationFilename()`) | Template, Zeichensatz, Locale, Zugang, Pagelayout, Indexdatei | + `-<variant key>`, wenn einer angewendet wird ([UI-Umschaltung](ui-umschalter.md)). Nötig, weil kompilierter Code aufgelöste Includes enthält. |
| `{cache-block}` (`eZTemplateCacheFunction`) | Platzierung, Siteaccess, Schlüssel | Siteaccess + `-<variant key>`, zur Laufzeit aus `$GLOBALS['expUISwitchVariantKey']` gelesen |
| Cache der Inhaltsansichten (`eZNodeviewfunctions`) | Hash mit dem Siteaccess | + Hash-Eintrag `uiswitch` |
| Rollenabhängiger HTTP-Seiten-Cache, Antwort-Cache von Velocity | | eine mit einer Wahl gerenderte Seite wird nicht gespeichert |
| ezjscore-Packer | Dateiliste | keine: Das `page_head_style.tpl` jedes Designs nennt seine eigenen Dateien |

Eine Umschaltung ruft kein `eZCache::clearByTag()` auf. Die Erweiterung leerte bei jeder Umschaltung und jedem
Zurücksetzen die Caches `template` und `template-block` der ganzen Installation; mit den obigen Schlüsseln hat jedes
Design eigene Caches, und die Caches der Benutzer ohne Wahl bleiben unberührt.

## Velocity und andere persistente Worker

Ein Velocity-Worker bedient viele Anfragen in einem Prozess. Pro Anfrage baut `index.php` ein neues `ezpKernelWeb`,
dessen `eZINI::resetAllInstances()` frische INI-Instanzen liefert, sodass ein angewendetes Design nie in den
Einstellungen bleibt. Was in einem Prozess bleibt, sind statische und globale Daten:

- `expUISwitch::beginRequest()` ruft zu Beginn jedes `requestInit()` das `reset()` jedes Providers auf, der früher im
  Prozess einen Wert angewendet hat, und setzt den Varianten-Schlüssel auf `''`.
- `expUISwitchDesignProvider::reset()` und `apply()` leeren die oben aufgeführten statischen und globalen
  Design-Daten. Das statische Override-Array ist dasjenige, das einmal ein kompiliertes Admin-Pagelayout mit einem
  öffentlichen Header ausgeliefert hat (24. September 2026).
- Die Registry überspringt einen Provider, dessen Klasse ein Worker nicht kennt (`class_exists`), sodass das Nennen
  der Klasse in den Einstellungen vor dem Neustart der Worker keine Anfragen abbricht. Erzeugen Sie die Autoloads neu
  und stellen Sie bereit, bevor die Einstellungen eine neue Provider-Klasse nennen.

## Die Kernel-Overrides von sevenx_themes_admin_classic ersetzen

Bis 0.2.4 ersetzte `sevenx_themes_admin_classic` zwei Kernel-Dateien über `classes/kerneloverride/` und
`EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE`:

- `kernel/private/classes/ezpkernelweb.php`: eine alte Kopie von `ezpKernelWeb` mit einem Block am Ende von
  `__construct()`, der das Design umschaltete. Alles andere in der Kopie ist älter als Monate von Kernel-Änderungen
  (Sicherheits-Header, der 403-Pfad des Formular-Tokens, der Audit-Header, Korrekturen für Velocity); wer sie
  installiert, setzt den Kernel also zurück.
- `kernel/private/classes/global_functions.php`: eine alte Kopie ohne Design-Logik, nur mitgeliefert, weil das
  kopierte `ezpKernelWeb` sie aus seinem eigenen Verzeichnis einband.

Version 0.2.5 entfernt beide, das Modul `switchadmindesign`, sein Werkzeug in der Werkzeugleiste und seine
Zurücksetzungen der Werkzeugleisten, und verlässt sich stattdessen auf die Umschaltung des Kernels. `admin_classic`
(Beschriftung „Klassisch (serverseitig)“, Kette `admin`) steht in den Kernel-Standardwerten von
`settings/uiswitch.ini [UISwitch_design]`, neben `admin4l`, `admin4`, `adminui`, `admin3` (Kette `admin2;admin`) und
`admin`; es wird überall angeboten, wo die Erweiterung aktiv ist, weil die Erweiterung das Design-Verzeichnis
registriert. Die eigene `settings/uiswitch.ini.append.php` der Erweiterung wiederholt dieselben Werte, was nicht
schadet (jedes Design wird einmal angeboten). Ebenso wird `adminui` nur in einem Siteaccess mit
`ActiveAccessExtensions[]=exp_adminui` angeboten, in einer Standardinstallation also allein im Siteaccess `adminui`
([Anleitung](../../anleitungen/ui-umschalter-design.md#adminui-in-anderen-siteaccesses-anbieten)).

Das Pagelayout der klassischen `admin` (`design/admin/templates/pagelayout.tpl`) zeigt sein rechtes Menü an, sofern
der Benutzer es nicht geschlossen hat (`admin_right_menu_show=0`), wie admin4; bisher sah ein Benutzer, der die
Einstellung nie gesetzt hatte, es eingeklappt. So bleibt das Werkzeug zum Umschalten erreichbar.

In einer Installation, die die Overrides verwendet hat:

1. Aktualisieren Sie die Erweiterung auf 0.2.5 oder neuer. Stellen Sie sicher, dass
   `extension/sevenx_themes_admin_classic/classes/kerneloverride/` nicht mehr existiert.
2. Erzeugen Sie die Autoloads **ohne** `-o` / `--kernel-override` neu:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   ```

   Ordnet `var/autoload/ezp_override.php` `ezpKernelWeb` noch der Erweiterung zu, erzeugen Sie sie mit `-o` neu,
   nachdem die Dateien entfernt sind, oder verschieben Sie sie; lassen Sie `EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE` in
   `config.php` ausgeschaltet, sofern keine andere Erweiterung es braucht.
3. Entfernen Sie einen Eintrag `PolicyOmitList[]=switchadmindesign` aus Ihren eigenen Einstellungen, falls Sie einen
   hinzugefügt haben, und ersetzen Sie die Richtlinie `switchadmindesign/switch` in Ihren Rollen durch
   `uiswitch/design`.
4. Stellen Sie bereit (`./console exp:velocity deploy --allow-root-user`).

| Altes Verhalten der Erweiterung | Verhalten des Kernels jetzt |
|---|---|
| Design in `ezpKernelWeb::__construct()` umgeschaltet, mit Lesen der Sitzung, bevor die Sitzung gestartet war | `expUISwitch::applyRequest()` in `requestInit()`, nach dem Start der Sitzung; startet nie eine Sitzung |
| Nur ein Siteaccess (`SharedAdminSiteaccessName`) | Jeder Siteaccess der Umschaltung (`uiswitch.ini [UISwitchSettings] SiteAccessList[]`, leer: jeder Siteaccess mit einem Administrationsdesign) |
| `SiteDesign=admin3` fest eingetragen | `SiteDesign` = das gewählte Design |
| `AdditionalSiteDesignList` als Array aus der Sitzung übernommen | Die Sitzung enthält nur den Design-Namen; die Kette kommt immer aus `DesignChain[]` |
| Erlaubte Werte fest in der View eingetragen (`admin_classic`, `admin3`) | `AllowedValues[]` (die Kernel-Standardwerte enthalten beide), gefiltert nach installierten Designs mit einer Kette |
| Keine Berechtigungsprüfung beim Anwenden | `uiswitch/design` bei jeder Anfrage geprüft; eine Wahl, deren Richtlinie entfallen ist oder die aus der Liste verschwunden ist, wird verworfen |
| `DesignLocationCache=disabled` für den Siteaccess | Cache bleibt; der Dateiname enthält den Varianten-Schlüssel, wenn eine Wahl angewendet wird |
| `eZCache::clearByTag( 'template' )` und `( 'template-block' )` bei jeder Umschaltung | Kein Cache wird geleert; Caches pro Design geschlüsselt |
| Modul `switchadmindesign/switch` (POST) und `reset` (GET, zustandsändernd) | `uiswitch/set` (nur POST, Formular-Token, seitenübergreifende Anfragen abgelehnt, 400/403/404/405); die Wahl des Standards setzt zurück |
| Kein Audit-Eintrag | `access.uiswitch.change` (Provider `design`, vorher, nachher, Siteaccess) |
| Nur Sitzung | Sitzung, oder mit `RememberPreference=enabled` auch die Einstellungen des Benutzers |
| Werkzeug `admin_switch_design` in `Toolbar_admin_developer`; `toolbar.ini` der Erweiterung setzte `AvailableToolArray[]` und `Toolbar_admin_right` zurück | Kernel-Werkzeug `admin_ui_switch` in `Toolbar_admin_right`, in admin4 und im klassischen Design `admin`; keine Zurücksetzung der Werkzeugleiste |
| Kopie von `global_functions.php` | Nichts: Die Kernel-Datei wird verwendet |

## Eine Installation prüfen

- `grep -rn "kerneloverride" var/autoload/ezp_override.php` gibt nichts aus (oder die Datei existiert nicht).
- Ohne Wahl ist die Datei des Design-Base-Caches eines Siteaccess weiterhin `<cache>/<DESIGN_BASE_CACHE_NAME>`, gefolgt
  von `md5( '<siteaccess>' )`.
- `php vendor/bin/phpunit tests/tests/kernel/classes/uiswitch/expUISwitchDesignProviderTest.php` läuft durch (ohne
  Datenbank): erlaubte Werte, Ketten, Beschriftungen, Anwenden und Zurücksetzen, der Dateiname des Design-Base-Caches
  mit und ohne Wahl und das Setzen des Varianten-Schlüssels durch die Registry.

## Verwandte Seiten

- [Funktion: das Administrationsdesign wählen](../../../eng-us/features/6.0/ui-switch-design.md)
- [Anleitung: die Umschaltung des Administrationsdesigns](../../anleitungen/ui-umschalter-design.md)
- [UI-Umschaltung: Hinweise für Entwickler](ui-umschalter.md)
- [Velocity-Engines und das Bereitstellen einer PHP-Änderung](velocity-engines.md)
- [Erweiterungen: Verhaltensänderungen](verhaltensaenderungen-von-erweiterungen.md)
