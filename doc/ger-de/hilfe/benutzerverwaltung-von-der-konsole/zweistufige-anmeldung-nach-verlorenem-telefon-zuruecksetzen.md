---
title: "Die zweistufige Anmeldung nach einem verlorenen Telefon zurücksetzen"
audience: admin, hosting
category: administration
order: 14
summary: Jemandem helfen, der das Telefon mit der Authenticator-App verloren hat - bestätigen, wer fragt, den zweiten Schritt und die fehlgeschlagenen Anmeldungen mit exp:user:2fa --reset zurücksetzen, die alten Sitzungen beenden und die Person die App neu einrichten lassen.
keywords: Telefon verloren, neues Telefon, Authenticator-App, zweistufige Anmeldung, 2FA zurücksetzen, TOTP, Passkeys entfernen, Sitzungen beenden, kann mich nicht anmelden, zweiter Schritt, lost phone, exp:user:2fa --reset, sevenx_authentication_2fa
type: help
---
# Die zweistufige Anmeldung nach einem verlorenen Telefon zurücksetzen

Ohne das Telefon kann die Person die Anmeldung nicht abschließen. Setzen Sie den zweiten Schritt zurück, damit sich
die Person mit dem Passwort anmelden und die App neu einrichten kann.

## Was Sie am Ende haben

Eine Person, die sich wieder anmeldet und die zweistufige Anmeldung auf dem neuen Telefon hat.

## Bevor Sie beginnen

- Die Extension `sevenx_authentication_2fa` ist aktiv.
- Sie haben bestätigt, wer fragt, auf einem Weg, den Sie schon kennen: eine Telefonnummer aus Ihren Unterlagen, oder
  persönlich.

## Schritte

1. Sehen Sie sich den zweiten Schritt des Kontos an.

   ```bash
   ./console exp:user:2fa jane
   ```

   Die Ausgabe zeigt die Methode, ob sie aktiv ist, und die Zahl der Passkeys.
2. Führen Sie das Zurücksetzen als Probelauf aus.

   ```bash
   ./console exp:user:2fa jane --reset
   ```

3. Setzen Sie den zweiten Schritt zurück.

   ```bash
   ./console exp:user:2fa jane --reset --apply
   ```

   Der zweite Schritt wird entfernt. Die fehlgeschlagenen Anmeldungen werden ebenfalls zurückgesetzt.
4. Lag auf dem verlorenen Telefon ein Passkey, entfernen Sie die Passkeys.

   ```bash
   ./console exp:user:2fa jane --remove-passkeys --apply
   ```

5. Beenden Sie die Sitzungen, die das verlorene Telefon noch haben könnte.

   ```bash
   ./console exp:user:sessions jane --end --apply
   ```

6. Bitten Sie die Person, sich anzumelden und die App neu einzurichten.

## Ergebnis

Die Person meldet sich mit dem Passwort an. Wo die zweistufige Anmeldung Pflicht ist, fragt die Anmeldung sofort nach
einer neuen Einrichtung. Sonst schaltet die Person sie unter **Mein Konto** wieder ein.

## Verwandte Seiten

- [Sitzungen und Anmeldesicherheit](sitzungen-und-anmeldesicherheit.md#zweistufige-anmeldung-und-passkeys)
- [Ihr Konto und die Anmeldung](../konten-und-anmeldung.md#telefon-verloren): was die Person liest.
