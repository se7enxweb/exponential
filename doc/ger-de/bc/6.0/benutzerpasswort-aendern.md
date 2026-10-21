# user/password: Was sich für Templates, Einstellungen und Session-Handler geändert hat (6.0.15)

Die Anleitung für Benutzer ist [Ihr Passwort ändern](../../funktionen/6.0/moderne-passwortaenderung.md).
Diese Seite beschreibt, was eine Website, ein Design oder eine Erweiterung nach dem Upgrade bemerken kann.

## Unverändert

- Die POST-Felder `oldPassword`, `newPassword` und `confirmPassword`; die Schaltflächen `OKButton` und `CancelButton`;
  `RedirectURI` und `RedirectOnCancel`; die Formularaktion `user/password/<UserID>`.
- Die Reihenfolge der Prüfungen: zuerst das aktuelle Passwort. Ist es falsch, wird nichts anderes gemeldet.
- Die Weiterleitungen, sowohl bei Abbrechen als auch nach einer Änderung (nach einer Änderung gibt es keine: die
  Seite wird erneut angezeigt).
- Die Template-Variablen `$userID`, `$userAccount`, `$oldPasswordNotValid`, `$newPasswordNotMatch`,
  `$newPasswordTooShort` und `$message`.
- Die Regeln, die eine Website bisher hatte: Die neuen Regeln in `[PasswordSettings]` sind standardmäßig alle
  ausgeschaltet.

## Geändert

- **`$oldPassword`, `$newPassword` und `$confirmPassword` sind immer leer.** Alte Templates haben sie in die
  `value`-Attribute der Felder geschrieben und damit eingegebene Passwörter in den Quelltext der Seite übernommen.
  Nach einem fehlgeschlagenen Versuch sind die Felder jetzt leer.
- **Ist das aktuelle Passwort richtig, werden alle Probleme mit dem neuen Passwort auf einmal gemeldet.** Bisher hat
  eine fehlende Übereinstimmung den Längenfehler verdeckt. Jetzt können `$newPasswordNotMatch` und
  `$newPasswordTooShort` beide 1 sein.
- **`$message` ist 0, wenn nur eine der neuen optionalen Regeln nicht erfüllt ist.** Ein Template, das nur die drei
  alten Flags kennt, zeigt in diesem Fall keine Meldung an statt seines Textes „erfolgreich geändert“. Templates
  sollten `$password_changed` und `$has_errors` verwenden.
- **Die Sitzungs-ID wird nach einer Änderung erneuert**, und `[PasswordSettings] EndOtherSessions` beendet die
  anderen Sitzungen des Benutzers: Sie werden bei ihrer nächsten Anfrage über den Passwortstempel (siehe unten)
  abgemeldet, mit jedem Session-Handler; der Datenbank-Handler löscht sie außerdem sofort.
- **Nach jeder Änderung wird eine E-Mail gesendet** (`[PasswordSettings] ChangeNotificationMail=enabled`), in der
  unverzichtbaren Kategorie `security`. Sie schalten sie mit `ChangeNotificationMail=disabled` aus.
- **Das Audit-Ereignis `access.user.password.change.failed`** nennt in `after.rule` die erste nicht erfüllte Regel
  (bisher nannte es nur `length`).

## Neue Template-Variablen

| Variable | Typ | Bedeutung |
|---|---|---|
| `$password_changed` | bool | die Änderung wurde mit dieser Anfrage durchgeführt |
| `$min_length` | int | `[UserSettings] MinPasswordLength` (3, wenn nicht gesetzt) |
| `$password_rules` | list | `{id, text, min, failed}` für jede Regel, in der Reihenfolge der Anzeige; `text` ist übersetzt |
| `$failed_rules` | list | die IDs der Regeln, die bei diesem Absenden nicht erfüllt waren |
| `$field_errors` | hash | immer die Schlüssel `oldPassword`, `newPassword` und `confirmPassword`, jeweils eine Liste übersetzter Sätze |
| `$field_ids` | hash | `password-old`, `password-new` und `password-confirm`, mit dem POST-Namen als Schlüssel |
| `$error_summary` | list | `{field, field_id, text}` für jeden Fehler, in der Reihenfolge der Felder (`field` ist `''` bei einem Fehler des ganzen Formulars) |
| `$has_errors` | bool | ob die Zusammenfassung Einträge hat |
| `$sessions_ended` | int oder null | wie viele andere Sitzungen der Session-Handler sofort gelöscht hat; null, wenn er das nicht kann (der PHP-Handler) |
| `$other_sessions_signed_out` | bool | die anderen Sitzungen werden bei ihrer nächsten Anfrage abgemeldet (der Passwortstempel, `EndOtherSessions`) |
| `$notification_sent` | bool | ob die E-Mail an den Transport übergeben wurde |
| `$redirect_uri` | string | wohin Abbrechen führt, für einen Link „Weiter“ |
| `$password_js_config`, `$password_js_config_json` | hash, string | die Konfiguration für das gemeinsame Skript; geben Sie sie als `data-exp-password-config="{$password_js_config_json|wash}"` aus |

Das Skript liest diese Hooks: `data-exp-password-form` (das Formular), `data-exp-password="current|new|confirm"`,
`data-exp-password-toggle`, `data-exp-password-rules` / `data-exp-password-rule`, `data-exp-password-meter`,
`data-exp-password-match`, `data-exp-password-generate` und `data-exp-password-summary`.

## Anmeldung und currentUser(): der Passwortstempel

- `eZUser::setCurrentlyLoggedInUser()` (eine echte Anmeldung, kein Wechsel mit `NO_SESSION_REGENERATE`) schreibt die
  Sitzungsvariable `eZUserPasswordStamp`. `eZUser::logoutCurrent()` entfernt sie.
- `eZUser::currentUser()` meldet eine Sitzung ab, wenn ihr Stempel zu einem Passwort gehört, das sich seitdem
  geändert hat. Das schließt eine Änderung durch einen Administrator, durch content/edit des Kontos, durch „Passwort
  vergessen?“ oder über die Konsole ein. Code, der das Passwort eines Benutzers ändert und erwartet, dass die anderen
  Sitzungen dieses Benutzers angemeldet bleiben, sieht sie abgemeldet; setzen Sie
  `[PasswordSettings] EndOtherSessions=disabled`, um das alte Verhalten beizubehalten.
- `eZUser::store()` gibt der eigenen Sitzung des gespeicherten Benutzers (wenn er der Benutzer der aktuellen Sitzung
  ist) den neuen Stempel und aktualisiert die Benutzerobjekte, die die Anfrage hält
  (`$GLOBALS['eZUserGlobalInstance_…']`), sodass die Sitzung, die ihr eigenes Passwort ändert, angemeldet bleibt.
- Eine Sitzung ohne Stempel (vor dem Upgrade angemeldet), der anonyme Benutzer und Konten mit leerem Passwort-Hash
  (LDAP-, Textdatei- und SSO-Benutzer) werden nicht geprüft.
- Das erneute Hashen mit `UpdateHash` bei der Anmeldung gibt jetzt den Benutzer mit dem neuen Hash zurück und leert
  den Cache dieses Benutzers, der bisher den alten Hash behielt, bis er ablief. Die anderen Sitzungen des Benutzers
  werden nach einem solchen erneuten Hashen einmal abgemeldet.
- Bei einer Abweichung liest `currentUser()` die gespeicherte Zeile einmal, bevor es abmeldet, weil der
  Benutzer-Cache älter als das Passwort sein kann. `eZUser::purgeUserCacheByUserId()` macht die Cache-Datei in
  OPcache ungültig (`opcache_invalidate()`), bevor es sie löscht; damit erhalten auch die anderen Worker sofort die
  neuen Rollen.
- Login-Handler in Erweiterungen funktionieren weiter: Sie melden über `setCurrentlyLoggedInUser()` an. Ein Handler,
  der Benutzer ohne Passwort führt, sollte einen leeren `password_hash` (Typ 0) speichern, wie es die LDAP- und
  Textdatei-Handler tun.

## Der Datentyp Benutzerkonto und die Konsole

- `eZUserType::validateObjectAttributeHTTPInput()` (Registrierung, content/edit, die Benutzerbearbeitung in der
  Administration) prüft das Passwort mit `expPasswordPolicy::accountErrors()`. Mit den Standardwerten ist das
  dieselbe Längenprüfung wie bisher, mit demselben Satz. Sind Regeln in `[PasswordSettings]` eingeschaltet, werden
  ihre Sätze zu dem einen Validierungsfehler des Feldes zusammengefügt.
- Der Platzhalter `_ezpassword` des Bearbeitungsformulars für „unverändert“ wird nicht mehr auf seine Länge geprüft.
  Bisher lehnte eine Website mit `MinPasswordLength` über 11 jede Bearbeitung eines Benutzers ab, bei der das
  Passwort unverändert blieb.
- `bin/php/resetuserpassword.php` lehnt ab, was die Regeln ablehnen. Ein generiertes Passwort (`-g`) wird so lange
  neu generiert, bis es die Regeln erfüllt.
- `expPasswordPolicy::validate()` nimmt einen optionalen dritten Parameter an, den im Formular eingegebenen
  Anmeldenamen (für `not_login`).

## Session-Handler

`ezpSessionHandler` hat eine neue Methode, `deleteOtherSessionsOfUser( $userID, $keepSessionKey )`, die die Zahl der
beendeten Sitzungen zurückgibt oder null, wenn der Handler sie nicht beenden kann. Die Basisklasse gibt null zurück,
sodass sich ein Handler, der die Methode nicht überschreibt, genau wie bisher verhält. `ezpSessionHandlerDB`
überschreibt sie und beendet jede Sitzung über `destroy()`, sodass die Callbacks `destroy_pre` und `destroy_post`
ausgeführt werden.

## Übersetzungen

Die neuen Übersetzungskontexte sind `kernel/user/password`, `kernel/user/password/js`, `kernel/user/password/mail`
und `design/standard/user/password_changed_mail` (eng-US und ger-DE).
