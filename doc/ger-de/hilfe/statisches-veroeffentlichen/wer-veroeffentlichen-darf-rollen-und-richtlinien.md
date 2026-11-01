---
title: "Wer veröffentlichen darf: Rollen und Richtlinien"
audience: admin, website-creator, project-manager
category: administration
order: 20
summary: Die vier Richtlinien des statischen Veröffentlichens - staticpublish/read, build, publish und setup - auf Wunsch je auf Ziele eingeschränkt, welche Schritte Bestätigen Sie, dass Sie es sind fragen und wie Sie einen Redakteur ein Ziel veröffentlichen lassen.
keywords: Statisches Veröffentlichen, Richtlinien, Rollen, Berechtigungen, Einschränkung nach Ziel, Bestätigen Sie, dass Sie es sind, erneute Anmeldung, Redakteur, Administrator, Auditprotokoll, static publishing, policies, roles, permissions, staticpublish/read, staticpublish/build, staticpublish/publish, staticpublish/setup, Target limitation, verify it's you, reauthentication, editor, Administrator, ReauthGroup_system, audit
type: help
---
# Wer veröffentlichen darf: Rollen und Richtlinien

Das statische Veröffentlichen hat vier Richtlinienfunktionen im Modul `staticpublish`. In einer neuen Installation hat
sie nur die Rolle Administrator. Jede nimmt die Einschränkung **Target** (Ziel), sodass eine Rolle nur für einige Ziele
zugelassen werden kann.

## Was Sie am Ende haben

Eine Rolle, mit der die richtigen Personen die richtigen Ziele bauen und veröffentlichen.

## Die Richtlinien

| Richtlinie | Erlaubt einem Benutzer | Seiten |
|---|---|---|
| `staticpublish/read` | die Ziele, ihre Builds, Vorschauen, ihren Verlauf und ihre Aufträge sehen; den Dashboard-Block | Statisches Veröffentlichen, Vorschau, Verlauf, die Seite eines Auftrags |
| `staticpublish/build` | ein Ziel bauen, einen Build verwerfen | Build, Verwerfen |
| `staticpublish/publish` | einen Build veröffentlichen, zu einer früheren Veröffentlichung zurücknehmen | Veröffentlichen, Zurücknehmen |
| `staticpublish/setup` | Ziele und ihren Deploy-Key anlegen und ändern, das statische Veröffentlichen ein- oder ausschalten | Neues Ziel, Einstellungen, Deploy-Key |

Ein neues Ziel anzulegen und der Schalter brauchen `staticpublish/setup` **ohne** Einschränkung nach Ziel: Eine
eingeschränkte Rolle kann nur die Ziele ändern, die sie nennt. Jede Seite braucht außerdem `staticpublish/read` für
ihr Ziel. Ein Benutzer ohne diese Richtlinie bekommt „Zugriff verweigert“, und der Menüeintrag und der Dashboard-Block
erscheinen nicht.

## Bestätigen Sie, dass Sie es sind

Diese Schritte fragen erneut nach dem Passwort (site.ini `[ReauthGroup_system]`): **Veröffentlichen**,
**Zurücknehmen**, **Speichern** im Assistenten, **Schlüssel erstellen** und der Schalter. Ansehen, Bauen, die Vorschau
und Verwerfen fragen nie.

## Einen Redakteur ein Ziel veröffentlichen lassen

1. **Rollen und Richtlinien** > die Rolle der Redakteure > **Neue Richtlinie**.
2. Modul `staticpublish`, Funktion `read`, Einschränkung **Target**: das Ziel. Speichern.
3. Dasselbe für `build` und `publish`.

Der Redakteur sieht jetzt nur dieses Ziel und kann es bauen und veröffentlichen, aber nicht seine Einstellungen ändern.

## Die Aufzeichnung

Jede Veröffentlichung, jede Rücknahme, jedes gespeicherte Ziel und jeder Deploy-Key steht im Auditprotokoll
(`system.staticpublish.*`), mit dem, der es getan hat. Zugangsdaten werden nur mit ihrem Namen aufgezeichnet, nie mit
ihrem Wert.

## Verwandte Seiten

- [Statisches Veröffentlichen](README.md)
- [Build, Vorschau und Veröffentlichen](build-vorschau-und-veroeffentlichen.md)
- [Verlauf und Rücknahme](verlauf-und-ruecknahme.md)

## Nächster Schritt

[Legen Sie Ihr erstes Ziel mit dem Einrichtungsassistenten an](der-einrichtungsassistent.md).
