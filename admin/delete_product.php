<?php
// Include the file containing the db class
require('../config/dbcon.php');

// Create database object
$database = new db();

if(isset($_GET["id"])) 
{
    $id = $_GET['id'];
    $result = $database->delete("products", "id = $id");
    if ($result) {
        
        
        header("Location: products.php");

        exit(); 
    } else {
        echo "Failed to delete product.";
    }
} 
else {
    echo "ID is missing from the URL.";
}
?>