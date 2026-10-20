# Bekannte Passwörter werden abgelehnt, und ein Link „Passwort vergessen?“ läuft ab (6.0.15)

Drei Lücken im Umgang von Exponential 6 mit Passwörtern werden in 6.0.15 geschlossen:

1. Der **Kickstarter** installierte ein `kickstart.ini [site_admin] Password=` beliebiger Länge.
2. Der **Setup-Assistent** akzeptierte ein bekanntes Wort, solange es lang genug war, und user/password, die
   Registrierung, das Formular zur Benutzerbearbeitung, die Konsole und die Webdienste prüften nur die Länge (und die
   optionalen Regeln in `[PasswordSettings]`).
3. Ein **Link „Passwort vergessen?“** funktionierte, bis er benutzt wurde, egal wie alt er war.

## Was sich ändert

### Eine Liste bekannter Passwörter

`expWeakPasswordList` (`kernel/classes/expweakpasswordlist.php`) enthält die Liste und die Prüfung. Sie lehnt ab:

- die eingebauten Wörter: die eigenen Namen der Software und historische Standardwerte (`publish`, `exponential`,
  `ezpublish`, `demo`), Kontonamen (`admin`, `administrator`, `root`, `user`, `test`), die Klassiker (`password`,
  `changeme`, `secret`, `welcome`, `letmein`, `qwerty`, `123456` und so weiter);
- einfache Varianten eines Wortes: beliebige Groß- und Kleinschreibung, Leerraum davor oder danach, Ziffern und
  Sonderzeichen am Ende oder am Anfang (`Admin2026!`), Trennzeichen (`change me`, `change_me`), vertauschte Zeichen
  (`p@ssw0rd`), häufige Endungen (`-s`, `-es`, `-ed`, `-er`, `-ers`, `-ing`, `-ings`) und das zweimal eingegebene
  Wort;
- Muster: ein Zeichen oder ein kurzes Muster, wiederholt (`aaaaaaaaaa`, `abcabcabc`), Folgen auf der Tastatur, im
  Alphabet oder bei den Ziffern (`qwertyuiop`, `abcdefghij`, `1234567890`), auch mit Ziffern oder Sonderzeichen
  darum herum.

Ein Passwort wird als Ganzes verglichen: Eine lange Passphrase, die ein solches Wort nur enthält
(`admin-of-the-blue-forest-42`), wird angenommen.

Jede Stelle, an der ein neues Passwort gesetzt wird, verwendet sie:

| Wo | Wie |
|---|---|
| Setup-Assistent | der Schritt für den Administrator lehnt es ab: „Dieses Passwort ist bekannt oder zu häufig, jeder könnte es erraten. Bitte wählen Sie ein anderes.“ |
| Kickstarter (`kickstart.ini`) | ein leeres, bekanntes oder zu kurzes (`MinPasswordLength`) Passwort wird durch ein zufälliges mit 20 Zeichen ersetzt, das einmal ausgegeben und in `var/log/initial-admin-password` (Modus `0600`) geschrieben wird |
| `exp:install` | dieselben Regeln wie bisher, jetzt mit der gemeinsamen Liste (bisher eine Liste mit neun Wörtern) |
| `user/password`, Registrierung, Formular zur Benutzerbearbeitung | die Regel `not_common` von `expPasswordPolicy`, neben dem Feld aufgeführt: „Kein bekanntes oder häufiges Passwort“ |
| `exp:resetuserpassword` (`bin/php/resetuserpassword.php`) | "Target password does not validate. The password is well-known or too common. Choose another one." |
| Webdienste (`expaccount`, `expuser`) | 422 mit den Regeln, die das Passwort nicht erfüllt; `expuser::validatePassword` gibt außerdem `errors` zurück |

### Einstellungen (`site.ini [UserSettings]`)

```ini
WeakPasswordCheck=enabled          # disabled: only the length and [PasswordSettings] count, as before
#WeakPasswordList[]=ourcompanyname # more words, in their base form; their variants are refused too
WeakPasswordFile=                  # a file with one word per line (# comments), relative to the root or absolute
ForgotPasswordKeyLifetime=3600     # seconds a forgot-password link works; 0: until it is used, as before
```

Die Installationsprogramme prüfen immer, unabhängig von `WeakPasswordCheck`: Eine neue Website startet nie mit einem
bekannten Administratorpasswort.

### Links „Passwort vergessen?“ laufen ab

Die Tabelle `ezforgot_password` hat schon immer den Zeitpunkt gespeichert, zu dem ein Schlüssel erzeugt wurde
(`time`), daher ist **keine Datenbankänderung** nötig.

- Ein Link, der älter als `ForgotPasswordKeyLifetime` ist (eine Stunde im Auslieferungszustand), wird mit einer
  eigenen Meldung abgelehnt: „Dieser Link ist abgelaufen.“ und einem Link, um einen neuen anzufordern; der Schlüssel
  wird entfernt. Designs, die `user/forgotpassword.tpl` ohne die neue Variable `key_expired` überschreiben, zeigen
  ihren Text „The key is invalid or has been used.“, wie bei jedem Schlüssel, der nicht funktioniert.
- Die E-Mail nennt, wie lange der Link funktioniert (`key_lifetime_minutes` in `user/forgotpasswordmail.tpl`).
- Abgelaufene Schlüssel werden bei jedem Aufruf von `user/forgotpassword` entfernt
  (`eZForgotPassword::removeExpired()`, ein `DELETE` auf eine kleine Tabelle), daher ist kein Cronjob nötig. Die
  Webdienste (`expaccount::forgotKeyValid`, `reset`) verwenden dieselbe Gültigkeitsdauer; bisher hatten sie fest
  24 Stunden.
- Das Audit-Protokoll verzeichnet einen abgelehnten, abgelaufenen Link als `access.user.password.reset.failed` mit dem
  Grund `expired`.

API: `eZForgotPassword::lifetime()`, `isExpiredAt( $time, $lifetime, $now )`, `isExpired()`, `expiresAt()`,
`removeExpired()`.

## Kompatibilität

- **Niemand wird ausgesperrt.** Geprüft wird nur ein neues Passwort. Die Anmeldung mit einem vorhandenen Passwort,
  wie schwach es auch ist, funktioniert wie bisher. Ein Benutzer, dessen Passwort auf der Liste steht, wird erst
  abgewiesen, wenn er ein neues wählt.
- **Skripte und Integrationen**, die Passwörter über die Formulare, die Konsole oder die Webdienste setzen und dabei
  ein bekanntes Passwort verwenden (zum Beispiel in Tests), werden jetzt abgewiesen. Verwenden Sie ein echtes
  Passwort, oder setzen Sie `WeakPasswordCheck=disabled` im Override dieser Umgebung.
- **Templates:** `user/password.tpl` listet die Regeln auf, die es erhält, daher erscheint die neue Regel ohne
  Änderung am Template. Das Template des Setup-Assistenten `setup/init/site_admin.tpl` hat das neue Flag
  `password_too_common`.
- **Links „Passwort vergessen?“**, die schon vor dem Upgrade verschickt wurden, laufen eine Stunde nach ihrer
  Erzeugung ab. Um das alte Verhalten beizubehalten, setzen Sie `ForgotPasswordKeyLifetime=0` in einem Override.
- `eZStepSiteAdmin::isWeakDefaultPassword()` behält seinen Namen und verwendet jetzt die gemeinsame Liste;
  `eZStepSiteAdmin::kickstartPasswordProblem()` und `minAdminPasswordLength()` sind neu.
- **Öffentliche Demos:** Mit der Umgebungsvariablen `EXP_DEMO_MODE=1` akzeptieren die Installationsprogramme ein
  bekanntes Administratorpasswort beliebiger Länge (ein leeres wird weiterhin abgelehnt), sodass eine Demo das
  Passwort installieren kann, das sie veröffentlicht; Administratorpasswörter können dann nicht geändert oder
  zurückgesetzt werden. Nur für öffentliche Demos: [Demomodus](../../../eng-us/bc/6.0/demo-mode.md).

## Tests

- `expWeakPasswordListTest` (ohne Datenbank): jedes eingebaute Wort und seine Varianten, die Muster, starke und
  generierte Passwörter, die angenommen werden, die zusätzliche Liste und Datei, der Schalter, die Regel `not_common`
  von `expPasswordPolicy`, die Installationsprogramme.
- `eZForgotPasswordExpiryTest` (ohne Datenbank): die Einstellung der Gültigkeitsdauer, die Grenze des Ablaufs, das
  Schlüsselobjekt, die ausgelieferten Einstellungen und Templates.
- `eZSetupStepsTest`, `InstallPasswordTest`, `expPasswordPolicyTest` (das Kontoformular und die Konsole lehnen ein
  bekanntes Passwort ab).
