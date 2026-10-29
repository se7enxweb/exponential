---
title: Das Benutzer-Auditprotokoll
audience: admin
category: account
order: 11
summary: Benutzer > Benutzer-Auditprotokoll beantwortet, wer was mit einem Konto getan hat, wann und von wo, und wer abgewiesen wurde: eine Zeile je Ereignis mit den Feldern vorher und nachher, gefiltert nach Benutzer, Aktion, Ergebnis und Datum, als CSV exportierbar; Passwörter werden nie aufgezeichnet.
keywords: Benutzer-Auditprotokoll, Audit, wer hat was geändert, Kontoverlauf, Mitgliederverwaltung, abgewiesen, fehlgeschlagene Anmeldung, Rolle zugewiesen, CSV exportieren, Datenschutzbeauftragter, user/audit, Datenschutz, Aufbewahrung, user audit log
context: user/audit
type: help
---
# Das Benutzer-Auditprotokoll

Das Benutzer-Auditprotokoll beantwortet für jedes Konto der Website eine Frage: **Wer hat was damit getan, wann und von
wo**, und wer hat es versucht und wurde abgewiesen. Verwenden Sie es, nachdem ein Redakteur Mitglieder verwaltet hat,
wenn jemand fragt, warum sich sein Konto geändert hat, wenn eine Anmeldung verdächtig aussieht, oder um neue Konten und
Rollenänderungen durchzusehen.

Es listet eine Zeile je Ereignis zu einem Benutzerkonto oder einer Benutzergruppe, aus jeder Oberfläche: der
Administration, dem Editor, der öffentlichen Website, der REST-Schnittstelle und den Befehlen.

## Öffnen

Klicken Sie in der Administration im oberen Menü auf **Benutzer** und dann in der linken Spalte auf
**Benutzer-Auditprotokoll** (die Adresse ist `https://www.example.com/admin/user/audit`). Die Seite braucht die
Berechtigung `user/audit`: Administratoren haben sie. Wer sie hat, sieht die Ereignisse **jedes** Kontos; geben Sie sie
also nur Rollen, die das sollen, zum Beispiel einer Rolle mit `user/audit` allein für eine Datenschutzbeauftragte.

## Was eine Zeile zeigt

| Spalte | Was sie enthält |
|---|---|
| **Wann** | Datum und Uhrzeit des Ereignisses |
| **Aktion** | was geschah, zum Beispiel `access.user.create`, `access.user.change`, `access.user.disable`, `access.session.login.failed`, darunter das Ergebnis (*Erledigt*, *Abgewiesen* oder *Fehlgeschlagen*) und der Grund |
| **Konto** | das Konto oder die Benutzergruppe, um die es ging |
| **Von** | wer gehandelt hat: ein Benutzer, oder *System* für einen Befehl oder einen Cronjob |
| **Wo** | der Teil der Installation (Siteaccess), die Seite und die Netzwerkadresse, soweit die Datenschutzregeln es erlauben (standardmäßig gekürzt) |
| **Vorher** / **Nachher** | die Felder, die sich geändert haben, zum Beispiel die E-Mail-Adresse vorher und nachher (standardmäßig als Hash) |

Passwörter, Passwort-Hashes, Tokens und andere Geheimnisse werden **nie** aufgezeichnet.

## Was aufgezeichnet wird

- ein Konto angelegt, geändert, verschoben, aktiviert, deaktiviert, gesperrt, entsperrt oder entfernt, seine
  E-Mail-Adresse oder sein Benutzername geändert;
- ein Passwort geändert oder zurückgesetzt, ein Zurücksetzen angefordert (nie das Passwort selbst);
- eine Registrierung, die auf Freigabe wartet, freigegeben oder abgelehnt wurde, und das Konto eines neuen
  Redakteurs freigegeben oder abgelehnt;
- eine Rolle einem Konto oder einer Gruppe zugewiesen oder entzogen;
- Anmeldungen, fehlgeschlagene Anmeldungen, Abmeldungen, *Bestätigen Sie, dass Sie es sind* und beendete Sitzungen;
  Passkeys hinzugefügt oder verwendet;
- jeder abgewiesene Versuch, etwa wenn ein Redakteur das Konto eines Administrators bearbeiten will.

Damit ist die Arbeit der [Mitgliederverwaltung](mitglieder-verwalten.md) abgedeckt: was ein Redakteur mit einem
Mitglied getan hat, und jedes Mal, wenn ein Redakteur nach einem Konto gegriffen hat, das er nicht ändern darf.

## Die Liste eingrenzen

Unter **Liste eingrenzen**:

1. **Benutzer**: ein Benutzername oder eine Benutzer-ID. Gefunden werden die Ereignisse **zu** diesem Konto **und** die
   Ereignisse, die dieser Benutzer **ausgelöst** hat; eine Suche zeigt also, was mit einem Mitglied geschah und was
   ein Redakteur getan hat. Ein Klick auf einen Benutzernamen in der Liste sucht nach ihm.
2. **Aktion**: ein Ereignis oder ein Zweig davon: `access.user` findet jedes Ereignis `access.user.*`.
3. **Ergebnis**: *Jedes Ergebnis*, *Erledigt*, *Abgewiesen* oder *Fehlgeschlagen*.
4. **Von** und **Bis**: Tage, beide eingeschlossen.
5. Klicken Sie auf **Anzeigen**; **Zurücksetzen** entfernt die Filter.

In der Liste ist der Benutzername unter **Konto** oder **Von** ein Link: Er zeigt alle Ereignisse dieses Kontos.

## Exportieren

**Liste als CSV exportieren** lädt die ganze gefilterte Liste herunter (eine Zeile je Ereignis, vorher und nachher als
JSON), für eine Durchsicht oder ein Auskunftsersuchen. Der Export selbst wird aufgezeichnet. Behandeln Sie die Datei
wie die Daten, die sie enthält: Sie nennt Personen und was sie getan haben.

## Wie lange Ereignisse aufbewahrt werden

Die Seite sagt es: standardmäßig 730 Tage. Der Cronjob-Teil `useraudit` (in der Gruppe infrequent) entfernt ältere
Zeilen (`settings/audit.ini [UserAuditSettings] RetentionDays`). Die Audit-Dateien der Website behalten eine eigene
Kopie mit eigener Aufbewahrungsfrist.

## Beispiel

Ein Mitglied sagt, seine E-Mail-Adresse sei ohne sein Wissen geändert worden. Öffnen Sie das Benutzer-Auditprotokoll,
geben Sie unter **Benutzer** den Benutzernamen des Mitglieds ein, wählen Sie die Aktion `access.user.email.change` und
klicken Sie auf **Anzeigen**. Die Zeile nennt den Redakteur, der sie geändert hat, wann und dass es im Editor geschah;
eine Suche nach dem Benutzernamen dieses Redakteurs zeigt dann, was er an diesem Tag sonst noch getan hat.

## Wenn die Seite fehlt oder leer ist

| Was Sie sehen | Warum |
|---|---|
| Kein **Benutzer-Auditprotokoll** in der linken Spalte | Ihrer Rolle fehlt `user/audit`, oder die Installation ist älter als 6.0.15. |
| *Die Tabelle des Benutzer-Auditprotokolls fehlt.* | Eine Installation von vor 6.0.15: Ein Entwickler führt `php update/common/scripts/6.0/createuseraudittable.php` aus. |
| *Die Aufzeichnung ist in audit.ini ausgeschaltet* | `[UserAuditSettings] UserAudit=disabled`; die Liste zeigt, was vorher aufgezeichnet wurde. |
| *Diese Seite braucht eine SQL-Datenbank* | Unter MongoDB behalten die Audit-Dateien die Ereignisse; eine Tabelle gibt es nicht. |

## Verwandte Seiten

- [Mitglieder verwalten](mitglieder-verwalten.md)
- [Konten für Redakteure und Administratoren einrichten](konten-fuer-redakteure-und-administratoren-einrichten.md)
- [Wie finde ich heraus, wer was geändert hat?](handbuch/wie-finde-ich-heraus-wer-was-geaendert-hat.md), für Inhalte
- [Sicherheit und Audit](../anleitungen/sicherheit-und-audit.md)
- [Benutzer-Auditprotokoll](../funktionen/6.0/benutzer-auditprotokoll.md), die technische Beschreibung mit jeder Einstellung
- [Freie Dokumentation](freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
