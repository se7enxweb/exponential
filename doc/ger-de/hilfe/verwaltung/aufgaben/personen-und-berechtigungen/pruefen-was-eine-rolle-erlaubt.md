---
title: Prüfen, was eine Rolle erlaubt
audience: government, admin, project-manager
category: public-sector
order: 310
summary: Schritt für Schritt - die Seite einer Rolle lesen, um jede Richtlinie als Satz zu sehen, wen sie erreicht und mit welchen Einschränkungen, gefährliche Kennzeichen und tote Einschränkungen erkennen und sie mit einem Testkonto testen.
keywords: Rolle, Richtlinien, Rolle prüfen, Seite der Rolle, Sätze der Richtlinien, Voller Zugriff, Kann Rollen ändern, ohne Auswertung verweigert, Betroffene Benutzer, Testkonto, Überprüfung des Zugangs, role, policies, check role, role page, policy sentences, Full access, Can change roles, no handler denies, users affected, test account, access review
type: help
---
# Prüfen, was eine Rolle erlaubt

Diese Aufgabe ist für Administratoren und für Führungskräfte, die eine Rolle abzeichnen müssen. Am Ende wissen Sie
genau, was eine Rolle ihren Inhabern zu tun erlaubt und für wen sie gilt. Die Seite der Rolle ist in der
[Anleitung zu Rollen, Abschnitt 2](../../../../anleitungen/rollen-und-richtlinien.md#2-eine-rolle-lesen) beschrieben.

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Seite der Rolle lesen](#die-seite-der-rolle-lesen)
3. [Warnzeichen](#warnzeichen)
4. [Sie testen](#sie-testen)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Konto mit dem Modul `role` (oder ein Administrator, der Ihnen die Seite zeigt).
- Ein Testkonto, das Sie verwenden dürfen, nicht das Konto einer echten Person.

## Die Seite der Rolle lesen

1. **Benutzer > Rollen und Richtlinien**, klicken Sie auf die Rolle.
2. **Die Zahlen**: Richtlinien, Zuweisungen, Benutzergruppen, direkt zugewiesene Benutzer und
   **Betroffene Benutzer insgesamt** (jeder Benutzer, den die Rolle erreicht, Gruppenmitglieder einmal gezählt). Die
   Zeile darunter sagt, wie viele Zuweisungen eingeschränkt sind.
3. **Die Richtlinien als Sätze**, zum Beispiel *Darf Inhalte lesen, in der Sektion Standard, der Klassen Article und
   Folder*. Unter jedem Satz: die ID der Richtlinie, ihr Modul und ihre Funktion und die Einschränkungen, wie sie
   gespeichert sind. Sortieren Sie die Liste nach Modul, um sie zu gruppieren.
4. **Die Benutzer und Gruppen**, mit ihrer Einschränkung (*Nur in der Sektion ...*, *Nur im Teilbaum ...*). Zuweisungen,
   deren Benutzer oder Gruppe entfernt wurde, werden zuerst gelistet.

Kopieren Sie die Sätze in Ihre Dokumentation der Rolle
([Rollen, Richtlinien und Einschränkungen](../../betrieb/personen-und-berechtigungen/rollen-richtlinien-und-einschraenkungen.md#zugriff-für-ein-audit-dokumentieren)).

## Warnzeichen

| Sie sehen | Bedeutet | Tun |
|---|---|---|
| *Voller Zugriff* (rot) | eine Richtlinie über jedes Modul | nur für namentlich benannte Administratoren |
| *Kann Rollen ändern* (gelb) | eine Richtlinie über das Modul `role` | nur für Administratoren |
| eine Richtlinie mit jeder Funktion eines Moduls | jede Funktion dieses Moduls, auch künftige | stattdessen die benötigten Funktionen auflisten |
| *(ohne Auswertung, verweigert)* nach einer Einschränkung | keine Erweiterung wertet sie aus; die Richtlinie gibt nichts | die Erweiterung aktivieren oder die Richtlinie entfernen |
| *Nicht zugewiesen* | gibt niemandem etwas | entfernen oder festhalten, warum sie bleibt |
| eine Zuweisung ohne Einschränkung, wo Sie eine erwartet haben | die Rolle gilt überall | entfernen und erneut mit einer Einschränkung zuweisen |

Denken Sie daran: Ein Benutzer hat die Vereinigung aller Rollen aller seiner Gruppen. Die Prüfung einer Rolle sagt
nicht, was eine Person tun kann; die Zeile der Person unter **Benutzer > Benutzerkonten prüfen** listet jede Rolle, die
sie hat.

## Sie testen

1. Nehmen Sie das Testkonto in die Gruppe auf, die die Rolle hat (und in nichts anderes).
2. Melden Sie sich damit in einem privaten Fenster an.
3. Probieren Sie je Bereich der Richtlinien eine erlaubte und eine verbotene Aktion. Eine verbotene Ansicht wird
   abgelehnt und als `access.permission.refused` aufgezeichnet.
4. Nehmen Sie das Testkonto wieder aus der Gruppe heraus.

## Verwandte Seiten
- [Prüfen, wer Administratorrechte hat](pruefen-wer-administratorrechte-hat.md)
- [Grundsätze der minimalen Rechte](../../betrieb/personen-und-berechtigungen/prinzip-der-geringsten-rechte.md)
- [Reihenfolge der Richtlinien einer Rolle](../../../../funktionen/6.0/reihenfolge-der-richtlinien-einer-rolle.md)
- [Template-Operatoren für Rollen und Richtlinien](../../../../funktionen/6.0/template-operatoren-fuer-rollen-und-richtlinien.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
