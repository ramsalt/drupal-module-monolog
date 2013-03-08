<?php

/**
 * @file
 * Export UI display customizations.
 */

/**
 * CTools export UI extending class.
 */
class monolog_export_ui extends ctools_export_ui {

  /**
   * Overrides ctools_export_ui::list_form().
   *
   * Simplifies the form similar to how the Context module does it.
   */
  function list_form(&$form, &$form_state) {
    parent::list_form($form, $form_state);

    $form['description'] = array(
      '#markup' =>  t('<p>A <strong>profile</strong> is a collection of handlers that process the record.</p><p>Common examples of handlers are a <em>syslog handler</em> that routes records to the syslog and a <em>stream wrapper handler</em> that writes records to files and other streams.</p>'),
      '#weight' => -10,
    );

    $form['top row']['submit'] = $form['bottom row']['submit'];
    $form['top row']['reset'] = $form['bottom row']['reset'];
    $form['bottom row']['#access'] = FALSE;
    return;
  }

  /**
   * Overrides ctools_export_ui::list_build_row().
   *
   * Removes the drop button in favor of a horizontal list.
   */
  function list_build_row($item, &$form_state, $operations) {
    parent::list_build_row($item, $form_state, $operations);
    unset($operations['disable']);

    // @todo Make a theme function.
    $label = check_plain($item->options['label']);
    $machine_name = '<small>' . t('(Machine name: @name)', array('@name' => $item->name)) . '</small>';
    $this->rows[$item->name]['data'][0]['data'] = array('#markup' => $label . ' ' . $machine_name);


    $this->rows[$item->name]['data'][2]['data'] = array(
      '#theme' => 'links__node_operations',
      '#links' => $operations,
      '#attributes' => array('class' => array('links', 'inline')),
    );
  }
}

/**
 * Define the preset add/edit form.
 *
 * @see git_sync_routine_form_submit()
 *
 * @ingroup forms
 */
function monolog_profile_form(&$form, &$form_state) {
  $profile = &$form_state['item'];
  $is_new = empty($profile->options);

  if ($is_new) {
    $profile->options = monolog_profile_new()->options;
    $form['handlers'] = array(
      '#type' => 'value',
      '#value' => array(),
    );
  }

  $form['info']['label'] = array(
    '#id' => 'edit-label',
    '#title' => t('Name'),
    '#type' => 'textfield',
    '#default_value' => $profile->options['label'],
    '#description' => t('The human-readable name of the synchronization routine.'),
    '#required' => TRUE,
    '#maxlength' => 255,
    '#size' => 30,
  );

  $form['info']['name'] = array(
    '#type' => 'machine_name',
    '#default_value' => $profile->name,
    '#maxlength' => 32,
    '#machine_name' => array(
      'exists' => 'monolog_profile_load',
      'source' => array('info', 'label'),
    ),
    '#disabled' => ('clone' != $form_state['form type'] && !empty($profile->name)),
    '#description' => t('The machine readable name of the synchronization routine. This value can only contain letters, numbers, and underscores.'),
  );

  if (!$is_new) {

    $base_path = 'admin/config/development/monolog/profile/list/' . $profile->name . '/handler';
    $link = l(t('Add handler'), $base_path . '/add');
    $form['action_link'] = array(
      '#markup' => '<ul class="action-links"><li>' . $link . '</li></ul>',
    );

    $handler_info = monolog_handler_info_load_all();
    $level_options = monolog_level_options();

    $header = array(
      t('Label'),
      t('Handler'),
      t('Log Level'),
      t('Operations'),
    );

    $rows = array();
    foreach ($profile->options['handlers'] as $handler_name => $handler) {

      if (!isset($handler_info[$handler['handler']])) {
        $handler_info[$handler['handler']] = array('label' => $handler['handler']);
      }

      $row = array(
        'label' => check_plain($handler['label']),
        'handler' => check_plain($handler_info[$handler['handler']]['label']),
        'level' => $level_options[$handler['level']],
      );

      $operations = array();
      $operations[] = array(
        'title' => t('Edit'),
        'href' => $base_path . '/edit/' . $handler_name,
      );
      $operations[] = array(
        'title' => t('Delete'),
        'href' => $base_path . '/delete/' . $handler_name,
      );

      $row['operations'] = array(
        'data' => array(
          '#theme' => 'links__node_operations',
          '#links' => $operations,
          '#attributes' => array('class' => array('links', 'inline')),
        ),
      );

      $rows[] = $row;
    }

    $form['handlers'] = array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => t('There are no handlers attached to this profile.'),
    );
  }
}

/**
 * Form submission handler for git_sync_routine_form().
 */
function monolog_profile_form_submit($form, &$form_state) {
  $profile = &$form_state['item'];
  form_state_values_clean($form_state);
  $profile->options = $form_state['values'];
  unset($profile->options['delete']);
}
