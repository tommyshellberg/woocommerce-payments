/** @format */

/**
 * External dependencies
 */
import { apiFetch } from '@wordpress/data-controls';
import { addQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies
 */
import { NAMESPACE } from '../constants';
import {
	updateTransactions,
	updateErrorForTransactions,
	updateTransactionsSummary,
	updateErrorForTransactionsSummary,
} from './actions';

export function* getTransactions( { paged = '1', perPage = '25' } ) {
	const url = addQueryArgs(
		`${ NAMESPACE }/transactions`,
		{
			page: paged,
			pagesize: perPage,
		}
	);

	try {
		const results = yield apiFetch( { path: url } );
		yield updateTransactions( { paged, perPage }, results.data || [] );
	} catch ( e ) {
		yield updateErrorForTransactions( { paged, perPage }, null, e );
	}
}

/**
 * Retrieves the transactions summary from the summary API.
 */
export function* getTransactionsSummary() {
	try {
		const summary = yield apiFetch( { path: `${ NAMESPACE }/transactions/summary` } );
		yield updateTransactionsSummary( summary );
	} catch ( e ) {
		yield updateErrorForTransactionsSummary( null, e );
	}
}