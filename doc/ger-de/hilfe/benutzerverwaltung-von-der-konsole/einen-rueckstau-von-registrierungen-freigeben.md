---
title: "Einen Rückstau von Registrierungen freigeben"
audience: admin, hosting
category: administration
order: 12
summary: Viele wartende Registrierungen von der Konsole abarbeiten - sie auflisten, den offensichtlichen Spam sammelweise mit einer bestätigten Anzahl ablehnen, die echten freigeben und denen die Aktivierungsmail erneut senden, die nie bestätigt haben.
keywords: Rückstau, viele Registrierungen, wartende Registrierungen, sammelweise freigeben, sammelweise ablehnen, Spam-Registrierungen, nicht aktivierte Benutzer, backlog, exp:user:pending, exp:user:bulk approve, exp:user:bulk reject, exp:user:resend-activation, --confirm
type: help
---
# Einen Rückstau von Registrierungen freigeben

Lehnen Sie zuerst den Spam ab, dann geben Sie den Rest frei. Jede Sammeländerung braucht die Anzahl, die Sie in ihrem
Probelauf gesehen haben.

## Was Sie am Ende haben

Keine Registrierung mehr, die ohne Entscheidung wartet.

## Schritte

1. Listen Sie die wartenden Registrierungen auf.

   ```bash
   ./console exp:user:pending --limit=500
   ```

   Die Spalte State sagt, worauf jede wartet: `email`, `administrator` oder `workflow`.
2. Finden Sie den Spam mit einer Suche, zum Beispiel nach der Domain der Adressen.

   ```bash
   ./console exp:user:bulk reject --state=pending --search=spam.example.net
   ```

   Der Probelauf listet die Konten und ihre Anzahl auf, zum Beispiel 37.
3. **Achtung:** Das Ablehnen entfernt die Konten endgültig.
   Lehnen Sie sie mit der Anzahl aus dem Probelauf ab.

   ```bash
   ./console exp:user:bulk reject --state=pending --search=spam.example.net --confirm=37 --apply
   ```

4. Sehen Sie sich den Rest an.

   ```bash
   ./console exp:user:pending --limit=500
   ```

5. Geben Sie die übrigen Registrierungen frei.

   ```bash
   ./console exp:user:bulk approve --state=pending
   ./console exp:user:bulk approve --state=pending --confirm=112 --apply
   ```

   Jedes Konto wird so freigegeben, wie `exp:user:approve` es freigeben würde: veröffentlicht, mit seiner
   Willkommensmail.

Soll eine Person ihre Adresse selbst bestätigen, senden Sie die Aktivierungsmail erneut, statt freizugeben:
`./console exp:user:resend-activation <user> --apply`.

## Ergebnis

`exp:user:pending` listet nichts mehr auf, oder nur die Registrierungen, die Sie den Personen selbst überlassen
wollen.

## Verwandte Seiten

- [Registrierungen freigeben](registrierungen-freigeben.md)
- [Import, Export und Sammeländerungen](import-export-und-sammelaenderungen.md#sammeländerungen)
