<?php

/*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 */


namespace api\v4\Spec;

use Civi\Api4\Service\Spec\CustomFieldSpec;
use Civi\Api4\Service\Spec\SpecFormatter;
use api\v4\Api4TestBase;

/**
 * @group headless
 */
class SpecFormatterTest extends Api4TestBase {

  /**
   * @dataProvider arrayFieldSpecProvider
   *
   * @param array $fieldData
   * @param string $expectedName
   * @param string $expectedType
   */
  public function testArrayToField($fieldData, $expectedName, $expectedType) {
    $field = SpecFormatter::arrayToField($fieldData, 'TestEntity');

    $this->assertEquals($expectedName, $field->getName());
    $this->assertEquals($expectedType, $field->getDataType());
  }

  public function testCustomFieldWillBeReturned() {
    $customGroupId = 1432;
    $customFieldId = 3333;
    $name = 'MyFancyField';

    $data = [
      'custom_group_id' => $customGroupId,
      'custom_group_id.name' => 'my_group',
      'custom_group_id.title' => 'My Group',
      'id' => $customFieldId,
      'name' => $name,
      'label' => $name,
      'data_type' => 'String',
      'html_type' => 'Select',
      'column_name' => $name,
      'serialize' => 1,
      'is_view' => FALSE,
    ];

    /** @var \Civi\Api4\Service\Spec\CustomFieldSpec $field */
    $field = SpecFormatter::arrayToField($data, 'TestEntity');

    $this->assertInstanceOf(CustomFieldSpec::class, $field);
    $this->assertEquals('my_group', $field->getCustomGroupName());
    $this->assertEquals($customFieldId, $field->getCustomFieldId());
    $this->assertEquals(\CRM_Core_DAO::SERIALIZE_SEPARATOR_BOOKEND, $field->getSerialize());
    $this->assertEquals('Select', $field->getInputType());
    $this->assertTrue($field->getInputAttrs()['multiple']);
  }

  /**
   * @return array
   */
  public function arrayFieldSpecProvider() {
    return [
      [
        [
          'name' => 'Foo',
          'title' => 'Bar',
          'type' => \CRM_Utils_Type::T_STRING,
        ],
        'Foo',
        'String',
      ],
      [
        [
          'name' => 'MyField',
          'title' => 'Bar',
          'type' => \CRM_Utils_Type::T_STRING,
          // this should take precedence
          'data_type' => 'Boolean',
        ],
        'MyField',
        'Boolean',
      ],
    ];
  }

}
