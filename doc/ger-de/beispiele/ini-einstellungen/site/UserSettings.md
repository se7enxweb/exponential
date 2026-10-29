---
title: site.ini [UserSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [UserSettings] in site.ini: MaxNumberOfFailedLogin, TrustedIPList, ShowMessageIfExceeded, MinPasswordLength, WeakPasswordCheck, WeakPasswordFile, ForgotPasswordKeyLifetime, GeneratePasswordIfEmpty ..."
keywords: Einstellung, site.ini, UserSettings, INI, setting, MaxNumberOfFailedLogin, TrustedIPList, ShowMessageIfExceeded, MinPasswordLength, WeakPasswordCheck, WeakPasswordFile, ForgotPasswordKeyLifetime, GeneratePasswordIfEmpty, GeneratePasswordLength, AnonymousUserID, DefaultUserPlacement, DefaultSectionID, RegistrationFeedback, RegistrationEmail, VerifyUserType, PasswordInRegistrationEmail, EmailRegistrationInfo, ActivationByAdministratorSendsApprovalMail, RegistrationAdminLinks, RegistrationAdminBaseURL, RegistrationAdminSiteAccess, RegistrationActionTTL, AccountApprovalMode, RegisterAutoLogin, UserClassID, UserGroupClassID, UserClassGroupID, UserCreatorID, DeactivateAndMove, DeactivatedUserGroupNodeID
type: generated
generated: exp:help:examples
---
# site.ini [UserSettings]

Der Block `[UserSettings]` von `settings/site.ini`, mit 45 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MaxNumberOfFailedLogin

> Die Möglichkeit, die Anmeldung nach MaxNumberOfFailedLogin fehlgeschlagenen Anmeldeversuchen zu verweigern.  
> Höchstzahl fehlgeschlagener Anmeldeversuche.  
> Ist die Höchstzahl '0', ist diese Möglichkeit deaktiviert  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
MaxNumberOfFailedLogin=0
*/ ?>
```

## TrustedIPList

> Ein Array mit IP-Adressen oder Netzen, die sich nach einigen fehlgeschlagenen Anmeldeversuchen immer anmelden dürfen.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
# ein leeres "TrustedIPList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TrustedIPList[]
*/ ?>
```

## ShowMessageIfExceeded

> TrustedIPList\[\]=192.0.0.42  
> TrustedIPList\[\]=192.0.0.0/27  
> Wenn true und die fehlgeschlagenen Anmeldeversuche des aktuellen Benutzers MaxNumberOfFailedLogin überschreiten, erhält der Benutzer eine Meldung.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
ShowMessageIfExceeded=false
*/ ?>
```

## MinPasswordLength

> Mindestlänge des Passworts  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
MinPasswordLength=10
*/ ?>
```

## WeakPasswordCheck

> Ein bekanntes oder häufiges Passwort ablehnen (expWeakPasswordList: "admin", "password",  
> "qwerty", "123456", die eigenen Namen der Software und Ähnliches, mit ihren einfachen  
> Varianten: Groß-/Kleinschreibung, Ziffern und Symbole an den Enden, Buchstabentausch wie "p@ssw0rd",  
> häufige Endungen, ein doppelt getipptes Wort; außerdem Tastaturfolgen und wiederholte Zeichen).  
> Geprüft für jedes neue Passwort: user/password, Registrierung, das Bearbeitungsformular des Benutzers,  
> exp:resetuserpassword und die Webdienste. Die Anmeldung mit einem vorhandenen Passwort  
> wird nie abgelehnt. Die Installer (Setup-Assistent, Kickstarter, exp:install) prüfen immer,  
> egal was hier steht. disabled: Nur die Länge und \[PasswordSettings\] zählen.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
WeakPasswordCheck=enabled
*/ ?>
```

## WeakPasswordFile

> Weitere abzulehnende Wörter, eines pro Zeile, in ihrer Grundform (Varianten werden ebenfalls abgelehnt)  
> WeakPasswordList\[\]  
> WeakPasswordList\[\]=ourcompanyname  
> Eine Datei mit weiteren Wörtern, eines pro Zeile (# beginnt einen Kommentar), relativ zum  
> Wurzelverzeichnis der Installation oder absolut; einmal pro Prozess gelesen, solange sie unverändert ist  
> WeakPasswordFile=settings/weak-passwords.txt  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
WeakPasswordFile=
*/ ?>
```

## ForgotPasswordKeyLifetime

> Sekunden, die ein Link „Passwort vergessen?“ nach dem Versand funktioniert (3600: eine Stunde).  
> Ein abgelaufener Link wird mit einer eigenen Meldung abgelehnt; abgelaufene Links werden beim  
> nächsten Aufruf von user/forgotpassword entfernt. 0: Ein Link funktioniert, bis er verwendet wird (das  
> Verhalten vor 6.0.15).  

Standard: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
ForgotPasswordKeyLifetime=3600
*/ ?>
```

## GeneratePasswordIfEmpty

> Wenn true, wird automatisch ein Passwort erzeugt, wenn es leer ist,  
> und dem Benutzer per E-Mail gesendet.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
GeneratePasswordIfEmpty=true
*/ ?>
```

## GeneratePasswordLength

Standard: `16`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
GeneratePasswordLength=16
*/ ?>
```

## AnonymousUserID

> Die ID des anonymen Benutzers; dieser Benutzer wird  
> für jeden verwendet, der nicht angemeldet ist.  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
AnonymousUserID=10
*/ ?>
```

## DefaultUserPlacement

> Wo neu selbst registrierte Benutzer gespeichert werden  

Standard: `12`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
DefaultUserPlacement=12
*/ ?>
```

## DefaultSectionID

> In welche Sektion neu selbst registrierte Benutzer kommen  
> (0 bedeutet, dass der Benutzer die Sektions-ID  
>  von seiner neuen Platzierung erhält)  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
DefaultSectionID=0
*/ ?>
```

## RegistrationFeedback

> Welche Art von Rückmeldung für neu angelegte Benutzer an die Website gesendet  
> wird. Verwenden Sie eine durch Semikolons getrennte Liste von Rückmeldungsarten.  
> Nativ wird nur email unterstützt; leer schaltet es ab.  
> Beispiel für das Einrichten einer eigenen Rückmeldungsart:  
> Die Funktion 'registrationFeedback' wird an der Klasse verwendet; wie der E-Mail-Code umgesetzt ist,  
> sehen Sie in kernel/user/register.php!  
> \[RegistrationFeedback\_<custom\_type>\]  
> Class=<custom\_type\_class\_name>  
> #File is optional, autoload will be used if not set  
> File=<custom\_type\_file\_path>  

Standard: `email`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RegistrationFeedback=email
*/ ?>
```

## RegistrationEmail

> Wohin die E-Mail über neu registrierte Benutzer gesendet wird, wenn  
> user/registrationfeedback.tpl keinen email\_receiver zurückgibt  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RegistrationEmail=
*/ ?>
```

## VerifyUserType

> Art der Prüfung bei der Benutzerregistrierung; nativ wird nur email  
> unterstützt, leer schaltet es ab.  
> Beispiel für das Einrichten einer eigenen Prüfung:  
> Die Funktion 'verifyUser' wird an der Klasse verwendet; wie der E-Mail-Code umgesetzt ist,  
> sehen Sie in kernel/user/register.php!  
> Der Rückgabewert der Funktion entscheidet, ob die Mail an den Benutzer gesendet wird.  
> \[VerifyUserType\_<custom\_type>\]  
> Class=<custom\_type\_class\_name>  
> #File is optional, autoload will be used if not set  
> File=<custom\_type\_file\_path>  

Standard: `email`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
VerifyUserType=email
*/ ?>
```

## PasswordInRegistrationEmail

> Wenn auf enabled gesetzt, wird das Passwort des Benutzers in der Registrierungsmail gesendet  
> Dieses Passwort wird NICHT im Klartext gespeichert  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
PasswordInRegistrationEmail=disabled
*/ ?>
```

## EmailRegistrationInfo

> Wenn auf disabled gesetzt, wird die Bestätigungsmail der Registrierung nicht an den Benutzer gesendet,  
> der das Konto angelegt hat  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
EmailRegistrationInfo=enabled
*/ ?>
```

## ActivationByAdministratorSendsApprovalMail

> Ob Aktivieren unter Benutzer > Nicht aktivierte Benutzer (user/unactivated) dem  
> aktivierten Benutzer die Mail „Registrierung genehmigt“ sendet (user/registrationapproved.tpl),  
> wie es der eigene Aktivierungslink des Benutzers tut. disabled aktiviert ohne Mail.  
> EmailRegistrationInfo=disabled schaltet diese Mail überall ab.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
ActivationByAdministratorSendsApprovalMail=enabled
*/ ?>
```

## RegistrationAdminLinks

> Die Mail „Neuer Benutzer registriert“ an den Administrator (RegistrationFeedback=email,  
> user/registrationfeedback.tpl) enthält signierte Links zur Administration:  
> das Konto ansehen, es genehmigen (aktivieren), es ablehnen (entfernen) und den Freigabe-  
> eintrag, wenn ein Workflow es zurückhält. Jeder öffnet user/registration, das den  
> angemeldeten Administrator um Bestätigung bittet. Anleitung: doc/eng-us/features/6.0/registration-admin-links.md  
> enabled oder disabled (die Mail hat dann keine Links)  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RegistrationAdminLinks=enabled
*/ ?>
```

## RegistrationAdminBaseURL

> Die Adresse der Administration, auf die die Links zeigen, z. B.  
> https://admin.example.com oder https://www.example.com/admin. Leer: https:// und  
> die SiteURL von RegistrationAdminSiteAccess, sonst die Adresse dieses Siteaccess.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RegistrationAdminBaseURL=
*/ ?>
```

## RegistrationAdminSiteAccess

> Der Administrations-Siteaccess. Leer: der Siteaccess, dessen \[SiteSettings\]  
> FrontSiteAccess dieser ist, sonst admin.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RegistrationAdminSiteAccess=
*/ ?>
```

## RegistrationActionTTL

> Wie lange ein Link funktioniert, in Sekunden (604800 = 7 Tage). Links zum Genehmigen und Ablehnen  
> funktionieren einmal.  

Standard: `604800`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RegistrationActionTTL=604800
*/ ?>
```

## AccountApprovalMode

> Wie das Setup (Setup-Assistent, Kickstarter) die Freigabe neuer Redakteurskonten  
> einrichtet: group (der Standard: das Workflow-Ereignis "Approve user accounts",  
> entschieden nach der Gruppe, in die ein Konto kommt), section (der Abschnitt Staff für  
> Administrator users und Editors und das einfache Freigabe-Ereignis darauf) oder none.  
> Wählen Sie eines, nie beides: Ein Konto würde zweimal angefragt. Eine bestehende  
> Installation: bin/php/adduserapproval.php --apply \[--mode=section\].  
> Anleitung: doc/eng-us/bc/6.0/user-account-approval.md  

Standard: `group`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
AccountApprovalMode=group
*/ ?>
```

## RegisterAutoLogin

> Wenn auf disabled gesetzt, wird der Benutzer bei der Registrierung nicht automatisch angemeldet,  
> auch wenn er automatisch aktiviert wird  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RegisterAutoLogin=enabled
*/ ?>
```

## UserClassID

> Welche Inhaltsklasse zum Anlegen von Benutzern verwendet wird  

Standard: `4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
UserClassID=4
*/ ?>
```

## UserGroupClassID

> Welche Inhaltsklasse zum Anlegen von Benutzergruppen verwendet wird  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
UserGroupClassID=3
*/ ?>
```

## UserClassGroupID

> Welche Klassengruppe die Benutzerklassen enthält; steuert, welche Klassen  
> im Navigationsbereich der Benutzer angezeigt werden. Wenn leer, werden alle Klassen verwendet.  

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
UserClassGroupID=2
*/ ?>
```

## UserCreatorID

> Welcher Benutzer als Ersteller gilt  

Standard: `14`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
UserCreatorID=14
*/ ?>
```

## DeactivateAndMove

> „Deaktivieren und verschieben“ (doc/eng-us/features/6.0/user-hygiene.md): enabled bietet auf der Seite eines Benutzers, im Kontextmenü  
> und unter Benutzer > Benutzerkonten prüfen an, Konten zu deaktivieren und alle ihre Platzierungen in einer verborgenen  
> Benutzergruppe ohne Rollen zusammenzuführen, sich zu merken, wo sie waren, und sie beim Reaktivieren zurückzuverschieben; eine Möglichkeit,  
> Konten stillzulegen, ohne sie zu entfernen (Benutzer kommen nie in den Papierkorb). disabled (der Standard) zeigt nichts und legt  
> nichts an. Braucht die Richtlinie user/deactivate.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
DeactivateAndMove=disabled
*/ ?>
```

## DeactivatedUserGroupNodeID

> Der Knoten dieser Gruppe. Leer: die Benutzergruppe mit der Remote-ID exp\_deactivated\_users\_group, bei der ersten Verwendung unter  
> content.ini \[NodeSettings\] UserRootNode angelegt (oder vorab mit bin/php/ezcreatedeactivatedusergroup.php).  
> Eine hier gesetzte Knoten-ID, die nicht existiert, hält die Aktion an; sie wird nie durch eine neue Gruppe ersetzt.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
DeactivatedUserGroupNodeID=
*/ ?>
```

## HashType

> Verwenden Sie md5\_password, md5\_user, md5\_site oder plaintext  
> md5\_password erzeugt den Passwort-Hash nur aus dem Passwort.  
> md5\_user erzeugt den Passwort-Hash aus Benutzer und Passwort.  
> md5\_site erzeugt den Passwort-Hash aus Website, Benutzer und Passwort  
> plaintext erzeugt keinen Hash, sondern speichert das Passwort, wie es ist; das wird nicht  
> empfohlen, weil es ein großes Sicherheitsrisiko ist.  
> bcrypt erzeugt den Hash nur aus dem Passwort  
> php\_default verwendet die Methode, die PHP für angemessen hält (derzeit bcrypt, seit PHP 5.5)  
> Hinweis: Mit md5\_site erzeugte Passwort-Hashes funktionieren nach  
>       einer Änderung des Website-Namens nicht mehr.  

Standard: `php_default`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
HashType=php_default
*/ ?>
```

## SiteName

> Welcher SiteName beim Hashen von user\_password  
> mit dem HashType 'md5\_site' verwendet wird  

Standard: `ez.no`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
SiteName=ez.no
*/ ?>
```

## UpdateHash

> true bedeutet, den Hash bei der Anmeldung auf HashType zu aktualisieren, wenn er abweicht  
> Nützlich bei einem Upgrade von einer älteren Exponential-Website  
> oder einem anderen System, das mysql als Passworttyp verwendet; der  
> Hash wird dann auf das aktualisiert, was gesetzt ist.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
UpdateHash=true
*/ ?>
```

## AuthenticateMatch

> Abgleich für die Authentifizierung, eine Liste der zu verwendenden Felder  
> Verfügbar sind login und email  

Standard: `login;email`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
AuthenticateMatch=login;email
*/ ?>
```

## RequireUniqueEmail

> Steuert, ob für alle Benutzer eine eindeutige E-Mail-Adresse erforderlich ist;  
> wenn true und email in AuthenticateMatch gesetzt ist, ist auf der Website nur  
> ein Vorkommen jeder E-Mail-Adresse erlaubt.  
> Auf false setzen, um es abzuschalten  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RequireUniqueEmail=true
*/ ?>
```

## RequireConfirmEmail

> Seit 4.5; steuert, ob Benutzer ihre E-Mail-Adresse bestätigen müssen  
> Wenn true, müssen Benutzer ihre E-Mail-Adresse bei der Registrierung  
> oder beim Bearbeiten ihres Profils erneut eingeben  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RequireConfirmEmail=false
*/ ?>
```

## UseSpecialCharacters

> Steuert, ob in Passwörtern zusätzlich zu den Zeichen im Bereich a-z, A-Z und 0-9  
> Sonderzeichen erlaubt sind.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
UseSpecialCharacters=false
*/ ?>
```

## UserNameValidationRegex

> Eigene Prüfung des Anmeldenamens  
> Betrifft nur neue Benutzer und vorhandene Benutzer, wenn sie ihre Zugangsdaten bearbeiten  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
# ein leeres "UserNameValidationRegex[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
UserNameValidationRegex[]
*/ ?>
```

## UserNameValidationErrorText

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
# ein leeres "UserNameValidationErrorText[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
UserNameValidationErrorText[]
*/ ?>
```

## DefaultUserNameValidationErrorText

Standard: `User login name did not validate!`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
DefaultUserNameValidationErrorText=User login name did not validate!
*/ ?>
```

## LogoutRedirect

> Aktivieren Sie diese beiden Zeilen, wenn Ihre MySQL-Datenbank nicht den Zeichensatz utf8mb4 verwendet.  
> UserNameValidationRegex\[utf8mb4\]=%(?:\\xF0\[\\x90-\\xBF\]\[\\x80-\\xBF\]{2}|\[\\xF1-\\xF3\]\[\\x80-\\xBF\]{3}|\\xF4\[\\x80-\\x8F\]\[\\x80-\\xBF\]{2})%xs  
> UserNameValidationErrorText\[utf8mb4\]=The username cannot contain 4-byte characters.  
> Beispiele für Prüfungen  
> UserNameValidationRegex\[sw\]=/^\\s/  
> UserNameValidationErrorText\[sw\]=The username cannot start with a whitespace.  
> UserNameValidationRegex\[ew\]=/\\s$/  
> UserNameValidationErrorText\[ew\]=The username cannot end with a whitespace.  
> UserNameValidationRegex\[rw\]=/\\s+/  
> UserNameValidationErrorText\[rw\]=The username cannot contain repeated whitespace.  
> UserNameValidationRegex\[lb\]=/\\n|\\r/  
> UserNameValidationErrorText\[lb\]=The username cannot contain line breaks.  
> UserNameValidationRegex\[t\]=/\\t/  
> UserNameValidationErrorText\[t\]=The username cannot contain tabs.  
> Alternatives Beispiel, das nur 'Wort'-Zeichen erlaubt  
> UserNameValidationRegex\[w\]=/\\W/  
> UserNameValidationErrorText\[w\]=The username can only contain a-z, digits or underline.  
> Oder  
> UserNameValidationRegex\[wh\]=/\[^A-Za-z0-9\_-\]/  
> UserNameValidationErrorText\[wh\]=The username can only contain a-z, digits, underline or hyphen.  
> Fügen Sie beim Testen dies am Ende hinzu, und denken Sie daran, es zu entfernen, wenn Sie fertig sind.  
> UserNameValidationRegex\[test\]=/\\w/  
> UserNameValidationErrorText\[test\]=The username seems to be ok!  
> Auf welche URL nach einer Abmeldung weitergeleitet wird  

Standard: `/user/login`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
LogoutRedirect=/user/login
*/ ?>
```

## RedirectOnLogoutWithLastAccessURI

> Die LastAccessURI verwenden, um zu entscheiden, wohin der Benutzer beim Abmelden weitergeleitet wird  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
RedirectOnLogoutWithLastAccessURI=disabled
*/ ?>
```

## LoginHandler

> Textattribut der Klasse 'User', das die URI enthält, auf die  
> der Benutzer nach der Anmeldung weitergeleitet wird.  
> LoginRedirectionUriAttribute\[user\]=front\_page\_uri  
> Textattribut der Klasse 'User group', das die URI enthält, auf die  
> ein Benutzer dieser Gruppe nach der Anmeldung weitergeleitet wird.  
> LoginRedirectionUriAttribute\[group\]=front\_page\_uri  

Standard: eine Liste mit 1: `standard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
# ein leeres "LoginHandler[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LoginHandler[]
LoginHandler[]=standard
*/ ?>
```

## SingleSignOnHandlerArray

> LoginHandler\[\]=LDAP  
> LoginHandler\[\]=textfile  
> Legt die verfügbaren Single-Sign-on-Handler fest.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
# ein leeres "SingleSignOnHandlerArray[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SingleSignOnHandlerArray[]
*/ ?>
```

## ExtensionDirectory

> Erweiterungsverzeichnisse für Login-Handler. Ein Login-Handler muss in  
> extension/<extension\_directory>/login\_handler/<login\_handler|lowercase>user.php liegen,  
> und die Klasse sollte eZ<login\_handler>User heißen  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[UserSettings]
# ein leeres "ExtensionDirectory[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionDirectory[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
