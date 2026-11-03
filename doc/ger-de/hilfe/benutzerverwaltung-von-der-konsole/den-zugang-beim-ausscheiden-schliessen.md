---
title: "Den Zugang beim Ausscheiden schließen"
audience: admin, hosting
category: administration
order: 11
summary: Den Zugang von jemandem, der geht, noch am selben Tag von der Konsole schließen - das Konto deaktivieren, damit jede Sitzung endet, Sitzungen, zweistufige Anmeldung und Rollen prüfen, festhalten, was geschehen ist, und das Konto erst später entfernen, wenn nichts mehr darauf verweist.
keywords: Ausscheiden, jemand geht, Mitarbeiter geht, Zugang schließen, Konto deaktivieren, Sitzungen beenden, Rolle entfernen, Konto später entfernen, übernommenes Konto, offboarding, exp:user:disable, exp:user:sessions, exp:user:roles unassign, exp:user:audit
type: help
---
# Den Zugang beim Ausscheiden schließen

Deaktivieren Sie das Konto noch am selben Tag. Das Deaktivieren erhält den Namen im Verlauf von allem, was die Person
getan hat.

## Was Sie am Ende haben

Ein Konto, das sich nicht anmelden kann, ohne verbleibende Sitzung, und eine Aufzeichnung seiner letzten Aktionen.

## Schritte

1. Sehen Sie sich das Konto an.

   ```bash
   ./console exp:user:show jane
   ```

   Die Ausgabe listet die Gruppen, Rollen, aktiven Sitzungen auf und ob das Konto geschützt ist.
2. Deaktivieren Sie das Konto.

   ```bash
   ./console exp:user:disable jane --apply
   ```

   Das Konto kann sich nicht anmelden. Alle seine Sitzungen enden.
3. Prüfen Sie, dass keine Sitzung übrig ist.

   ```bash
   ./console exp:user:sessions jane
   ```

4. Entfernen Sie die Passkeys des Kontos.

   ```bash
   ./console exp:user:2fa jane --remove-passkeys --apply
   ```

5. Entfernen Sie Rollen, die dem Konto selbst zugewiesen wurden.

   ```bash
   ./console exp:user:roles list jane
   ./console exp:user:roles unassign Administrator jane --apply
   ```

   Rollen, die aus einer Gruppe kommen, bleiben bei der Gruppe.
6. Bewahren Sie eine Kopie des jüngsten Verlaufs des Kontos auf, wenn Ihre Richtlinien das verlangen.

   ```bash
   ./console exp:user:audit jane --from=2026-01-01 --format=json > var/tmp/jane-audit.json
   ```

   **Datenschutz:** Die Datei enthält personenbezogene Daten. Bewahren Sie sie dort auf, wo nur die Personen sie
   lesen können, die sie brauchen.

## Ergebnis

Die Person hat keinen Zugang mehr. Die Inhalte, die sie angelegt hat, bleiben, mit dem Namen.

## Später

Entfernen Sie das Konto erst, wenn nichts mehr darauf verweist, zum Beispiel nach der Aufbewahrungsfrist Ihrer
Organisation ([Konten deaktivieren, aktivieren und entfernen](konten-deaktivieren-aktivieren-und-entfernen.md#ein-konto-entfernen)).

Soll das Konto auch die Rollen seiner Gruppen verlieren, nutzen Sie **Deaktivieren und verschieben** in der
Administration
([Deaktivieren oder „Deaktivieren und verschieben“?](konten-deaktivieren-aktivieren-und-entfernen.md#deaktivieren-oder-deaktivieren-und-verschieben)).

## Verwandte Seiten

- [Einen neuen Redakteur einrichten](einen-neuen-redakteur-einrichten.md)
- [Konten für Redakteure und Administratoren einrichten](../konten-fuer-redakteure-und-administratoren-einrichten.md#wenn-jemand-geht)
