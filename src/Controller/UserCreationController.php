<?php

namespace Drupal\example_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\example_module\ExampleModuleData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for the User pages.
 */
class UserCreationController extends ControllerBase {
  /**
   * @var \Drupal\example_module\ExampleModuleData
   */
  protected $data;

  /**
   * UserCreationController constructor.
   *
   * @param \Drupal\example_module\ExampleModuleData $data
   */
  public function __construct(ExampleModuleData $data) {
    $this->data = $data;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('example_module.data')
    );
  }

  /**
   * Render form.
   *
   * @return form
   */
  public function addUser() {
    $builder = \Drupal::formBuilder();
    $form = $builder->getForm('Drupal\example_module\form\UserCreationForm');
    return $form;
  }

  /**
   * Expose Data.
   *
   * @return response
   */
  public function getData(Request $request) {
    $users = $this->data->getData();
    $json_array = [
      'data' => [],
    ];
    foreach ($users as $user) {
      $json_array['data'][] = [
        'id' => $user->id,
        'name' => $user->name,
        'birthdate' => $user->birthdate,
        'role' => $user->role,
        'status' => $user->status,
      ];
    }
    return new JsonResponse($json_array);
  }

}
