---
title: "Lektion 2.3: Passwörter, Zurücksetzungen und Sperren"
audience: trainer, admin
category: training
order: 23
summary: Die Teilnehmenden ändern ihr eigenes Passwort, setzen das Passwort eines anderen Benutzers, setzen ein Passwort auf der Kommandozeile zurück und sperren und entsperren auf ihrer Schulungsinstallation ein Konto nach fehlgeschlagenen Anmeldungen. 45 Minuten.
keywords: website-administration, passwort, passwort ändern, passwort vergessen, resetuserpassword, exp:resetuserpassword, fehlgeschlagene anmeldungen, MaxNumberOfFailedLogin, gesperrtes konto, zurücksetzen, sitzungen, site administration, password, change password, forgot password, failed logins, locked account, reset, sessions
type: help
---
# Lektion 2.3: Passwörter, Zurücksetzungen und Sperren

„Ich kann mich nicht anmelden“ ist der Support-Anruf, den ein Administrator am häufigsten bekommt. Diese Lektion geht
jeden unterstützten Weg durch, ein Passwort zu ändern oder zurückzusetzen, zeigt, was danach mit den Sitzungen des
Benutzers geschieht, und wie Sie ein Konto entsperren, das nach zu vielen fehlgeschlagenen Anmeldungen gesperrt wurde.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 2: Benutzer und Gruppen](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren und Support-Mitarbeiter |
| Voraussetzungen | [Lektion 2.2](benutzerkonten-anlegen-und-verwalten.md): die Benutzer `tess.trainee` und `sam.sample`; ein Terminal im Wurzelverzeichnis der Installation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- ihr eigenes Passwort unter `user/password` ändern;
- im Bearbeitungsformular eines anderen Benutzers ein neues Passwort für ihn setzen;
- erklären, wie **Passwort vergessen?** funktioniert und wann es nicht helfen kann;
- ein Passwort auf der Kommandozeile mit `resetuserpassword.php` zurücksetzen;
- ein Konto entsperren, das durch fehlgeschlagene Anmeldungen gesperrt wurde.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Das Administratorpasswort ändern](../../../administratorpasswort-aendern.md) und
[Ein Benutzerpasswort auf der Kommandozeile zurücksetzen](../../../../../eng-us/features/6.0/reset-user-password.md).

1. **Ihr eigenes Passwort** (3 Minuten). In admin4 das Benutzermenü (die Schaltfläche mit Ihrem Namen in der
   Kopfzeile) > **Passwort ändern**. Die Seite listet die Anforderungen auf und hakt sie während der Eingabe ab;
   **Sicheres Passwort erzeugen** erstellt eines.
2. **Das Passwort eines anderen** (3 Minuten). Öffnen Sie den Benutzer, **Bearbeiten**, geben Sie das neue Passwort
   zweimal im Feld **User account** ein, **Zur Veröffentlichung schicken**. Besser: Lassen Sie den Benutzer
   **Passwort vergessen?** verwenden, damit Sie sein Passwort nie kennen.
3. **Was auf eine Änderung folgt** (3 Minuten). Mit `[PasswordSettings] EndOtherSessions=enabled` (die Voreinstellung)
   werden die anderen Sitzungen des Benutzers bei ihrer nächsten Anfrage abgemeldet; mit
   `ChangeNotificationMail=enabled` erhält der Benutzer „Ihr Passwort wurde geändert“, eine Nachricht, die nie das
   Passwort enthält. Die Änderung steht im Audit-Protokoll.
4. **Passwort vergessen?** (2 Minuten). Der Link in der E-Mail funktioniert einmal und eine Stunde lang
   (`ForgotPasswordKeyLifetime=3600`). Er braucht eine echte Adresse und funktionierenden E-Mail-Versand, deshalb
   funktioniert er auf einer Schulungs-Website mit Adressen unter `example.invalid` nie.
5. **Kommandozeile und Sperren** (4 Minuten). `bin/php/resetuserpassword.php` (auch `exp:resetuserpassword`)
   funktioniert, wenn sich niemand anmelden kann. Fehlgeschlagene Anmeldungen sperren ein Konto nur, wenn
   `[UserSettings] MaxNumberOfFailedLogin` größer als 0 ist; der ausgelieferte Wert ist 0 (keine Sperre).

### Häufige Fragen

**Warum ist ein mit `-p` übergebenes Passwort bedenklich?** Es ist in der Prozessliste und im Verlauf der Shell
sichtbar. Verwenden Sie lieber `-g`, um eines zu erzeugen, das einmal ausgegeben wird.

**Kann ein Administrator das Passwort eines Benutzers sehen?** Nein. Passwörter werden als Hashes gespeichert; niemand
kann sie zurücklesen.

**Der Benutzer sagt, niemand habe sein Passwort geändert, aber er hat die E-Mail bekommen.** Behandeln Sie das als
möglichen Angriff: Prüfen Sie das Audit-Protokoll und ändern Sie das Passwort erneut.

### Stolperfallen

- **Der Trainer sperrt sich aus.** Setzen die Teilnehmenden `MaxNumberOfFailedLogin` und vertippen sich dann beim
  Administratorpasswort, sperren sie sich selbst aus. Führen Sie die Übung mit `sam.sample` durch und halten Sie eine
  zweite Administratorsitzung offen.
- **Das Skript als root ausführen.** Als root-Benutzer des Betriebssystems braucht es `--allow-root-user`; andernfalls
  verwenden Sie `-a` und `-ap` mit dem Anmeldenamen eines Administrators, wodurch auch dieses Passwort im Verlauf
  landet.

## Praktische Übung

Auf Ihrer Schulungsinstallation, als Administrator angemeldet, mit einem Terminal im Wurzelverzeichnis der
Installation:

1. Öffnen Sie das Benutzermenü mit Ihrem Namen in der Kopfzeile und wählen Sie **Passwort ändern**. Lesen Sie die
   aufgelisteten Anforderungen; klicken Sie auf **Sicheres Passwort erzeugen**, sehen Sie es sich an und verlassen
   Sie die Seite, ohne zu speichern.
2. Öffnen Sie `tess.trainee`, klicken Sie auf **Bearbeiten**, geben Sie im Feld **User account** zweimal ein neues
   Passwort ein und klicken Sie auf **Zur Veröffentlichung schicken**.
3. Erzeugen Sie in einem Terminal ein neues Passwort für `sam.sample`:

   ```bash
   php bin/php/resetuserpassword.php --allow-root-user -u sam.sample -g -l 20
   ```

   (Führen Sie es ohne `--allow-root-user` und mit `-a <your login> -ap '<your password>'` aus, wenn Sie nicht root
   sind.)
4. Melden Sie sich in einem privaten Fenster mit dem ausgegebenen Passwort als `sam.sample` auf der öffentlichen
   Website an. Melden Sie sich dann ab.
5. Schalten Sie die Sperre nur für die Übungsumgebung ein und leeren Sie den INI-Cache:

   ```bash
   php bin/php/console exp:ini set site.ini/UserSettings/MaxNumberOfFailedLogin 3 global
   php bin/php/ezcache.php --clear-tag=ini
   ```

6. Versuchen Sie im privaten Fenster viermal, sich mit einem falschen Passwort als `sam.sample` anzumelden. Versuchen
   Sie es dann mit dem richtigen.
7. Öffnen Sie als Administrator `sam.sample` und **Benutzerkonten-Einstellungen konfigurieren**. Lesen Sie die Zahl
   der fehlgeschlagenen Versuche ab und klicken Sie auf **Zurücksetzen**.
8. Melden Sie sich erneut mit dem richtigen Passwort als `sam.sample` an.
9. Schalten Sie die Sperre wieder aus:

   ```bash
   php bin/php/console exp:ini rem site.ini/UserSettings/MaxNumberOfFailedLogin global
   php bin/php/ezcache.php --clear-tag=ini
   ```

## Erwartetes Ergebnis

- Schritt 3 gibt ein erzeugtes Passwort mit 20 Zeichen aus.
- Schritt 6: Nach mehr fehlgeschlagenen Versuchen, als die Einstellung erlaubt, wird auch das richtige Passwort
  abgelehnt; das Konto ist gesperrt.
- Schritt 7 zeigt die fehlgeschlagenen Versuche; nach **Zurücksetzen** funktioniert die Anmeldung in Schritt 8.
- Nach Schritt 9 zeigt `exp:ini where site.ini/UserSettings/MaxNumberOfFailedLogin` den Standardwert 0 als wirksam.
- Der Trainer prüft die Einträge im Audit-Protokoll später im Kurs oder bittet eine Person, Schritt 7 zu zeigen.

![Das Formular Passwort ändern](../../../../../eng-us/help/images/trainers/sa-change-password.webp)

## Quiz

1. Wie lange funktioniert ein Link aus **Passwort vergessen?** auf einer neuen Installation?
   A. 10 Minuten B. eine Stunde C. einen Tag D. bis er benutzt wird
2. Was geschieht mit den anderen Sitzungen eines Benutzers, wenn ein Administrator das Passwort ändert?
3. Warum ist `-g` bei `resetuserpassword.php` besser als `-p`?
4. Wird ein Konto mit den ausgelieferten Einstellungen nach zehn falschen Passwörtern gesperrt? Warum?
5. Nennen Sie zwei Stellen, an denen ein Administrator die fehlgeschlagenen Anmeldungen eines Kontos zurücksetzen
   kann.

<details>
<summary>Antworten</summary>

1. **B.** `ForgotPasswordKeyLifetime=3600` Sekunden
   ([Passwort vergessen?](../../../administratorpasswort-aendern.md#passwort-vergessen)).
2. Sie werden bei ihrer nächsten Anfrage abgemeldet (`EndOtherSessions=enabled`).
3. Ein Passwort auf der Kommandozeile bleibt in der Prozessliste und im Verlauf der Shell.
4. Nein. `MaxNumberOfFailedLogin` ist bei Auslieferung 0, und das bedeutet: keine Sperre.
5. **Zurücksetzen** unter **Benutzerkonten-Einstellungen konfigurieren** und **Entsperren** unter
   **Benutzerkonten prüfen** (Lektion 2.4).

</details>

## Weiterführende Literatur

- [Das Administratorpasswort ändern](../../../administratorpasswort-aendern.md): jeder Weg, und was zu tun ist, wenn Sie
  ausgesperrt sind.
- [Ein Benutzerpasswort auf der Kommandozeile zurücksetzen](../../../../../eng-us/features/6.0/reset-user-password.md): die
  Optionen und Fehlermeldungen des Skripts.
- [Ihr Passwort ändern](../../../../../eng-us/features/6.0/modern-password-change.md): die Regeln und die E-Mail.
- [Sitzungen](../../../../anleitungen/sitzungen.md): alle Sitzungen nach einer vermuteten Kompromittierung beenden.

## Verwandte Seiten

- [Modul 2: Benutzer und Gruppen](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
