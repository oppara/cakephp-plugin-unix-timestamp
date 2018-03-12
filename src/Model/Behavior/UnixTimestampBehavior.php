<?php
namespace Oppara\UnixTimestamp\Model\Behavior;

use Cake\ORM\Behavior\TimestampBehavior;
use Cake\ORM\Table;
use DateTime;

/**
 * UnixTimestamp behavior
 */
class UnixTimestampBehavior extends TimestampBehavior
{
    /**
     * Get or set the unix timestamp to be used
     *
     * Set the timestamp to the given DateTime object, or if not passed a new DateTime object
     * If an explicit date time is passed, the config option `refreshTimestamp` is
     * automatically set to false.
     *
     * @param \DateTime|null $ts Timestamp
     * @param bool $refreshTimestamp If true timestamp is refreshed.
     * @return integer unix timestamp
     */
    public function timestamp(DateTime $ts = null, $refreshTimestamp = false)
    {
        parent::timestamp($ts, $refreshTimestamp);

        return (int) $this->_ts->format('U');
    }

    /**
     * Update a field, if it hasn't been updated already
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity instance.
     * @param string $field Field name
     * @param bool $refreshTimestamp Whether to refresh timestamp.
     * @return void
     */
    protected function _updateField($entity, $field, $refreshTimestamp)
    {
        if ($entity->isDirty($field)) {
            if ($entity->{$field} instanceof DateTime) {
                $entity->set($field, $this->timestamp($entity->{$field}));
            }
            return;
        }
        $entity->set($field, $this->timestamp(null, $refreshTimestamp));
    }
}
