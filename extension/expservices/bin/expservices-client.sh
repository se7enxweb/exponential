#!/usr/bin/env bash
# expservices-client.sh: a remote client for the expservices library, in bash + curl.
#
#   expservices-client.sh [options] <command> [arguments]
#
# Commands
#   login <user> [password]     sign in (password from the argument, EXPSERVICES_PASSWORD, or asked); keeps the session cookie
#   logout                      sign out and forget the cookie
#   whoami                      the current user
#   token                       the form token of the current session
#   catalog [domain]            list the services (optionally one domain)
#   call <service> [arg ...]    read call: GET ezjscore/call/<service>::arg::arg
#   page <service> [arg ...]    read call of a paged service: appends offset and limit, prints every page (--limit N, --max N)
#   post <service> [k=v ...] [-- arg ...]   write call: POST with the form token (field and header)
#
# Options (or environment)
#   -u, --url URL      base URL, default $EXPSERVICES_URL or https://alpha.se7enx.com (add /siteaccess if the site needs it)
#   -c, --jar FILE     cookie jar, default $EXPSERVICES_JAR or ${XDG_CACHE_HOME:-$HOME/.cache}/expservices/cookies.txt
#   --raw              print the answer as received, do not unwrap or pretty print
#   --limit N          page size for "page" (default 25)
#   --max N            stop "page" after N items (default 1000)
#   --token TOKEN      personal API token (or EXPSERVICES_TOKEN): bearer sign-in, no login, no form token
#   -k, --insecure     accept a self signed certificate
#   -h, --help
#
# The answer of ezjscore is { "error_text": "", "content": <envelope> }; the envelope is { ok, data, meta } or
# { ok:false, error:{ code, message } }. This client prints "data" (and "meta" to stderr when --meta), and on an error
# prints "error CODE: message" to stderr and exits with 1 (an HTTP or network fault exits with 2).
# Needs: bash, curl, and jq or python3 for the JSON.

set -u
set -o pipefail

URL="${EXPSERVICES_URL:-https://alpha.se7enx.com}"
JAR="${EXPSERVICES_JAR:-${XDG_CACHE_HOME:-$HOME/.cache}/expservices/cookies.txt}"
API_TOKEN="${EXPSERVICES_TOKEN:-}"   # personal API token: bearer sign-in, no cookie and no form token
RAW=0; LIMIT=25; MAX=1000; META=0; CURL_EXTRA=()

die() { echo "expservices-client: $*" >&2; exit 2; }
usage() { sed -n '2,/^$/p' "$0" | sed 's/^# \{0,1\}//'; }

# ---- json helpers: jq when present, python3 otherwise
if command -v jq >/dev/null 2>&1; then
    pretty() { jq .; }
    jget() { jq -r "$1"; }
else
    command -v python3 >/dev/null 2>&1 || die "needs jq or python3"
    pretty() { python3 -m json.tool; }
    jget() { python3 -c '
import json,sys
expr=sys.argv[1].lstrip(".")
d=json.load(sys.stdin)
for part in [p for p in expr.replace("//","").split(".") if p]:
    if isinstance(d,dict): d=d.get(part)
    else: d=None
print("" if d is None else (json.dumps(d) if isinstance(d,(dict,list)) else d))' "$1"; }
fi

while [ $# -gt 0 ]; do
    case "$1" in
        -u|--url) URL="$2"; shift 2;;
        -c|--jar) JAR="$2"; shift 2;;
        --token) API_TOKEN="$2"; shift 2;;
        --raw) RAW=1; shift;;
        --meta) META=1; shift;;
        --limit) LIMIT="$2"; shift 2;;
        --max) MAX="$2"; shift 2;;
        -k|--insecure) CURL_EXTRA+=(-k); shift;;
        -h|--help) usage; exit 0;;
        --) shift; break;;
        -*) die "unknown option $1";;
        *) break;;
    esac
done
[ $# -ge 1 ] || { usage; exit 2; }
CMD="$1"; shift
URL="${URL%/}"
mkdir -p "$(dirname "$JAR")" || die "cannot create $(dirname "$JAR")"

# endpoint <service> [arg ...]  ->  URL
endpoint() {
    local svc="$1"; shift
    local out="$URL/ezjscore/call/$svc" a
    for a in "$@"; do out+="::$(urlencode "$a")"; done
    printf '%s?ContentType=json' "$out"
}
urlencode() { local s="$1" i c out=""; for ((i=0;i<${#s};i++)); do c="${s:i:1}"; case "$c" in [a-zA-Z0-9.~_-]) out+="$c";; *) out+=$(printf '%%%02X' "'$c");; esac; done; printf '%s' "$out"; }

# request <GET|POST> <url> [curl args...]  ->  body on stdout, status check
request() {
    local method="$1" url="$2"; shift 2
    local body status auth=()
    [ -n "$API_TOKEN" ] && auth=(-H "Authorization: Bearer $API_TOKEN" -H "X-Exp-Token: $API_TOKEN")
    body=$(curl -sS "${CURL_EXTRA[@]}" -X "$method" -b "$JAR" -c "$JAR" -H 'Accept: application/json' -H 'X-Requested-With: XMLHttpRequest' "${auth[@]}" \
        -w '\n%{http_code}' "$@" "$url") || die "request failed: $url"
    status="${body##*$'\n'}"; body="${body%$'\n'*}"
    case "$status" in 2*) ;; *) echo "expservices-client: HTTP $status from $url" >&2; [ -n "$body" ] && echo "$body" | head -c 300 >&2; exit 2;; esac
    printf '%s' "$body"
}

# envelope: read ezjscore JSON on stdin; print data on stdout, or the error and exit 1
envelope() {
    local json; json=$(cat)
    [ -n "$json" ] || return 2
    if [ "$RAW" = 1 ]; then printf '%s\n' "$json" | pretty; return 0; fi
    local et ok; et=$(printf '%s' "$json" | jget '.error_text // ""')
    [ -n "$et" ] && { echo "error: $et" >&2; return 1; }
    ok=$(printf '%s' "$json" | jget '.content.ok')
    if [ "$ok" != "true" ]; then
        echo "error $(printf '%s' "$json" | jget '.content.error.code'): $(printf '%s' "$json" | jget '.content.error.message')" >&2
        return 1
    fi
    [ "$META" = 1 ] && printf '%s' "$json" | jget '.content.meta' >&2
    printf '%s' "$json" | { if command -v jq >/dev/null 2>&1; then jq '.content.data'; else python3 -c 'import json,sys;print(json.dumps(json.load(sys.stdin)["content"]["data"],indent=4))'; fi; }
}

token() {
    request GET "$(endpoint expsession::token)" | jget '.content.data.token'
}

case "$CMD" in
    login)
        [ $# -ge 1 ] || die "login <user> [password]"
        user="$1"; pass="${2:-${EXPSERVICES_PASSWORD:-}}"
        if [ -z "$pass" ]; then read -r -s -p "Password: " pass; echo >&2; fi
        request POST "$(endpoint expsession::login)" --data-urlencode "username=$user" --data-urlencode "password=$pass" | envelope | { if command -v jq >/dev/null 2>&1; then jq 'del(.token)'; else cat; fi; }
        exit "${PIPESTATUS[0]}"
        ;;
    logout)
        t=$(token); request POST "$(endpoint expsession::logout)" -H "X-CSRF-Token: $t" --data-urlencode "ezxform_token=$t" | envelope; r=$?
        : > "$JAR"; exit $r;;
    whoami) request GET "$(endpoint expsession::whoami)" | envelope;;
    token) token;;
    catalog) request GET "$(endpoint expservices::catalog "$@")" | envelope;;
    call)
        [ $# -ge 1 ] || die "call <service> [arg ...]"
        request GET "$(endpoint "$@")" | envelope;;
    page)
        [ $# -ge 1 ] || die "page <service> [arg ...]"
        offset=0; got=0; svc="$1"; shift
        while [ "$got" -lt "$MAX" ]; do
            json=$(request GET "$(endpoint "$svc" "$@" "$offset" "$LIMIT")") || exit 2
            ok=$(printf '%s' "$json" | jget '.content.ok')
            if [ "$ok" != "true" ]; then printf '%s' "$json" | envelope; exit 1; fi
            if command -v jq >/dev/null 2>&1; then
                n=$(printf '%s' "$json" | jq '.content.data | length'); total=$(printf '%s' "$json" | jq '.content.meta.total // empty')
                printf '%s' "$json" | jq -c '.content.data[]'
            else
                n=$(printf '%s' "$json" | python3 -c 'import json,sys;d=json.load(sys.stdin)["content"];print(len(d["data"]))'); total=""
                printf '%s' "$json" | python3 -c 'import json,sys
for i in json.load(sys.stdin)["content"]["data"]: print(json.dumps(i))'
            fi
            got=$((got + n)); offset=$((offset + LIMIT))
            [ "$n" -lt "$LIMIT" ] && break
            [ -n "${total:-}" ] && [ "$got" -ge "$total" ] && break
        done;;
    post)
        [ $# -ge 1 ] || die "post <service> [k=v ...] [-- arg ...]"
        svc="$1"; shift; fields=(); args=()
        while [ $# -gt 0 ]; do
            if [ "$1" = "--" ]; then shift; args=("$@"); break; fi
            fields+=(--data-urlencode "$1"); shift
        done
        if [ -n "$API_TOKEN" ]; then   # bearer: no cookie, so no form token
            request POST "$(endpoint "$svc" "${args[@]}")" "${fields[@]}" | envelope
        else
            t=$(token) || exit 2
            request POST "$(endpoint "$svc" "${args[@]}")" -H "X-CSRF-Token: $t" --data-urlencode "ezxform_token=$t" "${fields[@]}" | envelope
        fi;;
    *) usage; exit 2;;
esac
