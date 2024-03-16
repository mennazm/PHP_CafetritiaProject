<?php
class db{
    private $host = "localhost";
    private $dbname = "cafeteria_project";
    private $username = "root";
    private $password = ""; 
    private $connection = null;

function __construct() {
            $this->connection = new mysqli($this->host, $this->username, $this->password, $this->dbname);
            if ($this->connection->connect_error) {
                die("Connection failed: " . $this->connection->connect_error);
    }
}

function getconnection(){
    return $this->connection;
}

function delete($tablename , $condition){
  return  $this->connection->query("DELETE FROM $tablename WHERE  $condition");
}

function getdata($cols, $tablename, $condition=1){
    $query = "SELECT $cols FROM $tablename";
    if ($condition !== '') {
        $query .= " WHERE $condition";
    }
    $result = $this->connection->query($query);
    if (!$result) {
        // Error handling - log the error message
        error_log("Database error: " . $this->connection->error);
        return false;
    }
    return $result;
}




function insert_data($tableName, $columns, $values){ 
    $columnsStr = implode(', ', $columns); 
    $valuesStr = implode(', ', array_map(function($value) { 
        return "'$value'"; 
    }, $values)); 
    return $this->connection->query("INSERT INTO $tableName ($columnsStr) VALUES ($valuesStr)"); 
} 

    // Insert a row into a table
    function insert_row($table_name, $data) {
        // Check if the table name is 'users' and if so, replace 'room_number' with 'room_id' and 'Ext' with 'room_id'
        if ($table_name === 'users') {
            // Fetch the room_id based on the room_number provided in the data array
            $room_number = $data['room_number'];
            $Ext = $data['Ext'];
            $room_query = "SELECT room_id FROM rooms WHERE room_number = '$room_number' AND Ext = '$Ext'";
            $room_result = $this->connection->query($room_query);
    
            if ($room_result && $room_result->num_rows > 0) {
                $room_row = $room_result->fetch_assoc();
                $data['room_id'] = $room_row['room_id'];
                // Remove 'room_number' and 'Ext' from the data array
                unset($data['room_number']);
                unset($data['Ext']);
            } else {
                // Handle error if room_number and Ext combination is not found in the rooms table
                error_log("Combination of room number '$room_number' and Ext '$Ext' not found in the rooms table");
                return false;
            }
        }
    
        // Prepare the keys and values for the query
        $keys = implode(", ", array_keys($data));
        $values = "'" . implode("', '", array_values($data)) . "'";
        $query = "INSERT INTO $table_name ($keys) VALUES ($values)";
    
        // Execute the query and return the result
        return $this->connection->query($query);
    }
    
 

    function update_data($tableName, $columns_values, $condition=1) { 
        $setClause = implode(', ', array_map(function ($column, $value) { 
            return "$column=" . (is_null($value) ? "NULL" : "'$value'"); 
        }, array_keys($columns_values), $columns_values)); 
        
        return $this->connection->query("UPDATE $tableName SET $setClause WHERE $condition"); 
    }
    


    function get_data_custom($query) {
        $result = $this->connection->query($query);
        if (!$result) {
            // Error handling - log the error message
            error_log("Database error: " . $this->connection->error);
            return false;
        }
        return $result;
    }
    
 


function getbyid($tableName , $id){
    // Execute SQL query to fetch the record with the specified ID
    $result = $this->connection->query("SELECT * FROM $tableName WHERE id=$id");
    
   
    if ($result) {
       
        return $result;
    } else {
       
        echo "Error: " . $this->connection->error;
        return null;
    }
}
public function getOrdersByDateRangeForUser($user_id, $start_date, $end_date) {
    $query = "SELECT * FROM orders WHERE user_id = ? AND order_date BETWEEN ? AND ?";
    $stmt = $this->connection->prepare($query);
    $stmt->bind_param("iss", $user_id, $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}


function getUserOrders($user_id) {
    $sql = "SELECT * FROM orders WHERE user_id = ?";
    $stmt = $this->connection->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function getOrderProducts($order_id) {
    $sql = "SELECT * FROM products WHERE product_id IN (SELECT product_id FROM order_items WHERE order_id = ?)";
    $stmt = $this->connection->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}




function getLastInsertedId() {
    return $this->connection->insert_id;
}

function getOrdersWithDetails() {
    $query = "SELECT orders.*, users.name AS username, rooms.room_number, rooms.Ext 
              FROM orders 
              INNER JOIN users ON orders.user_id = users.user_id 
              INNER JOIN rooms ON users.room_id = rooms.room_id";
    $result = $this->connection->query($query);
    return $result;
}


}