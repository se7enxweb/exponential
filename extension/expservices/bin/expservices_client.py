#!/usr/bin/env python3
"""expservices_client: a Python client for the expservices library (standard library only).

As a module::

    from expservices_client import Client, ServiceError
    c = Client("https://alpha.se7enx.com")
    print(c.call("expsystem::version"))                 # read: returns the envelope's data
    c.login("editor", "secret")                         # session cookie is kept by the client
    for item in c.pages("expnode::children", 2):        # paged read: yields every item
        print(item["name"])
    c.post("expcache::clear", {"tag": "ini"})           # write: POST with the form token
    try:
        c.call("expsession::roles")
    except ServiceError as e:
        print(e.code, e)                                # 401, 403, 404, ... from the envelope

As a command::

    expservices_client.py [--url URL] [--jar FILE] [--token TOKEN] whoami | token | catalog [domain]
    expservices_client.py call <service> [arg ...]
    expservices_client.py page <service> [arg ...] [--limit N] [--max N]
    expservices_client.py post <service> [k=v ...] [-- arg ...]
    expservices_client.py login <user> [password]      (password from EXPSERVICES_PASSWORD or asked)

Environment: EXPSERVICES_URL (default https://alpha.se7enx.com), EXPSERVICES_JAR (cookie jar file, default
~/.cache/expservices/cookies.txt). Exit status: 0 ok, 1 service error, 2 transport error.
"""
import getpass
import http.cookiejar
import json
import os
import sys
import urllib.error
import urllib.parse
import urllib.request

__all__ = ["Client", "ServiceError", "TransportError"]


class ServiceError(Exception):
    """The service answered { ok: false, error: { code, message } }."""

    def __init__(self, code, message):
        super().__init__(message)
        self.code = code


class TransportError(Exception):
    """HTTP or network fault, or an answer that is not the ezjscore JSON."""


class Client:
    def __init__(self, base_url="https://alpha.se7enx.com", jar=None, timeout=30, verify=True, api_token=None):
        self.base = base_url.rstrip("/")
        self.timeout = timeout
        self.jar_path = jar
        self.jar = http.cookiejar.MozillaCookieJar(jar) if jar else http.cookiejar.CookieJar()
        if jar and os.path.exists(jar):
            try:
                self.jar.load(ignore_discard=True, ignore_expires=True)
            except (OSError, http.cookiejar.LoadError):
                pass
        handlers = [urllib.request.HTTPCookieProcessor(self.jar)]
        if not verify:
            import ssl
            ctx = ssl.create_default_context()
            ctx.check_hostname = False
            ctx.verify_mode = ssl.CERT_NONE
            handlers.append(urllib.request.HTTPSHandler(context=ctx))
        self.opener = urllib.request.build_opener(*handlers)
        self._token = None
        self.api_token = api_token  # personal API token: bearer sign-in, no cookie and no form token needed

    # ------------------------------------------------------------ transport
    def url(self, service, args=()):
        tail = "".join("::" + urllib.parse.quote(str(a), safe="") for a in args)
        return "%s/ezjscore/call/%s%s?ContentType=json" % (self.base, service, tail)

    def _send(self, service, args=(), fields=None, headers=None):
        data = urllib.parse.urlencode(fields).encode() if fields is not None else None
        req = urllib.request.Request(self.url(service, args), data=data, method="POST" if data is not None else "GET")
        req.add_header("Accept", "application/json")
        req.add_header("X-Requested-With", "XMLHttpRequest")
        if self.api_token:
            req.add_header("Authorization", "Bearer " + self.api_token)
            req.add_header("X-Exp-Token", self.api_token)  # for hosts whose proxy drops Authorization
        for k, v in (headers or {}).items():
            req.add_header(k, v)
        try:
            with self.opener.open(req, timeout=self.timeout) as resp:
                body = resp.read().decode("utf-8")
        except urllib.error.HTTPError as e:
            raise TransportError("HTTP %d from %s" % (e.code, req.full_url))
        except (urllib.error.URLError, OSError) as e:
            raise TransportError("request failed: %s" % e)
        if self.jar_path:
            os.makedirs(os.path.dirname(os.path.abspath(self.jar_path)), exist_ok=True)
            self.jar.save(ignore_discard=True, ignore_expires=True)
        try:
            outer = json.loads(body)
        except ValueError:
            raise TransportError("not a JSON answer from %s" % req.full_url)
        if outer.get("error_text"):
            raise ServiceError(500, outer["error_text"])
        env = outer.get("content")
        if not isinstance(env, dict) or "ok" not in env:
            raise TransportError("unexpected answer shape from %s" % req.full_url)
        if not env["ok"]:
            err = env.get("error") or {}
            raise ServiceError(err.get("code", 500), err.get("message", "error"))
        return env

    # ------------------------------------------------------------ api
    def envelope(self, service, *args):
        """Read call; returns the whole envelope { ok, data, meta }."""
        return self._send(service, args)

    def call(self, service, *args):
        """Read call; returns data."""
        return self.envelope(service, *args)["data"]

    def token(self, refresh=False):
        if self._token is None or refresh:
            self._token = self.call("expsession::token")["token"]
        return self._token

    def post(self, service, fields=None, args=()):
        """Write call: POST with the form token as field ezxform_token and header X-CSRF-Token. Returns data."""
        body = dict(fields or {})
        if self.api_token:  # bearer: no cookie, so no form token (see "Auth with an API token")
            return self._send(service, args, fields=body)["data"]
        t = self.token()
        body["ezxform_token"] = t
        return self._send(service, args, fields=body, headers={"X-CSRF-Token": t})["data"]

    def login(self, username, password):
        env = self._send("expsession::login", fields={"username": username, "password": password})
        self._token = (env["data"] or {}).get("token")  # the new session's token
        return env["data"].get("user", env["data"])

    def logout(self):
        data = self.post("expsession::logout")
        self._token = None
        return data

    def whoami(self):
        return self.call("expsession::whoami")

    def catalog(self, domain=None):
        return self.call("expservices::catalog", *([domain] if domain else []))

    def pages(self, service, *args, limit=25, maximum=1000):
        """Paged read: calls service(args..., offset, limit) until the list ends; yields every item."""
        offset = got = 0
        while got < maximum:
            env = self.envelope(service, *args, offset, limit)
            items = env["data"] if isinstance(env["data"], list) else (env["data"] or {}).get("items", [])
            for item in items:
                yield item
            got += len(items)
            offset += limit
            total = (env.get("meta") or {}).get("total")
            if len(items) < limit or (total is not None and got >= total):
                break


def main(argv=None):
    import argparse
    p = argparse.ArgumentParser(description="expservices client", add_help=True)
    p.add_argument("--url", default=os.environ.get("EXPSERVICES_URL", "https://alpha.se7enx.com"))
    p.add_argument("--jar", default=os.environ.get("EXPSERVICES_JAR",
                   os.path.join(os.environ.get("XDG_CACHE_HOME", os.path.expanduser("~/.cache")), "expservices", "cookies.txt")))
    p.add_argument("--token", default=os.environ.get("EXPSERVICES_TOKEN"), help="personal API token (or EXPSERVICES_TOKEN)")
    p.add_argument("--limit", type=int, default=25)
    p.add_argument("--max", type=int, default=1000)
    p.add_argument("-k", "--insecure", action="store_true")
    p.add_argument("command", choices=["whoami", "token", "catalog", "call", "page", "post", "login", "logout"])
    p.add_argument("rest", nargs=argparse.REMAINDER)
    a = p.parse_args(argv)
    c = Client(a.url, jar=a.jar, verify=not a.insecure, api_token=a.token)
    rest = [x for x in a.rest if x != "--"] if a.command != "post" else a.rest
    show = lambda d: print(json.dumps(d, indent=2, ensure_ascii=False))
    try:
        if a.command == "whoami":
            show(c.whoami())
        elif a.command == "token":
            print(c.token(refresh=True))
        elif a.command == "catalog":
            show(c.catalog(*rest[:1]))
        elif a.command == "call":
            show(c.call(rest[0], *rest[1:]))
        elif a.command == "page":
            for item in c.pages(rest[0], *rest[1:], limit=a.limit, maximum=a.max):
                print(json.dumps(item, ensure_ascii=False))
        elif a.command == "post":
            svc, fields, args, in_args = rest[0], {}, [], False
            for x in rest[1:]:
                if x == "--":
                    in_args = True
                elif in_args:
                    args.append(x)
                else:
                    k, _, v = x.partition("=")
                    fields[k] = v
            show(c.post(svc, fields, args))
        elif a.command == "login":
            pw = rest[1] if len(rest) > 1 else os.environ.get("EXPSERVICES_PASSWORD") or getpass.getpass("Password: ")
            show(c.login(rest[0], pw))
        elif a.command == "logout":
            show(c.logout())
    except (IndexError, TypeError):
        p.error("missing arguments for " + a.command)
    except ServiceError as e:
        print("error %s: %s" % (e.code, e), file=sys.stderr)
        return 1
    except TransportError as e:
        print("expservices_client: %s" % e, file=sys.stderr)
        return 2
    return 0


if __name__ == "__main__":
    sys.exit(main())
