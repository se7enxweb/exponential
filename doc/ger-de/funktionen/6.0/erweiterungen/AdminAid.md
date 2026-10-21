# AdminAid: Benutzer wechseln, Objekte untersuchen, Klassen übersetzen

Diese Seite ist für Administratoren, die eine Website auf Fehler untersuchen. `AdminAid` ist ein Hilfsmittel zur
Fehlersuche mit vier Werkzeugen:

| Werkzeug | Was es tut |
|---|---|
| **Benutzer wechseln** (`/aid/user_switch/<user id>`) | Sich von einem Administratorkonto aus als beliebiger Benutzer anmelden, ohne dessen Passwort, um die Website so zu sehen, wie dieser Benutzer sie sieht. |
| **Objektansicht** und **Objektliste** | Eine ausführliche Übersicht über die Daten eines Inhaltsobjekts und eine Liste aller Objekte einer Klasse. |
| **Klassenübersetzung** | Eine einfache Oberfläche, um die Attribute einer Inhaltsklasse zu übersetzen. |
| **Suche** | IDs in der Datenbank über die konfigurierten Tabellen hinweg nachschlagen. |

## Sicher verwenden

Die Warnung des Autors gilt weiter: Lassen Sie die Extension auf einem Live-Server nur für begrenzte Zeit laufen. Sie
ist ein mögliches Sicherheitsrisiko, auch wenn sie theoretisch sicher ist.

1. Gewähren Sie die Richtlinienfunktionen des Moduls `aid` (`user_switch`, `object_view`, `class_translate`,
   `search`) nur Ihrer eigenen Rolle.
2. Setzen Sie `LimitByIP=enabled` und tragen Sie Ihre Adresse in `IPList[]` ein.
3. Aktivieren Sie die Extension und erledigen Sie Ihre Arbeit.
4. Entfernen Sie die Extension aus `ActiveExtensions`, wenn Sie fertig sind.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `aid.ini` | `Aid` | `UserSwitchIDLimit[]` | `14` | Nur Benutzer mit diesen IDs dürfen den Benutzer wechseln |
| `aid.ini` | `Aid` | `UserClass[]` | `user` | Klassen, die einen Benutzer darstellen |
| `aid.ini` | `Aid` | `LimitByIP` | `disabled` | Eine Beschränkung nach IP einschalten |
| `aid.ini` | `Aid` | `IPList[]` | leer | Erlaubte Adressen |
| `aid.ini` | `SearchDatabase` | `ClassList[]` und weitere | | Durchsuchte Tabellen und Spalten |

Dieses Repository erhielt in der Zeit von Exponential 6 nur Metadaten zur Finanzierung.

## Verwandte Seiten

- [Chronik](../../../geschichte/erweiterungen/AdminAid.md) und [Versionshinweise](../../../../changelogs/extensions/AdminAid.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/AdminAid.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2024-01](../../../geschichte/erweiterungen/monate/2024-01.md), [2026-03](../../../geschichte/erweiterungen/monate/2026-03.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
