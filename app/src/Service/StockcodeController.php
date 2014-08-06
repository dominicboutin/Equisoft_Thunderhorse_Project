<?php
namespace CIIN\Service
{
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Silex\Application;
	use Silex\ControllerProviderInterface;

	/**
	 * The routes used for stockcode.
	 *
	 * @package toyshop
	 */
	class StockcodeController implements ControllerProviderInterface
	{
		private $toys = array(
			'00001'=> array(
				'name' => 'Racing Car',
				'quantity' => '53',
				'description' => '...',
				'image' => 'racing_car.jpg',
			),
			'00002' => array(
				'name' => 'Raspberry Pi',
				'quantity' => '13',
				'description' => '...',
				'image' => 'raspberry_pi.jpg',
			),
		);    
		
		/**
		 * Connect function is used by Silex to mount the controller to the application.
		 *
		 * Please list all routes inside here.
		 *
		 * @param Application $app Silex Application Object.
		 *
		 * @return Response Silex Response Object.
		 */
		public function connect(Application $app)
		{
			/**
			 * @var \Silex\ControllerCollection $factory
			 */
			$factory = $app['controllers_factory'];

			$factory->get(
				'/',
				'Service\StockcodeController::getAll'
			);     
			
			$factory->get(
				'/get/{stockcode}',
				'Service\StockcodeController::getStockcode'
			);        

			$factory->delete(
				'/del/{stockcode}',
				'Service\StockcodeController::deleteStockcode'
			);        

			// ... Other routes     

			return $factory;
		}

		/**
		 * Get all the stockcodes.
		 *
		 * @param Application $app       The silex app.
		 *
		 * @return string
		 */
		public function getAll(Application $app)
		{
			return json_encode($this->toys);
		}

		/**
		 * Get a stockcode.
		 *
		 * @param Application $app       The silex app.
		 * @param string      $stockcode The stockcode.
		 *
		 * @return string
		 */
		public function getStockcode(Application $app, $stockcode)
		{
		//http://api.openweathermap.org/data/2.5/weather?q=montreal
		
		//$comments = json_decode($app['rest']->url($url)->get());
			if (!isset($this->toys[$stockcode])) {
				$app->abort(404, "Stockcode {$stockcode} does not exist.");
			}
			return json_encode($this->toys[$stockcode]);
		}


		/**
		 * Delete a stockcode.
		 *
		 * @param Application $app       The silex app.
		 * @param string      $stockcode The stockcode.
		 *
		 * @return string
		 */
		public function deleteStockcode(Application $app, $stockcode)
		{
			if (delete_toy($stockcode)) {
				// The delete went ok and we can now return a no content value
				// HTTP_NO_CONTENT = 204
				$responseCode = Response::HTTP_NO_CONTENT;
			} else {
				// Something went wrong
				$response_code = Response::HTTP_INTERNAL_SERVER_ERROR;
			}
		
			return new Response('', $responseCode);
		}    
		
		// Other functions here
		// ...
		// ...
	}
}