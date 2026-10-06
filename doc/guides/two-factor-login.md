# Two-step sign-in

After the password, a code from an authenticator app on the phone, or one sent by e-mail: the password alone is then
useless to anyone else. This guide is for site owners and administrators who turn it on, and for users who set it up.
It is provided by the extension sevenx_authentication_2fa (`composer.json` requires it; the installers activate it).
The extension's own documentation has the details: `extension/sevenx_authentication_2fa/README.md`, `INSTALL.md` and
`doc/two-factor-pages.md`.

Time: 15 minutes to turn it on, 2 minutes per user to set it up.

## What a user sees

1. **Sign in** as always, with username and password.
2. **Confirm it is you**: a page asks for the 6-digit code. With an authenticator app, open the app and type the code it
   shows for the site; with e-mail codes, the code is in the mail (and in its subject). Each code works once. After five
   wrong codes the sign-in ends and starts again with the password.
3. **Set it up** under *My account* > *Two-step sign-in* (`/user2fa/setup`; in the administration the same address):
   choose *Authenticator app*, scan the QR code (or type the key shown in groups of four), and enter the code the app
   shows. Until that code is entered nothing changes. The key is not shown again afterwards.
4. **Change or turn off**: switching to e-mail codes or off, or *Reset 2FA*, asks for a current code from the app, so
   someone at an unattended computer cannot weaken the account.

Lost the phone? An administrator opens the user's object in the administration, sets the *Two-step sign-in* field to
*E-mail one-time code* or *Disabled*, and publishes; the user signs in and sets the app up again.

## Turning it on

1. Activate the extension (`[ExtensionSettings] ActiveExtensions[]=sevenx_authentication_2fa`) and the login handler
   in `settings/override/site.ini.append.php`, before `standard`:

   ```ini
   [UserSettings]
   LoginHandler[]
   LoginHandler[]=sevenxUser2fa
   LoginHandler[]=standard
   ```

2. Add the field to the user class: a class attribute of the datatype *7x 2FA Configuration* (identifier for example
   `two_factor`). Users of a class without it cannot keep a second step; the setup page says so.
3. Give the roles that should manage their own second step the policy `user2fa/setup` (Member, Editor,
   Administrator ...). Nothing is needed for anonymous visitors: the steps before signing in are in
   `[RoleSettings] PolicyOmitList` of the extension and check their own state.
4. Recommended: a key that encrypts the authenticator secrets in the database, in
   `settings/override/sevenxauthentication2fa.ini.append.php` (keep it out of version control; losing it means
   everyone sets the app up again):

   ```ini
   [TOTPSettings]
   SecretKey=<output of: openssl rand -base64 48>
   ```

5. Decide whether it is everyone's choice or required:

   | `[General] Enforce2FA` | `DefaultMethod` | Effect |
   |---|---|---|
   | `disabled` (default) | any | Users opt in on the setup page. |
   | `enabled` | `totp` | A user without a second step sets up the app right after the password, before being signed in. |
   | `enabled` | `email` | A user without a second step gets e-mail codes straight away (the account's e-mail address must be real). |

6. Clear the caches and, for Velocity, deploy: `./console exp:velocity deploy --allow-root-user`.

Try it on one siteaccess first: activate the extension as an access extension of a test siteaccess and put the
`LoginHandler` lines in that siteaccess's `site.ini.append.php`. Remember that `settings/override` outranks a
siteaccess: mail sent from the test siteaccess still uses the global `[MailSettings] Transport`.

## Social login

Buttons such as *Continue with Google* appear on the login page (admin4, admin, Admin UI and the media design) for each
provider enabled in `sevenxauthentication2fa.ini`; `INSTALL.md` of the extension has the steps per provider. A user found
this way still has to pass their own second step. A cancelled or failed social login ends on a page that says what
happened, with the way back to the login form.

## What protects it

- The password alone never signs in: the session only remembers that the password was right; the user is signed in,
  with a new session id, after the right code.
- Codes: each authenticator code works once and only within a minute or so of the clock; wrong codes are limited per
  sign-in and count as failed logins of the account (`[UserSettings] MaxNumberOfFailedLogin` locks it); e-mail codes are
  kept hashed and a new one can be asked for once a minute, three times per sign-in; the link in the mail only works in
  the browser where the password was typed.
- The QR code is drawn on the server; the key goes to no other service. Pages with a key or a pending sign-in are not
  cached.
- Every address the sign-in sends a visitor on to passes the [safe redirect rules](../features/6.0/safe-redirects.md).
- Social login checks a one-time state bound to the provider and refuses addresses the provider has not verified.
- Every step is written to `var/log/auth.log` (never a code or a secret).

## Testing it

```bash
# the logic without a database
php vendor/bin/phpunit --no-configuration --bootstrap extension/sevenx_authentication_2fa/tests/bootstrap.php \
    extension/sevenx_authentication_2fa/tests/unit
```

By use: sign in with a test user, set up the app, sign out, sign in with a code, try a wrong code and the same code
twice, then turn it off with a current code. Never enrol a real administrator account while trying it out.
