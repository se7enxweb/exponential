---
title: Massenhafter Inhaltsfehler
audience: government, admin, editor, project-manager
category: public-sector
order: 100
summary: Runbook für einen großen Fehler im Inhalt - ein entfernter Teilbaum, eine falsche Sektion oder ein falscher Zustand auf vielen Seiten, ein misslungener Massenimport, eine zu früh veröffentlichte Ankündigung - mit dem Papierkorb, dem Versionsverlauf, Inhaltsjobs, dem Audit-Trail und als letztem Mittel einer Wiederherstellung der Datenbank.
keywords: Massenfehler, Massenänderung, entfernter Teilbaum, Wiederherstellung aus dem Papierkorb, falsche Sektion, falscher Zustand, Importfehler, Massenlöschung, Warnung mass_delete, Versionsverlauf, Inhaltsjobs, Datensicherung wiederherstellen, Vorfall, mass error, bulk mistake, removed subtree, trash restore, wrong section, wrong state, import error, mass delete, version history, content jobs, restore backup, incident
type: help
---
# Massenhafter Inhaltsfehler

Dieses Runbook richtet sich an die Webverantwortlichen und die Administration, wenn viele Seiten auf einmal fehlerhaft
wurden: Eine Redakteurin oder ein Redakteur hat einen ganzen Ordner entfernt, eine Sektion oder ein Zustand wurde auf
einen großen Teilbaum gesetzt, ein Import hat Seiten überschrieben, oder etwas wurde vor seiner Zeit veröffentlicht.
Verhindern Sie zuerst weiteren Schaden und verwenden Sie dann das genaueste Werkzeug, das den Fehler rückgängig macht.

## Inhalt
1. [Den Schaden stoppen](#den-schaden-stoppen)
2. [Genau herausfinden, was passiert ist](#genau-herausfinden-was-passiert-ist)
3. [Rückgängig machen, mit dem genauesten Werkzeug zuerst](#rückgängig-machen-mit-dem-genauesten-werkzeug-zuerst)
4. [Das letzte Mittel: die Datenbank wiederherstellen](#das-letzte-mittel-die-datenbank-wiederherstellen)
5. [Die Beteiligten informieren](#die-beteiligten-informieren)
6. [Verwandte Seiten](#verwandte-seiten)

## Den Schaden stoppen

- Wenn falsche Inhalte öffentlich und heikel sind (eine Ankündigung unter Sperrfrist, personenbezogene Daten),
  verbergen Sie das Element sofort (`content/hide`) oder schalten Sie den Wartungsmodus für die ganze Website ein.
- Wenn ein Inhaltsjob noch läuft (eine große Entfernung, Kopie oder Sektionszuweisung), können Sie ihn auf seiner
  Fortschrittsseite verfolgen; warten Sie auf ihn oder starten Sie keine neuen
  ([Inhaltsjobs](../../../../../eng-us/features/6.0/content-jobs.md)).
- Bitten Sie die Redaktion, Änderungen im betroffenen Bereich zu unterbrechen.

## Genau herausfinden, was passiert ist

```bash
./console exp:audit search --channel=content --from='<start>' --limit=100 --allow-root-user
./console exp:audit search --name='content.node.remove*' --from='<start>' --allow-root-user
```

Das Entfernen eines Teilbaums ist ein übergeordnetes Ereignis mit einem untergeordneten Ereignis je Knoten darunter;
ein Inhaltsjob ist das übergeordnete Ereignis von allem, was seine Stapel getan haben; die Warnung `mass_delete` wird
ausgelöst, wenn ein Benutzer in zehn Minuten 500 Knoten entfernt
([Audit-Trail, „Why you want it“](../../../../../eng-us/features/6.0/audit-trail.md#why-you-want-it)). Notieren Sie, wer, wann
und welcher oberste Knoten.

## Rückgängig machen, mit dem genauesten Werkzeug zuerst

| Fehler | Rückgängig machen |
|---|---|
| Elemente oder ein Teilbaum in den Papierkorb verschoben | **Papierkorb**: die Elemente wiederherstellen; der Papierkorb zeigt, wer jedes Element gelöscht hat und wo es war ([Papierkorb](../../../../bc/6.0/papierkorb.md)) |
| Falscher Text auf vielen Seiten veröffentlicht | die Versionsseite jeder Seite, **Neuen Entwurf machen** aus der letzten guten Version, veröffentlichen |
| Falsche Sektion einem Teilbaum zugewiesen | **Setup > Sektionen**, **Inhalt zuweisen** der richtigen Sektion an denselben obersten Knoten |
| Falscher Zustand auf einem Teilbaum gesetzt | Reiter **Details** des obersten Knotens, **Zustand des Objekts**, den richtigen Zustand mit „Auch für alles unterhalb dieses Knotens“ |
| Zu früh veröffentlicht | verbergen oder zurück in einen Entwurf; Caches und Feeds prüfen, die ihn bereits enthielten ([Veralteter Cache](veralteter-cache.md)) |
| An die falsche Stelle verschoben | zurückverschieben; beachten Sie, dass das Verschieben die Sektion von Objekten ändert, die die Sektion des alten übergeordneten Knotens hatten |

Endgültig entfernt (Papierkorb geleert, oder Benutzerkonten, die nie in den Papierkorb kommen): Nur eine Datensicherung
bringt sie zurück.

## Das letzte Mittel: die Datenbank wiederherstellen

Wenn die genauen Werkzeuge den Fehler nicht rückgängig machen können, stellen Sie die letzte Datensicherung von vor dem
Fehler wieder her, zuerst auf einem Testserver, um zu prüfen, ob sie enthält, was Sie brauchen
([Wiederherstellung auf einem Testserver](../../../../../eng-us/help/government/operations/backups-and-recovery/restoring-to-a-test-server.md)). Möglichkeiten: die
Produktion vollständig wiederherstellen (und dabei jede legitime Änderung seit der Datensicherung verlieren) oder die
verlorenen Inhalte von Hand oder mit einem Paketexport aus der wiederhergestellten Kopie zurückkopieren. Entscheiden
Sie das mit den Verantwortlichen für den Dienst.

## Die Beteiligten informieren

- Redaktion: was passiert ist, was wiederhergestellt wurde, was sie neu machen muss.
- Die Öffentlichkeit, wenn falsche Informationen sichtbar waren: eine Richtigstellung dort, wo sie erschienen.
- Die Person, die den Fehler gemacht hat: als Prozessproblem, nicht als Schuldfrage; die Nachbetrachtung fragt, warum
  das Werkzeug es zuließ ([Nachbetrachtung eines Vorfalls](nachbetrachtung-eines-vorfalls.md)).

## Verwandte Seiten
- [Versionsverlauf und Nachvollziehbarkeit](../../../../../eng-us/help/government/operations/editorial-workflows/version-history-and-accountability.md)
- [Verunstaltung der Website](verunstaltung-der-website.md)
- [Planung der Rücknahme](../../../../../eng-us/help/government/operations/upgrades-and-change/rollback-planning.md)
- [Veröffentlichen und Versionen (Hilfe)](../../../veroeffentlichen-und-versionen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
