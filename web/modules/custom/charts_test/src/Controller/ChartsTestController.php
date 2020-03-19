<?php

namespace Drupal\charts_test\Controller;

use Drupal\charts\Services\ChartsSettingsService;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Uuid\Php;
use GuzzleHttp\Client;

/**
 * Charts Api Example.
 */
class ChartsTestController extends ControllerBase implements ContainerInjectionInterface {

  protected $chartSettings;
  protected $messenger;
  protected $uuidService;
  protected $httpClient;
  protected $urlPrefix; 
  /**
   * Construct.
   *
   * @param \Drupal\charts\Services\ChartsSettingsService $chartSettings
   *   Service ChartsSettingsService.
   */
  public function __construct(ChartsSettingsService $chartSettings, 
                              MessengerInterface $messenger, 
                              Php $uuidService,
                              Client $http_client) {
    
    $this->chartSettings = $chartSettings->getChartsSettings();
    $this->messenger = $messenger;
    $this->uuidService = $uuidService;
    $this->httpClient = $http_client;
    $this->urlPrefix = \Drupal::config('apiurl')->get('value'); 
  }

  /**
   * Display.
   *
   * @return array
   *   Array to render.
   */
  public function display() {

    $library = $this->chartSettings['library'];
    if (empty($library)) {
      $this->messenger->addError($this->t('You need to first configure Charts default settings'));
      return [];
    }

    // Customize options here.
    $options = [
      'type' => $this->chartSettings['type'],
      'title' => $this->t('Lyle Falls'),
      'xaxis_title' => $this->t('Years'),
      'yaxis_title' => $this->t('Count'),
      'yaxis_min' => '',
      'yaxis_max' => '500',
      'three_dimensional' => FALSE,
      'title_position' => 'out',
      'legend_position' => 'right',
      'data_labels'=> $this->chartSettings['data_labels'],
      // 'grouping'   => TRUE,
      'colors'   => $this->chartSettings['colors'],
      'min'   => $this->chartSettings['min'],
      'max'   => $this->chartSettings['max'],
      'yaxis_prefix'   => $this->chartSettings['yaxis_prefix'],
      'yaxis_suffix'   => $this->chartSettings['yaxis_suffix'],
      'data_markers'   => $this->chartSettings['data_markers'],
      'red_from'   => $this->chartSettings['red_from'],
      'red_to'   => $this->chartSettings['red_to'],
      'yellow_from'   => $this->chartSettings['yellow_from'],
      'yellow_to'   => $this->chartSettings['yellow_to'],
      'green_from'   => $this->chartSettings['green_from'],
      'green_to'   => $this->chartSettings['green_to'],
    ];

    $response = json_decode($this->httpClient->get($this->urlPrefix.'/api/FishCounts/GetSteelheadDetails?facilityid=7&ladder=All&startdate=1/1/2018&enddate=12/31/2018&sort=2')->getBody());
    $areaResponse = json_decode($this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')->getBody());
    
    //$categories = []; 
    foreach($response->steelheadCount as $item) {
      $categories[] = $item->Date; 
      $wsth[] = $item->wsth;
      $hsth[] = $item->hsth;
      $usth[] = $item->usth;
    }

    foreach($areaResponse as $item) {
      $areawsth[] = $item->wsth;
      $areahsth[] = $item->hsth;
      $areausth[] = $item->usth;
    }

    // var_dump($wsth);
    // die; 

    // var_dump($data);
    // die; 
    // Sample data format.
    //$categories = ['Category 1', 'Category 2', 'Category 3', 'Category 4'];

    $seriesData[] = [
      'name' => '10-yr-wsth',
      'color' => '#F0FFF0	',
      //'type' => $this->chartSettings['type'],
      'type' => 'area',
      'data' =>  $areawsth,
      //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
    ];

    $seriesData[] = [
      'name' => '10-yr-hsth',
      'color' => '#FFEBCD',
      //'type' => $this->chartSettings['type'],
      'type' => 'area',
      'data' =>  $areahsth,
      //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
    ];

    $seriesData[] = [
      'name' => '10-yr-usth',
      'color' => '#EEE8AA',
      //'type' => $this->chartSettings['type'],
      'type' => 'area',
      'data' =>  $areausth,
      //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
    ];

    $seriesData[] = [
      'name' => 'wsth',
      'color' => '#0d233a',
      //'type' => $this->chartSettings['type'],
      'type' => 'line',
      'data' =>  $wsth,
      //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
    ];

    $seriesData[] = [
      'name' => 'hsth',
      'color' => '#8bbc21',
      //'type' => $this->chartSettings['type'],
      'type' => 'line',
      'data' =>  $hsth,
      //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
    ];

    $seriesData[] = [
      'name' => 'usth',
      'color' => '#910000',
      //'type' => $this->chartSettings['type'],
      'type' => 'line',
      'data' =>  $usth,
      //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
    ];

    // $seriesData[] = [
    //   'name' => '10-yr-wsth',
    //   'color' => '#7FFF00',
    //   //'type' => $this->chartSettings['type'],
    //   'type' => 'area',
    //   'data' =>  $areawsth,
    //   //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
    // ];

    // $seriesData[] = [
    //   'name' => '10-yr-hsth',
    //   'color' => '#7FFFD4',
    //   //'type' => $this->chartSettings['type'],
    //   'type' => 'area',
    //   'data' =>  $areahsth,
    //   //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
    // ];

    // $seriesData[] = [
    //   'name' => '10-yr-usth',
    //   'color' => '#8FBC8F',
    //   //'type' => $this->chartSettings['type'],
    //   'type' => 'area',
    //   'data' =>  $areausth,
    //   //$request = $this->httpClient->get($this->urlPrefix.'/api/FishCounts/TestGuzzleData')
    // ];


    // switch ($this->chartSettings['type']) {
    //   default:
    //     $seriesData[] = [
    //       'name' => 'Series 2',
    //       'color' => '#8bbc21',
    //       'type' => 'column',
    //       'data' => [150, 450, 500, 300],
    //     ];
    //     $seriesData[] = [
    //       'name' => 'Series 3',
    //       'color' => '#910000',
    //       'type' => 'area',
    //       'data' => [0, 0, 60, 90],
    //     ];
    //   case 'pie':
    //   case 'donut':

    // }

    // Creates a UUID for the chart ID.
    //$uuid_service = \Drupal::service('uuid');
    $chartId = 'chart-' . $this->uuidService->generate();

    $build['select'] = [
      '#type' => 'select', 
      '#title' => $this->t('Select List: '), 
      '#options' => ['option1', 'option2']
    ];

    $build['table'] = [
      '#theme' => 'charts_api_example',
      '#library' => (string) $library,
      '#categories' => $categories,
      '#seriesData' => $seriesData,
      '#options' => $options,
      '#id' => $chartId,
      '#override' => [],
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('charts.settings'),
      $container->get('messenger'),
      $container->get('uuid'), 
      $container->get('http_client')
    );
  }

}
