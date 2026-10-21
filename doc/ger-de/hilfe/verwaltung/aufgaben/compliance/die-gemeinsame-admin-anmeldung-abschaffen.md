---
title: Die gemeinsame Admin-Anmeldung abschaffen
audience: government, admin
category: public-sector
order: 50
summary: Schritt für Schritt - das gemeinsame Konto admin durch persönliche Administratorkonten ersetzen, damit das Audit-Protokoll Personen nennt, dann admin deaktivieren (nach dem Verlegen von UserCreatorID) oder unbrauchbar machen und ein getestetes Notfallverfahren behalten.
keywords: Admin-Konto, gemeinsames Konto, persönliche Konten, Rechenschaft, UserCreatorID, admin deaktivieren, Notfallzugang, Break-Glass, exp:resetuserpassword, Audit-Protokoll, minimale Rechte, öffentlicher Sektor, admin account, shared account, personal accounts, accountability, disable admin, break-glass, audit trail, least privilege, public sector
type: help
---
# Die gemeinsame Admin-Anmeldung abschaffen

Für Administratoren. Der Login `admin` existiert auf jeder Installation und ist der erste, den ein Angreifer probiert;
gemeinsame Nutzung verschleiert außerdem, wer was getan hat. Das Verfahren des Projekts steht in
[Das Administratorpasswort ändern: Kontenpflege](../../../administratorpasswort-aendern.md#kontenhygiene); diese Seite ist
seine Form als Checkliste.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Wenn etwas schiefgeht](#wenn-etwas-schiefgeht)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Jede Person, die heute `admin` verwendet, ist bekannt.
- Shell-Zugang für die Änderung von `UserCreatorID` und den Test des Notfallzugangs.

## Schritte

1. **Persönliche Konten**: Legen Sie unter **Benutzer > Administrator users** je Administrator einen Benutzer mit
   persönlichem Login und echter E-Mail-Adresse an. Prüfen Sie unter **Rollen und Richtlinien** > **Administrator**, ob
   die Gruppe die Rolle vergibt.
2. **Zweistufige Anmeldung** für jeden von ihnen (siehe die Aufgaben zu Personen und Berechtigungen in dieser Hilfe).
3. **Jede Person meldet sich** mit dem neuen Konto an und prüft ihre Arbeit.
4. **Verlegen Sie `UserCreatorID`**, bevor Sie `admin` deaktivieren: Setzen Sie in
   `settings/override/site.ini.append.php`
   `[UserSettings] UserCreatorID=<object id of an enabled administrator or service account>` (die mitgelieferte `14`
   ist `admin`; Kommandozeilenwerkzeuge und die Selbstregistrierung handeln als dieses Konto). Leeren Sie den INI-Cache
   und rollen Sie aus.
5. **Schaffen Sie `admin` ab**, auf eine dieser Weisen:
   - deaktivieren: Öffnen Sie den Benutzer `admin` > **Benutzerkonten-Einstellungen konfigurieren** > wählen Sie
     **Benutzerkonto aktivieren** ab > **OK**;
   - aktiviert, aber unbrauchbar lassen: `./console exp:resetuserpassword --allow-root-user -u admin -g -l 64`, und
     bewahren Sie das Passwort nicht auf.
6. **Notfallzugang**: Schreiben Sie das Verfahren auf (Root-Shell und `exp:resetuserpassword` für ein
   Administratorkonto) und testen Sie es einmal im Jahr auf einer Kopie.

## Prüfen, ob es funktioniert hat

- Die Anmeldung als `admin` schlägt fehl.
- Ein Kommandozeilenwerkzeug, das als `UserCreatorID` handelt (zum Beispiel eine Bereinigung des Papierkorbs auf einer
  Testkopie), läuft.
- Das Audit zeigt Handlungen von nun an unter persönlichen Logins.

## Wenn etwas schiefgeht

Wenn sich niemand anmelden kann, verwenden Sie das Notfallverfahren; alle Wege der Wiederherstellung stehen in
[Das Administratorpasswort ändern](../../../administratorpasswort-aendern.md).

## Verwandte Seiten

- [Rollen und minimale Rechte](../../../../../eng-us/help/government/security/roles-and-least-privilege.md)
- [Das Standard-Administratorpasswort](../../../standard-administratorpasswort.md)
- [Sicherheitshärtung: Standard- und gemeinsame Zugangsdaten](../../../sicherheitshaertung.md#standard--und-geteilte-zugangsdaten)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
