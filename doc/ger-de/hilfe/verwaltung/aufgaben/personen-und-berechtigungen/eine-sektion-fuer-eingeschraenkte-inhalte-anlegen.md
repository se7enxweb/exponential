---
title: Eine Sektion für eingeschränkte Inhalte anlegen
audience: government, admin
category: public-sector
order: 260
summary: Schritt für Schritt - eine Sektion wie „Intern“ anlegen, bestehende Inhalte mit Inhalt zuweisen hineinlegen und nur den richtigen Mitarbeitern Lesezugriff geben, sodass Besucher sie nie sehen.
keywords: Sektion, eingeschränkte Inhalte, interne Inhalte, neue Sektion, Inhalt zuweisen, section/assign, Besucher, Rolle Anonymous, Intranet, vertrauliche Seiten, section, restricted content, internal content, new section, Assign content, visitors, Anonymous role, intranet, confidential pages
type: help
---
# Eine Sektion für eingeschränkte Inhalte anlegen

Diese Aufgabe ist für Administratoren. Am Ende sind Inhalte in einer neuen Sektion, zum Beispiel „Intern“, für
Besucher unsichtbar und nur für die Mitarbeiterrollen lesbar, die Sie wählen. Die Seiten der Sektionen sind in
[Sektionen](../../../../anleitungen/sektionen.md) beschrieben.

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Die Sektion anlegen](#die-sektion-anlegen)
3. [Inhalte hineinlegen](#inhalte-hineinlegen)
4. [Mitarbeitern Lesezugriff geben](#mitarbeitern-lesezugriff-geben)
5. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
6. [Fallstricke](#fallstricke)
7. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- `section/edit` (zum Anlegen) und `section/assign` (zum Hineinlegen von Inhalten); Administratoren haben beides.
- Wissen Sie, welche Mitarbeiterrollen die Inhalte lesen müssen.

Eine Sektion ist keine Verschlüsselung und kein getrenntes System: Inhalte darin sind nur durch Rollen geschützt.
Informationen, deren Einstufung einen öffentlichen Webserver verbietet, gehören überhaupt nicht darauf.

## Die Sektion anlegen

1. **Setup > Sektionen** (`/section/list`), **Neue Sektion**.
2. **Name**: `Internal`. **Bezeichner** wird aus dem Namen ausgefüllt (`internal`); er muss mit einem Buchstaben
   beginnen und darf nur Buchstaben, Ziffern und `_` enthalten.
3. **Navigationsbereich**: Lassen Sie **Inhaltsstruktur** stehen.
4. **Sektion anlegen**. Die Liste sagt „Die Sektion Internal wurde angelegt.“

Die neue Sektion ist leer und, weil die Rolle Anonymous nur die Sektion Standard liest, für Besucher bereits
unsichtbar ([Sektionen, Abschnitt 2](../../../../anleitungen/sektionen.md#2-die-sektionen-einer-neuen-installation)).

## Inhalte hineinlegen

1. Auf der Karte der Sektion **Inhalt zuweisen**.
2. Wählen Sie im Inhaltsbrowser das oberste Element der internen Inhalte und klicken Sie auf **Auswählen**.
3. Für einen großen Teilbaum (ab `content.ini [ContentJobSettings] SynchronousLimit`, standardmäßig 50 Elemente)
   wählen Sie, ob es jetzt oder im Hintergrund als Inhaltsauftrag geschieht.
4. Das Element und alles darunter, dessen Hauptort dort liegt, erhalten die Sektion. Neue Inhalte, die darunter
   angelegt werden, übernehmen die Sektion ihres übergeordneten Elements.

## Mitarbeitern Lesezugriff geben

Für jede Mitarbeiterrolle, die die Inhalte lesen muss: **Benutzer > Rollen und Richtlinien**, die Rolle,
**Bearbeiten**, die Richtlinie `content/read`, **Einschränkungen**, markieren Sie „Internal“ in der Liste der Sektionen
neben den bestehenden Sektionen, **OK**, **Speichern**. Bearbeitungsrechte funktionieren auf dieselbe Weise.

## Prüfen, ob es funktioniert hat

- Öffnen Sie in einem privaten Fenster ohne Anmeldung die Adresse einer internen Seite: Sie wird nicht angezeigt.
- Als Mitarbeiter mit der Rolle angemeldet: Sie wird angezeigt.
- Die Seite der Sektion listet die Rollen, die sie verwenden.

## Fallstricke

- Zusätzliche Orte: Ein Objekt hat überall, wo es erscheint, eine Sektion, sodass ein internes Objekt, das in einen
  öffentlichen Ordner verlinkt ist, intern bleibt (gut), aber ein öffentliches Objekt, das in den internen Ordner
  verlinkt ist, öffentlich bleibt.
- Suchergebnisse und Menüs folgen den Leserechten, sodass Besucher interne Elemente darin nicht sehen.
- Das spätere Entfernen der Sektion ist nur möglich, wenn nichts sie verwendet, und lässt sich nicht rückgängig machen.

## Verwandte Seiten
- [Besucher eine neue Sektion lesen lassen](besucher-eine-neue-sektion-lesen-lassen.md)
- [Eine Rolle mit einer Einschränkung auf eine Sektion zuweisen](eine-rolle-mit-einer-einschraenkung-auf-eine-sektion-zuweisen.md)
- [Sektionen und Einschränkungen auf Teilbäume](../../../../../eng-us/help/government/operations/people-and-permissions/sections-and-subtree-limits.md)
- [Inhaltsaufträge](../../../../../eng-us/features/6.0/content-jobs.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
