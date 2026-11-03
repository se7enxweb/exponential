# Benutzerverwaltung von der Konsole

Lesen Sie diese Seite, wenn Sie die Benutzerkonten einer Installation betreuen und das aus einer Shell, einem
Deploy-Skript oder einem CI-Job tun wollen. Exponential 6.0.15 bringt die Befehle `exp:user`: Konten auflisten,
ansehen, anlegen, ändern, deaktivieren, aktivieren und entfernen, Passwörter zurücksetzen, Registrierungen freigeben,
Gruppen, Rollen, Sitzungen und die zweistufige Anmeldung verwalten, importieren, exportieren, viele Konten auf einmal
ändern und das Benutzer-Auditprotokoll lesen. Jeder Befehl tut, was die Administration tut, über dieselben
Operationen und mit denselben Audit-Ereignissen.

## Kurz gefasst

- 24 Befehle unter `exp:user:*`, jeder mit `--help` und der erzeugten
  [Referenz der Konsolenbefehle](../../beispiele/befehle/README.md).
- **Ein Probelauf ohne `--apply`.** Jeder Befehl, der etwas ändert, listet zuerst auf, was er tun würde und welche
  Prüfungen fehlschlagen. `--dry-run` wird angenommen; beide zusammen sind ein Bedienfehler.
- **Kein Passwort auf der Befehlszeile.** Ein Passwort kommt von der Standardeingabe (`--password-stdin`), wird
  erzeugt und einmal ausgegeben, oder die Person wählt es über die Aktivierungs- oder Zurücksetzungsmail. Eine
  Importdatei mit einer Passwortspalte wird verweigert. Keine Ausgabe enthält je einen Passwort-Hash.
- **Wer handelt, wird festgehalten.** Die Befehle handeln als der Benutzer aus `site.ini [UserSettings]
  UserCreatorID` oder als `--as`. Das Audit und das Benutzer-Auditprotokoll nennen diesen Benutzer und den
  Siteaccess.
- **Geschützte Konten** werden nie deaktiviert oder entfernt: der anonyme Benutzer, der Benutzer aus `UserCreatorID`,
  das Administratorkonto (ID 14 oder der Anmeldename `admin`), der handelnde Benutzer und der letzte aktivierte
  Administrator.
- **Dasselbe wie ein Klick.** Konten werden über das Veröffentlichen von Inhalten angelegt und geändert; Workflows und
  die Freigabe neuer Konten laufen also. Das Deaktivieren nutzt `user/setsettings` und beendet jede Sitzung.
- **Für Skripte**: `--format=json` gibt das Schema `exp-user/1` aus; die Exit-Codes sind 0 erledigt, 1
  fehlgeschlagen oder verweigert, 2 Bedienfehler, 3 Datenbank nicht erreichbar, 4 interner Fehler. Optionen und
  Schlüssel sind ein dauerhafter Vertrag.

## Die Befehle

| Befehl | Was er tut | Ändert |
|---|---|---|
| `exp:user:list` | Konten nach Zustand, Gruppe und Suche, seitenweise | nein |
| `exp:user:show <user>` | ein Konto: Gruppen, Rollen, Zustand, Registrierung, Anmeldungen, zweistufige Anmeldung, Sitzungen | nein |
| `exp:user:create` | legt ein Konto über das Veröffentlichen von Inhalten an | ja |
| `exp:user:edit <user>` | E-Mail-Adresse, Anmeldename, Namen, Gruppen, Hauptgruppe | ja |
| `exp:user:disable <user>`, `exp:user:enable <user>` | deaktiviert (und beendet die Sitzungen), aktiviert wieder | ja |
| `exp:user:delete <user>` | entfernt das Konto endgültig | ja |
| `exp:user:reset-password <user>` | der Link zum Zurücksetzen, ein erzeugtes Passwort oder eines von der Standardeingabe | ja |
| `exp:user:require-password-change <user>` | ein neues Passwort bei der nächsten Anmeldung (`--clear` entfernt die Markierung) | ja |
| `exp:user:check-login <user>` | ob ein Passwort von der Standardeingabe das Konto anmeldet; Exit 0 ja, 1 nein | nein |
| `exp:user:pending` | wartende Registrierungen | nein |
| `exp:user:approve <user>` | gibt eine Registrierung frei oder lehnt sie mit `--reject` ab | ja |
| `exp:user:resend-activation <user>` | die Aktivierungsmail erneut, mit einem neuen Schlüssel | ja |
| `exp:user:groups [action] [group]` | listet, legt an, benennt um, verschiebt und löscht Benutzergruppen | ja |
| `exp:user:roles [action] [role] [target]` | listet, weist zu und entfernt Rollen, mit einer Einschränkung auf Teilbaum oder Sektion | ja |
| `exp:user:whoami` | das Konto, als das die Konsole handelt, seine Rollen, ob es alle Rechte hat | nein |
| `exp:user:sessions <user>` | die erfassten Sitzungen; `--end` oder `--session=<id>` beendet sie | ja |
| `exp:user:unlock <user>` | setzt den Zähler fehlgeschlagener Anmeldungen eines gesperrten Kontos zurück | ja |
| `exp:user:2fa <user>` | zweistufige Anmeldung und Passkeys; `--reset`, `--remove-passkeys` | ja |
| `exp:user:export` | die Konten, die die Filter auswählen, als CSV oder JSON, ohne Passwörter | nein |
| `exp:user:import <file>` | Konten aus CSV oder JSON, jedes so angelegt, wie `exp:user:create` es anlegen würde | ja |
| `exp:user:bulk <action>` | viele Konten deaktivieren, aktivieren, entfernen, freigeben, ablehnen oder entsperren, mit `--confirm=<Anzahl>` | ja |
| `exp:user:audit <user>` | die Zeilen des Benutzer-Auditprotokolls über das Konto oder von ihm | nein |
| `exp:user:hygiene` | die Konten je Zustand, jeweils mit dem Befehl `exp:user:list`, der sie auflistet | nein |

`<user>` ist ein Anmeldename, eine E-Mail-Adresse (wenn nur ein Konto sie hat) oder die ID des Kontos. Eine Gruppe ist
eine Knoten-ID oder ein Name; eine Rolle ist ein Name oder eine ID.

## Die Audit-Ereignisse

| Befehl | Ereignisse |
|---|---|
| create, edit | die Ereignisse des Veröffentlichens von Inhalten; `access.user.email.change`, `access.user.login.change` |
| disable, enable, unlock | `access.user.disable`, `access.user.enable`, `access.user.unlock` |
| reset-password | `access.user.password.change`, `access.user.password.reset.request` |
| approve | `access.registration.approve`, `access.registration.reject` |
| roles | `access.role.assign`, `access.role.unassign` |
| sessions | `access.session.revoke` |
| 2fa | `access.user.two_factor.remove`, `access.passkey.*` |

Kein Ereignis enthält je ein Passwort.

## Was bewusst nicht angeboten wird

- **Zwei Konten zusammenführen.** Der Kernel hat keinen sicheren Weg, das Eigentum an Inhalten, Versionen,
  Rollenzuweisungen und den Verlauf von einem Konto auf ein anderes zu übertragen.
- **Passwörter in Importdateien** und **eine Option `--password`**: beide würden Passwörter dort verbreiten, wo sie
  bleiben.
- **Deaktivieren und verschieben.** Die Konsole deaktiviert; sie verschiebt keine Konten in die Gruppe für
  deaktivierte Konten. Nutzen Sie dafür die Administration
  ([Benutzerkonten prüfen](benutzerpflege.md#deaktivieren-und-verschieben)).

## Einstellungen

Die Befehle fügen keine Einstellungen hinzu. Sie lesen:

| Datei | Block | Schlüssel | Wofür |
|---|---|---|---|
| `site.ini` | `[UserSettings]` | `UserCreatorID` | der standardmäßig handelnde Benutzer; geschützt |
| `site.ini` | `[UserSettings]` | `AnonymousUserID` | geschützt |
| `site.ini` | `[UserSettings]` | `UserClassID`, `DefaultUserPlacement` | die Klasse und Gruppe eines neuen Kontos |
| `site.ini` | `[UserSettings]` | `RequireUniqueEmail` | die E-Mail-Prüfung bei create, edit und import |
| `site.ini` | `[UserSettings]` | `MaxNumberOfFailedLogin` | gesperrte Konten |
| `site.ini` | `[UserSettings]` | `ForgotPasswordKeyLifetime` | wie lange der Link zum Zurücksetzen gilt |
| `content.ini` | `[NodeSettings]` | `UserRootNode` | die Spitze des Benutzerbaums für `exp:user:groups` |

## Wie es weitergeht

- Die Anleitung: [Benutzerverwaltung von der Konsole](../../anleitungen/benutzerverwaltung-von-der-konsole.md).
- Das Hilfehandbuch, mit Aufgaben und Fehlerbehebung:
  [Benutzerverwaltung von der Konsole](../../hilfe/benutzerverwaltung-von-der-konsole/README.md).
- Die JSON-Ausgabe und die Exit-Codes: [JSON-Ausgabe von exp:user](../../spezifikationen/6.0/exp-user-json-ausgabe.md).

## Verwandte Seiten

- [Benutzerkonten prüfen](benutzerpflege.md)
- [Benutzer-Auditprotokoll](benutzer-auditprotokoll.md)
- [Ihre Geräte](benutzersitzungen.md)
- [Passwort bei nächster Anmeldung ändern](passwort-bei-naechster-anmeldung-aendern.md)
- [Das Passwort eines Benutzers auf der Kommandozeile zurücksetzen](benutzerpasswort-zuruecksetzen.md)
