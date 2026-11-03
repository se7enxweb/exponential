---
title: "Einen ausgesperrten Administrator wiederherstellen"
audience: admin, hosting
category: administration
order: 13
summary: Einen Administrator von der Shell des Servers wieder in die Administration lassen - herausfinden, warum die Anmeldung scheitert, das Konto entsperren, aktivieren, ein vergessenes Passwort durch ein erzeugtes ersetzen oder die zweistufige Anmeldung zurücksetzen.
keywords: ausgesperrt, Administrator ausgesperrt, kann mich nicht anmelden, Admin-Passwort vergessen, gesperrtes Konto, zu viele fehlgeschlagene Anmeldungen, Admin-Zugang wiederherstellen, Shell des Servers, locked out, exp:user:unlock, exp:user:reset-password --generate, exp:user:enable, exp:user:2fa --reset
type: help
---
# Einen ausgesperrten Administrator wiederherstellen

Wenn sich niemand mehr in der Administration anmelden kann, funktioniert die Konsole auf dem Server weiterhin. Finden
Sie heraus, warum die Anmeldung scheitert, und beheben Sie nur das.

## Was Sie am Ende haben

Einen Administrator, der sich wieder anmelden kann.

## Bevor Sie beginnen

- Shell-Zugang zum Server, im Wurzelverzeichnis der Installation.
- Der Anmeldename des Administrators, hier `admin`.

## Schritte

1. Sehen Sie sich das Konto an.

   ```bash
   ./console exp:user:show admin
   ```

   Die Zeile State sagt, ob es aktiviert, deaktiviert oder gesperrt ist. Die Zeile Two-step zeigt den zweiten Schritt.
2. Ist das Konto gesperrt, entsperren Sie es.

   ```bash
   ./console exp:user:unlock admin --apply
   ```

3. Ist das Konto deaktiviert, aktivieren Sie es.

   ```bash
   ./console exp:user:enable admin --apply
   ```

4. Ist das Passwort vergessen, setzen Sie ein erzeugtes.

   ```bash
   ./console exp:user:reset-password admin --generate --require-change --apply
   ```

   Das Passwort wird einmal ausgegeben. Die nächste Anmeldung fragt nach einem neuen.
5. Ist das Telefon mit der Authenticator-App verloren, setzen Sie den zweiten Schritt zurück.

   ```bash
   ./console exp:user:2fa admin --reset --apply
   ```

6. Melden Sie sich in der Administration an.

## Ergebnis

Der Administrator ist wieder drin. Das Audit hält jeden Schritt fest, mit dem Benutzer, der gehandelt hat.

**Hinweis:** Im Demomodus (`EXP_DEMO_MODE=1`) wird das Passwort eines Administrators nie geändert.

## Wenn es weiterhin scheitert

- Prüfen Sie, ob das Passwort ohne Browser funktioniert:
  `./console exp:user:check-login admin --password-stdin`, dann geben Sie das Passwort ein und drücken die Eingabetaste.
- Es gibt gar keinen Administrator mehr: legen Sie einen mit `exp:user:create --group="Administrator users"` an und
  prüfen Sie ihn mit `exp:user:whoami --as=<login>`.
- [Wenn ein Befehl fehlschlägt](wenn-ein-befehl-fehlschlaegt.md)

## Verwandte Seiten

- [Sitzungen und Anmeldesicherheit](sitzungen-und-anmeldesicherheit.md)
- [Das Administratorpasswort ändern](../administratorpasswort-aendern.md)
