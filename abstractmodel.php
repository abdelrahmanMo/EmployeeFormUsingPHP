<?php

class Abstractmodel {

    //put your code here\
    //هكتب انواع الداتا ال ممكن اشتغل عليها في الداتا بيز 
    const DATA_TYPE_BOOL = PDO::PARAM_BOOL;
    const DATA_TYPE_STR = PDO::PARAM_STR;
    const DATA_TYPE_INT = PDO::PARAM_INT;
    const DATA_TYPE_DECIMAL = 4;
    // تجهيز الداتا قبل  ما ابعتها للداتا بيز
    private function prepareValue(PDOStatement &$stmt) {
        foreach (static::$tableSchema as $columnName => $type) {

            if ($type == 4) {
                $sanitizedValue = filter_var($this->$columnName, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $stmt->bindValue(":{$columnName}", $sanitizedValue);
            } else {
                $stmt->bindValue(":{$columnName}", $this->$columnName, $type);
            }
        }
    }
// تجهيز جملة السكوال
    private static function buildNameParameterSQL() {
        $nameParams = '';
        foreach (static::$tableSchema as $columnName => $type) {
            $nameParams .= $columnName . '= :' . $columnName . ', ';
        }
        return trim($nameParams, ', ');
    }
// انشاء جملة الانسيرت
    private function create() {
        global $connection;
        $sql = 'INSERT INTO ' . static::$tableName . ' SET ' . self::buildNameParameterSQL();
        $stmt = $connection->prepare($sql);
        $this->prepareValue($stmt);
        return $stmt->execute();
    }
//prepare update statement
    private function update() {
        global $connection;
        $sql = 'UPDATE ' . static::$tableName . ' SET ' . self::buildNameParameterSQL() . ' WHERE ' . static::$primaryKey . '= ' . $this->{static::$primaryKey};
       
        $stmt = $connection->prepare($sql);
        $this->prepareValue($stmt);
        return $stmt->execute();
    }
    // make insert or update
    public function save()
    {
        return  $this->{static::$primaryKey} === NULL ? $this->create() :$this->update() ;
    }

    public function delete() {
        global $connection;
        $sql = 'DELETE FROM ' . static::$tableName . ' WHERE ' . static::$primaryKey . '= ' . $this->{static::$primaryKey};
        echo $sql;
        $stmt = $connection->prepare($sql);

        return $stmt->execute();
    }

    public static function getAll() {
        global $connection;
        $sql = 'SELECT * FROM ' . static::$tableName;
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, get_called_class(), array_keys(static::$tableSchema));
        return (is_array($result) && !empty($result)) ? $result  : FALSE;
    }
    public static function getByID($pk) {
        global $connection;
        $sql = 'SELECT * FROM ' . static::$tableName .' WHERE '. static::$primaryKey .' = '. $pk ;
        $stmt = $connection->prepare($sql);
        if($stmt->execute() == TRUE)
        {
            $obj=$stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, get_called_class(), array_keys(static::$tableSchema)) ;
            return array_shift($obj);
            }
        var_dump($stmt);
        return FALSE;
        //return $stmt->execute() === TRUE ? $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, get_called_class(), array_keys(static::$tableSchema)) : FALSE;
    }

    // هنا لو حابب انك تعمل جملة سيليكت بطريقه معينه انك تبعت الجمله وتبعت اراي فيها الحاجه ال انت عايز تبتها ونوعها 
    /*
        $emp = Employees::get(
        'SELECT * FROM employees WHERE address = :address',
        array(
            'address' => array(Employees::DATA_TYPE_STR , "giza")
        )
        );

        var_dump($emp);
     * 
     *      */
    public static function get($sql , $options = array())
    {
        global $connection;
        $stmt = $connection->prepare($sql);
        var_dump($stmt);
        if(!empty($options))
        {
             foreach ($options as $columnName => $type) {

            if ($type[0] == 4) {
                $sanitizedValue = filter_var($type[1], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $stmt->bindValue(":{$columnName}", $sanitizedValue);
            } else {
                  var_dump(":{$columnName}");
                $stmt->bindValue(":{$columnName}", $type[1], $type[0]);
            }
        }
      
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, get_called_class(), array_keys(static::$tableSchema));
        return (is_array($result) && !empty($result)) ? $result  : FALSE;
        }
    }

}
