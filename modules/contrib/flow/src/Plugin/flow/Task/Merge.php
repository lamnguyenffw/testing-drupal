<?php

namespace Drupal\flow\Plugin\flow\Task;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\flow\Flow;
use Drupal\flow\Helpers\EntityContentConfigurationTrait;
use Drupal\flow\Helpers\EntityFromStackTrait;
use Drupal\flow\Helpers\EntitySerializationTrait;
use Drupal\flow\Helpers\EntityTypeManagerTrait;
use Drupal\flow\Helpers\FormBuilderTrait;
use Drupal\flow\Helpers\ModuleHandlerTrait;
use Drupal\flow\Helpers\SingleTaskOperationTrait;
use Drupal\flow\Helpers\TokenTrait;
use Drupal\flow\Helpers\UserAccount;
use Drupal\flow\Plugin\FlowTaskBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Task for merging values from content.
 *
 * @FlowTask(
 *   id = "merge",
 *   label = @Translation("Merge values from content"),
 *   deriver = "Drupal\flow\Plugin\flow\Derivative\Task\MergeDeriver"
 * )
 */
class Merge extends FlowTaskBase implements PluginFormInterface {

  use EntityContentConfigurationTrait {
    buildConfigurationForm as buildContentConfigurationForm;
    submitConfigurationForm as submitContentConfigurationForm;
  }
  use EntityFromStackTrait;
  use EntitySerializationTrait;
  use EntityTypeManagerTrait;
  use FormBuilderTrait;
  use ModuleHandlerTrait;
  use SingleTaskOperationTrait;
  use StringTranslationTrait;
  use TokenTrait;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    /** @var \Drupal\flow\Plugin\flow\Task\Merge $instance */
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->setStringTranslation($container->get('string_translation'));
    $instance->setModuleHandler($container->get(self::$moduleHandlerServiceName));
    $instance->setFormBuilder($container->get(self::$formBuilderServiceName));
    $instance->setEntityTypeManager($container->get(self::$entityTypeManagerServiceName));
    $instance->setSerializer($container->get(self::$serializerServiceName));
    $instance->setToken($container->get(self::$tokenServiceName));
    if (empty($instance->settings['values'])) {
      $default_config = $instance->defaultConfiguration();
      $instance->settings += $default_config['settings'];
    }
    $instance->initEntityFromStack();
    $instance->initConfiguredContentEntity();
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function doOperate(ContentEntityInterface $entity): void {
    $source = $this->initConfiguredContentEntity($entity);
    $target = $entity;

    if (!empty($this->settings['check_langcode'])) {
      // Do not merge values when the language is different.
      if ($source->language()->getId() != $target->language()->getId()) {
        return;
      }
    }

    $merge_single = $this->settings['method']['single'] ?? 'set:clear';
    $merge_multi = $this->settings['method']['multi'] ?? 'unify';

    $field_names = $this->settings['fields'] ?? [];
    $unlimited = FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED;
    $needs_save = FALSE;

    foreach ($field_names as $field_name) {
      if (!$target->hasField($field_name) || !$source->hasField($field_name)) {
        continue;
      }
      $source_item_list = $source->get($field_name);
      $source_item_list->filterEmptyItems();
      if ($source_item_list->isEmpty()) {
        continue;
      }
      $target_item_list = $target->get($field_name);
      $target_item_list->filterEmptyItems();
      $merge_values = $source_item_list->getValue();
      $current_values = $target_item_list->getValue();
      $cardinality = $target_item_list->getFieldDefinition()->getFieldStorageDefinition()->getCardinality();

      if ($cardinality === 1 && $merge_single === 'set:not_set' && count($current_values)) {
        continue;
      }
      if ($cardinality !== 1 && $merge_multi === 'set:not_set' && count($current_values)) {
        continue;
      }

      // Determine if we have different values to merge.
      // @todo Find a better way to determine this.
      $comparison_merge_values = $comparison_current_values = [];
      $values_changed = count($merge_values) !== count($current_values);
      /** @var \Drupal\Core\Field\FieldItemInterface $source_item */
      foreach ($source_item_list as $i => $source_item) {
        $property_name = $source_item->mainPropertyName();
        $source_value = isset($property_name) && !is_null($source_item->$property_name) ? $source_item->$property_name : ($merge_values[$i] ?? $source_item->getValue());
        if (is_string($source_value)) {
          $source_value = nl2br(trim($source_value));
        }
        elseif (is_array($source_value) && isset($source_value['entity'])) {
          $source_value = $source_value['entity'];
        }
        $comparison_merge_values[$i] = $source_value;
      }
      /** @var \Drupal\Core\Field\FieldItemInterface $target_item */
      foreach ($target_item_list as $i => $target_item) {
        $target_value = isset($property_name) && !is_null($target_item->$property_name) ? $target_item->$property_name : ($current_values[$i] ?? $target_item->getValue());
        if (is_string($target_value)) {
          $target_value = nl2br(trim($target_value));
        }
        elseif (is_array($target_value) && isset($target_value['entity'])) {
          $target_value = $target_value['entity'];
        }
        $comparison_current_values[$i] = $target_value;
      }
      // When merging new entities, use a normalized array representation and
      // compare for these values.
      $needs_array_conversion = FALSE;
      foreach ($comparison_merge_values as $i => $merge_value) {
        $source_item = $source_item_list->get($i);
        $entity = $merge_value instanceof EntityInterface ? $merge_value : ($source_item && isset($source_item->entity) && ($source_item->entity instanceof EntityInterface) ? $source_item->entity : NULL);
        if ($entity && $entity->isNew()) {
          $needs_array_conversion = TRUE;
          $comparison_merge_values[$i] = $this->toConfigArray($entity);
          array_walk_recursive($comparison_merge_values[$i], function (&$v) {
            if (is_string($v)) {
              $v = nl2br(trim($v));
            }
          });
          if (!isset($configured_keys)) {
            $configured_keys = array_flip(array_keys($comparison_merge_values[$i]));
          }
        }
      }
      if ($needs_array_conversion) {
        foreach ($comparison_current_values as $i => $current_value) {
          $current_item = $target_item_list->get($i);
          $entity = $current_value instanceof EntityInterface ? $current_value : ($current_item && isset($current_item->entity) && ($current_item->entity instanceof EntityInterface) ? $current_item->entity : NULL);
          if ($entity) {
            $comparison_current_values[$i] = array_intersect_key($this->toConfigArray($entity), $configured_keys);
            array_walk_recursive($comparison_current_values[$i], function (&$v) {
              if (is_string($v)) {
                $v = nl2br(trim($v));
              }
            });
          }
        }
      }
      foreach ($comparison_merge_values as $i => $source_value) {
        foreach ($comparison_current_values as $k => $target_value) {
          if (($source_value === $target_value) || (is_scalar($source_value) && is_scalar($target_value) && (((string) $source_value === (string) $target_value) || ($source_value === FALSE && $target_value === '0')))) {
            $merge_values[$i] = $current_values[$k];
            continue 2;
          }
        }
        $values_changed = TRUE;
        break;
      }

      if ($merge_multi === 'unify') {
        $num_values = count($merge_values);
        if ($values_changed && ($cardinality === $unlimited || $num_values < $cardinality)) {
          foreach ($current_values as $i => $current_value) {
            if ($cardinality !== $unlimited && $num_values > $cardinality) {
              break;
            }
            if (in_array($comparison_current_values[$i], $comparison_merge_values, TRUE)) {
              continue;
            }
            $merge_values[] = $current_value;
            $num_values++;
          }
        }
      }

      if ($values_changed) {
        $target_item_list->setValue(array_values($merge_values));
        $needs_save = TRUE;
      }
    }

    if ($needs_save) {
      if ($target instanceof EntityChangedInterface) {
        $target->setChangedTime(\Drupal::time()->getCurrentTime());
      }
      Flow::needsSave($target, $this);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form += $this->buildContentConfigurationForm($form, $form_state);

    if (isset($form['values'])) {
      $form['values']['#process'][] = [$this, 'filterFormFields'];
    }

    $weight = -100000;

    $entity_type = $this->configuredContentEntity->getEntityType();
    $form_display = EntityFormDisplay::collectRenderDisplay($this->configuredContentEntity, $this->entityFormDisplay, TRUE);
    $available_fields = array_keys($form_display->getComponents());
    $available_fields = array_combine($available_fields, $available_fields);
    $selected_fields_to_merge = isset($this->settings['fields']) ? array_combine($this->settings['fields'], $this->settings['fields']) : [];
    $langcode_key = $entity_type->hasKey('langcode') ? $entity_type->getKey('langcode') : 'langcode';
    unset($available_fields[$langcode_key], $available_fields['default_langcode']);
    $field_options = [];
    foreach ($available_fields as $field_name) {
      if (!$this->configuredContentEntity->hasField($field_name)) {
        continue;
      }
      $field_options[$field_name] = $this->configuredContentEntity->get($field_name)->getFieldDefinition()->getLabel();
    }

    if ($entity_type->id() === 'user') {
      $field_options += UserAccount::getAvailableFields();
    }

    $weight += 10;
    $form['fields'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Fields to merge'),
      '#options' => $field_options,
      '#default_value' => $selected_fields_to_merge,
      '#weight' => $weight++,
      '#ajax' => [
        'callback' => [static::class, 'filterFormFieldsAjax'],
        'wrapper' => $form['values']['#wrapper_id'],
        'method' => 'html',
      ]
    ];

    $weight += 10;
    $form['method'] = [
      '#weight' => $weight++,
    ];
    $single_options = [
      'set:clear' => $this->t('Set and clear any previously set value'),
      'set:not_set' => $this->t('Set when no other value was set before'),
    ];
    $form['method']['single'] = [
      '#type' => 'select',
      '#title' => $this->t('Merging single-value fields'),
      '#required' => TRUE,
      '#options' => $single_options,
      '#default_value' => $this->settings['method']['single'] ?? 'set:clear',
      '#weight' => 10,
    ];
    $multi_options = ['unify' => $this->t('Unify all values')] + $single_options;
    $form['method']['multi'] = [
      '#type' => 'select',
      '#title' => $this->t('Merging multi-value fields'),
      '#required' => TRUE,
      '#options' => $multi_options,
      '#default_value' => $this->settings['method']['multi'] ?? 'unify',
      '#weight' => 20,
    ];
    $weight += 10;
    $form['check_langcode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Do not merge when the translation language is different.'),
      '#default_value' => $this->settings['check_langcode'] ?? TRUE,
      '#weight' => $weight++,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->submitContentConfigurationForm($form, $form_state);
    $this->settings['check_langcode'] = (bool) $form_state->getValue('check_langcode', FALSE);
    $this->settings['method'] = $form_state->getValue('method');
    $this->settings['fields'] = array_keys(array_filter($form_state->getValue('fields'), function ($value) {
      return !empty($value);
    }));

    // Filter field values that are not selected in the merge form.
    $entity_type = $this->configuredContentEntity->getEntityType();
    $entity_keys = $entity_type->getKeys();
    foreach (array_keys($this->settings['values']) as $k_1) {
      if (!in_array($k_1, $entity_keys) && !in_array($k_1, $this->settings['fields'])) {
        unset($this->settings['values'][$k_1]);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    $dependencies = parent::calculateDependencies();
    if (($entity = $this->getConfiguredContentEntity()) && !empty($this->settings['fields'])) {
      foreach ($this->settings['fields'] as $field_name) {
        if (!$entity->hasField($field_name)) {
          continue;
        }
        if ($field_config = $this->getEntityTypeManager()->getStorage('field_config')->load($entity->getEntityTypeId() . '.' . $entity->bundle() . '.' . $field_name)) {
          $dependencies[$field_config->getConfigDependencyKey()][] = $field_config->getConfigDependencyName();
        }
        if ($field_storage_config = $this->getEntityTypeManager()->getStorage('field_storage_config')->load($entity->getEntityTypeId() . '.' . $field_name)) {
          $dependencies[$field_storage_config->getConfigDependencyKey()][] = $field_storage_config->getConfigDependencyName();
        }
      }
    }
    return $dependencies;
  }

  /**
   * Process callback for only displaying selected fields in the form.
   *
   * @param array &$form
   *   The form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param array &$complete_form
   *   The complete form.
   *
   * @return array
   *   The form element, enriched by the entity form.
   */
  public function filterFormFields(array &$form, FormStateInterface $form_state, array &$complete_form): array {
    $entity_type = $this->configuredContentEntity->getEntityType();
    $form_display = EntityFormDisplay::collectRenderDisplay($this->configuredContentEntity, $this->entityFormDisplay, TRUE);
    $available_fields = array_keys($form_display->getComponents());
    if ($entity_type->id() === 'user') {
      $available_fields = array_merge($available_fields, array_keys(UserAccount::getAvailableFields()));
    }
    $available_fields = array_combine($available_fields, $available_fields);
    $selected_fields_to_merge = isset($this->settings['fields']) ? array_combine($this->settings['fields'], $this->settings['fields']) : [];
    $langcode_key = $entity_type->hasKey('langcode') ? $entity_type->getKey('langcode') : 'langcode';
    unset($available_fields[$langcode_key], $available_fields['default_langcode']);

    foreach ($available_fields as $field_name) {
      if (!$this->configuredContentEntity->hasField($field_name)) {
        continue;
      }
      if (isset($form[$field_name])) {
        $form[$field_name]['#access'] = isset($selected_fields_to_merge[$field_name]);
      }
    }

    return $form;
  }

  /**
   * Ajax callback for only displaying selected fields in the form.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The filtered field values element.
   */
  public static function filterFormFieldsAjax(array $form, FormStateInterface $form_state) {
    $checkbox = $form_state->getTriggeringElement();
    $element = &NestedArray::getValue($form, array_slice($checkbox['#array_parents'], 0, -2));
    $element = $element['values'];
    unset($element['#prefix'], $element['#suffix']);
    $user_input = $form_state->getUserInput();
    $field_name = end($checkbox['#array_parents']);
    $is_selected = (bool) NestedArray::getValue($user_input, $checkbox['#array_parents']);
    $element[$field_name]['#access'] = $is_selected;
    return $element;
  }

}
