<?php

namespace Drupal\example_module\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\example_module\ExampleModuleData;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configuration form definition for the user creation.
 */
class UserCreationForm extends ConfigFormBase {

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
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['example_module.user_creation'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_creation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#description' => $this->t('Fill with the user name'),
    ];
    $form['identification'] = [
      '#type' => 'textfield',
      '#title' => $this->t('identification'),
      '#description' => $this->t('Fill with the user identification'),
      '#ajax' => [
        'callback' => '::validateIdentificationAjax',
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => t('Verifying id...'),
        ],
      ],
      '#suffix' => '<span class="id-valid-message"></span>',
    ];
    $form['birthdate'] = [
      '#type' => 'date',
      '#title' => $this->t('Birthdate'),
      '#description' => $this->t('Fill with the user birthdate'),
    ];
    $form['role'] = [
      '#type' => 'select',
      '#title' => $this->t('Role'),
      '#description' => $this->t('Fill with the user role'),
      '#options' => [
        '1' => $this->t('Administrator'),
        '2' => $this->t('Webmaster'),
        '3' => $this->t('Developer'),
      ],
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $name = $form_state->getValue('name');
    $id = $form_state->getValue('identification');
    $birthdate = $form_state->getValue('birthdate');
    $role = $form_state->getValue('role');
    $status = $role == 1 ? 1 : 0;
    $this->data->createUser($name, $id, $birthdate, $role, $status);
    drupal_set_message(t('User registered'));
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $name = $form_state->getValue('name');
    $identification = $form_state->getValue('identification');
    if (!ctype_alnum($name)) {
      $form_state->setErrorByName('name', $this->t('Only Alphanumeric values'));
    }
    if (!ctype_digit($identification)) {
      $form_state->setErrorByName('identification', $this->t('Only Numeric values'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateIdentification(array &$form, FormStateInterface $form_state) {
    $identification = $form_state->getValue('identification');
    if ($this->data->searchId($identification) > 0) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function validateIdentificationAjax(array $form, FormStateInterface $form_state) {
    $valid = $this->validateIdentification($form, $form_state);
    $ajax_response = new AjaxResponse();
    if ($valid) {
      $css = ['border' => '1px solid green'];
      $message = $this->t('Identification ok.');
    }
    else {
      $css = ['border' => '1px solid red'];
      $message = $this->t('Identification not valid.');
    }
    $ajax_response->addCommand(new CssCommand('#edit-identification', $css));
    $ajax_response->addCommand(new HtmlCommand('.id-valid-message', $message));
    return $ajax_response;
  }

}
