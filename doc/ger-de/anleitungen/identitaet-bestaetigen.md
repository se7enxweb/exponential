# Identität bestätigen: vor einer heiklen Aktion zeigen, dass Sie es sind

Diese Anleitung erklärt „Identität bestätigen“ (im Englischen „Verify it's you“) in Exponential 6.0.15. Vor einer
heiklen Aktion, etwa ein Passwort ändern, einen Passkey hinzufügen, eine Rolle zuweisen, Caches leeren, Daten
exportieren oder den Papierkorb leeren, bestätigt ein angemeldeter Benutzer noch einmal, wer er ist: mit seinem
Passwort **und** einem seiner Passkeys, oder ohne Passkey mit seinem Passwort und dem Code seiner Authenticator-App.
Eine Bestätigung gilt danach zehn Minuten. Eine von jemand anderem übernommene Sitzung (ein unbeaufsichtigter Rechner,
ein gestohlenes Cookie) kann dann noch Seiten lesen, aber weder das Konto noch die Site übernehmen.

[Anleitungen](README.md) · Siehe auch: [Passkeys](passkeys.md) · [Zweistufige Anmeldung](zwei-faktor-anmeldung.md) ·
[Sicherheit und Audit](sicherheit-und-audit.md) · Änderungshinweis:
[Passkeys und „Identität bestätigen“](../bc/6.0/passkeys-und-identitaet-bestaetigen.md)

## Kurz gesagt

- Sechs Gruppen von Aktionen; fünf sind von Anfang an eingeschaltet: **das eigene Konto**, **Benutzer, Rollen und
  Richtlinien**, **Systemeinstellungen und Setup-Aktionen**, **Daten exportieren**, **Daten löschen**. Die sechste,
  **Inhalte entfernen, veröffentlichen und speichern**, ist aus und wird unter **Setup > Verify it's you**
  (`/setup/reauth`) eingeschaltet, für jeden Siteaccess oder einen, und für jeden Benutzer oder die Mitglieder
  bestimmter Benutzergruppen.
- Die Wege der Bestätigung folgen `[ReauthSettings] Factors[]`: Standard sind Passwort und Passkey; ohne Passkey
  Passwort und App-Code; ohne beides das Passwort allein; ein Konto ohne Passwort benutzt einen Passkey. Ein Weg
  gleicher Stärke wird neben dem ersten angeboten, nie ein schwächerer.
- Statt der Aktion erscheint die Seite. Nach einer richtigen Antwort läuft die Aktion genau so, wie sie gesendet wurde;
  die nächsten zehn Minuten (`[ReauthSettings] Minutes`) fragen nichts. Eine Anmeldung, die nach ebenso viel fragte,
  zählt als Bestätigung.
- Fünf falsche Antworten hintereinander melden den Benutzer ab.
- Nur die Webseiten fragen (index.php und Velocity). Die REST-Schnittstelle, SOAP, WebDAV, die
  `expservices`-Anmeldung und die Kommandozeile fragen nie, anonyme Besucher werden nie gefragt.
- Jede Frage, Antwort und Abweisung steht im Audit: `access.session.reauth.ask`, `access.session.reauth`,
  `access.session.reauth.failed`.

## Inhalt

1. [Die Seite](#1-die-seite)
2. [Die Wege der Bestätigung](#2-die-wege-der-bestätigung)
3. [Die Gruppen und ihre Regeln](#3-die-gruppen-und-ihre-regeln)
4. [Setup > Verify it's you](#4-setup--verify-its-you)
5. [Aufbau einer Regel](#5-aufbau-einer-regel)
6. [Für Erweiterungen](#6-für-erweiterungen)
7. [Was mit der Anfrage geschieht](#7-was-mit-der-anfrage-geschieht)
8. [Wo nie gefragt wird](#8-wo-nie-gefragt-wird)
9. [Audit](#9-audit)
10. [Alle Einstellungen](#10-alle-einstellungen)

---

## 1. Die Seite

Die Seite sagt, worum es bei der Aktion geht („Sie sind dabei, Benutzer, Rollen oder Richtlinien zu verwalten.“), und
zeigt je Weg der Bestätigung, den das Konto hat, eine Karte, die erste ist die zu benutzende:

- **Ihr Passwort und ein Passkey**: Passwort eingeben und **Mit einem Passkey weiter** wählen; der Browser fragt dann
  nach einem Ihrer Passkeys (Fingerabdruck, Gesicht, Bildschirmsperre oder Sicherheitsschlüssel).
- **Oder: Ihr Passwort und ein Code Ihrer Authenticator-App**: daneben angeboten, wenn das Konto auch eine App hat,
  für einen Browser ohne Passkeys.
- **Mit Ihrem Passwort**: ein Konto ohne Passkey und ohne App.
- **Mit einem Passkey**: ein Konto ohne Passwort.

**Abbrechen** führt ohne die Aktion zur vorigen Seite zurück. Eine falsche Antwort sagt das und wie viele Versuche
bleiben. In einem Browser ohne Passkeys ist die Schaltfläche der Passkey-Karte gesperrt und verweist auf einen anderen
Weg.

## 2. Die Wege der Bestätigung

`[ReauthSettings] Factors[]` nennt Kombinationen aus `password`, `passkey` und `code` (die Authenticator-App, siehe
`CodeHandlers[]`), die stärkste zuerst:

```ini
[ReauthSettings]
Factors[]
Factors[]=password+passkey
Factors[]=password+code
Factors[]=password
Factors[]=passkey
```

Gefragt wird die erste Kombination, deren Wege das Konto alle hat; spätere mit ebenso vielen Wegen werden daneben
angeboten. Ein Konto mit Passwort, Passkey und App bekommt also „Passwort und Passkey“ mit „Passwort und Code“
daneben, nie „Passwort“ allein: Ein gestohlenes Passwort kann den zweiten Schritt nicht umgehen. Passwort und Code
werden vor dem Passkey geprüft. Konten mit E-Mail-Codes der zweistufigen Anmeldung haben keinen `code`: Sie benutzen
Passwort und Passkey oder das Passwort.

**Eine Anmeldung zählt.** Eine Anmeldung auf den Webseiten, die nach einer Kombination fragte, die dem Konto angeboten
würde, zählt als Bestätigung: das Passwort eines Kontos, das nichts weiter hat, Passwort und App-Code der zweistufigen
Anmeldung, Passwort und Passkey als zweiter Schritt, ein Passkey eines Kontos ohne Passwort. Das Passwort allein eines
Kontos mit Passkey zählt nicht.

## 3. Die Gruppen und ihre Regeln

So, wie sie in `settings/site.ini` ausgeliefert werden:

| Gruppe | An | Regeln |
|---|---|---|
| `account`: die Sicherheit des eigenen Kontos | ja | `user/password self`, `user/passkeys *`, `user2fa/setup` (zweistufige Anmeldung einrichten und entfernen, neue Wiederherstellungscodes, App-Passwörter), `apikey/list`, `content/edit self credentials`, `user/sessions self SaveMailButton RevokeOthersButton` |
| `users`: Benutzer, Rollen und Richtlinien, neue Benutzerkonten freigeben | ja | `content/edit other`, `user/password other`, `user/sessions other *` (die Geräte eines anderen Benutzers vollständig), `user/sessionreport *`, `user/setting`, `user/unactivated`, `user/hygiene`, `user/deactivate`, `user/registration`, `content/removeuserobject ConfirmButton`, `collaboration/action other CollaborationAction_Accept CollaborationAction_Deny CollaborationAction_Defer`, `role/*`, `oauthadmin/*` |
| `system`: Systemeinstellungen und Setup-Aktionen | ja | `setup/*` außer `setup/info`, `logs`, `logshealth`, `logsdigest`, `logsfeed`, `templatelist`, `templateview`, `menu` und den drei Streams; `settings/edit`, `package/*`, `package/upload *`, `secrets/*`, `update/*`, `git_manager/*` |
| `export`: Daten exportieren | ja | `infocollector/export *`, `audit/export *`, `content/subitemsexport *`, `mailpreferences/export *`, `user/security DownloadButton`, `user/audit ExportButton`, `bccie/export *`, `bccie/doexport *`, `newsletter/subscription_list_csvexport *`, `newsletter/subscriber_export *`, `newsletter/statistics_export *` |
| `delete`: Daten löschen | ja | `content/trash EmptyButton RemoveButton`, `content/removeobject * bulk` (zwei oder mehr Elemente auf einmal entfernen, `BulkCount`) |
| `content`: Inhalte entfernen, veröffentlichen und speichern | nein | `content/removeobject ConfirmButton`, `content/removenode ConfirmButton`, `content/edit PublishButton PublishNotNotifyButton StoreButton StoreExitButton`, `content/versionview PreviewPublishButton PreviewPublishNotNotifyButton`, `content/multiedit MultiPublishButton` |

Die eigene E-Mail-Adresse zu ändern fragt also (es ändert Zugangsdaten), den eigenen Namen zu ändern nicht; das Konto
eines anderen Benutzers zu bearbeiten fragt immer. Ein einzelnes anderes eigenes Gerät abzumelden wartet auf keine
Frage (wer ein fremdes Gerät sieht, handelt sofort); alle abzumelden fragt. Die Sitzungsliste leert `setup/session`
(Gruppe system); das Audit-Protokoll hat keine Seite, die es leert.

**Freigeben** und **Ablehnen** eines neuen Benutzerkontos, das auf Freigabe wartet (ein Element des Workflow-Ereignisses
*Benutzerkonten freigeben*, siehe [Neue Redakteurskonten freigeben](neue-redakteurskonten-freigeben.md)), fragen
ebenfalls: Das Element betrifft dieses Konto, also greift die Regel `collaboration/action other ...` der Gruppe
Benutzer. Eine gewöhnliche Inhaltsfreigabe betrifft kein Konto und fragt hier nie.

**Die Gruppe Inhalte** fragt, wenn das Bearbeitungsformular mit Veröffentlichen, Entwurf speichern oder Speichern und
beenden gesendet oder ein Entfernen bestätigt wird, bevor die Seite zum Inhalt weitergeht: Eine gekaperte Sitzung oder
ein Skript kann nichts veröffentlichen oder löschen. Das Zeitfenster gilt, ein Redakteur bestätigt also einmal und
speichert dann zehn Minuten ohne Frage.

## 4. Setup > Verify it's you

**Setup > Verify it's you** (`/setup/reauth`, Richtlinie `setup/setup`) hat den Schalter der ganzen Funktion, die
Minuten, die eine Bestätigung gilt, einen Schalter je Gruppe und für die Gruppe Inhalte die Benutzergruppen, die sie
fragt (keine angehakt: jeder Benutzer). **Einstellungen für** wählt, wo die Werte gelten:

- **Jeder Siteaccess**: geschrieben in den globalen Override `settings/override/site.ini.append.php`.
- **Siteaccess <Name>** (`/setup/reauth/<Name>`): geschrieben in `settings/siteaccess/<Name>/site.ini.append.php`,
  etwa um nur auf dem Siteaccess der Redakteure zu fragen. Ein Wert in `settings/override` hat Vorrang.

Werte werden Zeile für Zeile geschrieben (der Rest der Datei bleibt, wie er ist, eine Sicherung kommt nach
`var/backup/ini`), und die Änderung wird als `system.setting.write` verzeichnet. Das Speichern ist selbst eine
Systemeinstellung und fragt daher zuerst. Die Regeln selbst werden auf der Seite nicht bearbeitet; sie sind
Einstellungen der `site.ini`, geändert von dem, der die Site ausliefert.

## 5. Aufbau einer Regel

Eine Regel ist `modul/ansicht [token ...]`:

| Teil | Bedeutung |
|---|---|
| `modul/ansicht` | die Ansicht; `modul/*` jede Ansicht des Moduls. Ein POST an sie fragt |
| `*` | jede Anfrage fragt, auch GET: eine Seite, die etwas herunterlädt oder vollständig zeigt, oder ein Formular mit Datei-Upload |
| `Name` | nur ein POST, der dieses Feld trägt (einen von mehreren Namen), meist die Schaltfläche der Aktion |
| `self`, `other` | nur wenn das Konto, das die Anfrage betrifft, das eigene oder das eines anderen Benutzers ist (`content/edit` eines Benutzerobjekts, `user/password`, `user/setting`, `user/edit`, `user/sessions`; `collaboration/action` betrifft das wartende Konto eines Elements von *Benutzerkonten freigeben*) |
| `credentials` | nur wenn der POST Login, E-Mail-Adresse oder Passwort eines Benutzers ändert |
| `bulk` | nur wenn mehrere Elemente auf einmal entfernt werden (`[ReauthSettings] BulkCount`, Standard 2) |
| `!modul/ansicht` | eine Ausnahme: Die Ansicht fragt in dieser Gruppe nie (geht vor `modul/*`) |

Eine Gruppe kann auch `UserGroups[]` nennen (Content-Objekt-IDs von Benutzergruppen): Sie fragt dann nur deren
Mitglieder. `user/login` und `user/logout` fragen nie, was auch eine Regel sagt.

## 6. Für Erweiterungen

**Eine Regel.** Ergänzen Sie die Regeln der Gruppe in einer INI-Datei der Erweiterung oder des Siteaccess, etwa in
`extension/myext/settings/site.ini.append.php`:

```ini
[ReauthGroup_system]
Rules[]=myext/dangerous
Rules[]=myext/download *
```

oder eine eigene Gruppe, die auf `setup/reauth` mit ihrem Namen erscheint:

```ini
[ReauthSettings]
Groups[]=myext

[ReauthGroup_myext]
Enabled=true
Rules[]=myext/delete DeleteButton
```

**Ein Trigger-Handler** entscheidet, wo eine Regel es nicht kann, aus den eigenen Daten der Erweiterung. Eine Klasse,
die `expReauthTriggerHandler` umsetzt und in `[ReauthSettings] TriggerHandlers[]` steht, bekommt für jede Anfrage, auf
die keine Regel passte, `reauthGroup( $module, $view, $method, $post, $params, $user )` und nennt eine Gruppe oder
null. Gefragt wird nur, solange diese Gruppe eingeschaltet ist und für den Benutzer gilt.

**Aus einer Ansicht.** `expReauth::requireFresh( $Params['Module'], 'meineansicht', 'system' )` gibt null zurück,
wenn der Benutzer innerhalb des Zeitfensters bestätigt hat, sonst das Modulergebnis, das stattdessen zurückzugeben ist
(die Seite, die die Anfrage nach der Antwort erneut sendet):

```php
if ( ( $result = expReauth::requireFresh( $Params['Module'], 'wipe', 'delete' ) ) !== null )
    return $result;
```

**Codes.** `[ReauthSettings] CodeHandlers[]` nennt die Klassen, die Codes prüfen (`expReauthCodeHandler`). Das
mitgelieferte `expReauthTotpCode` liest die Authenticator-App von `sevenx_authentication_2fa`, wenn diese Erweiterung
aktiv ist: Ein Code gilt einmal je Zeitschritt, und falsche Codes zählen auf die Sperre der Erweiterung.

Leeren Sie nach Änderungen an den Einstellungen den INI-Cache.

## 7. Was mit der Anfrage geschieht

- **Ein POST** läuft nicht. Die Seite sendet an dieselbe Adresse zurück, mit jedem Feld der ursprünglichen Anfrage als
  verstecktem Feld; nach einer richtigen Antwort läuft die Ansicht mit genau diesen Feldern.
- **Ein GET** (eine `*`-Regel) wird nach der Antwort neu angefragt: Der Browser wird zur selben Adresse umgeleitet.
- **Ein Datei-Upload** lässt sich vom Browser nicht zweimal senden: Nach der Antwort öffnet sich das Formular erneut,
  und die Dateien werden noch einmal gewählt. Nur eine tatsächlich gewählte Datei zählt: Ein Formular, das nur leere
  Dateifelder hat, ist ein POST wie jeder andere.
- **Ein Skript** (XMLHttpRequest, oder `Accept: application/json` ohne HTML) bekommt `403` mit
  `{"error": "reauth_required", "url": "..."}` statt der Seite.

Die Seite wird nie von einem Browser oder einem gemeinsamen Cache gespeichert.

## 8. Wo nie gefragt wird

Die Prüfung läuft in `ezpKernelWeb`, nachdem die Richtlinien die Ansicht erlaubt haben und bevor sie läuft, also
fragen nur die Webseiten. Die REST-Schnittstelle, SOAP, WebDAV, die `expservices`-Anmeldung und Konsolenbefehle haben
keinen solchen Schritt. Anonyme Besucher werden nie gefragt.

## 9. Audit

| Ereignis | Wann |
|---|---|
| `access.session.reauth.ask` | die Seite wird gezeigt (nachher: Gruppe, Ansicht, Methode) |
| `access.session.reauth` | eine richtige Antwort (nachher: Weg wie `password+passkey`, Gruppe, Ansicht, Minuten) |
| `access.session.reauth.failed` | eine falsche Antwort (Grund: credentials, code, passkey_..., method; nie das Passwort oder der Code) |

Ein hier benutzter Passkey wird auch als `access.passkey.use` mit dem Zweck `reauth` verzeichnet. Die
Passwortabfrage der Audit-Konsole erkennt eine hier gegebene Bestätigung an.

## 10. Alle Einstellungen

`site.ini [ReauthSettings]`:

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `Reauth` | `enabled` | die ganze Funktion |
| `Minutes` | `10` | Minuten, die eine Bestätigung gilt (1 bis 1440) |
| `MaxFailures` | `5` | falsche Antworten hintereinander, bevor der Benutzer abgemeldet wird |
| `Factors[]` | `password+passkey`, `password+code`, `password`, `passkey` | die Wege der Bestätigung, der stärkste zuerst (Abschnitt 2) |
| `Groups[]` | `account`, `users`, `system`, `export`, `delete`, `content` | die Gruppen, jede ein Block `[ReauthGroup_<Name>]` mit `Enabled`, `Rules[]` und wahlweise `UserGroups[]` |
| `CodeHandlers[]` | `expReauthTotpCode` | Klassen, die Codes prüfen |
| `TriggerHandlers[]` | leer | Klassen von Erweiterungen, die für eine Anfrage eine Gruppe nennen (Abschnitt 6) |
| `BulkCount` | `2` | die Zahl der Elemente, ab der ein Entfernen als `bulk` gilt |
