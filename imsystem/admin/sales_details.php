<?php
include 'includes/session.php';

if(isset($_POST['id'])){
  $id = $_POST['id'];
  
  $sql = "SELECT bd.product_company AS category, 
                 bd.product_unit AS unit, 
                 bd.qty AS quantity,
                 bd.product_name, 
                 bd.price, 
                 bd.total,
                 bd.inventory_selection 
          FROM billing_details bd
          WHERE bd.bill_id = '$id'";
  
  $query = $conn->query($sql);
  
  echo '<table class="table table-bordered">
          <thead>
            <tr>
              <th>Category</th>
              <th>Product</th>
              <th>Unit</th>
              <th>Quantity</th>
              <th>Price</th>
              <th>Total</th>
              <th>Inventory</th>
            </tr>
          </thead>
          <tbody>';
  
  while($row = $query->fetch_assoc()){
    echo "<tr>
            <td>".$row['category']."</td>
            <td>".$row['product_name']."</td>
            <td>".$row['unit']."</td>
            <td>".$row['quantity']."</td>
            <td>".number_format($row['price'], 2)."</td>
            <td>".number_format($row['total'], 2)."</td>
            <td>".$row['inventory_selection']."</td>
          </tr>";
  }
  
  echo '</tbody></table>';
}
?>