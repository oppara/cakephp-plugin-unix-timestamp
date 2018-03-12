<?php
namespace Oppara\UnixTimestamp\Test\TestCase\Model\Behavior;

use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Oppara\UnixTimestamp\Model\Behavior\UnixTimestampBehavior;

/**
 * UnixTimestamp\Model\Behavior\UnixTimestampBehavior Test Case
 */
class UnixTimestampBehaviorTest extends TestCase
{
    public $autoFixtures = false;
    public $fixtures = [
        'plugin.Oppara/UnixTimestamp.users',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->table = $this->getMockBuilder('Cake\ORM\Table')->getMock();
        $this->Behavior = new UnixTimestampBehavior($this->table, []);
    }

    /**
     * Sanity check Implemented events
     *
     * @return void
     */
    public function testImplementedEventsDefault()
    {
        $expected = [
            'Model.beforeSave' => 'handleEvent'
        ];
        $this->assertEquals($expected, $this->Behavior->implementedEvents());
    }

    /**
     * testImplementedEventsCustom
     *
     * The behavior allows for handling any event - test an example
     *
     * @return void
     */
    public function testImplementedEventsCustom()
    {
        $table = $this->getMockBuilder('Cake\ORM\Table')->getMock();
        $settings = ['events' => ['Something.special' => ['date_specialed' => 'always']]];
        $this->Behavior = new UnixTimestampBehavior($table, $settings);
        $expected = [
            'Something.special' => 'handleEvent'
        ];
        $this->assertEquals($expected, $this->Behavior->implementedEvents());
    }

    /**
     * testCreatedAbsent
     *
     * @return void
     * @triggers Model.beforeSave
     */
    public function testCreatedAbsent()
    {
        $ts = new \DateTime('2000-01-01');
        $this->Behavior->timestamp($ts);

        $event = new Event('Model.beforeSave');
        $entity = new Entity(['name' => 'Foo']);

        $return = $this->Behavior->handleEvent($event, $entity);
        $this->assertTrue($return, 'Handle Event is expected to always return true');
        $this->assertInternalType('integer', $entity->created);
        $this->assertSame((int) $ts->format('U'), $entity->created, 'Created timestamp is not the same');
    }

    /**
     * testCreatedPresent
     *
     * @return void
     * @triggers Model.beforeSave
     */
    public function testCreatedPresent()
    {
        $ts = new \DateTime('2000-01-01');
        $this->Behavior->timestamp($ts);

        $event = new Event('Model.beforeSave');
        $existingValue = new \DateTime('2011-11-11');
        $entity = new Entity(['name' => 'Foo', 'created' => $existingValue]);

        $return = $this->Behavior->handleEvent($event, $entity);
        $this->assertTrue($return, 'Handle Event is expected to always return true');
        $this->assertSame((int) $existingValue->format('U'), $entity->created, 'Created timestamp is expected to be unchanged');
    }

    /**
     * testCreatedNotNew
     *
     * @return void
     * @triggers Model.beforeSave
     */
    public function testCreatedNotNew()
    {
        $ts = new \DateTime('2000-01-01');
        $this->Behavior->timestamp($ts);

        $event = new Event('Model.beforeSave');
        $entity = new Entity(['name' => 'Foo']);
        $entity->isNew(false);

        $return = $this->Behavior->handleEvent($event, $entity);
        $this->assertTrue($return, 'Handle Event is expected to always return true');
        $this->assertNull($entity->created, 'Created timestamp is expected to be untouched if the entity is not new');
    }

    /**
     * testModifiedAbsent
     *
     * @return void
     * @triggers Model.beforeSave
     */
    public function testModifiedAbsent()
    {
        $ts = new \DateTime('2000-01-01');
        $this->Behavior->timestamp($ts);

        $event = new Event('Model.beforeSave');
        $entity = new Entity(['name' => 'Foo']);
        $entity->isNew(false);

        $return = $this->Behavior->handleEvent($event, $entity);
        $this->assertTrue($return, 'Handle Event is expected to always return true');
        $this->assertInternalType('integer', $entity->modified);
        $this->assertSame((int) $ts->format('U'), $entity->modified, 'Modified timestamp is not the same');
    }

    /**
     * testModifiedPresent
     *
     * @return void
     * @triggers Model.beforeSave
     */
    public function testModifiedPresent()
    {
        $ts = new \DateTime('2000-01-01');
        $this->Behavior->timestamp($ts);

        $event = new Event('Model.beforeSave');
        $existingValue = new \DateTime('2011-11-11');
        $entity = new Entity(['name' => 'Foo', 'modified' => $existingValue]);
        $entity->clean();
        $entity->isNew(false);

        $return = $this->Behavior->handleEvent($event, $entity);
        $this->assertTrue($return, 'Handle Event is expected to always return true');
        $this->assertInternalType('integer', $entity->modified);
        $this->assertSame((int) $ts->format('U'), $entity->modified, 'Modified timestamp is not the same');
    }

    /**
     * testInvalidEventConfig
     *
     * @return void
     * @triggers Model.beforeSave
     */
    public function testInvalidEventConfig()
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('When should be one of "always", "new" or "existing". The passed value "fat fingers" is invalid');
        $table = $this->getMockBuilder('Cake\ORM\Table')->getMock();
        $settings = ['events' => ['Model.beforeSave' => ['created' => 'fat fingers']]];
        $this->Behavior = new UnixTimestampBehavior($table, $settings);

        $event = new Event('Model.beforeSave');
        $entity = new Entity(['name' => 'Foo']);
        $this->Behavior->handleEvent($event, $entity);
    }

    /**
     * testGetTimestamp
     *
     * @return void
     */
    public function testGetTimestamp()
    {
        $return = $this->Behavior->timestamp();
        $this->assertInternalType('integer', $return);

        $now = Time::now();
        $this->assertSame((int) $now->format('U'), $return);
    }

    /**
     * testGetTimestampPersists
     *
     * @return void
     */
    public function testGetTimestampPersists()
    {
        $initialValue = $this->Behavior->timestamp();
        $postValue = $this->Behavior->timestamp();
        $this->assertSame(
            $initialValue,
            $postValue,
            'The timestamp should be exactly the same object'
        );
    }

    /**
     * testSetTimestampExplicit
     *
     * @return void
     */
    public function testSetTimestampExplicit()
    {
        $ts = new \DateTime();
        $this->Behavior->timestamp($ts);
        $return = $this->Behavior->timestamp();

        $this->assertSame((int) $ts->format('U'), $return, 'Should return the same value as initially set');
    }

    /**
     * testTouch
     *
     * @return void
     */
    public function testTouch()
    {
        $ts = new \DateTime('2000-01-01');
        $this->Behavior->timestamp($ts);

        $entity = new Entity(['username' => 'timestamp test']);
        $return = $this->Behavior->touch($entity);
        $this->assertTrue($return, 'touch is expected to return true if it sets a field value');
        $this->assertSame((int) $ts->format('U'), $entity->modified, 'Modified field is expected to be updated');
        $this->assertNull($entity->created, 'Created field is NOT expected to change');
    }

    /**
     * testTouchNoop
     *
     * @return void
     */
    public function testTouchNoop()
    {
        $table = $this->getMockBuilder('Cake\ORM\Table')->getMock();
        $config = [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                ]
            ]
        ];

        $this->Behavior = new UnixTimestampBehavior($table, $config);
        $ts = new \DateTime('2000-01-01');
        $this->Behavior->timestamp($ts);

        $entity = new Entity(['username' => 'timestamp test']);
        $return = $this->Behavior->touch($entity);
        $this->assertFalse($return, 'touch is expected to do nothing and return false');
        $this->assertNull($entity->modified, 'Modified field is NOT expected to change');
        $this->assertNull($entity->created, 'Created field is NOT expected to change');
    }

    /**
     * testTouchCustomEvent
     *
     * @return void
     */
    public function testTouchCustomEvent()
    {
        $table = $this->getMockBuilder('Cake\ORM\Table')->getMock();
        $settings = ['events' => ['Something.special' => ['date_specialed' => 'always']]];
        $this->Behavior = new UnixTimestampBehavior($table, $settings);
        $ts = new \DateTime('2000-01-01');
        $this->Behavior->timestamp($ts);

        $entity = new Entity(['username' => 'timestamp test']);
        $return = $this->Behavior->touch($entity, 'Something.special');
        $this->assertTrue($return, 'touch is expected to return true if it sets a field value');
        $this->assertSame((int) $ts->format('U'), $entity->date_specialed, 'Modified field is expected to be updated');
        $this->assertNull($entity->created, 'Created field is NOT expected to change');
    }

    /**
     * Test that calling save, triggers an insert including the created and updated field values
     *
     * @return void
     */
    public function testSaveTriggersInsert()
    {
        $this->loadFixtures('Users');
        $table = TableRegistry::get('users');
        $table->addBehavior('Oppara/UnixTimestamp.UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always'
                ]
            ]
        ]);

        $entity = new Entity(['username' => 'timestamp test']);
        $now = Time::now();
        $return = $table->save($entity);
        $this->assertSame($entity, $return, 'The returned object is expected to be the same entity object');

        $row = $table->find('all')->where(['id' => $entity->id])->first();

        $this->assertEquals($now->format('U'), $row->created);
        $this->assertEquals($now->format('U'), $row->updated);
    }
}

