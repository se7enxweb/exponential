# Identität bestätigen: vor einer heiklen Aktion zeigen, dass Sie es sind

Diese Anleitung erklärt „Identität bestätigen“ (im Englischen „Verify it's you“) in Exponential 6.0.15. Vor einer
heiklen Aktion, etwa ein Passwort ändern, eine Rolle zuweisen, Caches leeren oder Daten exportieren, bestätigt ein
angemeldeter Benutzer noch einmal, wer er ist: mit seinem Passwort, einem seiner Passkeys oder dem Code seiner
Authenticator-App. Eine Bestätigung gilt danach zehn Minuten. Eine von jemand anderem übernommene Sitzung (ein
unbeaufsichtigter Rechner, ein gestohlenes Cookie) kann dann noch Seiten lesen, aber weder das Konto noch die Site
übernehmen.

[Anleitungen](README.md) · Siehe auch: [Passkeys](passkeys.md) · [Zweistufige Anmeldung](zwei-faktor-anmeldung.md) ·
[Sicherheit und Audit](sicherheit-und-audit.md) · Änderungshinweis:
[Passkeys und „Identität bestätigen“](../bc/6.0/passkeys-und-identitaet-bestaetigen.md)

## Kurz gesagt

- Fünf Gruppen von Aktionen; vier sind von Anfang an eingeschaltet: **das eigene Konto**, **Benutzer, Rollen und
  Richtlinien**, **Systemeinstellungen und Setup-Aktionen**, **Daten exportieren**. Die fünfte, **Inhalte entfernen
  und veröffentlichen**, ist aus und wird unter **Setup > Verify it's you** (`/setup/reauth`) eingeschaltet.
- Statt der Aktion erscheint die Seite „Verify it's you“. Nach einer richtigen Antwort läuft die Aktion genau so, wie
  sie gesendet wurde; die nächsten zehn Minuten (`[ReauthSettings] Minutes`) fragen nichts.
- Fünf falsche Antworten hintereinander melden den Benutzer ab.
- Nur die Webseiten fragen (index.php und Velocity). Die REST-Schnittstelle, SOAP, WebDAV, die
  `expservices`-Anmeldung und die Kommandozeile fragen nie, anonyme Besucher werden nie gefragt.
- Jede Frage, Antwort und Abweisung steht im Audit: `access.session.reauth.ask`, `access.session.reauth`,
  `access.session.reauth.failed`.

## Inhalt

1. [Die Seite](#1-die-seite)
2. [Die Gruppen und ihre Regeln](#2-die-gruppen-und-ihre-regeln)
3. [Gruppen ein- und ausschalten](#3-gruppen-ein-und-ausschalten)
4. [Aufbau einer Regel](#4-aufbau-einer-regel)
5. [Eine Regel für die Ansicht einer Extension](#5-eine-regel-für-die-ansicht-einer-extension)
6. [Was mit der Anfrage geschieht](#6-was-mit-der-anfrage-geschieht)
7. [Wo nie gefragt wird](#7-wo-nie-gefragt-wird)
8. [Codes einer Authenticator-App](#8-codes-einer-authenticator-app)
9. [Audit](#9-audit)
10. [Alle Einstellungen](#10-alle-einstellungen)

---

## 1. Die Seite

Die Seite sagt, worum es bei der Aktion geht („Sie sind dabei, Benutzer, Rollen oder Richtlinien zu verwalten.“), und
bietet die Wege an, die das Konto hat:

- **Mit Ihrem Passwort**: das Feld und **Bestätigen**.
- **Einen Passkey benutzen**: erscheint, wenn das Konto Passkeys hat und der Browser sie benutzen kann; das Gerät
  bestätigt.
- **Mit einem Code Ihrer Authenticator-App**: erscheint, wenn für das Konto eine Authenticator-App in der zweistufigen
  Anmeldung eingerichtet ist.

**Abbrechen** führt ohne die Aktion zur vorigen Seite zurück. Eine falsche Antwort sagt das und wie viele Versuche
bleiben.

## 2. Die Gruppen und ihre Regeln

So, wie sie in `settings/site.ini` ausgeliefert werden:

| Gruppe | An | Regeln |
|---|---|---|
| `account`: die Sicherheit des eigenen Kontos | ja | `user/password self`, `user/passkeys *`, `user2fa/setup`, `apikey/list`, `content/edit self credentials`, `user/sessions self SaveMailButton RevokeOthersButton` |
| `users`: Benutzer, Rollen und Richtlinien, neue Benutzerkonten freigeben | ja | `content/edit other`, `user/password other`, `user/sessions other RevokeSessionButton RevokeOthersButton`, `user/sessionreport RevokeSessionButton ForceSignOutButton`, `user/setting`, `user/unactivated`, `user/hygiene`, `user/deactivate`, `user/registration`, `content/removeuserobject ConfirmButton`, `collaboration/action other CollaborationAction_Accept CollaborationAction_Deny CollaborationAction_Defer`, `role/*`, `oauthadmin/*` |
| `system`: Systemeinstellungen und Setup-Aktionen | ja | `setup/*` außer `setup/info`, `logs`, `logshealth`, `logsdigest`, `logsfeed`, `templatelist`, `templateview`, `menu` und den drei Streams; `settings/edit`, `package/*`, `package/upload *`, `secrets/*`, `update/*`, `git_manager/*` |
| `export`: Daten exportieren | ja | `infocollector/export *`, `audit/export *`, `content/subitemsexport *`, `mailpreferences/export *`, `user/security DownloadButton`, `user/audit ExportButton`, `bccie/export *`, `bccie/doexport *`, `newsletter/subscription_list_csvexport *`, `newsletter/subscriber_export *`, `newsletter/statistics_export *` |
| `content`: Inhalte entfernen und veröffentlichen | nein | `content/removeobject ConfirmButton`, `content/removenode ConfirmButton`, `content/trash EmptyButton RemoveButton`, `content/edit PublishButton PublishNotNotifyButton`, `content/versionview PreviewPublishButton PreviewPublishNotNotifyButton`, `content/multiedit MultiPublishButton` |

Die eigene E-Mail-Adresse zu ändern fragt also (sie ändert Zugangsdaten), den eigenen Namen zu ändern nicht; das Konto
eines anderen Benutzers zu bearbeiten fragt immer. Ein Konto unter einer Benutzergruppe anzulegen gilt als Bearbeiten
des Kontos eines anderen Benutzers: Es ist Benutzerverwaltung, denn Login, E-Mail-Adresse, Passwort und Gruppe eines
Kontos entscheiden, wer sich mit welchen Rechten anmelden kann, gleich ob sich eines seiner Felder ändert. Die Frage
kommt einmal, wenn das Formular veröffentlicht (oder gespeichert) wird, und die Antwort führt das Formular so aus, wie
es gesendet wurde (siehe „Was mit der Anfrage geschieht“).

**Freigeben** und **Ablehnen** eines neuen Benutzerkontos, das auf seine Freigabe wartet (ein Element des
Workflow-Ereignisses *Approve user accounts*, siehe [Neue Redakteurskonten freigeben](neue-redakteurskonten-freigeben.md)),
fragen ebenfalls: Das Element betrifft dieses Konto, also greift die Regel `collaboration/action other ...` der Gruppe
users. Die Entscheidung wird angehalten, bis der Freigebende geantwortet hat, und dann genau so ausgeführt, wie sie
gedrückt wurde. Eine gewöhnliche Inhaltsfreigabe betrifft kein Konto und fragt hier nie, auch nicht, wenn der Inhalt
ein Benutzerobjekt ist; die Gruppe content hat ebenfalls keine Regel für Freigaben, ihr Einschalten ändert für sie also
nichts. Einen Kommentar hinzufügen und das Element öffnen fragen nie.

## 3. Gruppen ein- und ausschalten

**Setup > Verify it's you** (`/setup/reauth`, Richtlinie `setup/setup`) hat den Schalter der ganzen Funktion, die
Minuten, die eine Bestätigung gilt, und einen Schalter je Gruppe. **Speichern** schreibt `Reauth`, `Minutes` und jedes
`[ReauthGroup_<Name>] Enabled` zeilenweise in den globalen Override `settings/override/site.ini.append.php` (der Rest
der Datei bleibt, eine Sicherung kommt nach `var/backup/ini`), die Änderung wird als `system.setting.write`
verzeichnet. Das Speichern ist selbst eine Systemeinstellung und fragt deshalb zuerst.

Die Regeln selbst werden auf der Seite nicht bearbeitet; sie sind Einstellungen von `site.ini` und werden von dem
geändert, der die Site ausliefert.

## 4. Aufbau einer Regel

Eine Regel ist `modul/ansicht [Token ...]`:

| Teil | Bedeutung |
|---|---|
| `modul/ansicht` | die Ansicht; `modul/*` jede Ansicht des Moduls. Ein POST an sie fragt |
| `*` | jede Anfrage fragt, auch GET: eine Seite, die herunterlädt, oder ein Formular mit Datei-Upload |
| `Name` | nur ein POST mit diesem Feld (einem von mehreren Namen), meist die Schaltfläche der Aktion |
| `self`, `other` | nur wenn das Konto, um das es geht, das eigene oder das eines anderen Benutzers ist (`content/edit` eines Benutzerobjekts, `user/password`, `user/setting`, `user/edit`, `user/sessions`; `collaboration/action` betrifft das wartende Konto eines Elements von *Approve user accounts*) |
| `credentials` | nur wenn der POST Login, E-Mail-Adresse oder Passwort eines Benutzers ändert |
| `!modul/ansicht` | eine Ausnahme: die Ansicht fragt in dieser Gruppe nie (gewinnt gegen `modul/*`) |

`user/login` und `user/logout` fragen nie, was eine Regel auch sagt.

## 5. Eine Regel für die Ansicht einer Extension

Die Regeln einer Gruppe in einer INI-Datei der Extension oder des Siteaccess ergänzen, etwa in
`extension/myext/settings/site.ini.append.php`:

```ini
[ReauthGroup_system]
Rules[]=myext/dangerous
Rules[]=myext/download *
```

oder eine eigene Gruppe:

```ini
[ReauthSettings]
Groups[]=myext

[ReauthGroup_myext]
Enabled=true
Rules[]=myext/delete DeleteButton
```

Danach den INI-Cache leeren. Eine eigene Gruppe erscheint mit ihrem Namen auf `setup/reauth`.

## 6. Was mit der Anfrage geschieht

- **Ein POST** wird nicht ausgeführt. Die Seite sendet an dieselbe Adresse zurück, mit jedem Feld der ursprünglichen
  Anfrage als verstecktem Feld; nach einer richtigen Antwort läuft die Ansicht mit genau diesen Feldern.
- **Ein GET** (eine Regel mit `*`) wird nach der Antwort neu angefragt: Der Browser wird auf dieselbe Adresse
  weitergeleitet.
- **Ein Datei-Upload** lässt sich vom Browser nicht zweimal senden: Nach der Antwort öffnet sich das Formular wieder,
  und die Dateien werden noch einmal gewählt. Deshalb fragt die Regel für `package/upload` schon beim Öffnen (`*`).
  Nur eine tatsächlich gewählte Datei zählt: Ein Formular, das nur Dateifelder hat, die leer blieben, ist ein POST wie
  jeder andere. Der übliche Fall ist das Anlegen oder Bearbeiten des Kontos eines anderen Benutzers: Das Formular eines
  Benutzers hat das Bild des Kontos, und wer ein Mitglied anlegt, ohne ein Bild zu wählen, bestätigt einmal, und das
  Konto wird so veröffentlicht, wie es ausgefüllt wurde, ohne das Formular noch einmal auszufüllen. Dass Dateien noch
  einmal gewählt werden müssen, sagt die Seite nur, wenn eine Datei gewählt war.
- **Ein Skript** (XMLHttpRequest oder `Accept: application/json` ohne HTML) erhält statt der Seite `403` mit
  `{"error": "reauth_required", "url": "..."}`.

Die Seite wird nie von einem Browser oder einem gemeinsamen Cache gespeichert.

## 7. Wo nie gefragt wird

Die Prüfung läuft in `ezpKernelWeb`, nachdem die Richtlinien die Ansicht erlaubt haben und bevor sie läuft; deshalb
fragen nur die Webseiten. Die REST-Schnittstelle, SOAP, WebDAV, die `expservices`-Anmeldung und Konsolenbefehle haben
diesen Schritt nicht, und die Regel, dass nur das Anmeldeformular der Webseiten nach einem zweiten Anmeldeschritt
fragt, bleibt, wie sie ist. Anonyme Besucher werden nie gefragt.

## 8. Codes einer Authenticator-App

`[ReauthSettings] CodeHandlers[]` nennt die Klassen, die Codes prüfen (`expReauthCodeHandler`). Das mitgelieferte
`expReauthTotpCode` liest die Authenticator-App von `sevenx_authentication_2fa`, wenn diese Extension aktiv ist: Ein
Code zählt einmal je Zeitschritt (derselbe Schritt gilt weder hier noch bei der Anmeldung noch einmal), und falsche
Codes zählen zur Sperre der Extension. Konten mit E-Mail-Codes erhalten kein Code-Feld; sie benutzen Passwort oder
Passkey.

## 9. Audit

| Ereignis | Wann |
|---|---|
| `access.session.reauth.ask` | die Seite erscheint (after: Gruppe, Ansicht, Methode) |
| `access.session.reauth` | eine richtige Antwort (after: Methode, Gruppe, Ansicht, Minuten) |
| `access.session.reauth.failed` | eine falsche Antwort (Grund: credentials, code, passkey_..., method; nie Passwort oder Code) |

Die erneute Passworteingabe der Audit-Konsole erkennt eine hier gegebene Bestätigung an.

## 10. Alle Einstellungen

`site.ini [ReauthSettings]`:

| Einstellung | Vorgabe | Bedeutung |
|---|---|---|
| `Reauth` | `enabled` | die ganze Funktion |
| `Minutes` | `10` | Minuten, die eine Bestätigung gilt (1 bis 1440) |
| `MaxFailures` | `5` | falsche Antworten hintereinander, bevor der Benutzer abgemeldet wird |
| `Groups[]` | `account`, `users`, `system`, `export`, `content` | die Gruppen, jede ein Block `[ReauthGroup_<Name>]` mit `Enabled` und `Rules[]` |
| `CodeHandlers[]` | `expReauthTotpCode` | Klassen, die Codes prüfen |
