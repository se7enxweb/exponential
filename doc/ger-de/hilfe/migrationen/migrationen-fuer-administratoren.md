---
title: "Migrationen für Administratoren"
audience: admin
category: migrations
order: 3
summary: Für Administratoren, die Migrationen anwenden, die Entwickler geschrieben haben - den Stand und den Dashboard-Block lesen, die Routine vor und nach einer Auslieferung, aus Setup > Migrationen oder auf der Konsole ausführen, wie ein Fehler aussieht und was schon automatisch geschah, wann Sie den Entwickler rufen, zurücknehmen, wer was darf, und eine Checkliste.
keywords: Migration, Migrationen, Schemaänderung, Datenbankänderung, Auslieferung, Administrator, Setup > Migrationen, Dashboard-Block, Lauf, Probelauf, Rücknahme, Identität bestätigen, Sperre, Wartungsmodus, Checkliste, migration, migrations, schema change, database change, deploy, exp:migration:migrate, administrator, dashboard block, run, dry run, rollback, verify it's you, migration/read, migration/run, migration/rollback, lock, maintenance mode, checklist
featured: true
type: help
---
# Migrationen für Administratoren

Diese Seite ist für Administratoren, die die Staging- oder Produktionsinstallation einer Site betreiben und die
Migrationen anwenden, die Entwickler geschrieben haben. Sie schreiben keine Migrationen; Sie prüfen, was wartet,
führen es zur richtigen Zeit aus und wissen, was zu tun ist, wenn ein Lauf scheitert. Alles hier geht in
**Setup > Migrationen** oder auf der Konsole des Servers.

## Was eine Migration für Sie ist

Die Änderung eines Entwicklers an der Site (eine neue Tabelle, eine Inhaltsklasse, Inhalte, eine Rolle, eine
Einstellung) kommt mit dem Code als Datei in `migrations/`. Die Installation merkt sich, welche sie angewendet hat.
Eine ausstehende Migration ist eine, die diese Installation noch nicht angewendet hat. Sie anzuwenden ist ein
**Lauf**: davor wird ein **Sicherungspunkt** genommen (ein Backup der Datenbank, der Dateien in `var/` und der
Einstellungen); geht etwas schief, wird der Sicherungspunkt automatisch zurückgespielt.

## Den Stand lesen

| Wo | Was Sie sehen |
|---|---|
| Der Dashboard-Block **Migrationen** | **Ausstehend**, **Fehlgeschlagen** und **Verwaist** als Zahlen, den **Letzten Lauf** und einen Hinweis, wenn ein Lauf im Gang oder die Installation gesperrt ist |
| **Setup > Migrationen** (der Stand) | jede Migration mit ihrem Stand (**erledigt**, **ausstehend**, **nicht nötig**, **fehlgeschlagen**, **unbekannt**), die Sperre, die letzten Läufe |
| **Verlauf** | jeder Lauf mit Sicherungspunkten, Commit, Tag und Push; **Fortsetzen** und **Zurücknehmen** |
| Die Konsole | `./console exp:migration:status` zeigt dasselbe |

**nicht nötig** heißt, die Migration ist nicht für diese Umgebung oder diese Datenbank; das ist in Ordnung. Die
Upgrade-Schritte des Kernels stehen zuerst, als Migrationen der Quelle `kernel`.

## Die Routine um eine Auslieferung

Vorher:

1. Lesen Sie den Stand: was steht aus, und braucht ein gescheiterter Lauf zuerst Aufmerksamkeit?
2. Öffnen Sie den **Probelauf**: jede ausstehende Migration mit dem, was sie ändern wird. Lesen Sie ihn; fragen Sie
   den Entwickler nach allem, was Sie nicht erwartet haben.
3. Klären Sie, ob eine Migration den **Wartungsmodus** einschaltet; planen Sie die Zeit.

Der Lauf:

4. Wenn der neue Code da ist, drücken Sie im Stand **N ausstehende ausführen** (oder führen Sie
   `./console exp:migration:migrate --apply` aus). Lassen Sie die Kästchen, wie sie sind: die Voreinstellungen sind
   die sicheren.
5. Bestätigen Sie „Identität bestätigen“. Der Lauf startet im Hintergrund, und seine Seite öffnet sich; Sie können
   die Seite schließen, der Lauf geht weiter.

Nachher:

6. Die Seite des Laufs zeigt **erledigt**. Der Stand zeigt nichts Ausstehendes.
7. Sehen Sie auf der Site nach, wo sich die Änderung zeigt (ein neuer Bereich, ein neues Feld im Editor).
8. Der Entwickler bittet eventuell um eine Auslieferung der Caches; auf der Konsole ist das
   `./console exp:velocity deploy`.

## Wenn ein Lauf scheitert

Sie sehen **fehlgeschlagen** auf der Seite des Laufs und im Dashboard-Block. Bis dahin ist automatisch geschehen:

- die gescheiterte Migration wurde angehalten, die nach ihr wurden nicht ausgeführt;
- ihre Datenbanktransaktion wurde zurückgerollt, so weit die Datenbank es kann;
- der gescheiterte Zustand wurde für den Entwickler in einem Sicherungspunkt bewahrt;
- der Sicherungspunkt von vor dem Lauf wurde wiederhergestellt: die Site ist so wie vor dem Lauf.

Was Sie tun: schicken Sie dem Entwickler die ID des Laufs und die Meldung auf seiner Seite (auch in seinem Protokoll).
Führen Sie ihn nicht erneut aus, bevor der Entwickler die Migration korrigiert hat; dann nehmen Sie **Fortsetzen**.
Rufen Sie den Entwickler sofort, wenn die Seite sagt, dass die Wiederherstellung gescheitert ist oder kein
Sicherungspunkt genommen wurde: dann kann die Site halb geändert sein.

## Zurücknehmen

**Setup > Migrationen > Rücknahme** nimmt zurück:

| Auswahl | Was geschieht |
|---|---|
| einen Lauf | der Sicherungspunkt von davor wird wiederhergestellt: Datenbank, Speicherdateien, Einstellungen |
| eine Migration | ihr `down()` macht sie rückgängig, wenn der Entwickler eines geschrieben hat |
| einen früheren Sicherungspunkt (Fachleute) | dieser Sicherungspunkt wird wiederhergestellt; Sie tippen seine ID zur Bestätigung erneut |

Zuerst wird ein Sicherungspunkt des jetzigen Zustands genommen, sodass sich eine Rücknahme selbst zurücknehmen lässt.
Die Wiederherstellung der Datenbank bringt alles auf den damaligen Stand zurück, auch was Redakteure seither
veröffentlicht haben: stimmen Sie das vorher mit den Redakteuren ab.

## Wer was darf

| Richtlinie | Erlaubt |
|---|---|
| `migration/read` | den Stand, den Verlauf, die Seiten der Läufe, den Probelauf, den Dashboard-Block |
| `migration/run` | Ausführen, Fortsetzen und Simulieren |
| `migration/rollback` | Zurücknehmen |

In einer neuen Installation hat sie nur die Rolle Administrator. Geben Sie `migration/read` allen, die den Stand
sehen sollen, und lassen Sie `migration/run` und `migration/rollback` auf Produktion bei den wenigen, die Änderungen
anwenden.

## Die Sperre

Nur ein Lauf, eine Rücknahme oder ein Upgrade schreibt gleichzeitig. Ist einer im Gang, sagt die Seite **Die
Installation ist gesperrt** und wer die Sperre hält; warten Sie. Eine Sperre eines beendeten Prozesses übernimmt der
nächste Lauf; Sie löschen nie eine Sperrdatei.

## Checkliste

- [ ] Der Dashboard-Block zeigt vor einer Auslieferung nichts Fehlgeschlagenes.
- [ ] Der Probelauf wurde gelesen, und jede Zeile ergibt Sinn.
- [ ] Wartungsfenster sind abgestimmt für Migrationen, die den Wartungsmodus einschalten.
- [ ] Der Lauf wurde mit den voreingestellten Kästchen gestartet.
- [ ] Nach dem Lauf: nichts ausstehend, der Push ist durchgegangen, die Site zeigt die Änderung.
- [ ] Bei einem Fehler: ID und Meldung des Laufs gingen an den Entwickler; nichts wurde von Hand erneut ausgeführt.

## Wie es weitergeht

- [Migrationen in der Administration](in-der-administration.md)
- [Stand und Verlauf](status-und-verlauf.md), [Ausführen, Probelauf und Rücknahme](ausfuehren-probelauf-und-rueckgaengig.md)
- [Wenn eine Migration scheitert](wenn-eine-migration-scheitert.md)
- Jetzt beginnen: [das Tutorial](../../anleitungen/migrationen-tutorial.md)

## Verwandte Seiten

- [Migrationen: der Einstieg](README.md)
- [Migrationen: die Anleitung](../../anleitungen/migrationen.md)
- [Migrationen: das Tutorial](../../anleitungen/migrationen-tutorial.md)
- [PHP-Migrationen](../../anleitungen/migrationen-php.md)
- [Migrationen in der Administration](in-der-administration.md)
- [Die Migrationsbefehle](befehle.md)
- [Die Migrationseinstellungen](einstellungen.md)
- [Das Migrationsdateiformat](../../spezifikationen/6.0/migrationsdateiformat.md)
- [Wenn eine Migration scheitert](wenn-eine-migration-scheitert.md)
- [Fehlerbehebung bei Migrationen](../../anleitungen/migrationen-fehlerbehebung.md)
- [Migrationen oder Umzug einer Site](migrationen-oder-umzug-einer-site.md)
- [Ein Rundgang durch die Administration](../rundgang-durch-die-administration.md)
- [„Identität bestätigen“](../../anleitungen/identitaet-bestaetigen.md), [Wartungsmodus](../../anleitungen/wartungsmodus.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
