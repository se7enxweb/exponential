# Ihre Geräte und Sitzungen: wo ein Konto angemeldet ist

**Für:** jeden Benutzer, der sich anmeldet, und die Administratoren, die sich um die Konten kümmern. **Zeit:** 10 Minuten.

Jede Anmeldung ist eine eigene Sitzung: ein Browser auf einem Computer, ein Telefon, ein Tablet, ein Programm, das sich
über WebDAV anmeldet. Die Seite **Ihre Geräte** listet sie auf und beendet jede mit einem Klick. Diese Anleitung zeigt
die Seite, die E-Mail, die über eine Anmeldung von einem neuen Gerät informiert, was ein Administrator für einen
Benutzer tun kann und was zu prüfen ist, wenn etwas falsch aussieht. Wie es innen arbeitet:
[Ihre Geräte: angemeldete Sitzungen je Benutzer](../funktionen/6.0/benutzersitzungen.md).

## Kurz gesagt

- **Mein Konto > Ihre Geräte** (`user/sessions`): jedes Gerät, das bei Ihrem Konto angemeldet ist, dieses zuerst.
- **Diese Sitzung abmelden** beendet ein Gerät; **Alle anderen Sitzungen abmelden** beendet alle außer dem, das Sie
  benutzen.
- Ein abgemeldetes Gerät sieht bei seinem nächsten Klick die Anmeldeseite, auch wenn es mit „Angemeldet bleiben“
  angemeldet blieb.
- Eine E-Mail **Neue Anmeldung bei Ihrem Konto** sagt Ihnen, wenn sich Ihr Konto von einem Gerät oder Browser anmeldet,
  das es in letzter Zeit nicht benutzt hat. Sie schalten sie auf derselben Seite ab.
- Administratoren mit der Richtlinie `user/sessions` öffnen **Sitzungen** auf der Kontoseite eines Benutzers und melden
  den Benutzer überall ab.

## 1. Die Seite öffnen

In der Administration: das Benutzermenü oben rechts, **Mein Konto**, dann die Karte **Ihre Geräte**. Auf der
öffentlichen Website: **Mein Profil**, dann **Geräte verwalten**. Die Adresse ist auf beiden `/user/sessions`.

Jede laufende Sitzung ist eine Karte:

- **Der Titel**: Browser und System, zum Beispiel „Firefox 131 on Windows 10/11“ oder „Safari 17 on iOS 17“.
- **Gerät**: Computer, Telefon, Tablet, Programm oder App (ein WebDAV-Laufwerk, ein Skript), automatischer Client.
- **Adresse**: das Netz, in dem das Gerät war, zum Beispiel `203.0.113.x`: genug, um „das war ich zu Hause“ von einem
  Ort zu unterscheiden, an dem Sie nie waren, ohne die ganze Adresse auf dem Bildschirm zu zeigen.
- **Angemeldet**: wann und wie: mit dem Passwort, mit Passwort und Code der Zwei-Schritt-Anmeldung, über WebDAV, SOAP
  oder REST oder per Single Sign-on.
- **Zuletzt aktiv**: der letzte Klick (auf fünf Minuten genau). „Jetzt“ für das Gerät, das Sie gerade benutzen.
- **Bleibt angemeldet**: „Angemeldet bleiben, bis ...“, wenn das Kästchen bei der Anmeldung angekreuzt war (es ist
  standardmäßig angekreuzt und gilt 360 Tage), sonst bis der Browser geschlossen wird.
- **Dieses Gerät** kennzeichnet die Sitzung, mit der Sie schauen.

Darunter zeigt **Kürzlich beendet** die Sitzungen der letzten 90 Tage, die vorbei sind, und wie sie endeten.

## 2. Ein Gerät abmelden

1. Suchen Sie die Karte des Geräts, das nicht angemeldet sein soll.
2. Klicken Sie **Diese Sitzung abmelden**. Die Seite sagt „Die Sitzung wurde abgemeldet.“
3. Das Gerät sieht bei seinem nächsten Klick die Anmeldeseite. „Angemeldet bleiben“ bringt es nicht zurück: Das
   gemerkte Cookie gehört zu der Sitzung, die beendet wurde.

Die Sitzung, die Sie gerade benutzen, hat keinen solchen Knopf: Melden Sie sich stattdessen mit **Abmelden** ab.

**Alle anderen Sitzungen abmelden** (über den Karten, mit einer Rückfrage) beendet jedes Gerät außer diesem. Benutzen
Sie es, nachdem Sie sich an einem fremden Computer angemeldet haben oder wenn ein Telefon verloren ging.

Wenn Sie ein Gerät nicht kennen: Melden Sie es ab und ändern Sie danach Ihr Passwort (Mein Konto > Passwort). Die
Passwortänderung meldet die anderen Sitzungen ebenfalls ab.

## 3. Die E-Mail über eine neue Anmeldung

Meldet sich Ihr Konto von einem Browser, System und einer Geräteart an, die es in den letzten 90 Tagen nicht benutzt
hat, bekommen Sie **Neue Anmeldung bei Ihrem Konto** an Ihre E-Mail-Adresse: das Gerät, die Zeit, die ungefähre Adresse
und den Link zu Ihre Geräte. Waren Sie es, ist nichts zu tun. Wenn nicht, folgen Sie dem Link, melden Sie die Sitzung
ab und ändern Sie Ihr Passwort.

Die allererste Anmeldung eines Kontos schickt nichts. Um die E-Mails abzustellen: Entfernen Sie unter **Hinweise zu
Anmeldungen** auf Ihre Geräte den Haken bei **Mir eine E-Mail schicken, wenn mein Konto sich von einem neuen Gerät
oder Browser anmeldet** und klicken Sie **Speichern**. Die Seite E-Mail-Einstellungen verweist auf dieses Kästchen: Die
E-Mail gehört zur Kontosicherheit und wird deshalb hier geschaltet.

### Wenn Sie es nicht waren: Konto absichern

**Sicherheitsaktivität** (verlinkt von Ihre Geräte und dem Benutzermenü) listet die Anmeldungen und Änderungen Ihres
Kontos. Neben jedem Ereignis der letzten 30 Tage, das Aufmerksamkeit braucht, fragt sie **Waren Sie das?**:

- **Ja, das war ich**: Die Frage verschwindet; das Ereignis bleibt in der Liste.
- **Nein, mein Konto absichern**: öffnet die Karte **Konto absichern** oben auf der Seite.

**Konto absichern** lässt Sie zuerst bestätigen, dass Sie es sind, und meldet dann jedes Gerät außer dem, das Sie gerade
benutzen, ab, auch die mit „Angemeldet bleiben“. Danach sagt die Seite, wie viele abgemeldet wurden, und bietet
**Passwort ändern** an: Wählen Sie sofort ein neues. Die Schaltfläche **Konto absichern** in jeder Sicherheits-E-Mail
führt direkt zu dieser Karte.

## 4. Für Administratoren: die Sitzungen eines Benutzers

Öffnen Sie die Kontoseite des Benutzers (`user/edit/<id>`, in der Benutzerliste **Bearbeiten** oder das Kontosymbol).
Die Karte **Sitzungen** zeigt, wie viele Sitzungen angemeldet sind; **Sitzungen anzeigen** öffnet
`user/sessions/<id>`:

- dieselben Karten, mit der vollständigen Adresse neben dem Netz;
- **Diese Sitzung abmelden** für ein Gerät, **Überall abmelden** für alle (wenn ein Konto kompromittiert ist, nachdem
  eine Rolle entzogen wurde, bevor ein Konto deaktiviert wird);
- die eigene Wahl des Benutzers zur E-Mail wird hier weder gezeigt noch geändert.

Karte und Seite brauchen die Richtlinienfunktion **user/sessions** (Rollen und Richtlinien > eine Rolle > Neue
Richtlinie > Modul user, Funktion sessions). Ohne sie antwortet die Seite „Zugriff verweigert“.

Jedes Abmelden auf diesen Seiten steht im Audit-Protokoll als `access.session.revoke`, mit wer es tat; eine Anmeldung
von einem neuen Gerät als `access.session.new_device` (Setup > Audit, nach Namen filtern).

Auf Wunsch bekommen die Administratoren einen Hinweis zu jeder Anmeldung von einem neuen Gerät:

```ini
# settings/override/site.ini.append.php
[UserSessionSettings]
NewDeviceAdminMail=enabled
AdminMailReceivers[]
AdminMailReceivers[]=security@example.com
```

## 5. Für den Betreiber: die Einrichtung

Neue Installationen von 6.0.15 haben die Tabelle `expuser_session`. Führen Sie nach einem Upgrade das Datenbank-Update
von 6.0.15 für Ihre Engine aus, oder auf jeder Engine:

```bash
php update/common/scripts/6.0/createusersessiontable.php --dry-run   # sagt, ob sie fehlt
php update/common/scripts/6.0/createusersessiontable.php
```

Bis dahin sagt die Seite, dass die Tabelle fehlt, und jede andere Seite arbeitet wie bisher. Sitzungen, die schon
angemeldet waren, erscheinen bei ihrem nächsten Klick, als „angemeldet, bevor Sitzungen aufgezeichnet wurden“.

Der Cronjob `usersessions.php` läuft im Teil `infrequent` (oder `php runcronjobs.php usersessions`): Er beendet die
Sitzungen, die `[Session] SessionTimeout` lang nicht benutzt wurden, und entfernt beendete Sitzungen nach
`RetentionDays`. Die Einstellungen stehen in `site.ini [UserSessionSettings]`; siehe die
[Funktionsseite](../funktionen/6.0/benutzersitzungen.md#einstellungen-siteini-usersessionsettings).

## 6. Probleme

| Was Sie sehen | Warum, und was zu tun ist |
|---|---|
| Die Seite sagt „Die Tabelle expuser_session fehlt“ | Das Datenbank-Update wurde nicht ausgeführt: siehe Abschnitt 5 |
| Ein Gerät zeigt „Unbekanntes Gerät“ | Sein Browser schickt keine übliche Kennung (ein Datenschutzwerkzeug, ein altes Programm); öffnen Sie auf der Karte **Kennung des Browsers**, um zu sehen, was es schickte |
| Die Adresse ist für jedes Gerät dieselbe | Die Website steht hinter einem Proxy, der die Adresse des Besuchers nicht weitergibt: `site.ini [HTTPHeaderSettings] ClientIpByCustomHTTPHeader` und `TrustedProxies[]` sowie der Header, den der Proxy setzt |
| Das abgemeldete Gerät ist noch angemeldet | Es hat noch keine Anfrage gestellt: Der nächste Klick zeigt die Anmeldeseite. Ein Gerät, das nur eine Seite offen hält, fragt den Server nicht |
| Keine E-Mail nach einer Anmeldung von einem neuen Telefon | Das Kästchen auf Ihre Geräte ist leer, oder `NewDeviceMail=disabled`, oder es war die erste Anmeldung des Kontos, oder derselbe Browser und dasselbe System wurden innerhalb von 90 Tagen benutzt |
| Ein Programm (WebDAV, REST mit einem App-Passwort) steht nicht in der Liste | Anfragen, die sich nur für sich selbst anmelden, ohne Sitzung, sind keine Sitzungen; widerrufen Sie stattdessen das App-Passwort oder den API-Schlüssel |

## Verweise

- [Ihre Geräte: angemeldete Sitzungen je Benutzer](../funktionen/6.0/benutzersitzungen.md) (die Funktion, die Einstellungen, die Tabelle)
- [Sitzungen](sitzungen.md) (der Session-Handler und die Sitzungsliste der Administration)
- [Zweistufige Anmeldung](zwei-faktor-anmeldung.md)
- [Sicherheit und Audit](sicherheit-und-audit.md)
