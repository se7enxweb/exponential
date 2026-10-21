---
title: "Lektion 8.3: Zweistufige Anmeldung"
audience: trainer, admin
category: training
order: 83
summary: Die Teilnehmenden schalten auf einer Schulungsinstallation die zweistufige Anmeldung ein, geben Rollen die zwei Richtlinien, die sie braucht, richten für einen Testbenutzer eine Authenticator-App oder E-Mail-Codes ein, melden sich mit einem Code an und helfen einem Benutzer, der sein Telefon verloren hat. 45 Minuten.
keywords: website-administration, zweistufige anmeldung, zwei-faktor, 2FA, sevenx_authentication_2fa, authenticator-app, TOTP, e-mail-code, user2fa/setup, user2fa/verify, Enforce2FA, LoginHandler, auth.log, site administration, two-step sign-in, two-factor, authenticator app, e-mail code
type: help
---
# Lektion 8.3: Zweistufige Anmeldung

Mit der zweistufigen Anmeldung meldet ein Passwort allein niemanden mehr an: Nach dem Passwort kommt ein Code aus
einer Authenticator-App oder aus einer E-Mail. Ein gestohlenes oder erratenes Passwort ist dann für sich allein
nutzlos. Diese Lektion zeigt Administratoren, wie sie die zweistufige Anmeldung einschalten, wie Benutzer sie
einrichten und was zu tun ist, wenn ein Benutzer sein Telefon verliert.

> **Hinweis**
> Richten Sie beim Ausprobieren nie ein echtes Administratorkonto ein. Verwenden Sie einen Testbenutzer mit einer
> Adresse unter `example.invalid`, und lassen Sie den E-Mail-Versand der Schulungsinstallation auf `file`.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 8: Sicherheit](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren |
| Voraussetzungen | [Lektion 8.2](passwoerter-und-anmelderichtlinie.md); ein Administratorkonto, ein Testbenutzer und eine Shell; eine Authenticator-App auf einem Telefon (optional) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, wogegen der zweite Schritt schützt und wogegen nicht;
- die zweistufige Anmeldung einschalten: die Erweiterung, den Login-Handler, das Feld in der Benutzerklasse und die
  zwei Richtlinien;
- für einen Testbenutzer eine Authenticator-App oder E-Mail-Codes einrichten und sich mit einem Code anmelden;
- den zweiten Schritt für einen Benutzer zurücksetzen, der sein Telefon verloren hat;
- zwischen freiwilliger Nutzung und `Enforce2FA=enabled` wählen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md). Die eigene Dokumentation der Erweiterung
steht in `extension/sevenx_authentication_2fa/README.md` und `INSTALL.md`.

1. **Was ein Benutzer sieht** (4 Minuten). Er meldet sich wie immer an; eine Seite fragt nach dem 6-stelligen Code;
   jeder Code funktioniert einmal; nach fünf falschen Codes endet die Anmeldung. Eingerichtet wird sie unter *Mein
   Konto* > *Zweistufige Anmeldung* (`/user2fa/setup`). Wer sie ändern oder ausschalten will, muss einen aktuellen
   Code eingeben.
2. **Einschalten** (5 Minuten). Vier Teile: die Erweiterung `sevenx_authentication_2fa` aktiv; der Login-Handler
   `sevenxUser2fa` vor `standard` in `[UserSettings] LoginHandler[]`; ein Feld mit dem Datentyp *7x 2FA
   Configuration* in der Benutzerklasse; und die Richtlinien `user2fa/setup` und `user2fa/verify` für jede Rolle, die
   sich anmeldet. Empfohlen: ein `SecretKey`, der die Geheimnisse der Authenticator-Apps verschlüsselt.
3. **Freiwillig oder Pflicht** (2 Minuten). `[General] Enforce2FA=disabled` (Voreinstellung) lässt Benutzer selbst
   entscheiden. `enabled` mit `DefaultMethod=totp` lässt einen Benutzer ohne zweiten Schritt die App direkt nach dem
   Passwort einrichten. Richten Sie zuerst die Administratoren ein.
4. **Telefon verloren** (2 Minuten). Ein Administrator öffnet das Objekt des Benutzers, setzt das Feld *Zweistufige
   Anmeldung* auf *E-Mail-Einmalcode* oder *Deaktiviert* und veröffentlicht.
5. **Was sie schützt** (2 Minuten). Eine neue Sitzungs-ID nach dem Code; begrenzte Codes pro Anmeldung; E-Mail-Codes
   nur als Hash gespeichert; jeder Schritt wird in `var/log/auth.log` geschrieben, nie ein Code oder ein Geheimnis.

### Häufige Fragen

**Ist die Erweiterung bei einer neuen Installation dabei?** Laut Anleitung aktivieren die Installer sie. Prüfen Sie
vor dem Kurs **Setup > Extensions** auf Ihrer Schulungsinstallation; das Feld in der Benutzerklasse und der
Login-Handler müssen eventuell noch eingerichtet werden.

**Was, wenn der Schlüssel zur Verschlüsselung verloren geht?** Dann richten alle die App neu ein. Halten Sie
`SecretKey` aus der Versionsverwaltung heraus und nehmen Sie ihn in die Sicherung auf (Modul 9).

**Überspringt die Anmeldung über soziale Netzwerke den zweiten Schritt?** Nein. Ein über einen Anbieter gefundener
Benutzer durchläuft trotzdem seinen eigenen zweiten Schritt.

### Stolperfallen

- **Abweichende Uhrzeit.** Authenticator-Codes gelten nur etwa eine Minute lang. Ein Schulungsrechner mit falscher
  Uhrzeit lehnt jeden Code ab.
- **Kein Feld in der Benutzerklasse.** Benutzer einer Klasse ohne das Feld können keinen zweiten Schritt behalten;
  die Einrichtungsseite sagt das.
- **`settings/override` hat Vorrang vor einem Siteaccess.** E-Mails aus einem Test-Siteaccess verwenden trotzdem den
  globalen Versandweg.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation. Hat der Trainer die Schritte 1 bis 4 bereits vorbereitet, beginnen
Sie mit Schritt 5.

1. Öffnen Sie **Setup > Extensions** und prüfen Sie, dass `sevenx_authentication_2fa` aktiv ist. Wenn nicht,
   aktivieren Sie die Erweiterung so, wie Ihr Trainer es zeigt.
2. Setzen Sie in `settings/override/site.ini.append.php` die Login-Handler (bearbeiten Sie die Datei; legen Sie
   zuerst eine Kopie an):

   ```ini
   [UserSettings]
   LoginHandler[]
   LoginHandler[]=sevenxUser2fa
   LoginHandler[]=standard
   ```

3. Öffnen Sie die Benutzerklasse (**Setup > Klassen**, Gruppe *Users*, Klasse *User*) und suchen Sie ein Feld mit dem
   Datentyp *7x 2FA Configuration*. Gibt es keines, fügen Sie eines mit dem Bezeichner `two_factor` hinzu und
   speichern Sie die Klasse.
4. Öffnen Sie **Benutzer > Rollen und Richtlinien**, öffnen Sie die Rolle Ihres Testbenutzers (zum Beispiel *Member*
   oder *Editor*) und fügen Sie die Richtlinien `user2fa/setup` und `user2fa/verify` hinzu.
5. Leeren Sie die Caches: `php bin/php/ezcache.php --clear-tag=ini` und, wenn die Website auf Velocity läuft,
   `php bin/php/console exp:velocity deploy`, so wie Ihr Trainer es zeigt.
6. Melden Sie sich in einem privaten Fenster als Testbenutzer an und öffnen Sie `/user2fa/setup`.
7. Wählen Sie *Authenticator-App*, scannen Sie den QR-Code mit einer App auf Ihrem Telefon und geben Sie den
   angezeigten Code ein. Ohne Telefon wählen Sie stattdessen E-Mail-Codes.
8. Melden Sie sich ab und wieder an. Geben Sie den Code aus der App ein, oder suchen Sie bei E-Mail-Codes die neueste
   Datei in `var/log/mail` und lesen Sie den Code darin.
9. Melden Sie sich ab, melden Sie sich an und geben Sie zweimal einen falschen Code ein, bevor Sie den richtigen
   eingeben.
10. Öffnen Sie als Administrator **Setup > Logdateien** und lesen Sie die neuesten Zeilen von `auth.log`.
11. Setzen Sie den Testbenutzer zurück: Öffnen Sie das Objekt des Benutzers, setzen Sie das Feld *Zweistufige
    Anmeldung* auf *Deaktiviert* und veröffentlichen Sie.

## Erwartetes Ergebnis

- Schritt 7 endet damit, dass der zweite Schritt für den Testbenutzer aktiv ist; der Schlüssel wird nicht noch einmal
  angezeigt.
- Schritt 8: Die Anmeldung fragt nach dem Passwort nach dem Code, und der Benutzer ist erst nach dem richtigen Code
  angemeldet.
- Schritt 10 listet die Schritte der Anmeldungen auf, auch die falschen Codes, aber keinen Code und kein Geheimnis.
- Nach Schritt 11 meldet sich der Testbenutzer allein mit dem Passwort an.
- Der Trainer zeigt die Ansicht von `auth.log` einer Person am Projektor.

## Quiz

1. Welche zwei Richtlinien muss jede Rolle haben, die sich anmeldet?
2. In welcher Reihenfolge müssen die Login-Handler stehen?
   A. `standard`, dann `sevenxUser2fa` B. `sevenxUser2fa`, dann `standard` C. Die Reihenfolge spielt keine Rolle
   D. Nur `sevenxUser2fa`
3. Ein Benutzer hat sein Telefon verloren. Was tut der Administrator?
4. Was bewirkt `Enforce2FA=enabled` mit `DefaultMethod=totp` für einen Benutzer ohne zweiten Schritt?
5. Welches Log zeichnet jeden Schritt der zweistufigen Anmeldung auf?

<details>
<summary>Antworten</summary>

1. `user2fa/setup` und `user2fa/verify` ([Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md#einschalten)).
2. **B.** Der Handler für die zweistufige Anmeldung steht vor `standard`.
3. Er öffnet das Objekt des Benutzers, setzt das Feld *Zweistufige Anmeldung* auf *E-Mail-Einmalcode* oder
   *Deaktiviert* und veröffentlicht; der Benutzer richtet die App danach neu ein.
4. Der Benutzer muss direkt nach dem Passwort eine Authenticator-App einrichten, bevor er angemeldet wird.
5. `var/log/auth.log`, ohne Codes oder Geheimnisse.

</details>

## Weiterführende Literatur

- [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md): Benutzer, Administratoren und was sie schützt.
- [Sicherheit und Audit, Abschnitt 5](../../../../anleitungen/sicherheit-und-audit.md#5-zwei-faktor-anmeldung-optional-10-minuten):
  der kurze Durchgang.
- [Zwei-Faktor-Anmeldung für Hoster](../../../hosting/sicherheit/zweistufige-anmeldung.md).

## Verwandte Seiten

- [Modul 8: Sicherheit](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
