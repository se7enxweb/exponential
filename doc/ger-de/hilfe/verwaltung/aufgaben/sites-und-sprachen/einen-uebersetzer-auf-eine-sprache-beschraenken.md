---
title: Einen Übersetzer auf eine Sprache beschränken
audience: government, admin
category: public-sector
order: 80
summary: Aufgabe für Administratoren - eine Rolle anlegen, deren Inhaltsrichtlinien durch Sprache eingeschränkt sind, sie einer Übersetzergruppe zuweisen und mit einem Testkonto prüfen.
keywords: Übersetzerrolle, Einschränkung Sprache, Rollen und Richtlinien, Benutzergruppe, Berechtigungen, translator role, Language limitation, content/translate, content/edit, roles and policies, user group, permissions
type: help
---
# Einen Übersetzer auf eine Sprache beschränken

Diese Aufgabe richtet sich an Administratoren. Hintergrund:
[Rollen und Berechtigungen für Übersetzer](../../sprachen/rollen-und-rechte-fuer-uebersetzer.md).

## Bevor Sie beginnen

Die Inhaltssprache existiert; Sie wissen, in welchen Sektionen oder Teilbäumen der Übersetzer arbeitet.

## Schritte

1. **Benutzer > Rollen und Richtlinien** (`/role/list`): Legen Sie eine Rolle „Übersetzer Französisch“ an.
2. Fügen Sie Richtlinien hinzu:
   - `content/read` (eingeschränkt auf die Sektionen, die übersetzt werden);
   - `content/edit` mit **Sprache** = Französisch (und der Sektion);
   - `content/translate` mit **Sprache** = Französisch;
   - `content/versionread` (die Sektion);
   - `user/login` für den Siteaccess der Administration.
3. Speichern Sie die Rolle. Legen Sie die Benutzergruppe „Übersetzer Französisch“ an und weisen Sie ihr die Rolle zu.
4. Legen Sie die Konten der Übersetzer in die Gruppe.

## Prüfen

Melden Sie sich mit einem Testkonto der Gruppe an (nicht mit dem Konto einer echten Person): Die Liste der
Bearbeitungssprachen bietet nur Französisch an; das Bearbeiten der englischen Version wird verweigert.

## Probleme

| Problem | Lösung |
|---|---|
| Der Übersetzer kann keine Übersetzung hinzufügen, nur vorhandene bearbeiten | `content/translate` (oder `content/create`) fehlt für die Sprache |
| Der Übersetzer sieht nichts | `content/read` ist auf die falschen Sektionen eingeschränkt |

## Verwandte Seiten

- [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
