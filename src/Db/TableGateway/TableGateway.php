<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Db\TableGateway;

use Drone\Db\SQLFunction;
use Drone\Exception;

/**
 * TableGateway class
 *
 * This class is a query builder for CRUD (create, read, update, delete)
 */
class TableGateway extends AbstractTableGateway implements TableGatewayInterface
{
    /**
     * The text with the last query executed
     *
     * @var string
     */
    protected $lastQuery;

    /**
     * An array with the last binded values
     *
     * @var array
     */
    protected $lastValues;

    /**
     * Returns the lastQuery
     *
     * @return string
     */
    public function getLastQuery()
    {
        return $this->lastQuery;
    }

    /**
     * Returns the lastValues
     *
     * @return array
     */
    public function getLastValues()
    {
        return $this->lastValues;
    }

    /**
     * Select statement
     *
     * @param array $where
     *
     * @return array With all results
     */
    public function select(Array $where = [])
    {
        $bind_values = [];

        $driver = $this->getDb()->getDriverName();

        if (count($where)) {
            $parsed_where = [];

            $k = 0;

            foreach ($where as $key => $value) {
                $k++;

                if (is_null($value)) {
                    $parsed_where[] = "$key IS NULL";
                } elseif ($value instanceof SQLFunction) {
                    $parsed_where[] = "$key = " . $value->getStatement();
                } elseif (is_array($value)) {
                    $parsed_in = [];

                    foreach ($value as $in_value) {
                        switch ($driver) {
                            case 'Oci8':
                                $parsed_in[] = ":$k";
                                $bind_values[":$k"] = $in_value;
                                break;

                            case 'Mysqli' || 'Sqlsrv':
                                $parsed_in[] = "?";
                                $bind_values[] = $in_value;
                                break;
                        }

                        $k++;
                    }

                    $parsed_where[] = "$key IN (" . implode(", ", $parsed_in) . ")";
                } else {
                    switch ($driver) {
                        case 'Oci8':
                            $parsed_where[] = "$key = :$k";
                            $bind_values[":$k"] = $value;
                            break;

                        case 'Mysqli' || 'Sqlsrv':
                            $parsed_where[] = "$key = ?";
                            $bind_values[] = $value;
                            break;
                    }
                }
            }

            $where = "WHERE \r\n\t" . implode(" AND\r\n\t", $parsed_where);
        } else {
            $where = "";
        }

        $table = $this->entity->getTableName();

        $sql = "SELECT * \r\nFROM {$table}\r\n$where";

        $this->lastQuery = $sql;
        $this->lastValues = $bind_values;

        if (count($bind_values)) {
            $this->getDb()->execute($sql, $bind_values);
        } else {
            $this->getDb()->execute($sql);
        }

        return $this->getDb()->getArrayResult();
    }

    /**
     * Insert statement
     *
     * @param array $data
     *
     * @throws RuntimeException from internal execute()
     * @throws LogicException
     *
     * @return resource|object
     */
    public function insert(Array $data)
    {
        if (!count($data)) {
            throw new \LogicException("Missing values for INSERT statement!");
        }

        $bind_values = [];

        $driver = $this->getDb()->getDriverName();

        $k = 0;

        $null_keys = [];

        foreach ($data as $key => $value) {
            $k++;

            # insert NULL values cause problems when BEFORE INSERT triggers are
            # defined to assigns values over fields. For SQLServer is better not
            # pass NULL values
            if ($driver == 'Sqlsrv' && is_null($value)) {
                $null_keys[] = $key;
                continue;
            }

            if (is_null($value)) {
                $value = "NULL";
            } elseif ($value instanceof SQLFunction) {
                $value = $value->getStatement();
            } else {
                switch ($driver) {
                    case 'Oci8':
                        $bind_values[":$k"] = $value;
                        $value = ":$k";
                        break;

                    case 'Mysqli' || 'Sqlsrv':
                        $bind_values[] = $value;
                        $value = "?";
                        break;
                }
            }

            $data[$key] = $value;
        }

        foreach ($null_keys as $key) {
            unset($data[$key]);
        }

        $cols = implode(",\r\n\t", array_keys($data));
        $vals = implode(",\r\n\t", array_values($data));

        $table = $this->entity->getTableName();

        $sql = "INSERT INTO {$table} \r\n(\r\n\t$cols\r\n) \r\nVALUES \r\n(\r\n\t$vals\r\n)";

        $this->lastQuery = $sql;
        $this->lastValues = $bind_values;

        return $this->getDb()->execute($sql, $bind_values);
    }

    /**
     * Update statement
     *
     * @param array $set
     * @param array $where
     *
     * @throws RuntimeException from internal execute()
     * @throws LogicException
     * @throws Exception\SecurityException
     *
     * @return resource|object
     */
    public function update(Array $set, Array $where)
    {
        $parsed_set = [];

        if (!count($set)) {
            throw new \LogicException("You cannot update rows without SET clause");
        }

        if (!count($where)) {
            throw new Exception\SecurityException("You cannot update rows without WHERE clause!");
        }

        $bind_values = [];

        $driver = $this->getDb()->getDriverName();

        $k = 0;

        foreach ($set as $key => $value) {
            $k++;

            if (is_null($value)) {
                $parsed_set[] = "$key = NULL";
            } elseif ($value instanceof SQLFunction) {
                $parsed_set[] = "$key = " . $value->getStatement();
            } elseif (is_array($value)) {
                $parsed_in = [];

                foreach ($value as $in_value) {
                    switch ($driver) {
                        case 'Oci8':
                            # [POSSIBLE BUG] - To Future revision (What about non-string values ?)
                            if (is_string($in_value)) {
                                $parsed_in[] = ":$k";
                            }

                            $bind_values[":$k"] = $in_value;
                            break;

                        case 'Mysqli' || 'Sqlsrv':
                            $parsed_in[] = "?";
                            $bind_values[] = $in_value;
                            break;
                    }

                    $k++;
                }

                $parsed_set[] = "$key IN (" . implode(", ", $parsed_in) . ")";
            } else {
                switch ($driver) {
                    case 'Oci8':
                        $parsed_set[] = "$key = :$k";
                        $bind_values[":$k"] = $value;
                        break;

                    case 'Mysqli' || 'Sqlsrv':
                        $parsed_set[] = "$key = ?";
                        $bind_values[] = $value;
                        break;
                }
            }
        }

        $parsed_set = implode(",\r\n\t", $parsed_set);

        $parsed_where = [];

        foreach ($where as $key => $value) {
            $k++;

            if (is_null($value)) {
                $parsed_where[] = "$key IS NULL";
            } elseif ($value instanceof SQLFunction) {
                $parsed_where[] = "$key = " . $value->getStatement();
            } elseif (is_array($value)) {
                $parsed_in = [];

                foreach ($value as $in_value) {
                    switch ($driver) {
                        case 'Oci8':
                            $parsed_in[] = ":$k";
                            $bind_values[":$k"] = $in_value;
                            break;

                        case 'Mysqli' || 'Sqlsrv':
                            $parsed_in[] = "?";
                            $bind_values[] = $in_value;
                            break;
                    }

                    $k++;
                }

                $parsed_where[] = "$key IN (" . implode(", ", $parsed_in) . ")";
            } else {
                switch ($driver) {
                    case 'Oci8':
                        $parsed_where[] = "$key = :$k";
                        $bind_values[":$k"] = $value;
                        break;

                    case 'Mysqli' || 'Sqlsrv':
                        $parsed_where[] = "$key = ?";
                        $bind_values[] = $value;
                        break;
                }
            }
        }

        $parsed_where = implode(" AND\r\n\t", $parsed_where);

        $table = $this->entity->getTableName();

        $sql = "UPDATE {$table} \r\nSET \r\n\t$parsed_set \r\nWHERE \r\n\t$parsed_where";

        $this->lastQuery = $sql;
        $this->lastValues = $bind_values;

        return $this->getDb()->execute($sql, $bind_values);
    }

    /**
     * Delete statement
     *
     * @param array $where
     *
     * @throws RuntimeException from internal execute()
     * @throws Exception\SecurityException
     *
     * @return resource|object
     */
    public function delete(Array $where)
    {
        if (count($where)) {
            $parsed_where = [];

            $bind_values = [];

            $driver = $this->getDb()->getDriverName();

            $k = 0;

            foreach ($where as $key => $value) {
                $k++;

                if (is_null($value)) {
                    $parsed_where[] = "$key IS NULL";
                } elseif ($value instanceof SQLFunction) {
                    $parsed_where[] = "$key = " . $value->getStatement();
                } elseif (is_array($value)) {
                    $parsed_in = [];

                    foreach ($value as $in_value) {
                        switch ($driver) {
                            case 'Oci8':
                                $parsed_in[] = ":$k";
                                $bind_values[":$k"] = $in_value;
                                break;

                            case 'Mysqli' || 'Sqlsrv':
                                $parsed_in[] = "?";
                                $bind_values[] = $in_value;
                                break;
                        }

                        $k++;
                    }

                    $parsed_where[] = "$key IN (" . implode(", ", $parsed_in) . ")";
                } else {
                    switch ($driver) {
                        case 'Oci8':
                            $parsed_where[] = "$key = :$k";
                            $bind_values[":$k"] = $value;
                            break;

                        case 'Mysqli' || 'Sqlsrv':
                            $parsed_where[] = "$key = ?";
                            $bind_values[] = $value;
                            break;
                    }
                }
            }

            $where = "\r\nWHERE \r\n\t" . implode(" AND\r\n\t", $parsed_where);
        } else {
            throw new Exception\SecurityException(
                "You cannot delete rows without WHERE clause!. Use TRUNCATE statement instead."
            );
        }

        $table = $this->entity->getTableName();

        $sql = "DELETE FROM {$table} $where";

        $this->lastQuery = $sql;
        $this->lastValues = $bind_values;

        return $this->getDb()->execute($sql, $bind_values);
    }
}
