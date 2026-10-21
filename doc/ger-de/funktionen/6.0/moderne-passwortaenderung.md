# Das eigene Passwort ändern

`user/password` ist die Seite, auf der ein angemeldeter Benutzer sein eigenes Passwort ändert, in der
Administrationsoberfläche (`/admin/user/password`) und auf der öffentlichen Website (`/user/password`). Seit 6.0.15
listet die Seite die Anforderungen an das Passwort neben dem Feld auf, meldet jedes Problem neben dem Feld, zu dem es
gehört, und sagt deutlich, wenn die Änderung durchgeführt wurde. Nach einer Änderung erhält der Benutzer eine E-Mail,
die das bestätigt. Eine Website kann eigene Passwortregeln hinzufügen.

## Kurz gefasst

- Das Formular hat weiterhin drei Felder: aktuelles Passwort, neues Passwort und das neue Passwort noch einmal. Ihre
  Namen, die Buttons und die Weiterleitungen sind dieselben wie bisher, sodass Formulare in anderen Designs und
  Extensions weiter funktionieren.
- Die Anforderungen werden unter dem neuen Passwort aufgeführt. Der Server prüft sie in jedem Fall. Mit JavaScript
  hakt die Seite sie außerdem schon während der Eingabe ab, zeigt eine Stärkeanzeige, meldet, ob die beiden neuen
  Passwörter übereinstimmen, lässt Sie jedes Feld ein- oder ausblenden und kann ein starkes Passwort erzeugen.
- Eine fehlgeschlagene Änderung nennt jedes Problem neben seinem Feld und in einer Zusammenfassung oben. Jeder Eintrag
  der Zusammenfassung verlinkt auf sein Feld.
- Die Seite schreibt ein eingegebenes Passwort nie in das HTML zurück. Vor 6.0.15 setzte sie die eingegebenen
  Passwörter in die `value`-Attribute der Felder.
- Nach einer Änderung bleiben Sie angemeldet, und Ihre Sitzung erhält eine neue ID. Ihre anderen Sitzungen (andere
  Browser, andere Geräte) werden bei ihrer nächsten Anfrage abgemeldet, unabhängig vom Session-Handler der Website.
  Das geschieht auch, wenn das Passwort anderswo geändert wird: durch einen Administrator, in content/edit, über
  „Passwort vergessen?“ oder auf der Konsole.
- Für jedes Formular, das ein Passwort setzt, gelten dieselben Regeln: die Registrierung, das Bearbeiten eines
  Benutzerkontos in content/edit oder in der Administrationsoberfläche und der Konsolenbefehl
  `bin/php/resetuserpassword.php`.
- Eine E-Mail „Ihr Passwort wurde geändert“ geht an die Adresse des Kontos. Sie enthält nie das Passwort. Sie gehört
  zur grundlegenden Kategorie **Kontosicherheit**, sodass niemand sie abschalten kann.
- Jede Änderung und jede abgewiesene Änderung wird im Prüfprotokoll festgehalten. Das Passwort wird nie festgehalten.

## Die Regeln

Die Längenregel hat sich nicht geändert: `site.ini [UserSettings] MinPasswordLength` (10 bei einer neuen Installation;
3, wenn die Einstellung fehlt). Die Länge wird wie bisher in Byte gezählt, sodass kein Passwort, das bisher
akzeptiert wurde, jetzt abgewiesen wird.

Die übrigen Regeln stehen in `site.ini [PasswordSettings]`. Sie sind standardmäßig alle **aus**, sodass eine
Aktualisierung die Regeln nicht verschärft:

| Einstellung | Regel-ID | Das neue Passwort |
|---|---|---|
| `RequireLowercase=enabled` | `lowercase` | enthält einen Kleinbuchstaben, in einer beliebigen Schrift |
| `RequireUppercase=enabled` | `uppercase` | enthält einen Großbuchstaben |
| `RequireDigit=enabled` | `digit` | enthält eine Ziffer |
| `RequireSymbol=enabled` | `symbol` | enthält ein Zeichen, das weder Buchstabe noch Ziffer ist (ein Leerzeichen zählt) |
| `MinCharacterClasses=N` | `classes` | mischt mindestens N dieser vier Arten (0 bis 4; 0 = aus) |
| `ForbidLogin=enabled` | `not_login` | enthält den Anmeldenamen nicht (Groß- und Kleinschreibung wird ignoriert; nur Anmeldenamen ab 3 Zeichen) |
| `ForbidCurrentPassword=enabled` | `not_current` | unterscheidet sich vom aktuellen Passwort |

Seit 6.0.15 ist eine weitere Regel standardmäßig **an**, in `site.ini [UserSettings]`:

| Einstellung | Regel-ID | Das neue Passwort |
|---|---|---|
| `WeakPasswordCheck=enabled` | `not_common` | ist kein bekanntes oder verbreitetes Passwort und auch keine einfache Abwandlung davon (`expWeakPasswordList`; weitere Wörter mit `WeakPasswordList[]` und `WeakPasswordFile`) |

Sie gilt nur für neue Passwörter; bei der Anmeldung wird niemand abgewiesen. `WeakPasswordCheck=disabled` stellt die
bisherigen Regeln wieder her. Einzelheiten:
[doc/eng-us/bc/6.0/weak-passwords-and-reset-key-lifetime.md](../../../eng-us/bc/6.0/weak-passwords-and-reset-key-lifetime.md).

Um eine Regel einzuschalten, verwenden Sie eine Überschreibung, zum Beispiel `settings/override/site.ini.append.php`:

```ini
[PasswordSettings]
MinCharacterClasses=3
ForbidLogin=enabled
```

Die Regeln leben in einer Klasse, `expPasswordPolicy` (`kernel/classes/exppasswordpolicy.php`). Ihre Methode `rules()`
gibt die Liste zurück, `validate( $password, $user )` die IDs der Regeln, an denen ein Passwort scheitert, und
`errorText( $id )` den Fehlersatz. `accountErrors( $password, $user, $login )` gibt die Sätze für ein
Benutzerkontoformular zurück; der Datentyp für Benutzerkonten (Registrierung, content/edit, die Benutzerbearbeitung der Administrationsoberfläche) und `bin/php/resetuserpassword.php` verwenden sie, sodass jedes Formular dieselben Passwörter abweist. Der Längensatz ist dort der bisherige; ein unverändertes Passwort im Bearbeitungsformular (`_ezpassword`) wird nicht geprüft.

## Nach einer Änderung

| Einstellung (`[PasswordSettings]`) | Standard | Was sie bewirkt |
|---|---|---|
| `EndOtherSessions` | enabled | gibt der aktuellen Sitzung eine neue ID und beendet die anderen Sitzungen des Benutzers (schaltet auch den unten beschriebenen Passwortstempel ein und aus) |
| `ChangeNotificationMail` | enabled | sendet „Ihr Passwort wurde geändert“ an die Adresse des Kontos |
| `StrengthMeter` | enabled | weist das Skript der Seite an, die Stärkeanzeige zu zeigen |
| `GenerateButton` | enabled | weist das Skript der Seite an, „Ein starkes Passwort erzeugen“ anzubieten ([UserSettings] `GeneratePasswordLength` Zeichen, mindestens 12) |

### Andere Sitzungen: der Passwortstempel

Bei der Anmeldung speichert die Sitzung einen **Passwortstempel** in `eZUserPasswordStamp`, neben `eZUserLoggedInID`.
Der Stempel ist die Benutzer-ID plus ein Hash mit Schlüssel (HMAC-SHA256, mit einem aus dem Website-Geheimnis
abgeleiteten Schlüssel) über die Benutzer-ID und den gespeicherten Passwort-Hash. Er enthält nie das Passwort oder
dessen Hash und ändert sich bei jeder Änderung des Passworts.

Bei jeder Anfrage vergleicht `eZUser::currentUser()` den Stempel der Sitzung mit dem Stempel des aktuellen Passworts
des Benutzers. Das kostet keine zusätzliche Datenbankabfrage: Der Hash stammt aus dem Benutzer-Cache, den
`currentUser()` ohnehin liest, und der Vergleich läuft in konstanter Zeit (`hash_equals`). Weichen die Stempel
voneinander ab, wird die Sitzung abgemeldet (`eZUser::logoutCurrent()`), und `access.session.revoke` wird mit dem
Grund `password_changed` festgehalten.

- **Die Sitzung, die die Änderung vorgenommen hat, bleibt angemeldet.** `eZUser::store()` gibt der Sitzung des
  gespeicherten Benutzers den neuen Stempel und aktualisiert das Benutzerobjekt, das die Anfrage vorhält.
- **Sitzungen von vor der Aktualisierung bleiben gültig.** Eine Sitzung ohne Stempel wird nicht geprüft; sie erhält
  bei ihrer nächsten Anmeldung einen Stempel. Ein Deployment meldet niemanden ab.
- **Nicht geprüft werden:** der anonyme Benutzer; Konten ohne eigenes Passwort (ein leerer Hash, wie bei LDAP-,
  Textdatei- und Single-Sign-on-Benutzern); ein vorübergehender Wechsel zu einem anderen Benutzer
  (`eZUser::NO_SESSION_REGENERATE`, verwendet von den Vorschau-Caches); und jede Sitzung bei
  `EndOtherSessions=disabled`.
- Der Benutzer-Cache ist eine PHP-Datei, die mit `include` gelesen wird, sodass der OPcache eines Servers eine veraltete
  Kopie behalten kann, bis er neu validiert. Passt ein Stempel nicht, liest die Prüfung einmal die gespeicherte Zeile,
  was eine Abfrage kostet, und zwar nur in diesem Fall; so meldet ein veralteter Cache nie die Sitzung ab, die sich
  mit dem neuen Passwort angemeldet hat. `eZUser::purgeUserCacheByUserId()` invalidiert die Datei außerdem im OPcache.
  Eine über die Website vorgenommene Änderung erreicht daher sofort jeden Worker dieses Servers. Eine auf der Konsole
  vorgenommene Änderung (`resetuserpassword.php`, ein Skript) erreicht die anderen Sitzungen eines Webservers, sobald
  dessen OPcache neu validiert (`opcache.revalidate_freq`; 10 Sekunden beim Apache des Demonstrationsservers, sofort
  bei Velocity).
- Wenn `[UserSettings] UpdateHash` ein Passwort bei der Anmeldung neu hasht (nach einer Änderung von `HashType`),
  werden die anderen Sitzungen des Benutzers einmal abgemeldet, weil sich der gespeicherte Hash geändert hat.

Mit dem Datenbank-Session-Handler (`[Session] Handler=ezpSessionHandlerDB`) werden die anderen Sitzungen außerdem
sofort zum Zeitpunkt der Änderung gelöscht (`ezpSessionHandler::deleteOtherSessionsOfUser()`). Ein Handler in einer
Extension kann das unterstützen, indem er diese Methode überschreibt.

### Die Mail

Die Mail verwendet das Template `design:user/password_changed_mail.tpl`, das ein Design überschreiben kann. Seine
Variablen sind `$user`, `$changed_at` (ein Zeitstempel), `$ip`, `$sessions_ended`, `$other_sessions_signed_out` und
`$site_name`, und das Template kann `$subject` setzen.
Die Mail läuft über das Mail-Gate in der Kategorie `security`. Ein Benutzer ohne gültige Adresse erhält keine Mail.

## Das Prüfprotokoll

| Ereignis | Wann |
|---|---|
| `access.user.password.change` | ein Passwort wird geändert (unverändert; festgehalten von `eZUser::store()`) |
| `access.user.password.change.failed` | eine Änderung wird abgewiesen; `after.rule` ist `old_password`, `confirmation` oder die ID der ersten Regel, die nicht erfüllt wurde |
| `access.session.revoke` (neu) | die anderen Sitzungen des Benutzers wurden beendet: mit dem Datenbank-Handler sofort gelöscht (Grund `password_change`, `after.count` die Anzahl) oder eine Sitzung bei ihrer nächsten Anfrage durch den Passwortstempel abgemeldet (Grund `password_changed`) |

## Für Template-Autoren

Die Seite erhält neue Template-Variablen, alle optional: `$password_changed`, `$password_rules`, `$field_errors`,
`$error_summary`, `$field_ids`, `$min_length`, `$password_js_config_json` und einige mehr. Alte Templates funktionieren
unverändert weiter. Die Variablen und die Hooks `data-exp-password-*` des gemeinsamen Skripts sind in
[dem Kompatibilitätshinweis](../../../eng-us/bc/6.0/user-password-change.md) aufgeführt.

## Demo-Modus

Auf einer öffentlichen Demo (Umgebungsvariable `EXP_DEMO_MODE=1`) verweigert die Seite die Änderung des Passworts eines Administrators: Sie zeigt „Diese Installation ist eine öffentliche Demo: Die Passwörter von Administratoren können nicht geändert oder zurückgesetzt werden.“ bereits vor jeder Eingabe (`demo_mode_locked`, `demo_mode_text`) und nach dem Absenden in der Fehlerzusammenfassung, und es wird nichts geändert. Ein Design, das `user/password.tpl` überschreibt, zeigt den Hinweis mit `first_set( $demo_mode_locked, false() )`. [Demo-Modus](../../../eng-us/bc/6.0/demo-mode.md).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
