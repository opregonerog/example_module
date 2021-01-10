<?php

namespace Drupal\example_module;

/**
 * Prepares the salutation to the world.
 */
class ExampleModuleData {
  /**
   * Check id duplicate.
   */
  public function searchId($id) {
    $database = \Drupal::database();
    $result = $database->select('example_users', 'eu')
      ->fields('eu')
      ->condition('id', $id)
      ->execute()->fetchAll();
    return count($result);
  }

  /**
   * Return users data.
   */
  public function getData() {
    $database = \Drupal::database();
    $result = $database->select('example_users', 'eu')
      ->fields('eu')
      ->execute()->fetchAll();
    return $result;
  }

  /**
   * Insert User.
   */
  public function createUser($name, $identification, $birthdate, $role, $status) {
    db_insert('example_users')
      ->fields([
        'name' => $name,
        'id' => $identification,
        'birthdate' => $birthdate,
        'role' => $role,
        'status' => $status,
      ])
      ->execute();
  }

}
