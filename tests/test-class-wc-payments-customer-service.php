<?php
/**
 * Class WC_Payments_Customer_Service_Test
 *
 * @package WooCommerce\Payments\Tests
 */

use PHPUnit\Framework\MockObject\MockObject;

/**
 * WC_Payments_Customer_Service unit tests.
 */
class WC_Payments_Customer_Service_Test extends WP_UnitTestCase {

	/**
	 * System under test.
	 *
	 * @var WC_Payments_Customer_Service
	 */
	private $customer_service;

	/**
	 * Mock WC_Payments_API_Client.
	 *
	 * @var WC_Payments_API_Client|MockObject
	 */
	private $mock_api_client;

	/**
	 * Pre-test setup
	 */
	public function setUp() {
		parent::setUp();

		$this->mock_api_client = $this->createMock( WC_Payments_API_Client::class );

		$this->customer_service = new WC_Payments_Customer_Service( $this->mock_api_client );
	}

	/**
	 * Post-test teardown
	 */
	public function tearDown() {
		delete_user_option( 1, '_wcpay_customer_id' );
		parent::tearDown();
	}

	/**
	 * Test get customer ID by user ID.
	 */
	public function test_get_customer_id_by_user_id() {
		update_user_option( 1, '_wcpay_customer_id', 'cus_test12345' );

		$customer_id = $this->customer_service->get_customer_id_by_user_id( 1 );

		$this->assertEquals( 'cus_test12345', $customer_id );
	}

	/**
	 * Test get customer ID by user ID when no stored customer ID.
	 */
	public function test_get_customer_id_by_user_id_when_no_stored_customer_id() {
		$customer_id = $this->customer_service->get_customer_id_by_user_id( 1 );

		$this->assertEquals( null, $customer_id );
	}

	/**
	 * Test get customer ID by user ID with null user ID.
	 */
	public function test_get_customer_id_by_user_id_with_null_user_id() {
		$customer_id = $this->customer_service->get_customer_id_by_user_id( null );

		$this->assertEquals( null, $customer_id );
	}

	/**
	 * Test get customer ID by user ID with user ID of 0.
	 */
	public function test_get_customer_id_by_user_id_with_user_id_of_zero() {
		$customer_id = $this->customer_service->get_customer_id_by_user_id( 0 );

		$this->assertEquals( null, $customer_id );
	}

	/**
	 * Test create customer for user.
	 *
	 * @throws WC_Payments_API_Exception
	 */
	public function test_create_customer_for_user() {
		$user             = new WP_User( 1 );
		$user->user_login = 'testUser';

		$this->mock_api_client->expects( $this->once() )
			->method( 'create_customer' )
			->with( 'Test User', 'test.user@example.com', 'Name: Test User, Username: testUser' )
			->willReturn( 'cus_test12345' );

		$customer_id = $this->customer_service->create_customer_for_user( $user, 'Test User', 'test.user@example.com' );

		$this->assertEquals( 'cus_test12345', $customer_id );
	}

	/**
	 * Test update customer for user.
	 *
	 * @throws WC_Payments_API_Exception
	 */
	public function test_update_customer_for_user() {
		$user = new WP_User( 0 );

		$this->mock_api_client->expects( $this->once() )
			->method( 'update_customer' )
			->with(
				'cus_test12345',
				[
					'name'        => 'Test User',
					'email'       => 'test.user@example.com',
					'description' => 'Name: Test User, Guest',
				]
			);

		$customer_id = $this->customer_service->update_customer_for_user(
			'cus_test12345',
			$user,
			'Test User',
			'test.user@example.com'
		);

		$this->assertEquals( 'cus_test12345', $customer_id );
	}

	/**
	 * Test update customer for user when user not found.
	 *
	 * @throws WC_Payments_API_Exception
	 */
	public function test_update_customer_for_user_when_user_not_found() {
		$user             = new WP_User( 1 );
		$user->user_login = 'testUser';

		// Wire the mock to throw a resource not found exception.
		$this->mock_api_client->expects( $this->once() )
			->method( 'update_customer' )
			->with(
				'cus_test12345',
				[
					'name'        => 'Test User',
					'email'       => 'test.user@example.com',
					'description' => 'Name: Test User, Username: testUser',
				]
			)
			->willThrowException( new WC_Payments_API_Exception( 'Error Message', 'resource_missing', 400 ) );

		// Check that the API call to create customer happens.
		$this->mock_api_client->expects( $this->once() )
			->method( 'create_customer' )
			->with( 'Test User', 'test.user@example.com', 'Name: Test User, Username: testUser' )
			->willReturn( 'cus_test67890' );

		$customer_id = $this->customer_service->update_customer_for_user(
			'cus_test12345',
			$user,
			'Test User',
			'test.user@example.com'
		);

		$this->assertEquals( 'cus_test67890', $customer_id );
	}

	/**
	 * Test update customer for user when a general exception is thrown.
	 *
	 * @throws WC_Payments_API_Exception
	 */
	public function test_update_customer_for_user_when_general_exception_is_thrown() {
		$user             = new WP_User( 1 );
		$user->user_login = 'testUser';

		// Wire the mock to throw a resource not found exception.
		$this->mock_api_client->expects( $this->once() )
			->method( 'update_customer' )
			->with(
				'cus_test12345',
				[
					'name'        => 'Test User',
					'email'       => 'test.user@example.com',
					'description' => 'Name: Test User, Username: testUser',
				]
			)
			->willThrowException( new WC_Payments_API_Exception( 'Generic Error Message', 'generic_error', 500 ) );

		$this->expectException( WC_Payments_API_Exception::class );
		$this->expectExceptionMessage( 'Generic Error Message' );

		$this->customer_service->update_customer_for_user(
			'cus_test12345',
			$user,
			'Test User',
			'test.user@example.com'
		);
	}

	public function test_set_default_payment_method_for_customer() {
		$this->mock_api_client
			->expects( $this->once() )
			->method( 'update_customer' )
			->with(
				'cus_12345',
				[
					'invoice_settings' => [ 'default_payment_method' => 'pm_mock' ],
				]
			);

			$this->customer_service->set_default_payment_method_for_customer( 'cus_12345', 'pm_mock' );
	}

	public function test_get_payment_methods_for_customer_fetches_from_api() {
		$mock_payment_methods = [
			[ 'id' => 'pm_mock1' ],
			[ 'id' => 'pm_mock2' ],
		];

		$this->mock_api_client
			->expects( $this->once() )
			->method( 'get_payment_methods' )
			->with( 'cus_12345', 'card' )
			->willReturn( [ 'data' => $mock_payment_methods ] );

		$response = $this->customer_service->get_payment_methods_for_customer( 'cus_12345' );

		$this->assertEquals( $mock_payment_methods, $response );
	}

	public function test_get_payment_methods_for_customer_fetches_from_transient() {
		$mock_payment_methods = [
			[ 'id' => 'pm_mock1' ],
			[ 'id' => 'pm_mock2' ],
		];

		$this->mock_api_client
			->expects( $this->once() )
			->method( 'get_payment_methods' )
			->with( 'cus_12345', 'card' )
			->willReturn( [ 'data' => $mock_payment_methods ] );

		$response = $this->customer_service->get_payment_methods_for_customer( 'cus_12345' );
		$this->assertEquals( $mock_payment_methods, $response );

		$response = $this->customer_service->get_payment_methods_for_customer( 'cus_12345' );
		$this->assertEquals( $mock_payment_methods, $response );
	}

	public function test_get_payment_methods_for_customer_no_customer() {
		$this->mock_api_client
			->expects( $this->never() )
			->method( 'get_payment_methods' );

		$response = $this->customer_service->get_payment_methods_for_customer( '' );
		$this->assertEquals( [], $response );
	}

	public function test_update_payment_method_with_billing_details_from_order() {
		$this->mock_api_client
			->expects( $this->once() )
			->method( 'update_payment_method' )
			->with(
				'pm_mock',
				[
					'billing_details' => [
						'address' => [
							'city'        => 'WooCity',
							'country'     => 'US',
							'line1'       => 'WooAddress',
							'postal_code' => '12345',
							'state'       => 'NY',
						],
						'email'   => 'admin@example.org',
						'name'    => 'Jeroen Sormani',
						'phone'   => '555-32123',
					],
				]
			);

		$order = WC_Helper_Order::create_order();

		$this->customer_service->update_payment_method_with_billing_details_from_order( 'pm_mock', $order );
	}
}
