---
title: "Lektion 2.2: Benutzer, Rollen und Passwörter"
audience: trainer, admin, developer
category: training
order: 22
summary: Die Teilnehmenden planen, wie Benutzerkonten, Gruppen und Rollen zu Exponential umziehen, entscheiden pro Quelle, ob Passwort-Hashes übernommen werden können oder ein Zurücksetzen nötig ist, und behandeln personenbezogene Daten mit einer Rechtsgrundlage. 45 Minuten.
keywords: migration zu exponential, benutzer, benutzergruppen, rollen, richtlinien, passwort-hash, passwort zurücksetzen, personenbezogene daten, datenschutz, migrating to exponential, users, user groups, roles, policies, password hash, password_hash_type, php_default, bcrypt, password reset, personal data, data protection
type: help
---
# Lektion 2.2: Benutzer, Rollen und Passwörter

Benutzer sind der Teil einer Migration, den die meisten Menschen bemerken, wenn etwas schiefgeht: Niemand kann sich
anmelden. Diese Lektion vermittelt, wie Konten, Gruppen und Rollen auf Exponential abgebildet werden, welche
Passwort-Hashes Exponential prüfen kann und wann ein Zurücksetzen die einzige ehrliche Möglichkeit ist.

> **Hinweis**
> Testkonten verwenden in jeder Übung Adressen unter `example.invalid`. Importieren Sie niemals echte Benutzer in eine
> Schulungsinstallation.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 2: Inhalte auf Exponential abbilden](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren und Entwickler; eine Ansprechperson für den Datenschutz ist willkommen |
| Voraussetzungen | [Lektion 2.1](klassen-datentypen-und-der-baum.md); eine Schulungsinstallation mit Shell-Zugang |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Benutzer, Gruppen und Rollen einer Quelle auf Benutzerobjekte, Benutzergruppen und Rollen mit Richtlinien abbilden;
- `ezuser.password_hash_type` lesen und die Hash-Typen nennen, die Exponential kennt;
- für eine gegebene Quelle entscheiden, ob Hashes als Typ 7 importiert werden können oder ein Zurücksetzen nötig ist;
- erklären, warum Berechtigungen für die neue Website entworfen und nicht eins zu eins kopiert werden.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Inhalte auf Exponential abbilden, Abschnitt 6](../../../../installation/migration/inhaltsmodell-zuordnen.md#6-benutzer-gruppen-rollen-und-passwörter),
[Kapitel 14.10](../../../../installation/14-migration-von-4x.md#1410-benutzer-passwörter-und-anmeldung) und
[Kapitel 17.7](../../../../installation/17-migrationsreferenz.md#177-passwörter-url-aliase-und-bildspeicherung).

1. **Konten sind Objekte** (4 Minuten). Benutzer sind Objekte der Klasse `user` in Benutzergruppen; Mitglieder und
   Redakteure kommen meist in getrennte Gruppen.
2. **Rollen** (4 Minuten). Rollen werden als Exponential-Rollen mit Richtlinien neu aufgebaut und Benutzern oder
   Gruppen zugewiesen. Kopieren Sie Berechtigungen nicht eins zu eins; entwerfen Sie sie für die neue Website. Zeigen
   Sie [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md).
3. **Hash-Typen** (6 Minuten). Zeigen Sie die Tabelle aus Kapitel 14.10: Typen 0 bis 7, von `empty` bis
   `php_default`. Mit `[UserSettings] UpdateHash=true` (der Standard) speichert eine Anmeldung mit einem alten Hash
   einen neuen.
4. **Andere Systeme** (6 Minuten). Standard-Hashes von PHP `password_hash()` (beginnend mit `$2y$`, `$argon2i$` oder
   `$argon2id$`) können als Typ 7 importiert werden und werden bei der nächsten Anmeldung neu gehasht. Jedes andere
   Verfahren kann nicht geprüft werden: Importieren Sie die Konten ohne verwendbares Passwort und verschicken Sie
   Mails zum Zurücksetzen des Passworts, oder bauen Sie einen eigenen Login-Handler (Entwicklungsarbeit).

### Häufige Fragen

**Können WordPress- oder Drupal-Benutzer ihre Passwörter behalten?** Die Anleitungen zu WordPress und Drupal sagen,
dass beide Passwörter in eigenen Formaten speichern, die Exponential nicht liest: Planen Sie das Zurücksetzen. Für
Joomla sagt die Anleitung, man solle testen, ob die Hashes Standardwerte von PHP `password_hash()` sind.

**Was ist mit alten Exponential-Websites?** Alte Hash-Typen funktionieren weiter und werden bei der Anmeldung
aktualisiert, mit den Ausnahmen aus Kapitel 14.10: `md5_site`-Hashes brauchen das alte `[UserSettings] SiteName`, und
`mysql`-Hashes funktionieren unter MySQL 8 nicht.

**Wann informieren wir die Benutzer?** Vor der Umstellung. „Ausgesperrte Benutzer“ ist eines der wiederkehrenden
Risiken der Checkliste, und die Gegenmaßnahme ist, das Zurücksetzen zu planen und die Benutzer vorab zu informieren.

### Stolperfallen

- **Hashes leichtfertig exportieren.** Passwort-Hashes sind personenbezogene Daten. Das Website-Archiv von
  xrowextract exportiert sie nie
  ([Migration weg von Exponential](../../../../installation/migration/exponential-verlassen.md#5-benutzer-und-personenbezogene-daten)).
- **Jedes Konto umziehen.** Importieren Sie nur, was die neue Website braucht, mit einer Rechtsgrundlage.
- **Die Anmeldung mit dem gemeinsamen Administratorkonto testen.** Verwenden Sie ein Testkonto pro Person.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation. Die Datenbank ist SQLite in `var/storage/sqlite3/exponential.db`;
öffnen Sie sie nur lesend.

1. Öffnen Sie die Datenbank:

   ```bash
   sqlite3 -readonly var/storage/sqlite3/exponential.db
   ```

2. Zeigen Sie die verwendeten Hash-Typen an:

   ```sql
   SELECT password_hash_type, COUNT(*) FROM ezuser GROUP BY password_hash_type;
   ```

3. Zeigen Sie die Konten ohne Einstellungszeile an (die Vorabprüfung aus
   [Kapitel 14.5.3](../../../../installation/14-migration-von-4x.md#1453-vorabprüfungsabfragen)); auf einer frischen
   Installation sollte sie nichts zurückgeben:

   ```sql
   SELECT u.contentobject_id, u.login FROM ezuser u
     LEFT JOIN ezuser_setting s ON s.user_id = u.contentobject_id
    WHERE s.user_id IS NULL;
   ```

4. Verlassen Sie die Datenbank mit `.quit`.
5. Lesen Sie die Einstellung, die den Hash-Typ neuer Passwörter bestimmt:

   ```bash
   php bin/php/console exp:ini get site.ini/UserSettings/HashType
   ```

6. Schreiben Sie für jede dieser Quellen „als Typ 7 importieren“ oder „zurücksetzen“ und den Grund in Ihre Notizen,
   anhand der Zuordnungsseite und der Anleitungen zu den Wegen: eine WordPress-Website; eine Drupal-Website; eine
   eigene PHP-Anwendung, die `password_hash()`-Werte mit `$2y$` am Anfang speichert; eine alte Exponential-4.x-Website
   unter MySQL 8 mit Hashes vom Typ 4.
7. Schreiben Sie eine kurze Ankündigung des Zurücksetzens für Redakteure (vier Sätze): was sich ändert, wann, was sie
   tun müssen, wen sie fragen können. Verwenden Sie darin nur Adressen unter `example.invalid`.

## Erwartetes Ergebnis

- Schritt 2 zeigt auf einer frischen Installation den Typ 7 (`php_default`).
- Schritt 3 gibt keine Zeilen zurück.
- Schritt 5 gibt `php_default` aus, sofern die Installation es nicht geändert hat.
- Schritt 6: WordPress und Drupal zurücksetzen; die eigene PHP-Anwendung Typ 7; die 4.x-Website mit `mysql`-Hashes
  unter MySQL 8 zurücksetzen (Kapitel 14.10).
- Der Trainer prüft Schritt 6 und liest eine Ankündigung laut vor.

## Quiz

1. Welchen Hash-Typ verwendet Exponential 6 standardmäßig für neue Passwörter?
2. Eine Quelle speichert Hashes, die mit `$argon2id$` beginnen. Was können Sie tun?
   A. Nichts, immer zurücksetzen B. Sie als Typ 7 importieren C. Sie in MD5 umwandeln D. Sie im Klartext speichern
3. Was bewirkt `[UserSettings] UpdateHash=true`?
4. Warum könnten sich Konten einer alten Website mit `md5_site`-Hashes nach der Migration nicht anmelden?
5. Richtig oder falsch: Rollen sollten eins zu eins aus dem Quellsystem kopiert werden.

<details>
<summary>Antworten</summary>

1. Typ 7, `php_default` ([Kapitel 14.10](../../../../installation/14-migration-von-4x.md#1410-benutzer-passwörter-und-anmeldung)).
2. **B.** Der Kernel prüft ihn mit `password_verify()` und hasht ihn bei der nächsten Anmeldung neu
   ([Zuordnungsseite, Abschnitt 6](../../../../installation/migration/inhaltsmodell-zuordnen.md#6-benutzer-gruppen-rollen-und-passwörter)).
3. Eine erfolgreiche Anmeldung mit einem alten Hash speichert einen neuen Hash vom Typ `HashType` (Kapitel 14.10).
4. Der Hash enthält `[UserSettings] SiteName`; wenn die alte Website ihn geändert hatte, muss derselbe Wert gesetzt
   werden (Kapitel 14.10).
5. **Falsch.** Entwerfen Sie sie für die neue Website (Zuordnungsseite, Abschnitt 6).

</details>

## Weiterführende Informationen

- [Inhalte auf Exponential abbilden, Abschnitt 6](../../../../installation/migration/inhaltsmodell-zuordnen.md#6-benutzer-gruppen-rollen-und-passwörter):
  Benutzer, Gruppen, Rollen und Passwörter.
- [Kapitel 17.7: Passwörter, URL-Aliase und Bildspeicherung](../../../../installation/17-migrationsreferenz.md#177-passwörter-url-aliase-und-bildspeicherung):
  die Hash-Typen zum Nachschlagen.
- [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md): Rollen auf der neuen Website entwerfen.

## Verwandte Seiten

- [Modul 2: Inhalte auf Exponential abbilden](README.md)
- [Migration zu Exponential: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
