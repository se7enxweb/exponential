# Passwort bei der nächsten Anmeldung ändern: was ein Update mit sich bringt (6.0.15)

Die Anleitung für Benutzer ist [Passwort bei der nächsten Anmeldung ändern](../../funktionen/6.0/passwort-bei-naechster-anmeldung-aendern.md).
Diese Seite beschreibt, was eine Website, ein Design oder eine Extension nach dem Update bemerken kann.

## Nichts ändert sich, bis ein Konto markiert wird

- Es wird keine Tabelle und keine Spalte hinzugefügt. Die Markierung ist eine Zeile in `ezpreferences` (Name
  `exp_password_change_required`), es gibt also kein Update-Skript, bei keiner Datenbank.
- Bestehende Konten tragen keine Markierung. Die Anmeldung funktioniert wie bisher, bis ein Administrator ein Konto
  markiert.
- Neue Konten, die ein Administrator in der Administration anlegt, sind **standardmäßig** markiert (das
  Kontrollkästchen im Formular ist vorausgewählt). Wählen Sie es ab, wenn die Person schon ein eigenes Passwort hat.
  Konten aus der Selbstregistrierung, von der Konsole, aus Importen oder aus Code werden nicht markiert.

## Für Designs und Extensions

- **`content/datatype/edit/ezuser.tpl`**: das Kontrollkästchen steht in `design/standard` und `design/admin4`
  (admin4l und der Siteaccess editor verwenden die von admin4, admin und admin3 die von standard). Ein Design oder eine
  Extension mit eigener Kopie des Templates zeigt kein Kontrollkästchen, bis sie die beiden in der Anleitung
  beschriebenen Felder ergänzt; ohne sie lässt das Veröffentlichen des Formulars die Markierung, wie sie ist.
- **`user/password.tpl`**: die neue Template-Variable `$password_change_required` ist wahr, solange der Benutzer
  wegen der Markierung dort ist. `design/standard`, `design/admin` und `design/admin4` zeigen einen Hinweis; die Kopie
  eines anderen Designs funktioniert unverändert, zeigt aber keinen Hinweis.
- **Ansichten, die ein markierter Benutzer erreichen muss**: jede Ansicht außer denen in `site.ini
  [PasswordChangeRequiredSettings] ExemptViews[]` (`user/password`, `user/logout`, `user2fa`) antwortet einem
  markierten, angemeldeten Benutzer mit einer Weiterleitung zu `user/password`, auch Ajax-Aufrufe (`ezjscore/call`).
  Eine Extension, deren Ansichten offen bleiben müssen, ergänzt sie in der Liste.
- **`user/preferences`** schreibt die Einstellung `exp_password_change_required` nicht.
- **REST**: ein markiertes Konto erhält von jeder Route, die eine Anmeldung verlangt, `403` mit
  `{"error":"password_change_required"}`, auch mit einem OAuth-Token oder einem persönlichen API-Schlüssel. Ein Client,
  der jedes 403 als „keine Berechtigung“ behandelt, sollte stattdessen die `error_description` anzeigen.
- **WebDAV und SOAP** weisen die Anmeldung eines markierten Kontos ab.
- **Demo-Modus** (`EXP_DEMO_MODE=1`): die Markierung eines Administrators bleibt gespeichert, wird aber ignoriert.

## Neu

- Klasse `expPasswordChangeRequired` (`kernel/classes/user/exppasswordchangerequired.php`).
- Klasse `expRestPasswordChangeRequiredStatus` (`kernel/private/rest/classes/status/password_change_required.php`).
- Die `eZUser`-Attribute `password_change_required` und `password_change_checked` sowie die Eigenschaft
  `PasswordChangeRequiredDraft` (die Wahl des Formulars, solange das Objekt ein Entwurf ist; im Entwurf als
  `password_change_required` gespeichert).
- `eZUserType::postedPasswordChangeRequired()`.
- `site.ini [PasswordChangeRequiredSettings] ExemptViews[]`.
- Konsolenbefehl `exp:user-password-change-required` (`bin/php/userpasswordchangerequired.php`).
- Audit-Ereignisse `access.user.password.change_required.set`, `.clear` und `.redirect` ([Katalog](audit.md)).

## Eine Installation prüfen

```bash
./console exp:user-password-change-required status --login=<login>
./console exp:audit tail --channel=access --name='access.user.password.change_required.*' --lines=20
```
