<?php

/*
    Copyright (c) 2014-2016 hazardland

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.
*/

    namespace db
    {
        const title = 'Db';
        const build = 1;
        const label = 0.9;

        /**
         * goals:
         * 1. code first (generate database tables, maintain class property changes, alter field alter field properties)
         * 2. database abstraction interface
         * 3. caching engine
         * 4. cache abstraction interface
         * 5. language interface
         * 6. multilang field support (field_1_en, field_1_ge, field_1_fr, field_1_fr, ...)
         * 7. permission support
         * 8. user abstrction interface
         * 9. group abstraction interface
         *
         * objects with which database can work via interfaces:
         * user - your instance of user
         * group - your instance of user group
         * locale - your instance of locale
         * locales - your instance of locales
         * cache - your instance of cache
         * link - your database connection
         * solution
         * project
         */

        /*
         * table [original_name] (use table named 'original_name' for this class)
         * database [database_name] (specify database of table)
         * link [link_name] (table is using link 'link_name', i.e: 'mysql_link_1')
         * prefix [foo_] (table field prefix is 'foo_', we ignore it in property names, but maintain when addressing tables)
         * order [field_name:[asc|desc]] (order name:asc,date,count:desc please avoid space)
         * charset [utf8] (table default charset)
         * engine [myisam]
         * rename [original_name] (rename table 'original_name' to the class current name)
         * cache [none|load|user|long] (select your cache type, long=\apcu by default, user=session, load=per_scrip_life)
         * scope [project|solution] (select your cache scope, used by system.php framework)
         * unique [name] (under developement, define simple unique index)
         * unique [search id, name] (under developement, define compound unique index)
         * index [fast id, name] (under developement, define compound index)
         * ignore (ignore this table in model)
         * deny [insert|select|update|delete] (deny some for this table)
         */

        class foo
        {
            /**
             * required (this field is requrired)
             * field/column [field_name] (use field named 'field_name' from this table for this property)
             * type [integer|boolean|float|text|binary|date|time]
             * length [length] (length value for field i.e. 8)
             * locale (flag this property as localized to automatically create fields for each locale)
             * enum (flag this property as enumeration of same type objecs specified in @var flag)
             * unsigned (flag propertty field as unsigned)
             * zerofill (enable zerofill for this property field)
             * default [value] (specify default value)
             * primary (flag property field as pripmary)
             * rename [field_name] (rename property field 'field_name' to current field name)
             * first (make this field first if just creating)
             * after [property_name] (alter this property field after 'property_name' field if just creating)
             * ignore (ignore this property in mapping)
             * foreign [\name\of\class] (set up relation to different class object, one to one if no enum, else one to many)
             * deny [insert|select|update] (allow this field in insert qeuery)
             * allow [insert|select|update] (allow this field in update qeuery)
             * deny [insert for user [username]] (coming soon)
             * on insert set [date|user]
             * on update set [date|user]
             * @var [\name\of\class|integer|boolean|float|text|binary|date|time] (define basic type of field or setup relation also and define property class)
             */
            public $name;

        }

        class link
        {
            public $name;
            public $debug = false;
            public $engine;
            /**
             * @var PDO
             */
            public $link;
            public $config = array (\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'", \PDO::ATTR_PERSISTENT => true);
            public $count = 0;
            public function __construct ($name=null, $database='mysql:host=127.0.0.1', $username='root', $password='1234', $config=null)
            {
                if (strpos($database,'mysql:')===0)
                {
                    $this->engine = 'myisam';
                }
                $this->name = $name;
                if (false && $config)
                {
                    $this->config = $config;
                }
                try
                {
                    $this->link = new \PDO ($database, $username, $password, $this->config);
                }
                catch (PDOException $error)
                {
                    echo $error->getMessage();
                }
            }
            public function select ($query)
            {
                $this->count++;
                if ($this->debug)
                {
                    $result = $this->link->query ($query);
                    if ($this->error())
                    {
                        $error = $this->error();
                        $debug = $query.($error[2]?"<p>".$error[2]:"");
                        debug ($debug,$this);
                    }
                    else
                    {
                        debug($query,$this);
                    }
                    return $result;
                }
                return $this->link->query ($query);
            }
            public function query ($query)
            {
                $this->count++;
                if ($this->debug)
                {
                    $result = $this->link->query ($query);
                    if ($this->error())
                    {
                        $error = $this->error();
                        $debug = $query.($error[2]?"<p>".$error[2]:"");
                        debug ($debug,$this);
                    }
                    else
                    {
                        debug($query,$this);
                    }
                    return $result;
                }
                return $this->link->query ($query);
            }
            public function value ($query)
            {
                $this->count++;
                $result = $this->link->query ($query);
                if ($this->debug)
                {
                    if ($this->error())
                    {
                        $error = $this->error();
                        $debug = $query.($error[2]?"<p>".$error[2]:"");
                        debug ($debug,$this);
                    }
                    else
                    {
                        debug($query,$this);
                    }
                }
                if ($result)
                {
                    return $result->fetchColumn(0);
                }
            }
            public function fetch ($query)
            {
                $this->count++;
                $result = $this->link->query ($query);
                if ($this->debug)
                {
                    if ($this->error())
                    {
                        $error = $this->error();
                        $debug = $query.($error[2]?"<p>".$error[2]:"");
                        debug ($debug,$this);
                    }
                    else
                    {
                        debug($query,$this);
                    }
                }
                if ($result)
                {
                    return $result->fetch();
                }
            }
            public function error ($code=null)
            {
                if ($code!==null)
                {
                    $error = $this->link->errorInfo();
                    if ($error && $error[0]==$code)
                    {
                        return true;
                    }
                    return false;
                }
                return $this->link->errorInfo();
            }
            public function id ()
            {
                return $this->link->lastInsertId();
            }
        }

        class flag
        {
            public $name;
            public $value;
            public function __construct ($name, $value=null)
            {
                $this->name = $name;
                $this->value = $value;
            }
            /**
             *
             * @param string $line
             * @return \db\flag
             */
            public static function flag ($line)
            {
                $line = trim ($line);
                if ($line)
                {
                    $start = strpos($line, '*');
                    if ($start!==false)
                    {
                        $start++;
                        $crop = trim(substr($line,$start));
                        $set = explode(" ", $crop);
                        if (is_array($set) && count($set))
                        {
                            $count = count ($set);
                            if ($count===1)
                            {
                                return new flag($set[0]);
                            }
                            else if ($count===2)
                            {
                                return new flag($set[0],$set[1]);
                            }
                            else if ($count>2)
                            {
                                return new flag($set[0],$set);
                            }
                        }
                    }
                }
            }
            /**
             * @param \ReflectionProperty $value
             * @param \ReflectionClass $value
             * @return flag[] array of flags
             */
            public static function set ($value)
            {
                if ($value!=null)
                {
                    $comment = $value->getDocComment();
                }
                if ($comment)
                {
                    $result = array();
                    $lines = explode ("\n", $comment);
                    if (is_array($lines) && $lines)
                    {
                        foreach ($lines as $line)
                        {
                            $flag = self::flag($line);
                            if ($flag!=null)
                            {
                                $result[] = $flag;
                            }
                        }
                        return $result;
                    }
                }
                return array();
            }
        }

        class field
        {
            /**
             * name of field
             * @var string
             */
            public $name;

            /**
             * actual name of field
             * @var string
             */
            public $column;

            /**
             * basic type of field
             * @var int
             */
            public $type;

            /**
             * foreign field table id
             * @var \ReflectionClass
             */
            public $foreign = null;

            /**
             * sql store type of field
             * @var string
             */
            public $data;

            /**
             * @var bool
             */
            public $locale = false;
            /**
             * @var bool
             */
            public $enum = false;
            /**
             * @var bool
             */
            public $lazy = false;
            /**
             * @var bool
             */
            public $required = false;

            /**
             * @var primary
             */
            public $primary = false;

            /**
             * @var bool
             */
            public $insert = true;
            /**
             * @var bool
             */
            public $select = true;
            /**
             * @var bool
             */
            public $update = true;
            /**
             * @var string
             */
            public $length;

            public $default;
            /**
             * not null
             * defualt hihu
             * required
             * length
             * @var boolean
             */
            public $unsigned = false;
            /**
             * @var bool
             */
            public $null = false;
            /**
             * @var bool
             */
            public $zero = false;
            /**
             * @var bool
             */
            public $after = null;
            /**
             * @var bool
             */
            public $first = false;
            public $last = null;
            /**
             * rename from
             * @var string
             */
            public $rename = null;
            /**
             * @var config
             */
            public $config = null;
            /**
             *
             * @var event
             */
            public $event = null;

            public $position;
            public $ignore = null;
            /**
             * field class if any
             * @var \ReflectionClass
             */
            public $class = null;
            public $value = false;
            public function __construct (\db\table $table, \ReflectionProperty $value)
            {
                if ($value==null || $value->isStatic())
                {
                    throw new \Exception();
                }
                $this->event = new event ();
                $this->config = new config ();
                $this->name = $value->getName();
                $this->column = $value->getName();
//                if (strtolower($this->name)=='id')
//                {
//                    $table->primary = &$this;
//                }
                $flags = flag::set($value);
                if (is_array($flags))
                {
                    foreach ($flags as &$flag)
                    {
                        if ($flag->name=='ignore')
                        {
                            throw new \Exception ('field ignored');
                        }
                        /* @var flag \db\flag */
                        else if ($flag->name=='@var' || $flag->name=='type')
                        {
                            if ($flag->value=='')
                            {
                                throw new \Exception('@var doc comment required for '.$this->name.' property');
                            }
                            if ($flag->value=='integer' || $flag->value=='int')
                            {
                                $this->type = type::integer;
                                if ($this->data==null)
                                {
                                    $this->data = 'int';
                                }
                            }
                            else if ($flag->value=='string')
                            {
                                $this->type = type::string;
                                if ($this->data==null)
                                {
                                    $this->data = 'char';
                                }
                                if ($this->length==null)
                                {
                                    $this->length = 128;
                                }
                            }
                            else if ($flag->value=='tinytext' || $flag->value=='text' || $flag->value=='mediumtext' || $flag->value=='longtext')
                            {
                                $this->type = type::string;
                                if ($this->data==null || $this->data=='char') //why data==null ?
                                {
                                    $this->data = $flag->value;
                                    $this->length = null;
                                }
                            }
                            else if ($flag->value=='date')
                            {
                                $this->type = type::date;
                                if ($this->data==null)
                                {
                                    $this->data = 'date';
                                }
                            }
                            else if ($flag->value=='time')
                            {
                                $this->type = type::time;
                                if ($this->data==null)
                                {
                                    $this->data = 'datetime';
                                }
                            }
                            else if ($flag->value=='boolean' || $flag->value=='bool')
                            {
                                $this->type = type::boolean;
                                if ($this->data==null)
                                {
                                    $this->data = 'smallint';
                                }
                                if ($this->length==null)
                                {
                                    $this->length = 1;
                                }
                            }
                            else if ($flag->value=='float')
                            {
                                $this->type = type::float;
                                if ($this->data==null)
                                {
                                    $this->data = 'float';
                                }
                            }
                            else if ($flag->value=='binary')
                            {
                                $this->type = type::binary;
                                if ($this->data==null)
                                {
                                    $this->data = 'blob';
                                }
                            }
                            else if ($flag->name!=='type')
                            {
                                try
                                {
                                    $this->class= new \ReflectionClass($flag->value);
                                }
                                catch (\Exception $error)
                                {
                                    //class not found for this field but thats ok
                                    //because it will load later if found
                                    //debug ("class not found for ".$table->class->getName().".".$this->name);
                                }
                                if ($this->class!=null)
                                {
                                    if ($this->class->isSubclassOf('\db\value'))
                                    {
                                        if ($this->type===null)
                                        {
                                           $this->type = type::string;
                                        }
                                        $this->value = true;
                                    }
                                    else// ($this->class->isSubclassOf('\db\entity'))
                                    {
                                        if (!$this->enum)
                                        {
                                            if ($this->type==null)
                                            {
                                                $this->type = type::integer;
                                            }
                                            if ($this->data===null)
                                            {
                                                $this->data = 'int';
                                            }
//                                            if ($this->length===null)
//                                            {
//                                                $this->length = 10;
//                                            }
//                                            if ($this->type==type::integer)
//                                            {
//                                                $this->unsigned = true;
//                                            }
                                        }
                                        $this->foreign = type ($flag->value);
                                    }
                                    // else
                                    // {
                                    //     throw new \Exception ('field not needed');
                                    // }
                                }
                                else
                                {
                                    $this->foreign = type ($flag->value);
                                    $this->class = $flag->value;
                                    $this->type = type::integer;
                                }
                            }
                        }
                        elseif ($flag->name=='on')
                        {
                            if ($flag->value[1]=='insert')
                            {
                                if ($flag->value[3]=='date')
                                {
                                    $this->event->insert->action = action::date;
                                }
                                else if ($flag->value[2]=='user')
                                {
                                    $this->event->insert->action = action::user;
                                }
                            }
                            else if ($flag->value[1]=='update')
                            {
                                if ($flag->value[3]=='date')
                                {
                                    $this->event->update->action = action::date;
                                }
                                else if ($flag->value[3]=='user')
                                {
                                    $this->event->update->action = action::user;
                                }
                            }
                        }
                        elseif ($flag->name=='required')
                        {
                            $this->required = true;
                        }
                        elseif ($flag->name=='primary')
                        {
                            $this->primary = true;
                        }
                        else if ($flag->name=='default')
                        {
                            $this->default = $flag->value;
                        }
                        else if ($flag->name=='field' || $flag->name=='column')
                        {
                            $this->column= $flag->value;
                        }
                        elseif ($flag->name=='rename')
                        {
                            $this->rename = $flag->value;
                        }
                        elseif ($flag->name=='locale')
                        {
                            $this->locale = true;
                        }
                        elseif ($flag->name=='enum')
                        {
                            $this->type = type::string;
                            if ($this->data!='char' && $this->data!='text' && $this->data!='shorttext')
                            {
                                $this->data = 'char';
                            }
                            if ($this->data=='char' && $this->length===null)
                            {
                                $this->length = 32;
                            }
                            $this->enum = true;
                        }
                        elseif ($flag->name=='lazy')
                        {
                            $this->lazy = true;
                        }
                        elseif ($flag->name=='first')
                        {
                            $this->first = true;
                        }
                        elseif ($flag->name=='after')
                        {
                            $this->after = $flag->value;
                        }
                        elseif ($flag->name=='deny')
                        {
                            if ($flag->value=='insert')
                            {
                                $this->insert = false;
                            }
                            else if ($flag->value=='update')
                            {
                                $this->update = false;
                            }
                            else if ($flag->value=='select')
                            {
                                $this->select = false;
                            }
                        }
                        else if ($flag->name=='length')
                        {
                            $this->length = $flag->value;
                        }
                        else if ($flag->name=='unsigned')
                        {
                            $this->unsigned = true;
                        }
                        else if ($flag->name=='null')
                        {
                            $this->null = true;
                        }
                        else if ($flag->name=='zerofill' || $flag->name=='zero')
                        {
                            $this->zero = true;
                        }
                        if ($flag->name=='type' && $this->data==null)
                        {
                            $this->data = strtolower($flag->value);
                        }
                    }
                }
                if ($this->data===null)
                {
                    $this->data = 'char';
                    //$this->length = 128;
                }
                if ($this->data=='char' && $this->length==null)
                {
                    $this->length = 128;
                }
                if (strtolower($this->default)==='null')
                {
                    $this->null = true;
                }
                else if ($this->null && $this->default===null)
                {
                    $this->default = 'null';
                }
                if ($this->primary)
                {
                    $this->primary();
                }
                if ($this->type==null)
                {
                    $this->type = type::string;
                }
            }
            public function foreign ($value)
            {
                if ($value===null)
                {
                    $this->foreign = null;
                    $this->class = null;
                    return;
                }
                try
                {
                    $this->class= new \ReflectionClass($value);
                }
                catch (\Exception $error)
                {

                }
                if ($this->class!=null)
                {
                    $this->foreign = type ($value);
                }
                else
                {
                    $this->foreign = type ($value);
                    $this->class = $value;
                }
            }
            public function type ()
            {
                $result = strtolower($this->data);
                if ($this->length!=null)
                {
                    $result .= "(".$this->length.")";
                }
                if ($this->unsigned)
                {
                    $result .= ' unsigned';
                }
                return $result;
            }
            public function primary ()
            {
                $this->default = null;
                $this->null = false;
                if (!$this->primary && $this->name=='id')
                {
                    $this->type = type::integer;
                    $this->data = 'int';
                    $this->length = 10;
                }
                $this->primary = true;
            }
            public function extra ()
            {
                $result = ' ';
                if ($this->primary && $this->type==type::integer)
                {
                    $result .= ' auto_increment';
                }
                if ($this->zero)
                {
                    $result .= ' zerofill';
                }
                if ($this->null)
                {
                    $result .= ' null';
                }
                else
                {
                    $result .= ' not null';
                }
                if ($this->default!==null)
                {
                    $result .= ' default ';
                    if (strtolower($this->default)==='null')
                    {
                        $result .= 'null';
                    }
                    else
                    {
                        $result .= "'".$this->default."'";
                    }
                }
                return $result;
            }
        }

        class table
        {
            /**
             *
             * @var string
             */
            public $id;
            /**
             * @var string
             */
            public $database;
            /**
             * @var string
             */
            public $name;
            /**
             * @var string
             */
            public $table;

            /**
             * @var string
             */
            public $engine;

            /**
             * @var string
             */
            public $charset = 'utf8';

            /**
             * @var bool
             */
            public $rename = null;

            /**
             * @var string
             */
            public $link;
            /**
             * @var string
             */
            public $prefix;
            /**
             * @var \ReflectionClass
             */
            public $class;
            public $cache = cache::none;
            public $scope = scope::project;
            /**
             * @var \db\field
             */
            public $primary;
            /**
             * @var query
             */
            private $columns;
            private $tables;
            public $query;
            private $hash;
            public $insert = true;
            public $select = true;
            public $update = true;
            public $delete = true;
            /**
             * @var \db\field[]
             */
            public $fields = array();
            /**
             * @param \db\database $database
             * @param type $class
             */
            public function __construct ($class)
            {
                $this->query = new query();
                $this->id = type ($class);
                $this->table = str_replace('.','_',substr($this->id,1));
                if (strripos($this->id,".")!==false)
                {
                    $this->name = substr($class,strripos($this->id,".")+1);
                }
                else
                {
                    $this->name = $this->id;
                }
                $input = new \stdClass();
                $this->class = new \ReflectionClass (str_replace (".", "\\", $this->id));
                $flags = flag::set($this->class);
                if (is_array($flags))
                {
                    foreach ($flags as &$flag)
                    {
                        if ($flag->name=='ignore')
                        {
                            throw new \Exception ('table ignored');
                        }
                        elseif ($flag->name=='database')
                        {
                            $this->database = $flag->value;
                        }
                        else if ($flag->name=='prefix')
                        {
                            $this->prefix = $flag->value;
                        }
                        else if ($flag->name=='link')
                        {
                            $this->link = $flag->value;
                        }
                        else if ($flag->name=='table')
                        {
                            $this->table = $flag->value;
                        }
                        else if ($flag->name=='engine')
                        {
                            $this->engine = $flag->value;
                        }
                        else if ($flag->name=='charset')
                        {
                            $this->charset = $flag->value;
                        }
                        else if ($flag->name=='rename')
                        {
                            $this->rename = $flag->value;
                        }
                        else if ($flag->name=='order')
                        {
                            $input->order = explode(',', $flag->value);
                        }
                        else if ($flag->name=='limit')
                        {
                            debug ($flag->value);
                        }
                        else if ($flag->name=='cache')
                        {
                            if ($flag->value=='long')
                            {
                                $this->cache = cache::long;
                            }
                            else if ($flag->value=='user')
                            {
                                $this->cache = cache::user;
                            }
                            else if ($flag->value=='load')
                            {
                                $this->cache = cache::load;
                            }
                            else if ($flag->value=='none')
                            {
                                $this->cache = cache::none;
                            }
                        }
                        else if ($flag->name=='scope')
                        {
                            if ($flag->value=='solution')
                            {
                                $this->scope = scope::solution;
                            }
                            else if ($flag->value=='project')
                            {
                                $this->project = scope::project;
                            }
                        }
                        elseif ($flag->name=='deny')
                        {
                            if ($flag->value=='insert')
                            {
                                $this->insert = false;
                            }
                            else if ($flag->value=='update')
                            {
                                $this->update = false;
                            }
                            else if ($flag->value=='select')
                            {
                                $this->select = false;
                            }
                            else if ($flag->value=='delete')
                            {
                                $this->delete = false;
                            }
                        }
                    }
                }
                foreach ($this->class->getProperties() as $value)
                {
                    /* @var $value \ReflectionProperty */
                    try
                    {
                        $field = new field($this, $value);
                        $this->fields[$field->name] = $field;
                        if ($field->primary)
                        {
                            $this->primary = &$this->fields[$field->name];
                        }
                    }
                    catch (\Exception $fail)
                    {

                    }
                    //$type = new type($value);
                    //$type->
                }
                if ($this->primary==null)
                {
                    if (isset($this->fields['id']))
                    {
                        $this->primary = &$this->fields['id'];
                        $this->primary->primary();
                    }
                }
                if (is_array($input->order))
                {
                    if (count($input->order)==1)
                    {
                        if (strpos($input->order[0],':'))
                        {
                            $input->order = explode(':',$input->order[0]);
                            if (isset($this->fields[$input->order[0]]))
                            {
                                $this->query->order->field($input->order[0]);
                                if ($input->order[1]=='asc' || $input->order[1]=='desc')
                                {
                                    $this->query->order->method ($input->order[1]);
                                }
                            }
                            // debug ($this->query->order);
                            // exit;
                        }
                        else if (isset($this->fields[$input->order[0]]))
                        {
                            $this->query->order->field($input->order[0]);
                        }
                    }
                    else
                    {
                        foreach ($input->order as $data)
                        {
                            if (strpos($input->order[0],':'))
                            {
                                $data = explode(':',$data);
                                if (isset($this->fields[$data[0]]))
                                {
                                    $this->query->order->add($data[0], $data[1]);
                                }
                            }
                            else
                            {
                                if (isset($this->fields[$data]))
                                {
                                    $this->query->order->add($data);
                                }
                            }
                        }
                        // debug ($this->query->order);
                        // debug ($this->query->order->result($this));
                        // exit;
                    }
                }
            }
            public function value ($field, $value)
            {
                if ($value===null)
                {
                    debug('d');
                }
                return $this->field($field)."='".id($value)."'";
            }
            public function with ($field, $value)
            {
                return $this->field($field)." like '%|".id($value)."|%'";
            }
            public function field ($name)
            {
                global $database;
                return $this->name($name,$database->locale());
            }
            public function name ($field=null, $locale=false, $short=false)
            {
                if ($field===null)
                {
                    $result = '';
                    if ($this->database!==null)
                    {
                        $result .= "`".$this->database."`.";
                    }
                    $result .= "`".$this->table."`";
                    return $result;
                }
                else
                {
                    if ($locale===false || $locale===true)
                    {
                        $short = $locale;
                    }
                    if (!is_object($field))
                    {
                        $field = $this->fields[$field];
                    }
                    if ($short===true)
                    {
                        if ($field->locale && is_object($locale) && isset($locale->name))
                        {
                            return "`".$this->prefix.$field->column."_".$locale->name."`";
                        }
                        else
                        {
                            return "`".$this->prefix.$field->column."`";
                        }

                    }
                    else if ($field->locale && is_object($locale) && isset($locale->name))
                    {
                        return $this->name().".`".$this->prefix.$field->column."_".$locale->name."`";
                    }
                    else
                    {
                        return $this->name().".`".$this->prefix.$field->column."`";
                    }
                }
            }
            public static function enum ($set,$table=null)
            {
                if (is_array($set))
                {
                    $result = '|';
                    foreach ($set as $item)
                    {
                        if (id($item,$table->primary->name))
                        {
                            $result .= id($item,$table->primary->name).'|';
                        }
                    }
                    if (strlen($result)==1)
                    {
                        return '';
                    }
                    return $result;
                }
                else if ($table)
                {
                    if (strlen($set)>2)
                    {
                        $result = array();
                        $keys = explode ('|',substr($set,1,-1));
                        if ($keys && is_array($keys))
                        {
                            foreach ($keys as $key)
                            {
                                if ($key)
                                {
                                    //debug ($table->name);
                                    $object = $table->load ($key);
                                    $result[$object->{$table->primary->name}] = $object;
                                }
                            }
                        }
                        return $result;
                    }
                    return array ();
                }
                else
                {
                    if (strlen($set)>2)
                    {
                        $result = array();
                        $keys = explode ('|',substr($set,1,-1));
                        if ($keys && is_array($keys))
                        {
                            foreach ($keys as $key)
                            {
                                if ($key)
                                {
                                    $result[$key] = $key;
                                }
                            }
                        }
                        return $result;
                    }
                }
                return $set;
            }
            public function create ($row, $from=0, $create=true)
            {
                $database = $this->database();
                $cell = 0;
                if ($from)
                {
                    $cell = $from;
                }
                $result = @$this->class->newInstance();
                foreach ($this->fields as $field)
                {
                    if ($field->select)
                    {
                        if ($field->locale && $database->locales())
                        {
                            foreach ($database->locales() as $locale)
                            {
                                $result->{$field->name."_".$locale->name} = $row[$cell];
                                $cell++;
                            }
                            $result->{$field->name} = &$result->{$field->name."_".$database->locale()->name};
                            if ($result->{$field->name."_".$database->locale(true)->name}!='')
                            {
                                foreach ($database->locales() as $locale)
                                {
                                    if ($result->{$field->name."_".$locale->name}=='')
                                    {
                                        $result->{$field->name."_".$locale->name} = $result->{$field->name."_".$database->locale(true)->name};
                                    }
                                }
                            }
                        }
                        else if ($field->value && is_object($field->class))
                        {
                            if (!is_object($result->{$field->name}))
                            {
                                $result->{$field->name} = @$field->class->newInstance();
                            }
                            $result->{$field->name}->set ($row[$cell]);
                            $cell++;
                        }
                        else if ($field->enum && $field->foreign)
                        {
                            if ($field->lazy)
                            {
                                if (strlen($row[$cell])>2)
                                {
                                    $keys = explode ('|',substr($row[$cell],1,-1));
                                    if ($keys && is_array($keys))
                                    {
                                        foreach ($keys as $key)
                                        {
                                            if ($key)
                                            {
                                                $result->{$field->name}[$key] = $key;
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    $result->{$field->name} = array ();
                                }
                            }
                            else
                            {
                                //debug ($this->name.'.'.$field->name);
                                $result->{$field->name} = self::enum($row[$cell],$database->table($field->foreign));
                            }
                            $cell++;
                        }
                        else if ($field->foreign)
                        {
                            if (!$from)
                            {
                                $table = $database->table ($field->foreign);
                                if ($table)
                                {
                                    if ($table->link==$this->link)
                                    {
                                        if ($row[$cell+1]===null)
                                        {
                                            $result->{$field->name} = $row[$cell];
                                        }
                                        else
                                        {
                                            //debug ($this->name);
                                            $result->{$field->name} = $table->create($row,$cell+1);
                                            if ($result->{$field->name}->{$table->primary->name}===null && $row[$cell]!==null)//***
                                            {
                                                //creating default object from empty warrning error
                                                //raises when using private fields and __set __get
                                                //until now it does not cause malfunction
                                                @$result->{$field->name}->{$table->primary->name} = $row[$cell];//***
                                            }
                                        }
                                        foreach ($table->fields as $foreign)
                                        {
                                            if ($foreign->locale && $database->locales())
                                            {
                                                $cell += count($database->locales());
                                            }
                                            else
                                            {
                                                $cell++;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $result->{$field->name} = $table->load($row[$cell]);//***
                                        if ($result->{$field->name}===false)
                                        {
                                            $result->{$field->name} = $row[$cell];//***
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $result->{$field->name} = $row[$cell];
                            }
                            $cell++;
                        }
                        else
                        {

                            if ($row[$cell]!==null && $field->type==type::float)
                            {
                                $result->{$field->name} = (float) $row[$cell];
                            }
                            else if ($row[$cell]!==null && $field->type==type::integer)
                            {
                                $result->{$field->name} = (int) $row[$cell];
                            }
                            else if ($row[$cell]!==null && $field->type==type::boolean)
                            {
                                $result->{$field->name} = (bool) $row[$cell];
                            }
                            else if ($row[$cell]!==null && $field->type==type::date)
                            {
                                //\debug ($row[$cell], $field->name.' load');
                                $result->{$field->name} = date (null, $row[$cell]);
                            }
                            else if ($row[$cell]!==null && $field->type==type::time)
                            {
                                $result->{$field->name} = time (null, $row[$cell]);
                            }
                            else if ($row[$cell]!==null && $field->type==type::binary)
                            {
                                $result->{$field->name} = base64_encode (null, $row[$cell]);
                            }
                            else
                            {
                                $result->{$field->name} = $row[$cell];
                            }
                            $cell++;
                        }

                    }
                }
                if ($create && method_exists($result,'create'))
                {
                    $result->create ();
                }
                return $result;
            }
            public function load ($query=null, $single=false)
            {
                $debug = debug_backtrace();
//                debug ($this->id);
//                debug ($debug);
                if ($debug>0)
                {
                    $first = false;
                    foreach ($debug as $step)
                    {
                        if (!$first)
                        {
                            $first = true;
                        }
                        else
                        {
                            if (isset($step['class']) && type($step['class']=='.db.table') && $step['function']=='load')
                            {
                                if ($step['object']->id==$this->id)
                                {
                                    return;
                                }
                            }
                        }

                    }
                }
                $database = $this->database();
                if (!$this->select)
                {
                    return false;
                }
                if (is_object($query) && type($query)=='.db.by')
                {
                    $where = $query->result ($this);
                    $query = new query();
                    $query->where = clone $this->query->where;
                    $query->order = clone $this->query->order;
                    $query->limit = clone $this->query->limit;
                    $query->where ($where);
                }
                if (is_object($query) && type($query)=='.db.pager')
                {
                    $pager = $query;
                    $query = new query();
                    $query->where = clone $this->query->where;
                    $query->order = clone $this->query->order;
                    $query->limit = clone $this->query->limit;
                    $query->pager = $pager;
                }
                if ($query===null)
                {
                    $query = $this->query;
                }
                if (!is_object($query))
                {
                    $row = $database->get ($this, string($query));
                    if (!$row)
                    {
                        $database->context->usage->query ++;
                        $request = "select ".$this->fields()." from ".$this->tables()." where ".$this->name($this->primary)."='".string($query)."'";
                        $result = $database->link($this->link)->query ($request);
                        if (!$result)
                        {
                            return false;
                        }
                        $row = $result->fetch();
                        if ($row)
                        {
                            $database->set ($this, string($query), $row);
                        }
                    }
                    else
                    {
                        $database->context->usage->cache ++;
                    }
                    if ($row)
                    {
                        return $this->create ($row);
                    }
                }
                else
                {
                    if (is_object($query->pager))
                    {
                        $query->pager->total = $database->link($this->link)->value ("select count(*) from ".$this->tables()." ".$query->join($this)." ".$query->where($this));
                    }
                    $rows = $database->get ($this, $query);
                    if (!$rows)
                    {
                        if (type($query)!='.db.query')
                        {
                            @debug ($query);
                        }
                        $database->context->usage->query ++;
                        $rows = array ();
                        $request = "select ".$this->fields()." from ".$this->tables()." ".$query->join($this)." ".$query->where($this)." ".$query->group($this)." ".$query->order($this)." ".$query->limit($this);
                        $result = $database->link($this->link)->query ($request);
                        if ($result)
                        {
                            foreach ($result as $row)
                            {
                                $rows[$row[$this->primary->position]] = $row;
                            }
                            $database->set ($this, $query, $rows);
                        }
                    }
                    else
                    {
                        $database->context->usage->cache ++;
                    }
                    if (is_array($rows) && count($rows))
                    {
                        $result = array();
                        foreach ($rows as $row)
                        {
                            $result[$row[$this->primary->position]] = $this->create ($row);
                        }
                        if ($single)
                        {
                            return reset($result);
                        }
                        return $result;
                    }
                }
                return false;
            }
            public function save (&$object, $action=null)
            {
                //debug ($action);
                if (is_object($object))
                {
                    $database = $this->database();
                    if ($action===null)
                    {
                        if ($object->{$this->primary->name})
                        {
                            $action = query::update;
                        }
                        else
                        {
                            $action = query::insert;
                        }
                    }
                    if (($action==query::update && $this->update) || ($action==query::insert && $this->insert))
                    {
                        //debug ($object);
                        $set = '';
                        foreach ($this->fields as &$field)
                        {
                            if (($action==query::update && $field->update) || ($action==query::insert && $field->insert))
                            {
                                //debug ($action);
                                if (($action==query::update && $field->primary) || ($field->primary && !$object->{$this->primary->name}))
                                {
                                    continue;
                                }
                                if (($action==query::update && $field->event->update->action==action::date) || ($action==query::insert && $field->event->insert->action==action::date))
                                {
                                    $object->{$field->name} = now();
                                    $set .= $this->name($field)."='".time($object->{$field->name})."', ";
                                    continue;
                                }
                                //echo $field->name;
                                if ($field->locale && $database->locales())
                                {
                                    if ($object->{$field->name}!==null && $object->{field(null,$field->name,$database->locale())}===null)
                                    {
                                        $object->{field(null,$field->name,$database->locale())} = $object->{$field->name};
                                    }
                                    foreach ($database->locales() as $locale)
                                    {
                                        $set .= $this->name($field,$locale)."='".string($object->{field(null,$field->name,$locale)})."', ";
                                    }
                                }
                                else if ($field->value)
                                {
                                    $set .= $this->name($field)."='".string($object->{$field->name}->get())."', ";
                                }
                                else if ($field->foreign && $field->enum)
                                {
                                    if (!is_array($object->{$field->name}))
                                    {
                                        $object->{$field->name} = array ();
                                    }
                                    $set .= $this->name($field)."='".self::enum($object->{$field->name},$database->table($field->foreign))."', ";
                                }
                                else if ($field->foreign)
                                {
                                    $set .= $this->name($field)."='".id($object->{$field->name},$database->table($field->foreign)->primary->name)."', ";
                                }
                                else
                                {

                                    if ($object->{$field->name}!==null)
                                    {
                                        if ($field->type==type::string)
                                        {
                                            $set .= $this->name($field)."='".id($object->{$field->name})."', ";
                                        }
                                        else if ($field->type==type::integer)
                                        {
                                            if (!is_object($object->{$field->name}))
                                            {
                                                $object->{$field->name} = intval ($object->{$field->name});
                                                $set .= $this->name($field)."='".$object->{$field->name}."', ";
                                            }
                                            else
                                            {
                                                $set .= $this->name($field)."='".intval(id($object->{$field->name}))."', ";
                                            }
                                        }
                                        else if ($field->type==type::float)
                                        {
                                            $object->{$field->name} = floatval ($object->{$field->name});
                                            $set .= $this->name($field)."='".$object->{$field->name}."', ";
                                        }
                                        else if ($field->type==type::boolean)
                                        {
                                            $object->{$field->name} = (bool)($object->{$field->name});
                                            $set .= $this->name($field)."='".intval($object->{$field->name})."', ";
                                        }
                                        else if ($field->type==type::date)
                                        {
                                            $object->{$field->name} = date($object->{$field->name});
                                            $set .= $this->name($field)."='".$object->{$field->name}."', ";
                                            $object->{$field->name} = date(null,$object->{$field->name});
                                        }
                                        else if ($field->type==type::time)
                                        {
                                            $object->{$field->name} = time($object->{$field->name});
                                            $set .= $this->name($field)."='".$object->{$field->name}."', ";
                                            $object->{$field->name} = time(null,$object->{$field->name});
                                        }
                                        else if ($field->type==type::binary)
                                        {
                                            $set .= $this->name($field)."='".base64_encode($object->{$field->name})."', ";
                                        }
                                    }
                                    else if (strtolower($field->default)==='null')
                                    {
                                        $set .= $this->name($field)."=null, ";
                                    }
                                    else
                                    {
                                        $set .= $this->name($field)."='".string($object->{$field->name})."', ";
                                    }
                                    //$set .= $this->name($field)."='".string($object->{$field->name})."', ";
                                }
                            }
                        }
                        if ($set!='')
                        {
                            $set = substr ($set, 0, -2);
                            if ($action==query::update)
                            {
                                $query = "update ".$this->name()." set ".$set." where ".$this->name($this->primary)."='".string($object->{$this->primary->name})."' limit 1";
                                if ($database->link($this->link)->query ($query))
                                {
                                    @$database->set ($this,$object->{$this->primary->name},false);
                                    if ($clear===null)
                                    {
                                        $clear = new query();
                                    }
                                    @$database->set ($this,$clear,false);
                                    return true;
                                }
                            }
                            else
                            {
                                $query = "insert into ".$this->name()." set ".$set;
                                if ($database->link($this->link)->query ($query))
                                {
                                    if ($object->{$this->primary->name}===null)
                                    {
                                        $object->{$this->primary->name} = $database->link($this->link)->id();
                                    }
                                    $database->set ($this,$object->{$this->primary->name},false);
                                    if (!isset($clear) || $clear===null)
                                    {
                                        $clear = new query();
                                    }
                                    @$database->set ($this,$clear,false);
                                    return true;
                                }
                            }
                        }
                    }
                    return false;
                }
                else if (is_array($object))
                {
                    $result = true;
                    foreach ($object as &$item)
                    {
                        if (!$this->save($item,$action))
                        {
                            $result = false;
                        }
                    }
                    return $result;
                }
            }
            public function reset ($query=null)
            {
                if (is_object($query))
                {
                    $database = $this->database();
                    $database->set ($this, $query, false);
                }
                else
                {
                    $this->columns = null;
                    $this->tables = null;
                    $this->hash = null;
                }
            }
            public function delete ($object)
            {
                if (is_array($object))
                {
                    $result = true;
                    foreach ($object as $item)
                    {
                        if (!$this->delete($item))
                        {
                            $result = false;
                        }
                    }
                    return $result;
                }
                if (is_object($object) && type($object)=='.db.by')
                {
                    $query = new query();
                    $query->where ($object->result($this));
                    $database = $this->database();
                    $request = "delete from ".$this->name()." where ".$query->where->result($this);
                    $result = $database->link($this->link)->query ($request);
                    if ($result)
                    {

                    }
                }
                else
                {
                    $database = $this->database();
                    $request = "delete from ".$this->name()." where ".$this->name($this->primary)."='".id($object,$this->primary->name)."' limit 1";
                    $result = $database->link($this->link)->query ($request);
                    if ($result)
                    {
                        $database->set ($this, id($object,$this->primary->name), false);
                    }
                }
            }
            public function exists ($query)
            {

            }
            public function of ($object)
            {

            }
            public function fields ()
            {
                if ($this->columns===null)
                {
                    $database = $this->database();
                    $cell = 0;
                    foreach ($this->fields as $field)
                    {
                        if ($field->select)
                        {
                            $field->position = $cell;
                            if ($field->locale && $database->locales())
                            {
                                foreach ($database->locales() as $locale)
                                {
                                    $this->columns .= $this->name($field,$locale).', ';
                                    $cell++;
                                }
                            }
                            else
                            {
                                $this->columns .= $this->name($field).', ';
                                $cell++;
                            }
                            if ($field->enum && $field->foreign)
                            {

                            }
                            else if ($field->foreign)
                            {
                                $table = $database->table ($field->foreign);
                                if ($table->link==$this->link)
                                {
                                    foreach ($table->fields as $foreign)
                                    {
                                        if ($foreign->locale && $database->locales())
                                        {
                                            foreach ($database->locales() as $locale)
                                            {
                                                $this->columns .= $table->name($foreign,$locale).', ';
                                                $cell++;
                                            }
                                        }
                                        else
                                        {
                                            $this->columns .= $table->name($foreign).', ';
                                            $cell++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($this->columns!==null)
                    {
                        $this->columns = substr($this->columns,0,-2);
                    }
                }
                return $this->columns;
            }
            public function table ()
            {
                return $this->name();
            }
            public function tables ()
            {
                if ($this->tables===null)
                {
                    $database = $this->database();
                    $this->tables .= $this->name();
                    foreach ($this->fields as $field)
                    {
                        if ($field->foreign && !$field->enum)
                        {
                            $table = $database->table ($field->foreign);
                            if ($table)
                            {
                                $this->tables .= " left join ".$table->name()." on ".$this->name($field)."=".$table->name($table->primary);
                            }
                        }
                    }
                }
                return $this->tables;
            }
            public function hash ()
            {
                if ($this->hash===null)
                {
                    $this->hash = md5($this->name()."|".$this->fields()."|".$this->tables());
                }
                return $this->hash;
            }
            public function next ($field=null)
            {
                if ($field===null)
                {
                    $field = $this->primary;
                }
                return intval($this->database()->link($this->link)->value("select max(".$this->name($field).") from ".$this->name()))+1;
            }
            public function database ()
            {
                return database::$object;
            }
            public function foreign ($field, $class)
            {
                $this->fields[$field]->foreign ($class);
                $this->columns = null;
                $this->tables = null;
            }
        }

        class database
        {
            public static $object;
            public $context;
             /**
             * @param string $default default database name
             * @param \db\link $link default connection to database
             */
            public function __construct ($default=null, $link=null, $username=null, $password=null)
            {
                if (!is_object($link) && $link!=null && $username!==null)
                {
                    $database = $link;
                    $hostname = $default;
                    $default = $database;
                    $link = new link ('default', $hostname, $username, $password);
                }
                $this->context = new \stdClass();
                $this->context->links = array ();
                $this->context->tables = array ();
                $this->context->locales = null;
                $this->context->locale = null;
                $this->context->caches = array ();
                $this->context->default = $default;
                $this->context->usage = new \stdClass();
                $this->context->usage->query = 0;
                $this->context->usage->cache = 0;
                $this->context->readonly = false;
                if ($link)
                {
                    $this->context->links[$link->name] = $link;
                }
                $this->context->caches[cache::load] = new load ();
                $this->context->caches[cache::long] = new long ();
                $this->context->caches[cache::user] = new user ();
                self::$object = $this;
            }
            public function __destruct ()
            {
                if ($this->link()->debug)
                {
                    debug ($this->context->usage);
                }
            }
            /**
             * @param \db\table $table
             */
            public function add ($class)
            {
                try
                {
                    $table = new table ($class);
                }
                catch (\Exception $error)
                {
                    return;
                }
                if ($table->database===null)
                {
                    $table->database = $this->context->default;
                }
                if ($table->id[0]=='.')
                {
                    $source = substr($table->id, 1);
                }
                else
                {
                    $source = $table->id;
                }
                if ($table->link===null)
                {
                    $table->link = $this->link()->name;
                }
                if ($table->engine===null && $this->link($table->link))
                {
                    $table->engine = $this->link($table->link)->engine;
                }
                $result = explode ('.',$source);
                if ($result[0]!='context')
                {
                    $space = &$this;
                    foreach ($result as $item)
                    {
                        if ($item!='' && !isset($space->{$item}))
                        {
                            $space->{$item} = new \stdClass();
                        }
                        $space = &$space->{$item};
                    }
                    $space = $table;
                    $this->context->tables[$table->id] = $table;
                }
            }
            public function save (&$object, $action=null)
            {
                if (is_array($object))
                {
                    foreach ($object as $item)
                    {
                        $this->save ($item, $action);
                    }
                }
                else
                {
                    $table = $this->table($object);
                    if (!$table)
                    {
                        debug ($object);
                        exit;
                    }
                    $table->save ($object, $action);
                }
                return $object;
            }
            public function scan ($prefix)
            {
                $prefix = type ($prefix);
                $result = get_declared_classes ();
                if ($prefix!=null)
                {
                    foreach ($result as $class)
                    {
                        $class = type ($class);
                        if (strpos($class,$prefix)===0)
                        {
                            $this->add ($class);
                            //debug ($class);
                        }
                    }
                }
            }
            public function table ($id)
            {
                return $this->context->tables[type($id)];
            }
            /**
             * @param link $link
             * @return link
             */
            public function link ($link=null)
            {
                if ($link===null)
                {
                    return reset($this->context->links);
                }
                if (is_object($link))
                {
                    $this->context->links[$link->name] = $link;
                }
                else
                {
                    return $this->context->links[$link];
                }
            }
            public function readonly ($value=true)
            {
                $this->context->readonly = $value;
            }
            public function update ($log=null)
            {
                if ($this->context->readonly)
                {
                    return;
                }
                if ($log)
                {
                    $file = $log;
                    $log = '';
                }
                else
                {
                    $file = false;
                }
                $databases = array ();
                foreach ($this->context->tables as &$table)
                {
                    //debug ("in table ".$table->name);
                    $link = $this->link($table->link);
                    $result = $link->select ("describe ".$table->name());
                    //check if table exists
                    if (!$result && $link->error('42S02'))
                    {
                        //table does not exist
                        //check if database exists
                        if (!isset($databases[$table->link][$table->database]))
                        {
                            //create database as it does not exist
                            $query = "create database if not exists `".$table->database."` default character set utf8 collate utf8_general_ci";
                            $databases[$table->link][$table->database] = true;
                            if ($file)
                            {
                                $log .= $query.";\n";
                            }
                            else
                            {
                                $link->query($query);
                            }
                        }
                        //check if table does not exist
                        //because programmer renamed class
                        //and hinted to rename from old name
                        if ($table->rename)
                        {
                            //okey rename table
                            $from = '';
                            if ($table->database!==null)
                            {
                                $from .= "`".$table->database."`.";
                            }
                            $from .= "`".$table->rename."`";
                            $query = "rename table ".$from." to ".$table->name();
                            if ($file)
                            {
                                $log .= $query.";\n";
                            }
                            else
                            {
                                $link->query($query);
                            }
                        }
                        else
                        {
                            //well we have to create talble at last
                            //as it does not exist and it does not require rename
                            //debug ("in create table ".$table->name);
                            $query = 'create table '.$table->name()." (";
                            foreach ($table->fields as &$field)
                            {
                                if ($field->locale && $this->locales())
                                {
                                    foreach ($this->locales() as $locale)
                                    {
                                        $query .= " ".$table->name($field,$locale,true)." ".$field->type()." ".$field->extra().",";
                                    }
                                }
                                else
                                {
                                    $query .= " ".$table->name($field,true)." ".$field->type()." ".$field->extra().",";
                                }
                            }
                            if ($table->primary)
                            {
                                $query .= "primary key (".$table->name($table->primary,true).")";
                            }
                            $query .= ") engine=".$table->engine." default charset=".$table->charset;
                            if ($file)
                            {
                                $log .= $query.";\n";
                            }
                            else
                            {
                                $link->query($query);
                            }
                        }
                        $result = false;
                    }
                    //damn table update
                    //multilangual fields complicate
                    //everithing
                    //but it alows to:
                    //1. rename field and all its child multilang fields will be renamed
                    //2. localize field add language fields and rename original field for default language field
                    //3. add new language causes to add fields right aftet fields last localized child
                    //4. add field after field
                    //5. natsionaluri modzraoba
                    //6. compare changes and alter changes
                    //7. add new fields
                    if ($result)
                    {
                        if ($table->database!==null)
                        {
                            $query = "show table status from `".$table->database."` where name = '".$table->table."'";
                        }
                        else
                        {
                            $query = "show table status where name = '".$table->table."'";
                        }
                        $info = $link->fetch($query);
                        if ($info)
                        {
                            //debug ($info);
                            if (strtolower($table->engine)!=strtolower($info['Engine']))
                            {
                                $query = "alter table ".$table->name()." engine=".$table->engine;
                                if ($file)
                                {
                                    $log .= $query.";\n";
                                }
                                else
                                {
                                    $link->query($query);
                                }
                            }
                        }

                        //debug ("in modify table ".$table->name);
                        $update = array ();
                        $columns = array ();
                        $insert = array ();
                        $localize = array ();
                        foreach ($result as $row)
                        {
                            $column = array ();
                            $column['name'] = $row['Field'];
                            $column['length'] = null;
                            $column['data'] = null;
                            $column['unsigned'] = false;
                            if (strpos($row['Type'],'('))
                            {
                                $column['data'] = substr($row['Type'], 0, strpos($row['Type'],'('));
                                $column['length'] = substr($row['Type'], strpos($row['Type'],'(')+1, strpos($row['Type'],')')-strpos($row['Type'],'(')-1);
                            }
                            else if (strpos($row['Type'],' '))
                            {
                                $column['data'] = substr($row['Type'], 0, strpos($row['Type'],' ')+1);
                            }
                            else
                            {
                                $column['data'] = $row['Type'];
                            }
                            if (strpos($row['Type'],'zerofill'))
                            {
                                $column['zero'] = true;
                            }
                            else
                            {
                                $column['zero'] = false;
                            }
                            if (strpos($row['Type'],'unsigned'))
                            {
                                $column['unsigned'] = true;
                            }
                            else
                            {
                                $column['unsigned'] = false;
                            }

                            $column['default'] = $row['Default'];
                            if ($row['Null']=='YES')
                            {
                                $column['null'] = true;
                            }
                            else
                            {
                                $column['null'] = false;
                            }
                            $columns[$column['name']] = $column;
                            //debug ($row);
                        }
                        //debug ($columns);
                        foreach ($table->fields as &$field)
                        {
                            $locales = $this->locales();
                            if (!$locales || !$field->locale)
                            {
                                $locales = array (null);
                            }
                            foreach ($locales as $locale)
                            {
                                //debug ($columns[field($table->prefix,$field->column,$locale)]['length']." ".$field->length);
                                if (isset($columns[field($table->prefix,$field->rename,$locale)]))
                                {
                                    $update[$field->name] = &$field;
                                }
                                else if (isset($columns[field($table->prefix,$field->column,$locale)]))
                                {
                                    $field->rename = null;
                                    if ($columns[field($table->prefix,$field->column,$locale)]['data']!=$field->data)
                                    {
                                        $update[$field->name] = &$field;
                                        $this->debug ("type mismatch",$field);
                                    }
                                    else if ($field->length!==null && $columns[field($table->prefix,$field->column,$locale)]['length']!=$field->length)
                                    {
                                        $update[$field->name] = &$field;
                                        $this->debug ("length mismatch",$field,$table);
                                    }
                                    else if ($columns[field($table->prefix,$field->column,$locale)]['default']!==null && $columns[field($table->prefix,$field->column,$locale)]['default']!=$field->default)
                                    {
                                        //debug ($columns[field($table->prefix,$field->column,$locale)]);
                                        $update[$field->name] = &$field;
                                        $this->debug ("default mismatch",$field);
                                    }
                                    else if($columns[field($table->prefix,$field->column,$locale)]['null']!=$field->null)
                                    {
                                        $update[$field->name] = &$field;
                                        $this->debug ("null mismatch",$field);
                                    }
                                    else if ($columns[field($table->prefix,$field->column,$locale)]['zero']!=$field->zero)
                                    {
                                        $update[$field->name] = &$field;
                                        $this->debug ("zero mismatch",$field);
                                    }
                                    else if ($columns[field($table->prefix,$field->column,$locale)]['unsigned']!=$field->unsigned)
                                    {
                                        $update[$field->name] = &$field;
                                        $this->debug ("unsigned mismatch",$field);
                                    }
                                }
                                else if (!isset($columns[field($table->prefix,$field->column,$locale)]))
                                {
                                    if (isset($columns[field($table->prefix,$field->column)]))
                                    {
                                        $field->ignore = $this->locale(true);
                                        $localize[$field->name]  = &$field;
                                    }
                                    //echo field($table->prefix,$field->column,$locale)."<br>";
                                    $insert[$field->name] = &$field;
                                }
                            }
                        }
                        ////
                        //debug ($localize);
                        if ($localize)
                        {
                            foreach ($localize as &$field)
                            {
                                $query = "alter table ".$table->name()." change ".$table->name($field,true)." ".$table->name($field,$field->ignore,true)." ".$field->type()." ".$field->extra();
                                //debug ($query);
                                if ($file)
                                {
                                    $log .= $query.";\n";
                                }
                                else
                                {
                                    $link->query($query);
                                }
                                $columns[field($table->prefix,$field->column,$field->ignore)] = &$columns[field($table->prefix,$field->column)];
                            }
                        }
                        //debug ($update);
                        if ($update)
                        {
                            foreach ($update as &$field)
                            {
                                $locales = $this->locales();
                                if (!$locales || !$field->locale)
                                {
                                    $locales = array (null);
                                }
                                foreach ($locales as $locale)
                                {
                                    $query = "alter table ".$table->name()." change ".($field->rename ? ("`".field($table->prefix,$field->rename,$locale)."`") : $table->name($field,$locale,true))." ".$table->name($field,$locale,true)." ".$field->type()." ".$field->extra();
                                    if ($file)
                                    {
                                        $log .= $query.";\n";
                                    }
                                    else
                                    {
                                        $link->query($query);
                                    }
                                }
                            }
                        }
                        //debug ($insert);
                        if ($insert)
                        {
                            $last = null;
                            if ($this->locales())
                            {
                                $last = @end ($this->locales());
                            }
                            foreach ($insert as &$field)
                            {
                                $locales = $this->locales();
                                if (!$locales || !$field->locale)
                                {
                                    $locales = array (null);
                                }
                                $after = null;
                                $previous = null;
                                if ($field->locale && $this->locales())
                                {
                                    foreach ($this->locales() as $item)
                                    {
                                        if (isset($columns[field($table->prefix,$field->column,$item)]))
                                        {
                                            $previous = $table->name ($field, $item, true);
                                        }
                                    }
                                }
                                //i cant really tell you what is going down there
                                //but its really working : D
                                //just as an advice dont code complex parts of frameworks midnights
                                //if you want to remember how it is working
                                foreach ($locales as $locale)
                                {
                                    if ((!isset($field->ignore) && !$locale) || ($field->ignore->name!=$locale->name && !isset($columns[field($table->prefix,$field->column,$locale)])))
                                    {
                                        $query = "alter table ".$table->name()." add ".$table->name($field,$locale,true)." ".$field->type()." ".$field->extra();
                                        if ($field->first)
                                        {
                                            $query .= " first";
                                        }
                                        else if ($field->after && !$previous)
                                        {
                                            if (!$after && isset($table->fields[$field->after]))
                                            {
                                                $after = $table->name($field->after,$last,true);
                                            }
                                            $query .= " after ".$after;
                                            $after = $table->name ($field, $locale, true);
                                        }
                                        else if ($previous)
                                        {
                                            if (!$after)
                                            {
                                                $after = $previous;
                                            }
                                            if ($after)
                                            {
                                                $query .= " after ".$after;
                                                $after = $table->name ($field, $locale, true);
                                            }
                                        }
                                        //debug ($query);
                                        if ($file)
                                        {
                                            $log .= $query.";\n";
                                        }
                                        else
                                        {
                                            $link->query($query);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if ($file)
                {
                    debug ($log);
                    file_put_contents($file, $log);
                }
            }
            function debug ($value=true)
            {
                if (is_bool($value))
                {
                    if ($value==true)
                    {
                        $this->link()->debug = true;
                    }
                    else
                    {
                        $this->link()->debug = false;
                    }
                }
                // else if ($nam)
                // return;
                // echo "<span style='font-family:\"dejavu sans mono\";font-size:11pt;font-weight:bold;'>"
                // .$table->name." ".$name." on field ".$field->name."(".$field->column.")</span>";
                // debug ($field);
            }
            public function locales ($locales=null)
            {
                if ($locales!==null)
                {
                    $this->context->locales = $locales;
                    foreach ($this->context->tables as &$table)
                    {
                        $table->reset();
                    }
                }
                else
                {
                    if (is_array($this->context->locales) && count($this->context->locales))
                    {
                        return $this->context->locales;
                    }
                    return false;
                }
            }
            public function locale ($locale=null)
            {
                if ($locale===true)
                {
                    return @reset ($this->locales());
                }
                if (is_object($locale))
                {
                    $this->context->locale = $locale;
                }
                else
                {
                    if ($this->context->locale===null && $this->locales())
                    {
                        return @reset ($this->locales());
                    }
                    return $this->context->locale;
                }
            }
            public function set (table &$table, $query, $value)
            {
                $scope = null;
                $cache = null;
                if (is_object($query))
                {
                    if ($query->cache==null)
                    {
                        $cache = $table->cache;
                    }
                    else
                    {
                        $cache = $query->cache;
                    }
                    if ($query->scope==null)
                    {
                        $scope = $table->scope;
                    }
                    else
                    {
                        $scope = $table->scope;
                    }
                }
                else
                {
                    $cache = $table->cache;
                    $scope = $table->scope;
                }
                if ($cache==cache::load || $cache==cache::long || $cache==cache::user)
                {
                    $this->context->caches[$cache]->set ($scope, $table, $query, $value);
                }
            }
            public function get (table &$table, $query)
            {
                $scope = null;
                $cache = null;
                if (is_object($query))
                {
                    if ($query->cache==null)
                    {
                        $cache = $table->cache;
                    }
                    else
                    {
                        $cache = $query->cache;
                    }
                    if ($query->scope==null)
                    {
                        $scope = $table->scope;
                    }
                    else
                    {
                        $scope = $table->scope;
                    }
                }
                else
                {
                    $cache = $table->cache;
                    $scope = $table->scope;
                }
                if ($cache==cache::load || $cache==cache::long || $cache==cache::user)
                {
                    return $this->context->caches[$cache]->get ($scope, $table, $query);
                }
                return false;
            }
        }

        abstract class value
        {
            public function set ($value)
            {

            }
            public function get ()
            {
            }
        }

        class type
        {
            const integer = 1;
            const boolean = 2;
            const float = 3;
            const string = 4;
            const binary = 5;
            const date = 6;
            const time = 7;
        }

        class action
        {
            const none = 0;
            const date = 1;
            const user = 2;
            /**
             * @var int
             */
            public $action = 0;
        }

        class event
        {
            /**
             * @var action
             */
            public $insert;
            /**
             * @var action
             */
            public $select;
            /**
             * @var action
             */
            public $update;
            /**
             * @var action
             */
            public $delete;
            public function __construct ()
            {
                $this->insert = new action();
                $this->select = new action();
                $this->update = new action();
                $this->delete = new action();
            }
        }

        class config
        {

        }

        class statement implements \ArrayAccess,\IteratorAggregate
        {
            public $items = array ();
            public $value;
            public function __construct ($value,$items=array())
            {
                $this->value = $value;
                $this->items = $items;
            }
            public function define ($name, $value)
            {
                $this->items[$name] = $value;
            }
            public function render ()
            {
                if (!$this->items)
                {
                    return $this->value;
                }
                if ($this->value=='')
                {
                    return '';
                }
                $result = $this->value;
                foreach ($this->items as $name=>$value)
                {
                    if (is_object($value) && type($value)=='.db.statement')
                    {
                        $result = str_replace ('{'.$name.'}', $value->render(), $result);
                    }
                    else if (is_object($value) && method_exists($value, '__toString'))
                    {
                        $value = strval ($value);
                        $result = str_replace ('{'.$name.'}', id($value), $result);
                    }
                    else
                    {
                        $result = str_replace ('{'.$name.'}', id($value), $result);
                    }
                }
                return preg_replace ("/{[A-Za-z0-9_\]\[]*}/", "", $result);
            }
            public function __toString ()
            {
                return $this->render();
            }
            public function offsetSet ($key, $value)
            {
                if (is_null($key))
                {
                    $this->items[] = $value;
                }
                else
                {
                    $this->items[$key] = $value;
                }
            }
            public function offsetExists ($key)
            {
                return isset($this->items[$key]);
            }
            public function offsetUnset ($key)
            {
                unset($this->items[$key]);
            }
            public function offsetGet ($key)
            {
                return isset($this->items[$key])?$this->items[$key]:null;
            }
            public function getIterator()
            {
                return new \ArrayIterator ($this->items);
            }
        }

        class query
        {
            const select = 1;
            const insert = 2;
            const update = 3;
            const delete = 4;
            public $cache;
            public $scope;
            /**
             * @var where
             */
            public $where;
            /**
             * @var order
             */
            public $order;
            /**
             * @var limit
             */
            public $limit;
            /**
             * @var pager
             */
            public $pager;
            /**
             * @var group
             */
            public $group;
            /**
             * @var join
             */
            public $join;
            public $debug = false;
            public function __construct ()
            {
                $this->where = new where ();
                $this->order = new order ();
                $this->limit = new limit ();
                $this->join = new join ();
                $this->group = new group ();
            }
            public function where ($table)
            {
                if (is_object($table))
                {
                    if (is_object($this->where))
                    {
                        return $this->where->result ($table);
                    }
                    return $this->where;
                }
                if (is_object($this->where))
                {
                    $this->where->string ($table);
                }
                else
                {
                    $this->where = $table;
                }
            }
            public function join ($table)
            {
                if (is_object($table))
                {
                    if (is_object($this->join))
                    {
                        return $this->join->result ($table);
                    }
                    return $this->join;
                }
                if (is_object($this->join))
                {
                    $this->join->string ($table);
                }
                else
                {
                    $this->join = $table;
                }
            }
            public function group ($table)
            {
                if (is_object($table))
                {
                    if (is_object($this->group))
                    {
                        return $this->group->result ($table);
                    }
                    return $this->group;
                }
                if (is_object($this->group))
                {
                    $this->group->string ($table);
                }
                else
                {
                    $this->group = $table;
                }
            }
            public function order ($table, $method=null)
            {
                if (is_object($table))
                {
                    if (is_object($this->order))
                    {
                        return $this->order->result ($table);
                    }
                    return $this->order;
                }
                $this->order->method ($method);
                $this->order->field ($table);
            }
            public function limit ($table, $count=null)
            {
                if (is_object($table))
                {
                    if (is_object($this->pager))
                    {
                        $this->limit = &$this->pager;
                    }
                    if (is_object($this->limit))
                    {
                        return $this->limit->result ($table);
                    }
                    return $this->limit;
                }
                if ($count===null)
                {
                    $this->limit->count ($table);
                }
                else
                {
                    $this->limit->from ($table);
                    $this->limit->count ($count);
                }
            }
            public function pager ($page, $count=50)
            {
                if (is_object($page))
                {
                    $this->pager = $page;
                }
                else
                {
                    $this->pager = new pager ($page, $count);
                }
                $this->limit = &$this->pager;
            }
            public function hash ($table)
            {
                $result = array ();
                if (is_object($this->where))
                {
                    $result[] = $this->where->result ($table);
                }
                else if ($this->where)
                {
                    $result[] = $this->where;
                }
                if (is_object($this->limit))
                {
                    $result[] = $this->limit->result ($table);
                }
                else if ($this->limit)
                {
                    $result[] = $this->limit->result;
                }
                $source = '';
                foreach ($result as $item)
                {
                    if ($item!==null)
                    {
                        $source .= $item;
                    }
                }
                if ($source!=='')
                {
                    return md5($source);
                }
                return null;
            }
        }

        class where
        {
            /**
             * @var string
             */
            public $string;
            public function result ($table)
            {
                if ($this->string!='')
                {
                    return " where ".$this->string." ";
                }
                return "";
            }
            public function string ($string)
            {
                $this->string = $string;
            }
            public function append ($string)
            {
                $this->string .= " ".$string;
            }
        }

        class group
        {
            /**
             * @var string
             */
            public $string;
            public function result ($table)
            {
                if ($this->string!='')
                {
                    return " group by ".$this->string." ";
                }
                return "";
            }
            public function string ($string)
            {
                $this->string = $string;
            }
        }

        class join
        {
            /**
             * @var string
             */
            public $string;
            public function result ($table)
            {
                if ($this->string!='')
                {
                    return " ".$this->string." ";
                }
                return "";
            }
            public function string ($string)
            {
                $this->string = $string;
            }
        }

        class order
        {
            private $table;
            private $items = array();
            private $field;
            private $method;
            public function __construct ($field=null, $method=null)
            {
                $this->field = $field;
                if (!is_object($method))
                {
                    $method = new method ($method);
                }
                $this->method = $method;
            }
            public function add ($field, $method=null)
            {
                if (is_object($field) && $method===null)
                {
                    $this->items[] = $field;
                }
                else
                {
                    $this->items[] = new order ($field, $method);
                }
            }
            public function field ($field)
            {
                $this->field = $field;
            }
            public function method ($method)
            {
                $this->method->mode ($method);
            }
            public function table (table &$table)
            {
                $this->table = &$table;
            }
            public function result (table &$table=null)
            {
                if ($table===null && $this->table)
                {
                    $table = &$this->table;
                }
                if ($table===null)
                {
                    return;
                }
                if (is_array($this->items) && $this->items)
                {
                    $result = " order by ";
                    foreach ($this->items as $item)
                    {
                        $result .= substr($item->result($table),10).",";
                    }
                    return " ".substr($result,0,-2)." ";
                }
                if ($this->field)
                {
                    $database = $table->database();
                    return " order by ".$table->name($this->field,$database->locale())." ".$this->method->result($table)." ";
                }
            }
            public function filled ()
            {
                if ($this->field==null && !$this->items)
                {
                    return false;
                }
                return true;
            }
        }

        class method
        {
            const asc = "asc";
            const desc = "desc";
            public $mode = self::asc;
            public function __construct ($mode=self::asc)
            {
                $mode = strtolower ($mode);
                if ($mode==self::asc || $mode==self::desc)
                {
                    $this->mode = $mode;
                }
            }
            public function swap ()
            {
                if ($this->mode==self::asc)
                {
                    $this->mode = self::desc;
                }
                else
                {
                    $this->mode = self::asc;
                }
                return $this->mode;
            }
            public function result (table &$table)
            {
                return $this->mode;
            }
            public function asc ()
            {
                $this->mode = self::asc;
            }
            public function desc ()
            {
                $this->mode = self::desc;
            }
            public function mode ($mode)
            {
                $mode = strtolower ($mode);
                if ($mode==self::asc || $mode==self::desc)
                {
                    $this->mode = $mode;
                }
            }
        }

        class limit
        {
            /**
             * @var int
             */
            public $from;
            /**
             * @var int
             */
            public $count;
            public function __construct ($from=null, $count=null)
            {
                if ($count==null)
                {
                    $this->count = intval(from);
                }
                else if ($from!=null)
                {
                    $this->from = intval ($from);
                    $this->count = intval ($count);
                }
            }
            public function result (table &$table)
            {
                if ($this->count==null)
                {
                    return "";
                }
                if ($this->from==null)
                {
                    return " limit ".intval($this->count)." ";
                }
                return " limit ".intval($this->from).",".intval($this->count)." ";
            }
            public function from ($from)
            {
                $this->from = intval ($from);
            }
            public function count ($count)
            {
                $this->count = intval ($count);
            }
        }

        class pager
        {
            /**
            * current page
            */
            private $page;
            /**
            * total pages count
            */
            private $pages;
            /**
            * total items count
            */
            private $total;
            /**
            * items per page
            */
            private $count;
            /**
            * from item position on current page
            */
            private $from;
            public function __construct ($page=null, $count=50)
            {
                $this->count = $count;
                $this->page = 1;
                $this->build ();
                $this->page ($page);
            }
            public function __set ($name, $value)
            {
                if (method_exists($this,$name))
                {
                    $this->{$name}($value);
                }
            }
            public function __get ($name)
            {
                if (method_exists($this,$name))
                {
                    return $this->{$name}();
                }
            }
            private function valid ()
            {
                if ($this->total!==null && $this->count!==null)
                {
                    return true;
                }
                return false;
            }
            public function build () //build pages
            {
                if (!$this->valid())
                {
                    return;
                }
                //set current page
                if ($this->count)
                {
                    $this->pages = intval ($this->total/$this->count);
                    if ($this->total % $this->count)
                    {
                        $this->pages++;
                    }
                    if (!$this->pages)
                    {
                        $this->pages = 1;
                    }
                }
                else
                {
                    $this->pages = 1;
                }
                //fix page
                if ($this->page>$this->pages)
                {
                    $this->page = $this->pages;
                }
                elseif ($this->page<=0)
                {
                    $this->page = 1;
                }
                //set from item
                if ($this->count)
                {
                    $this->from = ($this->page - 1) * $this->count;
                }
                else
                {
                    $this->from = 0;
                }
            }
            public function page ($value=null) //set current page
            {
                if ($value!==null)
                {
                    $this->page = intval ($value);
                    $this->build ();
                }
                else
                {
                    return $this->page;
                }
            }
            public function next ()
            {
                if ($this->page+1>$this->pages)
                {
                    return false;
                }
                $this->page ($this->page+1);
                return true;
            }
            public function pages ()
            {
                return $this->pages;
            }
            public function total ($value=null)
            {
                if ($value!==null)
                {
                    $this->total = $value;
                    $this->build ();
                }
                else
                {
                    return $this->total;
                }
            }
            public function count ($value=null)
            {
                if ($value!==null)
                {
                    $this->count = $value;
                    $this->build ();
                }
                else
                {
                    return $this->count;
                }
            }
            public function from ()
            {
                return $this->from;
            }
            public function result ($table=null)
            {
                if (!$this->count)
                {
                    return "";
                }
                if (!$this->from)
                {
                    return " limit ".intval($this->count)." ";
                }
                return " limit ".intval($this->from()).",".intval($this->count())." ";
            }
            public function __toString ()
            {
                return $this->result ();
            }
        }

        abstract class cache
        {
            const none = 1; //no store
            const load = 2; //store in array
            const user = 3; //store in session [database|]
            const long = 4; //store in apc cache
            public $store = array ();
            public function __construct ()
            {

            }
            function set ($scope, &$table, $query, $value)
            {
                $path = 'database|'.scope($scope).$table->hash();
                if (is_object($query))
                {
                    if (is_array($value))
                    {
                        $hash = $query->hash($table);
                        foreach ($value as $item)
                        {
                            $this->store ($path.'|entry|'.$item[$table->primary->position], $item);
                            $about = $this->fetch ($path.'|about|'.$item[$table->primary->position]);
                            if (!is_array($about))
                            {
                                $about = array ();
                            }
                            $about[$path.'|query|'.$hash] = true;
                            $this->store ($path.'|about|'.$item[$table->primary->position], $about);
                        }
                        $this->store ($path.'|query|'.$hash, $value);
                        $about = $this->fetch ($path.'|about|');
                        if (!is_array($about))
                        {
                            $about = array ();
                        }
                        $about[$path.'|query|'.$hash] = true;
                        $this->store($path.'|about|', $about);
                        //debug ($about);
                    }
                }
                else
                {
                    if (is_bool($value))
                    {
                        $about = $this->fetch ($path.'|about|'.$query);
                        if (is_array($about))
                        {
                            foreach ($about as $key => $temp)
                            {
                                $this->store ($key, false);
                                //debug ('deleting '.$key);
                                unset ($about[$key]);
                            }
                            $this->store($path.'|about|'.$query, $about);
                        }
                        $about = $this->fetch ($path.'|about|');
                        if (is_array($about))
                        {
                            foreach ($about as $key => $temp)
                            {
                                $this->store ($key, false);
                                //debug ('deleted '.$key);
                                unset ($about[$key]);
                            }
                            $this->store($path.'|about|', $about);
                        }
                    }
                    $this->store ($path.'|entry|'.$query, $value);
                }
            }
            function get ($scope, &$table, $query)
            {
                $path = 'database|'.scope($scope).$table->hash();
                if (is_object($query))
                {
                    return $this->fetch ($path.'|query|'.$query->hash($table));
                }
                return $this->fetch ($path.'|entry|'.$query);
            }
            function count ()
            {
                return $this->count;
            }
            function store ($name, $value)
            {
                $this->store [$name] = $value;
            }
            function fetch ($name)
            {
                return $this->store[$name];
            }
            function clear ()
            {
                $this->store = array ();
            }
        }

        class scope
        {
            const project = 1; //[database|project|[project_name]|]
            const solution = 2; //[database|solution|[solution_name]|]
        }

        class load extends cache
        {

        }

        class user extends cache
        {
            public $store = array ();
            public function __construct ()
            {
                if (isset($_SESSION))
                {
                    if (!isset($_SESSION['database']))
                    {
                        $_SESSION['database'] = array ();
                    }
                    $this->store = &$_SESSION['database'];
                }
            }

        }

        class long extends cache
        {
            function store ($name, $value)
            {
                if (function_exists('\apcu_store'))
                {
                    if (is_bool($value))
                    {
                        \apcu_delete ($name);
                    }
                    else
                    {
                        \apcu_store ($name, $value);
                    }
                }
                else if (function_exists('\apc_store'))
                {
                    if (is_bool($value))
                    {
                        \apc_delete ($name);
                    }
                    else
                    {
                        \apc_store ($name, $value);
                    }
                }
                else
                {
                    parent::store ($name,$value);
                }
            }
            function fetch ($name)
            {
                if (function_exists('\apcu_fetch'))
                {
                    return \apcu_fetch ($name);
                }
                else if (function_exists('\apc_fetch'))
                {
                    return \apc_fetch ($name);
                }
                else
                {
                    return parent::fetch($name);
                }
            }
            function clear ()
            {
                if (function_exists('\apcu_clear_cache'))
                {
                    \apcu_clear_cache  ();
                }
                else if (function_exists('\apc_clear_cache'))
                {
                    \apc_clear_cache ('user');
                }
                else
                {
                    parent::clear();
                }
            }
        }

        class locale
        {
            public $id;
            public $name;
            public $order;
            public function __construct ($name)
            {
                $this->name = $name;
            }
        }

        //$pages->load (by('name','home')->and('name',more,'dad'));

        class by
        {
            private $items = array();
            public function by ($field, $value)
            {
                $this->items['by:'.$field] = $value;
                return $this;
            }
            public function in ($field, $value)
            {
                $this->items['in:'.$field] = $value;
                return $this;
            }
            public function result (&$table)
            {
                global $database;
                if (is_object($table) && $this->items)
                {
                    $result = '';
                    foreach ($this->items as $field=>$value)
                    {
                        $field = explode(':', $field);
                        $primary = null;
                        if (is_object($value))
                        {
                            $parent = $database->table(\db\type($value));
                            if ($parent)
                            {
                                $primary = $parent->primary->name;
                            }
                        }
                        if ($field[0]=='by')
                        {
                            $result .= $table->name($field[1],$database->locale())."='".id($value,$primary)."' and ";
                        }
                        else if ($field[0]=='in')
                        {
                            $result .= $table->name($field[1],$database->locale())." like '%|".id($value,$primary)."|%' and ";
                        }
                    }
                    if ($result!=='')
                    {
                        return substr($result, 0, -5);
                    }
                }
            }
        }

        function by ($field, $value)
        {
            $object = new by ();
            return $object->by ($field,$value);
        }

        function in ($field, $value)
        {
            $object = new by ();
            return $object->in ($field,$value);
        }

        function string ($input)
        {
            if (is_object($input) || is_array($input))
            {
                debug ($input);
            }
            if (!get_magic_quotes_gpc())
            {
                return addslashes($input);
            }
            return $input;
        }

        function round ($float) //for display purposes
        {
            if ($float<0)
            {
                return 0;
            }
            return \number_format(\round ($float,2),2);
        }

        function id (&$object,$field='id')
        {
            if (is_object($object))
            {
                if (is_object($field))
                {
                    $from = $field->primary->name;
                }
                else
                {
                    $from = $field;
                }
                if (isset($object->{$from}))
                {
                    $id = $object->{$from};
                }
                else
                {
                    foreach ($object as $value)
                    {
                        $id = $value;
                        break;
                    }
                }
            }
            else
            {
                $id = $object;
            }
            return string($id);
        }

        /**
         * @return string get id for class
         * @param string $class
         */
        function type ($input)
        {
            if (is_object($input))
            {
                // $reflection = new \ReflectionClass ($input);
                // $class = $reflection->getName();
                $class = get_class ($input);
            }
            else
            {
                $class = $input;
            }
            if ($class==null)
            {
                return null;
            }
            $class = str_replace ("\\", ".", $class);
            if ($class[0]!='.')
            {
                $class = '.'.$class;
            }
            return $class;
        }

        function field ($prefix, $column, $locale=null)
        {
            if ($column==null)
            {
                return null;
            }
            if ($locale!=null)
            {
                return $prefix.$column."_".$locale->name;
            }
            return $prefix.$column;
        }

        function date ($destroy=null, $restore=null)
        {
            if ($restore!==null)
            {
                $restore = strtotime ($restore);
                if ($restore===false)
                {
                    return '0000-00-00';
                }
                return \date('Y-m-d', $restore);
            }

            if ($destroy===null)
            {
                return \date('Y-m-d',\time());
            }

            $destroy = strtotime ($destroy);
            if ($destroy===false)
            {
                return '0000-00-00';
            }
            return \date('Y-m-d', $destroy);

/*            //debug ("des ".$destroy." res ".$restore);
            $user = 0;
            //$timezone = intval(\date('Z'));
            $timezone = 0;
            if (isset($GLOBALS['system']->user->timezone))
            {
                $user = intval(strval($GLOBALS['system']->user->timezone));
            }
            if ($restore!==null)
            {
                $restore = strtotime ($restore);
                if ($restore===false)
                {
                    return '0000-00-00';
                }
                if ($user)
                {
                    $timezone = $user;
                }
                $timezone = 0; //NEW
                return \date('Y-m-d',$restore+$timezone);
            }
            if ($destroy===null)
            {
                $timezone = 0; //NEW
                return \date('Y-m-d H:i:s',\time()-$timezone);
            }
            $destroy = strtotime ($destroy);
            if (!$destroy)
            {
                return '0000-00-00';
            }
            if ($user)
            {
                $timezone = $user;
            }
            $timezone = 0; //NEW
            return \date('Y-m-d',$destroy-$timezone);
*/        }

        //პარამეტრის გარეშე აბრუნებს მიმდინარე უნვერსალური დროს
        //პირველი პარამეტრის შემთხვევაში აკონვერტირებს მითითებულ დროს უნვერსალური დროში
        //მეორე პარამეტრის შემთხვევაში აკონვერტირებს მითითებულ დროს უნვერსალური დროდან
        function time ($destroy=null, $restore=null)
        {
            $user = 0;
            $timezone = intval(\date('Z'));
            if (isset($GLOBALS['system']->user->timezone))
            {
                $user = intval(strval($GLOBALS['system']->user->timezone));
            }
            if ($restore!==null)
            {
                $restore = strtotime ($restore);
                if ($restore===false)
                {
                    return '0000-00-00 00:00:00';
                }
                if ($user)
                {
                    $timezone = $user;
                }
                return \date('Y-m-d H:i:s',$restore+$timezone);
            }
            if ($destroy===null)
            {
                return \date('Y-m-d H:i:s',\time()-$timezone);
            }
            $destroy = strtotime ($destroy);
            if (!$destroy)
            {
                return '0000-00-00 00:00:00';
            }
/*            if ($user)
            {
                $timezone = $user;
            }
*/
            return \date('Y-m-d H:i:s',$destroy-$timezone);
        }

        function now ($date=null)
        {
            if ($date!==null)
            {
                return \date ('Y-m-d H:i:s',$date);
            }
            return \date ('Y-m-d H:i:s');
        }

        function debug ($input, $title=null)
        {
            $backtrace = debug_backtrace();
            $result = "<div style=\"font-family:'dejavu sans mono','consolas','monospaced','monospace';font-size:10pt;width:600px;margin-bottom:20px;margin-left:20px;\"><div style='background:#f0f0f0'>";
            foreach ($backtrace as $key => $value)
            {
                $result .= "<a href='subl://".str_replace('\\','/',$value['file']).":".$value['line']."' style='color:black;text-decoration:none;'>".$value['file']."</a> [".$value['line']."] <font color=red>".$value['function']."</font><br>";
            }
            $result .= '</div>';
            if ($title!==null)
            {
                if (is_object($title))
                {
                    $result .= "<div style='background:#669999;color:white;'>QUERY ".$title->count."</div>";
                }
                else
                {
                    $result .= "<div style='background:black;color:white'>".$title."</div>";
                }
            }
            if (is_string($input))
            {
                $result .= color ($input);
            }
            else
            {
                $result .= str_replace (array("\\'","\n"," ","var","array","class","=&gt;","&nbsp;&nbsp;&nbsp;'","'&nbsp;&nbsp;<b><font color=green>="),array("'","<br>\n",'&nbsp;&nbsp;',"<b><font color=blue>var</font></b>","<b><font color=red>array</font></b>","<b><font color=green>class</font></b>","<b><font color=green>=</font></b>","&nbsp;&nbsp;&nbsp;<font color=green>'","'</font>&nbsp;&nbsp;<b><font color=green>="), htmlspecialchars (@var_export($input,true),ENT_NOQUOTES,'UTF-8'));
            }
            $result .= "</div>";
            echo $result;
        }

/*        function debug ($input)
        {
            $backtrace = debug_backtrace();
            $result = "<div style=\"font-family:'dejavu sans mono','consolas','monospaced','monospace';font-size:10pt;width:600px;margin-bottom:20px;margin-left:20px;background:#fafafa\"><div style='background:#f0f0f0'>";
            foreach ($backtrace as $key => $value)
            {
                $result .= $value['file']." [".$value['line']."] <font color=red>".$value['function']."</font><br>";
            }
            $result .= '</div>';
            if (is_string($input))
            {
                $result .= color ($input);
            }
            else
            {
                $result .= str_replace (array("\\'","\n"," ","var","array","class","=&gt;","&nbsp;&nbsp;&nbsp;'","'&nbsp;&nbsp;<b><font color=green>="),array("'","<br>\n",'&nbsp;&nbsp;',"<b><font color=blue>var</font></b>","<b><font color=red>array</font></b>","<b><font color=green>class</font></b>","<b><font color=green>=</font></b>","&nbsp;&nbsp;&nbsp;<font color=green>'","'</font>&nbsp;&nbsp;<b><font color=green>="), htmlspecialchars (var_export($input,true),ENT_NOQUOTES,'UTF-8'));
            }
            $result .= "</div>";
            echo $result;
        }
*/
        function scope ($scope)
        {
            global $system;
            if ($scope==scope::project)
            {
                if (isset($system->solution->path) && isset($system->project->name))
                {
                    return md5($system->solution->path.'|'.$system->project->name)."|";
                }
            }
            if ($scope==scope::solution)
            {
                if (isset($system->solution->name))
                {
                    return md5($system->solution->path.'|')."|";
                }
            }
        }

        /*
        * if input is array raturns enom string
        * else returns parameters as single array
        */
        function enum ()
        {
            if (func_num_args()==0)
            {
                return array ();
            }
            if (func_num_args()==1 && is_array(func_get_arg(0)))
            {
                $input = func_get_arg(0);
                $result = "|";
                if ($input)
                {
                    foreach ($input as $key => $value)
                    {
                        $result .= $key."|";
                    }
                    return $result;
                }
                return '';
            }
            $input = func_get_args();
            $result = array ();
            foreach ($input as $item)
            {
                if ($item!='')
                {
                    $result[\db\id($item)] = $item;
                }
            }
            return $result;
        }

        function color ($query)
        {
            return str_replace(
            array(
            '`',
            '.',
            'show ',
            'describe ',
            'select ',
            'from ',
            'left join',
            'insert ',
            'update ',
            'delete ',
            'where ',
            'order by ',
            'like ',
            'limit ',
            'group by ',
            ' and ',
            ' or ',
            ' asc',
            ' desc',
            ' on '),
            array(
            '<b>`</b>',
            '<b>.</b>',
            '<span style="color:green;font-weight:bold">SHOW </span>',
            '<span style="color:green;font-weight:bold">DESCRIBE </span>',
            '<span style="color:green;font-weight:bold">SELECT </span>',
            '<span style="color:brown;font-weight:bold"><br>FROM </span>',
            '<span style="color:brown;font-weight:bold"><br>LEFT JOIN</span>',
            '<span style="color:green;font-weight:bold">INSERT </span>',
            '<span style="color:green;font-weight:bold">UPDATE </span>',
            '<span style="color:green;font-weight:bold">DELETE </span>',
            '<span style="color:blue;font-weight:bold"><br>WHERE </span>',
            '<span style="color:red;font-weight:bold"><br>ORDER BY </span>',
            '<span style="color:brown;font-weight:bold">LIKE </span>',
            '<span style="color:red;font-weight:bold"><br>LIMIT </span>',
            '<span style="color:red;font-weight:bold"><br>GROUP BY </span>',
            '<b> AND </b>',
            '</b> OR </b>',
            '<b> ASC</b>',
            '<b> DESC</b>',
            '<span style="color:brown;font-weight:bold"> ON </span>',
            ),$query);
        }
    }

?>