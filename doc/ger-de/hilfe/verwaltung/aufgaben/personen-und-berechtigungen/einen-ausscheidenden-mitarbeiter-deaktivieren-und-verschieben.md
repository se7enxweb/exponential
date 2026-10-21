---
title: Einen ausscheidenden Mitarbeiter deaktivieren und verschieben
audience: government, admin
category: public-sector
order: 60
summary: Schritt für Schritt - Deaktivieren und verschieben einschalten, die versteckte Gruppe für deaktivierte Konten anlegen und das Konto eines ausscheidenden Mitarbeiters umkehrbar stilllegen, sodass seine Inhalte, Versionen und sein Verlauf im Audit erhalten bleiben.
keywords: Deaktivieren und verschieben, ausscheidender Mitarbeiter, Konto stilllegen, Deaktivierte Benutzer, versteckte Gruppe, Reaktivieren und zurückverschieben, DeactivateAndMove, ezcreatedeactivatedusergroup, Offboarding, deactivate and move, leaver, retire account, deactivated users, hidden group, reactivate and move back, offboarding
type: help
---
# Einen ausscheidenden Mitarbeiter deaktivieren und verschieben

Diese Aufgabe ist für Administratoren. Am Ende ist das Konto eines ausscheidenden Mitarbeiters deaktiviert, und alle
seine Orte liegen in einer versteckten Gruppe ohne Rollen; wo es war, wird gespeichert, sodass
**Reaktivieren und zurückverschieben** es wiederherstellen kann. Nichts wird entfernt. Die Funktion ist vollständig in
[Deaktivieren und verschieben](../../../../funktionen/6.0/benutzerpflege.md#deaktivieren-und-verschieben) beschrieben.

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Funktion einschalten](#die-funktion-einschalten)
3. [Die Gruppe vorab anlegen](#die-gruppe-vorab-anlegen)
4. [Deaktivieren und verschieben](#deaktivieren-und-verschieben)
5. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
6. [Reaktivieren](#reaktivieren)
7. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Funktion `user/deactivate` (in einer frischen Installation nur Administrator; eine Rolle mit `user/*` erhält sie
  ebenfalls).
- `content/edit` für das Konto und die Rechte an Inhalten, um es zu verschieben (siehe die Seite der Funktion, „Wer es
  verwenden darf“).
- Shell-Zugang für die Einstellungen, sofern Ihre Einstellungen nicht vom Deployment verwaltet werden (dann über die
  Versionsverwaltung).

## Die Funktion einschalten

Sie ist standardmäßig ausgeschaltet (`site.ini [UserSettings] DeactivateAndMove=disabled`). In
`settings/override/site.ini.append.php` (oder `site.ini.append.php` des Siteaccess der Administration):

```ini
[UserSettings]
DeactivateAndMove=enabled
```

Leeren Sie dann den INI-Cache:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

## Die Gruppe vorab anlegen

Das erste **Deaktivieren und verschieben** legt die Gruppe „Deactivated users“ unter dem Knoten Benutzer an, wenn der
Handelnde dort Benutzergruppen anlegen darf. Um sie vorab anzulegen, als Teil einer Änderung:

```bash
php bin/php/ezcreatedeactivatedusergroup.php -s <admin siteaccess>            # dry run: what it would do
php bin/php/ezcreatedeactivatedusergroup.php -s <admin siteaccess> --commit   # creates it
```

Die Optionen `--name="Former staff"` und `--section-id=N` geben ihr einen anderen Namen oder eine andere Sektion. Das
Skript gibt die Knoten-ID und die Zeilen für die Einstellungen aus; es schreibt niemals eine INI-Datei. Um eine eigene
Gruppe zu verwenden, setzen Sie `DeactivatedUserGroupNodeID=<node id>`.

## Deaktivieren und verschieben

1. Öffnen Sie die Kontoseite des ausscheidenden Mitarbeiters (sein Knoten im Benutzerbaum) und klicken Sie auf
   **Deaktivieren und verschieben**, oder markieren Sie es unter **Benutzer > Benutzerkonten prüfen** und verwenden Sie
   **Angekreuzte Konten deaktivieren und verschieben**.
2. Die Bestätigungsseite (`user/deactivate`) listet das Konto mit seinen aktuellen Gruppen und wohin es kommt.
3. Klicken Sie auf **Bestätigen**. In einer Transaktion wird das Konto deaktiviert (seine Sitzungen enden), seine
   anderen Orte werden entfernt und sein Hauptort wird in die Gruppe verschoben.

Das Audit-Protokoll zeichnet `access.user.deactivate_move` mit den Orten vorher und nachher auf.

## Prüfen, ob es funktioniert hat

- **Benutzerkonten prüfen**: Das Konto ist **Deaktiviert** und trägt in seinen Gruppen das Kennzeichen *deaktiviert*.
- Das Konto erscheint nicht in den Benutzerlisten der Site (die Gruppe ist versteckt).
- Der Gruppe ist keine Rolle zugewiesen: **Zuweisen** auf einer Seite einer Rolle lässt die Gruppe aus und sagt es.

## Reaktivieren

Wenn die Person zurückkehrt: auf der Kontoseite **Reaktivieren und zurückverschieben**, dann **Bestätigen**. Das Konto
wird aktiviert und zu seinem gespeicherten übergeordneten Hauptelement zurückverschoben (oder nach
`[UserSettings] DefaultUserPlacement`, wenn es das nicht mehr gibt). Aufgezeichnet als `access.user.reactivate_move`.

## Verwandte Seiten
- [Einen ausscheidenden Mitarbeiter entfernen](einen-ausscheidenden-mitarbeiter-entfernen.md)
- [Eintritte, Wechsel und Austritte](../../betrieb/personen-und-berechtigungen/eintritt-wechsel-austritt.md)
- [Pflege der Benutzerkonten](../../betrieb/personen-und-berechtigungen/pflege-von-benutzerkonten.md)
- [Anleitung zu Rollen und Richtlinien, Abschnitt 7](../../../../anleitungen/rollen-und-richtlinien.md#7-nicht-aktivierte-benutzer-aufräumen)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
