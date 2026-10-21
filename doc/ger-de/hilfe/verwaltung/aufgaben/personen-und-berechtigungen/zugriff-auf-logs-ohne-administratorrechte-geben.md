---
title: Zugriff auf Logs ohne Administratorrechte geben
audience: government, admin, developer
category: public-sector
order: 160
summary: Schritt für Schritt - einen Entwickler, Support-Ingenieur oder Betreiber Setup > Logdateien, die Fehlerübersicht und den Zustand der Logs mit maskierten personenbezogenen Daten lesen lassen, ohne vollen Zugriff, und getrennt über das Verwalten und das Aufheben der Maskierung entscheiden.
keywords: Zugriff auf Logs, Log-Viewer, setup/logs, setup/managelogs, setup/unmasklogs, maskierte Logs, Zugang für Entwickler, Support-Ingenieur, Fehlerübersicht, Zustand der Logs, minimale Rechte, log access, log viewer, masked logs, developer access, support engineer, error digest, log health, least privilege
type: help
---
# Zugriff auf Logs ohne Administratorrechte geben

Diese Aufgabe ist für Administratoren. Am Ende kann eine Kollegin oder ein Kollege die Logs der Installation in der
Administration lesen, maskiert, ohne Administrator zu sein. Die Rechte des Log-Viewers sind drei Funktionen des Moduls
`setup` ([Log-Viewer, „Wer ihn verwenden darf“](../../../../../eng-us/features/6.0/log-viewer.md#who-may-use-it)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Rechte wählen](#die-rechte-wählen)
3. [Die Rolle anlegen und zuweisen](#die-rolle-anlegen-und-zuweisen)
4. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Administratorkonto.
- Der Bedarf der Person: Lesen (die meisten Fälle), Rotieren oder Leeren von Logs (Betrieb) oder das Sehen unmaskierter
  E-Mail- und IP-Adressen (eine Untersuchung eines Vorfalls).

## Die Rechte wählen

| Funktion | Erlaubt | Geben Sie sie, wenn |
|---|---|---|
| `setup/logs` | **Setup > Logdateien** öffnen, verfolgen und durchsuchen, die Fehlerübersicht und den Zustand der Logs, einen Bereich herunterladen (wenn Downloads eingeschaltet sind) | Logs gelesen werden |
| `setup/managelogs` | **Jetzt rotieren**, eine Datei leeren, eine archivierte Rotation entfernen, jeweils nur, wenn in `logviewer.ini [ActionSettings]` eingeschaltet | Betriebsdienst |
| `setup/unmasklogs` | den Schalter **Unmaskiert zeigen** | eine Untersuchung, befristet |

E-Mail-Adressen, IP-Adressen, Passwörter, Token, Schlüssel, Sitzungs-IDs und Cookies sind standardmäßig maskiert;
geheime Werte von Einstellungen werden nie angezeigt. Jede Ansicht und jede Aktion wird auditiert.

## Die Rolle anlegen und zuweisen

1. **Benutzer > Rollen und Richtlinien > Neue Rolle** „Log reader“.
2. **Neue Richtlinie**: `setup` / `logs`, **Vollen Zugriff gewähren** für diese Funktion. Fügen Sie `setup` /
   `managelogs` nur hinzu, wenn oben gewählt.
3. **Neue Richtlinie**: `user` / `login`, SiteAccess: der Siteaccess der Administration (sofern keine andere Rolle es
   gibt).
4. **Speichern**, dann der Person oder ihrer Gruppe **Zuweisen**.

## Prüfen, ob es funktioniert hat

- Als die Person angemeldet: Das Setup-Menü zeigt **Logdateien**; **Fehlerübersicht** und **Zustand der Logs** öffnen
  sich.
- Zeilen zeigen maskierte Adressen (`203.0.x.x`); es gibt keinen Schalter **Unmaskiert zeigen**.
- Ohne `setup/managelogs` keine Schaltfläche **Jetzt rotieren**.

In der Shell werden Logs mit `./console exp:logs` gelesen, das keine Richtlinie prüft: Shell-Zugang ist eine getrennte
Entscheidung.

## Verwandte Seiten
- [Der Log-Viewer](../../../../../eng-us/help/government/operations/monitoring/the-log-viewer.md)
- [Die Fehlerübersicht lesen](../betrieb/die-fehleruebersicht-lesen.md)
- [Einem Prüfer Lesezugriff auf das Audit-Protokoll geben](einem-pruefer-lesezugriff-auf-das-audit-protokoll-geben.md)
- [Grundsätze der minimalen Rechte](../../../../../eng-us/help/government/operations/people-and-permissions/least-privilege-principles.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
