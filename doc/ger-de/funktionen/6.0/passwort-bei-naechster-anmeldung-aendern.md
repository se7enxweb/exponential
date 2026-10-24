# Passwort bei der nächsten Anmeldung ändern

Ein Administrator kann ein Benutzerkonto so markieren, dass der Benutzer nach seiner nächsten Anmeldung ein neues
Passwort setzen muss, bevor er irgendetwas anderes tun kann. Der übliche Fall: Sie legen für einen Redakteur ein Konto
mit einem vorläufigen Passwort an, geben ihm das Passwort über einen zweiten Weg (einen Anruf, einen Brief, einen
anderen Chat als die E-Mail mit dem Anmeldenamen), und der Redakteur ersetzt es durch ein Passwort, das nur er kennt.

## Kurz gesagt

- Das Bearbeitungsformular eines Benutzerkontos hat ein Kontrollkästchen **Passwort bei der nächsten Anmeldung
  ändern**. Es ist vorausgewählt, wenn ein Administrator ein Konto für jemand anderen anlegt. Bei einem bestehenden
  Konto zeigt es, ob das Konto markiert ist; abwählen hebt die Markierung auf.
- Nach der nächsten Anmeldung wird der Benutzer statt zur angefragten Seite zum Formular für die Passwortänderung
  (`user/password`) geschickt, mit dem Hinweis „Wählen Sie ein neues Passwort, bevor Sie fortfahren: Ein Administrator
  hat Sie gebeten, Ihr jetziges Passwort zu ersetzen.“ Jede andere Seite schickt ihn dorthin zurück, bis das Passwort
  geändert ist. Das gilt in jedem Siteaccess: in der Administration (admin, admin4, admin4l), im Editor und auf der
  öffentlichen Website.
- Die Passwortänderung hebt die Markierung auf. Die nächste Anmeldung verläuft normal.
- Abmelden (`user/logout`) bleibt möglich.
- Die zweistufige Anmeldung kommt zuerst. Ist sie vorgeschrieben, richtet der Benutzer sie wie gewohnt ein oder gibt
  den Code ein und wird danach zur Passwortänderung geschickt.
- Die REST-Schnittstelle antwortet mit `403` und dem Fehler `password_change_required`, bis das Passwort geändert ist.
  SOAP weist die Anmeldung ab. Konsolenbefehle sind nicht betroffen.
- Im Demo-Modus (`EXP_DEMO_MODE=1`) können Administratorpasswörter nicht geändert werden, deshalb wird die Markierung
  bei Administratoren ignoriert. Sonst würde sie sie aussperren. Andere Konten werden weiterhin zur Passwortänderung
  geschickt.
- Das Setzen und Aufheben der Markierung und jede Weiterleitung werden im Prüfprotokoll aufgezeichnet.

## Die Markierung setzen

### In der Administration

Öffnen Sie den Benutzer in der Administration und bearbeiten Sie ihn, oder legen Sie einen neuen Benutzer an. Unter
den Kontofeldern (Benutzername, Passwort, E-Mail) steht das Kontrollkästchen **Passwort bei der nächsten Anmeldung
ändern**:

- **Neues Konto**: vorausgewählt. Lassen Sie es ausgewählt, wenn Sie der Person ein vorläufiges Passwort geben.
- **Bestehendes Konto**: ausgewählt, wenn das Konto markiert ist. Auswählen markiert das Konto, abwählen hebt die
  Markierung auf.

Die Markierung gilt ab dem Veröffentlichen. Das Kontrollkästchen erscheint nicht beim eigenen Konto (das eigene
Passwort ändern Sie unter `user/password`) und nicht im öffentlichen Registrierungsformular.

Die Markierung gilt bei der **nächsten** Anmeldung des Benutzers. Ist der Benutzer gerade angemeldet, wird er nach
seiner nächsten Anmeldung zur Passwortänderung geschickt.

### Auf der Konsole

```bash
./console exp:user-password-change-required status --login=editor1
./console exp:user-password-change-required set --login=editor1
./console exp:user-password-change-required clear --user-id=14
./console exp:user-password-change-required status --login=editor1 --json
```

`status` zeigt, ob das Konto markiert ist und ob die Markierung bei der Anmeldung durchgesetzt wird (bei einem
Administrator im Demo-Modus nicht). Der Exit-Code ist 0 bei Erfolg, 1 bei einem unbekannten Benutzer und 2 bei einem
Bedienfehler.

## Was der Benutzer sieht

1. Der Benutzer meldet sich mit dem vorläufigen Passwort an (und mit dem zweiten Schritt, wenn die zweistufige
   Anmeldung eingeschaltet ist).
2. Der Browser geht zu `user/password`. Der Hinweis über dem Formular nennt den Grund.
3. Der Benutzer gibt das vorläufige Passwort als aktuelles Passwort ein und wählt ein neues. Die Passwortregeln der
   Website gelten wie immer ([Ihr Passwort ändern](moderne-passwortaenderung.md)); mit `[PasswordSettings]
   ForbidCurrentPassword=enabled` darf das neue Passwort nicht das vorläufige sein.
4. Nach der Änderung bestätigt die Seite sie, und jede Seite öffnet sich wieder wie gewohnt.

Bis dahin antwortet jede andere Adresse (ein Lesezeichen, das Dashboard, eine Inhaltsseite, ein Ajax-Aufruf) mit einer
Weiterleitung zu `user/password`. Die Antwort wird mit `Cache-Control: private, no-store` gesendet, kein Cache behält
sie.

## Einstellungen

`site.ini [PasswordChangeRequiredSettings]`:

```ini
[PasswordChangeRequiredSettings]
# Die Ansichten, die für ein markiertes Konto offen bleiben: Modul/Ansicht, oder ein Modul allein für alle seine Ansichten
ExemptViews[]
ExemptViews[]=user/password
ExemptViews[]=user/logout
ExemptViews[]=user2fa
```

`user/password` ist immer offen, was auch in der Liste steht. `user2fa` ist die zweistufige Anmeldung der Extension
`sevenx_authentication_2fa`. Eine Extension mit Ansichten, die ein markierter Benutzer erreichen muss, ergänzt sie in
ihrer eigenen `site.ini.append.php`.

## Wo die Markierung gespeichert ist

Die Markierung ist eine Benutzereinstellung, `exp_password_change_required`, in der Tabelle `ezpreferences`. Ihr Wert
ist der Zeitpunkt, zu dem sie gesetzt wurde; ein leerer Wert bedeutet keine Markierung. Es wurde keine Tabelle und
keine Spalte hinzugefügt, die Funktion arbeitet also mit jeder Datenbank ohne Update-Skript. Die Seite
`user/preferences` schreibt diese Einstellung nicht, ein Benutzer kann sie also nicht über die Einstellungslinks setzen
oder aufheben.

## REST und SOAP

Eine REST-Anfrage mit den Zugangsdaten eines markierten Kontos (Basic-Authentifizierung, ein OAuth-Token oder ein
persönlicher API-Schlüssel) erhält:

```http
HTTP/1.1 403 Forbidden
Content-Type: application/json; charset=UTF-8

{"error":"password_change_required","error_description":"This account must set a new password before it can use the REST interface. Sign in to the site and change the password (user/password)."}
```

Die Anfrage wird nicht angemeldet. Der Benutzer ändert das Passwort im Browser; danach funktioniert die nächste
REST-Anfrage. SOAP weist die Anmeldung eines markierten Kontos ebenso ab.

## Prüfprotokoll

| Ereignis | Wann | Angaben |
|---|---|---|
| `access.user.password.change_required.set` | ein Konto markiert wird | `after.source`: `edit` oder `console` |
| `access.user.password.change_required.clear` | die Markierung aufgehoben wird | `after.source`: `password` (der Benutzer hat es geändert), `edit` oder `console` |
| `access.user.password.change_required.redirect` | ein markiertes Konto zu `user/password` geschickt oder seine Anmeldung über REST oder SOAP abgewiesen wird | `after.channel`: `web`, `rest` oder `soap`; `after.view` im Web |

Alle drei liegen im Kanal `access` und sind standardmäßig eingeschaltet. Siehe [Prüfprotokoll](pruefprotokoll.md) und
den [Ereigniskatalog](../../bc/6.0/audit.md).

## Für Entwickler

- `expPasswordChangeRequired` (`kernel/classes/user/exppasswordchangerequired.php`): `isMarked()`, `isRequired()` (die
  Markierung, außer bei einem Administrator im Demo-Modus), `set()`, `clear()`, `exemptViews()`, `isExempt()`.
- Die Prüfung läuft in `ezpKernelWeb::dispatchLoop()` vor jeder Ansicht, nach der Rechteprüfung und dem
  Audit-Wächter und vor den Anfrageregeln.
- Die Template-Attribute `password_change_required` (das Konto ist markiert) und `password_change_checked` (das
  Formular wählt das Kästchen aus) eines Benutzerkontos. Ein Design mit eigener `content/datatype/edit/ezuser.tpl`
  ergänzt das Kontrollkästchen wie `design/standard`: das versteckte Feld
  `<base>_data_user_password_change_shown_<id>` mit dem Wert `1` und das Kontrollkästchen
  `<base>_data_user_password_change_required_<id>` mit dem Wert `1`. Ohne das versteckte Feld ändert sich nichts.
- Ein Design mit eigener `user/password.tpl` zeigt den Hinweis, wenn die Template-Variable `password_change_required`
  wahr ist.

Siehe auch: [Hinweise zum Update](../../bc/6.0/passwort-bei-naechster-anmeldung-aendern.md),
[Ihr Passwort ändern](moderne-passwortaenderung.md), [Demo-Modus](../../bc/6.0/demomodus.md).
