<?php
    namespace app\core;
    use \PDO;//thư viện hỗ trợ my sql
    use \PDOException;
    use app\core\Registry;
    use app\core\AppException;
    use app\core\Database;

    class QueryBuilder extends Database {

        private $columns;
        private $from;
        private $distinct = false;
        private $joins ;
        private $where;
        private $groups;
        private $havings;
        private $orders;
        private $limit;
        private $offset;
        //params để lưu giá trị excute tránh SQL enjection
        private $params = [];
        public function __construct($tableName){
            $this->from = $tableName;
            // echo "{$this->from}";
        }
        public static function table($tableName){
            //khoi tao chinh no -> goi den phuong thuc construct
            return new self($tableName);
        }

        public function select($columns){
            //func_get_args : tu dong tao chuoi thanh array hoac nhieu bien
            $this->columns = is_array($columns) ? $columns : func_get_args();
            // echo 'da select';
            return $this;//tra ve doi tuong
        }

        public function distinct(){
            $this->distinct = true;
            // echo 'da distinct';
            return $this;//tra ve chinh doi tuong do
        }

        public function join($table,$first,$operator,$second,$type='inner'){
            //DB::table('bang1')->select('col1','col2')->join('bangA','bang1.id','=','bangA.bang1ID')
            $this->joins[] = [$table,$first,$operator,$second,$type];
            return $this;
        }
        public function leftJoin($table,$first,$operator,$second){
            return $this->join($table,$first,$operator,$second,'left');
        }
        public function rightJoin($table,$first,$operator,$second){
            return $this->join($table,$first,$operator,$second,'right');
        }
        public function where($column, $operator, $value, $boolean = 'and'){
            //DB::table('bang1')->where('cot1','>',5,and)
            $this->where[] = [$column, $operator, $value, $boolean = 'and'];
            $this->params[] = $value;
            return $this;
        }
        public function orWhere($column, $operator, $value){
            return $this->where($column, $operator, $value,'or');
        }
        public function groupBy($columns){
            $this->groups = is_array($columns) ? $columns : func_get_args();
            return $this;
            
        }
        public function having($column, $operator, $value, $boolean = 'and'){
            //DB::table('bang1')->where('cot1','>',5,and)
            $this->havings[] = [$column, $operator, $value, $boolean = 'and'];
            $this->params[] = $value;
            return $this;
        }
        public function orHaving($column, $operator, $value){
            return $this->having($column, $operator, $value,'or');
        }

        public function orderBy($column, $direction = 'asc'){
            $this->orders[] = [$column, $direction];
            return $this;
        }

        public function limit($limit){
            $this->limit = $limit;
            return $this;
        }

        public function offset($offset){
            $this->offset = $offset;
            return $this;
        }
        //lay du lieu trong database
        public function get(){
            //kiem tra ton tai table
            if(!isset($this->from) || empty($this->from)){
                return false;
            }
            //bat dau cau truy van
            $sql = $this->distinct ? 'SELECT DISTINCT ' : 'SELECT ';
            if(isset($this->columns) && is_array($this->columns)){
                //explode: tách chuỗi thành mảng
                //implode('ki tu ơ giua', mang): tách mảng thành chuỗi
                $sql .= implode(' ,',$this->columns);
            }else{
                $sql .= ' *';
            }
            //from
            $sql .= " FROM {$this->from}";
            // join
            if(isset($this->joins) && is_array($this->joins)){
                foreach($this->joins as $join){
                    //dùng switch hay match() deu duoc
                    switch(strtolower($join[4])){
                        case "inner":
                            $sql .= " INNER JOIN";
                            break;
                        case "left":
                            $sql .= " LEFT JOIN";
                            break;
                        case "right":
                            $sql .= " RIGHT JOIN";
                            break;
                        default :
                            $sql .= " INNER JOIN";
                            break;
                    }
                    $sql .= " {$join[0]} ON {$join[1]} {$join[2]} {$join[3]}";
                }
            }
            //where
            $sql .= $this->buildWhereClause();
            //group by
            if(isset($this->groups) && is_array($this->groups)){
                $sql .= ' GROUP BY '.implode(' ,',$this->groups);
            }
            //having
            if(isset($this->havings) && is_array($this->havings)){
                $sql .= " HAVING";
                foreach($this->havings as $hk => $having){
                    $sql .= " {$having[0]} {$having[1]} {$having[2]}";
                    if($hk < (count($this->havings) - 1)){
                        $sql .= strtolower($having[3]) === 'and' ? ' AND' : ' OR';
                    }
                }
            }
            //order
            if(isset($this->orders) && is_array($this->orders)){
                $sql .= " ORDER BY";
                foreach($this->orders as $ok => $order){
                    $sql .= " {$order[0]} {$order[1]}";
                    if($ok < (count($this->orders) - 1)){
                        $sql .= ' ,';
                    }
                }
            }
            //limit
            if(isset($this->limit)){
                $sql .= " LIMIT {$this->limit}";
            }
            //offset
            if(isset($this->offset)){
                $sql .= " OFFSET {$this->offset}";
            }
            // return $sql;
            try {
                $stmt = self::connect()->prepare($sql);
                $stmt->execute($this->params);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new AppException("Query failed: " . $e->getMessage());
            }
        }

        //function insert
        public  function insert(array $data){
            if(!isset($this->from) || empty($this->from)){
                return false;
            };
            $columns = implode(", ", array_keys($data));
            //array_fill() là một hàm của PHP dùng để tạo một mảng với các giá trị giống nhau.
            //3 tham số: tham số đầu là giá trị bắt đầu mảng, tham số 2 là số lượng phần tử của mảng, phần tử 3 là giá trị lặp lại => ['?', '?', '?']
            $placeholders = implode(", ", array_fill(0, count($data), '?'));
            $sql = "INSERT INTO {$this->from} ($columns) VALUES ($placeholders)";
            //return $data;
            try {
                $stmt = self::connect()->prepare($sql);
                $stmt->execute(array_values($data));
                return true;
            } catch (PDOException $e) {
                throw new AppException("Insert failed: " . $e->getMessage());
            }
        }
        //tương tự insert nhưng có trả về phần tử
        public function create(array $data) {
            $this->insert($data);
            return $this->get();
        }
        //kiểm tra có tồn tại bản ghi k có thì update ngược lại thì insert
        public function insertOrUpdate(array $checkData, array $insertData) {
            // Kiểm tra xem có bản ghi tồn tại dựa trên $checkData
            //Hàm reset() đặt lại con trỏ nội bộ của mảng về phần tử đầu tiên và trả về giá trị đầu tiên của mảng.
            $this->where(key($checkData), '=', reset($checkData));
            $existingData = $this->get();
        
            // Nếu không có bản ghi nào tồn tại, thực hiện insert
            if (empty($existingData)) {
                return $this->insert($insertData);
            } else {
                // Nếu có bản ghi, thực hiện update
                $this->where(key($checkData), '=', reset($checkData));
                return $this->update($insertData);
            }
        }
        
        
        public function update(array $data) {
            $setPart = implode(" = ?, ", array_keys($data)) . " = ?";
            $sql = "UPDATE {$this->from} SET $setPart";
            //where
            $sql .= $this->buildWhereClause();
            // return $sql;
            try {
                $stmt = self::connect()->prepare($sql);
                $stmt->execute(array_merge(array_values($data), $this->params));
                return true;
            } catch (PDOException $e) {
                throw new AppException("Update failed: " . $e->getMessage());
            }
        }
        public function delete() {
            if(!isset($this->from) || empty($this->from)){
                return false;
            };
            $sql = "DELETE FROM {$this->from}";
            //where
            $sql .= $this->buildWhereClause();
            
            try {
                $stmt = self::connect()->prepare($sql);
                $stmt->execute($this->params);
                return true;
            } catch (PDOException $e) {
                throw new AppException("Delete failed: " . $e->getMessage());
            }
        }

        private function buildWhereClause() {
            $sql = '';
            if (isset($this->where) && is_array($this->where)) {
                $sql .= " WHERE";
                foreach ($this->where as $wk => $where) {
                    $sql .= " {$where[0]} {$where[1]} ?";
                    if ($wk < (count($this->where) - 1)) {
                        $sql .= strtolower($where[3]) === 'and' ? ' AND' : ' OR';
                    }
                }
            }
            return $sql;
        }
        
        
        
    }
    
?>