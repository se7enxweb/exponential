# Sichere Weiterleitungen und „zurück dorthin, woher Sie kamen“

Diese Seite ist für Entwickler, die einen Besucher nach einem Formular irgendwohin schicken, und für Website-Betreiber,
die sich fragen, wohin Abbrechen führt. Seit dem 6. Oktober 2026 geht jedes Bearbeitungsformular mit einer Schaltfläche
Abbrechen oder Verwerfen zu der Seite zurück, von der aus es geöffnet wurde, und jedes Weiterleitungsziel, das von
außerhalb des Codes kommt (ein Formularfeld, die Sitzung, ein URL-Parameter), durchläuft eine Menge von Regeln, gehalten
in `eZRedirectManager` (`kernel/classes/ezredirectmanager.php`).

## Wohin Abbrechen führt

| Ansicht | Schaltfläche | Führt zu, in dieser Reihenfolge |
|---|---|---|
| `user/edit` | `CancelButton` | die Seite des Formulars, die zuletzt angesehene Seite, `content/view/sitemap/5` |
| `user/password` | `CancelButton` (und Weiter nach einer Änderung) | die Seite des Formulars (`RedirectIfDiscarded`, sonst das ältere `RedirectOnCancel`, sonst `RedirectURI`), die zuletzt angesehene Seite, `site.ini [SiteSettings] DefaultPage` |
| `user/setting` | `CancelSettingButton` | die Seite des Formulars, die zuletzt angesehene Seite, die eigene Seite des Benutzers |
| `section/edit` | `CancelButton` | die Seite des Formulars, die zuletzt angesehene Seite, `section/list` |
| `state/edit` | `CancelButton` | die Seite des Formulars, die zuletzt angesehene Seite, die Zustandsgruppe |
| `state/group_edit` | `CancelButton` | die Seite des Formulars, die zuletzt angesehene Seite, `state/groups` |
| `role/edit` | `Discard` (nachdem der Entwurf entfernt ist) | die Seite des Formulars, die zuletzt angesehene Seite, `role/list` |
| `class/edit` | `DiscardButton` (nachdem der Entwurf entfernt ist) | die Seite des Formulars, die zuletzt angesehene Seite, die Klassengruppe, von der die Bearbeitung ausging, `class/grouplist` |
| `class/groupedit` | `DiscardButton` | die Seite des Formulars, die zuletzt angesehene Seite, `class/grouplist` |
| `user/register` | `CancelButton` | die Seite des Formulars, die zuletzt angesehene Seite, `/` |
| `content/edit` | Entwurf verwerfen | die Seite des Formulars (in der Sitzung gehalten), sonst wie bisher |

Die letzte Spalte endet immer mit dem Ort, an den Abbrechen vorher führte, sodass sich nichts ändert, wo nichts bekannt
ist.

**Die Seite des Formulars** ist das verborgene Feld `RedirectIfDiscarded`. Jede dieser Ansichten setzt die
Template-Variable `redirect_if_discarded`, und ihre Templates (standard, admin, admin4 sowie die Designs Admin UI und
media) senden sie:

```
{if and( is_set( $redirect_if_discarded ), $redirect_if_discarded )}<input type="hidden" name="RedirectIfDiscarded" value="{$redirect_if_discarded|wash}" />{/if}
```

Der Wert ist die Seite, die vor dem Öffnen des Formulars angesehen wurde, ins Formular eingefroren, sodass Abbrechen auch
dann dorthin zurückführt, wenn zwischendurch in einem anderen Tab andere Seiten geöffnet wurden. Ein eigenes Template, das
eines dieser Formulare ausgibt, kann dieselbe Zeile hinzufügen; ohne sie verwendet Abbrechen die zuletzt angesehene Seite,
was meist dieselbe ist.

**Die zuletzt angesehene Seite** ist `LastAccessesURI` der Sitzung, die der Kernel nach jeder Seite erfasst, die keine
Bearbeitungs-, Administrations-, Auswahl-, Authentifizierungs- oder Ajax-Ansicht ist.

## Die Regeln

Eine Seite wird nur genommen, wenn alle diese gelten; sonst wird die nächste der Liste versucht.

1. Sie ist ein Pfad dieser Website (`/content/view/full/2`, `content/view/full/2`, `/Company/About`, auch mit dem
   Siteaccess-Präfix, einer Abfrage und einem Fragment) oder eine `http`/`https`-URL, deren Host der aktuelle ist, einer
   aus `site.ini [SiteSettings] AllowedRedirectHosts` oder ein Host aus `[SiteAccessSettings] HostMatchMapItems` und
   `HostUriMatchMapItems`.
2. Sie enthält kein Steuerzeichen (CR und LF teilen Header; Browser verwerfen Tab, CR und LF innerhalb einer URL, sodass
   `/<TAB>/evil.example` zu `//evil.example` wird).
3. Sie enthält vor der Abfrage keinen Backslash: Browser lesen `\` als `/`, sodass `/\evil.example` und `\\evil.example`
   ein anderer Host sind.
4. Prozent-dekodiert (bis zu dreimal) beginnt sie nicht mit `//`, einem Schema, einem Backslash oder einem Steuerzeichen
   (`%2F%2Fevil.example`, `/%5Cevil.example`, `javascript%3A...`).
5. Sie hat kein anderes Schema als `http` und `https` (`javascript:`, `data:`, `vbscript:` werden abgelehnt) und keinen
   Benutzernamen oder kein Passwort (`https://own.example@evil.example` nennt evil.example).
6. Sie ist nicht die laufende Ansicht selbst (Abbrechen landete wieder auf dem Formular) und keine aus
   `site.ini [SiteSettings] DisallowedReturnViews`: Ansichten, die auf ein POST hin handeln, die Sitzung beenden oder einen
   Download starten (`content/action`, `user/logout`, `content/download`, ...).
7. Nur für die zuletzt angesehene Seite: Der aktuelle Benutzer darf sie noch ansehen (ein Knoten, den er lesen kann, eine
   Modulansicht, die seine Rollen erlauben). Eine solche Seite war ansehbar, als sie erfasst wurde, aber eine Rolle kann
   sich seitdem geändert haben.

Ein Pfad mit dem Siteaccess-Präfix der aktuellen Anfrage (`/admin/...` bei Zuordnung über die URI) wird weitergeleitet, wie
er ist, sodass er in diesem Siteaccess bleibt; ein Pfad mit dem Präfix eines anderen Siteaccess wird hinter das aktuelle
Präfix gesetzt, wie jede Weiterleitung eines Moduls, und bleibt ebenfalls im aktuellen Siteaccess.

## Jede Weiterleitung

`eZModule::redirectTo()`, die Weiterleitung hinter jeder Modulansicht, wendet die Regeln 1 bis 5 auf jedes Ziel an. Ein
Ziel, das sie verletzt, wird mit **403** und „Redirection requested on non-authorized host“ (Regeln 1 und 5, wie bisher)
oder „Redirection requested to an unsafe address“ (die übrigen) beantwortet, und der Grund wird ins Fehlerprotokoll
geschrieben. Ein Weiterleitungsparameter, der sie ungeprüft erreicht (`RedirectURI` von `user/login`, `content/action`,
`user/preferences`), kann einen Besucher daher nicht mehr über einen Backslash, einen kodierten Schrägstrich oder eine
`javascript:`-URL auf einen anderen Host schicken.

`eZRedirectManager::redirectURI()` und `redirectTo()` wenden die Regeln auf ihre bevorzugte URI und auf die Seite aus der
Sitzung an; der Standard, den der Aufrufer übergibt, ist der eigene des Aufrufers und wird nicht geprüft.

## Für Entwickler

```php
// the target, or false when it breaks a rule
$uri = eZRedirectManager::safeURI( $http->postVariable( 'RedirectURI' ) );

// why: false, 'type', 'empty', 'control', 'backslash', 'encoded', 'scheme', 'userinfo' or 'host'
$reason = eZRedirectManager::unsafeReason( $uri );

// "back to where you came from": the first safe page the form names, else the page viewed last, else the default
$module->redirectTo( eZRedirectManager::returnURI( $module, '/section/list', eZRedirectManager::formReturnURIs() ) );

// the value for the hidden field RedirectIfDiscarded of the form's template
$tpl->setVariable( 'redirect_if_discarded', eZRedirectManager::formReturnURI( $module ) );
```

`returnURI()` nimmt eine Liste von Seiten, und seine Optionen (`session`, `viewable`, `disallowed_views`,
`allowed_hosts`, `current_host`, `prefix`) lassen einen Test es ohne Sitzung, Datenbank oder Einstellungen ausführen.
Jede Ansicht oben hat ein statisches `cancelURI()`, das ihr Ziel zurückgibt, sodass ein Test es auf dieselbe Weise prüfen
kann.

## Tests

Es wird keine Datenbank gebraucht:

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/eZRedirectManagerSafeURITest.php
php vendor/bin/phpunit tests/tests/kernel/classes/eZUserEditCancelTest.php
```

`eZRedirectManagerSafeURITest` deckt die Regeln Fall für Fall ab (protokollrelativ, Backslash, Schema, kodiert, CR/LF,
Hosts, Benutzerinformationen), die Modulansicht eines Pfads, `returnURI()` mit und ohne Sitzung und das Ziel von
Abbrechen jeder Ansicht der Tabelle, mit einer sicheren und mit einer unsicheren Seite im Formular.

## Aktualisieren

- Ein eigenes Template für eine der Ansichten oben funktioniert weiter; es sendet kein `RedirectIfDiscarded`, sodass
  Abbrechen zur zuletzt angesehenen Seite führt (vorher: eine feste Seite). Fügen Sie die Zeile oben hinzu, um die Seite
  ins Formular einzufrieren.
- Code, der mit einer `javascript:`- oder `data:`-URL, einem Backslash oder einem Steuerzeichen weiterleitete, erhält
  jetzt eine 403.
- `eZModule::getAllowedRedirectHosts()` (privat) ist jetzt `eZModule::allowedRedirectHosts()` (öffentlich, statisch).
- Velocity: Die geänderten Klassen sind Kernel-Klassen, also `exp:velocity deploy --kernel` (oder ein Neustart nach dem
  Neubau des Archivs); leeren Sie dann den Cache der Template-Überschreibungen für die neuen verborgenen Felder.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
