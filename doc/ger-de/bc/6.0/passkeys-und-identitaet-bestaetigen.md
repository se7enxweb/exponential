# Passkeys und „Identität bestätigen“: eine Tabelle, zwei Ansichten und eine Frage vor heiklen Aktionen

Lesen Sie diese Seite, wenn Sie eine Installation auf 6.0.15 aktualisieren, wenn Sie `user/login.tpl` oder
`user/edit.tpl` überschreiben oder wenn Ihre Extension Ansichten hat, die Konten oder Einstellungen ändern oder Daten
exportieren. Die Funktionen erklären die Anleitungen [Passkeys](../../anleitungen/passkeys.md) und
[Identität bestätigen](../../anleitungen/identitaet-bestaetigen.md).

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neue Tabelle `exppasskey`; neue Ansichten `user/passkeys` und `setup/reauth`; neue Blöcke `site.ini [PasskeySettings]`, `[ReauthSettings]` und `[ReauthGroup_*]`; die Anmeldeformulare von `design/standard`, `design/admin` und admin4 melden auch mit einem Passkey an; heikle Aktionen auf den Webseiten fragen zuerst „Verify it's you“; sechs Audit-Ereignisse (`access.passkey.add`, `.rename`, `.remove`, `.use`, `.use.failed`, `access.session.reauth.ask`). |
| Wer betroffen ist | Jede Installation, die aktualisiert (die Tabelle). Jeder Administrator und Benutzer: das eigene Passwort oder die eigene E-Mail-Adresse ändern, Benutzer und Rollen verwalten, Setup-Aktionen und Exporte fragen jetzt einmal je zehn Minuten nach dem Passwort (oder einem Passkey oder Code). Overrides von `user/login.tpl` (ohne die Einbindung unten keine Passkey-Schaltfläche). Skripte, die in einer Browsersitzung an die aufgeführten Admin-Ansichten senden (sie erhalten 403 `reauth_required`). |
| Wie prüfen | `php update/common/scripts/6.0/createpasskeytable.php --dry-run --allow-root-user` sagt `table exppasskey: exists`; `/user/passkeys` antwortet 200; `/setup/reauth` zeigt fünf Gruppen |
| Wie beheben | Die Tabelle anlegen (unten); die Kernel-Autoloads neu erzeugen; die Caches INI, template und template-override leeren; PHP-FPM neu laden und Velocity neu starten. |
| Wie abschalten | Passkeys: `[PasskeySettings] Passkeys=disabled`. Die Frage: `[ReauthSettings] Reauth=disabled`, oder einzelne Gruppen auf `setup/reauth`. |

## Die Tabelle

`exppasskey`: `aaguid`, `algorithm`, `backup_eligible`, `backup_state`, `created`, `credential_hash` (eindeutig),
`credential_id`, `id`, `last_ip`, `last_used`, `name`, `public_key`, `sign_count`, `transports`, `user_handle`,
`user_id`. Indizes `exppasskey_credential` (eindeutig) und `exppasskey_user`.

- Neue Installationen: aus `share/db_schema.dba` (jede Datenbank) oder den Kernel-Schemas von MySQL, PostgreSQL und
  SQLite.
- Upgrades: das Ende von `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` (`CREATE TABLE IF NOT EXISTS`; ein
  abgesicherter `DO`-Block bei PostgreSQL), oder auf jeder Datenbank, Oracle und MongoDB eingeschlossen:

```bash
php update/common/scripts/6.0/createpasskeytable.php --allow-root-user
```

Ohne die Tabelle bieten die Passkey-Seiten nichts an und das Anmeldeformular zeigt keinen Passkey; nichts schlägt fehl.

## Templates

- `user/login.tpl` von `design/standard`, `design/admin` und `design/admin4` erhalten die Passkey-Schaltfläche, die
  Optionen in `data-exp-passkey-*`-Attributen, `autocomplete="username webauthn"` und
  `design/standard/javascript/exp_passkey.js`. Ein Design mit eigenem `user/login.tpl` bindet nach seinem Formular
  `{include uri='design:user/parts/passkey_login.tpl'}` ein.
- Neue Templates: `user/passkeys.tpl`, `user/reauth.tpl` (alle drei Designs), `setup/reauth.tpl` (admin, admin4),
  `user/parts/reauth_replay.tpl` (standard). Die Templates der Administration benutzen nur gemeinsame Komponenten und
  Tokens.
- `user/edit.tpl` (admin, admin4): eine Karte Passkeys aus `expUserAccountOverview` (Aktionsschlüssel `passkeys`).

## Die Frage vor heiklen Aktionen

Die Prüfung ist `expReauth::moduleResult()` in `ezpKernelWeb`, in dieser Reihenfolge: Die Richtlinien erlauben die
Ansicht, der Audit-Wächter (`expAuditGuard`), „Passwort bei der nächsten Anmeldung ändern“
(`expPasswordChangeRequired`), dann „Identität bestätigen“, dann die Anfrageregeln, dann die Ansicht. REST
(`ezpKernelRest`), SOAP, WebDAV und Konsolenbefehle laufen nicht hindurch. Die Ansicht einer Extension fragt nur, wenn
eine Regel sie nennt; siehe die Anleitung zum Ergänzen von Regeln.

Für Extensions:

- `expReauthCodeHandler` (`hasReauthCode( eZUser )`, `checkReauthCode( eZUser, $code )`): eine Klasse, die die Codes
  eines zweiten Anmeldeschritts prüft, eingetragen in `[ReauthSettings] CodeHandlers[]`. `expReauthTotpCode` tut das
  für `sevenx_authentication_2fa`.
- `expReauth::isFresh( $user )` sagt, ob der Benutzer innerhalb der Minuten bestätigt hat; `expAuditReauth` (die
  erneute Passworteingabe der Audit-Konsole) erkennt es an.
- Ein gefragter POST wird nach der Antwort mit seinen eigenen Feldern wiederholt; Dateien nicht (ein Browser kann sie
  nicht zweimal senden), deshalb sollte eine Ansicht mit Upload mit `*` genannt werden. Ein POST mit einer tatsächlich
  gewählten Datei öffnet das Formular nach der Antwort wieder; leer gebliebene Dateifelder sind kein Upload
  (`expReauth::hasUploads()`), und ein solches Formular wird wiederholt.
- `expReauth::addressedUser( $module, $view, $params, $post )` nimmt den POST als viertes Argument (optional): Ein POST
  an `collaboration/action`, der ein Element des Workflow-Ereignisses *Approve user accounts* entscheidet, betrifft das
  wartende Konto, also fragt die Gruppe users vor Freigeben und Ablehnen eines neuen Benutzerkontos
  ([Freigabe neuer Benutzerkonten](freigabe-von-benutzerkonten.md)). Inhaltsfreigaben betreffen kein Konto und fragen
  nicht.

## Audit

Sechs neue Namen (der Katalog hat 186): `access.passkey.add`, `access.passkey.rename`, `access.passkey.remove`,
`access.passkey.use`, `access.passkey.use.failed`, `access.session.reauth.ask`. `access.session.reauth` und
`access.session.reauth.failed` löst auch „Identität bestätigen“ aus. Eine Anmeldung mit Passkey ist
`access.session.login` mit dem Handler `expPasskeyLogin`.

## Später in 6.0.15: beide Wege zusammen, eine Richtlinie und weitere Gruppen

Lesen Sie dies auch, wenn Sie von einer früheren Vorabversion von 6.0.15 aktualisieren.

| | |
|---|---|
| Was sich änderte | „Identität bestätigen“ fragt nach Passwort **und** Passkey (`[ReauthSettings] Factors[]`), ohne Passkey nach Passwort und App-Code; das Formularfeld `ExpReauthMethod` wich `ExpReauthFactors`. Eine neue Gruppe `delete` (an): Papierkorb leeren, daraus entfernen, mehrere Elemente auf einmal entfernen. Die Geräte eines anderen Benutzers vollständig und der Anmeldebericht fragen vor dem Öffnen. Die Gruppe Inhalte fragt auch beim Speichern eines Entwurfs. Gruppen können `UserGroups[]` nennen; `setup/reauth` bearbeitet jeden Siteaccess oder einen (`setup/reauth/<siteaccess>`). Eine Passkey-Richtlinie (`[PasskeySettings] Policy`, `PasswordSignIn`, je Gruppe und Konto). Administratoren sehen und widerrufen die Passkeys anderer Konten (`user/passkeys/<Benutzer-ID>`, neue Richtlinie `user/passkeys_manage`). Namen der Authentifikatoren; `Attestation`, `RelyingPartyIDMap[]`, `AuthenticatorNames[]`, `ConsoleSlots[]`. Sicherheitsmeldungen, wenn ein Passkey hinzugefügt, entfernt oder widerrufen wird. Vier Audit-Namen: `access.passkey.revoke`, `access.passkey.password_refused`, `access.passkey.required`, `access.passkey.suggested` (der Katalog hat 202). |
| Wen es betrifft | Benutzer mit Passkey: Die Frage verlangt Passwort und Passkey. Designs mit eigenem `user/reauth.tpl` (sie senden `ExpReauthFactors` und durchlaufen `$combinations`; siehe `design/standard/templates/user/reauth.tpl`). Designs mit eigenem `user/passkeys.tpl` (neue Variablen `own`, `account`, `action_uri`, `policy`, `sent_by_policy`, `last_locked`, `console_slots`; Formulare senden an `$action_uri`). |
| Wie prüfen | `/setup/reauth` listet sechs Gruppen und hat **Einstellungen für**; die Passkey-Liste hat eine Spalte **Authentifikator**. |
| Wie beheben | Kernel-Autoloads neu erzeugen (neue Klassen `expPasskeyPolicy`, `expPasskeyPolicyHandler`, `expPasskeyAuthenticators`, `expReauthTriggerHandler`); Caches für INI, Templates und Template-Overrides leeren; PHP-FPM neu laden und Velocity neu starten. |
| Wie abschalten | Der alte einzelne Weg: `Factors[]` mit `password`, `passkey` und `code` je in einer eigenen Zeile. Die Gruppe delete auf `setup/reauth`. Die Richtlinie ist standardmäßig `optional`. |

Für Erweiterungen: `expReauthTriggerHandler` (`[ReauthSettings] TriggerHandlers[]`), `expReauth::requireFresh()`,
`expReauth::markFreshAfterSignIn()` (eine Anmeldung, die nach ebenso viel fragte, zählt als Bestätigung; der zweite
Schritt von `sevenx_authentication_2fa` ruft es auf), `expPasskeyPolicyHandler` (`[PasskeySettings] PolicyHandlers[]`),
`expPasskeyService::revoke()` und `removeChecked()`. Der zweite Schritt von `sevenx_authentication_2fa` bietet neben
dem App-Code einen Passkey an, und ein Konto, das einen zweiten Schritt haben muss und einen Passkey hat, braucht
keine App.
