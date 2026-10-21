# Die UI-Umschaltung: die Provider-API

Mit der UI-Umschaltung kann ein angemeldeter Benutzer eine Einstellung der Administrationsoberfläche selbst (des
„Rahmens“) ändern: zuerst die Anzeigesprache, und was immer eine Erweiterung hinzufügt. Diese Seite ist für
Entwickler: der Vertrag der Provider, die Registry und ihr Lebenszyklus pro Anfrage, der Varianten-Schlüssel des
Caches, das Modul, die Fetch-Funktion, ein vollständiges Beispiel eines Providers in einer Erweiterung, die Regeln für
Exponential Velocity und die Hinweise zur Kompatibilität. Was Benutzer sehen, steht auf
[der Funktionsseite](../../../eng-us/features/6.0/ui-switch.md); die Einstellungen stehen in
[der Anleitung zu den Einstellungen](../../anleitungen/ui-umschalter-einstellungen.md).

## Überblick

| Datei | Klasse | Aufgabe |
|---|---|---|
| `kernel/classes/uiswitch/expuiswitchproviderinterface.php` | `expUISwitchProviderInterface` | der Vertrag der Provider |
| `kernel/classes/uiswitch/expuiswitchprovider.php` | `expUISwitchProvider` (abstrakt) | Einstellungen, Liste erlaubter Werte, strikte Validierung, Varianten-Schlüssel des Caches |
| `kernel/classes/uiswitch/expuiswitch.php` | `expUISwitch` | Registry, Lebenszyklus pro Anfrage, Speicherung in der Sitzung, Prüfung der Richtlinie, Varianten-Schlüssel des Caches, sichere Weiterleitung, Daten für die Werkzeugleiste |
| `kernel/classes/uiswitch/expuiswitchlanguageprovider.php` | `expUISwitchLanguageProvider` | der Sprach-Provider (`language`) |
| `kernel/uiswitch/module.php`, `set.php`, `state.php` | Modul `uiswitch` | `uiswitch/set` (POST), `uiswitch/state` (GET, JSON); eine Richtlinienfunktion pro Provider |
| `kernel/private/classes/views/uiswitch/set.php`, `state.php` | `Exponential\View\Kernel\Uiswitch\Set`, `State` | der Code der Views |
| `kernel/uiswitch/function_definition.php` | | `fetch( 'uiswitch', 'toolbar' )`, bedient von `expUISwitch::fetchToolbar()` |
| `design/admin4/templates/toolbar/full/admin_ui_switch.tpl`, `uiswitch/choices.tpl` | | das Werkzeug der Werkzeugleiste und das Markup der Auswahl |
| `design/standard/templates/uiswitch/refused.tpl` | | die Seite einer abgelehnten Änderung |

## Der Vertrag der Provider: expUISwitchProviderInterface

| Methode | Vertrag |
|---|---|
| `identifier()` | `[a-z][a-z0-9_]{0,31}`. Zugleich die Richtlinienfunktion `uiswitch/<identifier>` und die Einstellungsgruppe `uiswitch.ini [UISwitch_<identifier>]`. Muss der Kennung entsprechen, mit der die Registry den Provider erzeugt hat, sonst wird der Provider übersprungen. |
| `allowedValues()` | Die Werte, die ein Benutzer im aktuellen Siteaccess wählen darf, in der Reihenfolge der Anzeige: die Liste erlaubter Werte. Der Standardwert gehört immer dazu. |
| `label( $value )` | Eine Beschriftung für einen erlaubten Wert, für die Werkzeugleiste (bereits übersetzt, wo das sinnvoll ist). |
| `defaultValue()` | Der eigene Wert des Siteaccess, auf den die Umschaltung zurückfällt. Ihn zu wählen hebt die Umschaltung auf. |
| `isValid( $value )` | Nur wahr für ein Element von `allowedValues()`, strikt verglichen: kein Trimmen, keine Angleichung der Groß- und Kleinschreibung, keine Normalisierung. Nichts aus der Anfrage erreicht einen Pfad, einen Dateinamen oder einen Einstellungsnamen, bevor diese Methode ja gesagt hat. |
| `apply( $value )` | Wendet einen gültigen Wert, der nicht der Standard ist, für diese Anfrage an: überschreibt die Einstellungen im Speicher und setzt die statischen und globalen Daten zurück, die er berührt, damit neu gebaut wird, was aus den alten Einstellungen gebaut wurde. Gibt zurück, ob er angewendet wurde. |
| `reset()` | Macht rückgängig, was `apply()` in einer früheren Anfrage desselben Prozesses hinterlassen hat (statische und globale Daten). Wird auf einem persistenten Worker zu Beginn der nächsten Anfrage aufgerufen. |
| `currentValue()` | Der Wert, der für diese Anfrage gilt. |
| `cacheVariantKey( $value )` | `''` für den Standardwert (kein Name einer Cache-Datei ändert sich), sonst eine kurze Zeichenkette aus `[A-Za-z0-9_.-]`, die mit dem Wert gebaute Caches trennt. |

## Die Basisklasse: expUISwitchProvider

Erweitern Sie `expUISwitchProvider`, statt die Schnittstelle von Grund auf zu implementieren. Die Klasse wird von der
Registry als `new $class( $identifier, $settings )` erzeugt, wobei `$settings` die Gruppe `[UISwitch_<identifier>]`
ist, und lebt für eine Anfrage. Ein Provider implementiert `defaultValue()` und `apply()` und überschreibt `reset()`,
wenn `apply()` statische Daten berührt (mit Aufruf von `parent::reset()`).

| Element | Was es tut |
|---|---|
| `VALUE_PATTERN` | `/^[A-Za-z0-9][A-Za-z0-9_.@-]{0,63}$/`: wie ein konfigurierter oder gespeicherter Wert überhaupt aussehen darf. |
| `setting( $name, $default = null )` | Eine Einstellung der Gruppe des Providers. |
| `configuredValues()` | `AllowedValues[]` wie konfiguriert: nur die wohlgeformten (`VALUE_PATTERN`), jeder einmal. |
| `isAvailable( $value )` (protected) | Ob ein wohlgeformter konfigurierter Wert in dieser Installation verwendet werden kann (seine Dateien existieren, ...). Standardmäßig `true`; der Sprach-Provider prüft die Locale- und Übersetzungsdateien. |
| `allowedValues()` | Zuerst der Standardwert, dann jeder konfigurierte Wert, für den `isAvailable()` gilt; einmal pro Instanz berechnet. |
| `isValid( $value )` | Eine nicht leere Zeichenkette, die strikt in `allowedValues()` enthalten ist. |
| `label( $value )` | Der Wert selbst; überschreiben Sie die Methode für einen lesbaren Namen. |
| `title()` | Der Name der Auswahl: `[UISwitch_<identifier>] Title`, übersetzt im Kontext `kernel/uiswitch`, oder die Kennung mit großem Anfangsbuchstaben. |
| `labelLanguage( $value )` | Die Sprache, in der die Beschriftung eines Wertes geschrieben ist, als HTML-Wert für `lang`, oder `''` für die Sprache der Seite. Der Sprach-Provider gibt für `ger-DE` `de-DE` zurück. |
| `remembersPreference()` | `RememberPreference=enabled` in der Gruppe. |
| `currentValue()` | `$this->current`, wenn `apply()` es gesetzt hat, sonst `defaultValue()`. |
| `cacheVariantKey( $value )` | `''` für den Standard oder einen ungültigen Wert, sonst `<identifier>-<value>`, wobei andere Zeichen durch `_` ersetzt werden (zum Beispiel `language-ger-DE`). |
| `reset()` | Setzt `$this->current` auf null zurück. |

## Die Registry: expUISwitch

### Provider

`providers()` baut die Provider dieser Anfrage aus `uiswitch.ini`: Für jede wohlgeformte Kennung von
`[UISwitchSettings] ProviderList[]` mit einer Gruppe `[UISwitch_<identifier>]` nimmt es `Class=` und überspringt mit
einer Debug-Warnung eine Klasse, die nicht existiert (`class_exists`, sodass ein Velocity-Worker, der vor dem
Bereitstellen der Klasse gestartet wurde, nie daran scheitert) oder `expUISwitchProviderInterface` nicht
implementiert. Eine Gruppe mit `Enabled=false` (oder `disabled`, `0`) wird stillschweigend übersprungen. Mit
`[UISwitchSettings] Enabled=false` gibt es keine Provider. `provider( $identifier )` gibt einen zurück oder null.

### Lebenszyklus pro Anfrage

`ezpKernelWeb::requestInit()` ruft die Registry direkt nach dem Start der Sitzung und der Datenbankprüfung auf, vor
der Login-Vorprüfung, dem Ereignis `request/input`, `eZLocale::instance()`, der Seitensprache und jedem Template:

```php
if ( class_exists( 'expUISwitch' ) )
{
    expUISwitch::beginRequest();
    if ( $this->siteBasics['session-required'] && empty( $this->siteBasics['validity-check-required'] ) )
        expUISwitch::applyRequest();
}
```

- `beginRequest()` ruft `reset()` für jeden Provider auf, der früher in diesem Prozess einen Wert angewendet hat,
  vergisst die Provider und die angewendeten Werte und setzt `$GLOBALS['expUISwitchVariantKey'] = ''`.
- `applyRequest()` startet nie eine Sitzung und wirft nie eine Ausnahme. Es wendet nichts an, außer das Framework ist
  eingeschaltet, der aktuelle Siteaccess ist ein Siteaccess der Umschaltung (`isSwitchSiteAccess()`), die Sitzung ist
  gestartet und der aktuelle Benutzer ist angemeldet. Es liest die gespeicherten Werte dieses Benutzers und
  Siteaccess und übergibt sie an `applyValues()`. Ein gespeicherter Wert, den `applyValues()` nicht angewendet hat,
  wird aus der Sitzung entfernt.
- `applyValues( $stored, $user )` prüft jeden Wert erneut, bei jeder Anfrage: Der Benutzer muss jetzt
  `uiswitch/<identifier>` besitzen (`canUse()`), der Wert muss `isValid()` bestehen und darf nicht der Standard sein.
  Ein Provider mit `RememberPreference=enabled` und ohne Eintrag in der Sitzung nimmt den Wert aus den Einstellungen
  des Benutzers, unter denselben Prüfungen. Jeder Wert, der besteht, geht an `apply()`; am Ende wird der
  Varianten-Schlüssel des Caches in `$GLOBALS['expUISwitchVariantKey']` gespeichert.
- `appliedValues()` gibt zurück, was in dieser Anfrage angewendet wurde.

Der Setup-Assistent (Gültigkeitsprüfung) und Anfragen ohne Sitzung wenden nichts an.

### Format in der Sitzung

Die Sitzungsvariable `expUISwitch` (`expUISwitch::SESSION_VARIABLE`) enthält:

```php
array(
    'user'   => 14,                                  // the user the values belong to
    'values' => array(
        'admin'  => array( 'language' => 'ger-DE' ),  // siteaccess => provider => value
        'editor' => array( 'language' => 'eng-US' ),
    ),
)
```

Werte eines anderen Benutzers werden ignoriert und beim nächsten Schreiben entfernt, sodass eine Sitzung, die den
Benutzer wechselt, nie eine Wahl mitnimmt. Gelesen werden nur wohlgeformte Kennungen und Werte
(`IDENTIFIER_PATTERN`, `VALUE_PATTERN`), und gespeichert werden immer nur Werte, die ein Provider akzeptiert hat. Bleibt
nichts übrig, wird die Variable entfernt. Gemerkte Werte sind die Benutzereinstellungen `exp_uiswitch_<identifier>`
(`PREFERENCE_PREFIX`).

### Einen Wert setzen

`set( $identifier, $value )` gibt `ok`, `unavailable` (Framework aus, kein Siteaccess der Umschaltung, kein solcher
Provider), `denied` (keine Richtlinie) oder `invalid` (nicht in der Liste erlaubter Werte) zurück. Bei `ok` speichert
es den Wert für den aktuellen Benutzer und Siteaccess (der Standardwert löscht ihn), aktualisiert die
Benutzereinstellung, wenn der Provider sie sich merkt, und verzeichnet das Audit-Ereignis `access.uiswitch.change` mit
Wert und Siteaccess vorher und nachher, dem Benutzer als Objekt und dem Provider als Ziel.

### Weitere Hilfsmethoden

| Methode | Was sie tut |
|---|---|
| `isEnabled()` | `[UISwitchSettings] Enabled` (Standard true). |
| `isSwitchSiteAccess( $siteaccess = null )` | In `SiteAccessList[]`, wenn diese nicht leer ist, sonst ob die Design-Liste des Siteaccess ein Design enthält, dessen Name mit `admin` beginnt. |
| `canUse( $identifier, $user = null )` | Angemeldet und im Besitz von `uiswitch/<identifier>`. |
| `providerIdentifiers()`, `policyFunctions()` | Die Kennungen aus `ProviderList[]`; die Richtlinienfunktionen (immer einschließlich `language`). |
| `isSameSiteRequest( $secFetchSite )` | Nur falsch für ein `Sec-Fetch-Site`, das nicht leer, `same-origin` oder `same-site` ist. |
| `safeRedirect( $uri, $fallback )` | Ein lokaler Pfad (`/x/y`), oder der Ersatzwert bei `//host`, einem Schema, einem Backslash, Segmenten `.` oder `..`, Steuerzeichen und mehr als 2048 Zeichen. |
| `toolbar()`, `fetchToolbar()` | Die Daten der Werkzeugleiste (siehe unten). |
| `setSettings( $ini )` | Verwendet andere Einstellungen als `uiswitch.ini` (Tests). |

## Der Varianten-Schlüssel des Caches

`expUISwitch::cacheVariantKey()` ist `''`, wenn nichts angewendet ist, sonst die Schlüssel der angewendeten Provider,
nach Kennung sortiert und mit `.` verbunden (`language-ger-DE`, oder `design-admin3.language-ger-DE` bei zwei
Providern). Er wird in `$GLOBALS['expUISwitchVariantKey']` gehalten, sodass erzeugter Cache-Code ihn lesen kann, ohne
die Klasse zu laden. Er wird an vier Stellen verwendet:

| Wo | Was er bewirkt |
|---|---|
| `{cache-block}` (`lib/eztemplate/classes/eztemplatecachefunction.php`) | An den Siteaccess-Teil des Cache-Schlüssels angehängt, in kompilierten und in verarbeiteten Templates, zur Laufzeit gelesen: Das gecachte Markup eines Benutzers mit einer Wahl wird getrennt gespeichert. |
| Der Cache der Inhaltsansichten (`kernel/classes/eznodeviewfunctions.php`) | Dem Cache-Hash als Schlüssel `uiswitch` hinzugefügt. |
| Kompilierte Templates (`eZTemplateCompiler::compilationFilename()`) | An den Schlüssel der kompilierten Datei angehängt, sodass ein Provider, der ändert, worauf ein Template aufgelöst wird (ein Design), eigene kompilierte Dateien erhält. Die Sprache verändert die kompilierten Namen bereits über die Locale. |
| Der rollenabhängige HTTP-Seiten-Cache (`kernel/private/classes/httpcache/ezphttpcachelistener.php`) | Eine mit einem Schlüssel gerenderte Seite wird nicht gespeichert; als Grund wird `ui switch` angegeben. |

## Das Modul uiswitch

| View | Methode | Parameter | Antwort |
|---|---|---|---|
| `uiswitch/set` | nur POST | `Provider`, `Value`, `RedirectURI` | Eine Weiterleitung auf `RedirectURI`, wenn es ein lokaler Pfad ist, sonst auf `/content/dashboard`. Ablehnungen rendern `design:uiswitch/refused.tpl` mit dem Status 405 (`method`, mit `Allow: POST`), 403 (`cross-site`, `denied`), 400 (`invalid`) oder 404 (`unavailable`). Der Kernel lehnt einen POST ohne Formular-Token (403) ab, bevor die View läuft. |
| `uiswitch/state` | GET | keine | JSON `{ "siteaccess": ..., "providers": [ ...toolbar() ] }` mit `Cache-Control: private, no-store`. |

Die Richtlinienfunktionen werden in `module.php` aus `expUISwitch::policyFunctions()` gebaut, eine pro Kennung aus
`ProviderList[]` (und immer `language`), ohne Einschränkungen. Der Provider einer Erweiterung erhält seine Funktion
`uiswitch/<identifier>` daher ohne Änderung am Kernel. Die Views verlangen eine beliebige davon
(`language or <id2> ...`); `set` prüft den einzelnen Provider erneut. `Set::decide()` und `Set::status()` sind rein
und werden für sich getestet.

## fetch( 'uiswitch', 'toolbar' )

Templates nennen die Klasse nie. Der Fetch gibt eine Liste zurück, die leer ist, wenn das Framework ausgeschaltet ist,
der Siteaccess kein Siteaccess der Umschaltung ist oder der Benutzer keinen Provider verwenden darf. Ein Provider mit
weniger als zwei erlaubten Werten wird weggelassen.

```
array(
  array(
    'identifier'    => 'language',
    'title'         => 'Interface language',
    'current'       => 'eng-GB',
    'current_label' => 'English (United Kingdom)',
    'default'       => 'eng-GB',
    'values'        => array(
      array( 'value' => 'eng-GB', 'label' => 'English (United Kingdom)', 'current' => true,  'default' => true,  'lang' => 'en-GB' ),
      array( 'value' => 'ger-DE', 'label' => 'Deutsch (Deutschland)',    'current' => false, 'default' => false, 'lang' => 'de-DE' ),
    ),
  ),
)
```

`design:uiswitch/choices.tpl` rendert sie (Parameter `choices` und `prefix`, ein auf der Seite eindeutiges Präfix für
IDs); das Werkzeug der Werkzeugleiste, der Block von admin4l und die Seitenleiste von AdminUI binden es alle ein.

## Beispiel: ein Provider in einer Erweiterung

Eine Erweiterung `myext`, mit der Benutzer eine Dichte der Listen in der Administration wählen können.

**1. Die Klasse**, `extension/myext/classes/myextuiswitchdensityprovider.php`:

```php
<?php

class myextUISwitchDensityProvider extends expUISwitchProvider
{
    /** the siteaccess's own value, read before any apply() */
    public function defaultValue()
    {
        return (string)eZINI::instance( 'myext.ini' )->variable( 'ListSettings', 'Density' );
    }

    public function label( $value )
    {
        return ezpI18n::tr( 'extension/myext', ucfirst( $value ) );
    }

    public function apply( $value )
    {
        if ( !$this->isValid( $value ) || $value === $this->defaultValue() )
            return false;
        eZINI::instance( 'myext.ini' )->setVariable( 'ListSettings', 'Density', $value );
        myextListRenderer::resetStatics();   // anything built from the old value
        $this->current = $value;
        return true;
    }

    public function reset()
    {
        myextListRenderer::resetStatics();
        parent::reset();
    }
}
```

**2. Die Einstellungen**, `extension/myext/settings/uiswitch.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[UISwitchSettings]
ProviderList[]=density

[UISwitch_density]
Class=myextUISwitchDensityProvider
Enabled=true
Title=List density
AllowedValues[]
AllowedValues[]=compact
AllowedValues[]=comfortable
RememberPreference=disabled
*/ ?>
```

**3. Stellen Sie die Klasse bereit, bevor die Einstellungen sie nennen.** Erzeugen Sie zuerst die Autoloads neu und
starten Sie die Worker neu, dann aktivieren Sie die Einstellungen:

```bash
./console exp:velocity deploy --allow-root-user            # extension autoloads, caches, PHP-FPM, Velocity
./console exp:velocity deploy --kernel --allow-root-user   # when the class lives in kernel/ or lib/
```

Ein Worker, der die Klasse nicht kennt, überspringt den Provider mit einer Warnung, statt zu scheitern, zeigt aber
keine Auswahl an, bis er neu gestartet wird. Aktivieren Sie dann die Einstellungen der Erweiterung und stellen Sie
noch einmal bereit: Der INI-Cache wird geleert (`--clear-tag=ini`) und die Worker starten neu, sodass die
Richtlinienfunktionen des Moduls `density` enthalten.

**4. Vergeben Sie die Richtlinie** `uiswitch/density` an die Rollen, die sie verwenden dürfen (*Rollen und
Richtlinien*, Modul `uiswitch`, Funktion `density`). Ab dann zeigt die Werkzeugleiste die Auswahl; eine Änderung an
Templates ist nicht nötig.

Werte in `AllowedValues[]` müssen `VALUE_PATTERN` entsprechen. Nennt ein Wert eine Datei oder ein Design, prüfen Sie in
`isAvailable()`, dass es existiert, und bauen Sie nie einen Pfad aus einem Wert, bevor `isValid()` ihn akzeptiert hat.

## Exponential Velocity

Velocity-Worker sind persistent: Statische und globale Daten überdauern von einer Anfrage zur nächsten.

- Alles, was `apply()` in statischen oder globalen Daten ändert, muss in `reset()` rückgängig gemacht werden.
  `beginRequest()` ruft `reset()` für jeden Provider auf, der in diesem Prozess einen Wert angewendet hat, bevor in der
  nächsten Anfrage irgendetwas anderes geschieht.
- `apply()` ändert Einstellungen nur im Speicher, auf den `eZINI`-Instanzen dieser Anfrage; schreiben Sie nie eine
  Einstellungsdatei.
- Setzen Sie nach dem Ändern einer Einstellung zurück, was daraus gebaut wurde. Der Sprach-Provider ruft
  `eZTranslatorManager::resetTranslations()` (der Übersetzer, die Standard-`eZLocale`, die globalen Daten des
  Übersetzungs-Caches) sowohl in `apply()` als auch in `reset()` auf.
- Sichern Sie jeden Verweis auf eine neue Klasse mit `class_exists()` ab, wo ein laufender Worker sie vielleicht noch
  nicht hat, wie es der Kernel in `ezpKernelWeb::requestInit()` und `module.php` tut.

## Abwärtskompatibilität

- **Keine Änderung von Cache-Namen ohne Wahl.** Ist keine Umschaltung angewendet, ist der Varianten-Schlüssel `''`, und
  der Cache der Inhaltsansichten, die Schlüssel von `{cache-block}`, die Namen kompilierter Templates und der
  HTTP-Seiten-Cache verhalten sich genau wie bisher. Installationen, in denen niemand die Umschaltung verwendet,
  behalten ihre Caches.
- **Inhaltssprachen bleiben unberührt.** Nur `[RegionalSettings] Locale` (und `TextTranslation=enabled`) ändern sich,
  im Speicher, für den Benutzer, der gewählt hat.
- **Neues Modul und neue Richtlinie.** `settings/module.ini` führt `uiswitch` auf. Andere Rollen als Administrator
  brauchen die Richtlinie; neue Installationen geben `uiswitch/language` an Editor (`kernel/sql/*/cleandata.sql`,
  Richtlinie 332, und die Multi-Site-Installationsprogramme).
- **Der Eintrag in der Werkzeugleiste ist in bestehenden Installationen optional.** Er erscheint erst, wenn
  `admin_ui_switch` zu `toolbar.ini` hinzugefügt wird (oder der Block zum Admin-Layout); siehe
  [die Anleitung zu den Einstellungen](../../anleitungen/ui-umschalter-einstellungen.md#4-die-werkzeugleiste-in-einer-bestehenden-installation).
- **bcswitchadminlanguage wird ersetzt.** Die Erweiterung `bcswitchadminlanguage` (Modul `switchadminlanguage`, ein
  Kernel-Override von `ezpkernelweb.php`) tat dasselbe über einen gemeinsamen Admin-Siteaccess. Installieren Sie sie
  nicht neben der UI-Umschaltung: Ihr Kernel-Override würde den Hook in `ezpKernelWeb::requestInit()` ersetzen.
  Entfernen Sie sie, und vergeben Sie `uiswitch/language` dort, wo ihre Richtlinie vergeben war.

## Tests

`tests/tests/kernel/classes/uiswitch/expUISwitchTest.php` (ohne Datenbank, 18 Tests): die ausgelieferten
Einstellungen, die Registry (fehlende Klassen, Nicht-Provider, ausgeschaltete und fehlerhafte Einträge),
Richtlinienfunktionen und die Funktionsliste des Moduls, die strikte Liste erlaubter Werte, die Prüfungen von Locale
und Übersetzung des Sprach-Providers und sein Anwenden im Speicher, die Prüfung der Richtlinie pro Provider, die
Siteaccesses der Umschaltung, das Format in der Sitzung, die erneuten Prüfungen beim Anwenden, das Zurücksetzen bei der
nächsten Anfrage, Varianten-Schlüssel und die Namen kompilierter Templates, lokale Weiterleitungen und die Ablehnungen
von `uiswitch/set`.
