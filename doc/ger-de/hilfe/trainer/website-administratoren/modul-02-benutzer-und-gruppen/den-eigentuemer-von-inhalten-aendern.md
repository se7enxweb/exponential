---
title: "Lektion 2.5: Den Eigentümer von Inhalten ändern"
audience: trainer, admin
category: training
order: 25
summary: Die Teilnehmenden ändern in der Administration den Eigentümer eines Elements und eines Teilbaums, jeweils zuerst mit einer Vorschau, und sehen sich dieselbe Änderung auf der Kommandozeile vorab an, als Teil der Übergabe der Inhalte eines ausscheidenden Benutzers. 30 Minuten.
keywords: website-administration, eigentümer, eigentümer ändern, ezownerchange, einschränkung owner, übergabe, ausscheidender benutzer, teilbaum, dry-run, audit, site administration, owner, change owner, owner limitation, handover, departing user, subtree
type: help
---
# Lektion 2.5: Den Eigentümer von Inhalten ändern

Jedes Inhaltselement hat einen Eigentümer, den Benutzer, dem es gehört. Rollen können einschränken, was jemand mit
seinen eigenen Inhalten tun darf; wenn also eine Kollegin oder ein Kollege geht oder Inhalte unter dem falschen Konto
erstellt wurden, muss der Eigentümer geändert werden. Diese Lektion verwendet die Erweiterung ezownerchange, die in
neuen Installationen eingeschaltet ist, um Inhalte sicher zu übergeben.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Website-Administration](../README.md) |
| Modul | [Modul 2: Benutzer und Gruppen](README.md) |
| Dauer | 30 Minuten (10 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Website-Administratoren |
| Voraussetzungen | [Lektion 2.4](nicht-aktivierte-benutzer-und-kontopruefungen.md); die Benutzer `tess.trainee` und `sam.sample` |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, warum der Eigentümer für die Einschränkung `Owner` einer Rolle wichtig ist;
- den Eigentümer eines Elements ändern und dabei zuerst die Vorschau lesen;
- den Eigentümer eines Teilbaums ändern, beschränkt auf die Elemente eines Eigentümers;
- eine Änderung auf der Kommandozeile mit `--dry-run` vorab ansehen;
- eine sinnvolle Reihenfolge der Schritte nennen, wenn ein Benutzer ausscheidet.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Den Eigentümer von Inhalten ändern](../../../../anleitungen/eigentuemer-aendern.md).

1. **Warum Eigentümer wichtig sind** (3 Minuten). Die Richtlinien `content/read`, `content/edit` und `content/remove`
   können die Einschränkung `Owner` tragen („nur die eigenen Inhalte“). Ist der Eigentümer eine ausgeschiedene
   Kollegin oder ein ausgeschiedener Kollege, kann niemand mit einer solchen Rolle die Inhalte mehr bearbeiten.
2. **Die Übergabe** (4 Minuten). Im Kontextmenü eines Knotens **Eigentümer ändern** (braucht `owner/change`; die Rolle
   Administrator hat sie). Wählen Sie den neuen Eigentümer im Inhaltsbrowser und klicken Sie auf **Auswählen**: Noch
   hat sich nichts geändert. Die Vorschau listet jedes Objekt auf, das die Änderung erreicht. Wählen Sie
   **Nur dieses Objekt** oder **Dieses Objekt und alles darunter** (braucht `owner/subtree`), wahlweise nur die
   Objekte des aktuellen Eigentümers, **Vorschau aktualisieren** und dann **Den Eigentümer von N Objekten ändern**.
3. **Kommandozeile** (2 Minuten). Für Teilbäume mit mehr als 500 Objekten oder Übergaben per Skript dient das Skript
   `extension/ezownerchange/bin/php/expownerchange.php`, zuerst mit `--dry-run --list`.
4. **Checkliste für Ausscheidende** (1 Minute): die Inhalte übergeben, dann das Konto deaktivieren oder stilllegen
   (Lektion 2.4), dann das Audit-Protokoll prüfen.

### Häufige Fragen

**Ändert der Eigentümerwechsel, wer das Element oder seine Versionen erstellt hat?** Nein. Der Eigentümer ändert sich;
der Versionsverlauf hält fest, wer jede Version erstellt hat.

**Was ist, wenn ich manche Elemente im Teilbaum nicht bearbeiten darf?** Jedes Objekt ändert sich nur dort, wo Sie es
auch bearbeiten dürfen (`content/edit`); die Vorschau zeigt das Ergebnis für jedes.

**Wird die Änderung protokolliert?** Ja, jede Änderung im Audit-Protokoll als `content.ezownerchange.change`.

### Stolperfallen

- **Die Vorschau überspringen.** Bei einem großen Teilbaum ist die Vorschau der einzige Ort, an dem Sie sehen, was
  sonst noch geändert wird. Lesen Sie sie im Kurs laut vor.
- **Fehlender Menüeintrag.** Steht **Eigentümer ändern** nicht im Kontextmenü, ist die Erweiterung nicht aktiv oder
  der Rolle fehlt `owner/change`. Prüfen Sie in der Übungsumgebung **Setup > Extensions**.
- **Objekt-IDs und Knoten-IDs.** Die Kommandozeile erwartet Eigentümer-IDs (Objekt-IDs der Benutzer) und eine
  Knoten-ID. Zeigen Sie, wo Sie beide im Reiter **Details** eines Elements ablesen.

## Praktische Übung

Auf Ihrer Schulungsinstallation, als Administrator angemeldet:

1. Melden Sie sich in einem privaten Fenster als `tess.trainee` an (das Konto liegt seit Lektion 2.2 in **Editors**)
   und erstellen Sie einen Ordner `Tess folder` mit einem Artikel darin, sofern Ihre Website Redakteuren das Erstellen
   von Inhalten erlaubt. Ist das nicht der Fall, erstellen Sie Ordner und Artikel als Administrator und springen Sie
   zu Schritt 3.
2. Melden Sie sich im privaten Fenster ab.
3. Öffnen Sie als Administrator `Tess folder`. Notieren Sie im Reiter **Details** den Eigentümer, die Knoten-ID und
   die Objekt-ID.
4. Öffnen Sie das Kontextmenü von `Tess folder` und wählen Sie **Eigentümer ändern**.
5. Wählen Sie im Inhaltsbrowser `sam.sample` und klicken Sie auf **Auswählen**.
6. Wählen Sie in der Vorschau **Nur dieses Objekt** und klicken Sie auf **Vorschau aktualisieren**. Lesen Sie die
   Liste.
7. Wählen Sie **Dieses Objekt und alles darunter**, beschränken Sie es auf die Objekte des aktuellen Eigentümers und
   klicken Sie erneut auf **Vorschau aktualisieren**. Vergleichen Sie die Anzahl.
8. Klicken Sie auf **Den Eigentümer von N Objekten ändern**. Lesen Sie das Ergebnis.
9. Prüfen Sie den Eigentümer im Reiter **Details** des Ordners und des Artikels.
10. Sehen Sie sich in einem Terminal die Rückänderung vorab an; ersetzen Sie dabei die IDs durch die aus Schritt 3
    und die Objekt-IDs beider Benutzer:

    ```bash
    php extension/ezownerchange/bin/php/expownerchange.php --to-owner=<tess object id> --node=<folder node id> --from-owner=<sam object id> --dry-run --list
    ```

## Erwartetes Ergebnis

- Schritt 7 zählt den Ordner und den Artikel (zwei Objekte), wenn beide `tess.trainee` gehörten.
- Nach Schritt 8 nennen beide Elemente im Reiter **Details** `sam.sample` als Eigentümer.
- Schritt 10 listet die Objekte auf, die zurückgeändert würden, und ändert nichts.
- Der Trainer prüft den Reiter **Details** des Ordners einer Person.

## Quiz

1. Welche Einschränkung einer Richtlinie macht den Eigentümer für das Bearbeiten wichtig?
2. Welche Richtlinie brauchen Sie, um den Eigentümer eines ganzen Teilbaums zu ändern?
   A. `owner/change` B. `owner/subtree` C. `content/move` D. `user/hygiene`
3. Wann ändert sich der Eigentümer tatsächlich: nach **Auswählen** oder später?
4. Was bewirken `--dry-run --list` auf der Kommandozeile?
5. Bringen Sie in die richtige Reihenfolge: das Konto deaktivieren, das Audit-Protokoll prüfen, die Inhalte übergeben.

<details>
<summary>Antworten</summary>

1. Die Einschränkung `Owner` von `content/read`, `content/edit` oder `content/remove`
   ([Den Eigentümer von Inhalten ändern](../../../../anleitungen/eigentuemer-aendern.md)).
2. **B.** Zusätzlich zu `owner/change`.
3. Erst nach **Den Eigentümer von N Objekten ändern**; **Auswählen** führt zur Vorschau.
4. Sie zeigen, welche Objekte sich ändern würden, und ändern nichts.
5. Die Inhalte übergeben, das Konto deaktivieren, das Audit-Protokoll prüfen.

</details>

## Weiterführende Literatur

- [Den Eigentümer von Inhalten ändern](../../../../anleitungen/eigentuemer-aendern.md): die Administration und die
  Kommandozeile.
- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md): Einschränkungen wie
  `Owner`.
- [Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md): das Audit-Protokoll lesen.

## Verwandte Seiten

- [Modul 2: Benutzer und Gruppen](README.md)
- [Website-Administration: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
