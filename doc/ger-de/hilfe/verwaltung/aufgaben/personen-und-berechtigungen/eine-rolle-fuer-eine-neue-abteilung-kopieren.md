---
title: Eine Rolle für eine neue Abteilung kopieren
audience: government, admin
category: public-sector
order: 180
summary: Schritt für Schritt - entscheiden, ob eine neue Abteilung eine Kopie einer Rolle braucht oder nur eine neue eingeschränkte Zuweisung derselben Rolle, und die Kopie sicher erstellen, wenn ihre Richtlinien wirklich abweichen.
keywords: Rolle kopieren, neue Abteilung, Kopieren, Kopie erstellen, Variante einer Rolle, Rollenzuweisung, Rolle wiederverwenden, Rolle duplizieren, copy role, new department, Copy, Make the copy, role variant, role assignment, reuse role, duplicate role
type: help
---
# Eine Rolle für eine neue Abteilung kopieren

Diese Aufgabe ist für Administratoren. Die meisten neuen Abteilungen brauchen **keine** neue Rolle: Dieselbe Rolle
„Department editor“, mit einer Einschränkung auf einen Teilbaum zugewiesen, genügt. Kopieren Sie eine Rolle nur, wenn
die Richtlinien der neuen Abteilung abweichen (andere Klassen, andere Funktionen). Die Aktion **Kopieren** ist in der
[Anleitung zu Rollen, Abschnitt 6](../../../../anleitungen/rollen-und-richtlinien.md#6-rollen-kopieren-und-entfernen) beschrieben.

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Zuerst fragen: kopieren oder zuweisen?](#zuerst-fragen-kopieren-oder-zuweisen)
3. [Die Kopie erstellen](#die-kopie-erstellen)
4. [Sie anpassen und zuweisen](#sie-anpassen-und-zuweisen)
5. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
6. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Konto mit dem Modul `role`. **Kopieren** ist für eine Rolle mit einer Richtlinie jenseits Ihres eigenen Zugangs
  ausgeschaltet.
- Die Gruppe und der Ordner der neuen Abteilung existieren.

## Zuerst fragen: kopieren oder zuweisen?

| Situation | Tun |
|---|---|
| Gleiche Arbeit, anderer Ordner | keine Kopie: **Mit Einschränkungen zuordnen**, Teilbaum, der bestehenden Rolle ([Eine Rolle mit einer Einschränkung auf einen Teilbaum zuweisen](eine-rolle-mit-einer-einschraenkung-auf-einen-teilbaum-zuweisen.md)) |
| Gleiche Arbeit, eine eigene Sektion | keine Kopie: **Mit Einschränkungen zuordnen**, Sektion |
| Andere Inhaltsklassen oder Funktionen | kopieren, dann die Kopie ändern |

Jede Kopie ist eine weitere Rolle, die überprüft werden muss; weniger Rollen sind leichter zu prüfen.

## Die Kopie erstellen

1. **Benutzer > Rollen und Richtlinien**; klicken Sie auf der Karte der Rolle auf **Kopieren**.
2. Eine Seite sagt, was die Kopie enthält. Klicken Sie auf **Kopie erstellen**. Eine Rolle „Copy of <Name>“ wird mit
   denselben Richtlinien und ohne Zuweisungen angelegt und öffnet sich im Editor. Das bloße Öffnen der Adresse der Kopie
   erstellt nichts.

## Sie anpassen und zuweisen

1. Benennen Sie sie um, zum Beispiel „Department editor - <Abteilung> (records)“.
2. Ändern Sie die Richtlinien, die abweichen (klicken Sie neben einer Richtlinie auf **Einschränkungen**, um sie zu
   ändern).
3. **Speichern**.
4. **Mit Einschränkungen zuordnen** an die Gruppe und den Ordner der Abteilung.

## Prüfen, ob es funktioniert hat

- Die Rollenliste zeigt die neue Rolle mit der Zahl ihrer Richtlinien und Zuweisungen.
- Melden Sie sich als Mitglied an und probieren Sie eine Aktion, die die Kopie hinzufügt, und eine, die sie wegnimmt.
- Halten Sie fest, warum die Kopie existiert, damit eine spätere Überprüfung sie nicht versehentlich wieder
  zusammenführt.

## Verwandte Seiten
- [Eine Redakteursrolle einer Abteilung für einen Teilbaum anlegen](eine-redakteursrolle-einer-abteilung-fuer-einen-teilbaum-anlegen.md)
- [Prüfen, was eine Rolle erlaubt](pruefen-was-eine-rolle-erlaubt.md)
- [Rollengestaltung: Redakteure der Abteilungen](../../betrieb/personen-und-berechtigungen/rollenentwurf-fachredaktionen.md)
- [Reihenfolge der Richtlinien einer Rolle](../../../../funktionen/6.0/reihenfolge-der-richtlinien-einer-rolle.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
