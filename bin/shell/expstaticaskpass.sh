#!/usr/bin/env sh
# @description Static publishing: git's password helper (GIT_ASKPASS) for a target with a token credential
# @long-description Not run by hand. exp:static:publish, exp:static:rollback and exp:static:push name this script in GIT_ASKPASS when a target signs in with a token (Credential=token:<secret name>). git runs it with its prompt as the only argument; it prints the user name for a "Username" prompt and the token for any other. Both come from the environment of the git process (EXP_STATIC_GIT_USER, EXP_STATIC_GIT_TOKEN), so the token is never on a command line, in the remote address or in a log.

case "$1" in
    [Uu]sername*) printf '%s\n' "${EXP_STATIC_GIT_USER:-x-access-token}" ;;
    *) printf '%s\n' "${EXP_STATIC_GIT_TOKEN}" ;;
esac
