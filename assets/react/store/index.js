/**
 * The external dependencies.
 */
import createSagaMiddleware from 'redux-saga';
import { createStore, combineReducers, applyMiddleware, compose } from 'redux';
import { get } from 'lodash';

/**
 * The internal dependencies.
 */
import containers from 'containers/reducer';
import sidebars from 'sidebars/reducer';
import fields from 'fields/reducer';

/**
 * Create the store instance.
 *
 * @param  {Object} [state]
 * @return {Object}
 */
export default function(state = {}) {
	const composeEnhancer = get(window, '__REDUX_DEVTOOLS_EXTENSION_COMPOSE__', compose);
	const saga = createSagaMiddleware();
	const reducer = combineReducers({ containers, sidebars, fields });
	const store = createStore(reducer, state, composeEnhancer(applyMiddleware(saga)));

	return store;
}
