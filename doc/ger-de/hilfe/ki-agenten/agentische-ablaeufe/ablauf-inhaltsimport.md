---
title: Ein Ablauf für den Inhaltsimport
audience: ai-agents, developer, app-creator, admin
category: ai-agents
order: 68
summary: Ein durchgearbeitetes Beispiel für einen agentischen Import - ein Agent bildet einen externen Feed auf eine Inhaltsklasse ab und schreibt ihn über die REST-API v2 in Exponential - mit einem eigenen Konto, beschränkt auf einen Teilbaum, einem Schlüssel nur mit den nötigen Geltungsbereichen, einer Testinstallation, einem Menschen, der den ersten Stapel prüft, einer geprüften Sektion und einer Kontrolle des Audit-Trails.
keywords: Inhaltsimport, Feed-Import, agentischer Ablauf, REST v2 create, Geltungsbereich publish, Importkonto, Teilbaum-Einschränkung, Testinstallation, Audit-Trail, Prüfung, expservices, content import, feed import, agentic workflow, publish scope, import account, subtree limitation, test installation, audit trail, review
type: help
---
# Ein Ablauf für den Inhaltsimport

Diese Seite richtet sich an Entwickler, die einen Agenten Inhalte importieren lassen, zum Beispiel Einträge aus dem Feed eines
Partners, in einen Ordner der Website. Sie zeigt einen Ablauf, in dem jeder Schreibzugriff begrenzt, geprobt und geprüft ist.

## Inhalt

1. [Die Aufgabe](#die-aufgabe)
2. [Das Konto vorbereiten](#das-konto-vorbereiten)
3. [Die Schnittstelle wählen](#die-schnittstelle-wählen)
4. [Auf einer Testinstallation proben](#auf-einer-testinstallation-proben)
5. [Ihn ausführen](#ihn-ausführen)
6. [Ihn prüfen](#ihn-prüfen)
7. [Varianten](#varianten)

## Die Aufgabe

Ein externer Feed listet Veranstaltungen. Der Agent liest jeden Eintrag, bildet ihn auf die Felder der Klasse `event` ab (Titel,
Datum, Zusammenfassung, Textkörper) und legt je neuem Eintrag ein Objekt in einem Ordner "Partner-Veranstaltungen" an.

Der Feed ist **nicht vertrauenswürdiger Text**: Seine Felder können alles enthalten
([Nicht vertrauenswürdige Daten und Prompt-Injection](nicht-vertrauenswuerdige-daten-und-prompt-injection.md)).

## Das Konto vorbereiten

1. Legen Sie ein Konto für den Import an, in einer eigenen Benutzergruppe.
2. Geben Sie seiner Rolle `content/read` und `content/create`, beschränkt auf die Klasse `event` und den Teilbaum von
   "Partner-Veranstaltungen". Fügen Sie dort `content/remove` nur hinzu, wenn der Import Einträge entfernen soll.
3. Prüfen Sie das Ergebnis vor jedem Schreibzugriff. `exp:access:check` antwortet für read, edit, remove und die anderen Funktionen
   eines bestehenden Knotens, zum Beispiel, dass das Konto den Ordner lesen, aber andere Seiten nicht bearbeiten darf:

   ```bash
   ./console exp:access:check --user=<import login> --node=<Partner events node id>
   ./console exp:access:check --user=<import login> --node=<another page> --function=edit
   ```

   Prüfen Sie das Recht zum Anlegen selbst auf der Testinstallation, indem Sie als dieses Konto einen Eintrag anlegen
   ([Rollen und minimale Rechte](../steuerung/rollen-und-minimale-rechte.md)).
4. Erstellen Sie, als dieses Konto angemeldet, einen persönlichen API-Schlüssel mit dem Geltungsbereich `publish` (und `read`) und
   einer kurzen Laufzeit ([Ein nur lesender API-Schlüssel, Schritt für Schritt](ein-nur-lesender-api-schluessel-schritt-fuer-schritt.md) zeigt dieselben
   Schritte für `read`).

## Die Schnittstelle wählen

| Schnittstelle | Passt, wenn | Hinweis |
|---|---|---|
| REST v2 `content/node/create` mit dem Schlüssel des Importkontos | Der Agent läuft irgendwo; Einträge werden nur hinzugefügt | Anlegen und veröffentlichen ([Agenten und die REST-API](agenten-und-die-rest-api.md#über-rest-schreiben)) |
| expservices mit einem Token des Importkontos | Sie brauchen auch Dienste über Inhalte hinaus | Prüfen Sie den Katalogeintrag und das Merkmal `write` jedes Dienstes ([Agenten und expservices](agenten-und-expservices.md)) |

Die Schritte unten verwenden REST v2.

## Auf einer Testinstallation proben

Lassen Sie einen neuen Import nie zuerst in die Produktion schreiben. Verwenden Sie eine Kopie oder eine frische Testinstallation,
führen Sie dort den ganzen Ablauf aus, lesen Sie die Ergebnisse, wie ein Redakteur es täte, und richten Sie den Agenten erst dann
auf die Produktion.

## Ihn ausführen

1. **Abbilden**: Der Agent liest die Klassendefinition aus der Dokumentation oder einem Beispielobjekt (`GET` auf eine bestehende
   Veranstaltung mit `ResponseGroups=Fields`) und bildet Feedfelder auf Attributbezeichner ab. Ihr Code, nicht das Modell, prüft,
   dass jeder Bezeichner existiert.
2. **Im Code prüfen**: Daten lassen sich parsen, Pflichtfelder sind vorhanden, Text ist schlicht oder erlaubtes Markup, Links
   verwenden http(s). Das Modell schlägt vor; Ihr Code entscheidet.
3. **Ein Mensch liest den ersten Stapel**: einige abgebildete Einträge als reine Daten, bevor etwas gesendet wird.
4. **Schreiben**: ein `POST /api/ezp/v2/content/node/create` je Eintrag, mit `parentNodeID`, `classIdentifier`, `languageLocale` und
   den Attributen. Halten Sie die neuen Knoten-IDs fest.
5. **Sich merken, was importiert wurde**: Bewahren Sie die Feed-ID mit der neuen Knoten-ID in Ihrem eigenen Speicher auf, sodass
   ein zweiter Lauf nur neue Einträge anlegt.

## Ihn prüfen

- Sehen Sie im Audit-Trail des Kernels nach den Aktionen des Importkontos ([Der Audit-Trail für KI](../steuerung/audit-trail-fuer-ki.md)).
- Öffnen Sie einige Einträge in der Administration, wie ein Redakteur.
- Vergleichen Sie Zahlen: Einträge im Feed, gesendete Einträge, Antworten `201`, Objekte im Ordner.

## Varianten

- **Menschen müssen importierte Einträge freigeben**: Importieren Sie in eine Sektion, die Besucher nicht sehen und die ein
  Freigabe-Workflow abdeckt ([Einen Menschen beteiligt halten](einen-menschen-beteiligt-halten.md)).
- **Aktualisierungen und Entfernungen**: Geben Sie dem Konto `content/edit` oder `content/remove` nur für den Ordner, verwenden Sie
  die passenden Geltungsbereiche des Schlüssels und proben Sie sie getrennt.
- **Große Importe**: Senden Sie in kleinen Stapeln und beenden Sie den Lauf beim ersten `401` oder `403`; sie bedeuten, dass ein
  Mensch hinsehen muss.

## Verwandte Seiten

- [Eine Schnittstelle für einen Agenten wählen](eine-schnittstelle-waehlen.md)
- [Inhalte im Code anlegen](../../ersteller/anwendungsentwickler/inhalte-im-code-anlegen.md)
- [Bevor Sie einen Agenten verbinden](../checklisten/bevor-sie-einen-agenten-verbinden.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
