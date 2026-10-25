# Sitzungen: wer angemeldet ist, und Browser abmelden

Diese Anleitung erklärt Sitzungen in Exponential und die Seite **Setup > Sitzungen** (`/setup/session`): was eine Sitzung ist,
die beiden Arten von Session-Handlern und was die Seite mit jedem davon tun kann, die Kennzahlen, den Filter, die Suche, das Sortieren
und Blättern, das sichere Entfernen von Sitzungen und was zu tun ist, wenn sich etwas nicht wie erwartet verhält.

Sie richtet sich an Administratoren. Jede Seite, jedes Feld und jede Regel unten wurde am 6. Oktober 2026 am Code dieses Repositorys
geprüft; die Dateien sind unter [Quellen](#quellen) genannt.

[Anleitungen](README.md) · Siehe auch: [Sicherheit und Audit](sicherheit-und-audit.md) ·
[Betrieb einer Site](betrieb-einer-website.md) · [Blättern in Listen](../funktionen/6.0/seitenweise-listen-im-admin.md) ·
[Ihre Geräte und Sitzungen](ihre-geraete-und-sitzungen.md) (die Sitzungen eines Benutzers, nach Gerät, endgültig abgemeldet)

## Kurz gesagt

- Eine **Sitzung** hält einen Browser angemeldet. Sie wird bei der ersten Anfrage geöffnet, die sie braucht, und endet
  `site.ini [Session] SessionTimeout` Sekunden nach ihrer letzten Verwendung (Standard 31104000, 360 Tage; bis
  Oktober 2026 259200, drei Tage).
- **Angemeldet bleiben** ist auf den Anmeldeformularen vorausgewählt: Das Sitzungs-Cookie hält dann
  `RememberMeTimeout` Sekunden (Standard 31104000, 360 Tage) statt nur bis zum Schließen des Browsers. iOS Safari und
  Chrome verwerfen solche Browser-Sitzungs-Cookies von sich aus, wenn sie in den Hintergrund gehen; mit dem Kästchen
  bleibt die Anmeldung erhalten. Die Lebensdauer gilt für die ganze Anmeldung, auch wenn die Sitzung später eine neue
  ID bekommt (der Code-Schritt der Zwei-Faktor-Anmeldung), unter Apache und Velocity gleich. Wer das Kästchen leert,
  bekommt das Cookie, das mit dem Browser endet.
- Wo Sitzungen aufbewahrt werden, hängt von `site.ini [Session] Handler` ab. Leer (der Standard) bedeutet
  `ezpSessionHandlerPHP`: PHP bewahrt sie in seinem eigenen Speicher auf (Dateien), und die Seite **kann sie nicht einzeln auflisten
  oder entfernen**. `ezpSessionHandlerDB` bewahrt sie in der Tabelle `ezsession` auf, und die Seite listet sie auf und entfernt sie.
- Ohne Sitzungstabelle zeigt die Seite, **wer sich kürzlich angemeldet hat** (aus `ezuservisit`, das jeder Handler führt),
  mit einem Zeitfenster, einer Suche, Sortierung und Blättern, und erklärt, wie Sie umstellen.
- Mit Sitzungstabelle zeigt die Seite Kennzahlen, einen Filter und eine Suche, eine Karte pro Benutzer (oder pro Sitzung eines
  Benutzers) mit letzter Aktivität und Leerlaufzeit, und entfernt Sitzungen über eine **Bestätigung**. Die Sitzung, die Sie gerade verwenden,
  wird nie aus einer Auswahl entfernt.
- Sitzungsschlüssel werden nie angezeigt: Eine Karte trägt eine kurze Referenz und die ersten vier Zeichen des Schlüssels.

## 1. Die Seite ohne Sitzungstabelle (der Standard)

Öffnen Sie **Setup > Sitzungen**. Mit leerem `Handler=` beginnt die Seite mit einem blauen Hinweis: Sitzungen werden von
`ezpSessionHandlerPHP` im Sitzungsspeicher von PHP aufbewahrt (`session.save_handler`, meist `files`), daher können sie hier weder gezählt
noch entfernt werden. PHP entfernt abgelaufene Sitzungen selbst (`session.gc_maxlifetime`, das Exponential auf
`SessionTimeout` setzt).

Darunter:

| Teil | Was er zeigt |
|---|---|
| Kennzahlen | Benutzer, die sich innerhalb des letzten `ActivityTimeout` (standardmäßig eine Stunde), innerhalb des letzten Tages und innerhalb der Sitzungsdauer angemeldet haben |
| Angemeldet innerhalb | Das Zeitfenster der Liste: die letzte Stunde, der letzte Tag, die Sitzungsdauer |
| Benutzer suchen | Teil eines Namens, Logins oder einer E-Mail-Adresse; **Liste aktualisieren** wendet ihn an, **Suche löschen** entfernt ihn |
| Kürzlich angemeldet | Eine Karte pro Benutzer: Name (ein Link zum Benutzer), Login, E-Mail, letzte Anmeldung und wie lange sie her ist, die Anmeldung davor, die Anzahl der Anmeldungen; Ihre eigene Karte ist mit **Sie** markiert |
| Sortieren nach | Letzte Anmeldung (neueste zuerst), voller Name, Login, E-Mail, Anmeldungen; die aktuelle Sortierung erneut zu wählen, kehrt sie um |
| Pro Seite und Seiten | 50, 25 oder 100 pro Seite (`admininterface.ini [PaginationSettings] ItemsPerPageList_setup_session[]`) und die Seitennavigation |
| So verwalten Sie Sitzungen auf dieser Seite | Die Einstellungen für den Datenbank-Handler (Abschnitt 3) |

Ein Benutzer in dieser Liste kann noch eine offene Sitzung haben; einer, der sich abgemeldet hat oder dessen Browser sein Cookie vergessen hat, hat
keine. Mit dem PHP-Handler wird der Besuchseintrag nur bei der Anmeldung geschrieben, „letzte Anmeldung“ ist also genau das und nicht der
letzte Klick.

Zeitfenster und Suche bleiben in Ihrer eigenen Sitzung erhalten, während Sie blättern und sortieren.

## 2. Die Seite mit Sitzungstabelle

Mit `Handler=ezpSessionHandlerDB` listet die Seite die Zeilen von `ezsession` auf.

**Kennzahlen**: Sitzungen insgesamt, Benutzer, die innerhalb von `ActivityTimeout` aktiv waren, Sitzungen angemeldeter Benutzer, anonyme
Sitzungen und Sitzungen, die abgelaufen sind, aber noch in der Tabelle stehen (rot, wenn es welche gibt).

**Aktionen für alle Sitzungen**:

- **Abgelaufene / alte Sitzungen entfernen** ist gefahrlos: Es entfernt die abgelaufenen Zeilen und die Warenkörbe, die sie hinterlassen, so wie
  `bin/php/ezsessiongc.php` und der Cronjob-Teil `session_gc` es tun. Niemand wird abgemeldet. Die Meldung danach sagt, wie
  viele entfernt wurden.
- **Alle Sitzungen entfernen** meldet alle ab, auch Sie. Es fragt vorher nach, auf einer Seite, die das sagt.

**Filter**: Benutzer (Jeder, Angemeldete Benutzer, Anonyme Benutzer), **Benutzer suchen** (Teil eines Namens, Logins oder einer
E-Mail-Adresse; `%` und `_` stehen für sich selbst) und **Inaktive Benutzer einschließen** (auch Benutzer, deren letzte Aktivität länger zurückliegt als
`ActivityTimeout`). **Liste aktualisieren** wendet sie an; sie bleiben in Ihrer Sitzung erhalten.

**Die Liste** hat eine Karte pro Benutzer: Name, Login, Kennzeichen (**Sie**, Anonyme Besucher, die Anzahl der Sitzungen,
Abgelaufen), E-Mail, letzte Aktivität, Leerlaufzeit in Worten und wann die Sitzung endet, mit den Knöpfen **Sitzungen** (die
Sitzungen dieses Benutzers einzeln) und **Benutzer** (das Benutzerobjekt). Sortieren Sie nach letzter Aktivität, vollem Namen, Login, E-Mail oder Anzahl;
die Sortierung steht in der Adresse (`/(sortby)/name/(order)/asc`), sodass das Blättern sie beibehält.

**Die Sitzungen eines Benutzers** (`/setup/session/<user id>` oder **Sitzungen** auf einer Karte) haben eine Karte pro Browser oder Gerät,
benannt nach den ersten vier Zeichen des Schlüssels. Die Sitzung, die Sie gerade verwenden, ist markiert und kann nicht angekreuzt werden.
**Sitzungen aller Benutzer** führt zurück.

## 3. Auf Sitzungsverwaltung umstellen

1. In `settings/override/site.ini.append.php`:

   ```ini
   [Session]
   Handler=ezpSessionHandlerDB
   ```

2. Leeren Sie den INI-Cache und laden Sie die PHP-Worker neu, oder stellen Sie bereit:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ./console exp:velocity deploy --allow-root-user
   ```

   Alle melden sich einmal neu an: Von PHP aufbewahrte Sitzungen werden nicht in die Tabelle übernommen.
3. Öffnen Sie **Setup > Sitzungen**: die Liste aus Abschnitt 2.

`ForceStart=enabled` öffnet auch für jeden anonymen Besucher eine Sitzung; setzen Sie es nur, wenn anonyme Besucher
gezählt werden sollen, denn es schreibt für jeden Besuch eine Zeile.

## 4. Sitzungen sicher entfernen

| Knopf | Was geschieht |
|---|---|
| Ausgewählte entfernen (alle Benutzer) | Eine Bestätigung listet die angekreuzten Benutzer mit der Anzahl ihrer Sitzungen auf; **Die Sitzungen entfernen** meldet ihre Browser ab. Ist Ihr eigener Benutzer angekreuzt, werden seine anderen Sitzungen entfernt, und die, die Sie gerade verwenden, bleibt. |
| Ausgewählte entfernen (ein Benutzer) | Eine Bestätigung für die angekreuzten Sitzungen; die Sitzung, die Sie gerade verwenden, wird ausgelassen, auch wenn sie angekreuzt war. |
| Alle Sitzungen entfernen | Eine Bestätigung, die sagt, dass alle abgemeldet werden, auch Sie. |
| Abgelaufene / alte Sitzungen entfernen | Keine Bestätigung: Niemand wird abgemeldet. |

**Abbrechen** kehrt zurück, ohne etwas zu ändern. Das Entfernen einer Sitzung meldet einen Browser ab; es sperrt den
Benutzer nicht. Um zu verhindern, dass sich jemand erneut anmeldet, deaktivieren Sie zusätzlich den Benutzer oder ändern Sie sein Passwort.

Die Seite sendet dieselben Knöpfe wie bisher (`RemoveAllSessionsButton`, `RemoveTimedOutSessionsButton`,
`RemoveSelectedSessionsButton`, `UserIDArray[]`); die Bestätigung sendet sie erneut mit `ConfirmSessionRemoval=1`.
Die Sitzungen eines Benutzers werden als Referenzen gesendet (`SessionRefArray[]`); ein älteres Override, das `SessionKeyArray[]` sendet,
funktioniert weiterhin, allerdings nur für Schlüssel dieses Benutzers. Jedes Formular trägt das Formular-Token.

## 5. Probleme

| Symptom | Ursache und Behebung |
|---|---|
| „Ihr aktueller Session-Handler unterstützt keine Session-Verwaltung.“ | `Handler` ist leer oder `ezpSessionHandlerPHP`. Das ist normal; siehe Abschnitt 3 zum Umstellen. |
| Die Liste der kürzlichen Anmeldungen ist leer | Niemand hat sich innerhalb des Zeitfensters angemeldet: Wählen Sie die Sitzungsdauer. |
| „Nicht alle abgelaufenen Sitzungen konnten entfernt werden.“ | Der Lauf wurde abgebrochen, um eine Zeitüberschreitung zu vermeiden. Wiederholen Sie ihn, führen Sie `php bin/php/ezsessiongc.php` aus oder richten Sie den Cronjob-Teil `session_gc` ein. |
| „Nur die Sitzung, die Sie gerade verwenden, war ausgewählt“ | Ihre eigene aktuelle Sitzung wird nie aus der Liste entfernt. Melden Sie sich ab, um sie zu beenden. |
| „Zeitversatz erkannt“ bei Letzte Aktivität | Das Ende der Sitzung liegt weiter in der Zukunft als die Sitzungsdauer: Die Uhr eines Webservers weicht ab, oder `SessionTimeout` wurde verringert. |

## Quellen

- View: `kernel/private/classes/views/setup/session.php` (`Exponential\View\Kernel\Setup\Session`), Moduldefinition
  `kernel/setup/module.php` (`session`, Richtlinie `setup/administrate`).
- Templates (design/admin und design/admin4): `setup/session.tpl`, `setup/session_no_db.tpl`,
  `setup/session_confirmremove.tpl`, `setup/session_exp_style.tpl`; `design/standard/templates/setup/session_no_db.tpl`
  für andere Designs.
- Einstellungen: `site.ini [Session] Handler, SessionTimeout, ActivityTimeout, ForceStart`;
  `admininterface.ini [PaginationSettings] ItemsPerPageList_setup_session[]`.
- Garbage Collection: `kernel/private/classes/services/sessiongarbagecollector.php`, `bin/php/ezsessiongc.php`,
  `cronjobs/session_gc.php`.
- Tests: `tests/tests/kernel/classes/setup/SessionPageTest.php` (Referenzen, Regel zur eigenen Sitzung, Aufräumen, Zeitdauern,
  Zeilen; ohne Datenbank).
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
