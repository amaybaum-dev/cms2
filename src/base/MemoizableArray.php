<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\base;

use craft\helpers\ArrayHelper;
use craft\helpers\Json;

/**
 * MemoizableArray represents an array of values that need to be run through [[ArrayHelper::where()]] or [[ArrayHelper::firstWhere()]] repeatedly,
 * where it could be beneficial if the results were memoized.
 *
 * Any class properties that are set to an instance of this class should be excluded from class serialization:
 *
 * ```php
 * public function __serialize()
 * {
 *     $vars = get_object_vars($this);
 *     unset($vars['myMemoizedPropertyName'];
 *     return $vars;
 * }
 * ```
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.5.8
 */
class MemoizableArray extends \ArrayObject
{
    private array $_memoized = [];

    /**
     * Returns all items.
     *
     * @return array
     */
    public function all(): array
    {
        // It's not clear from the PHP docs whether there is a difference between
        // casting this as an array or calling getArrayCopy(). Casting feels safer though.
        return (array)$this;
    }

    /**
     * Filters the array to only the values where a given key (the name of a sub-array key or sub-object property) is set to a given value.
     *
     * Array keys are preserved by default.
     *
     * @param string $key the column name whose result will be used to index the array
     * @param mixed $value the value that `$key` should be compared with
     * @param bool $strict whether a strict type comparison should be used when checking array element values against `$value`
     * @return self the filtered array
     */
    public function where(string $key, $value = true, bool $strict = false): self
    {
        $memKey = $this->_memKey(__METHOD__, $key, $value, $strict);

        if (!isset($this->_memoized[$memKey])) {
            $this->_memoized[$memKey] = new MemoizableArray(ArrayHelper::where($this, $key, $value, $strict, false));
        }

        return $this->_memoized[$memKey];
    }

    /**
     * Filters the array to only the values where a given key (the name of a sub-array key or sub-object property)
     * is set to one of a given range of values.
     *
     * Array keys are preserved by default.
     *
     * @param string $key the column name whose result will be used to index the array
     * @param mixed[] $values the value that `$key` should be compared with
     * @param bool $strict whether a strict type comparison should be used when checking array element values against `$values`
     * @return self the filtered array
     */
    public function whereIn(string $key, array $values, bool $strict = false): self
    {
        $memKey = $this->_memKey(__METHOD__, $key, $values, $strict);

        if (!isset($this->_memoized[$memKey])) {
            $this->_memoized[$memKey] = new MemoizableArray(ArrayHelper::whereIn($this, $key, $values, $strict, false));
        }

        return $this->_memoized[$memKey];
    }

    /**
     * Returns the first value where a given key (the name of a sub-array key or sub-object property) is set to a given value.
     *
     * @param string $key the column name whose result will be used to index the array
     * @param mixed $value the value that `$key` should be compared with
     * @param bool $strict whether a strict type comparison should be used when checking array element values against `$value`
     * @return mixed the first matching value, or `null` if no match is found
     */
    public function firstWhere(string $key, $value = true, bool $strict = false)
    {
        $memKey = $this->_memKey(__METHOD__, $key, $value, $strict);

        // Use array_key_exists() because it could be null
        if (!array_key_exists($memKey, $this->_memoized)) {
            $this->_memoized[$memKey] = ArrayHelper::firstWhere($this, $key, $value, $strict);
        }

        return $this->_memoized[$memKey];
    }

    /**
     * Generates a memoization key.
     *
     * @param string $method
     * @param string $key
     * @param mixed $value
     * @param bool $strict
     * @return string
     */
    private function _memKey(string $method, string $key, $value, bool $strict): string
    {
        if (!is_scalar($value)) {
            $value = Json::encode($value);
        }
        return "$method:$key:$value:$strict";
    }

    /**
     * @inheritdoc
     */
    public function append($value): void
    {
        parent::append($value);
        $this->_memoized = [];
    }

    /**
     * @inheritdoc
     */
    public function asort($flags = SORT_REGULAR): void
    {
        parent::asort($flags);
        $this->_memoized = [];
    }

    /**
     * @inheritdoc
     */
    public function exchangeArray($array): array
    {
        $return = parent::exchangeArray($array);
        $this->_memoized = [];
        return $return;
    }

    /**
     * @inheritdoc
     */
    public function ksort($flags = SORT_REGULAR): void
    {
        parent::ksort($flags);
        $this->_memoized = [];
    }

    /**
     * @inheritdoc
     */
    public function natcasesort(): void
    {
        parent::natcasesort();
        $this->_memoized = [];
    }

    /**
     * @inheritdoc
     */
    public function natsort(): void
    {
        parent::natsort();
        $this->_memoized = [];
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($key, $value): void
    {
        parent::offsetSet($key, $value);
        $this->_memoized = [];
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($key): void
    {
        parent::offsetUnset($key);
        $this->_memoized = [];
    }

    /**
     * @inheritdoc
     */
    public function uasort($callback): void
    {
        parent::uasort($callback);
        $this->_memoized = [];
    }

    /**
     * @inheritdoc
     */
    public function uksort($callback): void
    {
        parent::uksort($callback);
        $this->_memoized = [];
    }
}
