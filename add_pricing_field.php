<?php
include("connection.php");

// Display detailed error information
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Add consultation_fee column to doctor table if it doesn't exist
$check_column = $database->query("SHOW COLUMNS FROM doctor LIKE 'consultation_fee'");
if ($check_column && $check_column->num_rows == 0) {
    $alter_query = "ALTER TABLE doctor ADD COLUMN consultation_fee DECIMAL(10,2) DEFAULT 50.00 AFTER docexp";
    if ($database->query($alter_query)) {
        echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px;'>
                Successfully added consultation_fee column to doctor table.
              </div>";
        
        // Update doctors with different pricing
        $update_queries = [
            "UPDATE doctor SET consultation_fee = 75.00 WHERE docid = 1", // John Smith (Child Psychiatry)
            "UPDATE doctor SET consultation_fee = 65.00 WHERE docid = 2", // Sarah Johnson (Child Psychology)
            "UPDATE doctor SET consultation_fee = 60.00 WHERE docid = 3", // Michael Brown (Developmental Specialist)
            "UPDATE doctor SET consultation_fee = 70.00 WHERE docid = 4", // Emily Davis (Behavioral Therapy)
            "UPDATE doctor SET consultation_fee = 80.00 WHERE docid = 5"  // David Wilson (Child Neurology)
        ];
        
        $success_count = 0;
        foreach ($update_queries as $query) {
            if ($database->query($query)) {
                $success_count++;
            } else {
                echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px;'>
                        Error updating pricing: " . $database->error . "
                      </div>";
            }
        }
        
        echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px;'>
                Updated pricing for " . $success_count . " doctors.
              </div>";
    } else {
        echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px;'>
                Error adding column: " . $database->error . "
              </div>";
    }
} else {
    // Check if the column exists but needs to be updated with values
    $check_values = $database->query("SELECT COUNT(*) as count FROM doctor WHERE consultation_fee IS NULL OR consultation_fee = 0");
    $row = $check_values->fetch_assoc();
    
    if ($row['count'] > 0) {
        echo "<div style='background-color: #fff3cd; color: #856404; padding: 15px; margin-bottom: 20px; border-radius: 5px;'>
                Column consultation_fee exists but some doctors have no pricing. Updating values...
              </div>";
              
        // Update doctors with different pricing
        $update_queries = [
            "UPDATE doctor SET consultation_fee = 75.00 WHERE docid = 1", // John Smith (Child Psychiatry)
            "UPDATE doctor SET consultation_fee = 65.00 WHERE docid = 2", // Sarah Johnson (Child Psychology)
            "UPDATE doctor SET consultation_fee = 60.00 WHERE docid = 3", // Michael Brown (Developmental Specialist)
            "UPDATE doctor SET consultation_fee = 70.00 WHERE docid = 4", // Emily Davis (Behavioral Therapy)
            "UPDATE doctor SET consultation_fee = 80.00 WHERE docid = 5", // David Wilson (Child Neurology)
            "UPDATE doctor SET consultation_fee = 50.00 WHERE consultation_fee IS NULL OR consultation_fee = 0" // Default for any others
        ];
        
        $success_count = 0;
        foreach ($update_queries as $query) {
            if ($database->query($query)) {
                $success_count++;
            } else {
                echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px;'>
                        Error updating pricing: " . $database->error . "
                      </div>";
            }
        }
        
        echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px;'>
                Updated pricing for " . $success_count . " queries.
              </div>";
    } else {
        echo "<div style='background-color: #d1ecf1; color: #0c5460; padding: 15px; margin-bottom: 20px; border-radius: 5px;'>
                Column consultation_fee already exists and has values.
              </div>";
    }
}

echo "<br><a href='patient/doctors.php'>Go to Doctors Page</a>";
?>
