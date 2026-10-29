/*
 * Passkeys in the browser (doc/eng-us/guides/passkeys.md): the page side of WebAuthn for the forms the kernel draws.
 *
 * A form opts in with data-exp-passkey="register" (user/passkeys), "login" (user/login) or "reauth" ("Verify it's
 * you"), and carries the options the server made for it (a new challenge each time) as JSON in
 * data-exp-passkey-options. A button with data-exp-passkey-start runs the browser's passkey dialog; the answer goes
 * into hidden fields (base64url) and the form is sent. Texts come from the form's data-msg-* attributes; the status
 * line is the element with data-exp-passkey-status (aria-live). Without WebAuthn in the browser the elements with
 * data-exp-passkey-only stay hidden and nothing else changes: the password still works.
 *
 * On the sign-in form the browser's passkey autofill ("conditional mediation") is started when the browser offers it:
 * the user name field (autocomplete="username webauthn") then lists the site's passkeys, and choosing one signs in.
 *
 * Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved. GNU General Public License v2.0 (or any later version).
 */
( function () {
    'use strict';

    function supported() {
        return typeof window.PublicKeyCredential === 'function' && navigator.credentials && typeof navigator.credentials.get === 'function';
    }

    function toBuffer( text ) {
        var b64 = String( text ).replace( /-/g, '+' ).replace( /_/g, '/' );
        while ( b64.length % 4 ) {
            b64 += '=';
        }
        var bin = atob( b64 );
        var out = new Uint8Array( bin.length );
        for ( var i = 0; i < bin.length; i++ ) {
            out[i] = bin.charCodeAt( i );
        }
        return out.buffer;
    }

    function toText( buffer ) {
        var bytes = new Uint8Array( buffer );
        var bin = '';
        for ( var i = 0; i < bytes.length; i++ ) {
            bin += String.fromCharCode( bytes[i] );
        }
        return btoa( bin ).replace( /\+/g, '-' ).replace( /\//g, '_' ).replace( /=+$/, '' );
    }

    function creationOptions( o ) {
        var copy = JSON.parse( JSON.stringify( o ) );
        copy.challenge = toBuffer( o.challenge );
        copy.user.id = toBuffer( o.user.id );
        copy.excludeCredentials = ( o.excludeCredentials || [] ).map( function ( c ) {
            return { type: c.type, id: toBuffer( c.id ) };
        } );
        return copy;
    }

    function requestOptions( o ) {
        var copy = JSON.parse( JSON.stringify( o ) );
        copy.challenge = toBuffer( o.challenge );
        copy.allowCredentials = ( o.allowCredentials || [] ).map( function ( c ) {
            return { type: c.type, id: toBuffer( c.id ) };
        } );
        return copy;
    }

    function setField( form, name, value ) {
        var input = form.querySelector( 'input[type="hidden"][name="' + name + '"]' );
        if ( !input ) {
            input = document.createElement( 'input' );
            input.type = 'hidden';
            input.name = name;
            form.appendChild( input );
        }
        input.value = value;
    }

    function status( form, text, isError ) {
        var el = form.querySelector( '[data-exp-passkey-status]' ) || document.querySelector( '[data-exp-passkey-status="' + ( form.id || '' ) + '"]' );
        if ( !el ) {
            return;
        }
        el.textContent = text || '';
        el.hidden = !text;
        // a notice in the state's colour (.a4-notice.is-bad / .is-info in the administration)
        el.classList.toggle( 'is-bad', !!isError );
        el.classList.toggle( 'is-info', !isError );
    }

    function message( form, key, fallback ) {
        return form.getAttribute( 'data-msg-' + key ) || fallback;
    }

    function options( form ) {
        try {
            return JSON.parse( form.getAttribute( 'data-exp-passkey-options' ) || 'null' );
        } catch ( e ) {
            return null;
        }
    }

    function fillAssertion( form, credential ) {
        var r = credential.response;
        setField( form, 'PasskeyCredentialID', toText( credential.rawId ) );
        setField( form, 'PasskeyClientData', toText( r.clientDataJSON ) );
        setField( form, 'PasskeyAuthenticatorData', toText( r.authenticatorData ) );
        setField( form, 'PasskeySignature', toText( r.signature ) );
        setField( form, 'PasskeyUserHandle', r.userHandle ? toText( r.userHandle ) : '' );
    }

    function failed( form, error ) {
        var cancelled = error && ( error.name === 'NotAllowedError' || error.name === 'AbortError' );
        status( form, cancelled ? message( form, 'cancelled', 'The passkey dialog was closed. Nothing was changed.' )
                                : message( form, 'failed', 'The passkey could not be used. Please try again.' ), true );
        form.removeAttribute( 'aria-busy' );
    }

    // ---- register (user/passkeys)
    function register( form ) {
        var o = options( form );
        if ( !o ) {
            return;
        }
        form.setAttribute( 'aria-busy', 'true' );
        status( form, message( form, 'working', 'Follow the steps of your browser or device.' ), false );
        navigator.credentials.create( { publicKey: creationOptions( o ) } ).then( function ( credential ) {
            var r = credential.response;
            setField( form, 'PasskeyCredentialID', toText( credential.rawId ) );
            setField( form, 'PasskeyClientData', toText( r.clientDataJSON ) );
            setField( form, 'PasskeyAttestation', toText( r.attestationObject ) );
            setField( form, 'PasskeyTransports', typeof r.getTransports === 'function' ? r.getTransports().join( ',' ) : '' );
            setField( form, 'RegisterPasskeyButton', '1' );
            form.submit();
        } ).catch( function ( e ) {
            failed( form, e );
        } );
    }

    // ---- sign in (user/login)
    var conditional = null;

    function signIn( form, credential ) {
        fillAssertion( form, credential );
        setField( form, 'PasskeyLoginButton', '1' );
        // the password fields are not sent with a passkey
        Array.prototype.forEach.call( form.querySelectorAll( 'input[type="password"]' ), function ( p ) {
            p.disabled = true;
        } );
        form.submit();
    }

    function login( form ) {
        var o = options( form );
        if ( !o ) {
            return;
        }
        var start = function () {
            form.setAttribute( 'aria-busy', 'true' );
            status( form, message( form, 'working', 'Follow the steps of your browser or device.' ), false );
            navigator.credentials.get( { publicKey: requestOptions( o ) } ).then( function ( credential ) {
                signIn( form, credential );
            } ).catch( function ( e ) {
                failed( form, e );
            } );
        };
        if ( conditional ) {
            // the autofill request ends first: a browser runs one passkey request at a time
            conditional.abort();
            conditional = null;
            window.setTimeout( start, 150 );
            return;
        }
        start();
    }

    function startConditional( form ) {
        var o = options( form );
        if ( !o || typeof window.PublicKeyCredential.isConditionalMediationAvailable !== 'function' || typeof AbortController !== 'function' ) {
            return;
        }
        window.PublicKeyCredential.isConditionalMediationAvailable().then( function ( yes ) {
            if ( !yes ) {
                return;
            }
            conditional = new AbortController();
            navigator.credentials.get( { publicKey: requestOptions( o ), mediation: 'conditional', signal: conditional.signal } ).then( function ( credential ) {
                signIn( form, credential );
            } ).catch( function () {
                // closed or replaced by the button: nothing to say
            } );
        } ).catch( function () {} );
    }

    // ---- verify it's you
    function reauth( form ) {
        var o = options( form );
        if ( !o ) {
            return;
        }
        form.setAttribute( 'aria-busy', 'true' );
        status( form, message( form, 'working', 'Follow the steps of your browser or device.' ), false );
        navigator.credentials.get( { publicKey: requestOptions( o ) } ).then( function ( credential ) {
            fillAssertion( form, credential );
            setField( form, 'ExpReauthMethod', 'passkey' );
            setField( form, 'ExpReauthConfirmButton', '1' );
            Array.prototype.forEach.call( form.querySelectorAll( 'input[type="password"], input[name="ExpReauthCode"]' ), function ( p ) {
                p.disabled = true;
            } );
            form.submit();
        } ).catch( function ( e ) {
            failed( form, e );
        } );
    }

    function init() {
        var forms = document.querySelectorAll( 'form[data-exp-passkey]' );
        if ( !forms.length ) {
            return;
        }
        if ( !supported() ) {
            Array.prototype.forEach.call( forms, function ( form ) {
                status( form, form.getAttribute( 'data-msg-unsupported' ) || '', false );
            } );
            return;
        }
        Array.prototype.forEach.call( document.querySelectorAll( '[data-exp-passkey-only]' ), function ( el ) {
            el.hidden = false;
        } );
        Array.prototype.forEach.call( forms, function ( form ) {
            var kind = form.getAttribute( 'data-exp-passkey' );
            var run = kind === 'register' ? register : ( kind === 'login' ? login : ( kind === 'reauth' ? reauth : null ) );
            if ( !run ) {
                return;
            }
            Array.prototype.forEach.call( form.querySelectorAll( '[data-exp-passkey-start]' ), function ( button ) {
                button.addEventListener( 'click', function ( event ) {
                    event.preventDefault();
                    run( form );
                } );
            } );
            if ( kind === 'login' && form.hasAttribute( 'data-exp-passkey-autofill' ) ) {
                startConditional( form );
            }
        } );
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }
}() );
