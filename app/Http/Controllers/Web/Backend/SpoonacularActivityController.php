<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Services\Spoonacular\SpoonacularApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SpoonacularActivityController extends Controller
{
    protected SpoonacularApiService $spoonacularService;

    public function __construct(SpoonacularApiService $spoonacularService)
    {
        $this->spoonacularService = $spoonacularService;
    }

    /**
     * Get all activities
     *
     * @return \Illuminate\Http\JsonResponse
     */
//    public function getAllActivities(Request $request) {
//        $language = $request->get('language', 'EN');
//        $currency = $request->get('currency', 'ISK');
//        $page = (int) $request->get('page', 1);
//        $pageSize = (int) $request->get('page_size', 50);
//
//        try {
//            $activities = $this->bokunService->getAllActivities($language, $currency, $page, $pageSize);
//
//            if(count($activities['items']) > 0){
//                foreach ($activities['items'] as $item){
//
//                    $data = ProductItems::where('product_api_id', $item['id'])->first();
//
//                    if(empty($data)){
//                        $data = new ProductItems();
//                        $data->product_api_id = $item['id'];
//                        $data->product_api_external_id =  $item['externalId'] ?? null;
//                        $data->title = $item['title'] ?? null;
//                        $data->slug = Helper::generateSlug(\App\Models\ProductItems::class, $item['title']);
//                        $data->summary = $item['summary'] ?? null;
//                        $data->price = $item['price'] ?? null;
//
//                        $data->currency_short_code = $currency;
//                        $data->currency_symbol = $currency;
//                        // City
//                        $data->city_id = Helper::getCity($item['googlePlace']['country'], $item['googlePlace']['city'], $item['googlePlace']['cityCode']   );
//                        $data->google_place_lat = $item['googlePlace']['geoLocationCenter']['lat'] ?? null;
//                        $data->google_place_lng = $item['googlePlace']['geoLocationCenter']['lng'] ?? null;
//                        $data->api_platform = 'bokun';
//                        $data->vendor_id = $item['vendor']['id'] ?? null;
//                        $data->vendor_name = $item['vendor']['title'] ?? null;
//                        $data->box = $item['box'] == 'false' ? 'false' : 'true';
//                        $data->difficulty_level = $item['difficultyLevel'] == 'false' ? 'false' : 'true';
//                        $data->activity_categories = json_encode($item['activityCategories'] ?? null) ;
//                        $data->keywords = json_encode($item['keywords'] ?? null);
//                        $data->thumbnail_image = $item['keyPhoto']['originalUrl'] ?? asset('image/placeholder.jpg');
//                        $data->save();
//
//                        foreach ($item['photos'] as $photo){
//                            $imageData = new ProductItemsGallary();
//                            $imageData->item_id = $data->id;
//                            $imageData->image_url = $photo['originalUrl'];
//                            $imageData->save();
//                        }
//                    }
//                }
//            };
//
//            return response()->json('Data insert successfully');
//
//
//        } catch (\Exception $e) {
//            return response()->json([
//                'error' => 'Failed to get activities',
//                'message' => $e->getMessage()
//            ], 500);
//        }
//    }

    public function search(Request $request)
    {
        $filters = $request->all();
        $language = $request->get('language', 'EN');
        $currency = $request->get('currency', 'ISK');

        try {
            $results = $this->spoonacularService->searchActivities($language, $currency, $filters);

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to search activities',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getActivity(Request $request, $id)
    {
        $language = $request->get('language', 'EN');
        $currency = $request->get('currency', 'ISK');
        try {
            $activity = $this->spoonacularService->getActivity($id, $language, $currency);

            return response()->json($activity);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get activity details',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAvailability(Request $request, $id)
    {
        $startDate = $request->get('start_date', Carbon::now()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->addDays(30)->format('Y-m-d'));

        try {
            $availability = $this->spoonacularService->getAvailableDates($id, $startDate, $endDate);

            return response()->json($availability);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get availability',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
