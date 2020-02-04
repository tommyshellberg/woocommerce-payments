
/**
 * Internal dependencies
 */
import reducer from '../reducer';
import types from '../action-types';
import { ID_PREFIX } from '../../constants';
import { getResourceId } from '../../util';

describe( 'Transactions reducer tests', () => {
	const mockQuery = { paged: '2', perPage: '50' };
	const mockTransactions = [
		{
			id: 1234,
			amount: 1000,
			fees: 50,
			net: 950,
		},
		{
			id: 1235,
			amount: 2000,
			fees: 100,
			net: 1900,
		},
	];
	const mockSummary = {
		total: 1000,
		fees: 50,
		net: 950,
	};

	const emptyState = {};
	const filledState = {
		[ getResourceId( ID_PREFIX.transactions, mockQuery ) ]: {
			data: mockTransactions,
		},
		summary: {
			data: mockSummary,
		},
	};

	test( 'Unrelated action is ignored', () => {
		expect( reducer( emptyState, { type: 'WRONG-TYPE' } ) ).toBe( emptyState );
		expect( reducer( filledState, { type: 'WRONG-TYPE' } ) ).toBe( filledState );
	} );

	test( 'New transactions reduced correctly', () => {
		// Set up mock data
		const expected = {
			[ getResourceId( ID_PREFIX.transactions, mockQuery ) ]: {
				data: mockTransactions,
			},
		};

		const reduced = reducer(
			emptyState,
			{
				type: types.SET_TRANSACTIONS,
				data: mockTransactions,
				query: mockQuery,
			}
		);
		expect( reduced ).toStrictEqual( expected );
	} );

	test( 'Transactions updated correctly on updated info', () => {
		const newTransactions = [
			...mockTransactions,
			...mockTransactions,
		];

		const expected = {
			...filledState,
			[ getResourceId( ID_PREFIX.transactions, mockQuery ) ]: {
				data: newTransactions,
			},
		};

		const reduced = reducer(
			filledState,
			{
				type: types.SET_TRANSACTIONS,
				data: newTransactions,
				query: mockQuery,
			}
		);
		expect( reduced ).toStrictEqual( expected );
	} );

	test( 'New transactions summary reduced correctly', () => {
		const expected = {
			summary: {
				data: mockSummary,
			},
		};

		const reduced = reducer(
			emptyState,
			{
				type: types.SET_TRANSACTIONS_SUMMARY,
				data: mockSummary,
			}
		);
		expect( reduced ).toStrictEqual( expected );
	} );

	test( 'Transactions summary updated correctly on updated info', () => {
		const newSummary = {
			total: 5000,
			fees: 100,
			net: 4900,
		};

		const expected = {
			...filledState,
			summary: {
				data: newSummary,
			},
		};

		const reduced = reducer(
			filledState,
			{
				type: types.SET_TRANSACTIONS_SUMMARY,
				data: newSummary,
			}
		);
		expect( reduced ).toStrictEqual( expected );
	} );
} );