# "This form has expired": form token refusals

Exponential protects every form against cross-site request forgery with a form
token (the `ezformtoken` extension in the kernel). When a POST arrives without
its token, or with a wrong one, the visitor used to see a 500 page and the log
recorded "Unexpected error" for what is really a refusal. Since 27 September
2026 the answer is a clear page with the right status.

## What a visitor sees

A page that says, in plain words, that the form has expired, that the page was
open for a long time or the form was sent from another page, and that **nothing
was saved**. It offers **Reload the form and send it again**, or to go to the
front page (the dashboard in the administration). A signed-out visitor is told
they may have to sign in again, and when the refused form was itself a sign-in
form the page asks to sign in again. The wording is the same for a missing and a
wrong token, and avoids talking about security tokens and sessions.

The page exists in the standard, admin and admin3 designs
(`design:error/kernel/6.tpl`). While a design has no such template, or it
renders empty because the template cache was built before it existed, a built-in
explanation is shown.

## What the browser and scripts get

| | Value |
|---|---|
| HTTP status | `403 Forbidden` (kernel error 6, mapped in `error.ini`) |
| Headers | `Cache-Control: no-store`, the configured security headers |
| HTML request | Kernel error 6 through the error module inside the pagelayout of the module that was posted to; no redirect; session and posted data are left as they are |
| XHR, `Accept: application/json` | A JSON body with the same message |
| Error view title and path | "Form expired" instead of "kernel (6)" |

In the log, a refusal writes one line to `var/log/warning.log` with the check
(missing or wrong), the method, the path and the siteaccess, **never a token**.
The same refusal again (same check, siteaccess, path and client) within
`[HTMLForms] RefusalLogCollapseSeconds` is counted instead of written, and the
next line gives the count.

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `settings/site.ini` | `[HTMLForms]` | `RefusalLogCollapseSeconds` | `60` | Collapse window for repeated refusals. `0` writes every one. |
| `settings/site.ini` | `[HTMLForms]` | `Secret` | placeholder text | CSRF secret. Set your own per project. |
| `settings/error.ini` | kernel error 6 | | `403 Forbidden` | Status of the refusal. |

## For developers

- `ezpFormTokenException` carries the failed check and code 403; the old code
  threw a plain `Exception` that nothing caught until the uncaught handler.
- `ezpFormTokenRefusal` builds the answer: status, headers, the template
  parameters of `design:error/kernel/6.tpl` (`reason`, `reason_code`,
  `referrer`, `retry_url`, `is_ajax`, `title`, `message`, `action`), the JSON
  body and the built-in page.
- The web kernel catches the exception at `request/input`. A refusal escaping
  anywhere else (the REST or tree menu kernel, a module running the check itself,
  the uncaught exception handler) gets the same 403 and warning line.
- The built-in strings are literal `ezpI18n` calls in the contexts
  `kernel/error/formtoken` and `design/standard/error/formtoken`, translated into
  German and `eng-US`. An unmatched refusal no longer writes an
  `http_response_code() has no effect` warning.
- A page carrying a form token is marked `Cache-Control: private, no-cache,
  must-revalidate` so no shared cache can store it with a token and hand it to an
  anonymous visitor.

## Related pages

- [Security defaults of September 2026](../../specifications/6.0/security-defaults-2026-09.md)
- [RAD security](../../bc/6.0/rad-security.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
