<?php
/**
 * Lib\User
 * PHP version 7
 *
 * @category  Database
 * @package   Util
 * @author    Bill Rocha <prbr@ymail.com>
 * @copyright 2016 Bill Rocha <http://google.com/+BillRocha>
 * @license   <https://opensource.org/licenses/MIT> MIT
 * @version   GIT: 0.0.1
 * @link      http://paulorocha.tk/github/devbr
 */

namespace Lib;

use Lib\Db;

/**
 * User Class
 *
 * @category Database
 * @package  Util
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     http://paulorocha.tk/github/devbr
 */
class User
{
    public static $node = null;

    private $_login = false;
    private $_data = ['id'=>null,
                     'name'=>null,
                     'token'=>null,
                     'life'=>null,
                     'login'=>null,
                     'password'=>null,
                     'level'=>null,
                     'status'=>null];

    private $_db = null;
    private $_dbConfig = ['table'=>'user',
                         'id'=>'id',
                         'name'=>'name',
                         'token'=>'token',
                         'life'=>'life',
                         'login'=>'login',
                         'password'=>'password',
                         'level'=>'level',
                         'status'=>'status'];

    /**
     * Constructor
     *
     * @param array|null $config Configuration data (or null)
     */
    public function __construct($config = null)
    {
        if ($config !== null) {
            foreach ($config as $i => $d) {
                if (isset($this->_dbConfig[$i])) {
                    $this->_dbConfig[$i] = $d;
                }
            }
        } elseif (method_exists('Config\Neos\Database', 'getUserConfig')) {
            $this->_dbConfig = \Config\Neos\Database::getUserConfig();
        }

        $this->_db = new Db;
    }

    /**
     * Singleton instance
     *
     * @return object this
     */
    public static function this()
    {
        if (is_object(static::$node)) {
            return static::$node;
        }
        //else...
        list($config) = array_merge(func_get_args(), [null]);
        return static::$node = new static($config);
    }

    /**
     * Initialize user
     *
     * @param string $login    user login
     * @param string $password user password
     *
     * @return bool            True/false to login
     */
    public function doLogin($login, $password)
    {
        $this->_db->query(
            'SELECT * FROM '.$this->_dbConfig['table']
            .' WHERE '.$this->_dbConfig['login'].' = :lg 
               AND '.$this->_dbConfig['password'].' = :pw',
            [':lg'=>$login, ':pw'=>$password]
        );

        $row = $this->_db->result();
        if (isset($row[0])) {
            $row = $row[0]->getAll();

            foreach ($this->_data as $i => $d) {
                if (isset($row[$this->_dbConfig[$i]])) {
                    $this->_data[$i] = $row[$this->_dbConfig[$i]];
                }
            }
            $this->_login = true;
            return true;
        }
        $this->_login = false;
        return false;
    }

    /**
     * Performer LOGOUT
     *
     * @param integer $id the ID from user
     *
     * @return array     Array of data from user login
     */
    public function logout($id = null)
    {
        if ($id !== null) {
            $this->_data['id'] = $id;
        }

        //Reset
        $this->_data['life'] = 0;
        $this->_data['token'] = md5(microtime());
        
        $this->_db->query(
            'UPDATE '.$this->_dbConfig['table']
                         .' SET '.$this->_dbConfig['token'].'="'
                         .$this->_data['token'].'", '
                         .$this->_dbConfig['life'].' = "'
                         .$this->_data['life'].'" WHERE '
                         .$this->_dbConfig['id'].' = '
            .$this->_data['id']
        );
    }

    /**
     * Initialize user by ID
     *
     * @param integer $id the ID
     *
     * @return bool|string     false or $this->_data by ID
     */
    public function getById($id)
    {
        $this->_db->query(
            'SELECT * FROM '.$this->_dbConfig['table']
                          .' WHERE '.$this->_dbConfig['id'].' = :id',
            [':id'=>$id]
        );
        $row = $this->_db->result();
        if (isset($row[0])) {
            $row = $row[0]->getAll();

            $this->_login = true; //Setando LOGIN como válido/logado

            foreach ($this->_data as $i => $d) {
                if (isset($row[$this->_dbConfig[$i]])) {
                    $this->_data[$i] = $row[$this->_dbConfig[$i]];
                }
            }
            return $this->_data;
        }
        return false;
    }


    /**
     * Set TOKEN data key
     *
     * @param string $token The TOKEN string
     *
     * @return bool        False/true for ok
     */
    public function saveToken($token)
    {
        $rows = $this->_db->query(
            'UPDATE '.$this->_dbConfig['table']
                        .' SET '.$this->_dbConfig['token'].' = :tk'
                        .' WHERE '.$this->_dbConfig['id'].' = :id',
            [':tk'=>$token, ':id'=>$this->_data['id']]
        );
        if ($rows > 0) {
            $this->_data['token'] = $token;
            return true;
        }
        return false;
    }

    /**
     * Get Token by id
     *
     * @param integer $id The user ID (integer)
     *
     * @return bool|string     return TOKEN or false
     */
    public function getToken($id)
    {
        $row = $this->_db->query(
            'SELECT token FROM '.$this->_dbConfig['table']
                          .' WHERE '.$this->_dbConfig['id'].' = :id',
            [':id'=>$id]
        );
        if (isset($row[0])) {
            $row = $row[0]->getAll();
            return $row[$this->_dbConfig['token']];
        }
        return false;
    }

    /**
     * Universal GET
     *
     * @param string $node null return ALL data (array)
     *
     * @return array    return $this->_data
     */
    public function get($node = null)
    {
        if ($node === null) {
            return $this->_data;
        }
        return (isset($this->_data[$node])) ? $this->_data[$node] : false;
    }

    /**
     * Universal SET
     *
     * @param array|string   $node  Se for um array, grava todos os dados
     *                              Se for um string e existir, grava $value
     *                              Se for um string e existir, grava $value
     *                              Se for um string e existir, grava $value
     * @param string|integer $value [optional] valor a ser gravado
     *
     * @return object (this)
     */
    public function set($node, $value = null)
    {
        if (!is_array($node)) {
            $node[$node] = $value;
        }
        foreach ($node as $i => $d) {
            if (isset($this->_data[$i])) {
                $this->_data[$i] = $d;
            }
        }
        return $this;
    }


    /**
     * Save this USER on DataBase
     *
     * @param Integer $id [optional] set a ID from this user
     *                    $id = "null" (or none) gera INSERT new user
     *
     * @return array      INSERT/UPDATE & rows (0 => indica não salvo)
     */
    public function save($id = null)
    {
        //update this user id value
        if ($id !== null) {
            $this->_data['id'] = $id;
        }

        if ($this->_data['id'] !== null) {
            $action = 'UPDATE ';
            $where = ' WHERE '.$this->_dbConfig['id'].' = :id';
        } else {
            $action = 'INSERT INTO ';
            $where = '';
        }

        $cols = '';
        $vals = [];
        foreach ($this->_data as $k => $v) {
            if ($k !== 'id') {
                $cols .= $this->_dbConfig[$k].' = :'.$k.',';
            }
            $vals[':'.$k] = $v;
        }
        
        $cols = substr($cols, 0, -1); //tirando a ultima vírgula

        $this->_db->query(
            $action.$this->_dbConfig['table']
            .' SET '.$cols.$where, $vals
        );
        return ['action'=>substr($action, 0, 5),
                'rows'=>$this->_db->getRows()];
    }
}
