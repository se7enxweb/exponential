/**
 * The React-like model, part 2: the store and one-way data flow.
 *
 *   state --(components)--> description --(vdom)--> DOM
 *     ^                                               |
 *     |   dispatch( action ) <---- event handlers ----+
 *     +--- reducer( state, action ) -> new state
 *
 * createStore( reducer, initial ):
 *   getState()          the current state (treat it as read-only; reducers return new objects)
 *   dispatch( action )  { type, ... }: runs the reducer, notifies subscribers, then runs the effects
 *   subscribe( fn )     fn( state, action ) after every change; returns an unsubscribe function
 *   effect( fn )        fn( action, store ) runs AFTER the reducer for every action: the only place for service calls,
 *                       timers, focus. An effect reports back by dispatching another action (never by changing state).
 * Reducers are pure. Components never call services and never change state; they dispatch.
 */
(function (window) {
    'use strict';
    var Exp = window.ExpPortal = window.ExpPortal || {};
    Exp.createStore = function (reducer, initial) {
        var state = initial === undefined ? reducer(undefined, { type: '@@init' }) : initial, subs = [], effects = [], store = {
            getState: function () { return state; },
            dispatch: function (action) {
                var next = reducer(state, action), changed = next !== state; state = next;
                if (changed) { subs.slice().forEach(function (f) { f(state, action); }); }
                effects.slice().forEach(function (f) { f(action, store); });
                return action;
            },
            subscribe: function (fn) { subs.push(fn); return function () { subs = subs.filter(function (s) { return s !== fn; }); }; },
            effect: function (fn) { effects.push(fn); }
        };
        return store;
    };
    /** combine( { slice: reducer } ) -> reducer; a slice that returns the same object leaves the state untouched. */
    Exp.combine = function (map) {
        return function (state, action) {
            state = state || {}; var out = {}, changed = false, k;
            for (k in map) { out[k] = map[k](state[k], action); if (out[k] !== state[k]) { changed = true; } }
            return changed ? out : state;
        };
    };
}(window));
