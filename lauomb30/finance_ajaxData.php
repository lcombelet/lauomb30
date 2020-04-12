<?php
// Include config file
require_once 'config.php';

if(!empty($_POST["category_id"])){
    // Fetch state data based on the specific country
    $query = "SELECT * FROM tbl_fin_subcategory WHERE fin_category_id = ".$_POST['category_id']."";
    $result = $mysqli->query($query);

    // Generate HTML of state options list
    if($result->num_rows > 0){
        echo '<option value="">Select subcategory</option>';
        while($row = $result->fetch_assoc()){
            echo '<option value="'.$row['id'].'">'.$row['description'].'</option>';
        }
    }else{
        echo '<option value="">Subcategory not available</option>';
    }
}
?>
