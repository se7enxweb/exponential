---
title: Veröffentlichte Informationen zurücknehmen und berichtigen
audience: government, editor, admin
category: public-sector
order: 180
summary: Wie Sie in Exponential Informationen berichtigen oder zurücknehmen, die eine öffentliche Stelle veröffentlicht hat, sodass die Öffentlichkeit sieht, was sich geändert hat, und die Aufzeichnung vollständig bleibt - Berichtigungen mit Hinweisen, Verbergen statt Entfernen, der Papierkorb und der Sonderfall versehentlich veröffentlichter Daten.
keywords: Berichtigung, zurücknehmen, widerrufen, Erratum, verbergen, verstecken, wieder anzeigen, Papierkorb, entfernen, versehentlich veröffentlicht, Rechenschaft, Transparenz, Hinweis, correction, withdraw, retract, hide, unhide, trash, remove, published by mistake, accountability, transparency, notice
type: help
---
# Veröffentlichte Informationen zurücknehmen und berichtigen

Diese Seite richtet sich an Redaktionen und Administratoren, die etwas korrigieren oder zurücknehmen müssen, das die
Stelle veröffentlicht hat. Transparenz hängt davon ab, dass Berichtigungen sichtbar sind und die Aufzeichnung
vollständig bleibt. Exponential bietet das Bearbeiten mit Versionen, das Verbergen und das Entfernen in den
Papierkorb; jedes hat eine andere Wirkung
([Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md)).

## Inhalt

1. [Die richtige Aktion wählen](#die-richtige-aktion-wählen)
2. [Mit einem sichtbaren Hinweis berichtigen](#mit-einem-sichtbaren-hinweis-berichtigen)
3. [Durch Verbergen zurücknehmen](#durch-verbergen-zurücknehmen)
4. [Entfernen und der Papierkorb](#entfernen-und-der-papierkorb)
5. [Versehentlich veröffentlicht](#versehentlich-veröffentlicht)

## Die richtige Aktion wählen

| Situation | Aktion | Adresse | Aufzeichnung |
|---|---|---|---|
| Ein sachlicher Fehler | Bearbeiten, einen datierten Berichtigungshinweis hinzufügen | bleibt | alte Version in der Geschichte |
| Durch ein neueres Dokument überholt | Bearbeiten: „Ersetzt durch“ mit einem Link hinzufügen | bleibt | vollständig |
| Darf nicht mehr öffentlich sein, ist aber Schriftgut | Verbergen | wird Besuchern nicht mehr angezeigt | Inhalt und Geschichte bleiben |
| Versehentlich angelegt, nie relevant | Entfernen (in den Papierkorb) | endet | im Papierkorb, bis er geleert wird |
| Personenbezogene oder vertrauliche Daten veröffentlicht | Ersetzen, alte Versionen entfernen, melden | bleibt | siehe unten |

## Mit einem sichtbaren Hinweis berichtigen

1. Öffnen Sie den Eintrag, **Bearbeiten**.
2. Berichtigen Sie den Text oder ersetzen Sie die Datei.
3. Fügen Sie am Ende eine Zeile hinzu (oder in einem Feld „Berichtigungen“ Ihrer Klasse): „Berichtigt am <Datum>:
   <was sich geändert hat>.“
4. **Zur Veröffentlichung schicken**.

Die vorherige Version bleibt für die Mitarbeitenden in der Versionsgeschichte, und **Zwei Versionen vergleichen** zeigt
die Änderung (siehe „Versionsgeschichte als Rechenschaft“).

## Durch Verbergen zurücknehmen

Das Verbergen nimmt einen Eintrag von der öffentlichen Website, ohne ihn zu entfernen: im Baum über das Menü des
Eintrags, **Fortgeschritten** > **Verstecken / Zeigen**; oder in einer Liste **Unterelemente** Einträge markieren und
**Weitere Aktionen** > **Auswahl verstecken**. Versteckte Einträge sind in der Administration gekennzeichnet und können
wieder angezeigt werden. Bevor ein Eintrag versteckt wird, auf den andere Objekte verweisen, listet Exponential diese
Verweise zuerst auf (`content.ini [HideSettings] ReverseRelationCheck=warn`).

Wenn Besucher trotzdem erfahren sollen, dass der Eintrag existiert hat, veröffentlichen Sie an passender Stelle eine
kurze Hinweisseite, statt ihn oder zusätzlich zum Verbergen.

## Entfernen und der Papierkorb

Das Entfernen verschiebt Inhalte in den Papierkorb, wo sie wiederhergestellt werden können, bis der Papierkorb geleert
wird ([Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md)); das Leeren löscht den Inhalt
und seine Geschichte. Das Prüfprotokoll zeichnet das Entfernen und das endgültige Löschen auf
([Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md)). Für aktenrelevante Inhalte beschränken Sie `content/remove` auf
Administratoren und bevorzugen das Verbergen.

## Versehentlich veröffentlicht

Wenn personenbezogene Daten oder vertrauliches Material öffentlich wurden:

1. Handeln Sie sofort: Verbergen Sie den Eintrag oder ersetzen Sie die Datei und veröffentlichen Sie.
2. Entfernen Sie auf der Seite **Versionen** des Eintrags die Versionen, die das Material noch enthalten (Richtlinie
   `content/versionremove`).
3. Leeren Sie die Caches vor der Website ([Caches](../../../anleitungen/caches.md)).
4. Informieren Sie Ihre Datenschutz- oder Informationssicherheitsbeauftragten; sie entscheiden über Meldungen.
5. Prüfen Sie die Anfrageprotokolle und, wenn Lesezugriffe aufgezeichnet werden, den Audit-Kanal `read` auf Downloads
   ([Anfrageprotokolle](../../../../eng-us/features/6.0/request-logs.md)).
6. Veröffentlichen Sie die berichtigte Version mit einem neutralen Hinweis, wenn der Eintrag öffentlich bleiben muss.

## Verwandte Seiten

- [Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md)
- [Schwärzen ist Handarbeit](schwaerzen-ist-handarbeit.md)
- [Versionsgeschichte als Rechenschaft](versionsgeschichte-als-rechenschaft.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
